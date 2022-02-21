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

namespace Rootways\Canadapost\Model\Service;

use Magento\Framework\Module\Dir;

class SoapClient extends \SoapClient
{
    protected $code = '';

    protected $_requestType;
    
    public function __construct($wsdl, $requestType, $options = null)
    {
        $this->_requestType = $requestType;
        parent::__construct($wsdl, $options);
    }

    public function __doRequest($request, $location, $action, $version, $one_way = null)
    {
        if ($this->_requestType == 'shipment') {
            $dom = new \DOMDocument('1.0');

            $dom->loadXML($request);

            //get element name and values of group-id or transmit-shipment.
            $groupIdOrTransmitShipment =  $dom->getElementsByTagName("groupIdOrTransmitShipment")->item(0);
            $element = $groupIdOrTransmitShipment->firstChild->firstChild->nodeValue;
            $value = $groupIdOrTransmitShipment->firstChild->firstChild->nextSibling->firstChild->nodeValue;

            //remove bad element
            $newDom = $groupIdOrTransmitShipment->parentNode->removeChild($groupIdOrTransmitShipment);

            //append correct element with namespace
            $body =  $dom->getElementsByTagName("shipment")->item(0);
            $newElement = $dom->createElement($element, $value);
            $body->appendChild($newElement);

            //save $dom to string
            $request = $dom->saveXML();
        }
        //doRequest
        return parent::__doRequest($request, $location, $action, $version);
    }
    
    public function __soapCall(
        $function_name,
        $arguments,
        $options = null,
        $input_headers = null,
        &$output_headers = null
    ) {
        try {
           $result = parent::__soapCall(
                $function_name,
                $arguments,
                $options,
                $input_headers,
                $output_headers
            );
            $res = json_decode(json_encode($result), true);
            $req = json_decode(json_encode($arguments), true);
            $this->rwLogger($req, $res);
            return $result;
        } catch (\SoapFault $fault) {
            $result = array('error_message' =>  "Error while retrieve the info from Canada post: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})");
            return $result;
        }
    }
    
    public function rwLogger($req, $res)
    {
        if (isset($req['get-rates-request']['mailing-scenario']['customer-number'])) {
            unset($req['get-rates-request']['mailing-scenario']['customer-number']);
        }
        if (isset($req['get-rates-request']['mailing-scenario']['contract-id'])) {
            unset($req['get-rates-request']['mailing-scenario']['contract-id']);
        }
        $logger = new \Zend\Log\Logger();
        $rwLog = new \Zend\Log\Writer\Stream(BP.'/var/log/rw_canadapost.log');
        $logger->addWriter($rwLog);
        $logger->info("#######Request#######");
        $logger->info($req);
        $logger->info("#######Response#######");
        $logger->info($res);
    }
}
