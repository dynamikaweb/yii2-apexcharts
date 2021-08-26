<?php

namespace dynamikaweb\apexcharts;


class Chart extends ChartHtml
{
    private $_models;

    /**
     * checks if there is dataprovider and starts assembling the categories and series
     *
     * @return void
     */
    public function init()
    {
        if($this->dataProvider && $this->dataProvider->getCount() > 0){
            $this->setModelsObject();
            $this->setCategories();
            $this->getData();
        }
    }

    /**
     * transforms dataProvider into series based on columns
     * output example: 'series' => [
     *      [
     *          'name' => 'column',
     *          'data' => [21, 23, 20, 40]
     *      ]
     * ]
     *
     * @return void
     */
    public function getData()
    {
        $data = array();

        foreach($this->columns as $key => $column){

            if (is_array($column)){
                $this->series[$key]['name'] = isset($column['label'])?$column['label']
                    :(method_exists($this->_models[0], 'getAttributeLabel')?$this->_model[0]->getAttributeLabel($column['attribute']):$column['attribute']);
            } else {
                $this->series[$key]['name'] = method_exists($this->_models[0], 'getAttributeLabel')?$this->_model[0]->getAttributeLabel($column):$column; 
            }

            foreach($this->_models as $model){
                if(!is_array($column)){
                    $serie = $model->$column;
                } else if(isset($column['value'])){
                    $serie = is_callable($column['value']) ? call_user_func($column['value'], $model) : $column['value'];
                } else {
                    $serie = $model->{$column['attribute']};
                }

                $this->series[$key]['data'][] = $serie;
            }
        }

    }

    /**
     * set of arrays as objects
     *
     * @return void
     */
    public function setModelsObject()
    {
        foreach($this->dataProvider->getModels() as $model){
            //If array transforms to object
            $this->_models[] = is_array($model) ? (object)$model : $model;
        }        
    }

    /**
     * set the series categories
     *
     * @return void
     */
    public function setCategories()
    {
        if(!$this->categories){
            foreach($this->_models as $key => $model){
                $this->categories[$key] = $this->category?$model->{$this->category}:$key; 
            }
        }
    }
}