jquery location picker widget for yii2
======================================

The widget implement [jquery-locationpicker-plugin
](https://github.com/Logicify/jquery-locationpicker-plugin)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

~~~
php composer.phar require pigochu/yii2-jquery-locationpicker ">=0.1.2"
~~~

or add

~~~
"pigochu/yii2-jquery-locationpicker": ">=0.1.2"
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
		'enableSearchBox' => true , // Optional , default is true
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

Get coordinates :

Default valueTemplate is '{latitude},{longtitude} , So we will get resulit like : '25.023308046766083,121.46041916878664'

We can convert it via explode() :

~~~php
<?php
list($latitude,$longtitude) = explode(',' , $model->coordinates);
?>
~~~

CoordinatesPicker with two fields Model
---------------------------------------

If your ActiveRecord Model has latitude and longitude and want to let  ActiveField auto binding , you can create a temp Model to do this :

TempMode.php
~~~php


<?php
// TempModel.php
use yii\base\Model;
class TempModel extends Model
    public $coordinates;
    public function rules()
    {
        return [
            [['coordinates'] ,'required'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'coordinates' => 'coordinates',
        ];
    }
?>
~~~



In your controller and view , you need do something

- create TempModel as $model2 then pass to view
- create hidden fields for latitude and longitude in view.
- define clientOptions['inputBinding'] of CoordinatesPicker.

Controller's code

~~~php
public function actionXXX($yourId) {
    $model = YourOriginalModel::findOne($yourId);
    $model2 = new TempModel();
    $model2->coordinates = $model->latitude . "," . $model->longitude;
    return $this->render('XXX' , [
        'model' => $model ,
        'model2' => $model2,
    ]);
}

~~~

View's code:

~~~php
    <?php
		// $model is your original Model
	 	echo $form->field($model, 'latitude')->hiddenInput()->label(false);
	 	echo $form->field($model, 'longitude')->hiddenInput()->label(false);
		// $model2 is TempModel
        echo $form->field($model2, 'coordinates')->widget('\pigolab\locationpicker\CoordinatesPicker' , [
			// ... your other setting
            'clientOptions' => [
                'radius'    => 300,
				'inputBinding' => [
                    'latitudeInput'     => new JsExpression("$('#"  .Html::getInputId($model, "latitude").  "')"),
                    'longitudeInput'    => new JsExpression("$('#"  .Html::getInputId($model, "longitude").  "')"),
				]
            ]
        ])->label('coordinates');
~~~


