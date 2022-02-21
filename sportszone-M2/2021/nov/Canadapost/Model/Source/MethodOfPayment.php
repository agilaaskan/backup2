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
 * Class MethodOfPayment.
 */
class MethodOfPayment implements \Magento\Framework\Option\ArrayInterface
{
   public function toOptionArray() 
   {
       return [
           [
               "value" => "Account",
               "label" => "Account"
           ],
           [
               "value" => "CreditCard ",
               "label" => "Credit Card"
           ]           
        ];
   }
}
