<?php

class UploadValidator extends CValidator
{
    public $required = true;

    protected function validateAttribute($object, $attribute)
    {
        $image = CUploadedFile::getInstancesByName($attribute);
        if($this->required && $image == null) $this->addError($object, $attribute, 'Нужно обязательно загрузить файл');
    }
}