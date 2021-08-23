<?php

namespace dynamikaweb\apexcharts;

/**
 * Set Assets dependency
 */
class ChartAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@npm/apexcharts';

    public $js = [
        'dist/apexcharts.min.js',
    ];

}