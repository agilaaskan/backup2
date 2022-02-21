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

class ManifestActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $_urlBuilder;
	
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
	
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['id'])) {
                    if (isset($item['m_order_id'])) {
                        $item[$name]['view_order'] = [
                            'href' =>  $this->_urlBuilder->getUrl("sales/order/view", ['order_id' => $item['m_order_id']]),
                            'target' => '_blank',
                            'label' => __('View')
                        ];
                    } else {
                        $item[$name]['view'] = [
                            'href' =>  $this->_urlBuilder->getUrl("rwcanadapost/manifest/view", ['id' => $item['id']]),
                            'label' => __('View')
                        ];
                        $item[$name]['shipments'] = [
                            'href' => $this->_urlBuilder->getUrl("rwcanadapost/manifest/shipments", ['id' => $item['id']]),
                            'label' => __('Transmit Shipments'),
                            'confirm' => [
                                'title' => __('Transmit Shipment for ID ' . $item['id']),
                                'message' => __('Are you sure you want to create transmit shipment?')
                            ]
                        ];
                        $item[$name]['label'] = [
                            'href' =>  $this->_urlBuilder->getUrl("rwcanadapost/manifest/shippinglabel", ['id' => $item['id']]),
                            'label' => __('Print Manifest')
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
