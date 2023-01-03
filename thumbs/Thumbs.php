<?php

namespace app\components\common;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Url;

//creates a thumb of image from jpg JPG, PNG

class Thumbs extends BaseObject {

	CONST ImagePath = 'images';
	CONST ThumbPath = 'thumbs';

	// @$path 
	// @return in same directory
	public static function create($path) {
		$fullPath = Yii::getAlias('@webroot/' . self::ImagePath .'/' . $path);
		$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
		$baseName = pathinfo($fullPath, PATHINFO_FILENAME);
		$dir = pathinfo($fullPath, PATHINFO_DIRNAME);

		//check if jpg or png
		if($ext =='jpg' || $ext =='JPG') $img=imagecreatefromjpeg($fullPath);
		elseif($ext == 'png') $img=imagecreatefrompng($fullPath);
		else Yii::warning("Thumbs->create unable to handle ext {$ext}.");

		$w = imagesx($img);
		$h = imagesy($img);			
		$webp=imagecreatetruecolor($w,$h);
		
		imagecopy($webp,$img,0,0,0,0,$w,$h);
		imagewebp($webp,"{$dir}/{$baseName}_thumb.webp", 80);
		
		imagedestroy($img);
		imagedestroy($webp);
		return true; 
	}

	//if thumb exists show webp thumb
	//else show original 
	public static function show($path) {

		$fullPath = Yii::getAlias('@webroot/' . self::ImagePath .'/' . $path);
		$ext = pathinfo($fullPath, PATHINFO_EXTENSION);
		$baseName = pathinfo($fullPath, PATHINFO_FILENAME);
		$dir = pathinfo($fullPath, PATHINFO_DIRNAME);	
		$fileName = pathinfo($fullPath, PATHINFO_FILENAME);

		if(self::thumbExist($path)) {
			//get path before filename 
			$dir = str_replace($fileName,'',$path);
			$dir = str_replace('/.'.$ext,'',$dir);
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