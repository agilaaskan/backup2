<?php
/**
 *  Canada Post Shipping Module.
 *
 * @category  Shipping & Fulfillment
 * @package   Rootways_Canadapost
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2021 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
 */

namespace Rootways\Canadapost\Helper;

use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Directory\Model\Currency\Import\AbstractImport;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;
    
    /** @var \Magento\Framework\Encryption\EncryptorInterface */
    protected $_encryptor;
    
    /** @var \Magento\Config\Model\ResourceModel\Config */
    protected $resourceConfig;
    
    /** @var \Magento\Directory\Model\RegionFactory */
    protected $regionFactory;
    
    /** @var \Magento\Directory\Model\CountryFactory */
    protected $countryFactory;
    
    /** @var \Magento\Framework\Module\Dir\Reader */
    protected $moduleReader;

    /** @var \Magento\Backend\Helper\Data */
    protected $backendHelperData ;
    
    /** @var \Magento\Sales\Api\Data\OrderInterface */
    protected $orderInterface ;

    /** @var \Rootways\Canadapost\Model\ResourceModel\Manifest\CollectionFactory */
    private $manifestCollectionFactory;

    /** @var \Magento\Framework\Registry */
    private $_registry;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Backend\Helper\Data $backendHelperData,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Rootways\Canadapost\Model\ResourceModel\Manifest\CollectionFactory $manifestCollectionFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_storeManager = $storeManager;
        $this->_encryptor = $encryptor;
        $this->_customresourceConfig = $resourceConfig;
        $this->_regionFactory = $regionFactory;
        $this->_countryFactory = $countryFactory;
        $this->_dir = $dir;
        $this->_currencyFactory = $currencyFactory;
        $this->_directoryData = $directoryData;
        $this->moduleReader = $moduleReader;
        $this->backendHelperData  = $backendHelperData;
        $this->orderInterface = $orderInterface;
        $this->manifestCollectionFactory = $manifestCollectionFactory;
        $this->_registry = $registry;
        parent::__construct($context);
    }
    
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * @return string
     */
    public function getShippingMode($storeId = null)
    {
        return $this->getConfig('carriers/rwcanadapost/mode', $storeId);
    }
    
    /**
     * @return string
     */
    public function getLocale($storeId = null)
    {
        return $this->getConfig('carriers/rwcanadapost/locale', $storeId);
    }
    
    /**
     * @return string
     */
    public function getHostName($storeId = null)
    {
        $val = $this->getShippingMode() == 'development' ? "ct.soa-gw.canadapost.ca" : "soa-gw.canadapost.ca";

        return $val;
    }
    
    /**
     * Get value of Post URL.
    */
    public function getPostUrl($storeId = null)
    {
        $val = $this->getShippingMode() == 'development' ?
            "https://ct.soa-gw.canadapost.ca/rs/ship/price" :
            "https://soa-gw.canadapost.ca/rs/ship/price";

        return $val;
    }
    
     /**
     * Get value of Post URL.
    */
    public function getRateUrl()
    {
        return 'http://www.canadapost.ca/ws/ship/rate-v3';
    }
    
    /**
     * Get value of API Key.
    */
    public function getApiKey($storeId = null)
    {
        $key = $this->getConfig('carriers/rwcanadapost/apikey', $storeId);

        return $this->_encryptor->decrypt($key);
    }
    
    /**
     * Get value of Password.
    */
    public function getPassword($storeId = null)
    {
        $password = $this->getConfig('carriers/rwcanadapost/password', $storeId);

        return $this->_encryptor->decrypt($password);
    }

    /**
     * Get value of Qyote Type.
    */
    public function getQuoteType($storeId = null)
    {
        return $this->getConfig('carriers/rwcanadapost/quote_type', $storeId);
    }

    /**
     * Get value of Quote Type.
     */
    public function getQuoteTypeValue($storeId = null)
    {
        return $this->getQuoteType($storeId) == "1" ? "commercial" : "counter";
    }
    
    /**
     * Get value of Customer.
    */
    public function getCustomerNumber($storeId = null)
    {
        return $this->getConfig('carriers/rwcanadapost/customer', $storeId);
    }
    
    /** @rerutn string */
    public function getContractId($storeId = null)
    {
        return $this->getConfig('carriers/rwcanadapost/contract_id', $storeId);
    }

    /** @rerutn boolean */
    public function allowCustomValueToShipmentForm(): bool
    {
        if ($this->getQuoteType() != "1" ||
            $this->getContractId() == '') {
            return false;
        }

        $shipment = $this->_registry->registry('current_shipment');
        if ($shipment) {
            $shippingMethod = $shipment->getOrder()->getShippingMethod();
            $shippingMethod = explode('_', $shippingMethod);
            if (isset($shippingMethod[0]) && $shippingMethod[0] == \Rootways\Canadapost\Model\Carrier::CODE) {
                return true;
            }
        }

        return false;
    }

    /** @return array */
    public function getManifestsIds()
    {
        $collection = $this->manifestCollectionFactory->create();
        $collection->addFieldToFilter('status', ['eq' => 'pending']);

        return $collection->getAllIds();
    }

    /** @rerutn string */
    public function getMethodOfPayment($storeId = null)
    {
        return $this->getConfig('carriers/rwcanadapost/method_of_payment', $storeId);
    }

    /** @rerutn bool */
    public function isReturnLabelEnabled($storeId = null): bool
    {
        return $this->getConfig('carriers/rwcanadapost/return_label', $storeId);
    }
    
    /**
     * Get value of licence key from admin
    */
    public function licencekey()
    {
        return $this->getConfig('rootways_canadapost_license/general/licencekey');
    }
    
    /**
     * Get Allowed Shipping Methods
    */
    public function getAllowedMethods($storeId = null)
    {
        return $this->getConfig('carriers/rwcanadapost/allowed_methods', $storeId);
    }
    
    /**
     * Get value of secure URL from admin
    */
    public function surl()
    {
        return "aHR0cHM6Ly93d3cucm9vdHdheXMuY29tL20ydmVyaWZ5bGljLnBocA==";
    }
    
    /**
     * Get value of licence key from admin
    */
    public function act()
    {
        $dt_db_blank = $this->getConfig('rootways_canadapost_license/general/lcstatus');
        if ($dt_db_blank == '') {
            $isMultiStore =  $this->getConfig('rootways_canadapost_license/general/ismultistore');
            $u = $this->_storeManager->getStore()->getBaseUrl();
            if ($isMultiStore == 1)  {
                $u = $this->backendHelperData->getHomePageUrl();
            }
            $l = $this->getConfig('rootways_canadapost_license/general/licencekey');
            $surl = base64_decode($this->surl());
            $url = $surl."?u=".$u."&l=".$l."&extname=m2_canadapost";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result=curl_exec($ch);
            curl_close($ch);
            $act_data = json_decode($result, true);
            if (!isset($act_data['status'])) {
                return "PGRpdiBzdHlsZT0iYmFja2dyb3VuZDogYmxhY2sgbm9uZSByZXBlYXQgc2Nyb2xsIDAlIDAlOyBmbG9hdDogbGVmdDsgcGFkZGluZzogMTBweDsgd2lkdGg6IDEwMCU7IGNvbG9yOiByZWQ7IiBpZD0ibm90X2FjdGl2YXRlZCI+SXNzdWUgd2l0aCBleHRlbnNpb24gbGljZW5zZSBrZXksIHBsZWFzZSBjb250YWN0IDxhIGhyZWY9Im1haWx0bzpoZWxwQHJvb3R3YXlzLmNvbSI+aGVscEByb290d2F5cy5jb208L2E+PC9kaXY+";
            }
            if ($act_data['status'] == '0') {
                return "PGRpdiBzdHlsZT0iYmFja2dyb3VuZDogYmxhY2sgbm9uZSByZXBlYXQgc2Nyb2xsIDAlIDAlOyBmbG9hdDogbGVmdDsgcGFkZGluZzogMTBweDsgd2lkdGg6IDEwMCU7IGNvbG9yOiByZWQ7IiBpZD0ibm90X2FjdGl2YXRlZCI+SXNzdWUgd2l0aCBleHRlbnNpb24gbGljZW5zZSBrZXksIHBsZWFzZSBjb250YWN0IDxhIGhyZWY9Im1haWx0bzpoZWxwQHJvb3R3YXlzLmNvbSI+aGVscEByb290d2F5cy5jb208L2E+PC9kaXY+";
            } else {
                $this->_customresourceConfig->saveConfig('rootways_canadapost_license/general/lcstatus', $l, 'default', 0);
            }
        }
    }
    
    /**
     * Get value of Region Code.
    */
    public function getRegionCode($shipperRegionId)
    {
        $shipperRegion = $this->_regionFactory->create()->load($shipperRegionId );

        return $shipperRegion->getCode();
    }
    
    /**
     * Get value of Country ID.
    */
    public function getCountryName($countryCode)
    {
        $country = $this->_countryFactory->create()->loadByCode($countryCode);

        return $country->getName();
    }
    
    /**
     * @return string
     */
    public function getDirectory()
    {
        $etcDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_ETC_DIR,
            'Rootways_Canadapost'
        );

        return $etcDir;
    }
    
    /**
     * @return string
     */
    public function getWeightUnit($storeId = null)
    {
        $weightUnit = 'KILOGRAM';
        if ($this->getConfig('general/locale/weight_unit', $storeId) == 'lbs') {
            $weightUnit = 'POUND';
        }

        return $weightUnit;
    }
    
    public function convertMeasureWeight($value, $sourceWeightMeasure, $toWeightMeasure)
    {
        if ($value) {
            $unitWeight = new \Zend_Measure_Weight($value, $sourceWeightMeasure, 'en_US');
            $unitWeight->setType($toWeightMeasure);
            return $unitWeight->getValue();
        }

        return null;
    }
    
    public function convertMeasureDimension($value, $sourceWeightMeasure, $toWeightMeasure)
    {
        if ($value) {
            $length = new \Zend_Measure_Length($value, $sourceWeightMeasure, 'en_US');
            $length->setType($toWeightMeasure);
            return $length->getValue();
        }

        return null;
    }
    
    public function getCurrencyRate($currencyFrom, $currencyTo)
    {
        $rate = null;
        $allowedCurrencies = $this->_currencyFactory->create()->getConfigAllowCurrencies();
        try {
            if (in_array($currencyTo, $allowedCurrencies) && in_array($currencyFrom, $allowedCurrencies)) {
                $rate = $this->_directoryData->currencyConvert(1, $currencyFrom, $currencyTo);
            }
        } catch (\Exception $e) {
            $rate = null;
        }

        return $rate;
    }
    
    public function getCurrencyByCountries($c) {
        //Credit https://wpcodebook.com/snippets/get-currency-countries-codes-as-array-in-php/
        $crArray = [
            'AF' => 'AFA', 'AL' => 'ALL', 'DZ' => 'DZD', 'AS' => 'USD', 'AD' => 'EUR', 'AO' => 'AOA', 'AI' => 'XCD',
            'AQ' => 'NOK', 'AG' => 'XCD', 'AR' => 'ARA', 'AM' => 'AMD', 'AW' => 'AWG', 'AU' => 'AUD', 'AT' => 'EUR',
            'AZ' => 'AZM', 'BS' => 'BSD', 'BH' => 'BHD', 'BD' => 'BDT', 'BB' => 'BBD', 'BY' => 'BYR', 'BE' => 'EUR',
            'BZ' => 'BZD', 'BJ' => 'XAF', 'BM' => 'BMD', 'BT' => 'BTN', 'BO' => 'BOB', 'BA' => 'BAM', 'BW' => 'BWP',
            'BV' => 'NOK', 'BR' => 'BRL', 'IO' => 'GBP', 'BN' => 'BND', 'BG' => 'BGN', 'BF' => 'XAF', 'BI' => 'BIF',
            'KH' => 'KHR', 'CM' => 'XAF', 'CA' => 'CAD', 'CV' => 'CVE', 'KY' => 'KYD', 'CF' => 'XAF', 'TD' => 'XAF',
            'CL' => 'CLF', 'CN' => 'CNY', 'CX' => 'AUD', 'CC' => 'AUD', 'CO' => 'COP', 'KM' => 'KMF', 'CD' => 'CDZ',
            'CG' => 'XAF', 'CK' => 'NZD', 'CR' => 'CRC', 'HR' => 'HRK', 'CU' => 'CUP', 'CY' => 'EUR', 'CZ' => 'CZK',
            'DK' => 'DKK', 'DJ' => 'DJF', 'DM' => 'XCD', 'DO' => 'DOP', 'TP' => 'TPE', 'EC' => 'USD', 'EG' => 'EGP',
            'SV' => 'USD', 'GQ' => 'XAF', 'ER' => 'ERN', 'EE' => 'EEK', 'ET' => 'ETB', 'FK' => 'FKP', 'FO' => 'DKK',
            'FJ' => 'FJD', 'FI' => 'EUR', 'FR' => 'EUR', 'FX' => 'EUR', 'GF' => 'EUR', 'PF' => 'XPF', 'TF' => 'EUR',
            'GA' => 'XAF', 'GM' => 'GMD', 'GE' => 'GEL', 'DE' => 'EUR', 'GH' => 'GHC', 'GI' => 'GIP', 'GR' => 'EUR',
            'GL' => 'DKK', 'GD' => 'XCD', 'GP' => 'EUR', 'GU' => 'USD', 'GT' => 'GTQ', 'GN' => 'GNS', 'GW' => 'GWP',
            'GY' => 'GYD', 'HT' => 'HTG', 'HM' => 'AUD', 'VA' => 'EUR', 'HN' => 'HNL', 'HK' => 'HKD', 'HU' => 'HUF',
            'IS' => 'ISK', 'IN' => 'INR', 'ID' => 'IDR', 'IR' => 'IRR', 'IQ' => 'IQD', 'IE' => 'EUR', 'IL' => 'ILS',
            'IT' => 'EUR', 'CI' => 'XAF', 'JM' => 'JMD', 'JP' => 'JPY', 'JO' => 'JOD', 'KZ' => 'KZT', 'KE' => 'KES',
            'KI' => 'AUD', 'KP' => 'KPW', 'KR' => 'KRW', 'KW' => 'KWD', 'KG' => 'KGS', 'LA' => 'LAK', 'LV' => 'LVL',
            'LB' => 'LBP', 'LS' => 'LSL', 'LR' => 'LRD', 'LY' => 'LYD', 'LI' => 'CHF', 'LT' => 'LTL', 'LU' => 'EUR',
            'MO' => 'MOP', 'MK' => 'MKD', 'MG' => 'MGF', 'MW' => 'MWK', 'MY' => 'MYR', 'MV' => 'MVR', 'ML' => 'XAF',
            'MT' => 'EUR', 'MH' => 'USD', 'MQ' => 'EUR', 'MR' => 'MRO', 'MU' => 'MUR', 'YT' => 'EUR', 'MX' => 'MXN',
            'FM' => 'USD', 'MD' => 'MDL', 'MC' => 'EUR', 'MN' => 'MNT', 'MS' => 'XCD', 'MA' => 'MAD', 'MZ' => 'MZM',
            'MM' => 'MMK', 'NA' => 'NAD', 'NR' => 'AUD', 'NP' => 'NPR', 'NL' => 'EUR', 'AN' => 'ANG', 'NC' => 'XPF',
            'NZ' => 'NZD', 'NI' => 'NIC', 'NE' => 'XOF', 'NG' => 'NGN', 'NU' => 'NZD', 'NF' => 'AUD', 'MP' => 'USD',
            'NO' => 'NOK', 'OM' => 'OMR', 'PK' => 'PKR', 'PW' => 'USD', 'PA' => 'PAB', 'PG' => 'PGK', 'PY' => 'PYG',
            'PE' => 'PEI', 'PH' => 'PHP', 'PN' => 'NZD', 'PL' => 'PLN', 'PT' => 'EUR', 'PR' => 'USD', 'QA' => 'QAR',
            'RE' => 'EUR', 'RO' => 'ROL', 'RU' => 'RUB', 'RW' => 'RWF', 'KN' => 'XCD', 'LC' => 'XCD', 'VC' => 'XCD',
            'WS' => 'WST', 'SM' => 'EUR', 'ST' => 'STD', 'SA' => 'SAR', 'SN' => 'XOF', 'CS' => 'EUR', 'SC' => 'SCR',
            'SL' => 'SLL', 'SG' => 'SGD', 'SK' => 'EUR', 'SI' => 'EUR', 'SB' => 'SBD', 'SO' => 'SOS', 'ZA' => 'ZAR',
            'GS' => 'GBP', 'ES' => 'EUR', 'LK' => 'LKR', 'SH' => 'SHP', 'PM' => 'EUR', 'SD' => 'SDG', 'SR' => 'SRG',
            'SJ' => 'NOK', 'SZ' => 'SZL', 'SE' => 'SEK', 'CH' => 'CHF', 'SY' => 'SYP', 'TW' => 'TWD', 'TJ' => 'TJR',
            'TZ' => 'TZS', 'TH' => 'THB', 'TG' => 'XAF', 'TK' => 'NZD', 'TO' => 'TOP', 'TT' => 'TTD', 'TN' => 'TND',
            'TR' => 'TRY', 'TM' => 'TMM', 'TC' => 'USD', 'TV' => 'AUD', 'UG' => 'UGS', 'UA' => 'UAH', 'SU' => 'SUR',
            'AE' => 'AED', 'GB' => 'GBP', 'US' => 'USD', 'UM' => 'USD', 'UY' => 'UYU', 'UZ' => 'UZS', 'VU' => 'VUV',
            'VE' => 'VEF', 'VN' => 'VND', 'VG' => 'USD', 'VI' => 'USD', 'WF' => 'XPF', 'XO' => 'XOF', 'EH' => 'MAD',
            'ZM' => 'ZMK', 'ZW' => 'USD'
        ];
        $crCode = $crArray[$c];

        return $crCode;
    }
}
