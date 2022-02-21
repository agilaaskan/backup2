<?php
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(0);	 
require_once('db.php');
//mysqli_query("SET NAMES utf8");
function getProductIdFromSku($sku, $table_prefix= '',$conn){

	$sql = "SET innodb_lock_wait_timeout = 5000";
	mysqli_query($conn,$sql);
	$sql = "SELECT entity_id as product_id, sku FROM ".$table_prefix."catalog_product_entity where sku = '".$sku."'";
	$res = mysqli_query($conn,$sql);
	$cnt = mysqli_num_rows($res);
	if($cnt > 0){
		$res_arr = mysqli_fetch_array($res); 
		$product_id = $res_arr['product_id']; 
		return $product_id;
	} 
	return 0;
}

function getAssciatedProductFromID($pid, $table_prefix= '',$conn){

	$sql = "SELECT product_id FROM ".$table_prefix."catalog_product_super_link where parent_id = '".$pid."' order by link_id ASC limit 1";
	$res = mysqli_query($conn,$sql);
	$cnt = mysqli_num_rows($res);
	if($cnt > 0){
		$res_arr = mysqli_fetch_array($res); 
		$product_id = $res_arr['product_id']; 
		return $product_id;
	} 
	return 0;
}

function removeProductImagesFromDB($remove_img, $prdExistID, $table_prefix='',$conn){
	
	$sql = "SELECT value_id FROM ".$table_prefix."catalog_product_entity_media_gallery where entity_id = '".$prdExistID."' AND value like '".$remove_img."'";
	$res = mysqli_query($conn,$sql);
	$cnt = mysqli_num_rows($res);
	
	if($cnt > 0){
		$res_arr = mysqli_fetch_array($res); 
		$value_id = $res_arr['value_id']; 
		
		mysqli_query($conn,$sql);
		if($value_id > 0){
			$sql ="SET FOREIGN_KEY_CHECKS = 0";
			mysqli_query($conn,$sql);
			
			$sql1 = "delete FROM `catalog_product_entity_media_gallery` WHERE `value_id` = '".$value_id."'";
			mysqli_query($conn,$sql1);
			
			$sql2 = "delete FROM `catalog_product_entity_media_gallery_value` WHERE `value_id` = '".$value_id."'";
			mysqli_query($conn,$sql2);
		}
		
	} 
}function removeSuperLinkProducts($pid, $table_prefix= '',$con){

	$sql = "SELECT * FROM ".$table_prefix."catalog_product_super_link where parent_id = '".$pid."'";
	$res = mysqli_query($con,$sql);
	$cnt = mysqli_num_rows($res);
	if($cnt > 0){
		while($res_arr = mysqli_fetch_array($res)){
			$product_super_attribute_id = $res_arr['product_super_attribute_id'];
			$sql = "delete from catalog_product_super_attribute_label where product_super_attribute_id = '".$product_super_attribute_id."'";
			$res = mysqli_query($con,$sql);
			
			$sql = "delete from catalog_product_super_attribute_pricing where product_super_attribute_id = '".$product_super_attribute_id."'";
			$res = mysqli_query($con,$sql);
			
		}
		
	} 

	$sql = "delete from catalog_product_super_link where parent_id = '".$pid."'";
	
	
//Expand/CollapseStructurecatalog_product_super_attribute_label
//Expand/CollapseStructurecatalog_product_super_attribute_pricing

	$res = mysqli_query($con,$sql);
	
	$sql = "delete from catalog_product_super_attribute where product_id = '".$pid."'";
	
	$res = mysqli_query($con,$sql);
	
	return 0;
}
function updateGroupprice($groupId,$groupprice,$result,$table_prefix='',$conn){
	$pricevalue = $groupprice;
		$sql ="SET FOREIGN_KEY_CHECKS = 0";
		mysqli_query($conn,$sql);
			
$sql = "INSERT INTO ".$table_prefix."catalog_product_entity_group_price (entity_id,all_groups,customer_group_id,value,website_id) VALUES ('".$result."','0','".$groupId."','".$pricevalue."','0')";
$res = mysqli_query($conn,$sql);

}

function deleteGroupprice($result,$table_prefix='',$conn){
	
    $sql = "delete from ".$table_prefix."catalog_product_entity_group_price where entity_id = '".$result."'";
    $res = mysqli_query($conn,$sql);

}

function updateCategories($prdID,$catID, $table_prefix= '',$conn){
	
	$sql = "SELECT product_id FROM ".$table_prefix."catalog_category_product where product_id = '".$prdID."' AND category_id = '".$catID[0]."'";
	$res = mysqli_query($conn,$sql);
	$cnt = mysqli_num_rows($res);
	
	if($cnt == 0){
		$sql ="SET FOREIGN_KEY_CHECKS = 0";
		mysqli_query($conn,$sql);
		$sql2 = "INSERT INTO ".$table_prefix."catalog_category_product (`category_id` ,`product_id` ,`position`) VALUES ( '".$catID[0]."', '".$prdID."', '0')";
		mysqli_query($conn,$sql2);
	} 
}

