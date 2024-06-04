<?php declare(strict_types = 1);

namespace Nwgncy\CrmConnector\Struct;

use Shopware\Core\Framework\Struct\Struct;

class CrmRecord extends Struct {
     //Consts
     public const COMPANY_NAME                   = 'companyname';
     public const LANGUAGE                       = 'language';
     public const STREET                         = 'street';
     public const POSTALCODE                     = 'postalcode';
     public const PLZ                            = 'plz';
     public const PRODUCT_EAN                    = 'productean';
     public const PRODUCT_NUMBER                 = 'productnumber';
     public const CAD_FORMAT                     = 'format';
     public const CITY                           = 'city';
     public const COUNTRY                        = 'country';
     public const CAD_REQUEST                    = 'cadrequest';
     public const CATALOG_REQUEST                = 'catalogrequest';
     public const NEWSLETTER_REQUEST             = 'newsletterrequest';
     public const PHONE                          = 'phone';
     public const FAX                            = 'fax';
     public const WEBREQUEST_ID                  = 'webrequestid';
     public const CALLING_WEBSITE_COUNTRY        = 'callingwebsitecountry';
     public const CAMPAIGN                       = 'campaign';
     public const TENTE_STATE                    = 'tente_state';
     public const TENTE_INQUIRY_PRODUCTS         = 'tente_inquiryproducts';
     public const TENTE_QUOTE_REQUEST            = 'tente_quoterequest';
     public const TENTE_ONLINE_SHOP_ORDER        = 'tente_onlineshoporder';
     public const TENTE_PHONECALL_REQUEST        = 'tente_phonecallrequest';
     public const TENTE_MEETING_REQUEST          = 'tente_meetingrequest';
     public const TENTE_FREE_TEXT_REQUEST        = 'tente_freetextrequest';
     public const TENTE_USER_ACCOUNT_CREATED     = 'tente_useraccountcreated';
     public const INFORMATION                    = 'information';
     public const EMAIL                          = 'email';
     public const FIRSTNAME                      = 'firstname';
     public const LASTNAME                       = 'lastname';
     public const HOMEPAGE                       = 'homepage';
     public const GENDER                         = 'gender';
     public const DEPARTMENT                     = 'department';
     public const ORIGIN                         = 'origin';
     public const TURNOVER                       = 'turnover';
     public const DC_COMPLIANCE                  = 'dc_compliance';
     public const DC_ADVERTISING_CONSENT         = 'dc_advertising_consent';
     public const DC_IP_ADDRESS                  = 'dc_ip_address';
     public const DC_TIMESTAMP                   = 'dc_timestamp';
     public const FORM_REQUEST_URL               = 'form_request_url';
     public const CUSTOM_FORM_HIDDEN_INPUTS      = 'custom_form_hidden_inputs';
     public const FUNCTION                       = 'function';
  
     //properties
     private string $companyName = '';
     private string $language = '';
     private string $street = '';
     private string $postalcode = '';
     private string $plz = '';
     private string $productnumber = '';
     private string $productean = '';
     private string $format = '';
     private string $city = '';
     private string $country = '';
     private string $cadRequest = 'false';
     private string $catalogRequest = 'false';
     private string $newsletterRequest = 'false';
     private string $phone = '';
     private string $fax = '';
     private string $webrequestId = '';
     private string $callingWebsiteCountry = '';
     private string $campaign = '';
     private string $tenteState = '';
     private string $tenteInquiryProducts = '';
     private string $tenteQuoteRequest = 'false';
     private string $tenteOnlineShopOrder = 'false';
     private string $tentePhonecallRequest = 'false';
     private string $tenteMeetingRequest = 'false';
     private string $tenteFreeTextRequest = 'false';
     private string $tenteUserAccountCreated = 'false';
     private string $information = '';
     private string $email = '';
     private string $firstname = '';
     private string $lastname = '';
     private string $homepage = '';
     private string $gender = '';
     private string $department = '';
     private string $origin = '';
     private string $turnover = 'C';
     private string $dcCompliance = 'true';
     private string $dcAdvertisingConsent = 'false';
     private string $dcIpAddress = '';
     private string $dcTimestamp = '';
     private string $formRequestUrl = '';

