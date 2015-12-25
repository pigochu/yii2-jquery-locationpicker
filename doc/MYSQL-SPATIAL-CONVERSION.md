How to use PHP trait do simple conversion MySQL Spatial Type between ActiveRecord
=================================================================================

If you are using MySQL Spatial Data Type like "Point" to save the coordinates , but YII2 ActiveRecord doesn't support MySQL Spatial Data , you can use my simple code to conversion easily.

Suppose you have a ActiveRecord class named "Foo"

~~~php
class Foo extends ActiveRecord {
    // your code .............
}
~~~

And Foo has a field named "coordinates" , In mysql real table also has a coordinates column and type is Point.


### 1. create CoordinatesTrait
~~~php
<?php

namespace common\models;
use \yii\db\Expression;
/**
 * CoordinatesTrait
 *
 * @author pigo
 */
trait CoordinatesRecordTrait {
    
    /** @var float */
    public $latitude;
    
    /** @var float */ 
    public $longitude;
    
    private $_strCoordinates;
    
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->_strCoordinates = $this->coordinates;
            list($latitude , $longitude) = explode(',' , $this->coordinates);
            $this->coordinates = new Expression("point({$latitude},{$longitude})");
            return true;
        } else {
            return false;
        }
    }
    
    public function afterSave ($insert, $changedAttributes) {
        parent::afterSave($insert, $changedAttributes);
        $this->coordinates = $this->_strCoordinates;
        list($latitude , $longitude) = explode(',' , $this->coordinates);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }
    
    public function afterFind() {
        parent::afterFind();
        $this->coordinates = implode(',' , [$this->latitude , $this->longitude]);
    }

}
~~~
### 2. create CoordinatesQueryTrait

~~~php
<?php

namespace common\models;
use \yii\db\Expression;
/**
 * CoordinatesQueryTrait
 *
 * @author pigo
 */
trait CoordinatesQueryTrait {
    public function latlong($fieldName) {
        $this->addSelect(new Expression("ST_X([[{$fieldName}]]) as latitude"));
        $this->addSelect(new Expression("ST_Y([[{$fieldName}]]) as longitude"));
        return $this;
    }
}
~~~

### 3. add CoordinatesRecordTrait to your Foo class

~~~php
class Foo extends ActiveRecord {
    use CoordinatesRecordTrait;
    // your code .............
}
~~~

### 4. if you have a FooQuery (ActiveQuery) class , add CoordinatesQueryTrait to FooQuery

~~~php
class Foo extends ActiveQuery {
    use CoordinatesQueryTrait;
    // your code .............
}
~~~

Thats all,  CoordinatesRecordTrait and CoordinatesQueryTrait can be reuse

When you find records via Foo::find() , you can get latitude and longitude easily.

~~~php
<?php

    // not use latlong()
    $model1 = Foo::find()->where("id=:id" , ['id'=>$id])->one();
    var_dump($model1->coordinates);
    // you will get mysql BLOB string , but how to display it ??

    // when use latlong()
    $model2 = Foo::find()->latlong('coordinates')->where("id=:id" , ['id'=>$id])->one();
    var_dump($model2->coordinates);
    // you will get result like "25.040369020430944,121.5122790711639" 

    // you can also get latitude and longitude directly
    var_dump($model2->latitude);
    var_dump($model2->longitude);

    // save coordinates easily,too
    $model2->coordinates = "26.040369020430944,120.5122790711639";
    $model2->save();
    // You do not need to do any conversion
~~~

