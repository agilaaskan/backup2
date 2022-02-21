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
use Rootways\Canadapost\Model\ResourceModel\Manifest\CollectionFactory;
use Rootways\Canadapost\Model\Carrier;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class \Rootways\Canadapost\Controller\Adminhtml\Manifest\MassShipments
 */
class MassShipments extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Rootways_Canadapost:rwcanadapost_shipping';

    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Carrier
     */
    protected $carrier;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Carrier $carrier
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Carrier $carrier
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
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
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $manifestSuccessIds = [];
        $manifestFailedIds = [];
        $manifestSuccess = 0;
        $manifestError = 0;
        $errorMsg = [];
        foreach ($collection as $manifest) {
            $response = null;
            if ($manifest->getStatus() != \Rootways\Canadapost\Model\Carrier::MANIFEST_COMPLETED) {
                $response = $this->carrier->getManifest($manifest);
                if (isset($response['status']) && $response['status'] == 1) {
                    $manifestSuccessIds[] = $manifest->getId();
                    $manifestSuccess++;
                } else {
                    $errorMsg = array_merge($errorMsg, $response['message']);
                    $manifestError++;
                }
            } else {
                // already created.
            }
        }

        if ($manifestSuccess) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) shipments have been generated.', $manifestSuccess)
            );
        }

        if ($manifestError) {
            $this->messageManager->addErrorMessage(
                __(
                    'A total of %1 record(s) shipments were not generated.',
                    $manifestError
                )
            );
            foreach ($errorMsg as $error) {
                $this->messageManager->addErrorMessage(
                    __('Manifest id: %1. Error Message: %2', [$error['id'], $error['error_message']])
                );
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('rwcanadapost/*/index');
    }
}
