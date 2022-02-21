<?php

namespace Amasty\Feed\Controller\Adminhtml\Feed;

/**
 * Class Template
 *
 * @package Amasty\Feed
 */
class Template extends AbstractMassAction
{
    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     */
    public function massAction($collection)
    {
        foreach ($collection as $model) {
            $this->feedCopier->template($model);
            $this->messageManager->addSuccessMessage(__('Template %1 was created', $model->getName()));
        }
    }
}
