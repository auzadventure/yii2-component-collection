<?php
namespace app\widgets;

use yii\widgets\InputWidget;
# uses bootstrap 4.0

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
        if($this->fields == '') die('you need to set fields');

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
    // edit_i edit_key
    public function genFieldString() {
        $a = array_map([$this,'toKey'],$this->fields);
        
        $model = $this->model;
        $attribute = $this->attribute;
        $a[] = " 'edit_i': ''";
        $a[] = " 'edit_key': ''";
        $a[] = " 'edit_value': ''";

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

    //IDX from vue row loop
    public function editFieldTag($field) {
        $s = "<a href='#' @click=\"setEdit(idx,'{$field}')\"><i class=\"fa fa-edit small\"></i></a>";
        return $s; 
    }


    //Get the table row {{field}}
    
    public function genTableRow() {
        $html = "";
        foreach ($this->fields as $field) {

            $editFieldTag = $this->editFieldTag($field);
            $html .= "<td class='px-3'>{{inputRow.{$field}}} {$editFieldTag}</td>";
        }

        return $html;
    }

}