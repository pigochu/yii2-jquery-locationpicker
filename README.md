jquery location picker widget for yii2
======================================

The widget implement [jquery-locationpicker-plugin
](https://github.com/Logicify/jquery-locationpicker-plugin)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

~~~
php composer.phar require pigochu/yii2-jquery-locationpicker ">=0.1.0"
~~~

or add

~~~
"pigochu/yii2-jquery-locationpicker": ">=0.1.0"
~~~

to the require section of your `composer.json` file.


Basic Usage for Test
--------------------

~~~php
<?= \pigolab\locationpicker\LocationPickerWidget::widget(); ?>
~~~

Binding UI with the widget
--------------------------

This sample is transformed via [http://logicify.github.io/jquery-locationpicker-plugin/#usage](http://logicify.github.io/jquery-locationpicker-plugin/#usage)

~~~php
<?php
use yii\web\JsExpression;
?>

Location: <input type="text" id="us2-address" style="width: 200px"/>
<br>
Radius: <input type="text" id="us2-radius"/>
<br>
Lat.: <input type="text" id="us2-lat"/>
<br>
Long.: <input type="text" id="us2-lon"/>
<br>


<?php
    echo \pigolab\locationpicker\LocationPickerWidget::widget([
       'key' => 'abcabcabcabc ...',	// optional , Your can also put your google map api key
       'options' => [
            'style' => 'width: 100%; height: 400px', // map canvas width and height
        ] ,
        'clientOptions' => [
            'location' => [
                'latitude'  => 46.15242437752303 ,
                'longitude' => 2.7470703125,
            ],
            'radius'    => 300,
            'inputBinding' => [
                'latitudeInput'     => new JsExpression("$('#us2-lat')"),
                'longitudeInput'    => new JsExpression("$('#us2-lon')"),
                'radiusInput'       => new JsExpression("$('#us2-radius')"),
                'locationNameInput' => new JsExpression("$('#us2-address')")
            ]
        ]        
    ]);
?>

~~~

CoordinatesPicker
-----------------

CoordinatesPicker let you get coordinates in ActiveForm , In addition I implemented some features not in original jquery-locationpicker-plugin : 

 - enable/disable search box , search box will overlay on map
 - enable/disable map type control

 

Example :

~~~php
<?php
	echo $form->field($model, 'coordinates')->widget('\pigolab\locationpicker\CoordinatesPicker' , [
		'key' => 'abcabcabc...' ,	// optional , Your can also put your google map api key
		'valueTemplate' => '{latitude},{longitude}' , // Optional , this is default result format
		'options' => [
			'style' => 'width: 100%; height: 400px',  // map canvas width and height
		] ,
		'enableSearchBox => true , // Optional , default is true
		'searchBoxOptions' => [ // searchBox html attributes
			'style' => 'width: 300px;', // Optional , default width and height defined in css coordinates-picker.css
		],
		'enableMapTypeControl' => true , // Optional , default is true
		'clientOptions' => [
			'radius'    => 300,
		]
	]);
?>
~~~

if you var_dump($model->coordinates) , You will get result like : 25.023308046766083,121.46041916878664 , so you can get latitude and longitude via explode(',' , $model->coordinates) .  
