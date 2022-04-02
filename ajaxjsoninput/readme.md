## Install
1. Copy to widget folder ```/widgets```

2. Works with ```field```
```
SET IT UP with form model
	->field($model,'bloglinkUrl')
           ->widget(\app\widgets\AjaxJsonInput::className(),
            [
                'fields'=>['title','url'],
                'debug'=> true | false    #shows the input field
            ]
        )
        
```
