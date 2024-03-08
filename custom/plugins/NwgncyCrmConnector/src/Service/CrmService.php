<?php

declare(strict_types=1);

namespace Nwgncy\CrmConnector\Service;

use DateTimeInterface;
use Nimbits\NimbitsPriceOnRequestNext\Events\MailBeforeSendEvent as ProductInquiryBeforeSendEvent;
use Nwgncy\CrmConnector\Event\CadFileDownloadEvent;
use Nwgncy\CrmConnector\Event\ContactFormEventDecorated;
use Nwgncy\CrmConnector\Handler\Microsoft\MicrosoftDynamicsCrmHandler;
use Nwgncy\CrmConnector\Handler\Sap\SapCrmHandler;
use Nwgncy\CrmConnector\Struct\CrmRecord;
use Psr\Log\LoggerInterface;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Checkout\Customer\Event\CustomerRegisterEvent;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\Country\Aggregate\CountryState\CountryStateEntity;
use Shopware\Core\System\Country\CountryEntity;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\Locale\LocaleEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

class CrmService
{
    private LoggerInterface $logger;
    private EntityRepository $languageRepository;
    private EntityRepository $localeRepository;
    private EntityRepository $productRepository;
    private EntityRepository $countryRepository;
    private MicrosoftDynamicsCrmHandler $microsoftDynamicsCrmHandler;
    private SapCrmHandler $sapCrmHandler;

    private $expectedEventTypes = [
        CustomerRegisterEvent::class,
        ContactFormEventDecorated::class,
        ProductInquiryBeforeSendEvent::class,
        CadFileDownloadEvent::class
    ];

    public function __construct(LoggerInterface $logger, EntityRepository $languageRepository, EntityRepository $localeRepository, EntityRepository $productRepository, EntityRepository $countryRepository, MicrosoftDynamicsCrmHandler $microsoftDynamicsCrmHandler, SapCrmHandler $sapCrmHandler)
    {
        $this->logger = $logger;
        $this->languageRepository = $languageRepository;
        $this->localeRepository = $localeRepository;
        $this->productRepository = $productRepository;
        $this->countryRepository = $countryRepository;
        $this->microsoftDynamicsCrmHandler = $microsoftDynamicsCrmHandler;
        $this->sapCrmHandler = $sapCrmHandler;
    }

    private function execute(CrmRecord $record)
    {
        $this->sapCrmHandler->sendData($record);
        $this->microsoftDynamicsCrmHandler->sendData($record);
        //Other crm handlers ..
    }