     private string $function = '';

     private array $customFormHiddenInputs = [];

     private array $customFormHiddenInputNames = [
        'CID',
        'SID',
        'UID',
        'f',
        'p',
        'a',
        'el',
        'llid',
        'c',
        'counted',
        'RID',
        'mailnow'
     ];

     //Register event missing : gender homepage turnover department origin dccompliance
     //cadrequest is caderequest
     public function setFunction(string $function): void {
          $this->function = $function;
     }
     
     public function getFunction(): string {
          return $this->function;
     }
     
     public function getCompanyName(): string {
          return $this->companyName;
     }
  
     public function setCompanyName(string $companyName): void {
          $this->companyName = $companyName;
     }
  
     public function getLanguage(): string {
          return $this->language;
     }

     public function setLanguage(string $language): void {
          $this->language = $language;
     }
  
     public function getStreet(): string {
          return $this->street;
     }
  
     public function setStreet(string $street): void {
          $this->street = $street;
     }
  
     public function getPostalcode(): string {
          return $this->postalcode;
     }
  
     public function setPostalcode(string $postalcode): void {
          $this->postalcode = $postalcode;
     }


        public function setProductNumber(string $Productnumber): void {
                $this->productnumber = $Productnumber;
        }


        public function getProductNumber(): string {
            return $this->productnumber;
        }

        public function setProductEan(string $Productean): void {
            $this->productean = $Productean;
        }

        public function getProductEan(): string {
            return $this->productean;
        }

        public function setPlz(string $plz): void {
            $this->plz = $plz;
        }

        public function getplz(): string {
            return $this->plz;
        }

        public function setCadFileFormat(string $CadFileFormat): void {
            $this->format = $CadFileFormat;
        }

        public function getCadFileFormat(): string {
            return $this->format;
        }
  
     public function getCity(): string {
          return $this->city;
     }
  
     public function setCity(string $city): void {
          $this->city = $city;
     }
  
     public function getCountry(): string {
          return $this->country;
     }
  
     public function setCountry(string $country): void {
          $this->country = $country;
     }
  
     public function getCadRequest(): string {
          return $this->cadRequest;
     }
  
     public function setCadRequest(string $cadRequest): void {
          $this->cadRequest = $cadRequest;
     }
  
     public function getCatalogRequest(): string {
          return $this->catalogRequest;
     }
  
     public function setCatalogRequest(string $catalogRequest): void {
          $this->catalogRequest = $catalogRequest;
     }
  
     public function getNewsletterRequest(): string {
          return $this->newsletterRequest;
     }
  
     public function setNewsletterRequest(string $newsletterRequest): void {
          $this->newsletterRequest = $newsletterRequest;
     }
  
     public function getPhone(): string {
          return $this->phone;
     }
  
     public function setPhone(string $phone): void {
          $this->phone = $phone;
     }
  
     public function getFax(): string {
          return $this->fax;
     }
  
     public function setFax(string $fax): void {
          $this->fax = $fax;
     }
  
     public function getWebrequestId(): string {
          return $this->webrequestId;
     }
  
     public function setWebrequestId(string $webrequestId): void {
          $this->webrequestId = $webrequestId;
     }
  
     public function getCallingWebsiteCountry(): string {
          return $this->callingWebsiteCountry;
     }
  
     public function setCallingWebsiteCountry(string $callingWebsiteCountry): void {
          $this->callingWebsiteCountry = $callingWebsiteCountry;
     }
  
     public function getCampaign(): string {
          return $this->campaign;
     }
  
     public function setCampaign(string $campaign): void {
          $this->campaign = $campaign;
     }
  
