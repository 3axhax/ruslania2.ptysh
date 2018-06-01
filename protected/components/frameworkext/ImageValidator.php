<?php

class ImageValidator extends CValidator
{
    public $required = true;

    protected function validateAttribute($object, $attribute)
    {
        $image = CUploadedFile::getInstanceByName($attribute);
        if($this->required && $image == null) $this->addError($object, $attribute, 'Нужно обязательно загрузить файл');
    }
}