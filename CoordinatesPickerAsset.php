<?php
namespace pigolab\locationpicker;
use yii\web\View;
use yii\base\InvalidConfigException;
/**
 * LocationPickerAsset
 *
 * @author pigo
 */
class CoordinatesPickerAsset extends \yii\web\AssetBundle
{
    public $css = [
        'coordinates-picker.css'
    ];
    public function init() {
        parent::init();
        $this->setSourcePath(__DIR__ . '/assets');
    }
}
