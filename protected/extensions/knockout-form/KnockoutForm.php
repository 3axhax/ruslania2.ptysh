<?php

class KnockoutForm extends CWidget
{
    const PLAIN = 1;
    const ARR = 2;
    const RADIO = 3;
    const SELECT = 4;

    public $htmlOptions = array();
    public $action = '';
    public $class = '';
    public $method = 'post';
    public $inputErrorClass = 'error';
    public $afterAjaxSubmit = null;
    public $beforeAjaxSubmit = null;
    public $applyBindings = true;
    public $sendAsFormData = false;
    public $disableSubmitButton = true;
    public $model = null;
    public $viewModel = 'viewModel';

    protected $computed = array();
    protected $filesField = array();
    protected $attributes = array();

    public function init()
    {
        self::RegisterScripts();
        if (!isset($this->htmlOptions['id'])) $this->htmlOptions['id'] = $this->id;
        if (!isset($this->htmlOptions['class'])) $this->htmlOptions['class'] = $this->class;
        $this->htmlOptions['data-bind'] = 'submit: onSubmit';
        if (empty($this->afterAjaxSubmit)) throw new CException('No JS function AfterAjaxSubmit defined');
        echo CHtml::beginForm($this->action, $this->method, $this->htmlOptions);
    }

    public function run()
    {
        $req = Yii::app()->request;
        $attrs = array();

        $attrMain = array();
        foreach($this->attributes as $attr=>$data)
        {
            $type = $data[0];
            $id = $data[1];

            $attrs[] = str_replace("'", "\'", $attr);
            if($type == self::PLAIN)
            {
                $attrMain[] = 'self.'.$attr." = ko.observable($('#".$id."').val());";
            }
            else if($type == self::RADIO || $type == self::SELECT)
            {
                $attrMain[] = 'self.'.$attr.' = ko.observable('.$this->model[$attr].');';
            }

        }
        $attrs = implode("', '", $attrs);
        $attrsMain = implode("\n", $attrMain);

        $beforeAjaxStart = (empty($this->beforeAjaxSubmit)) ? '' : <<<AAA
        if({$this->beforeAjaxSubmit}(self)) {
AAA;
        $beforeAjaxEnd = (empty($this->beforeAjaxSubmit)) ? '' : '}';


        $js = <<<EEE

        var $this->viewModel = function()
        {
            var self = this;
            self.csrfName = '{$req->csrfTokenName}';
            self.csrfValue = '{$req->csrfToken}';
            self.disableSubmitButton = ko.observable(false);
            self.attributes = ['{$attrs}'];
            $attrsMain
            self.errors = ko.observableArray([]);
            self.errorStr = ko.observableArray([]);
            self.hasErrors = function(id)
            {
                return ko.computed(function ()
                {
                    var ret = false;
                    $.each(self.errors(), function(idx, value) { if(value.id == id) { ret = true; return false; } });
                    return ret;
                });
            };

            self.onSubmit = function(formElement)
            {
                $beforeAjaxStart
                self.disableSubmitButton(true);
                $.when(knockoutPostObject(self, '{$this->id}', '{$this->action}'))
                 .done(function(json) { knockoutResponse(json, self, {$this->afterAjaxSubmit}); })
                 .fail(function(a, b, c) { alert(a.statusText); });
                $beforeAjaxEnd
            }
        }

        var v{$this->viewModel} = new {$this->viewModel};

EEE;

        if ($this->applyBindings)
        {
            $js .= 'ko.applyBindings(v' . $this->viewModel . ', document.getElementById("' . $this->id . '"));';
        }

        //Yii::app()->clientScript->registerScript('koVM' . $this->viewModel, $js, CClientScript::POS_END);
        echo CHtml::endForm();
        echo '<script>'.$js.'</script>';
    }

    public function hiddenField($attribute)
    {
        $this->attributes[$attribute] = array(KnockoutForm::SELECT, null);
    }

    public function radioButton($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind']['checked'] = $attribute;
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $ret = CHtml::activeRadioButton($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(KnockoutForm::RADIO, $htmlOptions['id']);
        return $ret;
    }


    public function textField($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $ret = CHtml::activeTextField($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(KnockoutForm::PLAIN, $htmlOptions['id']);
        return $ret;
    }

    public function textArea($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $ret = CHtml::activeTextArea($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(KnockoutForm::PLAIN, $htmlOptions['id'], true);
        return $ret;
    }

    public function dropDownList($attribute, $select, $htmlOptions = array())
    {
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(KnockoutForm::SELECT, $htmlOptions['id'], true);
        $valuesAttribute = $attribute . 'Select';
        $js = 'var ' . $valuesAttribute . '=[';
        $r = array();
        foreach ($select as $id => $name)
        {
            $r[] = '{ id: ' . $id . ', name: "' . $name . '"}';
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

    public function passwordField($attribute, $htmlOptions = array())
    {
        $htmlOptions['data-bind'] = $this->SetupDataBind($attribute, $htmlOptions);
        CHtml::resolveNameID($this->model, $attribute, $htmlOptions);
        $ret = CHtml::activePasswordField($this->model, $attribute, $htmlOptions);
        $this->attributes[$attribute] = array(KnockoutForm::PLAIN, $htmlOptions['id']);
        return $ret;
    }

    public function submitButton($label, $htmlOptions = array())
    {
        if ($this->disableSubmitButton) $htmlOptions['data-bind']['disable'] = 'disableSubmitButton';
        $htmlOptions['data-bind'] = $this->SetupDataBind(null, $htmlOptions);
        return CHtml::submitButton($label, $htmlOptions);
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

    public static function PerformValidation($model, $id)
    {
        $result = array('WasValidated' => false, 'HasValidationErrors' => true, 'error' => array(), 'errorStr' => array());
        if(Yii::app()->request->isPostRequest && isset($_POST['ajax']) && $_POST['ajax'] == $id)
        {
            if(!isset($_POST['fd'])) return $result;
            $fd = intVal($_POST['fd']);
            if($fd) // process FormData
            {

            }
            else // process JSON
            {
                $model->attributes = $_POST['json'];
                $objAttributes = $model->attributes;
                foreach ($objAttributes as $attr=>$value)
                {
                    if (isset($model->$attr) && $model->isAttributeSafe($attr) && isset($arr[$attr]))
                    {
                        $model->$attr = is_array($arr[$attr]) ? $arr[$attr] : trim($arr[$attr]);
                    }
                }

                $model->validate();
                $result['WasValidated'] = true;
                if(empty($model->scenario)) $result['WasValidated'] = false;

                if ($model->hasErrors())
                {
                    $result['HasValidationErrors'] = true;
                    foreach ($model->getErrors() as $attribute => $errors)
                    {
                        $result['error'][$attribute] = $errors;
                        $result['errorStr'][] = $errors;
                    }
                }
                else
                {
                    $result['HasValidationErrors'] = false;
                }
                return $result;
            }
        }
        return $result;
    }

    public static function object2array($d)
    {
        if (is_object($d))
        {
            $d = get_object_vars($d);
        }

        if (is_array($d))
        {
            foreach ($d as $key => $val)
            {
                $d[$key] = self::object2array($val);
            }
        }
        else
        {
            // Return array
            return $d;
        }
        return $d;
    }

    public static function HasErrors($ret)
    {
        return isset($ret['HasValidationErrors']) ? $ret['HasValidationErrors'] : false;
    }

    public static function RegisterScripts()
    {
        $assets = dirname(__FILE__).'/assets';
        $baseUrl = Yii::app()->assetManager->publish($assets);

        if(is_dir($assets))
        {
            Yii::app()->clientScript->registerCoreScript('jquery');
            Yii::app()->clientScript->registerScriptFile($baseUrl . '/knockout.js', CClientScript::POS_HEAD);
            Yii::app()->clientScript->registerScriptFile($baseUrl . '/knockout.mapping.js', CClientScript::POS_HEAD);
            Yii::app()->clientScript->registerScriptFile($baseUrl . '/knockoutPostObject.js', CClientScript::POS_HEAD);
        }
        else
        {
            throw new Exception('KnockoutForm - Error: Couldn\'t find assets to publish.');
        }
    }


}