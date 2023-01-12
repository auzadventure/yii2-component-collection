<?php
//yii2 has it own security function 
function randString($len=6) {
	return substr(sha1(time()), 0, $len);
}