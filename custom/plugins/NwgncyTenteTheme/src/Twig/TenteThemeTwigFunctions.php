<?php declare(strict_types=1);

namespace Nwgncy\TenteTheme\Twig;

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Twig\Extension\AbstractExtension;
use Shopware\Core\Framework\Context;
use Twig\TwigFunction;
use Twig\TwigFilter;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class TenteThemeTwigFunctions extends AbstractExtension
{
     private EntityRepository $categoryRepository;

     private EntityRepository $landingPageRepository;

     private EntityRepository $countryRepository;
     
     private EntityRepository $languageRepository;


     protected $sapCountries = [
          "Austria" => 10,
          "Germany" => 65,
          "Switzerland" => 168,
          "Liechtenstein" => 100,
          "Afghanistan" => 1,
          "Albania" => 2,
          "Algeria" => 3,
          "Andorra" => 4,
          "Angola" => 5,
          "Antigua and Barbuda" => 6,
          "Argentina" => 7,
          "Armenia" => 8,
          "Australia" => 9,
          "Azerbaijan" => 11,
          "Bahamas" => 12,
          "Bahrain" => 13,
          "Bangladesh" => 14,
          "Barbados" => 15,
          "Belarus" => 16,
          "Belgium" => 17,
          "Belize" => 18,
          "Benin" => 19,
          "Bhutan" => 20,
          "Bolivia" => 21,
          "Bosnia and Herzegovina" => 22,
          "Botswana" => 23,
          "Brazil" => 24,
          "Brunei Darussalam" => 25,
          "Bulgaria" => 26,
          "Burkina Faso" => 27,
          "Burma" => 28,
          "Burundi" => 29,
          "Cambodia" => 30,
          "Cameroon" => 31,
          "Canada" => 32,
          "Canary Islands" => 201,
          "Cape Verde" => 33,
          "Central African Republic" => 34,
          "Chad" => 35,
          "Chile" => 36,
          "China" => 37,
          "Colombia" => 38,
          "Comoros" => 39,
          "Congo" => 40,
          "Congo, Democratic Republic of the" => 41,
          "Costa Rica" => 42,
          "Cote d'Ivoire" => 43,
          "Croatia" => 44,
          "Cuba" => 45,
          "Cyprus" => 46,
          "Czech Republic" => 47,
          "Denmark" => 48,
          "Djibouti" => 49,
          "Dominica" => 50,
          "Dominican Republic" => 51,
          "East Timor" => 258,
          "Ecuador" => 52,
          "Egypt" => 53,
          "El Salvador" => 54,
          "Equatorial Guinea" => 55,
          "Eritrea" => 56,
          "Estonia" => 57,
          "Ethiopia" => 58,
          "Fiji" => 59,
          "Finland" => 60,
          "France" => 61,
          "Gabon" => 62,
          "Gambia, The" => 63,
          "Georgia" => 64,
          "Ghana" => 66,
          "Gibraltar" => 203,
          "Greece" => 67,
          "Greenland" => 198,
          "Grenada" => 68,
          "Guatemala" => 69,
          "Guinea" => 70,
          "Guinea-Bissau" => 71,
          "Guyana" => 72,
          "Haiti" => 73,
          "Honduras" => 74,
          "Hong Kong" => 205,
          "Hungary" => 75,
          "Iceland" => 76,
          "India" => 77,
          "Indonesia" => 78,
          "Iran" => 79,
          "Iraq" => 80,
          "Ireland" => 81,
          "Israel" => 82,
          "Italy" => 83,
          "Jamaica" => 84,
          "Japan" => 85,
          "Jordan" => 86,
          "Kazakhstan" => 87,
          "Kenya" => 88,
          "Kiribati" => 89,
          "Korea, North" => 90,
          "Korea, South" => 91,
          "Kosovo" => 259,
          "Kuwait" => 92,
          "Kyrgyzstan" => 93,
          "Laos" => 94,
          "Latvia" => 95,
          "Lebanon" => 96,
          "Lesotho" => 97,
          "Liberia" => 98,
          "Libya" => 99,
          "Lithuania" => 101,
          "Luxembourg" => 102,
          "Macau" => 206,
          "Macedonia" => 103,
          "Madagascar" => 104,
          "Malawi" => 105,
          "Malaysia" => 106,
          "Maldives" => 107,
          "Mali" => 108,
          "Malta" => 109,
          "Marshall Islands" => 110,
          "Mauritania" => 111,
          "Mauritius" => 112,
          "Mexico" => 113,
          "Micronesia" => 114,
          "Moldova" => 115,
          "Monaco" => 116,
          "Mongolia" => 117,
          "Montenegro" => 202,
          "Morocco" => 118,
          "Mozambique" => 119,
          "Myanmar" => 120,
          "Namibia" => 121,
          "Nauru" => 122,
          "Nepal" => 123,
          "Netherlands Antilles" => 204,
          "New Zealand" => 125,
          "Nicaragua" => 126,
          "Niger" => 127,
          "Nigeria" => 128,
          "Norway" => 129,
          "Oman" => 130,
          "Pakistan" => 131,
          "Palau" => 132,
          "Panama" => 134,
          "Papua New Guinea" => 135,
          "Paraguay" => 136,
          "Peru" => 137,
          "Philippines" => 138,
          "Poland" => 139,
          "Portugal" => 140,
          "Qatar" => 141,
          "Romania" => 142,
          "Russia" => 143,
          "Rwanda" => 144,
          "Samoa" => 148,
          "San Marino" => 149,
          "São Tomé and Príncipe" => 150,
          "Saudi Arabia" => 151,
          "Senegal" => 152,
          "Serbia" => 153,
          "Seychelles" => 154,
          "Sierra Leone" => 155,
          "Singapore" => 156,
          "Slovakia" => 157,
          "Slovenia" => 158,
          "Solomon Islands" => 159,
          "Somalia" => 160,
          "South Africa" => 161,
          "Spain" => 162,
          "Sri Lanka" => 163,
          "St. Kitts and Nevis" => 145,
          "St. Lucia" => 146,
          "St. Vincent and The Grenadines" => 147,
          "Sudan" => 164,
          "Suriname" => 165,
          "Swaziland" => 166,
          "Sweden" => 167,
          "Syria" => 169,
          "Taiwan" => 170,
          "Tajikistan" => 171,
          "Tanzania" => 172,
          "Thailand" => 173,
          "The Netherlands" => 124,
          "Togo" => 174,
          "Tonga" => 175,
          "Trinidad and Tobago" => 176,
          "Tunisia" => 177,
          "Turkey" => 178,
          "Turkmenistan" => 179,
          "Tuvalu" => 180,
          "Uganda" => 181,
          "Ukraine" => 182,
          "United Arab Emirates" => 183,
          "United Kingdom" => 184,
          "United States of America" => 185,
          "Uruguay" => 186,
          "Uzbekistan" => 187,
          "Vanuatu" => 188,
          "Vatican City" => 189,
          "Venezuela" => 190,
          "Vietnam" => 191,
          "Virgin Islands" => 199,
          "Western Sahara" => 192,
          "Yemen" => 193,
          "Yugoslavia" => 194,
          "Zaire" => 195,
          "Zambia" => 196,
          "Zimbabwe" => 197
     ];
     
     protected $sapLanguages = [
          'English' => 1,
          'German' => 2,
          'French' => 3,
          'Italian' => 4,
          'Spanish' => 8,
          'Swedish' => 9,
          'Norwegian' => 10,
          'Dutch' => 11,
          'Danish' => 12,
          'Finnish' => 13,
          'Portuguese' => 14,
          'Czech' => 15,
          'Hungarian' => 16,
          'Polish' => 17,
          'Romanian' => 18,
          'Slovenian' => 19,
          'Japanese' => 20,
          'Russian' => 21,
          'Bulgarian' => 22,
          'Moldavian' => 23,
          'Slovak' => 24,
          'Turkish' => 25,
          'Korean' => 26,
          'Thai' => 27,
          'Chinese (Simplified)' => 28,
          'Chinese (Traditional)' => 29,
          'Ukrainian' => 38,
          'Macedonian' => 39,
          'Arabic' => 40,
          'Serbocroatian' => 41,
          'Bosnian' => 42,
          'Hebrew' => 43,
          'Serbian' => 44,
          'Greek' => 45,
          'Latvian' => 46,
          'Estonian' => 47,
          'Croatian' => 48,
          'Hindi' => 49,
          'Portuguese (Brazil)' => 50,
          'Mexican' => 51,
          'Vietnamese' => 52,
          'Lithuanian' => 53,
          'Azerbaijani' => 54,
          'Georgian' => 55,
          'Afrikaans' => 58,
          'Persian' => 68
     ];
     

     public function __construct(
          EntityRepository $countryRepository, 
          EntityRepository $languageRepository,
          EntityRepository $categoryRepository,
          EntityRepository $landingPageRepository
     ) {
          $this->countryRepository = $countryRepository;
          $this->languageRepository = $languageRepository;
          $this->categoryRepository = $categoryRepository;
          $this->landingPageRepository = $landingPageRepository;
     }

     public function getFunctions()
     {
          return [
               new TwigFunction('getServiceMenuLeft', [$this, 'getServiceMenuLeft']),
               new TwigFunction('getContactPageID', [$this, 'getContactPageID']),
               new TwigFunction('getCountryList', [$this, 'getCountryList']),
               new TwigFunction('getLanguageList', [$this, 'getLanguageList']),
          ];
     }

     public function getFilters()
     {
         return [
             new TwigFilter('contains', [$this, 'contains']),
         ];
     }

     public function getContactPageID(SalesChannelContext $salesChannelContext)
     {
          $context = $salesChannelContext->getContext();
          $contactPageId = null;
            $criteria = new Criteria();
            $landings = $this->landingPageRepository->search($criteria, $context)->getElements();
            foreach($landings as $landing) {
                if(is_array($landing->getTranslated()['customFields'])) {
                    if(array_key_exists('is_contact_landing_page', $landing->getTranslated()['customFields'])) {
                        $contactPageId = $landing->getId();
                    }
                }
            }

            return $contactPageId;
     }

     public function getServiceMenuLeft(SalesChannelContext $salesChannelContext): ?CategoryCollection
     {
          $context = $salesChannelContext->getContext();
          $salesChannel = $salesChannelContext->getSalesChannel();
          if ($salesChannel instanceof SalesChannelEntity) {
               $salesChannelCustomFields = $salesChannel->getCustomFields();
               if (is_array($salesChannelCustomFields) && array_key_exists('serviceCategoryLeftId', $salesChannelCustomFields) && $serviceCategoryLeftId = $salesChannelCustomFields['serviceCategoryLeftId']) {
                    $criteria = new Criteria([$serviceCategoryLeftId]);
                    $criteria->addAssociation('children');
                    $serviceMenuLeftCategory = $this->categoryRepository->search($criteria, $context)->first();
                    if ($serviceMenuLeftCategory instanceof CategoryEntity) {
                         $children = $serviceMenuLeftCategory->getChildren();
                         return $children;
                    }
               }
          }
          return null;
     }

     public function contains($string, $substring)
     {
          return strpos($string, $substring) !== false;
     }

     public function getLanguageList($salesChannelContext)
     {

          $languageId = $salesChannelContext->getSalesChannel()->getLanguageId();
          $context = $salesChannelContext->getContext();

          $criteria = new Criteria();
          $criteria->addAssociation('locale');
          $criteria->addAssociation('locale.translations');
          $criteria->addAssociation('translations');
          $criteria->addFilter(new EqualsFilter('language.id', $languageId));

          $result = $this->languageRepository->search($criteria, $context);

          if ($result->getTotal() > 0) {
               $item = $result->first();
               $translations = $item->getLocale()->getTranslations();

               if (!empty($translations)) {
                    foreach ($translations as $translation) {
                         
                         if ($translation->getLanguageId() ==  Defaults::LANGUAGE_SYSTEM) {
                              $languageInDefault = $translation->getName();
                              if (isset($this->sapLanguages[$languageInDefault])) {
                                   return $this->sapLanguages[$languageInDefault];
                              }
                         }
                    }
                    return [];
               }
          }
          return [];
     }

     public function getCountryList($salesChannelContext)
     {
          $context = $salesChannelContext->getContext();
          $countryId = $salesChannelContext->getSalesChannel()->getCountryId();
          $currentLanguageId = $context->getLanguageId();
          $criteria = new Criteria();
          $criteria->addAssociation('translations');
          $countriesResult = $this->countryRepository->search($criteria, $context)->getEntities();
          return $this->mapCountryToCRMId($countriesResult, $currentLanguageId, $countryId);

     }

     public function mapCountryToCRMId($countries, $currentLanguageId, $currentCountryId)
     {

          $defaultLanguageId = Defaults::LANGUAGE_SYSTEM;
          $defaultCurrentLanguageArr = [];
          $primaryCountry = '';
          foreach ($countries as $country) {

               $countryId = $country->getId();
               $defaultLanguageCountryName = '';
               $currentLanguageCountryName = '';

               foreach ($country->getTranslations() as $translation) {

                    $languageId = $translation->getLanguageId();

                    if ($languageId == $defaultLanguageId) {
                         $defaultLanguageCountryName = $translation->getName();
                    }

                    if ($languageId == $currentLanguageId) {
                         if ($currentCountryId == $countryId) {
                              $primaryCountry = $translation->getName();
                         }
                         $currentLanguageCountryName = $translation->getName();
                    }
               }

               if ($currentLanguageCountryName == '') {
                    $currentLanguageCountryName = $defaultLanguageCountryName;
               }
               $defaultCurrentLanguageArr[$defaultLanguageCountryName] = $currentLanguageCountryName;

          }

          $sapCountries = $this->sapCountries;
          foreach ($defaultCurrentLanguageArr as $default => $current) {
               if (isset($sapCountries[$default])) {
                    $value = $sapCountries[$default];
                    unset($sapCountries[$default]);
                    $sapCountries = [$current => $value] + $sapCountries;
               }
          }

          ksort($sapCountries);

          if ($primaryCountry !== '') {
              
               if (isset($sapCountries[$primaryCountry])) {

                    $value = $sapCountries[$primaryCountry];
                    unset($sapCountries[$primaryCountry]);
                    $sapCountries = array($primaryCountry => $value) + $sapCountries;

               }
          }
          return $sapCountries;
     }
} 