    public function processEvent(Event $event)
    {
        try {


            //defaults
            $crmRecord = new CrmRecord();
            $isValidEvent = false;
            $salesChannelContext = null;
            $context = Context::createDefaultContext();
            if (method_exists($event, 'getContext')) {
                $eventContext = $event->getContext();
                if ($eventContext instanceof Context) {
                    $context = $eventContext;
                }
                if ($eventContext instanceof SalesChannelContext) {
                    $salesChannelContext = $eventContext;
                    $context = $eventContext->getContext();
                }
            }

            $crmRecord->setLanguage($this->getLanguageCode($context));

            if (method_exists($event, 'getSalesChannelContext')) {
                $eventSalesChannelContext = $event->getSalesChannelContext();
                if ($eventSalesChannelContext instanceof SalesChannelContext) {
                    $salesChannelContext = $eventSalesChannelContext;
                }
            }

            if (isset($salesChannelContext) && $salesChannelContext instanceof SalesChannelContext) {
                $salesChannel = $salesChannelContext->getSalesChannel();
                $salesChannelCountry = $salesChannel->getCountry();
                if ($salesChannelCountry instanceof CountryEntity) {
                    $salesChannelCountryCode = $this->getCountryCode($salesChannelCountry);
                    if ($salesChannelCountryCode) {
                        $crmRecord->setCountry($salesChannelCountryCode);
                        $crmRecord->setCallingWebsiteCountry($salesChannelCountryCode);
                    }
                }


                if ($event->getCustomer() instanceof CustomerEntity) {
                    $customer = $event->getCustomer();
                } else {

                    $customer = $salesChannelContext->getCustomer();
                }
                if ($customer instanceof CustomerEntity) {
                    $webRequestId = $this->formatWebrequestId($customer->getId());
                    $crmRecord->setWebrequestId($webRequestId);
                    $customerBillingAddress = $customer->getDefaultBillingAddress();
                    if ($customerBillingAddress instanceof CustomerAddressEntity) {
                        $street = $customerBillingAddress->getStreet();
                        $postalCode = $customerBillingAddress->getZipcode();
                        $department = $customerBillingAddress->getDepartment();
                        if ($department) {
                            $crmRecord->setDepartment($department);
                        }
                        if ($postalCode) {
                            $crmRecord->setPostalcode($postalCode);
                        }
                        $city = $customerBillingAddress->getCity();
                        $countryState = $customerBillingAddress->getCountryState();
                        if ($countryState instanceof CountryStateEntity) {
                            $countryStateName = $countryState->getName();
                            if ($countryStateName != null) {
                                $crmRecord->setTenteState($countryStateName);
                            }
                        }
                        $country = $customerBillingAddress->getCountry();
                        if ($country instanceof CountryEntity) {
                            $countryCode = $this->getCountryCode($country);
                            if ($countryCode) {
                                $crmRecord->setCallingWebsiteCountry($countryCode);
                                $crmRecord->setCountry($countryCode);
                            }
                        }
                        $phone = $customerBillingAddress->getPhoneNumber();
                        if ($phone) {
                            $crmRecord->setPhone($phone);
                        }
                        $crmRecord->setStreet($street);
                        $crmRecord->setCity($city);
                    }

                    $firstName = $customer->getFirstName();
                    $lastName = $customer->getLastName();
                    $email = $customer->getEmail();

                    $crmRecord->setFirstname($firstName);
                    $crmRecord->setLastname($lastName);
                    $crmRecord->setEmail($email);

                    $companyName = $customer->getCompany();

                    if ($companyName !== null) {
                        $crmRecord->setCompanyName($companyName);
                    }

                    $customerCustomFields = $customer->getCustomFields();
                    if (is_array($customerCustomFields)) {
                        if (array_key_exists('homepage', $customerCustomFields) && is_string($homepage = $customerCustomFields['homepage'])) {
                            $crmRecord->setHomepage($homepage);
                        }
                        if (array_key_exists('fax', $customerCustomFields) && is_string($fax = $customerCustomFields['fax'])) {
                            $crmRecord->setFax($fax);
                        }
                    }
                }
            }

            //event handling
            if ($event instanceof CustomerRegisterEvent) {
                $isValidEvent = true;
                $crmRecord = $this->customerRegisterEventHandler($event, $crmRecord);
            }
            if ($event instanceof ContactFormEventDecorated) {
                $isValidEvent = true;
                $crmRecord = $this->contactFormEventHandler($event, $crmRecord);
            }
            if ($event instanceof ProductInquiryBeforeSendEvent) {
                $isValidEvent = true;
                $context = Context::createDefaultContext();
                $crmRecord = $this->productInquiryEventHandler($event, $crmRecord, $context);
            }
            if ($event instanceof CadFileDownloadEvent) {
                $isValidEvent = true;
                $crmRecord = $this->cadDownloadEventHandler($event, $crmRecord, $context);
            }

            if ($isValidEvent) { //the event is valid and the corresponding records were added
                $crmRecord->setCallingWebsiteCountry($crmRecord->getCountry());
                $crmRecord->setDcIpAddress($this->getClientIpAddress());
                $this->execute($crmRecord);
                return $event;
            }

            $this->logEventError($event, null);
        } catch (\Throwable $th) {
            // dd($th);
            $this->logEventError($event, $th, 'critical');
        }
    }

    private function logEventError(Event $event, ?Throwable $exception, string $type = 'error')
    {
        $logMessage = "Invalid event received at CRM Handler.";
        $eventName = "undefined-event-name";
        if ($this->isExpectedEvent($event)) {
            if ($event instanceof ProductInquiryBeforeSendEvent) {
                $eventName = 'Product Inquiry Event';
            }

            if (method_exists($event, 'getName') && is_string($event->getName())) {
                $eventName = $event->getName();
            }
            $logMessage = "An error occured processing CRM Event " . $eventName . ".";
        }
        if ($exception !== null) {
            $exceptionCode = $exception->getCode();
            $exceptionMessage = $exception->getMessage();
            $logMessage .= " Exception: Code(" . $exceptionCode . ") " . $exceptionMessage . ".";
        }

        if ($type == "critical") {
            $this->logger->critical($logMessage);
        } else {
            $this->logger->error($logMessage);
        }
    }

    private function isExpectedEvent(Event $event)
    {
        $isInstanceOfExpectedEvent = false;
        foreach ($this->expectedEventTypes as $entityType) {
            if ($event instanceof $entityType) {
                $isInstanceOfExpectedEvent = true;
                break;
            }
        }
        return $isInstanceOfExpectedEvent;
    }

    private function dcTimestampFormatting(int $timestamp)
    {
        $formated = date("Y-m-d H:i:s", $timestamp);
        if (!is_string($formated)) {
            $formated = "";
        }
        return $formated;
    }

