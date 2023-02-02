<?php

namespace app\components\common;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Url;

//creates a thumb of image from jpg JPG, PNG


class Thumbs extends BaseObject {

	// The folder path inside /web/ that your images belong to
	CONST ImagePath = 'images';

	// @$path 
	// @return in same directory
	// If PNG->JPG->Web
	public static function create($path) {
		$fullPath = Yii::getAlias('@webroot/' . self::ImagePath .'/' . $path);
		$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
		$baseName = pathinfo($fullPath, PATHINFO_FILENAME);
		$dir = pathinfo($fullPath, PATHINFO_DIRNAME);

		//check if jpg or png
		if($ext =='jpg' || $ext =='JPG') {
			$img=imagecreatefromjpeg($fullPath);
			$w = imagesx($img);
			$h = imagesy($img);			
			$webp=imagecreatetruecolor($w,$h);
			
			imagecopy($webp,$img,0,0,0,0,$w,$h);
			imagewebp($webp,"{$dir}/{$baseName}_thumb.webp", 80);
			
			imagedestroy($img);
			imagedestroy($webp);
			
			return true; 		
		}

		elseif($ext == 'png') {
			$imageJpg = self::createJpgFromPng($fullPath);
			self::create($imageJpg);
		}
		else Yii::warning("Thumbs->create unable to handle ext {$ext}.");

	}

	// Changers a PNG -> Jpg 
	// Fix the black background with white
	// create {filename.jpg} in same directory
	// return $filename.jpg
	public static function createJpgFromPng($fullPath) {
		//we go png -> jpg to handle the black background
		

		$img=imagecreatefrompng($fullPath);
		$w = imagesx($img);
		$h = imagesy($img);	
		$canvas=imagecreatetruecolor($w,$h);
		imagealphablending($canvas, TRUE);

		$white = imagecolorallocate($canvas,  255, 255, 255);
		imagefilledrectangle($canvas, 0, 0, $w, $h,$white);
	
		imagecopy($canvas,$img,0,0,0,0,$w,$h);
		
		$baseName = pathinfo($fullPath, PATHINFO_FILENAME);
		$dir = pathinfo($fullPath, PATHINFO_DIRNAME);

		imagejpeg($canvas,"{$dir}/{$baseName}.jpg",100);
		imagedestroy($canvas);
		
		return "{$baseName}.jpg";		
	}

	//resizes a webp image. keeps aspect ratio 
	public static function resizeWebp($path,$maxWidth) {
		$fullPath = Yii::getAlias('@webroot/' . self::ImagePath .'/' . $path);
		list($w, $h) = getimagesize($fullPath);
		if($w < $maxWidth) return;

		$ratio = round($h/$w,2);
		$w_ = $maxWidth;
		$y_ = $w_ * $ratio; 
		

		$canvas = imagecreatetruecolor($w_, $y_);
		$img = imagecreatefromwebp($fullPath);
		imagecopyresampled($canvas, $img, 0, 0, 0, 0, $w_, $y_, $w, $h);
		imagewebp($canvas,$fullPath, 80);
		imagedestroy($canvas);
		
	}


	//if web thumb exists show .webp thumb
	//else show original 
	//Use it as Html::img($Thumb->show($path)) 
	public static function show($path) {

		$fullPath = Yii::getAlias('@webroot/' . self::ImagePath .'/' . $path);
		$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
		$baseName = pathinfo($fullPath, PATHINFO_FILENAME);
		$dir = pathinfo($fullPath, PATHINFO_DIRNAME);	
		$fileName = pathinfo($fullPath, PATHINFO_FILENAME);

		if(self::thumbExist($path)) {
			//get path before filename 
			
			$dir = str_replace($fileName.".".$ext,'',$path);
			$fileName = "{$baseName}_thumb.webp";

			return Yii::getAlias("@web/".self::ImagePath."/{$dir}/{$fileName}"); 
		}
		else return Yii::getAlias("@web/".self::ImagePath.'/{$path}');
	}


	// Return True/False 
	public static function thumbExist($path) {
		$fullPath = Yii::getAlias('@webroot/' . self::ImagePath .'/' . $path);
		$baseName = pathinfo($fullPath, PATHINFO_FILENAME);
		$dir = pathinfo($fullPath, PATHINFO_DIRNAME);	

		$fullPathThumb = "{$dir}/{$baseName}_thumb.webp";	
		return file_exists($fullPathThumb);	
	}
}