<?php
$servername = "localhost";
$username = "m2sportszonecana_newm2v2";
$password = 'w?Z(qBe7*B~B';
$dbname = "m2sportszonecana_newm2v2";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// $file = fopen("old.csv","r");
// while($row = fgetcsv($file)) {

//   print_r ($row[1])
// $sql2 = "SELECT option_id FROM `eav_attribute_option_value` WHERE `value` IN ('$row[1]')";




$file = fopen('gold.csv', "r");
$counter = 0;

// while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
 
  $i =0;
  // try {
  while($column = fgetcsv($file)) {
    echo "Start";
   $i++;
  //  $cat6 = [];
  //  $cat = [];
  //  $cat1 = [];
if ($column > 0) {

$good_ids= $column[1];
$sql2 = "SELECT option_id  FROM `eav_attribute_option_value` WHERE `value` IN ($good_ids)";

$qu = $conn->query($sql2);
$or_id = [];
  while($opt_id = $qu->fetch_assoc()) {
    $or_id[]=$opt_id["option_id"];
  }
  $category_ids=array();
  $category_ids= $or_id;

  $cat = implode(',', $category_ids);
// print_r ($cat);

if ($cat != ""){
$sql3 = "SELECT option_id FROM `eav_attribute_option` WHERE `option_id` IN ($cat) AND `attribute_id` = 410";
$qu1 = $conn->query($sql3);
$or_id1 = [];
  while($opt_id1 = $qu1->fetch_assoc()) {
    $or_id1[]=$opt_id1["option_id"];
  }
  $category_ids1=array();
  $category_ids1= $or_id1;

  $cat1 = implode(',', $category_ids1);
  echo "<br>";
  echo "ID LIST :";
  
print_r ($cat1);


if ($cat1 != ""){
$sql4= "SELECT value, COUNT(value) AS `cnt`
    FROM catalog_product_entity_int WHERE attribute_id =410 AND value IN ($cat1)
    GROUP BY value
    ORDER BY `cnt` DESC
    LIMIT 1";

$qu3 = $conn->query($sql4);
$or_id3 = [];
while($opt_id3 = $qu3->fetch_assoc()) {
  $or_id3[]=$opt_id3["value"];
}
$category_ids3= $or_id3[0];
echo "<br>";
echo "ORG ID :";
print_r ($category_ids3);

$upd = "UPDATE `catalog_product_entity_int` set value = $category_ids3 WHERE `attribute_id` = 410 AND `value` IN ($cat1)";
$qu4 = $conn->query($upd);

 
$key = array_search($category_ids3, $category_ids1);
unset($category_ids1[$key]);

echo "<br>";

$cat6 = implode(',', $category_ids1);
// print_r ($cat6);

$del34 = "DELETE  FROM `eav_attribute_option` WHERE `option_id` IN ($cat6) AND `attribute_id` = 410";
$qu5 = $conn->query($del34);


$finn_upda = "UPDATE `eav_attribute_option_value` SET value = '$column[0]' WHERE `option_id` = $category_ids3";
$qu65 = $conn->query($finn_upda);
echo "<br>";
print_r ($column[0]);
echo "<br>";
echo "Done";
echo "<br>";
}
}
}
}
  // }
  // catch(Exception $e) {
  //   echo $e->getMessage();
  //  }
  echo "<br>";
print_r ($i);
?>

