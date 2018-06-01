<?php

class CKnockoutForm extends CWidget
{
    const TYPE_BOOL = 1;
    const TYPE_ARRAY = 2;
    const TYPE_STRING = 3;
    const TYPE_FILE = 4;

    public $htmlOptions = array();
    public $action = '';
    public $method = 'post';
    public $enableAjaxValidation = true;
    public $inputErrorClass = 'error';
    public $disableSubmitButton = true;
    protected $attributes = array();
    public $submitViaAjax = false;
    public $submitViaAjaxVar = 'sva';
    public $afterAjaxSubmit = null;
    public $afterValidate = null;
    public $applyBindings = true;
    public $sendAsFormData = false;
    public $model = null;
    public $viewModel = 'viewModel';
    public $timeoutVM = 0;
    protected $dependentObservables = array();
    protected $filesField = array();


    public static function RegisterScripts($full=true)
    {
        $cs = Yii::app()->clientScript;
        $cs->registerScriptFile('/js/knockout.js');
        $cs->registerScriptFile('/js/knockout.mapping.js');
        if($full)
        {
            $cs->registerScriptFile('/js/knockout.form.js');
            $cs->registerScriptFile('/js/knockout-sortable.js');
            $cs->registerScriptFile('/js/knockout.file.js');
        }
    }

    public function init()
    {
        self::RegisterScripts();
        if (!isset($this->htmlOptions['id'])) $this->htmlOptions['id'] = $this->id;
        if ($this->enableAjaxValidation || $this->submitViaAjax)
        {
            $this->htmlOptions['data-bind'] = 'submit: onSubmit';
        }

        if ($this->submitViaAjax && empty($this->afterAjaxSubmit)) throw new CException('No JS function AfterAjaxSubmit defined');

        echo CHtml::beginForm($this->action, $this->method, $this->htmlOptions);
    }

    public function run()
    {
        if (empty($this->model)) throw new CException('No model');
        $knError = 'var knError = function(id, msg) { this.id = id; this.msg = msg; }; ';
        Yii::app()->clientScript->registerScript('knError', $knError, CClientScript::POS_HEAD);

        $js = ' var ' . $this->viewModel . ' = { ' . "\n"
            . "model : '" . (get_class($this->model)) . "', \n"
            . "sva : ko.observable(" . ($this->submitViaAjax ? 'true' : 'false') . "), \n"
            . "errors :  ko.observableArray([]),\n"
            . "hasErrors: function(id) { \n"
            . "return ko.dependentObservable(function () { \n"
            . " var ret = false; \n"
            . " $.each(this.errors(), function(idx, value) { if(value.id == id) { ret = true; return false; } }); \n"
            . " return ret; }, " . $this->viewModel . "); }, \n"
            . "getErrors: function(id) { \n"
            . "return ko.dependentObservable(function () { \n"
            . " var ret = ''; $.each(this.errors(), function(idx, value) { if (value.id == id) { ret += value.msg; } }); return ret; \n"
            . "}, " . $this->viewModel . "); }, \n"
            . "csrf : '" . Yii::app()->request->csrfTokenName . "', \n"
            . Yii::app()->request->csrfTokenName . " : '" . Yii::app()->request->csrfToken . "',\n "
            . "attributes : [";

        $attribs = array();
        foreach ($this->attributes as $attr => $type)
        {
            $attribs[] = "'" . $attr . "'";
        }
        $js .= implode(',', $attribs) . "],\n";

        if(!empty($this->filesField))
        {
            $js .= 'fileAttributes: ["'.implode('","', $this->filesField).'"],'."\n";
        }

        $attribs = array();

        foreach ($this->attributes as $attr => $data)
        {
            $type = isset($data[0]) ? $data[0] : $data;
            $id = isset($data[1]) ? $data[1] : null;
            $getDataFromHtml = isset($data[2]) && $data[2];

            $val = isset($this->model[$attr])
                ? $this->model[$attr]
                : (isset($this->model->$attr) ? $this->model->$attr : null);
            $mode = 'ko.observable(';

            if ($type == CKnockoutForm::TYPE_ARRAY || is_array($val))
            {
                $mode = 'ko.observableArray([';
                if (!empty($val) && is_array($val))
                {
                    $temp = array();
                    foreach ($val as $v)
                    {
                        if (is_array($v))
                        {
                            foreach ($v as $key => $v1)
                            {
                                $temp[] = '{' . $key . ' : "' . $v1 . '"}';
                            }
                        }
                        else
                        {
                            $temp[] = '"' . $v . '"';
                        }
                    }
                    $val = implode(', ', $temp);
                }
                else $val = '';
                $val .= ']';
            }
            else if (is_bool($val)) $val = ($val) ? 'true' : 'false';
            else if (is_numeric($val)) $val = $val;
            else if ($getDataFromHtml)
            {
                $val = "$('#" . $id . "').val()";
            }
            else
            {
                $val = "'" . $val . "'";
            }
            $attribs[] = $attr . ': ' . $mode . $val . ')';
        }

        $js .= implode(",\n", $attribs) . "\n";
        if (!empty($attribs)) $js .= ', ';

        if ($this->enableAjaxValidation)
        {
            $js .= 'ourCallback: function(ret) {  if(!ret.RetValue && !ret.HasValidatingErrors) window["' . $this->afterAjaxSubmit . '"](ret.Json, '.$this->viewModel.'); }, ';
            $js .= 'afterValidate: function() { ' . (($this->afterValidate == null) ? '' : $this->afterValidate) . ' }, ';
            $js .= 'onSubmit: function(elem) { ';
            if ($this->disableSubmitButton)
            {
                $js .= ' this.disableSubmitButton(true); ' . "\n";
            }
            $js .= ' knockoutPostForm(elem, ' . $this->viewModel . ', this.ourCallback, this.afterValidate, '.($this->sendAsFormData ? 'true' : 'false').'); }' . "\n";
        }

        if ($this->disableSubmitButton)
        {
            $js .= ', disableSubmitButton : ko.observable(false), ' . "\n";
        }

        $js .= '};';

        if (!empty($this->dependentObservables))
        {
            foreach ($this->dependentObservables as $name => $code)
            {
                $line = <<<EEE

                $this->viewModel.$name = ko.dependentObservable(function()
                {
                    $code
                }, $this->viewModel);
EEE;
                $js .= $line . "\n";
            }
        }

        if ($this->applyBindings)
        {
            $apply = 'ko.applyBindings(' . $this->viewModel . ', document.getElementById("' . $this->id . '"));';
            if($this->timeoutVM == 0) $js .= $apply;
            else $js .= 'setTimeout(function() { '.$apply.' }, '.$this->timeoutVM.');';
        }

        Yii::app()->clientScript->registerScript('koVM' . $this->viewModel, $js, CClientScript::POS_END);
        echo CHtml::endForm();
    }

