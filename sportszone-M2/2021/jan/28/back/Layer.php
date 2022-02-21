<?php
namespace Askan\Salefilter\Plugin;



class Layer
{
  protected $request;
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\RequestInterface $req
    ) {
       $this->request = $request;
       $this->req = $req;
    }

  public function aroundGetProductCollection(
    \Magento\Catalog\Model\Layer $subject,
    \Closure $proceed
  ) {
    $result = $proceed();
   $sale = $this->req->getParam('csale');
    if($sale == 1){
    $result->addAttributeToFilter('special_price', ['neq' => '']);
  }return $result;
    }
    
}

