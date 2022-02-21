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


$sql2 = "SELECT catalog_product_entity_varchar.entity_id,catalog_product_entity_varchar.value, catalog_product_entity_media_gallery.value_id, catalog_product_entity_media_gallery_value.position FROM `catalog_product_entity_varchar` INNER JOIN catalog_product_entity_media_gallery ON catalog_product_entity_media_gallery.value = catalog_product_entity_varchar.value INNER JOIN  catalog_product_entity_media_gallery_value ON  catalog_product_entity_media_gallery_value.value_id =  catalog_product_entity_media_gallery.value_id INNER JOIN catalog_product_entity ON catalog_product_entity.entity_id = catalog_product_entity_varchar.entity_id WHERE catalog_product_entity_varchar.attribute_id = 85 AND catalog_product_entity_media_gallery_value.position > 1 AND catalog_product_entity.type_id = 'configurable' AND catalog_product_entity_varchar.entity_id = 129245";

$qu = $conn->query($sql2);
  while($opt_id = $qu->fetch_assoc()) {
    echo $opt_id["entity_id"];
    echo ",";
    echo $opt_id["value"];
    echo ",";
    echo $opt_id["value_id"];
    echo ",";
    echo $opt_id["position"];
    echo "<br>";
  }
?>