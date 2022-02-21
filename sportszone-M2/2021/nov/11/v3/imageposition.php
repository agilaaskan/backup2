<?php
$servername = "localhost";
$username = "m2sportszonecana_newv3";
$password = 'Web$peed@123';
$dbname = "m2sportszonecana_newv3";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
echo "<br>";


$sql2 = "SELECT catalog_product_entity_int.entity_id, catalog_product_entity_int.attribute_id, catalog_product_entity.type_id FROM catalog_product_entity INNER JOIN catalog_product_entity_int ON catalog_product_entity.entity_id = catalog_product_entity_int.entity_id WHERE catalog_product_entity_int.attribute_id = 96 AND catalog_product_entity_int.value =1 AND catalog_product_entity.type_id = 'simple'";

$qu = $conn->query($sql2);
  while($opt_id = $qu->fetch_assoc()) {
    $sid = $opt_id['entity_id'];
    $sql3 = "SELECT * FROM `catalog_product_entity_int` WHERE `attribute_id` = 102 AND value = 1 AND entity_id IN ($sid)";
    $qu1 = $conn->query($sql3);
    while($opt_id1 = $qu1->fetch_assoc()) {
      $sid1 = $opt_id1['entity_id'];
      $sql4 = "SELECT *  FROM `catalog_product_entity_varchar` WHERE `entity_id` = $sid1 AND attribute_id = 87;";
      $qu2 = $conn->query($sql4);
      while($opt_id2 = $qu2->fetch_assoc()) {
        $simg = $opt_id2['value'];
        $sid3 = $opt_id2['entity_id'];
        if ($simg != 'no_selection' ) {
          // $sql5 ="SELECT catalog_product_entity_media_gallery_value.position,catalog_product_entity_media_gallery.value_id,catalog_product_entity_media_gallery.value, catalog_product_entity_media_gallery_value.entity_id FROM catalog_product_entity_media_gallery_value  INNER JOIN catalog_product_entity_media_gallery ON catalog_product_entity_media_gallery_value.value_id = catalog_product_entity_media_gallery.value_id WHERE catalog_product_entity_media_gallery_value.entity_id = $sid3 AND catalog_product_entity_media_gallery.value = '$simg' AND catalog_product_entity_media_gallery_value.position NOT IN (0,1)";
          // $qu5 = $conn->query($sql5);
          // while($opt_id5 = $qu5->fetch_assoc()) {
          //   $simg5 = $opt_id5['position'].",".$opt_id5['value_id'].",".$opt_id5['value'].",".$opt_id5['entity_id'];
          //   echo $simg5."<br>";
          // }
          $sql5 ="SELECT catalog_product_entity_media_gallery_value.position,catalog_product_entity_media_gallery.value_id,catalog_product_entity_media_gallery.value, catalog_product_entity_media_gallery_value.entity_id FROM catalog_product_entity_media_gallery_value  INNER JOIN catalog_product_entity_media_gallery ON catalog_product_entity_media_gallery_value.value_id = catalog_product_entity_media_gallery.value_id WHERE catalog_product_entity_media_gallery_value.entity_id = $sid3 AND catalog_product_entity_media_gallery_value.position IN (0)";
          $qu5 = $conn->query($sql5);
          while($opt_id5 = $qu5->fetch_assoc()) {
            $simg5 = $opt_id5['position'].",".$opt_id5['value_id'].",".$opt_id5['value'].",".$opt_id5['entity_id'];
            echo $simg5."<br>";
          }
        }
      }
    }
  }
?>