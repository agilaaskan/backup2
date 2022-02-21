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

namespace Rootways\Canadapost\Plugin\Shipping\Model\Shipping;

use Rootways\Canadapost\Helper\Data as DataHelper;
use Rootways\Canadapost\Model\ManifestFactory;
use Rootways\Canadapost\Model\ViewManifestFactory;
use \Magento\Framework\Registry as RegistryFramework;

class LabelGenerator
{
    /**
     * @var DataHelper
     */
    private $customHelper;

    /**
     * @var ManifestFactory
     */
    private $_manifestFactory;

    /**
     * @var ViewManifestFactory
     */
    private $_viewManifestFactory;

    /**
     * @var RegistryFramework
     */
    private $_registry;

    /**
     * Shipment constructor.
     *
     * @param DataHelper $customHelper
     * @param ManifestFactory $manifestFactory
     * @param ViewManifestFactory $viewManifestFactory
     * @param RegistryFramework $registry
     */
    public function __construct(
        DataHelper $customHelper,
        ManifestFactory $manifestFactory,
        ViewManifestFactory $viewManifestFactory,
        RegistryFramework $registry
    ) {
        $this->customHelper = $customHelper;
        $this->_manifestFactory = $manifestFactory;
        $this->_viewManifestFactory = $viewManifestFactory;
        $this->_registry = $registry;
    }

    /**
     * @param \Magento\Shipping\Model\Shipping\LabelGenerator $subject
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function beforeCreate(
        \Magento\Shipping\Model\Shipping\LabelGenerator $subject,
        \Magento\Sales\Model\Order\Shipment $shipment,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $viewManifest = $this->_viewManifestFactory->create();
        if ($shipmentId = $shipment->getEntityId()) {
            $viewManifest->load($shipmentId, 'magento_shipment_id');
        }
        $this->_registry->register('rw_canadapost_view_manifest', $viewManifest);

        if ($this->customHelper->allowCustomValueToShipmentForm()) {
            $manifestId = null;
            $shipmentData = $request->getParam('shipment');
            if (isset($shipmentData['rw_manifest_id'])) {
                $manifestId = $shipmentData['rw_manifest_id'];
            }

            $manifest = $this->_manifestFactory->create();
            if ((int)$manifestId) {
                $manifest->load($manifestId);
            } elseif ($manifestId == 'new' || !$manifestId) {
                $manifest->setGroupId(time());
            }

            $this->_registry->register('rw_canadapost_manifest_group_id', $manifest);
        }
    }
}
