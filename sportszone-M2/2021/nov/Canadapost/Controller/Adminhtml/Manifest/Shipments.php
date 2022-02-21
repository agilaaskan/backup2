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

declare(strict_types=1);

namespace Rootways\Canadapost\Controller\Adminhtml\Manifest;

use Magento\Backend\App\Action\Context;
use Rootways\Canadapost\Model\ManifestFactory;
use Rootways\Canadapost\Model\Carrier;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class \Rootways\Canadapost\Controller\Adminhtml\Manifest\Shipments
 */
class Shipments extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Rootways_Canadapost::rwcanadapost_shipping';

    /**
     * @var ManifestFactory
     */
    protected $manifestFactory;

    /**
     * @var Carrier
     */
    protected $carrier;

    /**
     * @param Context $context
     * @param ManifestFactory $manifestFactory
     * @param Carrier $carrier
     */
    public function __construct(
        Context $context,
        ManifestFactory $manifestFactory,
        Carrier $carrier
    ) {
        $this->manifestFactory = $manifestFactory;
        $this->carrier = $carrier;
        parent::__construct($context);
    }

    /**
     * Mass Delete Action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $manifest = $this->manifestFactory->create();
        $manifest->load($id);

        $errorMsg = [];
        if ($manifest->getStatus() != \Rootways\Canadapost\Model\Carrier::MANIFEST_COMPLETED) {
            $response = $this->carrier->getManifest($manifest);
            if (isset($response['status']) && $response['status'] == 1) {
                
            } else {
                $errorMsg = array_merge($errorMsg, $response['message']);
            }
        } else {
            $errorMsg = array(array('id'=> $manifest->getId(), 'error_message'=> 'Transmit Shipment has already been created. You should be able to print Manifest.'));
        }

        if ($errorMsg) {
            foreach ($errorMsg as $error) {
                $this->messageManager->addErrorMessage(
                    __('ID: %1. Error Message: %2', [$error['id'], $error['error_message']])
                );
            }
        } else {
            $this->messageManager->addSuccessMessage(__('Transmit Shipments have been successfully generated.'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('rwcanadapost/*/index');
    }
}