function updateConfigrableProductAttribute($prdstoreId, $optionId, $prdID,$attId, $table_prefix= '',$conn){
	
	if($prdstoreId == '2'){ 
		$sql = "delete from  ".$table_prefix."catalog_product_entity_int where entity_id = '".$prdID."' and attribute_id = '".$attId."'";
       
		$res = mysqli_query($conn,$sql); 
		
		$store_array = array('0','2','15');
		foreach($store_array as $storeId){
			$sql2 = "INSERT INTO ".$table_prefix."catalog_product_entity_int (`entity_type_id` ,`attribute_id` ,`store_id`,`entity_id`,`value`) VALUES ('4','".$attId."', '".$storeId."','".$prdID."', '".$optionId."')";
			mysqli_query($conn,$sql2);
           
			
			if($storeId == '15'){
				//$$LAST_INSERT_ID();
				$sql = "update ".$table_prefix."catalog_product_entity_int set value ='".$optionId."' WHERE entity_id = '".$prdID."' and attribute_id = '".$attId."' and store_id = '0'";
				$res = mysqli_query($conn,$sql);
             
			
			}
		}		
		
	}else if($prdstoreId == '15'){
        
       $sql = "SELECT * FROM ".$table_prefix."catalog_product_entity_int WHERE attribute_id = '".$attId."' and store_id = '2' and entity_id = '".$prdID."' ";
	  $res = mysqli_query($conn,$sql);
	 $cnt = mysqli_num_rows($res);
       if( $cnt > 0){
           $res_s = mysqli_fetch_array($res);
           $optionId =  $res_s['value'];
           $sql = "update ".$table_prefix."catalog_product_entity_int set value ='".$optionId."' WHERE entity_id = '".$prdID."' and attribute_id = '".$attId."' and store_id = '0' ";
	        $res = mysqli_query($conn,$sql);
       }
        
    }
}



function UpdateAttributeLabelStoreWise($attId, $prdstoreId, $frontendlabel, $table_prefix='',$conn){
	
	$sql = "SELECT attribute_label_id FROM ".$table_prefix."eav_attribute_label WHERE attribute_id = '".$attId."' and store_id = '".$prdstoreId."' ";
	$res = mysqli_query($conn,$sql);
	$cnt = mysqli_num_rows($res);
	
	if($cnt > 0){
		$sql = "update ".$table_prefix."eav_attribute_label set value = '".$frontendlabel."' 
		WHERE attribute_id = '".$attId."' and store_id = '".$prdstoreId."' ";
		$res = mysqli_query($conn,$sql);
	}else{
		
		$sql = "insert into ".$table_prefix."eav_attribute_label (`attribute_id`, `store_id`, `value`) 
		VALUES ('".$attId."', '".$prdstoreId."', '".$frontendlabel."')";
		$res = mysqli_query($conn,$sql);
		$cnt = 0;
	} 
	return $cnt;
}

function getAttributeOptionsID($attID,$prdstoreId,$frontendlabel,$table_prefix='',$conn){
	
	$sql = "SELECT option_id FROM ".$table_prefix."eav_attribute_option WHERE attribute_id = '".$attID."' ORDER BY `option_id` DESC";
	$res = mysqli_query($conn,$sql);
	$cnt = mysqli_num_rows($res);
	
	if($cnt > 0){
		$res_arr = mysqli_fetch_array($res); 
		$option_id = $res_arr['option_id'];
		
		if($prdstoreId == "2" || $prdstoreId == "0"){
			/*$sql_a = "SELECT option_id FROM ".$table_prefix."eav_attribute_option_value 
		     WHERE option_id = '".$option_id."' and value = '".$frontendlabel."' ";*/
            
             $sql_a =" SELECT * FROM eav_attribute_option_value eaov
             INNER JOIN eav_attribute_option eao on eaov.option_id = eao.option_id
    		 WHERE eao.attribute_id = '".$attID."' and eaov.value = '".$frontendlabel."' group by eaov.value";
             		
		}else{
			$sql_a = "SELECT option_id FROM ".$table_prefix."eav_attribute_option_value 
		     WHERE option_id = '".$option_id."' and store_id = '0' ";  //value = '".$frontendlabel."' 
		}
        
        
		
		
	    $res_a = mysqli_query($conn,$sql_a);
	    $cnt_a = mysqli_num_rows($res_a);
		
		if($cnt_a == 0){
                   //echo $sql_a; 
                   $sql = "insert into ".$table_prefix."eav_attribute_option (`attribute_id`, `sort_order`) 
					VALUES ('".$attID."', '0')";
					$res = mysqli_query($conn,$sql);
					$option_id = mysqli_insert_id();
				
		}
		else{
			$res_arr = mysqli_fetch_array($res_a); 
			$option_id = $res_arr['option_id'];
			
		}
		
	}else{
		$option_id = 0;
	} 
	
	
	return $option_id;
}

function UpdateAttributeOptionsLabelStoreWise($option_id, $prdstoreId, $frontendlabel, $table_prefix='',$conn){
	
	//if($prdstoreId == '4'){ $frontendlabel = utf8_encode($frontendlabel); }
	
	$sql = "SELECT value_id FROM ".$table_prefix."eav_attribute_option_value WHERE option_id = '".$option_id."' and store_id = '".$prdstoreId."' ";
	$res = mysqli_query($conn,$sql);
	$cnt = mysqli_num_rows($res);
	 
	if($cnt > 0){
		//$frontendlabel = mb_convert_encoding($frontendlabel, 'UTF-16LE', 'UTF-8');
		$sql = "update ".$table_prefix."eav_attribute_option_value set value = '".html_entity_decode($frontendlabel)."' 
		WHERE option_id = '".$option_id."' and store_id = '".$prdstoreId."' ";
		$res = mysqli_query($conn,$sql);
	}else{
		//$frontendlabel = mb_convert_encoding($frontendlabel, 'UTF-16LE', 'UTF-8');
		$sql = "insert into ".$table_prefix."eav_attribute_option_value (`option_id`, `store_id`, `value`) 
		VALUES ('".$option_id."', '".$prdstoreId."', '".html_entity_decode($frontendlabel)."')";
		$res = mysqli_query($conn,$sql);
		$cnt = 0;
	} 
	return $cnt;
}

