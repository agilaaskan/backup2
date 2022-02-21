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

namespace Rootways\Canadapost\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Xml\Security;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\Result\ProxyDeferredFactory;
use Rootways\Canadapost\Model\Method\Logger as RwLogger;

/**
 * Rootways Canadapost shipping
 */
class Carrier extends AbstractCarrierOnline implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /** @var string */
    const CODE = 'rwcanadapost';

    /** @var string */
    const MANIFEST_COMPLETED = 'completed';

    /** @var string */
    const MANIFEST_PENDING = 'pending';

    /** @var string */
    protected $_code = self::CODE;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $rateMethodFactory;
    
    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;
    
    /** @var array */
    protected $type;

    /** @var \Magento\Framework\Registry */
    protected $_registry;

    /** @var RwLogger */
    private $rwLogger;

    /**
     * @param \Rootways\Canadapost\Helper\Data $customHelper
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param Security $xmlSecurity
     * @param \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Directory\Helper\Data $directoryData
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\Registry $registry
     * @param RwLogger $rwLogger
     * @param array $data
     */
    public function __construct(
        \Rootways\Canadapost\Helper\Data $customHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateTime,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        Security $xmlSecurity,
        \Magento\Shipping\Model\Simplexml\ElementFactory $xmlElFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Shipping\Model\Tracking\ResultFactory $trackFactory,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\Registry $registry,
        RwLogger $rwLogger,
        array $data = []
    ) {
        $this->_rateErrorFactory = $rateErrorFactory;
        $this->_rateResultFactory = $rateFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->customHelper = $customHelper;
        $this->_productRepository = $productRepository;
        $this->_localeDate = $dateTime;
        $this->_trackErrorFactory = $trackErrorFactory;
        $this->_registry = $registry;
        $this->rwLogger = $rwLogger;
        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $xmlSecurity,
            $xmlElFactory,
            $rateFactory,
            $rateMethodFactory,
            $trackFactory,
            $trackErrorFactory,
            $trackStatusFactory,
            $regionFactory,
            $countryFactory,
            $currencyFactory,
            $directoryData,
            $stockRegistry,
            $data
        );
    }
    
    public function getAllowedMethods()
    {
        $allowed = explode(",", $this->customHelper->getAllowedMethods());
        $arr = [];
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
        }
        
        return $arr;
    }
    
    public function collectRates(RateRequest $request)
    {
        if ( !$this->getConfigFlag('active') ) {
            return false;
        }
 
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $storeId = $this->getStoreId($request);
        
        $apiKey = $this->customHelper->getApiKey($storeId);
        $password = $this->customHelper->getPassword($storeId);
        
        $destCountryId = $request->getDestCountryId();
	    $zip_code = trim(preg_replace('/\s+/', '', $request->getDestPostcode()));
	    $zip = strtoupper($zip_code);
		
        if ($request->getOrigCountry()) {
            $sourceCountry = $request->getOrigCountry();
        } else {
            $sourceCountry = $this->_scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->getStoreId($request)
            );
        }
        if ($request->getOrigPostcode()) {
        	$oripost = $request->getOrigPostcode();
            $originpost = trim(preg_replace('/\s+/', '', $oripost));
        } else {
            $oripost = $this->_scopeConfig->getValue(
                \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->getStoreId($request)
            );
            $originpost = trim(preg_replace('/\s+/', '', $oripost));
        }
        
        $price = 0;
        $w = 0;
        /*foreach ($request->getAllItems() as $item) {
            $q = $item->getQty();
            $price = $price+($item->getPrice())*$q;
            $w = $w+($item->getWeight())*$q;
        }*/
        //file_put_contents('vish_request_data.txt', print_r($request->debug(), true), FILE_APPEND);
        if($request->getPackageValueWithDiscount()) {
            $price = $request->getPackageValueWithDiscount();
        }
        if ($request->getPackageWeight()) {
            $w = $request->getPackageWeight();
        }
        
        $weightUnit = $this->customHelper->getWeightUnit($storeId);
        $weight = $this->convertWeightToKg($w, $weightUnit);
        $allowFreeShip = 0;
        if ($this->customHelper->getConfig('carriers/rwcanadapost/free_shipping_enable', $storeId)
            && $this->customHelper->getConfig('carriers/rwcanadapost/free_shipping_subtotal', $storeId) != '') {
            if ($price >= $this->customHelper->getConfig('carriers/rwcanadapost/free_shipping_subtotal', $storeId)) {
                $allowFreeShip = 1;
            }
        }
        
        if ($price >5000 && $destCountryId=="CA") {
            return false;
        } 
        if ($price >1000 && $destCountryId!="CA") {
            return false;
        }
        if ($price <=0 OR $w==0) {        	 
            return false;
        }
        if ($sourceCountry !="CA") {
            return false;
        }
        
        if ($destCountryId == 'CA') {
            $destination = [
                'domestic' => [
                    'postal-code' => $zip
                ]
            ];
        } else if ($destCountryId=='US') {
            $destination = [
                'united-states' => [
                    'zip-code' => $zip
                ]
            ];
        } else {
             $destination = [
                'international' => [
                    'country-code' => $destCountryId
                ]
            ];
        }
        
        $quoteType = $this->customHelper->getQuoteType($storeId);
        $mailing_scenario = [
            'quote-type' =>  $this->customHelper->getQuoteTypeValue($storeId)
        ];
        
        if ($quoteType == '1') {
            $mailing_scenario['customer-number'] = $this->customHelper->getCustomerNumber($storeId);
            $mailing_scenario['contract-id'] = $this->customHelper->getContractId($storeId);
        }
        
        $mailing_scenario['parcel-characteristics'] = [
            'weight' => $weight,
            'unpackaged' => false,
            'mailing-tube'=> false
        ];
        
        $length = $this->customHelper->getConfig('carriers/rwcanadapost/default_length', $storeId);
        $width = $this->customHelper->getConfig('carriers/rwcanadapost/default_width', $storeId);
        $height = $this->customHelper->getConfig('carriers/rwcanadapost/default_height', $storeId);
        if ($length || $width || $height) {
            $mailing_scenario['parcel-characteristics']['dimensions'] = [
                'length'    => round($length, 1),
                'width'     => round($width, 1),
                'height'    => round($height, 1)
            ];
        }

        $mailing_scenario['origin-postal-code'] = $originpost;
        $mailing_scenario['destination'] = $destination;
        
        $params = [
            'get-rates-request' => [
                'locale' => $this->customHelper->getLocale($storeId),
                'mailing-scenario' => $mailing_scenario
            ]
        ];
        //file_put_contents('vish_canadapost_getquote_request.txt', print_r($params, true), FILE_APPEND);

        $wsdlFileName = '/libcanadapost/wsdl/rating.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'rating', $storeId);
        $response = $client->__soapCall('GetRates', $params, null, null);
        //file_put_contents('var/log/rw_cpost_rate_respo.txt', print_r($response, true), FILE_APPEND);
        $errorMsg = '';
        if (isset($response->{'price-quotes'})) {
            $allowedMethods = explode(",", $this->customHelper->getAllowedMethods($storeId));
            foreach ($response->{'price-quotes'}->{'price-quote'} as $priceQuote ) {
                $method = (string)$priceQuote->{'service-code'};
                if (in_array($method, $allowedMethods)) {
                    $title = $priceQuote->{'service-name'};
                    if ($this->customHelper->getConfig('carriers/rwcanadapost/show_delivery_date', $storeId)) {
                        if (isset($priceQuote->{'service-standard'}->{'expected-delivery-date'})) {
                            $title .= ' - ' .
                                __('Est. Delivery %1',
                                   $this->_localeDate->formatDateTime(
                                       $priceQuote->{'service-standard'}->{'expected-delivery-date'},
                                       \IntlDateFormatter::MEDIUM,
                                       \IntlDateFormatter::NONE
                                   ));
                        }
                    }
                    
                    if ($this->customHelper->getConfig('carriers/rwcanadapost/rates_price_type', $storeId)) {
                        $price = $priceQuote->{'price-details'}->{'due'}; // Including Tax
                    } else {
                        $price = $priceQuote->{'price-details'}->{'base'}; // Excluding Tax
                        if (isset($priceQuote->{'price-details'}->{'adjustments'})) {
                            foreach ($priceQuote->{'price-details'}->{'adjustments'}->{'adjustment'} as $adjustment) {
                                $price += $adjustment->{'adjustment-cost'};
                            }
                        }
                    }
                    if ($allowFreeShip == 1) {
                        if($this->calculateFeeShippingMethod($method, $destCountryId, $storeId)) {
                            $price = 0.00;
                            $title = __('Free Shipping'). ' - '. $title;
                        }
                    }
                    $result->append($this->_getDefaultRate($price, $method, $title, $storeId));
                }
            }
            
        } else {
            $errorMsg = $this->getConfigData('specificerrmsg');
            if ($this->customHelper->getConfig('carriers/rwcanadapost/show_original_message', $storeId)) {
                if (is_array($response)) {
                    if (isset($response['error_message'])) {
                        $errorMsg = $response['error_message'];
                    }
                }
                if (isset($response->{'messages'})) {
                    foreach ($response->{'messages'}->{'message'} as $message) {
                        $errorMsg = $message->code.': '.$message->description;
                    }
                }
            }
        }
        
        if ($errorMsg) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorMsg);
            $result->append($error);
        }  
        return $result;
    }
    
    public function processAdditionalValidation(\Magento\Framework\DataObject $request)
    {
        if (!count($this->getAllItems($request))) {
            return $this;
        }
        $storeId = $this->getStoreId($request);
        $maxAllowedWeight = (double)$this->getConfigData('max_package_weight');
        $weightUnit = $this->customHelper->getWeightUnit($storeId);
        $errorMsg = '';
        $configErrorMsg = $this->getConfigData('specificerrmsg');
        $defaultErrorMsg = __('Exceeds weight limit for Canada Post shipment. The weight of each piece must be less than or equal to  %1 KG', $maxAllowedWeight);
        $showMethod = $this->getConfigData('showmethod');

        /** @var $item \Magento\Quote\Model\Quote\Item */
        foreach ($this->getAllItems($request) as $item) {
            $product = $item->getProduct();
            if ($product && $product->getId()) {
                $weight = $this->convertWeightToKg($product->getWeight(), $weightUnit);
                $stockItemData = $this->stockRegistry->getStockItem(
                    $product->getId(),
                    $item->getStore()->getWebsiteId()
                );
                $doValidation = true;

                if ($stockItemData->getIsQtyDecimal() && $stockItemData->getIsDecimalDivided()) {
                    if ($stockItemData->getEnableQtyIncrements() && $stockItemData->getQtyIncrements()
                    ) {
                        $weight = $weight * $stockItemData->getQtyIncrements();
                    } else {
                        $doValidation = false;
                    }
                } elseif ($stockItemData->getIsQtyDecimal() && !$stockItemData->getIsDecimalDivided()) {
                    $weight = $weight * $item->getQty();
                }

                if ($doValidation && $weight > $maxAllowedWeight) {
                    //$errorMsg = $configErrorMsg ? $configErrorMsg : $defaultErrorMsg;
                    $errorMsg = $defaultErrorMsg;
                    break;
                }
            }
        }

        if (!$errorMsg && !$request->getDestPostcode() && $this->isZipCodeRequired($request->getDestCountryId())) {
            $errorMsg = __('This shipping method is not available. Shipment Zip code is required.');
        }

        if ($errorMsg && $showMethod) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $error->setErrorMessage($errorMsg);

            return $error;
        } elseif ($errorMsg) {
            return false;
        }

        return $this;
    }
    
    /**
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }
    
    /**
     * @return boolean
     */
    public function isShippingLabelsAvailable()
    {
        return true;
    }
    
    public function _getDefaultRate($price, $method, $title, $storeId)
    {
        $finalPrice = $this->calculateHandlingFee($price, $storeId);
        $rate = $this->_rateMethodFactory->create();
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($method);
        $rate->setMethodTitle($title);
        $rate->setCost($finalPrice);
        $rate->setPrice($finalPrice);
        return $rate;
    }
    
    public function calculateFeeShippingMethod($method, $destCountryId, $storeId)
    {
        $freeShip = 0;
        if ($destCountryId == 'CA') {
            if ($this->customHelper->getConfig('carriers/rwcanadapost/free_method_ca', $storeId) == $method) {
                $freeShip = 1;
            }
        } else if ($destCountryId == 'US') {
            if ($this->customHelper->getConfig('carriers/rwcanadapost/free_method_us', $storeId) == $method) {
                $freeShip = 1;
            }
        } else {
            if ($this->customHelper->getConfig('carriers/rwcanadapost/free_method_international', $storeId) == $method) {
                $freeShip = 1;
            }
        }
        return $freeShip;
    }
    
    public function calculateHandlingFee($price, $storeId)
    {
        $handlingType = $this->customHelper->getConfig('carriers/rwcanadapost/handling_type', $storeId);
        $handlingFee = $this->customHelper->getConfig('carriers/rwcanadapost/handling_fee', $storeId);
        $finalPrice = $price;
        if ($handlingFee != '' && $handlingType == \Magento\Shipping\Model\Carrier\AbstractCarrier::HANDLING_TYPE_FIXED) {
            $finalPrice = $price + $handlingFee;
            //$finalPrice = ($price + $handlingFee) * $this->_numBoxes; // Userful when want to apply handling fee per package instaled of per order.
        }
        
        if ($handlingFee != '' && $handlingType == \Magento\Shipping\Model\Carrier\AbstractCarrier::HANDLING_TYPE_PERCENT) {
            $finalPrice = $price + ($price * $handlingFee / 100);
            //$finalPrice = $price + ($price * $handlingFee / 100) * $this->_numBoxes; // Userful when want to apply handling fee per package instaled of per order.
        }
        return $finalPrice;
    }
    
    public function getCode($type, $code = '')
    {
        $codes = [
            'method' => [
                // Canada
                'DOM.RP'        => __('Regular Parcel'),
                'DOM.EP'        => __('Expedited Parcel'),
                'DOM.LIB'       => __('Library Materials/Books'),
                'DOM.PC'        => __('Priority'),
                'DOM.XP'        => __('Xpresspost'),
                'DOM.XP.CERT'   => __('Xpresspost Certified'),
                //USA
                'USA.EP'        => __('Expedited Parcel USA'),
                'USA.PW.ENV'    => __('Priority Worldwide Envelope USA'),
                'USA.PW.PAK'    => __('Priority Worldwide pak USA'),
                'USA.PW.PARCEL' => __('Priority Worldwide Parcel USA'),
                'USA.SP.AIR'    => __('Small Packet USA Air'),
                'USA.TP'        => __('Tracked Packet – USA'),
                'USA.TP.LVM'    => __('Tracked Packet – USA (LVM)'),
                'USA.XP'        => __('Xpresspost USA'),
                //international
                'INT.IP.AIR'    => __('International Parcel Air'),
                'INT.IP.SURF'   => __('International Parcel Surface'),
                'INT.XP'        => __('Xpresspost International'),
                'INT.PW.ENV'    => __('Priority Worldwide Envelope Int’l'),
                'INT.PW.PAK'    => __('Priority Worldwide pak Int’l'),
                'INT.PW.PARCEL' => __('Priority Worldwide parcel Int’l'),
                'INT.SP.AIR'    => __('Small Packet International Air'),
                'INT.SP.SURF'   => __('Small Packet International Surface'),
                'INT.TP'        => __('Tracked Packet – International')
            ]
        ];

        if (!isset($codes[$type])) {
            return false;
        } elseif ('' === $code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            return false;
        } else {
            return $codes[$type][$code];
        }
    }
    
    public function requestToShipment($request)
    {
        $packages = $request->getPackages();
        if (!is_array($packages) || !$packages) {
            throw new \Magento\Framework\Exception\LocalizedException(__('No packages for request'));
        }
        
        foreach ($packages as $packageId => $package) {
            $request->setPackageId($packageId);
            $request->setPackagingType($package['params']['container']);
            $request->setPackageWeight($package['params']['weight']);
            $request->setPackageParams(new \Magento\Framework\DataObject($package['params']));
            $request->setPackageItems($package['items']);
            $packageRequests[] = clone $request;
        }
        
        $result = $this->_doShipmentRequest($request);
        
        $response = new \Magento\Framework\DataObject(
            [
                'info' => [
                    [
                        'tracking_number' => $result->getTrackingNumber(),
                        'label_content' => $result->getShippingLabelContent(),
                    ],
                ],
            ]
        );
        $request->setMasterTrackingId($result->getTrackingNumber());
        
        return $response;
    }
    
    protected function _doShipmentRequest(\Magento\Framework\DataObject $request)
    {
        $result = new \Magento\Framework\DataObject();
        
        if ($viewManifest = $this->_registry->registry('rw_canadapost_view_manifest')) {
            if ($viewManifest->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('A shipment was already created for this item.')
                );
            }
        }

        $response = $this->_createShipmentRequest($request);
        
        return $response;
    }
    
    protected function _createShipmentRequest($request)
    {
        $storeId = $this->getStoreId($request);
        $senderAddress2 = $request->getShipperAddressStreet2() ? substr($request->getShipperAddressStreet2(), 0, 44) : '';
        $destinationCompany = $request->getRecipientContactCompanyName() ? substr($request->getRecipientContactCompanyName(), 0, 44) : '';
        $destinationAddress2 = $request->getRecipientAddressStreet2() ? substr($request->getRecipientAddressStreet2(), 0, 44) : '';
        
        $packageParams = $request->getPackageParams();
        $weight = $this->convertWeightToKg($request->getPackageWeight(), $packageParams->getWeightUnits());
        $height = $this->convertDimensionToCm($packageParams->getHeight(), $packageParams->getDimensionUnits());
        $width = $this->convertDimensionToCm($packageParams->getWidth(), $packageParams->getDimensionUnits());
        $length = $this->convertDimensionToCm($packageParams->getLength(), $packageParams->getDimensionUnits());
        
        $delivery_spec = [
            'service-code' => $request->getShippingMethod(),
            'sender' => [
                'company' => substr($request->getShipperContactCompanyName(), 0, 44),
                'contact-phone' => $request->getShipperContactPhoneNumber(),
                'address-details'   => [
                    'address-line-1' => substr($request->getShipperAddressStreet1(), 0, 44),
                    'address-line-2' => $senderAddress2,
                    'city' => substr($request->getShipperAddressCity(), 0, 40),
                    'prov-state' => $request->getShipperAddressStateOrProvinceCode(),
                    'postal-zip-code' => $this->formatPostalCode($request->getShipperAddressPostalCode())
                ]
            ],
            'destination' => [
                'name' => substr($request->getRecipientContactPersonName(), 0, 44),
                'company' => $destinationCompany,
                'client-voice-number' => $request->getRecipientContactPhoneNumber(),
                'address-details' => [
                    'address-line-1' => substr($request->getRecipientAddressStreet1(), 0, 44),
                    'address-line-2' => $destinationAddress2,
                    'city' => substr($request->getRecipientAddressCity(), 0, 40),
                    'prov-state' => $request->getRecipientAddressStateOrProvinceCode(),
                    'country-code' => $request->getRecipientAddressCountryCode(),
                    'postal-zip-code' => $this->formatPostalCode($request->getRecipientAddressPostalCode())
                ]
            ],
            'options' => [
                'option' => [
                    'option-code' => 'DC'
                ]
            ],
            'parcel-characteristics' => [
                'weight' => number_format($weight, 2, '.', ''),
                'unpackaged' => false,
                'mailing-tube'=> false
            ],
            'notification'  => [
                'email'         => $request->getRecipientEmail(),
                'on-shipment'   => true,
                'on-exception'  => false,
                'on-delivery'   => true
            ],
            'preferences' => [
                'show-packing-instructions' => true
            ]
        ];
        
        
        if ($length || $width || $height) {
            $delivery_spec['parcel-characteristics']['dimensions'] = [
                'length'    => $length,
                'width'     => $width,
                'height'    => $height
            ];
        }
        
        if ($request->getShipperAddressCountryCode() != $request->getRecipientAddressCountryCode()) {
            $delivery_spec['options']['option'] = [
                'option-code' => $this->customHelper->getConfig('carriers/rwcanadapost/non_delivery_handling_opt', $storeId)
            ];
            $reasonForExport = $this->customHelper->getConfig('carriers/rwcanadapost/reason_for_export', $storeId);
            $destinationCurrency = $this->customHelper->getCurrencyByCountries($request->getRecipientAddressCountryCode());
            $currencyRate = $this->customHelper->getCurrencyRate(
                $request->getBaseCurrencyCode(),
                $destinationCurrency
            );
            if (!$currencyRate) {
                $destinationCurrency = $request->getBaseCurrencyCode();
                $currencyRate = 1;
            }
            $delivery_spec['customs'] = [
                'currency' => $destinationCurrency,
                'conversion-from-cad' => $currencyRate,
                'reason-for-export' => $reasonForExport,
                'sku-list' => [
                    'item' => $this->getSkuList($request, $storeId)
                ]
            ];
            if ($reasonForExport == 'OTH') {
                $othReason = $this->customHelper->getConfig('carriers/rwcanadapost/reason_for_export', $storeId);
                $delivery_spec['customs']['other-reason'] = substr(
                    $this->customHelper->getConfig('carriers/rwcanadapost/other_reason_for_export', $storeId),
                    0,
                    44
                );
            }
        }

        $responseVaribleName = 'non-contract-shipment-info';
        if ($this->customHelper->getQuoteType($storeId) == "1" &&
            $this->customHelper->getContractId($storeId) != '') {
            $responseVaribleName = 'shipment-info';
            $wsdlFileName = '/libcanadapost/wsdl/shipment.wsdl';
            $actionType = 'shipment';
            $soapCallFuncationName = 'CreateShipment';
            $delivery_spec['sender']['address-details']['country-code'] = 'CA';
            $delivery_spec['settlement-info'] = [
                'contract-id' => $this->customHelper->getContractId($storeId),
                'intended-method-of-payment' => $this->customHelper->getMethodOfPayment($storeId)
            ];
            $params = [
                'create-shipment-request' => [
                    'mailed-by' => $this->customHelper->getCustomerNumber($storeId),
                    'shipment' => [
                        //'v8:group-id' =>  time(),
                        'requested-shipping-point' => $this->formatPostalCode($request->getShipperAddressPostalCode()),
                        //'cpc-pickup-indicator' => true //Set this element to true if your shipments are picked up by Canada Post or a third party.
                        //Provide the Postal Code of your pickup location in requested-shipping-point
                        'expected-mailing-date' => date('Y-m-d'),
                        'delivery-spec' => $delivery_spec
                    ]
                ]
            ];

            $manifestId = '';
            if ($manifest = $this->_registry->registry('rw_canadapost_manifest_group_id')) {
                $groupId = $manifest->getGroupId();
                $manifestRowId = '';
                if ($manifest->getId()) {
                    $manifestRowId = $manifest->getId();
                }
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Error while generating the manifest group id.')
                );
            }
            $params['create-shipment-request']['shipment']['groupIdOrTransmitShipment']['ns1:group-id'] = $groupId;

            // Pass this parameter if do not want to create Manifest (Transmit Shipments) for this label
            //$params['create-shipment-request']['shipment']['v8:transmit-shipment'] = true; 
            

            if ($this->customHelper->isReturnLabelEnabled($storeId)) {
                $returnZipCode = $this->formatPostalCode($this->customHelper->getConfig('carriers/rwcanadapost/return_postal_zip_code', $storeId));
                $params['create-shipment-request']['shipment']['return-spec'] = [
                    'service-code' => $this->customHelper->getConfig('carriers/rwcanadapost/return_shipping_method', $storeId),
                    'return-recipient' => [
                        'name' => $this->customHelper->getConfig('carriers/rwcanadapost/return_name', $storeId),
                        'company' => $this->customHelper->getConfig('carriers/rwcanadapost/return_company', $storeId),
                        'address-details' => [
                            'address-line-1' => $this->customHelper->getConfig('carriers/rwcanadapost/return_address1', $storeId),
                            'address-line-2' => $this->customHelper->getConfig('carriers/rwcanadapost/return_address2', $storeId),
                            'city' => $this->customHelper->getConfig('carriers/rwcanadapost/return_city', $storeId),
                            'prov-state' => $this->customHelper->getConfig('carriers/rwcanadapost/return_prov_state', $storeId),
                            'postal-zip-code' => $returnZipCode
                        ]
                    ],
                    'return-notification' => $this->customHelper->getConfig('carriers/rwcanadapost/return_notification', $storeId)
                ];
            }
        } else {
            $wsdlFileName = '/libcanadapost/wsdl/ncshipment.wsdl';
            $actionType = 'ncshipment';
            $soapCallFuncationName = 'CreateNCShipment';
            $params = [
                'create-non-contract-shipment-request' => [
                    'mailed-by' => $this->customHelper->getCustomerNumber($storeId),
                    'locale' => $this->customHelper->getLocale($storeId),
                    'non-contract-shipment' => [
                        'requested-shipping-point' => $this->formatPostalCode($request->getShipperAddressPostalCode()),
                        'delivery-spec' => $delivery_spec
                    ]
                ]
            ];
        }

        $client = $this->_createSoapClient($wsdlFileName, $actionType, $storeId);
        $response = $client->__soapCall($soapCallFuncationName, $params, null, null);
        $result = new \Magento\Framework\DataObject();
        
        if (isset($response->{$responseVaribleName})) {
            //$shippingLabelContent = $this->getCanadaPostLabelForNonContract($responseVaribleName, $request, $response, $storeId);
            $lableArray = [];
            foreach ($response->{$responseVaribleName}->{'artifacts'}->{'artifact'} as $artifact) {
                $lableArray[] = $this->getCanadaPostLabelForNonContract($artifact, $request, $response, $storeId);
            }
            $shippingLabelContent = $this->combineLabelsPdf($lableArray);

            if (isset($response->{$responseVaribleName}->{'tracking-pin'})) {
                $trackingNumber = $response->{$responseVaribleName}->{'tracking-pin'};
            } else {
                $trackingNumber = __('N/A');
            }
            $result->setShippingLabelContent($shippingLabelContent);
            $result->setTrackingNumber($trackingNumber);
            if ($responseVaribleName == 'shipment-info' && $manifestRowId == '') {
                $this->addManifest($manifest, $request, $storeId);
            }
            $this->addToViewShipmentTable($response->{$responseVaribleName});
        } else {
            $errorMsg = __('');
            if (is_array($response) && isset($response['error_message'])) {
                $errorMsg = $response['error_message'];
            }
            if (is_object($response) && $response->{'messages'}) {
                foreach ($response->{'messages'}->{'message'} as $message) {
                    if (is_array($message)){
                        $message = $message[0];
                    } else {
                        $message = $message;
                    }
                    if (isset($message->code) && isset($message->description) ) {
                        $errorMsg = 'Error Code = '.$message->code.': Error Message = '.$message->description;
                    }
                }
            }
            throw new \Magento\Framework\Exception\LocalizedException(
                __($errorMsg)
            );
        }
        
        return $result;
    }
    
    protected function getCanadaPostLabelForNonContract($responseVaribleName, $rawRequest, $response, $storeId)
    {
        $labelId ='';
        $pageIndex = '';
        if (isset($responseVaribleName->{'artifact-id'})) {
            $labelId =  $responseVaribleName->{'artifact-id'};
        }
        if (isset($responseVaribleName->{'page-index'})) {
            $pageIndex =  $responseVaribleName->{'page-index'};
        }
        
        $wsdlFileName = '/libcanadapost/wsdl/artifact.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'artifact', $storeId);
        try {
            $result = $client->__soapCall(
                'GetArtifact',
                [
                    'get-artifact-request' => [
                        'locale'            => $this->customHelper->getLocale($storeId),
                        'mailed-by'         => $this->customHelper->getCustomerNumber($storeId),
                        'artifact-id'       => $labelId,
                        'page-index'        => $pageIndex
                    ]
                ],
                null,
                null
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
        if (isset($result->{'artifact-data'})) {
            return base64_decode($result->{'artifact-data'}->{'image'});
        } else {
            if (isset($response['error_message'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($response['error_message'])
                );
            }
        }
    }

    protected function getCanadaPostManifestLabelForContract($labelId = '', $storeId = null)
    {
        $pageIndex = '';
        $wsdlFileName = '/libcanadapost/wsdl/artifact.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'artifact');
        try {
            $result = $client->__soapCall(
                'GetArtifact',
                [
                    'get-artifact-request' => [
                        'locale'            => $this->customHelper->getLocale($storeId),
                        'mailed-by'         => $this->customHelper->getCustomerNumber($storeId),
                        'artifact-id'       => $labelId,
                        'page-index'        => $pageIndex
                    ]
                ],
                null,
                null
            );
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
        if (isset($result->{'artifact-data'})) {
            return base64_decode($result->{'artifact-data'}->{'image'});
        } else {
            if (isset($response['error_message'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($response['error_message'])
                );
            }
        }
    }
    
    protected function getSkuList($request, $storeId)
    {
        $weightUnit = $this->customHelper->getWeightUnit($storeId);
        $skus = array();
        foreach ($request->getPackageItems() as $itemShipment) {
            $_product = $this->_productRepository->getById($itemShipment['product_id']);
            $unitWeight = $this->convertWeightToKg(
                $itemShipment['weight'],
                $weightUnit
            );

            $data = [
                'sku' => substr($_product->getSku(), 0, 15),
                'customs-description' => substr($itemShipment['name'], 0, 45),
                'unit-weight' => number_format($unitWeight, 2, '.', ''),
                'customs-value-per-unit' => round($itemShipment['customs_value'], 2),
                'customs-number-of-units' => $itemShipment['qty'],
            ];

            if ($_product->getCountryOfManufacture()) {
                $data['country-of-origin'] = $_product->getCountryOfManufacture();
            }

            $skus[] = $data;
        }
        
        return $skus;
    }
    
    protected function _createSoapClient($wsdlFileName, $type, $storeId = null)
    {
        ini_set('soap.wsdl_cache_enabled', 0);
        $appDir = $this->customHelper->getDirectory();        
        $hostName = $this->customHelper->getHostName($storeId);
        $wsdl =  $appDir . $wsdlFileName;
        $cert = $appDir.'/libcanadapost/cert/cacert.pem';
        
        if ($type == 'rating') {
            $location = 'https://' . $hostName . '/rs/soap/rating/v4';
        }

        if ($type == 'shipment' || $type == 'shipment_price') {
            $location = 'https://' . $hostName . '/rs/soap/shipment/v8';
        }

        if ($type == 'manifest') {
            $location = 'https://' . $hostName . '/rs/soap/manifest/v8';
        }

        if ($type == 'track') {
            $location = 'https://' . $hostName . '/vis/soap/track';
        }

        if ($type == 'ncshipment') {
            $location = 'https://' . $hostName . '/rs/soap/ncshipment/v4';
        }

        if ($type == 'artifact') {
            $location = 'https://' . $hostName . '/rs/soap/artifact';
        }
        
        if ($type == 'pickup') {
            $location = 'https://' . $hostName . '/ad/soap/pickup/availability';
        }
        
        if ($type == 'postoffice') {
            $location = 'https://' . $hostName . '/rs/soap/postoffice';
        }

        $options = [
            'location' => $location,
            'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            'trace'=>0
        ];

        $opts = ['ssl' =>
            [
                'verify_peer'=> false,
                'cafile' => $cert,
                'CN_match' => $hostName
            ],
            'http' => [
                'protocol_version' => 1.0,
            ]
        ];

        $ctx = stream_context_create($opts);
        $options['stream_context'] = $ctx;

        $client = new \Rootways\Canadapost\Model\Service\SoapClient($wsdl, $type, $options, $this->rwLogger);
        // Set WS Security UsernameToken
        $WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $usernameToken = new \stdClass();
        $usernameToken->Username = new \SoapVar($this->customHelper->getApiKey($storeId), XSD_STRING, null, null, null, $WSSENS);
        $usernameToken->Password = new \SoapVar($this->customHelper->getPassword($storeId), XSD_STRING, null, null, null, $WSSENS);
        $content = new \stdClass();
        $content->UsernameToken = new \SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $WSSENS);
        $header = new \SOAPHeader($WSSENS, 'Security', $content);
        $client->__setSoapHeaders($header);
        return $client;
    }
    
    
    public function getTracking($trackNumber, $storeId = null)
    {
        $trackingResuls = $this->_trackFactory->create();
        
        $params = [
            'get-tracking-detail-request' => [
                'locale' => $this->customHelper->getLocale($storeId),
                'pin' => $trackNumber
            ]
        ];
        $wsdlFileName = '/libcanadapost/wsdl/track.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'track', $storeId = null);
        $response = $client->__soapCall('GetTrackingDetail', $params, null, null);
        
        /*
        // Below are parameters for get tracking summary
        $params = [
            'get-tracking-summary-request' => [
                'locale' => $this->customHelper->getLocale($storeId),
                'pin' => $trackNumber
            ]
        ];
        $wsdlFileName = '/libcanadapost/wsdl/track.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'track');
        $response = $client->__soapCall('GetTrackingSummary', $params, null, null);
        */
        /*
        // Below are parameters for get nearest post offices.
        $params = [
            'get-nearest-post-office-request' => [
                'locale' => $this->customHelper->getLocale($storeId),
                'maximum' =>  '5',
                'search-data' => array (
                    // 'city'			=> 'Ottawa',
                    'postal-code'	=> 'K2B8J6'
                    // 'province'		=> 'ON',
                    // 'street-name'	=> 'Richmond'	
                )
            ]
        ];
        
        $wsdlFileName = '/libcanadapost/wsdl/postoffice.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'postoffice');
        $response = $client->__soapCall('GetNearestPostOffice', $params, null, null);
        */
        
        /*
        // Below are parameters for get post office detail.
        $params = [
            'get-post-office-detail-request' => [
                'locale' => $this->customHelper->getLocale($storeId),
                'office-id' =>  '0000105040'
            ]
        ];
        
        $wsdlFileName = '/libcanadapost/wsdl/postoffice.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'postoffice');
        $response = $client->__soapCall('GetPostOfficeDetail', $params, null, null);
        */

        if (isset($response->{'tracking-detail'})) {
            $tracking = $this->_trackStatusFactory->create();
            $tracking->setCarrier($this->_code);
            $tracking->setCarrierTitle($this->customHelper->getConfig('carriers/rwcanadapost/title', $storeId));
            $tracking->setTracking($trackNumber);
            
            //$tracking->setTrackSummary($trackingResulsFromService->summary);
            //$tracking->addData($this->processTrackingDetails($trackInfo));
            
            $trackingResuls->append($tracking);
        } else {
            $error = $this->_trackErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->customHelper->getConfig('carriers/rwcanadapost/title', $storeId));
            $error->setTracking($trackNumber);
            $error->setErrorMessage('Error while retrieving tracking info.');
            $trackingResuls->append($error);
        }

        return $trackingResuls;
        
    }
    
    public function addManifest($manifestModel, $request, $storeId)
    {
        $manifestModel->addData([
            "group_id" => $manifestModel->getGroupId(),
			"store_id" => $storeId,
            "order_id" => $request->getOrderShipment()->getOrderId(),
			"status" => 'pending'
        ]);
        try {
            $manifestModel->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        }
    }

    public function addToViewShipmentTable($canadapostResponseData)
    {
        if ($viewManifest = $this->_registry->registry('rw_canadapost_view_manifest')) {
            $shipmentId = null;
            if (isset($canadapostResponseData->{'shipment-id'})) {
                $shipmentId = $canadapostResponseData->{'shipment-id'};
            }
            $trackingNumber = __('N/A');
            if (isset($canadapostResponseData->{'tracking-pin'})) {
                $trackingNumber = $canadapostResponseData->{'tracking-pin'};
            }
            $viewManifest->setCanadapostShipmentAmount($this->getShipmentPrice($shipmentId)['cost']);
            $viewManifest->setRatedWeight($this->getShipmentPrice($shipmentId)['weight']);
            $viewManifest->setCanadapostShipmentId($shipmentId);
            $viewManifest->setCanadapostTrackingNumber($trackingNumber);
        }
    }

    public function getShipmentPrice($shipmentId, $storeId = null)
    {
        $params = [
            'get-shipment-price-request' => [
                'locale' => $this->customHelper->getLocale($storeId),
                'mailed-by' => $this->customHelper->getCustomerNumber($storeId),
                'shipment-id'   => $shipmentId
            ]
        ];
        $wsdlFileName = '/libcanadapost/wsdl/shipment.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'shipment_price', $storeId = null);
        $response = $client->__soapCall('GetShipmentPrice', $params, null, null);
        $cost = 0;
        $ratedWeight = '';
        if (isset($response->{'shipment-price'})) {
            if ($response->{'shipment-price'}->{'rated-weight'}) {
                $ratedWeight = $response->{'shipment-price'}->{'rated-weight'};
            }
            if ($this->customHelper->getConfig('carriers/rwcanadapost/rates_price_type', $storeId)) {
                $price = $response->{'shipment-price'}->{'due-amount'}; // Including Tax
            } else {
                $price = $response->{'shipment-price'}->{'base-amount'}; // Excluding Tax
                if (isset($response->{'shipment-price'}->{'adjustments'})) {
                    foreach ($response->{'shipment-price'}->{'adjustments'}->{'adjustment'} as $adjustment) {
                        $price += $adjustment->{'adjustment-amount'};
                    }
                }
            }
            $cost = $price;
        }

        return array('cost' => $cost, 'weight' => $ratedWeight);
    }

    public function getManifest($manifest)
    {
        $storeId = $manifest->getStoreId();

        $companyName = $this->_scopeConfig->getValue(
            "general/store_information/name",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $phoneNumber = $this->_scopeConfig->getValue(
            "general/store_information/phone",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $potalZipcode = $this->_scopeConfig->getValue(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $address1 = $this->_scopeConfig->getValue(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS1,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $address2 = $this->_scopeConfig->getValue(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS2,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $city = $this->_scopeConfig->getValue(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $state = $this->_scopeConfig->getValue(
            \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $params = [
            'transmit-shipments-request' => [
                'locale' => $this->customHelper->getLocale($storeId),
                'mailed-by' => $this->customHelper->getCustomerNumber($storeId),
                'transmit-set' => [
                    'group-ids' => [
                        'group-id' => $manifest->getGroupId()
                    ],
                    'requested-shipping-point' => $this->formatPostalCode($potalZipcode),
                    //'cpc-pickup-indicator'	=> 'true',
                    'detailed-manifests' => true,
                    'method-of-payment' => $this->customHelper->getMethodOfPayment($storeId),
                    'manifest-address' => [
                        'manifest-company' => $companyName,
                        'phone-number' => $phoneNumber,
                        'address-details' => [
                            'address-line-1' => $address1,
                            'address-line-2' => $address2 ? $address2 : '',
                            'city' => $city,
                            'prov-state' => $state,
                            'postal-zip-code' => $this->formatPostalCode($potalZipcode),
                        ],
                    ],
                ]
            ]
        ];

        $wsdlFileName = '/libcanadapost/wsdl/manifest.wsdl';
        $client = $this->_createSoapClient($wsdlFileName, 'manifest', $storeId);
        $response = $client->__soapCall('TransmitShipments', $params, null, null);
        $errorMsg = [];
        if (isset($response->{'manifests'})) {
            $manifestIds = [];
            foreach ($response->{'manifests'}->{'manifest-id'} as $manifestId) {
                $manifestIds[] = $manifestId;
            }
            
            $manifest->addData([
                'manifest_id' => implode(',', $manifestIds),
                'status' => 'completed',
                'updated_at' => date('Y-m-d H:i:s')
            ])->save();

            $status = 1;
        } else {
            $status = 0;
            if (isset($response->{'messages'})) {
                foreach ($response->{'messages'}->{'message'} as $message) {
                    $errorMsg[] = array('id' => $manifest->getId(), 'error_message' => $message->code.': '.$message->description);
                }
            }
        }
        $output = array('status' => $status, 'message' => $errorMsg);
        
        return $output;
    }

    public function getArtifact($manifest)
    {
        $storeId =  $manifest->getStoreId();
        $status = 0;
        $errorMsg = [];
        $labelsContent = [];
        if ($manifest->getManifestId()) {
            $artifactCollection = [];
            foreach (explode(',', $manifest->getManifestId()) as $manifestId) {
                $params = [
                    'get-manifest-artifact-id-request' => [
                        'locale' => $this->customHelper->getLocale($storeId),
                        'mailed-by' => $this->customHelper->getCustomerNumber($storeId),
                        'manifest-id' => $manifestId
                    ]
                ];

                $wsdlFileName = '/libcanadapost/wsdl/manifest.wsdl';
                $client = $this->_createSoapClient($wsdlFileName, 'manifest', $storeId);
                $response = $client->__soapCall('GetManifestArtifactId', $params, null, null);

                if (isset($response->{'manifest'})) {
                    $artifactCollection[] = [
                        'artifact_id' => $response->{'manifest'}->{'artifact-id'}
                    ];
                } else {
                    foreach ($response->{'messages'}->{'message'} as $message) {
                        $errorMsg[] = array('id' => $manifest->getId(), 'error_message' => $message->code.': '.$message->description);
                    }
                }
            }

            if (!empty($artifactCollection)) {
                $status = 1;
                foreach ($artifactCollection as $artifactId) {
                    $labelsContent[] = $this->getCanadaPostManifestLabelForContract($artifactId['artifact_id']);
                }
            }
        }
        $labelData = $this->combineLabelsPdf($labelsContent);
        
        $output = array('status' => $status, 'message' => $errorMsg, 'data' => $labelData);

        return $output;
    }

    /**
     * Combine array of labels as instance PDF
     *
     * @param array $labelsContent
     * @return \Zend_Pdf
     */
    public function combineLabelsPdf(array $labelsContent)
    {
        $outputPdf = new \Zend_Pdf();
        foreach ($labelsContent as $content) {
            if (stripos($content, '%PDF-') !== false) {
                $pdfLabel = \Zend_Pdf::parse($content);
                foreach ($pdfLabel->pages as $page) {
                    $outputPdf->pages[] = clone $page;
                }
            } else {
                /*$page = $this->createPdfPageFromImageString($content);
                if ($page) {
                    $outputPdf->pages[] = $page;
                }*/
            }
        }

        return $outputPdf->render();
    }
    
    public function formatPostalCode($postalCode)
    {
        return strtoupper(preg_replace('/\s+/', '', $postalCode));
    }
    
    public function convertWeightToKg($weight, $weightUnit, $roundPrecision = 3)
    {
        return round(
            $this->customHelper->convertMeasureWeight(
                $weight,
                $weightUnit,
                \Zend_Measure_Weight::KILOGRAM
            ),
            $roundPrecision
        );
    }
    
    public function convertDimensionToCm($dimension, $dimensionUnit, $roundPrecision = 1)
    {
        return round(
            $this->customHelper->convertMeasureDimension(
                $dimension,
                $dimensionUnit,
                \Zend_Measure_Length::CENTIMETER
            ),
            $roundPrecision
        );
    }

    public function getStoreId($request)
    {
        return $request->getStoreId() ? $request->getStoreId() : null;
    }
}
