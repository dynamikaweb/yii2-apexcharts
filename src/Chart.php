<?php

namespace dynamikaweb\apexcharts;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class Chart extends \yii\base\Widget
{
    public $options = [];
    public $pluginOptions = [];
    
    public $dataProvider;
    public $columns;
    public $category;

    public $series = [];
    public $categories = [];
    public $type = 'line';
    public $width = '100%';
    public $height = '';
    
    public function run()
    {
        ChartAsset::register($this->view);

        $tag = ArrayHelper::remove($this->options,'tag', 'div');
        $options = ArrayHelper::merge($this->options, ['id' => $this->id]);
        echo Html::tag($tag, null, $options);

        $this->registerClientScript();
    }

    /**
     * Registers required scripts
     */
    public function registerClientScript()
    {
        $this->pluginOptions = ArrayHelper::merge([
                'chart' => [
                    'type' => $this->type,
                    'width' => $this->width,
                    'height' => $this->height
                ],
                'series' => $this->series,
                'xaxis' => [
                    'categories' => $this->categories
                ]
            ],
            $this->pluginOptions
        );
        
        $jsOptions = json_encode($this->pluginOptions);

        $script = "
          var chart = new ApexCharts(document.querySelector('#{$this->id}'), {$jsOptions});
          chart.render();";

        $this->view->registerJs($script);
    }
}