function checkPrdAtt($attCode, $table_prefix= '',$conn){
	
	$sql = "SELECT attribute_id FROM ".$table_prefix."eav_attribute WHERE attribute_code = '".$attCode."'";
	$res = mysqli_query($conn,$sql);
	$cnt = mysqli_num_rows($res);
	
	if($cnt > 0){
		$res_arr = mysqli_fetch_array($res); 
		$attribute_id = $res_arr['attribute_id'];
		$cnt = $attribute_id;
	}else{
		$cnt = 0;
	} 
	return $cnt;
}

function createCatelogProduct($prdstoredataData, $client, $sessionId, $table_prefix='',$conn){
	
	$returnArr =array();
	foreach($prdstoredataData as $prdData){
		$prdstoreId = $prdData['store_id'];
			// product data
		$prdType = strtolower($prdData['prdType']);
		$sku = $prdData['sku'];
		$configrable_attributes_array = array();
		
		$prdExistID = getProductIdFromSku($sku, $table_prefix,$conn);
	
		$colorValu = '';
		$sizeValu = '';
        $urlkey = '';
		if($prdType != 'configurable'){	
			
			foreach($prdData['additionalAttributes'] as $addAtts){
				foreach($addAtts as $addAtt){
					$addAtt = (object)($addAtt);
					$addAttkey = strtolower(str_replace(" ","_",trim($addAtt->key)));
					$attID = checkPrdAtt($addAttkey, $table_prefix,$conn);
					if($addAtt->frontendlabel == '')
					{
						$frontendlabel = $addAtt->key;
					}
					else
					{
						$frontendlabel = $addAtt->frontendlabel;
					}
					
					if($attID == 0){
						
						$attData = array(
							"attribute_code" =>$addAttkey,
							"frontend_input" => "text",
							"scope" => "0",
							"default_value" => "0",
							"is_unique" => 0,
							"is_required" => 0,
							"apply_to" => array("simple","grouped","configurable","virtual","bundle","downloadable"),
							"is_configurable" => 1,
							"is_searchable" => 1,
							"is_visible_in_advanced_search" => 1,
							"is_comparable" => 1,
							"is_used_for_promo_rules" => 1,
							"is_visible_on_front" => 1,
							"used_in_product_listing" => 1,
							"additional_fields" => array(),
							"frontend_label" => array(array("store_id" =>"0", "label" =>$frontendlabel))
						);
					
						try{
							$attId = $client->catalogProductAttributeCreate($sessionId, $attData);
						} catch(exception $e){
							//return $returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
						}
						
						/*if($prdstoreId == 0){ 
                          UpdateAttributeLabelStoreWise($attId, '0', $frontendlabel, $table_prefix,$conn);
						  UpdateAttributeLabelStoreWise($attId, $prdstoreId, $frontendlabel, $table_prefix,$conn);
                          
                        }*/
				
						$label = array (
							array(
								"store_id" => array(0),
								"value" => $addAtt->value
							)
						);
						$data = array(
							"label" => $label,
							"order" => "1",
							"is_default" => "1"
						);
						
						try{
							$option_id = $client->catalogProductAttributeAddOption($sessionId, $addAttkey, $data); 
						} catch(exception $e){
							
							//$returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
						}
						
						if($addAttkey =='color')
						{
							 $colorValu = $addAtt->value;
						}
						if($addAttkey =='size')
						{
							 $sizeValu = $addAtt->value;
						}
						$optionID = getAttributeOptionsID($attId,$prdstoreId,$addAtt->value,$table_prefix,$conn);
						if($optionID > 0){
							UpdateAttributeOptionsLabelStoreWise($optionID, '0', $addAtt->value, $table_prefix,$conn);
							UpdateAttributeOptionsLabelStoreWise($optionID, $prdstoreId, $addAtt->value, $table_prefix,$conn);
						}
						try{
							$client->catalogProductAttributeSetAttributeAdd($sessionId, $attId, 4 );
						} catch(exception $e){
							//return $returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
						}

					}else{
						
						$attributeCode = $addAttkey; 
						$optionID = getAttributeOptionsID($attID,$prdstoreId,$addAtt->value,$table_prefix,$conn);
						
						if($optionID == 0){
							
								  $label = array (
										array(
											"store_id" => array(0),
											"value" => $addAtt->value
										)
									);
									$data = array(
										"label" => $label,
										"order" => "1",
										"is_default" => "1"
									);
									
									try{
										$option_id = $client->catalogProductAttributeAddOption($sessionId, $addAttkey, $data); 
									} catch(exception $e){											
			
									}
									
									$optionID = getAttributeOptionsID($attID,$prdstoreId,$addAtt->value,$table_prefix,$conn);
									
						}
						
						if($optionID > 0){
							if( $prdstoreId == '2'){ UpdateAttributeOptionsLabelStoreWise($optionID, '0', $addAtt->value, $table_prefix,$conn); }
							UpdateAttributeOptionsLabelStoreWise($optionID, $prdstoreId, $addAtt->value, $table_prefix,$conn);
						}
						
						if($addAttkey =='color')
						{
							 $colorValu = $addAtt->value;
						}
						
						if($addAttkey =='size')
						{
							 $sizeValu = $addAtt->value;
						}
							
					}
                    
					$configrable_attributes = array('color','size');
                    $addAttkey = strtolower($addAttkey);
					if(in_array($addAttkey,$configrable_attributes)){
						$configrable_attributes_array[] = array('optionId'=>$optionID, 'attID'=>$attID,'attr_key'=>$addAttkey,'value'=>$addAtt->value); 
					}
						
				}
			}
			
		}
        
        if($prdstoreId == '2'){
            if($prdType != 'configurable')
            {
                if($colorValu != "" && $sizeValu != ""){
                          
                    $urlkey = trim($prdData['prdName']).' '.strtoupper($colorValu).' '.strtoupper($sizeValu);
                            
                }else if($colorValu != "" && $sizeValu == ""){
                          
                    $urlkey = trim($prdData['prdName']).' '.strtoupper($colorValu);
                        
                }else if($sizeValu != "" && $colorValu == ""){
                            
                        $urlkey = trim($prdData['prdName']).' '.strtoupper($sizeValu);
                            
                }else{
                            
                    $urlkey = trim($prdData['prdName']);
                            
                }
            }
            else{
                $urlkey = trim($prdData['prdName']);
            }
            $urlkey = str_replace(" ","-",trim($urlkey));
        }
        
        $productName = trim($prdData['prdName']);
	
		   /*if($prdType != 'configurable')
           {
			  	$productName = strip_tags($prdData['prdName']).'-'.$colorValu.'-'.$sizeValu;
           }
           else{
			  	$productName = strip_tags($prdData['prdName']);
		   }*/
	
		if($prdExistID <= 0 ){  
		 
			/*$specialPrice = '';
			if($prdData['prdSpecialPrice'] < $prdData['prdPrice'])
			{
				$specialPrice = $prdData['prdSpecialPrice'];
			}*/
            if(!isset($prdData['prdDesc']) || $prdData['prdDesc'] == ""){
                $prdDesc = '&nbsp;';
            }else{
                $prdDesc = $prdData['prdDesc'];
            }
			
			$prdSpecialFromDate = $prdData['prdSpecialFromDate'];	
			if($prdSpecialFromDate !=""){
				$prdSpecialFromDate = date('Y-m-d', strtotime($prdSpecialFromDate));
			}
			
			$prdSpecialToDate = $prdData['prdSpecialToDate'];	
			if($prdSpecialToDate !=""){
				$prdSpecialToDate = date('Y-m-d', strtotime($prdSpecialToDate));
			}
			
			/* prd data array */
			$productData = array(
				'categories' => $prdData['prdCategories'],
				'websites' => array(1),
				'storeView' => $prdstoreId,
				'name' => $productName,
                //'url_key' => $urlkey,
				'description' => $prdData['prdDesc'],
				'short_description' => $prdData['prdShortDesc'],
				'weight' => $prdData['prdWeight'],
				'status' => $prdData['prdStatus'],
				'visibility' => $prdData['prdVisibility'],
				'price' => $prdData['prdPrice'],
				//'special_price' => $specialPrice,
				'special_price' => $prdData['prdSpecialPrice'],
				'special_from_date' => $prdSpecialFromDate,
				'special_to_date' => $prdSpecialToDate,
				'tax_class_id' => $prdData['prdTaxId'],
				'additional_attributes' => $prdData['additionalAttributes'],
				'configurable_attributes' => $prdData['configurable_attributes'],
				'meta_title' => $prdData['prdMetaTitle'],
				'meta_keyword' => $prdData['prdMetaKeyword'],
				'meta_description' => $prdData['prdMetaDesc'],
				'stock_data' => array(
					'qty' => $prdData['prdQuantity'] , 
					'is_in_stock' => $prdData['prdInStock'],
					'manage_stock' => $prdData['prdMngStock'],
					'use_config_manage_stock' => $prdData['prdConfigMngStock'],
					'min_qty' => $prdData['prdMinQty'],
					'use_config_min_qty' => $prdData['prdConfigMinQty'],
					'min_sale_qty' => $prdData['prdMinSaleQty'],
					'use_config_min_sale_qty' => $prdData['prdConfigMinSaleQty'],
					'max_sale_qty' => $prdData['prdMaxSaleQty'],
					'use_config_max_sale_qty' => $prdData['prdConfigMaxSaleQty']
				)
			);
            
            if($prdstoreId == '2'){
                $productData['url_key'] = $urlkey;
            }
		
			// get attribute set
			try{
				$attributeSets = $client->catalogProductAttributeSetList($sessionId);
			} catch(exception $e){
				//return $returnArr[] = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
			}	
			$attributeSet = current($attributeSets);
			
			if($prdstoreId == '2'){
				
				if($prdType == 'configurable'){
					
					$productData['associated_skus'] = $prdData['associatedSKUs'];
					array_push($productData['associated_skus'],'attributes');			
					foreach($prdData['priceChangeRule'] as $atts => $opts){

						$atts = str_replace(" ","_",trim($atts));
						
						foreach($opts as $opt => $vals){
							array_push($productData['associated_skus'],$atts);
							array_push($productData['associated_skus'],$opt);
							array_push($productData['associated_skus'],$vals);
							
						}
					}
					$productData['price_changes'] = (object)$prdData['priceChangeRule'];
				}
			}
		
			try{
				$result = $client->catalogProductCreate($sessionId, $prdType, $attributeSet->set_id, $sku, $productData,$prdstoreId);		
			}catch(exception $e){
				return $returnArr[] = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
			}
		
			$prdImages = array();
			$NewImage = array();
			$images_updated = 'no';
			if($prdstoreId == '2'){ 
				 
				foreach($prdData['prdImg'] as $k=>$img){
					if(strlen($img) > 0){
						$images_updated = 'yes';
						$img_content = base64_encode(file_get_contents($img));				
						if($img_content !="" ){
							//$prdImages = array(); 
							$image_name = 'product_image_'.$result.'_'.time();
					        $file = array('content' => $img_content, 'mime' => 'image/jpeg','name'=>$image_name);
							try{
								$prdImage = $client->catalogProductAttributeMediaCreate($sessionId, $result, array('file' => $file, 'label' => $prdData['prdName'], 'types' => array('image','small_image','thumbnail'), 'exclude'=>0));
								array_push($prdImages,$prdImage);
							} catch(exception $e){
								//return $returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
							}
						}
						
					}
				
				}
			}
			// Update Product group price
            
            $prdExistID = $result;
			
			foreach($prdData['prdGroupPrice'] as $groupPrice){
				$groupId = $groupPrice['groupId'];
				$grouppricevalue = $groupPrice['price'];
				updateGroupprice($groupId,$grouppricevalue,$result,$table_prefix,$conn);
						
				}
			
            if($prdType != 'configurable' && sizeof($configrable_attributes_array) > 0){	
				
				foreach($configrable_attributes_array as $cofigAttrArr){
					if($prdstoreId == 2){
                         updateConfigrableProductAttribute($prdstoreId, $cofigAttrArr['optionId'], $prdExistID, $cofigAttrArr['attID'], $table_prefix,$conn);    
					   }else{
						  updateConfigrableProductAttribute($prdstoreId, $cofigAttrArr['optionId'], $prdExistID, $cofigAttrArr['attID'], $table_prefix,$conn);  
					   }
						
				}
				
			} 
		
				if($images_updated == 'no'  && $prdType == 'configurable'){
					
						$image_url = 'https://extremesports.octopusbridge.com/media/wysiwyg/placeholder.jpg';
						$img_content = base64_encode(file_get_contents($image_url));
						$image_name = 'product_image_'.$prdExistID.'_'.time().'.jpg';
					    $file = array('content' => $img_content, 'mime' => 'image/jpeg','name'=>$image_name);
						try{
							$newCreatedImg = $client->catalogProductAttributeMediaCreate($sessionId, $prdExistID, array('file' => $file, 'label' => $prdData['prdName'], 'types' => array('image','small_image','thumbnail'), 'exclude'=>0));
						} catch(exception $e){ echo $e->faultstring;
							//return $returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
						}
						
							
					
				}
				
			
			$info = new stdclass();
			$info->attributes = array('sku', 'name', 'description', 'price', 'url_path');
			$productObj = $client->catalogProductInfo($sessionId, $result, NULL, $info);	
			$getimageObj = $client->catalogProductAttributeMediaList($sessionId, $result, NULL, 'ID');
			$hosted_image_url = $getimageObj[0]->url;	
			$product_url = $productObj->url_path;
			//$productObj = $client->catalogProductInfo((object)array('sessionId' => $sessionId, 'productId' => $result));
			//$url_key = $productObj->result['url_key'];
			//$url_path = $productObj->result['url_path'];

			$returnArr[] = array("status" =>'1', "product_id"=>$result,"product_url" =>$product_url,"hosted_image_url"=>$hosted_image_url,"image_Posted_url"=>$image_Posted_url, "message"=>'Product created successfully store id'.$prdstoreId, 'new_ceated_imgs'=>$prdImages,'NewImage'=>$NewImage);
		
		}
		else{
            
            $forUpdateVars = (array) $prdData['forUpdate'];
			$productData = array();
			$productData = array(
				'websites' => array(1),
				'additional_attributes' => $prdData['additionalAttributes'],
				'configurable_attributes' => $prdData['configurable_attributes'],
				'stock_data' => array(
					'is_in_stock' => $prdData['prdInStock'],
					'manage_stock' => $prdData['prdMngStock'],
					'use_config_manage_stock' => $prdData['prdConfigMngStock'],
					'min_qty' => $prdData['prdMinQty'],
					'use_config_min_qty' => $prdData['prdConfigMinQty'],
					'min_sale_qty' => $prdData['prdMinSaleQty'],
					'use_config_min_sale_qty' => $prdData['prdConfigMinSaleQty'],
					'max_sale_qty' => $prdData['prdMaxSaleQty'],
					'use_config_max_sale_qty' => $prdData['prdConfigMaxSaleQty']
				)
			);
			
			$productData['name'] = $productName;
			//}
			//if($forUpdateVars['prdDesc'] == 1){
				$productData['description'] = $prdData['prdDesc'];
			//}
			//if($forUpdateVars['prdShortDesc'] == 1){
				$productData['short_description'] = $prdData['prdShortDesc'];
			//}
			//if($forUpdateVars['prdPrice'] == 1){
				$productData['price'] = $prdData['prdPrice'];
			//}
		//	if($forUpdateVars['prdQuantity'] == 1){
				$productData['stock_data']['qty'] = $prdData['prdQuantity'];
			//}
			//if($forUpdateVars['prdSpecialPrice'] == 1){
				/*$specialPrice = '';
				if($prdData['prdSpecialPrice'] < $prdData['prdPrice'])
    			{
    				$specialPrice = $prdData['prdSpecialPrice'];
    			}*/
				//$productData['special_price'] = $specialPrice;
				$productData['special_price'] = $prdData['prdSpecialPrice'];
		//	}
			//if($forUpdateVars['prdWeight'] == 1){
				$productData['weight'] = $prdData['prdWeight'];
			//}
		//	if($forUpdateVars['prdStatus'] == 1){
				$productData['status'] = $prdData['prdStatus'];
		//	}
			//if($forUpdateVars['prdUrlPath'] == 1){
				$productData['url_path'] = $prdData['prdUrlPath'];
                if($prdstoreId == '2'){
                    $productData['url_key'] = $urlkey;
                }
		//	}
		//	if($forUpdateVars['prdVisibility'] == 1){
				$productData['visibility'] = $prdData['prdVisibility'];
		//	}
		//	if($forUpdateVars['prdTaxId'] == 1){
			if($prdData['prdTaxId'] =='')
			{
				$prdData['prdTaxId'] ='0';
			}
				$productData['tax_class_id'] = $prdData['prdTaxId'];
		//	}
		
			$prdSpecialFromDate = $prdData['prdSpecialFromDate'];	
			if($prdSpecialFromDate !=""){
				$prdSpecialFromDate = date('Y-m-d', strtotime($prdSpecialFromDate));
			}
			
			$prdSpecialToDate = $prdData['prdSpecialToDate'];	
			if($prdSpecialToDate !=""){
				$prdSpecialToDate = date('Y-m-d', strtotime($prdSpecialToDate));
			}
			
				$productData['special_from_date'] = $prdSpecialFromDate;
		//	}
			//if($forUpdateVars['prdSpecialToDate'] == 1){
				$productData['special_to_date'] = $prdSpecialToDate;
		//	}
		//	if($forUpdateVars['prdMetaTitle'] == 1){
				$productData['meta_title'] = $prdData['prdMetaTitle'];
		//	}
			//if($forUpdateVars['prdMetaKeyword'] == 1){
				$productData['meta_keyword'] = $prdData['prdMetaKeyword'];
			//}
			//if($forUpdateVars['prdMetaDesc'] == 1){
				$productData['meta_description'] = $prdData['prdMetaDesc'];
			//}
		
			//$productData['storeId'] = '4';
			$productData['storeId'] = $productData['store_id'];
			if($prdType == 'configurable' && $prdstoreId == '2'){
				// Remove all previous link of configraable items
				removeSuperLinkProducts($prdExistID, $table_prefix,$con);  
					
				$productData['associated_skus'] = $prdData['associatedSKUs'];
				array_push($productData['associated_skus'],'attributes');			
				foreach($prdData['priceChangeRule'] as $atts => $opts){
					foreach($opts as $opt => $vals){
						array_push($productData['associated_skus'],$atts);
						array_push($productData['associated_skus'],$opt);
						array_push($productData['associated_skus'],$vals);
					}
				}
				$productData['price_changes'] = (object)$prdData['priceChangeRule'];
			
			}
		
		
			try{
				updateCategories($prdExistID, $prdData['prdCategories'], $table_prefix,$conn);  
				$result = $client->catalogProductUpdate($sessionId, trim($prdExistID), $productData,$prdstoreId);				
		
			}catch(exception $e){
		
				return $returnArr[] = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
			
			}
			
			
			if(sizeof($prdData['prdGroupPrice']) > 0){
                deleteGroupprice($prdExistID,$table_prefix,$conn);
            }
			
			$path = getcwd();
			
			if($prdstoreId == '2'){
				// remove old images
		
				if(sizeof($prdData['remove_images']) > 0){
			
					foreach($prdData['remove_images'] as $remove_img){
						try{
							if(strlen($remove_img) > 0){
								
								$image_path = $path."/../media/catalog/product".$remove_img;
								@unlink($image_path);
								removeProductImagesFromDB($remove_img, $prdExistID, $table_prefix,$conn); 
							}
						} 
						catch(exception $e){						
						}
					}
			
				}
		
			
				// Retrive exsting Images
				if(sizeof($prdData['updatePrdImg']) > 0){
			
						$media_list = array();
						try{
							$media_list = $client->catalogProductAttributeMediaList($sessionId, $prdExistID, $prdstoreId, 'ID');
						} catch(exception $e){
							
						}
					
					// remove images
					if(sizeof($media_list) > 0){				
						foreach($media_list as $k=>$media_info){
							$remove_img1 = isset($media_info->file)?$media_info->file:"";
							if(strlen($remove_img1) > 0){ 
								try{
									$client->catalogProductAttributeMediaRemove($sessionId, $prdExistID,$remove_img1);
								} catch(exception $e){
								}
								$image_path = $path."/../media/catalog/product".$remove_img1;
								@unlink($image_path);
								
								// Remove Images from DB
								 removeProductImagesFromDB($remove_img1, $prdExistID, $table_prefix,$conn); 
							}
						}
					}			
				}	

				$newCreatedImgs = array();
				$images_updated = 'no';
				foreach($prdData['updatePrdImg'] as $img){
					 $newImg = isset($img['newImg'])?$img['newImg']:"";
					 $oldFile  = isset($img['oldImg'])?$img['oldImg']:"";
					if(strlen($newImg) > 0){
						 $images_updated = 'yes';	
						$img_content = base64_encode(file_get_contents($newImg));	
						if($img_content != "" ){
							
							$image_name = 'product_image_'.$prdExistID.'_'.time().'.jpg';
					        $file = array('content' => $img_content, 'mime' => 'image/jpeg','name'=>$image_name);
							$updatedImgs = array();
							
							if(strlen($oldFile) > 0){
							
								try{
									$client->catalogProductAttributeMediaUpdate($sessionId, $prdExistID, $oldFile , array('file' => $file, 'label' => $prdData['prdName'], 'types' => array('image','small_image','thumbnail'), 'exclude' => 0));
									
									$updatedImgs[] = $oldFile;
								} catch(exception $e){
									//return $returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
								}
							
							}else{
							
								try{
									$newCreatedImg = $client->catalogProductAttributeMediaCreate($sessionId, $prdExistID, array('file' => $file, 'label' => $prdData['prdName'], 'types' => array('image','small_image','thumbnail'), 'exclude'=>0));
								} catch(exception $e){
									//return $returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
								}
							
							}
						}				
					}
				}
				
				
				if($images_updated == 'no'  && $prdType == 'configurable'){
					
						$media_list = array();
						try{
							$media_list = $client->catalogProductAttributeMediaList($sessionId, $prdExistID, $prdstoreId, 'ID');
						} catch(exception $e){
							
						}
					
						// remove images
						if(sizeof($media_list) > 0){				
							foreach($media_list as $k=>$media_info){
								$remove_img1 = isset($media_info->file)?$media_info->file:"";
								if(strlen($remove_img1) > 0){ 
									$image_path = $path."/../media/catalog/product".$remove_img1;
									@unlink($image_path);
									
									// Remove Images from DB
									 removeProductImagesFromDB($remove_img1, $prdExistID, $table_prefix,$conn); 
								}
							}
						}

						$image_url = 'https://extremesports.octopusbridge.com/media/wysiwyg/placeholder.jpg';
						$img_content = base64_encode(file_get_contents($image_url));
						$image_name = 'product_image_'.$prdExistID.'_'.time();
						$file = array('content' => $img_content, 'mime' => 'image/jpeg');
						try{
							$newCreatedImg = $client->catalogProductAttributeMediaCreate($sessionId, $prdExistID, array('file' => $file, 'label' => $prdData['prdName'], 'types' => array('image','small_image','thumbnail'), 'exclude'=>0));
						} catch(exception $e){
							//echo $e->faultstring;exit;
						}						
					
						
					
				}
				
			}
			
			if($prdType != 'configurable' && sizeof($configrable_attributes_array) > 0){	
				
				foreach($configrable_attributes_array as $cofigAttrArr){
					if($prdstoreId == 2){
                         updateConfigrableProductAttribute($prdstoreId, $cofigAttrArr['optionId'], $prdExistID, $cofigAttrArr['attID'], $table_prefix,$conn);    
					   }else{
						  updateConfigrableProductAttribute($prdstoreId, $cofigAttrArr['optionId'], $prdExistID, $cofigAttrArr['attID'], $table_prefix,$conn);  
					   }
						
				}
				
			} 
			
			$info = new stdclass();
			$info->attributes = array('sku', 'name', 'description', 'price', 'url_path');
			$productObj = $client->catalogProductInfo($sessionId, $prdExistID, NULL, $info);	
			$getimageObj = $client->catalogProductAttributeMediaList($sessionId, $prdExistID, NULL, 'ID');
			$hosted_image_url = $getimageObj[0]->url;	
			$product_url = $productObj->url_path;	

			$returnArr[] = array("status" =>'1', "product_id"=>$prdExistID,"product_url" =>$product_url,"hosted_image_url" =>$hosted_image_url , "message"=>'Product '.$prdstoreId.' updated successfully store', "updated_imgs"=>$updatedImgs, "new_ceated_imgs"=>$newCreatedImgs, "OldImage"=>$img->oldImg, "NewImage"=>$img, "Strlength" =>strlen($img->newImg));
		
		} 
	}
	
	$sql ="SET FOREIGN_KEY_CHECKS = 1";
	mysqli_query($conn,$sql);
	
	return $returnArr;
}

