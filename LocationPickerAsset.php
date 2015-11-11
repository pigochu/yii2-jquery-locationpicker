<?php
namespace pigolab\locationpicker;
use yii\web\View;
use yii\base\InvalidConfigException;
/**
 * LocationPickerAsset
 *
 * @author pigo
 */
class LocationPickerAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/jquery-locationpicker-plugin/dist';
    public $css = [
    ];
    public $js = [
        'locationpicker.jquery.min.js',
    ];
    public $jsOptions = [
        'position' => View::POS_END,
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
