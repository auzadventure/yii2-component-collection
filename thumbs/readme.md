
## Step 1
Update the namespace on the top of the file to your app. This uses copy and paste not composer. 

## Test
I have a test class called TestThumbs.php that runs some tests. Put test.png in a folder and it will run some test. Put a png file in sample/test.png.

```
$Thumbs = new \app\components\common\Thumbs;
$Test = new \app\components\common\TestThumbs;
        
$Test->create('sample/test.png');
```


## Create
If PNG, will convert jpg then webp. Otherwise just directly webp. 
Turns transparent background white. 

``` ->create($path)```

## ResizeWebp($path,$width,boolean $isThumb)
Keeps aspect ratio and resizes a webp image based on width. width in pixel and 

## Show 
if web thumb exists show .webp thumb
else show original 

Use it as Html::img($Thumb->show($path)) 
``` ->show($path) ```