function deleteCatelogProduct($prdData, $client, $sessionId, $table_prefix='',$conn){
	$sku = $prdData['sku']; 
	
	$product_id = getProductIdFromSku($sku, $table_prefix,$conn);
	if($product_id <= 0){
		return array("status" =>'0', "message"=>'Product not exist');
	}
	
	try{
		$result = $client->catalogProductDelete($sessionId, $product_id);
	} catch(exception $e){
		return $returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
	}
	$returnArr = array("status" =>'1', "sku"=>$sku, "message"=>'Product deleted successfully');
	return $returnArr;
}

function deleteCatelogProductTemp($prdData, $client, $sessionId, $table_prefix, $product_id,$conn){ 
	$sku = $prdData['sku']; 
	
	$product_id = getProductIdFromSku($sku, $table_prefix,$conn);
	if($product_id <= 0){
		return array("status" =>'0', "message"=>'Product not exist');
	}
	
	try{
		$result = $client->catalogProductDelete($sessionId, $product_id);
	} catch(exception $e){
		//return $returnArr = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
	}
}

function GetProductList($prdData,$client,$sessionId, $table_prefix, $product_id,$conn){
	$sku = $prdData['sku']; 
	
	$product_id = getProductIdFromSku($sku, $table_prefix,$conn);
	if($product_id <= 0){
		return array("status" =>'0', "message"=>'Product not exist');
	}
	
	$info = new stdclass();
	$info->attributes = array('sku', 'name', 'description', 'price', 'url_path');
	$product = $client->catalogProductInfo($sessionId, $product_id, NULL, $info);
	$name = $product->name;
	$description = $product->description;
	$price = $product->price;
	$formatted_price = number_format($price, 2);    
	$image_url = $image[0]->url;
	$product_url = $product->url_path;
           
	$sku = $product->sku;
			
	return  array("product_url" =>$product_url, "message"=>'Product exist');
}

