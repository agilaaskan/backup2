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

namespace Rootways\Canadapost\Ui\DataProvider\ManifestView;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class ListingDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /** @return array */
    public function getData()
    {
        $manifestId = $this->request->getParam('id');
        if ($manifestId) {
            $this->data['config']['params']['id'] = $manifestId;

            $this->addFilter(
                $this->filterBuilder
                    ->setField('manifest_id')
                    ->setValue($manifestId)
                    ->create()
            );
        }
        $data = parent::getData();

        return $data;
    }
}