    private function webRequestDateFormatting(int $timestamp)
    {
        $formated = date("Y-m-d", $timestamp);
        if (!is_string($formated)) {
            $formated = "";
        }
        return $formated;
    }

    private function getLanguageCode(Context $context)
    {
        $languageCode = "";
        $languageId = $context->getLanguageId();
        $languageResult = $this->languageRepository->search(new Criteria([$languageId]), $context)->first();
        if ($languageResult instanceof LanguageEntity) {
            $languageLocaleId = $languageResult->getLocaleId();
            $languageLocale = $this->localeRepository->search(new Criteria([$languageLocaleId]), $context)->first();
            if ($languageLocale instanceof LocaleEntity) {
                $languageFullCode = $languageLocale->getCode();
                $languageCode = strtoupper(substr($languageFullCode, 0, 2)); //ISO 639-1
            }
        }
        return $languageCode;
    }

    private function getCountryCode(CountryEntity $country)
    {
        $countryCode = "";
        if ($tmpCountryCode = $country->getIso()) {
            $countryCode = $tmpCountryCode;
        }
        return strtoupper($countryCode);
    }

    private function getCountryCodeById(string $id, Context $context)
    {
        $criteria = new Criteria([$id]);
        $countryResult = $this->countryRepository->search($criteria, $context)->first();
        if ($countryResult instanceof CountryEntity) {
            return $this->getCountryCode($countryResult);
        }
        return "";
    }

    private function createRecordInformationArr(int $timestamp, string $state, string $message)
    {
        $informationArr = [
            'Webrequest-Date = ' . $this->webRequestDateFormatting($timestamp),
            'State = ' . $state,
            $message
        ];
        return $informationArr;
    }

    private function customerRegisterEventHandler(CustomerRegisterEvent $event, CrmRecord $crmRecord): CrmRecord
    {
        $crmRecord->setTenteUserAccountCreated('true');
        $crmRecord->setTenteQuoteRequest('false');
        $customer = $event->getCustomer();
        $webRequestId = $this->formatWebrequestId($customer->getId());
        $crmRecord->setWebrequestId($webRequestId);
        $customerBillingAddress = $customer->getDefaultBillingAddress();
        $state = "";
        $timestamp = time();
        if ($customerBillingAddress instanceof CustomerAddressEntity) {
            $street = $customerBillingAddress->getStreet();
            $postalCode = $customerBillingAddress->getZipcode();
            $department = $customerBillingAddress->getDepartment();
            if ($department) {
                $crmRecord->setDepartment($department);
            }
            if ($postalCode) {
                $crmRecord->setPostalcode($postalCode);
            }
            $city = $customerBillingAddress->getCity();
            $countryState = $customerBillingAddress->getCountryState();
            if ($countryState instanceof CountryStateEntity) {
                $countryStateName = $countryState->getName();
                if ($countryStateName != null) {
                    $crmRecord->setTenteState($countryStateName);
                    $state = $countryStateName;
                }
            }
            $country = $customerBillingAddress->getCountry();
            if ($country instanceof CountryEntity) {
                $countryCode = $this->getCountryCode($country);
                if ($countryCode) {
                    $crmRecord->setCountry($countryCode);
                }
            }
            $phone = $customerBillingAddress->getPhoneNumber();
            if ($phone) {
                $crmRecord->setPhone($phone);
            }

            $crmRecord->setStreet($street);
            $crmRecord->setCity($city);
        }

        $firstName = $customer->getFirstName();
        $lastName = $customer->getLastName();
        $email = $customer->getEmail();
        $createdAt = $customer->getCreatedAt();

        if ($createdAt instanceof DateTimeInterface) {
            $timestamp = $createdAt->getTimestamp();
        }

        $crmRecord->setDcTimestamp($this->dcTimestampFormatting($timestamp));
        $recordInformationMessage = 'User Account Created = Yes';

        $informationArr = $this->createRecordInformationArr($timestamp, $state, $recordInformationMessage);
        $information = implode("\r\n", $informationArr);
        $crmRecord->setInformation($information);

        $crmRecord->setFirstname($firstName);
        $crmRecord->setLastname($lastName);
        $crmRecord->setEmail($email);

        $companyName = $customer->getCompany();

        if ($companyName !== null) {
            $crmRecord->setCompanyName($companyName);
        }

        $customerCustomFields = $customer->getCustomFields();
        if (is_array($customerCustomFields)) {
            if (array_key_exists('customer_homepage', $customerCustomFields) && is_string($homepage = $customerCustomFields['customer_homepage'])) {
                $crmRecord->setHomepage($homepage);
            }
            if (array_key_exists('customer_fax', $customerCustomFields) && is_string($fax = $customerCustomFields['customer_fax'])) {
                $crmRecord->setFax($fax);
            }
            if (array_key_exists('customer_newsletter_registration', $customerCustomFields)) {
                $crmRecord->setNewsletterRequest('true');
            }
        }

        return $crmRecord;
    }

