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

class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Rootways_Canadapost:rwcanadapost_shipping';

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create('page');
        $resultPage->setActiveMenu('Rootways_Canada Post::manifest');
        $resultPage->addBreadcrumb(__('Manifest'), __('Manifest'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Canada Post Manifests'));

        return $resultPage;
    }
}
