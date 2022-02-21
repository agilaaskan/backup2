<?php
$path = getcwd();

$path = str_replace("apis","",$path);
require_once($path.'app/Mage.php');
$app = Mage::app('admin');
umask(0);


$write = Mage::getSingleton('core/resource')->getConnection('core_write');
$sql2 = 'DELETE FROM catalog_category_product WHERE product_id not in (select entity_id from catalog_product_entity)';
$write->query($sql2);   
$sql3 = 'DELETE FROM catalog_category_product WHERE category_id not in (select entity_id from catalog_category_entity)';
$write->query($sql3); 

exec('/usr/bin/php -f '.$path.'/shell/indexer.php reindexall');


function callAPIs($par){
	
	$inputJSON = file_get_contents('php://input');
	$dataArr = json_decode($inputJSON, TRUE );
	$client = new SoapClient('https://www.extremesports.com.kw/api/v2_soap?wsdl=1');
	
	/* user details */
	$apiUser = $dataArr['apiUser'];
	$apiKey = $dataArr['apiKey'];
	//return "apiuser ".$dataArr['apiUser'];
	
	try{
		$sessionId = $client->login($dataArr['apiUser'], $dataArr['apiKey']);
	} catch (exception $e) {
		$returnArr = array("status" =>'0', "message"=>'Invalid username or API KEY');
		return $returnArr;
	}
	
	$dir = getcwd();
	//exec('/usr/bin/php -f '.$path.'/shell/indexer.php reindexall');
	return array("status" =>'1', "message"=>'Reindexing Success'); 
	
	$client->endSession($sessionId);

}
$arr = callAPIs($_REQUEST);
echo json_encode($arr,$path);
exit;
	
?>