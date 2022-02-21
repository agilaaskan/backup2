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
 * Class ReasonForExport.
 */
class ReasonForExport implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'DOC',
                'label' => __('Document'),
            ],
            [
                'value' => 'SAM',
                'label' => __('Commercial Sample'),
            ],
            [
                'value' => 'REP',
                'label' => __('Repair or Warranty'),
            ],
            [
                'value' => 'SOG',
                'label' => __('Sale of Goods'),
            ],
            [
                'value' => 'OTH',
                'label' => __('Other'),
            ]
        ];
    }
}

