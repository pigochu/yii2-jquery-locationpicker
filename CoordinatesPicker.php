<?php
namespace pigolab\locationpicker;

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use yii\web\JqueryAsset;
use yii\web\JsExpression;


/**
 * CoordinatesPicker
 */
class CoordinatesPicker extends \yii\widgets\InputWidget
{
    
    /** @var string google map api key */
    public $key;
    
    /**
     * This is model attribute's value format
     * @var string
     */
    public $valueTemplate = '{latitude},{longitude}';
    
    /** @var boolean enable search box overlay on map canvas */
    public $enableSearchBox = true;
    
    /** @var array */
    public $searchBoxOptions = [];
    
    /** @var string|\yii\web\JsExpression */
    public $searchBoxPosition;
    
    /**
     * Please use mapOptions
     * 
     * <pre>
     * [
     *     'mapOptions' => [
     *         'mapTypeControl' => true,
     *         'mapTypeControlOptions' => [
     *             'style'    => new JsExpression('google.maps.MapTypeControlStyle.HORIZONTAL_BAR'),
     *             'position' => new JsExpression('google.maps.ControlPosition.TOP_CENTER'),
     *         ]
     *     ]
     * ]
     * </pre>
     * 
     * @var boolean
     * @deprecated since 0.2.0
     * @see https://developers.google.com/maps/documentation/javascript/controls
     */
    public $enableMapTypeControl = true;
    
    /**
     * Google Map Options
     * @var array
     * @since 0.2.0
     */
    public $mapOptions = [];
    
    
    /** @var map canvas html attribute */
    public $options = [];
    /** @var jquery-locationpicker js options */
    public $clientOptions = [];
    /** @var jquery-locationpicker js events */
    public $clientEvents = [];
    
    public function init() {
        parent::init();
        CoordinatesPickerAsset::register($this->view);
    }

    public function run()
    {
        if($this->model === null) {
            $inputId = $this->getId();
        } else {
            if($this->getId(false) === null) {
                $inputId = Html::getInputId($this->model, $this->attribute);
                $this->setId($inputId);
            } else {
                $inputId = $this->getId();
            }
            
        }
        $widgetId = $inputId . '-map';


        if($this->name === null) {
            $inputName = $this->model===null ? null: Html::getInputName($this->model, $this->attribute);
        } else {
            $inputName = $this->name;
        }
       
        if($this->enableSearchBox === true) {
            $this->_renderSearchBox();
        }
        
        // render attribute as hidden input
        if($this->model === null) {
            echo Html::hiddenInput($inputName, $this->value , ['id'=> $inputId , 'name' => $inputName]);
        } else {
            echo Html::activeHiddenInput(
                $this->model,
                $this->attribute,
                ['id'=> $inputId , 'name' => $inputName]
            );
            $this->value = $this->model->{$this->attribute};
        }
        
       
        
        $this->_registerOnChangedEvent($this->id);
        $this->_registerOnInitializedEvent();

        $this->_setClientLocation();
        
        
        $widgetOptions = $this->options;
        unset($widgetOptions['id']);
        echo LocationPickerWidget::widget([
            'id'  => $widgetId,
            'key' => $this->key ,
            'options' => $widgetOptions,
            'clientOptions' => $this->clientOptions,
            'clientEvents'  => $this->clientEvents,
        ]);

    }

    private function _setClientLocation() {
        
        $coordinates = null;
        if($this->model) {
            $coordinates = Html::getAttributeValue($this->model, $this->attribute);
        }
        
        // $coordinates = $this->model->attributes[$this->attribute];
        if($coordinates === null || empty($coordinates)) {
            return;
        }
 
        $latitudeIndex = strpos($this->valueTemplate , '{latitude}');
        $longitudeIndex = strpos($this->valueTemplate , '{longitude}');
        
        if($latitudeIndex === false || $longitudeIndex === false) {
            throw new InvalidConfigException('Property "valueTemplate" is invalid.');
        }

        $pattern = '/' . strtr($this->valueTemplate , [
            '{latitude}' =>  '(?P<latitude>.*)' ,
            '{longitude}' => '(?P<longitude>.*)'
         ]). '/';

        
        preg_match_all($pattern, $coordinates, $matches , PREG_SET_ORDER);

        $this->clientOptions['location'] = [
            'latitude' => floatval($matches[0]['latitude']),
            'longitude' => floatval($matches[0]['longitude']),
        ];
    }
    
    
    /**
     * Generate searchbox javascript code
     */
    private function _renderSearchBox() {
            if(!isset($this->searchBoxOptions['class'])) {
                $this->searchBoxOptions['class'] = 'coordinates-picker searchbox';
            }
            
            if(!isset($this->searchBoxOptions['id'])) {
                $this->searchBoxOptions['id'] = $this->getId() . '-searchbox';
            }

            // render SearchBox
            echo Html::tag('input' , '' , $this->searchBoxOptions);
            
            $this->clientOptions['enableAutocomplete'] = true;

            
            if(!isset($this->clientOptions['inputBinding'])) {
                $this->clientOptions['inputBinding'] = [];
            }
            
            if(!isset($this->clientOptions['inputBinding']['locationNameInput'])) {
                // binding search box
                $this->clientOptions['inputBinding']['locationNameInput'] = new JsExpression("jQuery('#" .$this->searchBoxOptions['id'].  "')");
            }
    }
    
