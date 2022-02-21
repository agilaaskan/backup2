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
class NonDeliveryHandlingOptions implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'RASE',
                'label' => __('Return at Sender’s Expense'),
            ],
            [
                'value' => 'RTS',
                'label' => __('Return to Sender'),
            ],
            [
                'value' => 'ABAN',
                'label' => __('Abandon'),
            ]
        ];
    }
}

