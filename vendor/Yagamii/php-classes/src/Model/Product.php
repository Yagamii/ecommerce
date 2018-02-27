<?php

namespace Yagamii\Model;

use \Yagamii\Model;
use \Yagamii\DB\Sql;
use \Yagamii\Mailer;

class Product extends Model {

	protected $fields = [
		"idproduct", "desproduct", "vlprice", "vlwidth", "vlheight", "vllength", "vlweight", "desurl", "desphoto"
	];

	public static function listAll(){

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");

	}

	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
				":idproduct"=>$this->getidproduct(),
				":desproduct"=>$this->getdesproduct(),
        ":vlprice"=>$this->getvlprice(),
        ":vlwidth"=>$this->getvlwidth(),
        ":vlheight"=>$this->getvlheight(),
        ":vllength"=>$this->getvllength(),
        ":vlweight"=>$this->getvlweight(),
        ":desurl"=>$this->getdesurl()
));

		$this->setData($results[0]);

	}

	public function get($idproduct){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
				":idproduct"=>$idproduct
		]);

		$this->setData($results[0]);

	}

	public function delete(){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
				":idproduct"=>$this->getidproduct()
		]);

		Category::updateFile();

	}

	public static function updateFile(){

		$categories = Category::listAll();

		$html = [];

		foreach($categories as $row){
				array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');
		}

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR. "views". DIRECTORY_SEPARATOR. "categories-menu.html", implode('', $html));

	}

	public function checkPhoto(){

		if(file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
		 "res". DIRECTORY_SEPARATOR .
		 "site" . DIRECTORY_SEPARATOR .
		 "img" . DIRECTORY_SEPARATOR .
		 "products" . DIRECTORY_SEPARATOR .
		 $this->getidproduct() . ".jpg"
	 )){

		 $url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";

	 }else{

		 $url = "/res/site/img/product.jpg";

	 }

	 return $this->setdesphoto($url);

	}

	public function getValues(){

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;
	}

	public function setPhoto($file){

		$extension = explode('.', $file['name']);
		$extension = end($extension);

		switch($extension){

			case"jpg":
			case"jpeg":

				$image = imagecreatefromjpeg($file["tmp_name"]);

			break;
			case "gif":

				$image = imagecreatefromgif($file["tmp_name"]);

			break;
			case "png":

				$image = imagcreatefrompng($file["tmp_name"]);

			break;
		}

		$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR .
		 "res". DIRECTORY_SEPARATOR .
		 "site" . DIRECTORY_SEPARATOR .
		 "img" . DIRECTORY_SEPARATOR .
		 "products" . DIRECTORY_SEPARATOR .
		 $this->getidproduct() . ".jpg";

		imagejpeg($image, $dist);

		imagedestroy($image);

		$this->checkPhoto();

	}

}

 ?>
