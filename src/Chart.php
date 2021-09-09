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
            $this->normalizeObject();
            $this->setCategories();
            
            if($this->direction == self::DIRECTION_COLUMN){
                $this->normalizeData();    
            } else {
                $this->normalizeDataRow();    
            }
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
    public function normalizeData()
    {

        foreach($this->columns as $column){
            
            if($column == $this->category){
                continue;
            }

            if (is_array($column)){
                $name = isset($column['label'])?$column['label']
                    :(method_exists($this->_models[0], 'getAttributeLabel')?$this->_model[0]->getAttributeLabel($column['attribute']):$column['attribute']);
            } else {
                $name = method_exists($this->_models[0], 'getAttributeLabel')?$this->_model[0]->getAttributeLabel($column):$column; 
            }

            $data = [];
            foreach($this->_models as $model){
                if(!is_array($column)){
                    $serie = $model->$column;
                } else if(isset($column['value'])){
                    $serie = is_callable($column['value']) ? call_user_func($column['value'], $model) : $column['value'];
                } else {
                    $serie = $model->{$column['attribute']};
                }

                $data[] = $serie;
            } 

            $this->series[] = ['name' => $name, 'data' => $data];
        }

    }

    public function normalizeDataRow()
    {

        foreach($this->_models as $key => $model){
            $name = $model->{$this->category};
            $data = [];
            
            foreach($this->columns as $column){
                if($column == $this->category){
                    continue;
                }
                if(!is_array($column)){
                    $serie = $model->$column;
                } else if(isset($column['value'])){
                    $serie = is_callable($column['value']) ? call_user_func($column['value'], $model) : $column['value'];
                } else {
                    $serie = $model->{$column['attribute']};
                }

                $data[] = $serie;
            } 

            $this->series[] = ['name' => $name, 'data' => $data];
        }

    }

    /**
     * set of arrays as objects
     *
     * @return void
     */
    public function normalizeObject()
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
        $this->columns = $this->columns?$this->columns:array_keys((array) $this->_models[0]);

        if(!$this->category){
            $this->category = $this->columns[0];
        } 
        unset($this->columns[$this->category]);

        if(!$this->categories){
            
            if($this->direction == self::DIRECTION_COLUMN){
                foreach($this->_models as $key => $model){
                    $this->categories[$key] = $model->{$this->category}; 
                }
            } else {
                foreach($this->_models[0] as $key => $attribute){
                    if($key == $this->category){
                        continue;
                    }
                    $this->categories[] = $key;
                }
            }
        }
    }
}