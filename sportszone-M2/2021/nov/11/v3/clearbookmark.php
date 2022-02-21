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

// sql to delete a record
$sql = "DELETE FROM ui_bookmark WHERE `namespace` IN ('product_listing','sales_order_grid','product_attributes_listing','design_config_listing','customer_listing','customer_listing','cms_block_listing','aw_blog_post_listing','megamenu_menu_listing') AND identifier = 'current' AND user_id = '6'";

if ($conn->query($sql) === TRUE) {
  echo "Record deleted successfully";
} else {
  echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>