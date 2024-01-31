## Install
1. Copy to widget folder ```/widgets```
2. Add the namespace reference. Change the path on

```echo $this->render('@app/widgets/views/ajax-json-input',['widget'=>$this]);```
to where you put the view file

3. configure the fields 

4. Works with ```field```
SET IT UP with form model
```

	->field($model,'bloglinkUrl')
           ->widget(\app\widgets\AjaxJsonInput::className(),
            [
                'fields'=>['title','url'],
                'debug'=> true | false    #shows the input field
            ]
        )
        
```

5. You can update but clicking the 'edit button'
6. Need Bootstrap 4.0 for the icons