function GetCsvFile($prdData,$client,$sessionId, $table_prefix,$conn){
	$csvContent = file_get_contents($prdData['csvUrl']);
	//print_r($csvContent);exit;
	$csvFile ="productPriceCsv_".strtotime("now").".csv";
	$logFile ="productPriceImportLog_".strtotime("now").".txt";
	if(file_put_contents("productPriceCsv/".$csvFile,$csvContent)){
	 	$fp = file("productPriceCsv/".$csvFile);
	 	$total_row = count($fp);
	 	$sql = "INSERT INTO `".$table_prefix."price_update_log` (`csv_name`,`log_name`,`total_row`,`last_update_row`,`create_time`) VALUES ('".$csvFile."','".$logFile."','".$total_row."','0','".date('Y-m-d h:m:s')."') ";//exit;
	 	$res = mysqli_query($conn,$sql);

	 	$file = 'log/'.$logFile;
		file_put_contents($file,'');
	 	return  array("status" =>'1',"message"=>'https://extremesports.octopusbridge.com/apis/'.$file,"log_download_url"=>'https://extremesports.octopusbridge.com/apis/'.$file);
	}else{
	 	return  array("status" =>'0',"message"=>'Error! Please try again.');
	}
}

function GetStockFileProcess($prdData,$client,$sessionId){
	
		$logFile ="productStockLog/productStockImportLog_".strtotime("now").".txt";
		$fileW = file_put_contents($logFile,'');
		
		$file = fopen($logFile,"w");
		
	 	$total_row = count($fp);
		$prductCsvFile = file_get_contents($prdData['csvUrl']);
		$newFileName = "productStockLog/productStockImport_".strtotime("now").".csv";
		$temp_csv = file_put_contents($newFileName,$prductCsvFile);
		$prductStockFileOpen = fopen($newFileName,"r");
		while (($data = fgetcsv($prductStockFileOpen)) !== FALSE) {
			
				$prdSku = $data[0];
				$prdQuantity = $data[1];
				$is_in_stock = 1;
				
				if($prdQuantity <= 0 ){
					$is_in_stock = 0;
				}
				
				$stockArr = array('qty' => $prdQuantity, 'is_in_stock' => $is_in_stock);
				try{
					$result = $client->catalogInventoryStockItemUpdate($sessionId, $prdSku, $stockArr);
					fwrite($file,date('Y-m-d h:i:s')." Sku : ".$prdSku." product successfully updated \n");
				}catch(exception $e){
					//return $returnArr[] = array("status" =>'0', "sku"=>$sku, "message"=>'Error : '.$e->faultstring);
					fwrite($file,date('Y-m-d h:i:s')." Sku : ".$prdSku." product was not updated 'Error : '".$e->faultstring."'\n");
				}
		
		}
		//unlink($logFile);
		//unlink($newFileName);
		fclose($file);
	 	return  array("status" =>'1',"message"=>'https://extremesports.octopusbridge.com/apis/'.$logFile,"log_download_url"=>'https://extremesports.octopusbridge.com/apis/'.$logFile);
	
}

