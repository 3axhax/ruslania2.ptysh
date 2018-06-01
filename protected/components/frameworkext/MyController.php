<?php

class MyController extends CController
{
    public $breadcrumbs = array();
    public $pageTitle;
    public $pageKeywords;
    public $pageDescription;
    protected $uid = 0;
    protected $sid = 0;
    protected $sessionID = 0;

    public function GetAvail($avail)
    {
        if(array_key_exists('avail', $_GET))
        {
            $avail = intVal($_GET['avail']) ? true : false;
        }
        else
        {
            $availCookie = Yii::app()->request->cookies['avail'];
            if(!empty($availCookie))
            {
                $avail = $availCookie->value ? true : false;
            }
            else
            {
                $avail = true;
            }
        }

        $options['expire'] = time()+(60*60*24*30);
        Yii::app()->request->cookies['avail'] = new CHttpCookie('avail', $avail ? 1 : 0, $options);
        $_GET['avail'] = $avail ? 1 : 0;

        return $avail;

        return $avail;

        var_dump($availCookie);
        if(!empty($availCookie)) $avail = $availCookie->value ? true : false;
        else $avail = true;

        $avail = empty($avail) ? false : true;
        $_GET['avail'] = $avail ? 1 : 0;
        return $avail;
    }

    protected function SetNewLanguage($lang)
    {
        Yii::app()->language = $lang;
        Yii::app()->user->setState('language', $lang);
        $cookie = new CHttpCookie('v2language', $lang);
        $cookie->expire = time() + (60*60*24*365); // (1 year)
        Yii::app()->request->cookies['v2language'] = $cookie;
    }

    public function __construct($id, $module = null)
    {
        parent::__construct($id, $module);

        $lang = Yii::app()->params['DefaultLanguage'];

        if (isset($_GET['language']))
        {
            $lang = $_GET['language'];
        }
        else if (Yii::app()->user->hasState('language'))
        {
            $lang = Yii::app()->user->getState('language');
        }
        else if (isset(Yii::app()->request->cookies['v2language']))
        {
            $lang = Yii::app()->request->cookies['v2language']->value;
        }

        $validLangs = Yii::app()->params['ValidLanguages'];
        if(!in_array($lang, $validLangs)) $lang = Yii::app()->params['DefaultLanguage'];

        $this->SetNewLanguage($lang);

        $currency = Currency::EUR;
        if(isset($_GET['currency'])) $currency = intVal($_GET['currency']);
        else if(Yii::app()->user->hasState('currency')) $currency = Yii::app()->user->getState('currency');
        else if(isset(Yii::app()->request->cookies['currency'])) $currency = Yii::app()->request->cookies['currency']->value;
        if(!in_array($currency, Currency::GetList())) $currency = Currency::EUR;

        Yii::app()->user->setState('currency', $currency);
        $cookie = new CHttpCookie('currency', $currency);
        $cookie->expire = time() + (60*60*24*365); // (1 year)
        Yii::app()->request->cookies['currency'] = $cookie;
        Yii::app()->currency = $currency;
    }

    public function filters()
    {
        return array('accessControl',
                     array('application.components.frameworkext.PostFilter')
        );
    }

    public function beforeRender($view)
    {
        //if(empty($this->pageTitle))
        if(empty($this->pageTitle) && is_array($this->breadcrumbs))
        {
            $title  = array();
            foreach($this->breadcrumbs as $idx=>$data)
            {
                if(is_numeric($idx)) $title[] = $data;
                else $title[] = $idx;
            }
            $this->pageTitle = implode(' &gt; ', $title);
        }
        return true;
    }

    public function beforeAction($action)
    {
        $this->uid = Yii::app()->user->id;
        $session = Yii::app()->session;
        $this->sessionID = $session->sessionID;
		if(!isset($session['shopcartkey']))
        {
            $salt = 'someRuslaniaSalt';
            $key = hash('sha256', uniqid(microtime(), true).$salt);
			$session['shopcartkey'] = $key;
        }
				
        $this->sid = $session['shopcartkey'];
        return true;
    }

    public function afterAction($action)
    {
        if (Yii::app()->user->isGuest)
        {
            $uri = Yii::app()->request->requestUri;
            Yii::app()->user->returnUrl = $uri;
        }
    }

    protected function ResponseJson($ret)
    {
        echo json_encode($ret);
        Yii::app()->end();
    }

    protected function ResponseJsonError($msg)
    {
        $ret = array('hasError' => true, 'error' => $msg);
        $this->ResponseJson($ret);
    }

    protected function ResponseJsonOk($msg)
    {
        $ret = array('hasError' => false, 'message' => $msg);
        $this->ResponseJson($ret);
    }


    protected function PerformAjaxValidation($model, $form)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $form)
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    protected function PerformKnockoutValidation($model, $form)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === $form)
        {
            $ret = CKnockoutForm::validate($model);
            if ($ret['HasValidationErrors'])
            {
                echo $this->ResponseJson($ret);
                Yii::app()->end();
            }
        }
        return false;
    }


    public function render($view,$data=null,$return=false)
    {
        $data['ui'] = Yii::app()->ui;
        return parent::render($view, $data, $return);
    }

    public function renderPartial($view,$data=null,$return=false,$processOutput=false)
    {
        $data['ui'] = Yii::app()->ui;
        return parent::renderPartial($view,$data,$return,$processOutput);
    }

    // вывод ярлычков
    public function renderStatusLables($status, $size = '', $isOffer = false)
    {
        if ($status == 'sale') echo '<div class="status-block'.$size.' sale">Акция</div>';
        if ($status == 'new') echo '<div class="status-block'.$size.' new">Новинка!</div>';
        if (!$isOffer && ($status == 'recommend')) echo '<div class="status-block'.$size.' rec">В подборке</div>';
    }

    public function getPreferLanguage()
    {
        if (($list = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']))) 
        {
            if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/', $list, $list)) 
            {
                $language = array_combine($list[1], $list[2]);
                foreach ($language as $n => $v)
                    $language[$n] = $v ? $v : 1;
                arsort($language);
            }
        } 
        else $language = array();
        if ($language)
        {
            foreach ($language as $lang => $value)
            {
                if (in_array(strtok($lang, '-'), Yii::app()->params['ValidLanguages'])) return strtok($lang, '-');
            }
        }
        return Yii::app()->params['DefaultLanguage'];
    }

}
