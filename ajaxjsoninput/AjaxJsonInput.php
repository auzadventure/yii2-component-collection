<?php
namespace app\widgets;

use yii\widgets\InputWidget;

/* @fields []

SET IT UP with form model
	->field($model,'bloglinkUrl')
           ->widget(\app\widgets\AjaxJsonInput::className(),
            [
                'fields'=>['title','url'],
                'debug'=> true | false    #shows the input field
            ]
        ) 	
 */
class AjaxJsonInput extends InputWidget {

    public $fields; 
    public $debug = true;

    public function init()
    {
        parent::init();
        if($this->fields == '') die('AjaxJsonInput: You need to set fields');

        $this->options['v-model'] = 'inputStr';
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        $options = $this->options;
        $options['v-model'] = $this->attribute . "Str";
        
        echo $this->render('@app/widgets/views/ajax-json-input',['widget'=>$this]);

        //echo Html::activeTextInput($this->model, $this->attribute, $options);
    }

    //Generates the Str for Vue DATA { 'field' : '' }
    public function genFieldString() {
	$a = array_map([$this,'toKey'],$this->fields);
    	
        $model = $this->model;
        $attribute = $this->attribute;

        //has existing value
        if($model->$attribute != '') {
            $a[] = " 'inputA': {$model->$attribute}";
            $a[] = " 'inputStr' : '{$model->$attribute}'";
        }
        else {
            $a[] = " 'inputA': [] ";
            $a[] = " 'inputStr': ''";
        }
        
    	$a = implode(',',$a);
    	return $a; 
    }
    
    private function toKey($v) {
    	return "'{$v}' : ''";
    }

    // Generates the obj from Fields { 'field' : this.value }
    public function genFieldObjString() {
		$a = array_map([$this,'toObjStr'],$this->fields);
    	$a = implode(',',$a);
    	return $a; 
    }

    private function toObjStr($v) {
    	return "'{$v}': this.{$v}";
    }

    //clears the fields this.field = ''

    public function genFieldStrClear() {
		$a = array_map([$this,'toStrClear'],$this->fields);
    	$a = implode(' ',$a);
    	return $a; 
    }

    private function toStrClear($v) {
    	return "this.{$v} = '';";
    }

    //Get the table row {{field}}
    
    public function genTableRow() {
    	$html = "";
    	foreach ($this->fields as $field) {
    		$html .= "<td class='px-3'>{{inputRow.{$field}}}</td>";
    	}

    	return $html;
    }

}
