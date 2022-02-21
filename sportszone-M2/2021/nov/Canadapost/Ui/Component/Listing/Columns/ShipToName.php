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

namespace Rootways\Canadapost\Ui\Component\Listing\Columns;

class ShipToName extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** @var  /Magento\Sales\Api\Data\OrderInterface */
    protected $orderInterface;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        array $components = [],
        array $data = []
    ) {
        $this->orderInterface = $orderInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
	
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                /** @var \Magento\Sales\Api\Data\OrderInterface $order */
                $order = $this->orderInterface->load($item['increment_id']);
                if($shippingAddress = $order->getShippingAddress()) {
                    $item[$fieldName] = $shippingAddress->getFirstname() . ' '.  $shippingAddress->getLastname();
                }
            }
        }

        return $dataSource;
    }
}