    public function koArray($attribute)
    {
        $this->attributes[$attribute] = array(CKnockoutForm::TYPE_ARRAY, null);
    }

    public function DependentObservable($name, $code)
    {
        $this->dependentObservables[$name] = $code;
    }

    public function joinDataBind($dataBind)
    {
        $ret = $dataBind;
        if (is_array($dataBind))
        {
            $r = array();
            foreach ($dataBind as $key => $val)
            {
                $pos = strpos($key, '-');
                if ($pos !== false) $key = "'" . $key . "'";

                if (!is_array($val))
                {
                    $r[] = $key . ': ' . $val;
                }
                else
                {
                    $val = $this->joinDataBind($val);
                    $r[] = $key . ': { ' . $val . '}';
                }
            }
            $ret = implode(', ', $r);
        }
        return $ret;
    }

    public function textField($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $ret = CHtml::activeTextField($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(CKnockoutForm::TYPE_STRING, $htmlOptions['id'], true);
        return $ret;
    }

    public function fileField($attribute, $multiple=false, $htmlOptions = array())
    {
        //$htmlOptions['data-bind']['multiple'] = $multiple;
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions, true);
        $this->filesField[] = $attribute;
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $htmlOptions['name'] = $multiple ? $attribute.'[]' : $attribute;
        $ret = CHtml::activeFileField($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(CKnockoutForm::TYPE_FILE, $htmlOptions['id'], true);
        return $ret;
    }

    public function passwordField($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $ret = CHtml::activePasswordField($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(CKnockoutForm::TYPE_STRING, $htmlOptions['id'], true);
        return $ret;
    }

    public function textArea($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $ret = CHtml::activeTextArea($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(CKnockoutForm::TYPE_STRING, $htmlOptions['id'], true);
        return $ret;
    }

    public function submitButton($label, $htmlOptions = array())
    {
        if ($this->disableSubmitButton) $htmlOptions['data-bind']['disable'] = 'disableSubmitButton';
        $htmlOptions['data-bind'] = $this->SetupDataBind(null, $htmlOptions);
        return CHtml::submitButton($label, $htmlOptions);
    }

    public function checkBox($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind']['checked'] = $attribute;
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(CKnockoutForm::TYPE_BOOL, $htmlOptions['id']);
        $htmlOptions['uncheckValue'] = null;
        return CHtml::activeCheckBox($this->model, $attribute, $htmlOptions);
    }

    public function koField($attribute, $htmlOptions = array())
    {
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(CKnockoutForm::TYPE_STRING, $htmlOptions['id']);
    }

    public function dropDownList($attribute, $select, $idKey, $nameKey, $htmlOptions = array())
    {
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = '';
        $valuesAttribute = $attribute . 'Select';
        $js = 'var ' . $valuesAttribute . '=[';
        $r = array();
        if(isset($htmlOptions['empty']))
        {
            $r[] = '{ id: null, name: "'.$htmlOptions['empty'].'"}';
        }
        foreach ($select as $s)
        {
            $r[] = '{ id: ' . $s[$idKey] . ', name: "' . str_replace('"', '\"', $s[$nameKey]) . '"}';
        }
        $js .= implode(', ', $r) . '];';

        $r = '<script type="text/javascript">' . $js . '</script>';

        $htmlOptions['data-bind']['value'] = $attribute;
        $htmlOptions['data-bind']['options'] = $valuesAttribute;
        $htmlOptions['data-bind']['optionsText'] = "'name'";
        $htmlOptions['data-bind']['optionsValue'] = "'id'";
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);

        return $r . CHtml::tag('select', $htmlOptions, '');
    }

    public function label($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind']['css'][$this->inputErrorClass] = 'hasErrors("' . $attribute . '")';
        $htmlOptions['data-bind'] = $this->SetupDataBind(null, $htmlOptions);
        return CHtml::activeLabel($this->model, $attribute, $htmlOptions);
    }

    public function autoCompleteArray($id, $url, $htmlOptions = array())
    {
        Yii::app()->clientScript->registerScriptFile('/js/jquery.tokeninput.js');
        $theme = isset($htmlOptions['theme']) ? 'theme: "' . $htmlOptions['theme'] . '",' : '';
        $customAdded = isset($htmlOptions['onCustomAdded'])
            ? 'onCustomAdded: function(item) { ' . $htmlOptions['onCustomAdded'] . ' },' : '';
        $allowCustom = isset($htmlOptions['onCustomAdded']) ? 'allowCustomEntry:true,' : '';
        $onAdd = isset($htmlOptions['onAdd']) ? 'onAdd: function(item) { ' . $htmlOptions['onAdd'] . ' },' : '';
        $onDelete = isset($htmlOptions['onDelete'])
            ? 'onDelete: function(item) { ' . $htmlOptions['onDelete'] . ' },' : '';

        $this->attributes[$id] = array(CKnockoutForm::TYPE_ARRAY, null);

        unset($htmlOptions['theme']);
        unset($htmlOptions['onCustomAdded']);
        unset($htmlOptions['onAdd']);
        unset($htmlOptions['onDelete']);

        $js = <<<EOE
        <script type="text/javascript">
            $(document).ready(function()
            {
                $('#$id').tokenInput('$url',
                        {
                            $theme
                            preventDuplicates:true,
                            $allowCustom
                            $customAdded
                            $onAdd
                            $onDelete
                        });
EOE;
        $htmlOptions['id'] = $id;
        echo CHtml::textField('', '', $htmlOptions);

        if (isset($this->model->$id) && !empty($this->model->$id) && count($this->model->$id) > 0)
        {
            $data = $this->model->$id;
            $js .= '$.each([' . $data . '], function (a, v) { '
                . ' $("#' . $id . '").tokenInput("add", v); '
                . '});';
        }

        $js .= '});</script>';
        echo $js;
    }

    public function autoCompleteField($model, $attribute, $url, $htmlOptions = array())
    {
        Yii::app()->clientScript->registerScriptFile('/js/jquery.tokeninput.js');
        CHtml::resolveNameID($model, $attribute, $htmlOptions);
        $id = $htmlOptions['id'];
        $this->attributes[$attribute] = '';
        $theme = isset($htmlOptions['theme']) ? 'theme: "' . $htmlOptions['theme'] . '",' : '';
        $onlyOne = (isset($htmlOptions['onlyOne']) && $htmlOptions['onlyOne']) ? 'tokenLimit: 1, ' : '';
        $customAdded = isset($htmlOptions['onCustomAdded'])
            ? 'onCustomAdded: function(item) { ' . $htmlOptions['onCustomAdded'] . ' },' : '';

        $js = <<<EOE
<script type="text/javascript">
    $(document).ready(function()
    {
        $('#$id').tokenInput('$url',
                {
                    $theme
                    $onlyOne
                    preventDuplicates:true,
                    allowCustomEntry: true,
                    $customAdded
                    onAdd : function(item)
                    {
                        $this->viewModel.$attribute(item.id);
                    },
                    onDelete : function(item)
                    {
                        $this->viewModel.$attribute(null);
                    }
                });
    });
</script>
EOE;
        echo CHtml::activeTextField($model, $attribute, $htmlOptions);
        echo $js;
    }

    public static function GetAttributeValue($attribute)
    {
        if (!isset($_POST['json']) || empty($_POST['json'])) return null;

        $json = json_decode($_POST['json']);
        $arr = self::object2array($json);
        return isset($arr[$attribute]) ? $arr[$attribute] : null;
    }

    public static function validate($model, $attributes = null)
    {
        $result = array();
        if (empty($_POST['json']))
        {
            $result['HasValidationErrors'] = true;
            $result['error'] = 'Empty request';
            $model->addError('GeneralError', 'EmptyRequest');
            return $result;
        }

        $json = json_decode($_POST['json']);
        $arr = self::object2array($json);
        $model->attributes = $arr;
        $objAttributes = $model->attributes;
        foreach ($objAttributes as $attr=>$value)
        {
            if (isset($model->$attr) && $model->isAttributeSafe($attr) && isset($arr[$attr]))
            {
                $model->$attr = is_array($arr[$attr]) ? $arr[$attr] : trim($arr[$attr]);
            }
        }

        $model->validate($attributes);

        if ($model->hasErrors())
        {
            $result['HasValidationErrors'] = true;
            foreach ($model->getErrors() as $attribute => $errors)
            {
                $result['error'][$attribute] = $errors;
            }
        }
        else
        {
            $result['HasValidationErrors'] = false;
        }
        return $result;
    }

    private function SetupDataBind($attribute = null, $htmlOptions = array(), $isFile = false)
    {
        $dataBind = isset($htmlOptions['data-bind']) ? $htmlOptions['data-bind'] : array();
        if (!empty($attribute))
        {
            if(isset($dataBind['valueWithInit']) && $dataBind['valueWithInit'])
            {
                $dataBind['valueWithInit'] = $attribute;
            }
            else if($isFile)
            {
                $dataBind['file'] = $attribute;
//                $dataBind['fileObjectURL'] = $attribute.'ObjectURL';
//                $dataBind['fileBinaryData'] = $attribute.'ImageBinary';
            }
            else $dataBind['value'] = $attribute;

            if (isset($dataBind['checked'])) unset($dataBind['value']);

            if ($this->inputErrorClass !== false)
            {
                $dataBind['css'][$this->inputErrorClass] = "hasErrors('" . $attribute . "')";
            }
        }

        return $this->JoinDataBind($dataBind);
    }

    public static function AssignAttributes($model, $data)
    {
        $obj = json_decode($data);
        $attributes = $model->attributeNames();
        foreach ($attributes as $key => $type) if (isset($obj->$key)) $model->$key = $obj->$key;
    }

    public static function AsJsObject($obj, $keyName = 'id', $valName = 'name')
    {
        $r = array();
        foreach ($obj as $id => $value)
        {
            if (gettype($value) == 'object')
            {
                $r[] = '{ id : ' . $value->$keyName . ', name : "' . $value->$valName . '"}';
            }
            else if (isset($value[$keyName]) && isset($value[$valName]))
            {
                $r[] = '{ id : ' . $value[$keyName] . ', name : "' . $value[$valName] . '"}';
            }
            else
            {
                $r[] = '{' . $keyName . ': ' . $id . ', ' . $valName . ': "' . $value . '"}';
            }
        }

        return implode(', ', $r);
    }


    public static function PerformKnockoutValidation($model, $form, $asFormData=false)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $form)
        {
            if($asFormData)
            {
                $model->attributes = $_POST;
                $model->validate();
                $ret['HasValidationErrors'] = $model->hasErrors();
                foreach ($model->getErrors() as $attribute => $errors)
                {
                    $ret['error'][$attribute] = $errors;
                }
            }
            else
            {
                $ret = CKnockoutForm::validate($model);
            }

            if ($ret['HasValidationErrors'])
            {
                echo json_encode($ret);
                Yii::app()->end();
            }
            return true;
        }
        return false;
    }

    public static function MapToJsObject($name, $select, $idKey, $nameKey)
    {
        $ret = 'var '.$name.' = [';
        foreach($select as $s)
        {
            $r[] = '{ id: ' . $s[$idKey] . ', name: "' . str_replace('"', '\"', $s[$nameKey]) . '"}';
        }

        $ret .= implode(', ', $r);

        $ret .= '];'."\n";
        return $ret;
    }

}
