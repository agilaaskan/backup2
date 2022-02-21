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

namespace Rootways\Canadapost\Model;

class ViewManifest extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Rootways\Canadapost\Model\ResourceModel\ViewManifest');
    }
}
