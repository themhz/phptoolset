<?php

use phptools\handlers\Database;

include '../config.php';
include '../handlers/Database.php';

//if($_SERVER["REMOTE_ADDR"]=="::1"){
//    $user = "root";
//    $password = "";
//    $dbhost = "localhost";
//    $basename = "kipodomi";
//    $dbh = new PDO("mysql:host=$dbhost;dbname=$basename", $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;",PDO::ATTR_PERSISTENT => true));
//    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//}else{
//    $user = "kipodom_user";
//    $password = "EdUo6E8H5l";
//    $dbhost = "localhost";
//    $basename = "kipodom_dev";
//    $dbh = new PDO("mysql:host=$dbhost;dbname=$basename", $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;",PDO::ATTR_PERSISTENT => true));
//    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//}


$db = Database::getInstance();


//print_r($db->getBaseName());
//die();
//$sql = "SELECT b.virtuemart_product_id as id,
//	b.product_name as name,
//    b.product_desc as description,
//		e.file_url as image,
//        c.product_price as price,
//        a.product_weight as weight,
//        a.product_weight_uom as unit,
//        a.product_in_stock as stock,
//       a.product_width as width,
//       a.product_height as height,
//       a.product_length as length,
//       a.product_sku as sku
//        FROM kipodomi.kipodo_virtuemart_products a
//	inner join kipodo_virtuemart_products_el_gr b on a.virtuemart_product_id = b.virtuemart_product_id
//    inner join kipodo_virtuemart_product_prices c on a.virtuemart_product_id = c.virtuemart_product_id
//    inner join kipodo_virtuemart_product_medias d on a.virtuemart_product_id = d.virtuemart_product_id
//    inner join kipodo_virtuemart_medias e on d.virtuemart_media_id = e.virtuemart_media_id
//
//    ";

$sql = "SELECT b.virtuemart_product_id as id, 
	b.product_name as name,    	
    b.product_desc as description,		
        min(c.product_price) as price,
        a.product_weight as weight,
        a.product_weight_uom as unit,
        a.product_in_stock as stock,
       a.product_width as width,
       a.product_height as height,
       a.product_length as length,
       a.product_sku as sku,
       c.override
        FROM kipodomi.kipodo_virtuemart_products a
	inner join kipodo_virtuemart_products_el_gr b on a.virtuemart_product_id = b.virtuemart_product_id
    inner join kipodo_virtuemart_product_prices c on a.virtuemart_product_id = c.virtuemart_product_id     
    where (c.override = 0 or c.override is null )    
    group by c.virtuemart_product_id
    order by b.virtuemart_product_id
    ";

$db->setBaseName("kipodomi");
$sth = $db->dbh->prepare($sql);
$sth->execute();



$results = $sth->fetchAll(\PDO::FETCH_OBJ);

//echo "<table border='1'>";
$counter = 1;
foreach($results as $item){
//    echo "<tr>";
//    echo "<td>$counter</td>";
//    echo "<td>$item->id</td>";
//    echo "<td>$item->name</td>";
//    echo "<td><img src='https://kipodomi-tools.gr/$item->image' style='width:200px;'></td>";
//    echo "<td>$item->price</td>";
//    echo "<td>".$item->weight." ".$item->unit."</td>";
//
//    echo "</tr>";
    //echo "inserting $item->name, $item->description, $item->price, $item->stock, 1, $item->image, $item->width, $item->height, $item->length, $item->weight, 0";
//    $image = explode("/", $item->image);
//    $pos = count($image);
//    $image = $image[$pos-1];
    //echo $image;
    insertProductsToDb($db, $item->id, $item->sku, $item->name, $item->description, $item->price, $item->stock, 1, $item->width, $item->height, $item->length, $item->weight, 0);
    $counter++;
    //
}
//echo "</table>";

//insert products in custom database
function insertProductsToDb($db, $id, $sku, $name, $description, $price, $stock, $enabled,  $width, $height, $length, $weight, $deleted){
    //$db = Database::getInstance();
    $db->setBaseName("mywebshop");
    $sql = "insert into products (id, sku, name, description, price, stock, enabled,  width, height, length, weight, deleted, regdate)  
            values (:id, :sku, :name, :description, :price, :stock, :enabled, :width, :height, :length, :weight, :deleted, :regdate)";
    $date = new \DateTime();
    $sth = $db->dbh->prepare($sql);
    $sth->execute(array(':id' => $id,
                        ':sku' => $sku,
                        ':name' => $name,
                        ':description' => $description,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':enabled' => $enabled,
                        ':width' => $width,
                        ':height' => $height,
                        ':length' => $length,
                        ':weight' => $weight,
                        ':deleted' => $deleted,
                        ':regdate' => $date->format('Y-m-d-H')

    ));


}