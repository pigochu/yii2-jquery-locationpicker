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
    public $sourcePath = __DIR__ . "/assets";
    public $css = [
        'coordinates-picker.css'
    ];
}
