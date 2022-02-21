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

namespace Rootways\Canadapost\Plugin\Sales\Model\Order;

use Rootways\Canadapost\Helper\Data as DataHelper;
use Magento\Framework\Registry;

class Shipment
{
    /**
     * @var DataHelper
     */
    private $customHelper;

    /**
     * @var Registry
     */
    private $_registry;

    /**
     * @param DataHelper $customHelper
     * @param Registry $registry
     */
    public function __construct(
        DataHelper $customHelper,
        Registry $registry
    ) {
        $this->customHelper = $customHelper;
        $this->_registry = $registry;
    }

    /**
     * @param \Magento\Sales\Model\Order\Shipment $subject
     * @param \Magento\Sales\Model\Order\Shipment $orderShipment
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function afterSave(
        \Magento\Sales\Model\Order\Shipment $subject,
        \Magento\Sales\Model\Order\Shipment $orderShipment
    ) {
        $manifestRowId = null;
        if ($this->customHelper->getQuoteType() == "1" &&
            $this->customHelper->getContractId() != '') {
            if ($manifest = $this->_registry->registry('rw_canadapost_manifest_group_id')) {
                if ($manifest->getId()) {
                    $manifestRowId = $manifest->getId();
                }
            }
        }
        if ($viewManifest = $this->_registry->registry('rw_canadapost_view_manifest')) {
            $viewManifest->setMagentoShipmentId((int)$orderShipment->getEntityId());
            $viewManifest->setIncrementId($orderShipment->getOrderId());
            $viewManifest->setStoreId((int)$orderShipment->getStoreId());
            $viewManifest->setManifestId($manifestRowId);
            $viewManifest->save();
        }
        
        return $orderShipment;
    }
}
