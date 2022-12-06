<?php
use yii\helpers\Html;

$id = $widget->getID();
$fields = $widget->fields;
$fieldData = $widget->genFieldString(); 


?> 
<div id="<?=$id?>"class="">
	<table class='table table-bordered table-sm'>
		<tr v-for='(inputRow,idx) in inputA'>
			<td> {{ idx + 1 }} </td>
			<?= $widget->genTableRow() ?>
			<td> 
				<button class='btn btn-outline-warning btn-sm'
						@click.prevent='remove(idx)'
				> 
				  <i class="bi bi-dash-circle"></i>
				</button>
			</td>
		</tr>
		<tr>
			<td style='width:25px'></td>
			<?php foreach($fields as $field): ?>
				<td>
					<?= Html::textInput($field,'',
						['placeholder'=>ucfirst($field),
						'v-model'=>$field,
						'class'=>'form-control'
					]);
					?>
				</td>
			<?php endforeach ?>
			<td>
				<button class='btn btn-outline-primary btn-sm mt-1' @click.prevent='add'>
					<i class="bi bi-plus-circle"></i>
				</button>
			</td>
		</tr>
	</table>
	
	<?php 
	//The field below 
	if($widget->debug)
		echo Html::activeTextInput($widget->model, $widget->attribute, $widget->options);
	else 
		echo Html::activeHiddenInput($widget->model, $widget->attribute, $widget->options);
	?>

</div>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js',['position'=>$this::POS_HEAD]);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css');

//From Fields
$dataString = $widget->genFieldString();
$objString = $widget->genFieldObjString();
$clearString = $widget->genFieldStrClear();
$field1 = $fields[0];


$js = <<<JS
var vm = new Vue({
  'el': '#{$id}',
  'data' : {{$dataString}},

  methods: {
  	add () {
  		if(this.{$field1} == '') return;
  		var res_ = {{$objString}};
  		this.inputA.push(res_);
  		{$clearString}
  		this.inputStr = JSON.stringify(this.inputA);
  	},

  	remove(idx) {
  		this.inputA.splice(idx,1);
  		this.inputStr = JSON.stringify(this.inputA);
  	}
  }
})
JS;
$this->registerJS($js,$this::POS_END);

//css
$css = "
#{$id} .btn {
  background-color: DodgerBlue;
  border: none;
  color: white;
  padding: 5px 11px 8px;
  font-size: 16px;
  cursor: pointer;
}";
$this->registerCSS($css,['position'=>$this::POS_HEAD]);
?>
