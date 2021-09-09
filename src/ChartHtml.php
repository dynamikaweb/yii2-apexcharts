<?php

namespace dynamikaweb\apexcharts;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class ChartHtml extends \yii\base\Widget
{
    const DIRECTION_ROW = 'row';
    const DIRECTION_COLUMN = 'column';

    public $options = [];
    public $pluginOptions = [];
    
    public $dataProvider;
    public $columns;
    public $category;

    /**
     * direction of dataprovider data processing
     */
    public $direction = self::DIRECTION_COLUMN;

    public $series = [];
    public $categories = [];
    public $type = 'line';
    public $width = '100%';
    public $height = '';
    
    /**
     * @return void
     */
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
        
        $jsOptions = Json::htmlEncode($this->pluginOptions);

        $script = "
          var chart = new ApexCharts(document.querySelector('#{$this->id}'), {$jsOptions});
          chart.render();";

        $this->view->registerJs($script);
    }
}