    private function _registerOnChangedEvent($attributeId) {

        $onchangedDefaultFunction = null;
        if(isset($this->clientOptions['onchanged'])) {
            $onchangedDefaultFunction = $this->clientOptions['onchanged'];
        }
        
        // function(c,r,i) = function(currentLocation, radius, isMarkerDropped)
        $onChangedJS = "function(c,r,i) { (function() {\n"
                     . "var _t='" .$this->valueTemplate. "';\n"
                     . "jQuery('#" .$attributeId. "').val(_t.replace('{latitude}',c.latitude ).replace('{longitude}',c.longitude));\n"
                     . "})();\n";

        // call the default onchanged function
        if($onchangedDefaultFunction instanceof JsExpression) {
            $onChangedJS .= "var _fn = " .$onchangedDefaultFunction . "\n";
            $onChangedJS .= "_fn.call(this,arguments);\n";
        }
        $onChangedJS .= "}";

        $this->clientOptions['onchanged'] = new JsExpression($onChangedJS);
    }
    
    private function _registerOnInitializedEvent() {
        
        if($this->enableSearchBox === false && $this->enableMapTypeControl === false) {
            return;
        }
        
        if(isset($this->searchBoxOptions['id'])) {
            $searchBoxId = $this->searchBoxOptions['id'];
        }
        
        $onInitializedDefaultFunction = null;
        if(isset($this->clientOptions['oninitialized'])) {
            $onInitializedDefaultFunction = $this->clientOptions['oninitialized'];
        }
        
        // function(c) = function(component)
        $onInitializedJS = "function(c) {(function(){\n"
                         . "var _map = jQuery(c).locationpicker('map').map;\n";
        
        
           
        if(empty($this->value) && !isset($this->clientOptions['location'])) {
            // set default hidden field value
            $id = $this->getId();
            $onInitializedJS .= "var _t='" .$this->valueTemplate. "' , _l=$.fn.locationpicker.defaults.location;\n"
                              . "jQuery('#" .$id. "').val(_t.replace('{latitude}',_l.latitude ).replace('{longitude}',_l.longitude));";
        }
        if($this->enableSearchBox) {
            
            $position = new JsExpression('google.maps.ControlPosition.TOP_LEFT');
            if($this->searchBoxPosition !== null) {
                $position = $this->searchBoxPosition;
            }
            $onInitializedJS .= "jQuery('#{$searchBoxId}').show();\n";
            $onInitializedJS .= "_map.controls[{$position}].push(jQuery('#{$searchBoxId}').get(0));\n";
        }
                     
        if($this->enableMapTypeControl === true) {

            $onInitializedJS .= "console.warn('yii2-jquery-locationpicker : enableMapTypeControl is deprecated since 0.2.0 , we recommand use mapOptions to define google map options.')\n";
            
            if(!isset($this->mapOptions['mapTypeControl'])) {
                $this->mapOptions['mapTypeControl'] = true;
            }
        }
        
        if(count($this->mapOptions)) {
            $onInitializedJS .= "_map.setOptions(" . Json::htmlEncode($this->mapOptions) .  ");\n";
        }
        
        $onInitializedJS .= "})();\n";

        // call the default oninitialized function
        if($onInitializedDefaultFunction instanceof JsExpression) {
            $onInitializedJS .= "var _fn = " .$onInitializedDefaultFunction . "\n";
            $onInitializedJS .= "_fn.call(this,arguments);\n";
        }
        $onInitializedJS .= "}";

        $this->clientOptions['oninitialized'] = new JsExpression($onInitializedJS);
    }

}
