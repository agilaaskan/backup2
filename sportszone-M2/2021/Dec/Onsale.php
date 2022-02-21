<?php
namespace Inchoo\Onsale\Block;
 
class Onsale extends \Magento\Framework\View\Element\Template
{
        protected $_storeManager;
        protected $_urlInterface;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface,    
        array $data = []
    )
    {        
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function getOnSaleUrl()
    {
        $url=$this->_storeManager->getStore()->getCurrentUrl('*/*/*', array(
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => array(
                'sale' => 1,
                'p' => NULL
            )
        ));
 
        return $url;
    }
 
    public function getNotOnSaleUrl()
    {    
        $url=$this->_storeManager->getStore()->getCurrentUrl('*/*/*', array(
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => array(
                'sale' => NULL,
                'p' => NULL
            )
        ));
        return $url;
    }
}
