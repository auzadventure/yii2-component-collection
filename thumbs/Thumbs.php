<?php

namespace app\components\common;

use Yii;
use yii\base\BaseObject;
use yii\helpers\Url;

//creates a thumb of image from jpg JPG, PNG
// 1. Set imagePath and set ThumbPath

class Thumbs extends BaseObject {

	// The folder path inside /web/ that your images belong to
	CONST ImagePath = 'images';
	
	# If null, thumb will be in same folder as original 
	CONST ThumbFolder = 'thumbs'; 

	// @$path 
	// @return in same directory
	// If PNG->JPG->Web
	public static function create($path,$isThumb=false) {
		if($isThumb) $fullPath = self::getThumbPath($path);
		else $fullPath = self::getImagePath($path);
		
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

			$dir = self::getDir($path);
			imagewebp($webp,"{$dir}/{$baseName}_thumb.webp", 80);
			
			imagedestroy($img);
			imagedestroy($webp);
			
			return true; 		
		}

		elseif($ext == 'png') {
			$imageJpg = self::createJpgFromPng($fullPath,$path);
			self::create($imageJpg,$isThumb=true);
		}
		else Yii::warning("Thumbs->create unable to handle ext {$ext}.");

	}

	// Changers a PNG -> Jpg 
	// Fix the black background with white
	// create {filename.jpg} in same directory
	// return $filename.jpg
	public static function createJpgFromPng($fullPath,$path) {
		//we go png -> jpg to handle the black background
		
		$img=imagecreatefrompng($fullPath);
		$w = imagesx($img);
		$h = imagesy($img);	
		$canvas=imagecreatetruecolor($w,$h);
		imagealphablending($canvas, TRUE);

		$white = imagecolorallocate($canvas,  255, 255, 255);
		imagefilledrectangle($canvas, 0, 0, $w, $h,$white);
	
		imagecopy($canvas,$img,0,0,0,0,$w,$h);
		
		$path = str_replace('.png','.jpg',$path);
		$jpgPath = self::getThumbPath($path);
		
		imagejpeg($canvas,$jpgPath,100);
		imagedestroy($canvas);
		
		
		return $path;		
	}

	//resizes a webp image. keeps aspect ratio
	//Path of thumb is without thumbs/images/ 
	public static function resizeWebp($path,$maxWidth,$isThumb=false) {
		
		$fullPath = ($isThumb) ? self::getThumbPath($path) : self::getImagePath($path);
		
		
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

		if(self::thumbExist($path)) return self::thumbWebPath($path);
		
		else return Yii::getAlias("@web/".self::ImagePath."/{$path}");
	}


	// Return True/False
	// Takes a file checks if the thumbs exist for it
	public static function thumbExist($path) {
		$baseName = pathinfo($path, PATHINFO_FILENAME);
		
		$thumbPath = str_replace($baseName,$baseName."_thumb",$path);
		$thumbPath = substr($thumbPath,0,strlen($thumbPath)-4) . '.webp';
		$thumbFullPath = self::getThumbPath($thumbPath);

		return file_exists($thumbFullPath);
	}



	// return current Imgdir or thumbs dir
	// Creates if does not exist
	public static function getDir($path) {
		
		if(self::ThumbFolder=='') {
			$fullPath = Yii::getAlias('@webroot/' . self::ImagePath .'/' . $path);
			$dir = pathinfo($fullPath, PATHINFO_DIRNAME);
			return $dir;
		}
		
		$folder = self::ThumbFolder.'/'.self::ImagePath.'/';
		$fullPath = Yii::getAlias('@webroot/'.$folder.$path);
		$dir = pathinfo($fullPath, PATHINFO_DIRNAME);
		if(!is_dir($dir)) mkdir($dir,777,true);
		
		return $dir;  
	}

	public static function getImagePath($path) {
		return Yii::getAlias('@webroot/'.self::ImagePath.'/'. $path);		
	}

	public static function getThumbPath($path) {
		$folder = self::getDir($path);
		$folder = self::ThumbFolder.'/'.self::ImagePath.'/';
		return Yii::getAlias('@webroot/'.$folder.$path);
	}

	public static function thumbWebPath($path) {
		$baseName = pathinfo($path, PATHINFO_FILENAME);
		
		$thumbPath = str_replace($baseName,$baseName."_thumb",$path);
		$thumbPath = substr($thumbPath,0,strlen($thumbPath)-4) . '.webp';

		$path = self::ThumbFolder.'/'.self::ImagePath;

		return Yii::getAlias("@web/{$path}/{$thumbPath}");
	}

}