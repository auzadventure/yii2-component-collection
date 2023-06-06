<?php
use yii\helpers\Html;

$id = $widget->getID();
$fields = $widget->fields;
$fieldData = $widget->genFieldString(); 

$field = $widget->field;


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
				  <i class='fas fa-minus'></i>
				</button>
			</td>
		</tr>
		<!-- Edit Row !--> 
		<tr v-show="edit_i !== ''">
			<td></td>
			<td><div class='mt-2 ml-2'>Edit <b>{{ edit_field }}</b></div></td>
			 <td>
			 		<input v-model='edit_value' class='form-control'>
			 </td>
			 <td><button @click.prevent='edit' class='btn btn-outline-primary btn-sm mt-1 ml-2'><i class='fas fa-check'></i></button></td>
		</tr>

		<tr>
			<td style='width:25px'></td>
			<?php foreach($fields as $field): ?>
				<td>
					<?= Html::textInput($field,'',
						['placeholder'=>ucfirst($field),
						'v-model'=>$field,
						'class'=>'form-control'
					]);?>


				</td>
			<?php endforeach ?>
			<td>
				<button class='btn btn-outline-primary btn-sm mt-1' @click.prevent='add'>
					<i class='fas fa-plus'></i>
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
  	},

  	//sets variables and shows edit form
  	setEdit(i,field) {
  		this.edit_i = i;
  		this.edit_field = field; 
  		if (this.inputA[this.edit_i][this.edit_field] !== undefined) {
  			this.edit_value = this.inputA[this.edit_i][this.edit_field];
  		}
  	},

  	edit() {
			console.log(this.edit_i)
  		console.log(this.edit_field)
  		console.log(this.edit_value)
  		if (this.edit_i != '' && this.edit_value != '') {
  			this.inputA[this.edit_i][this.edit_field] = this.edit_value; 
  			this.edit_i = '';
  			this.edit_field = '';
  			this.edit_value = '';
  			this.inputStr = JSON.stringify(this.inputA);
  		}
  	}
  }
})
JS;
$this->registerJS($js,$this::POS_END);

?>
