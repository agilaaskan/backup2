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

namespace Rootways\Canadapost\Controller\Adminhtml\Manifest;

use Magento\Backend\App\Action\Context;
use Rootways\Canadapost\Model\ManifestFactory;

class View extends \Magento\Backend\App\Action
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
     * @param Context $context
     * @param ManifestFactory $manifestFactory
     */
    public function __construct(
        Context $context,
        ManifestFactory $manifestFactory
    ) {
        $this->manifestFactory = $manifestFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $manifest = $this->manifestFactory->create();
        $manifest->load($id);
        $groupName = $manifest->getGroupId();

        $resultPage = $this->resultFactory->create('page');
        $resultPage->setActiveMenu('Rootways_Canada Post::manifest');
        $resultPage->addBreadcrumb(__('Manifest'), __('Manifest'));
        $resultPage->addBreadcrumb(__('View'), __('View'));
        $resultPage->getConfig()->getTitle()->prepend(__('Shipment List for Manifest Group ID ' . $groupName));

        return $resultPage;
    }
}