function callAPIs($par, $table_prefix='',$conn){
	
	$inputJSON = file_get_contents('php://input');
	
	$dataArr = json_decode($inputJSON, TRUE );
	$client = new SoapClient('https://extremesports.octopusbridge.com/index.php/api/v2_soap/?wsdl=1');
	
	/* user details */
	$apiUser = $dataArr['apiUser'];
	$apiKey = $dataArr['apiKey'];
	
	try{
		$sessionId = $client->login($dataArr['apiUser'], $dataArr['apiKey']);
	} catch(exception $e){
		$returnArr = array("status" =>'0', "message"=>'Invalid username or API KEY');
		return $returnArr;
	}
	
	switch($dataArr['action']){
		case 'create':
        	return createCatelogProduct($dataArr['data'], $client, $sessionId, $table_prefix,$conn);
			break;
		case 'update':
        	return createCatelogProduct($dataArr['data'], $client, $sessionId,$conn);
        	break;
        case 'delete':
        	return deleteCatelogProduct($dataArr['data'], $client, $sessionId, $table_prefix,$conn);
           	break;
		case 'get':
			return GetProductList($dataArr['data'],$client,$sessionId, $table_prefix,$conn);
        case 'url':
			return GetCsvFile($dataArr['data'],$client,$sessionId, $table_prefix,$conn);
			break;
		case 'stock':
			return GetStockFileProcess($dataArr['data'],$client,$sessionId);
			break;
		default:
			die("Error: wrong request name ".$dataArr['action']);
			break;
	}
	
	$client->endSession($sessionId);

}

$arr = callAPIs($_REQUEST, $table_prefix,$conn);
echo json_encode($arr);
exit;
?>