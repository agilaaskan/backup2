<?php

namespace Amasty\Feed\Model\GoogleWizard;

use Amasty\Feed\Model\Export\Product as ExportProduct;

/**
 * Class Link
 */
class Link extends Element
{
    protected $type = 'attribute';

    protected $tag = 'link';

    protected $limit = 2000;

    protected $modify = 'html_escape';

    protected $value = ExportProduct::PREFIX_URL_ATTRIBUTE . '|with_category';

    protected $name =  'link';

    protected $description = "URL directly linking to your item's page on your website";

    protected $required = true;

    /**
     * Get tag values
     *
     * @return array
     */
    protected function getEvaluateData()
    {
        return [
            ":tag" => $this->getTag(),
            ":value" => $this->getValue(),
            ":format" => $this->getFormat(),
            ":optional" => $this->getOptional(),
            ":modify" => $this->getModify(),
            ":parent" => 'yes'
        ];
    }
}
