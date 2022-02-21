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

namespace Rootways\Canadapost\Ui\DataProvider\ManifestView\Listing;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Event\ManagerInterface as EventManager;

class Collection extends SearchResult
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    protected $_manifestId;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param null|string $resourceModel
     * @param null|string $identifierName
     * @param null|string $connectionName
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        $mainTable,
        $resourceModel = null,
        $identifierName = null,
        $connectionName = null
    ) {
        $this->_request = $request;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName,
            $connectionName
        );
    }


    /**
     * Override _initSelect to add custom columns
     *
     * @return void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $id = $this->getManifestId($this->_request->getParam('id'));
        
        if ($id = '') {
            $this->getSelect()->where('main_table.manifest_id = ?', $id);
        }

        return $this;
    }

    public function getManifestId($id)
    {
        if (!$this->_manifestId) {
            $this->_manifestId = $id;
        }

        return $this->_manifestId;
    }
}
