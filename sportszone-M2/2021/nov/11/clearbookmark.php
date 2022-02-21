<?php
$servername = "localhost";
$username = "us8kjbqpy03on";
$password = 'Web$peed@123';
$dbname = "dbo7tcn5duo8um";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// sql to delete a record
$sql = "DELETE FROM ui_bookmark WHERE `namespace` IN ('product_listing','sales_order_grid','product_attributes_listing','design_config_listing','customer_listing','customer_listing','cms_block_listing','aw_blog_post_listing','megamenu_menu_listing') AND identifier = 'current'";

if ($conn->query($sql) === TRUE) {
  echo "Record deleted successfully";
} else {
  echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>