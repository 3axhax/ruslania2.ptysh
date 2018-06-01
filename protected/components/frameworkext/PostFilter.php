<?php

class PostFilter extends CFilter
{
    protected function preFilter($filterChain)
    {
        if(!Yii::app()->request->isPostRequest) return true;

        // Обрезаем все пробельные символы в POST запросе
        $array = $this->trimPost($_POST);
        $_POST = $array;
        return true;
    }

    private function trimPost($array)
    {
        foreach($array as $key=>$val)
        {
            if(!is_array($array[$key])) $array[$key] = trim($array[$key]);
            else $array[$key] = $this->trimPost($array[$key]);
        }
        return $array;
    }
}
