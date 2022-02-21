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

namespace Rootways\Canadapost\Model\Source;

/**
 * Class ShippingMethod.
 */
class ShippingMethod implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Rootways\Canadapost\Model\Carrier
     */
    protected $_shippingMethods;
    
    /**
     * @var string
     */
    protected $_code = 'method';
    
    /**
     * @param \Rootways\Canadapost\Model\Carrier $shippingMethods
     */
    public function __construct(
        \Rootways\Canadapost\Model\Carrier $shippingMethods
    )
    {
        $this->_shippingMethods = $shippingMethods;
    }
    
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->_shippingMethods->getCode($this->_code);
        $arr = [];
        foreach ($configData as $code => $title) {
            $arr[] = ['value' => $code, 'label' => $title];
        }
        return $arr;
    }
}
