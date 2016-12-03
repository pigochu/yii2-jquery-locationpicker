jquery location picker widget for yii2
======================================

The widget implement [jquery-locationpicker-plugin
](https://github.com/Logicify/jquery-locationpicker-plugin)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

~~~
php composer.phar require pigochu/yii2-jquery-locationpicker ">=0.2.0"
~~~

or add

~~~
"pigochu/yii2-jquery-locationpicker": ">=0.2.0"
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
       'key' => 'abcabcabcabc ...',	// require , Put your google map api key
       'options' => [
            'style' => 'width: 100%; height: 400px', // map canvas width and height
        ] ,
        'clientOptions' => [
            'location' => [
                'latitude'  => 46.15242437752303 ,
                'longitude' => 2.7470703125,
            ],
            'radius'    => 300,
            'addressFormat' => 'street_number',
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
 - enable/disable all googlemap's control


![](https://i.imgur.com/SyNOXXL.png)


Example :

~~~php
<?php
	echo $form->field($model, 'coordinates')->widget('\pigolab\locationpicker\CoordinatesPicker' , [
		'key' => 'abcabcabc...' ,	// require , Put your google map api key
		'valueTemplate' => '{latitude},{longitude}' , // Optional , this is default result format
		'options' => [
			'style' => 'width: 100%; height: 400px',  // map canvas width and height
		] ,
		'enableSearchBox' => true , // Optional , default is true
		'searchBoxOptions' => [ // searchBox html attributes
			'style' => 'width: 300px;', // Optional , default width and height defined in css coordinates-picker.css
		],
		'searchBoxPosition' => new JsExpression('google.maps.ControlPosition.TOP_LEFT'), // optional , default is TOP_LEFT
		'mapOptions' => [
			// google map options
			// visit https://developers.google.com/maps/documentation/javascript/controls for other options
            'mapTypeControl' => true, // Enable Map Type Control
            'mapTypeControlOptions' => [
                  'style'    => new JsExpression('google.maps.MapTypeControlStyle.HORIZONTAL_BAR'),
                  'position' => new JsExpression('google.maps.ControlPosition.TOP_LEFT'),
			],
            'streetViewControl' => true, // Enable Street View Control
        ],
		'clientOptions' => [
			// jquery-location-picker options
			'radius'    => 300,
            'addressFormat' => 'street_number',
		]
	]);
?>
~~~

Get coordinates :

Default valueTemplate is '{latitude},{longtitude} , So we will get resulit like : '25.023308046766083,121.46041916878664'

We can convert it via explode() :

~~~php
<?php
list($latitude,$longitude) = explode(',' , $model->coordinates);
?>
~~~

Deprecated options : enableMapTypeControl
-----------------------------------------

Since version 0.2.0 , don't use 'enableMapTypeControl' , I added 'mapOptions' for set googlemap options.
You can enable/disable all controlls or set control's style , position now.

Example : enable rotateControl , streetViewControl , mapTypeControl and set style/position

~~~php
<?php
   echo $form->field($model, 'coordinates')->widget('\pigolab\locationpicker\CoordinatesPicker' , [

        'clientOptions' => [ 'zoom' => 20 ], // rotateControl will display when zoom is 20
        // .... other options ...
		'mapOptions' => [
			// set google map optinos
			'rotateControl' => true,
			'scaleControl' => false,
			'streetViewControl' => true,
			'mapTypeId' => new JsExpression('google.maps.MapTypeId.SATELLITE'),
			'heading'=> 90,
            'tilt' => 45 ,
                
			'mapTypeControl' => true,
            'mapTypeControlOptions' => [
                  'style'    => new JsExpression('google.maps.MapTypeControlStyle.HORIZONTAL_BAR'),
                  'position' => new JsExpression('google.maps.ControlPosition.TOP_CENTER'),
			]
		]
   ]);
?>
~~~

Please visit GoogleMaps Full Control document :
https://developers.google.com/maps/documentation/javascript/controls



Other DocumentS
---------------
- [CoordinatesPicker with two fields Model](doc/TWO-FIELDS-CONVERSION.md)
- [How to use PHP trait do simple conversion MySQL Spatial Type between ActiveRecord](doc/MYSQL-SPATIAL-CONVERSION.md)
