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
 * Class Province
 */
class Province extends \Magento\Directory\Model\ResourceModel\Region\Collection
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $this->addCountryFilter('CA');
        $options =  ['value' => '', 'label' => __('--Please select--')];
        foreach ($this as $item) {
            $options[] = [
                'value' => $item->getCode(),
                'label' => $item->getName(),
            ];
        }
        
        return $options;
    }
}
