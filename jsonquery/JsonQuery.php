<?php
/* handles the user favourites */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Expression;

// use with ->andWhere(JsonQuery::objSearch)
class JsonQuery extends Model {

	
	//Search 1 json object {'key': 'value'}
	public static function objSearch($attr,$key,$value) {
		$q = "JSON_UNQUOTE(JSON_EXTRACT($attr,'\$.{$key}')) = '$value'";
		return new Expression($q);
	}

	//Search any Array [{'key':value}] for any key with value
	//Exact Match
	public static function aSearchValue($attr,$value) {
		$q = "JSON_SEARCH(results,'one','$value',null) IS NOT NULL";
		return $q; 
	}


	// Search a specific {key:value} in an [obj,obj]
	// Exact Match 
	public static function aSearchKeyValue($attr,$key,$value) {
		$q = "JSON_SEARCH(results,'one','$value',null, $[*].'$key') IS NOT NULL";
		return $q; 
	}
}