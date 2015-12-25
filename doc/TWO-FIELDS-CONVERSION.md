CoordinatesPicker with two fields Model
=======================================

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

## Controller's code ##

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

## View's code: ##

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
