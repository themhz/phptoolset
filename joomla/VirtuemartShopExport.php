<?php

namespace phptools\joomla;
use phptools\handlers\Database;
use phptools\handlers\LogHandler;


//In order to transfer products from virtuemart, we need to copy:
//1.Products, 2.Product Images, 3.Product prices, 4.Categories, 5.Product Categories
//This class gets the products, images, prices, Categories and product categories and transfers them
//to a new database.
//It uses PDO to do the transfers and works with mysql


class VirtuemartShopExport
{

    public $sourceDb;
    public $destinationDb;
    public $db;

    public function __construct($sourceDb, $destinationDb)
    {
        $this->db = Database::getInstance();
        $this->sourceDb = $sourceDb;
        $this->destinationDb = $destinationDb;

    }

    public function getSrouceDbCategories(){
        $sql = "
            SELECT a.category_child_id as id, a.category_parent_id as parent_id , b.category_name as `name`
            FROM kipodomi.kipodo_virtuemart_category_categories a
            inner join kipodomi.kipodo_virtuemart_categories_el_gr b on a.category_child_id = b.virtuemart_category_id
            order by a.category_child_id;
            ";
        $this->db->setBaseName($this->sourceDb);
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_OBJ);

        return $results;
    }

    public function getSrouceDbProductCategories(){
        $sql = "
                SELECT virtuemart_product_id as product_id, 
                       virtuemart_category_id as category_id,                       
                       ordering 
                FROM kipodomi.kipodo_virtuemart_product_categories; 
            ";
        $this->db->setBaseName($this->sourceDb);
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_OBJ);

        return $results;
    }

    public function getSourceDbProducts(){
        $sql = "SELECT distinct b.virtuemart_product_id as id, 
                b.product_name as name,    	
                b.product_desc as description,		                    
                    a.product_weight as weight,
                    a.product_weight_uom as unit,
                    a.product_in_stock as stock,
                   a.product_width as width,
                   a.product_height as height,
                   a.product_length as length,
                   a.product_sku as sku, 
                   min(c.product_price) as price
                    FROM kipodomi.kipodo_virtuemart_products a
                inner join kipodo_virtuemart_products_el_gr b on a.virtuemart_product_id = b.virtuemart_product_id
                left join kipodo_virtuemart_product_prices c on a.virtuemart_product_id = c.virtuemart_product_id                                                                 
                group by b.virtuemart_product_id
                order by b.virtuemart_product_id asc                                
        ";
        $this->db->setBaseName($this->sourceDb);
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_OBJ);

        return $results;
    }

    public function getSourceDbImages(){
        $sql = "SELECT b.virtuemart_product_id as product_id,
                b.product_name as name,
                b.product_desc as description,
                    e.file_url as image,
                    c.product_price as price,
                    a.product_weight as weight,
                    a.product_weight_uom as unit,
                    a.product_in_stock as stock,
                   a.product_width as width,
                   a.product_height as height,
                   a.product_length as length,
                   a.product_sku as sku
                    FROM kipodomi.kipodo_virtuemart_products a
                inner join kipodo_virtuemart_products_el_gr b on a.virtuemart_product_id = b.virtuemart_product_id
                inner join kipodo_virtuemart_product_prices c on a.virtuemart_product_id = c.virtuemart_product_id
                inner join kipodo_virtuemart_product_medias d on a.virtuemart_product_id = d.virtuemart_product_id
                inner join kipodo_virtuemart_medias e on d.virtuemart_media_id = e.virtuemart_media_id
            
                ";

        $this->db->setBaseName($this->sourceDb);
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
        $results = $sth->fetchAll(\PDO::FETCH_OBJ);
        return $results;
    }


    public function insertDestinationProductCategories($results){

        foreach($results as $item){
            $date = new \DateTime();
            $sql = "insert into product_categories (product_id, category_id, published, ordering, regdate)  
            values (:product_id, :category_id, :published, :ordering, :regdate)";

            $this->db->setBaseName($this->destinationDb);
            $sth = $this->db->dbh->prepare($sql);
            try{
                $sth->execute(array(':product_id' => $item->product_id,
                    ':category_id' => $item->category_id,
                    ':published' => 1,
                    ':ordering' => 1,
                    ':regdate' => $date->format('Y-m-d-H')
                ));
            }catch(\Exception $e){
                echo 'Trying to insert '.$item->product_id.' from insertDestinationProductCategories, but Caught exception: '."\n";

            }
        }
    }


    public function insertDestinationCategories($results){

        foreach($results as $item){
            $sql = "insert into categories (id, parent_id, name)  
            values (:id, :parent_id, :name)";

            $this->db->setBaseName($this->destinationDb);
            $sth = $this->db->dbh->prepare($sql);
            try{
                $sth->execute(array(':id' => $item->id,
                    ':parent_id' => $item->parent_id,
                    ':name' => $item->name
                ));
            }catch(\Exception $e){
                echo 'Trying to insert '.$item->id.' from insertDestinationCategories, but Caught exception: '."\n";

            }
        }
    }





    public function insertDestinationProducts($results){
        $counter = 1;

        foreach($results as $item){
            $date = new \DateTime();

            $sql = "insert into products (id, sku, name, description, price, stock, enabled,  width, height, length, weight, deleted, regdate)  
            values (:id, :sku, :name, :description, :price, :stock, :enabled, :width, :height, :length, :weight, :deleted, :regdate)";


            $this->db->setBaseName($this->destinationDb);
            $sth = $this->db->dbh->prepare($sql);

            try{
                $sth->execute(array(':id' => $item->id,
                    ':sku' => $item->sku,
                    ':name' => $item->name,
                    ':description' => $item->description,
                    ':price' => $item->price,
                    ':stock' => $item->stock,
                    ':enabled' => 1,
                    ':width' => $item->width,
                    ':height' => $item->height,
                    ':length' => $item->length,
                    ':weight' => $item->weight,
                    ':deleted' => 0,
                    ':regdate' => $date->format('Y-m-d-H')

                ));
            }catch(\Exception $e){
                echo 'Trying to insert '.$item->id.' from insertDestinationProducts, but Caught exception: '. "\n";

            }



            $counter++;
        }
    }

    public function insertDestinationProductImages($results){
        $counter = 1;
        foreach($results as $item){
            $image = explode("/", $item->image);
            $pos = count($image);
            $image = $image[$pos-1];

            $sql = "insert into product_images (product_id, image, `order`, regdate)  
            values (:product_id, :image, :order, :regdate)";
            $date = new \DateTime();

            $this->db->setBaseName($this->destinationDb);
            $sth = $this->db->dbh->prepare($sql);

            try{
                $sth->execute(array(':product_id' => $item->product_id,
                    ':image' => $image,
                    ':order' => 1,
                    ':regdate' => $date->format('Y-m-d-H')
                ));
            }catch(\Exception $e){
                echo 'Trying to insert '.$item->product_id." from insertDestinationProductImages, but Caught exception: \n";

            }



            $counter++;
        }
    }



    public function deleteSourceProducts(){
        $sql = "delete from products";
        $this->db->setBaseName($this->destinationDb);
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
    }

    public function deleteSourceProductCategories(){
        $sql = "delete from product_categories";
        $this->db->setBaseName($this->destinationDb);
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
    }

    public function deleteSourceProductImages(){
        $sql = "delete from product_images";
        $this->db->setBaseName($this->destinationDb);
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
    }

    public function deleteSourceCategories(){
        $sql = "delete from categories";
        $this->db->setBaseName($this->destinationDb);
        $sth = $this->db->dbh->prepare($sql);
        $sth->execute();
    }

    public function copySourceDbProductsToDestinationDb(){
        $this->deleteSourceProductImages();
        $this->deleteSourceProductCategories();
        $this->deleteSourceProducts();
        $this->deleteSourceCategories();

        $this->insertDestinationProducts($this->getSourceDbProducts());
        $this->insertDestinationProductImages($this->getSourceDbImages());
        $this->insertDestinationCategories($this->getSrouceDbCategories());
        $this->insertDestinationProductCategories($this->getSrouceDbProductCategories());

    }

}