    private function contactFormEventHandler(ContactFormEventDecorated $event, CrmRecord $crmRecord): CrmRecord
    {
        $contactFormData = $event->getContactFormData();
        $state = "";
        $currentTimestamp = time();
        $crmRecord->setTenteQuoteRequest('false');
        $crmRecord->setTenteFreeTextRequest('true');

        if (array_key_exists('firstName', $contactFormData) && is_string($firstName = $contactFormData['firstName'])) {
            $crmRecord->setFirstname($firstName);
        }
        if (array_key_exists('lastName', $contactFormData) && is_string($lastName = $contactFormData['lastName'])) {
            $crmRecord->setLastname($lastName);
        }
        if (array_key_exists('email', $contactFormData) && is_string($email = $contactFormData['email'])) {
            $crmRecord->setEmail($email);
        }
        if (array_key_exists('phone', $contactFormData) && is_string($phonenumber = $contactFormData['phone'])) {
            $crmRecord->setPhone($phonenumber);
        }
        if (array_key_exists('company', $contactFormData) && is_string($company = $contactFormData['company'])) {
            $crmRecord->setCompanyName($company);
        }
        if (array_key_exists('countryIso', $contactFormData) && is_string($countryIso = $contactFormData['countryIso'])) {
            $crmRecord->setCountry($countryIso);
        }
        if (array_key_exists('state', $contactFormData) && is_string($state = $contactFormData['state'])) {
            $crmRecord->setTenteState($state);
        }
        if (array_key_exists('city', $contactFormData) && is_string($city = $contactFormData['city'])) {
            $crmRecord->setCity($city);
        }
        if (array_key_exists('zip', $contactFormData) && is_string($zip = $contactFormData['zip'])) {
            $crmRecord->setPostalcode($zip);
        }
        if (array_key_exists('street', $contactFormData) && is_string($street = $contactFormData['street'])) {
            $crmRecord->setStreet($street);
        }

        if (array_key_exists('comment', $contactFormData) && is_string($comment = $contactFormData['comment'])) {
            $crmRecord->setInformation($comment);
        }
        if (array_key_exists('newsletter-subscribe', $contactFormData) && is_string($newsletteSubscribe = $contactFormData['newsletter-subscribe'])) {

            $crmRecord->setNewsletterRequest('true');
        }

        $crmRecord->setDcTimestamp($this->dcTimestampFormatting($currentTimestamp));

        return $crmRecord;
    }

