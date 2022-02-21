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

namespace  Rootways\Canadapost\Model\Source;

/**
 * Class QuoteType.
 */
class QuoteType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                "value" => 0,
                "label" => __('Counter')
            ],
            [
                "value" => 1,
                "label" => __('Commercial')
            ]
        ];
    }
}
