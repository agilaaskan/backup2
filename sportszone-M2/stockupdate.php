<?php
$servername = "localhost";
$username = "aquarium_m2newlive";
$password = 'nRf?K#-vstN!';
$dbname = "aquarium_m2newlive";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

      $newid = "UPDATE cataloginventory_stock_item SET is_in_stock=1 WHERE backorders IN (1,2) AND is_in_stock = 0";
        // print_r($result);
        if ($conn->query($newid) === TRUE) {
            echo "yes";
          }
       $sql = "SELECT product_id FROM cataloginventory_stock_item WHERE backorders IN (1,2)";
          $result = $conn->query($sql);
          if ($result && $result->num_rows > 0) {
              while($row1 = $result->fetch_assoc()) {
                $pro= $row1['product_id'];
                $sql1="UPDATE cataloginventory_stock_status SET stock_status=1 WHERE product_id = '$pro'";
                if ($conn->query($sql1) === TRUE) {
                  echo "yes";
                }
              }    
          }
      $conn->close();
?>