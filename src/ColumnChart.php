<?php

namespace dynamikaweb\apexcharts;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class ColumnChart extends Chart
{
    public $type = 'bar';
    private $_models;

    public function init()
    {
        if($this->dataProvider && $this->dataProvider->getCount() > 0){
            $this->getData();
        }
    }

    public function getData()
    {
        $this->setModelsObject();
        $data = array();

        //set the series categories
        if(!$this->categories){
            foreach($this->_models as $key => $model){
                $this->categories[$key] = $this->category?$model->{$this->category}:$key; 
            }
        }

        foreach($this->columns as $key => $column){

            if(!is_array($column)){
                $this->series[$key] = [
                    'name' => method_exists($this->_models[0], 'getAttributeLabel')?$this->_model[0]->getAttributeLabel($column):$column,
                    'data' => []
                ];
            }

            foreach($this->_models as $model){
                $this->series[$key]['data'][] = $model->$column;
            }
        
        }

    }

    public function setModelsObject()
    {
        $models = [];
        foreach($this->dataProvider->getModels() as $key => $model){

            //If array transforms to object
            if(is_array($model)){
               $model = Json::decode(Json::encode($model), false);
            }
            $models[] = $model;
        }

        $this->_models = $models;
    }
}