     public function getTenteState(): string {
          return $this->tenteState;
     }
  
     public function setTenteState(string $tenteState): void {
          $this->tenteState = $tenteState;
     }
  
     public function getTenteInquiryProducts(): string {
          return $this->tenteInquiryProducts;
     }
  
     public function setTenteInquiryProducts(string $tenteInquiryProducts): void {
          $this->tenteInquiryProducts = $tenteInquiryProducts;
     }
  
      public function getTenteQuoteRequest(): string {
          return $this->tenteQuoteRequest;
     }
  
     public function setTenteQuoteRequest(string $tenteQuoteRequest): void {
          $this->tenteQuoteRequest = $tenteQuoteRequest;
     }
  
     public function getTenteOnlineShopOrder(): string {
          return $this->tenteOnlineShopOrder;
     }
  
     public function setTenteOnlineShopOrder(string $tenteOnlineShopOrder): void {
          $this->tenteOnlineShopOrder = $tenteOnlineShopOrder;
     }
  
     public function getTentePhonecallRequest(): string {
          return $this->tentePhonecallRequest;
     }
  
     public function setTentePhonecallRequest(string $tentePhonecallRequest): void {
          $this->tentePhonecallRequest = $tentePhonecallRequest;
     }
  
     public function getTenteMeetingRequest(): string {
          return $this->tenteMeetingRequest;
     }
  
     public function setTenteMeetingRequest(string $tenteMeetingRequest): void {
          $this->tenteMeetingRequest = $tenteMeetingRequest;
     }
  
     public function getTenteFreeTextRequest(): string {
          return $this->tenteFreeTextRequest;
     }
  
     public function setTenteFreeTextRequest(string $tenteFreeTextRequest): void {
          $this->tenteFreeTextRequest = $tenteFreeTextRequest;
     }
  
     public function getTenteUserAccountCreated(): string {
          return $this->tenteUserAccountCreated;
     }
  
     public function setTenteUserAccountCreated(string $tenteUserAccountCreated): void {
          $this->tenteUserAccountCreated = $tenteUserAccountCreated;
     }
  
     public function getInformation(): string {
          return $this->information;
     }
  
     public function setInformation(string $information): void {
          $this->information = $information;
     }
  
     public function getEmail(): string {
          return $this->email;
     }
  
     public function setEmail(string $email): void {
          $this->email = $email;
     }
  
      public function getFirstname(): string {
          return $this->firstname;
      }
  
      public function setFirstname(string $firstname): void {
          $this->firstname = $firstname;
      }
  
      public function getLastname(): string {
          return $this->lastname;
      }
  
      public function setLastname(string $lastname): void {
          $this->lastname = $lastname;
      }
  
      public function getHomepage(): string {
          return $this->homepage;
      }
  
      public function setHomepage(string $homepage): void {
          $this->homepage = $homepage;
      }
  
      public function getGender(): string {
          return $this->gender;
      }
  
      public function setGender(string $gender): void {
          $this->gender = $gender;
      }
  
      public function getDepartment(): string {
          return $this->department;
      }
  
      public function setDepartment(string $department): void {
          $this->department = $department;
      }
  
      public function getOrigin(): string {
          return $this->origin;
      }
  
      public function setOrigin(string $origin): void {
          $this->origin = $origin;
      }
  
      public function getTurnover(): string {
          return $this->turnover;
      }
  
      public function setTurnover(string $turnover): void {
          $this->turnover = $turnover;
      }
  
      public function getDcCompliance(): string {
          return $this->dcCompliance;
      }
  
      public function setDcCompliance(string $dcCompliance): void {
          $this->dcCompliance = $dcCompliance;
      }
  
      public function getDcAdvertisingConsent(): string {
          return $this->dcAdvertisingConsent;
      }
  
      public function setDcAdvertisingConsent(string $dcAdvertisingConsent): void {
          $this->dcAdvertisingConsent = $dcAdvertisingConsent;
      }
  
