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
    protected $_canonicalPath = null;//адрес страницы canonical
    protected $_otherLangPaths = array();
    protected $_maxPages = false;// признак, что на странице есть пагинация (false - нету, число - количество страниц)

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

//        $this->_langSet();

        $currency = Currency::EUR;

//        if (in_array(Yii::app()->language, array('ru', 'rut', 'en'))) $currency = Currency::USD; //валюта по умолчанию для России и Англии

        if (Yii::app()->getRequest()->cookies['showSelLang']->value) {
            if(isset($_GET['currency'])) $currency = intVal($_GET['currency']);
            else if(Yii::app()->user->hasState('currency')) $currency = Yii::app()->user->getState('currency');
            else if(isset(Yii::app()->request->cookies['currency'])) $currency = Yii::app()->request->cookies['currency']->value;
            if(!in_array($currency, Currency::GetList())) $currency = Currency::EUR;

            Yii::app()->user->setState('currency', $currency);
            $cookie = new CHttpCookie('currency', $currency);
            $cookie->expire = time() + (60*60*24*365); // (1 year)
            Yii::app()->request->cookies['currency'] = $cookie;
        }

        Yii::app()->currency = $currency;
    }

    private function _langSet() {
        $lang = Yii::app()->params['DefaultLanguage'];
        $params = $this->getActionParams();

        if (!empty($params['language'])) {
            $lang = $params['language'];
        }
        elseif (isset($_GET['language'])) {
            $lang = $_GET['language'];
        }
        elseif (Yii::app()->user->hasState('language')) {
            $lang = Yii::app()->user->getState('language');
        }
        elseif (isset(Yii::app()->request->cookies['v2language'])) {
            $lang = Yii::app()->request->cookies['v2language']->value;
        }

        $validLangs = Yii::app()->params['ValidLanguages'];
        if(!in_array($lang, $validLangs)) $lang = Yii::app()->params['DefaultLanguage'];

        $this->SetNewLanguage($lang);
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
        if (is_array($this->breadcrumbs)) {
            $title  = array();
            foreach($this->breadcrumbs as $idx=>$data)
            {
                if(is_numeric($idx)) $title[] = $data;
                else $title[] = $idx;
            }
            if(empty($this->pageTitle))
            {
                $this->pageTitle = implode(' &gt; ', $title);
                if (($this->_maxPages !== false)&&(($page = (int) Yii::app()->getRequest()->getParam('page')) > 1)) {
                    $this->pageTitle .= ' &ndash; ' . Yii::app()->ui->item('PAGES_N', $page);
                }
                $this->pageTitle .= ' &ndash; ' . Yii::app()->ui->item('RUSLANIA');
            }
            if (empty($this->pageDescription)) {
                $this->pageDescription = implode(' &gt; ', $title);
                if (($this->_maxPages !== false)&&(($page = (int) Yii::app()->getRequest()->getParam('page')) > 1)) {
                    $this->pageDescription .= ' &ndash; ' . Yii::app()->ui->item('PAGES_N', $page);
                }
            }
            if (empty($this->pageKeywords)) {
                $this->pageKeywords = implode(' ', $title);
            }
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
        if ($status == 'sale') echo '<div class="status-block'.$size.' sale">'.Yii::app()->ui->item('IN_SALE').'</div>';
        if ($status == 'new') echo '<div class="status-block'.$size.' new">'.Yii::app()->ui->item('IN_RECOMMEND').'</div>';
        if (!$isOffer && ($status == 'recommend')) echo '<div class="status-block'.$size.' rec">'.Yii::app()->ui->item('IN_OFFERS').'</div>';
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

    function getCanonicalPath() {
        //если есть постраничный вывод, то не показываем canonical для гугла
        if (($this->_maxPages !== false)&&($this->_getUserAgent() === 'google')) return '';
        $curPage = (int) Yii::app()->getRequest()->getParam('page');
        //в задании про canonical написано сделать на всех страницах
        //а взадании про пагинацию на страницах начаная со второй
//        if (($this->_maxPages !== false)&&($curPage < 2)) return '';

        return $this->_canonicalPath;
    }

    function getOtherLangPaths() { return $this->_otherLangPaths; }

    /**
     * @return array ('next'=>адрес следующей страницы, 'prev'=>адрес предыдущей страницы) или пустой массив если не надо
     */
    function getNextPrevPath() {
        if (empty($this->_canonicalPath)) return array();
        if ($this->_maxPages === false) return array();

        $path = urldecode(getenv('REQUEST_URI'));
        $ind = mb_strpos($path, "?", null, 'utf-8');
        $q = ($ind === false)?'':mb_substr($path, $ind, null, 'utf-8');
        unset($path);

        $paths = array();
        $query = $_GET;
        foreach ($query as $k=>$v) {
            //убираю параметры, которые кто-то зачем-то в скриптах положил в $_GET
            if (!preg_match("/\b" . $k . "\b/ui", $q)) unset($query[$k]);
            //пустые параметры тоже уберу, их не доложно быть
            elseif ($v === '') unset($query[$k]);
            //эти параметры в куках, убираю
            elseif (in_array($k, array('avail', 'currency', 'language'))) unset($query[$k]);
        }
        $curPage = 1;
        if (isset($query['page'])) $curPage = max($curPage, (int)$query['page']);
        if ($this->_maxPages > 1) $curPage = min($curPage, $this->_maxPages);
        $nextPage = $curPage + 1;
        $prevPage = $curPage - 1;
        if ($prevPage === 1) {
            unset($query['page']);
            if (empty($query)) $paths['prev'] = $this->_canonicalPath;
            else $paths['prev'] = $this->_canonicalPath . '?' . http_build_query($query);
        }
        elseif ($prevPage > 1) {
            $query['page'] = $prevPage;
            $paths['prev'] = $this->_canonicalPath . '?' . http_build_query($query);
        }
        if ($nextPage < $this->_maxPages) {
            $query['page'] = $nextPage;
            $paths['next'] = $this->_canonicalPath . '?' . http_build_query($query);
        }
        return $paths;
    }

    protected function _getUserAgent() {
        $ua = mb_strtolower(getenv('HTTP_USER_AGENT'), 'utf-8');
        if (mb_strpos($ua, 'yandex', null, 'utf-8')) return 'yandex';
        if (mb_strpos($ua, 'google', null, 'utf-8')) return 'google';
        return '';
    }

    /** функция запускается, если адрес, с которого зашли на страницу не соответствует адресу, который должен быть (реальный адрес)
     * на 04.06.18 старыми адресами считаются
     * - адреса заканчивающиеся на ".html" или
     * - адреса заканчивающиеся не на "/"
     * - адреса без наименования
     * реальные адреса заканчиваются на "/"
     *
     * 16.07.18
     * есть таблица seo_href_titles там подготевленные title для разных языков для формирования путей.
     * Для страницы предполагается однозначный путь, даже если поменяется наименование
     * в таблице seo_old_href_titles старые названия, на случай если иземяется title из таблицы seo_href_titles
     *
     * функция делает редирект на реальный адрес, если старый адрес похож на реальный
     * @param $oldPage
     * @param $realPage
     * @param $query
     */
    protected function _redirectOldPages($oldPage, $realPage, $query, $data = array()) {
//        $this->redirect($realPage . $query, true, 301);
//        return;

        if (mb_substr($oldPage, -5, null, 'utf-8') === '.html') $oldPage = mb_substr($oldPage, 0, -5, 'utf-8') . '/';
        elseif (mb_substr($oldPage, -1, null, 'utf-8') !== '/') $oldPage = $oldPage . '/';
        elseif (preg_match("/(\d+)\/?$/", $oldPage)&&(mb_strpos($realPage, mb_substr($oldPage, 0, -1, 'utf-8'), null, 'utf-8') !== false)) $oldPage = $realPage;

        if ($oldPage === $realPage) $this->redirect($realPage . $query, true, 301);

        $route = $this->id . '/' . $this->action->id;
        if (!empty($data['entity'])) {
            $entity = $data['entity'];
            if (is_numeric($entity)) $data['entity'] = Entity::GetUrlKey($entity);
            else $entity = Entity::ParseFromString($entity);
            $idName = HrefTitles::get()->getIdName($entity, $route);
            Debug::staticRun(array($idName, $data));
            if (!empty($idName)&&!empty($data[$idName])) {
                $data['__useTitleParams'] = true;
                foreach (HrefTitles::get()->getOldNames($entity, $route, $data[$idName], Yii::app()->language) as $oldTitle) {
                    $data['title'] = $oldTitle;
                    $path = Yii::app()->createUrl($route, $data);
                    if ($path === $oldPage) $this->redirect($realPage . $query, true, 301);
                }
            }
        }
    }

}