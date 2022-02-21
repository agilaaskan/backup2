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
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class \Rootways\Canadapost\Controller\Adminhtml\Manifest\ShippingLabel
 */
class ShippingLabel extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Rootways_Canadapost:rwcanadapost_shipping';

    /**
     * @var ManifestFactory
     */
    protected $manifestFactory;

    /**
     * @var Carrier
     */
    protected $carrier;

    /**
     * @var FileFactory
     */
    private $_fileFactory;

    /**
     * @param Context $context
     * @param ManifestFactory $manifestFactory
     * @param Carrier $carrier
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        ManifestFactory $manifestFactory,
        Carrier $carrier,
        FileFactory $fileFactory
    ) {
        $this->manifestFactory = $manifestFactory;
        $this->carrier = $carrier;
        $this->_fileFactory = $fileFactory;
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
        if ($manifest->getStatus() == \Rootways\Canadapost\Model\Carrier::MANIFEST_COMPLETED) {
            $response = $this->carrier->getArtifact($manifest);
            if (isset($response['status']) && $response['status'] == 1) {
                return $this->_fileFactory->create(
                    'ManifestLabel(' . $manifest->getId() . ').pdf',
                    $response['data'],
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            } else {
                $errorMsg = array_merge($errorMsg, $response['message']);
            }
        } else {
            $this->messageManager->addErrorMessage(__('Manifest ID has not been generated. To Print Manifest, please create Transmit Shipment.'));
        }

        if ($errorMsg) {
            foreach ($errorMsg as $error) {
                $this->messageManager->addErrorMessage(
                    __('Manifest id: %1. Error Message: %2', [$error['id'], $error['error_message']])
                );
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('rwcanadapost/*/index');
    }
}
