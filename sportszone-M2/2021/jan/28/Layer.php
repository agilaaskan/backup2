<?php
namespace Askan\Salefilter\Plugin;



class Layer
{
  protected $request;
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\RequestInterface $req
    ) {
       $this->request = $request;
       $this->req = $req;
       $this->timezone = $timezone;
    }

  public function aroundGetProductCollection(
    \Magento\Catalog\Model\Layer $subject,
    \Closure $proceed
  ) {
    $now = $this->timezone->date()->format('Y-m-d');
    $result = $proceed();
   $sale = $this->req->getParam('csale');
    if($sale == 1){
      $result->addAttributeToFilter('special_price', ['neq' => ''])
      ->addAttributeToFilter([['attribute' => 'special_from_date',
                  'lteq' => date('Y-m-d G:i:s', strtotime($now)),
                  'date' => true, ], ['attribute' => 'special_to_date',
                  'gteq' => date('Y-m-d G:i:s', strtotime($now)),
                  'date' => true,]]);
        }return $result;
    }
    
}

