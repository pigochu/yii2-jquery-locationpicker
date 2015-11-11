jquery location picker widget for yii2
======================================

The widget implement [jquery-locationpicker-plugin
](https://github.com/Logicify/jquery-locationpicker-plugin)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require pigochu/yii2-jquery-locationpicker ">=0.0.1"
```

or add

```
"pigochu/yii2-jquery-locationpicker": "*"
```

to the require section of your `composer.json` file.


Basic Usage for Test
--------------------

```
<?= \pigolab\locationpicker\LocationPickerWidget::widget(); ?>
```

Binding UI with the widget
--------------------------

This sample is transformed via [http://logicify.github.io/jquery-locationpicker-plugin/#usage](http://logicify.github.io/jquery-locationpicker-plugin/#usage)

```
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
       'key' => 'abcabcabcabc ...',   // optional , Your can also put your google map api key
       'options' => [
            'style' => 'width: 100%; height: 400px'
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

```

@TODO
-----

- CoordinatesInput : Use in ActiveForm and SearchBox overlay on Map Canvas 