      public function getDcIpAddress(): string {
          return $this->dcIpAddress;
      }
  
      public function setDcIpAddress(string $dcIpAddress): void {
          $this->dcIpAddress = $dcIpAddress;
      }
  
      public function getDcTimestamp(): string {
          return $this->dcTimestamp;
      }
  
     public function setDcTimestamp(string $dcTimestamp): void {
          $this->dcTimestamp = $dcTimestamp;
     }
  
     public function setFormRequestUrl(string $formRequestUrl): void {
          $this->formRequestUrl = $formRequestUrl;
     }
  
     public function getFormRequestUrl(string $formRequestUrl): string {
          return $this->formRequestUrl;
     }
     
     public function addDataToCustomFormHiddenInputs($name, $value): void {
          $this->customFormHiddenInputs[$name] = $value;
     }

     public function getDataToCustomFormHiddenInputs(): array {
          return $this->customFormHiddenInputs;
     }
       
     public function getCustomFormHiddenInputNames(): array {
          return $this->customFormHiddenInputNames;
     }
       
     public function getCustomFormData(): array {
          return [
               'inp_1' => $this->firstname,
               'inp_2' => $this->lastname,
               'inp_3'  => $this->email,
               'inp_18'  => $this->companyName,
               'inp_9912'  => $this->function,
               'inp_45'  => $this->country,
               'inp_35'  => $this->language,
               'inp_21'  => $this->phone,
               'inp_8463'  => $this->information
          ];
     }

     public function getData(): array {
          return array(
               self::COMPANY_NAME => $this->companyName,
               self::LANGUAGE => $this->language,
               self::STREET => $this->street,
               self::POSTALCODE => $this->postalcode,
               self::CITY => $this->city,
               self::COUNTRY => $this->country,
               self::CAD_REQUEST => $this->cadRequest,
               self::CATALOG_REQUEST => $this->catalogRequest,
               self::NEWSLETTER_REQUEST => $this->newsletterRequest,
               self::PHONE => $this->phone,
               self::FAX => $this->fax,
               self::PLZ => $this->plz,
               self::PRODUCT_EAN => $this->productean,
               self::PRODUCT_NUMBER => $this->productnumber,
               self::CAD_FORMAT => $this->format,
               self::WEBREQUEST_ID => $this->webrequestId,
               self::CALLING_WEBSITE_COUNTRY => $this->callingWebsiteCountry,
               self::CAMPAIGN => $this->campaign,
               self::TENTE_STATE => $this->tenteState,
               self::TENTE_INQUIRY_PRODUCTS => $this->tenteInquiryProducts,
               self::TENTE_QUOTE_REQUEST => $this->tenteQuoteRequest,
               self::TENTE_ONLINE_SHOP_ORDER => $this->tenteOnlineShopOrder,
               self::TENTE_PHONECALL_REQUEST => $this->tentePhonecallRequest,
               self::TENTE_MEETING_REQUEST => $this->tenteMeetingRequest,
               self::TENTE_FREE_TEXT_REQUEST => $this->tenteFreeTextRequest,
               self::TENTE_USER_ACCOUNT_CREATED => $this->tenteUserAccountCreated,
               self::INFORMATION => $this->information,
               self::EMAIL => $this->email,
               self::FIRSTNAME => $this->firstname,
               self::LASTNAME => $this->lastname,
               self::HOMEPAGE => $this->homepage,
               self::GENDER => $this->gender,
               self::DEPARTMENT => $this->department,
               self::ORIGIN => $this->origin,
               self::TURNOVER => $this->turnover,
               self::DC_COMPLIANCE => $this->dcCompliance,
               self::DC_ADVERTISING_CONSENT => $this->dcAdvertisingConsent,
               self::DC_IP_ADDRESS => $this->dcIpAddress,
               self::DC_TIMESTAMP => $this->dcTimestamp
          );
     }





}