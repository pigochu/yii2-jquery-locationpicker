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
    public $searchBoxOptions = [];
    
    /** @var boolean */
    public $enableMapTypeControl = true;
    
    /** @var map canvas html attribute */
    public $options = [];
    /** @var jquery-locationpicker js options */
    public $clientOptions = [];
    /** @var jquery-locationpicker js events */
    public $clientEvents = [];
    
    public function init() {
        parent::init();
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        CoordinatesPickerAsset::register($this->view);
    }

    public function run()
    {
        if($this->enableSearchBox === true) {
            $this->_renderSearchBox();
        }
        
        // render attribute as hidden input
        echo Html::activeHiddenInput(
            $this->model,
            $this->attribute
        );
        $attributeId = Html::getInputId($this->model, $this->attribute);
        $this->_registerOnChangedEvent($attributeId);
        $this->_registerOnInitializedEvent();

        $this->_setClientLocation();
        
        $widgetOptions = $this->options;
        
        if(isset($widgetOptions['id'])) {
            unset($widgetOptions['id']);
        }

        echo LocationPickerWidget::widget([
            'key' => $this->key ,
            'options' => $widgetOptions,
            'clientOptions' => $this->clientOptions,
            'clientEvents'  => $this->clientEvents,
        ]);
    }

    private function _setClientLocation() {
        
        // var_dump($this->model);
        
        $coordinates = $this->model->attributes[$this->attribute];
        if($coordinates === null) {
            return;
        }
 
        $latitudeIndex = strpos($this->valueTemplate , '{latitude}');
        $longitudeIndex = strpos($this->valueTemplate , '{longitude}');
        
        if($latitudeIndex === false || $longitudeIndex === false) {
            throw new InvalidConfigException('Property "valueTemplate" is invalid.');
        }
        
        if($latitudeIndex < $longitudeIndex) {
            $latitudeIndex  = 1;
            $longitudeIndex = 2;
        } else {
            $latitudeIndex  = 2;
            $longitudeIndex = 1;
        }
        $pattern = '/' . str_replace(['{latitude}' , '{longitude}'] , '(\d+(?:\.\d+))' , $this->valueTemplate) . '/';

        preg_match_all($pattern, $coordinates, $matches);
        $latitude = floatval($matches[$latitudeIndex][0]);
        $longitude = floatval($matches[$longitudeIndex][0]);

        $this->clientOptions['location'] = [
            'latitude' => $latitude,
            'longitude' => $longitude,
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
        $onchangedOldFunction = null;
        if(isset($this->clientOptions['onchanged'])) {
            $onchangedOldFunction = $this->clientOptions['onchanged'];
        }
        
        // function(c,r,i) = function(currentLocation, radius, isMarkerDropped)
        $onChangedJS = "function(c,r,i) {"
                     . "var _t='" .$this->valueTemplate. "';"
                     . "jQuery('#" .$attributeId. "').val(_t.replace('{latitude}',c.latitude ).replace('{longitude}',c.longitude));";

        // if clientOptions has "onchaged" , convert it to anymouse function and call it
        if($onchangedOldFunction instanceof JsExpression) {
            $onChangedJS .= "(". $onchangedOldFunction ."(c,r,i))\n" ;
        }
        $onChangedJS .= "\n}";

        $this->clientOptions['onchanged'] = new JsExpression($onChangedJS);
    }
    
    private function _registerOnInitializedEvent() {
        
        if($this->enableSearchBox === false && $this->enableMapTypeControl === false) {
            return;
        }
        
        if(isset($this->searchBoxOptions['id'])) {
            $searchBoxId = $this->searchBoxOptions['id'];
        }
        
        $onInitializedOldFunction = null;
        if(isset($this->clientOptions['oninitialized'])) {
            $onInitializedOldFunction = $this->clientOptions['oninitialized'];
        }
        
        // function(c) = function(component)
        $onInitializedJS = "function(c) {\n" . "var _map = jQuery(c).locationpicker('map').map;\n";

        if($this->enableSearchBox) {
            $onInitializedJS .= "_map.controls[google.maps.ControlPosition.TOP_LEFT].push(jQuery('#{$searchBoxId}').get(0));\n";
        }
                     
        if($this->enableMapTypeControl === true) {
            $onInitializedJS .= "_map.setOptions({mapTypeControl: true});\n";
        }
                     
        // if clientOptions has "oninitialized" , convert it to anymouse function and call it
        if($onInitializedOldFunction instanceof JsExpression) {
            $onInitializedJS .= "(". $onInitializedOldFunction ."(c))\n" ;
        }
        $onInitializedJS .= "\n}";

        $this->clientOptions['oninitialized'] = new JsExpression($onInitializedJS);
    }

}