    private function productInquiryEventHandler(ProductInquiryBeforeSendEvent $event, CrmRecord $crmRecord, Context $context): CrmRecord
    {
        $crmRecord->setTenteQuoteRequest('true');
        $mailVars = $event->getMailvars();
        $exactVars = [];
        if (array_key_exists('vars', $mailVars)) {
            $exactVars = $mailVars['vars'];
        }
        $state = "";
        $timestamp = time();

        if (array_key_exists('firstname', $mailVars) && is_string($firstName = $mailVars['firstname'])) {
            $crmRecord->setFirstname($firstName);
        }
        if (array_key_exists('surname', $mailVars) && is_string($lastName = $mailVars['surname'])) {
            $crmRecord->setLastname($lastName);
        }
        if (array_key_exists('company', $mailVars) && is_string($company = $mailVars['company'])) {
            $crmRecord->setCompanyName($company);
        }

        if (array_key_exists('mail', $mailVars) && is_string($email = $mailVars['mail'])) {
            $crmRecord->setEmail($email);
        }
        if (array_key_exists('phonenumber', $mailVars) && is_string($phonenumber = $mailVars['phonenumber'])) {
            $crmRecord->setPhone($phonenumber);
        }
        if (array_key_exists('address', $mailVars) && is_string($address = $mailVars['address'])) {
            // might be useful
        }
        if (array_key_exists('comment', $mailVars) && is_string($comment = $mailVars['comment'])) {
            // might be useful
        }

        if (array_key_exists('nbpr_street', $exactVars) && is_string($street = $exactVars['nbpr_street'])) {
            $crmRecord->setStreet($street);
        }


        if (array_key_exists('nbpr_newsletter', $exactVars) && is_string($newsletterRequest = $exactVars['nbpr_newsletter'])) {
            $crmRecord->setNewsletterRequest('true');
        }

        if (array_key_exists('nbpr_zipcode', $exactVars) && is_string($zipCode = $exactVars['nbpr_zipcode'])) {
            $crmRecord->setPostalcode($zipCode);
        }

        if (array_key_exists('nbpr_city', $exactVars) && is_string($city = $exactVars['nbpr_city'])) {
            $crmRecord->setCity($city);
        }

        if (array_key_exists('nbpr_country', $exactVars) && is_string($country = $exactVars['nbpr_country'])) {
            $countryCode = $this->getCountryCodeById($country, $context);
            $crmRecord->setCountry($countryCode);
        }

        if (array_key_exists('date', $mailVars) && is_string($date = $mailVars['date'])) {
            $timestampFromDate = strtotime($date);
            if ($timestampFromDate != false) {
                $timestamp = $timestampFromDate;
            }
        }


        $crmRecord->setDcTimestamp($this->dcTimestampFormatting($timestamp));

        $lineItemsDatabaseFormattedArr = [];
        $lineItemsCrmFormattedArr = [];
        if (array_key_exists('lineItems', $mailVars) && is_array($lineItems = $mailVars['lineItems'])) {
            foreach ($lineItems as $item) {
                if (is_object($item)) {
                    $quantity = property_exists($item, 'quantity') ? $item->quantity : 'null';
                    $number = property_exists($item, 'number') ? $item->number : 'null';
                    if ($number !== 'null') {
                        $productEan = 'undefined';
                        $productCriteria = new Criteria();
                        $productCriteria->addFilter(new EqualsFilter('productNumber', $number));
                        $productResult = $this->productRepository->search($productCriteria, $context)->first();
                        if ($productResult instanceof ProductEntity) {
                            if ($productResult->getEan() !== null) {
                                $productEan = $productResult->getEan();
                            }
                        }
                        $databaseFormat = $quantity . 'x "' . $number . '" (EAN ' . $productEan . ')';
                        $crmFormat = $productEan . ";" . $quantity;
                        $lineItemsDatabaseFormattedArr[] = $databaseFormat;
                        $lineItemsCrmFormattedArr[] = $crmFormat;
                    }
                }
            }
        }
        $lineItemsDatabaseFormatted = implode("\n", $lineItemsDatabaseFormattedArr); //might be useful
        $lineItemsCrmFormatted = implode("|", $lineItemsCrmFormattedArr);

        if ($lineItemsCrmFormatted) {
            $crmRecord->setTenteInquiryProducts($lineItemsCrmFormatted);
        }

        $informationArr = $this->createRecordInformationArr($timestamp, $state, $lineItemsDatabaseFormatted);
        $information = implode("\r\n", $informationArr);

        $crmRecord->setInformation($information);

        return $crmRecord;
    }

    private function cadDownloadEventHandler(CadFileDownloadEvent $event, CrmRecord $crmRecord, Context $context): CrmRecord
    {
        $crmRecord->setCadRequest('true');
        $state = "";
        $timestamp = time();
        $fileName = null;

        $dataArray = $event->getDataArray();

        if (isset($dataArray['format'])) {
            $crmRecord->setCadFileFormat($dataArray['format']);
        }

        if (isset($dataArray['productNumber'])) {
            $crmRecord->setProductNumber($dataArray['productNumber']);
        }
        if (isset($dataArray['productEan'])) {
            $crmRecord->setProductEan($dataArray['productEan']);
        }

        if ($crmRecord->getPostalcode()) {
            $crmRecord->setPlz($crmRecord->getPostalcode());
        }

        $crmRecord->setDcTimestamp($this->dcTimestampFormatting($timestamp));

        $recordInformationMessage = 'CAD File downloaded = Yes'; //not sure
        $recordInformationMessage .= "\n\n"; // New line
        $recordInformationMessage .= 'File name : ' . $fileName; // Second variable

        $informationArr = $this->createRecordInformationArr($timestamp, $state, $recordInformationMessage);
        $information = implode("\r\n", $informationArr);

        $crmRecord->setInformation($information);

        return $crmRecord;
    }

    private function formatWebrequestId(string $id): string
    {
        //   return 'M2-' . $id;
        return $id;
    }

    private function getClientIpAddress()
    {
        $possibleHeaders = ['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
        foreach ($possibleHeaders as $header) {
            if (isset($_SERVER[$header]) && $_SERVER[$header] !== NULL && !empty($_SERVER[$header]) && is_string($_SERVER[$header])) {
                return $_SERVER[$header];
            }
        }
        return 'unknown';
    }
}
