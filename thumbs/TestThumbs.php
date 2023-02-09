<?php

namespace app\components\common;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Html;
use app\components\common\Thumbs;
use yii\helpers\Url;

//creates a thumb of image from jpg JPG, PNG
// 1. Set imagePath and set ThumbPath

class TestThumbs extends BaseObject {

	public $thumbPath;

	//Primary Check
	public function create($path) {
		Thumbs::create($path); 
		
		echo "<h2>--Create Path--</h2>";	
		$this->testPath($path);
		
		echo "<h2>--Create JPG--</h2>";
		$jpgPath = $this->testJpg($path);
		
		echo "<h2>--Create webp--</h2>";
		$this->testWebp($path,$jpgPath);

		echo "<h2>--Show Thumb Exist--</h2>";
		$this->testShow($path);

		echo "<h2>--Resize Webp--</h2>";
		$this->testResize("sample/test_thumb.webp");


		/*
		if($width==200) echo "<br> Yes it works";
		else echo "<br> resize fails";
		*/
		
	}






	
	public function testPath($path) {
		if(file_exists(Thumbs::getImagePath($path))) {
			echo 'Path correct';
		}
		else echo 'get image path wrong<br>';		
	}
	
	//test if creates jpg file 
	public function testJpg($path) {
		
		$jpg = str_replace('.png','.jpg',$path);
		$thumbPath = Thumbs::getImagePath($path);
		$jpgPath = Thumbs::getThumbPath($jpg);
		if(file_exists($jpgPath)) {
			echo 'JPG created in Thumbs Folder';
		}
		else {
			echo 'error creating jpg';
		}
		return $jpgPath; 
	}

	public function testWebp($path,$jpgPath) {
		$baseName = pathinfo($jpgPath, PATHINFO_FILENAME);
		$webpPath = Thumbs::getThumbPath($baseName."_thumb.webp");

		if(file_exists($jpgPath)) {
			echo 'Webp created in Thumbs Folder';
		}
		else {
			echo 'error creating Webp';
		}		
	}

	public function testShow($path) {
		$thumbPath = Thumbs::show($path);
	
		echo "The Path {$thumbPath} <br> You should see the image below<br>";
		echo Html::img($thumbPath,['style'=>'width:200px']);
		$this->thumbPath = $thumbPath;
	}

	public function testResize($path) {
		Thumbs::resizeWebp($path,200,true);
		
		$webpPath = Thumbs::getThumbPath($path);

		list($width,$height) = getimagesize($webpPath);
		if($width==200) echo "<br>Yes Test Resize is working";
		else echo "<br> Not working";
	}
}