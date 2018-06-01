<?php

class MyDataProvider extends CActiveDataProvider
{
    public $cacheID;
    public $cacheTimeout = 3600;
    public $cacheKey = null;

    protected function fetchData()
    {  		
        $criteria=clone $this->getCriteria();

        if(($pagination=$this->getPagination())!==false)
        {
            $pagination->setItemCount($this->getTotalItemCount());
            $pagination->applyLimit($criteria);
        }

        $baseCriteria=$this->model->getDbCriteria(false);
 
        if(($sort=$this->getSort())!==false)
        {
            // set model criteria so that CSort can use its table alias setting
            if($baseCriteria!==null)
            {
                $c=clone $baseCriteria;
                $c->mergeWith($criteria);
                $this->model->setDbCriteria($c);
            }
            else
                $this->model->setDbCriteria($criteria);
            $sort->applyOrder($criteria);
        }

        $key = $criteria->condition;
        $p = array();
        foreach($criteria->select as $k=>$v) $p[] = $v;
        foreach($criteria->params as $k=>$v) $p[] = $k.'='.$v;
        $key .= implode('|', $p);
        $key .= $criteria->order;
        $key .= $criteria->offset.'-'.$criteria->limit;
        $key = trim(strtolower($key));

        $this->cacheKey = $key;

        $cacheID = $this->cacheID;
        $data = false;

        if(!empty($cacheID))
        {
            $cache = Yii::app()->$cacheID;
            $data = $cache->get($key);
        }

        if(empty($data))
        {
            $this->model->setDbCriteria($baseCriteria!==null ? clone $baseCriteria : null);		
            $data=$this->model->findAll($criteria);			
            $this->model->setDbCriteria($baseCriteria);  // restore original criteria

            if(!empty($cacheID)) $cache->set($key, $data, $this->cacheTimeout);
        }


        return $data;
    }

}