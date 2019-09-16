<?php

class OffersController extends MyController
{
    public function actionSpecial($mode)
    {
        $urlData = ['mode'=>$mode];
        $this->_checkUrl($urlData);
        $o = new Offer;
        switch($mode)
        {
            case 'firms': $oid = Offer::FIRMS; break;
            case 'uni' : $oid = Offer::UNI; break;
            case 'lib' : $oid = Offer::LIBRARY; break;
            case 'fs' : $oid= Offer::FREE_SHIPPING; break;
            case 'alle2': $oid = Offer::ALLE_2_EURO; break;
            default : $oid = Offer::INDEX_PAGE; break;
        }

/*        $titles = array('firms' => 'A_OFFERS_FRMS',
                        'lib' => 'A_OFFERS_LIBS',
                        'uni' => 'A_OFFERS_UNIVERCITY',
                        'fs' => 'FREE_SHIPPING_OFFER',
                        'alle2' => 'OFFER_ALLE_2',
        );


        $title = Yii::app()->ui->item('A_OFFERS').Yii::app()->ui->item($titles[$mode]);*/

        $offer = $o->GetOffer($oid, true, true);
        $title = ProductHelper::GetTitle($offer);

//        $this->breadcrumbs[Yii::app()->ui->item('RUSLANIA_RECOMMENDS')] = Yii::app()->createUrl('offers/list');
        $this->breadcrumbs[] = $title;
//        $groups = $o->GetItems($oid);
//        $this->render('view', array('offer' => $offer, 'groups' => $groups));
        $eid = (int) Yii::app()->getRequest()->getParam('eid');
        if ($eid <= 0) $eid = 0;
        $o = OfferItem::model();
        list($groups, $paginator) = $o->getList($oid, $eid, (in_array($oid, array(777, 999))&&($eid === 0)));
        $this->render('view', array('offer' => $offer, 'groups' => $groups, 'entitys'=>$o->getEntitys($oid), 'paginator' => $paginator, 'url'=>Yii::app()->createUrl('offers/' . $this->action->id, $urlData)));
    }

    public function actionList()
    {
        $this->_checkUrl([]);
        $o = new Offer;
        $list = $o->GetList();
        $this->breadcrumbs[] = Yii::app()->ui->item('RUSLANIA_RECOMMENDS');

        $this->_maxPages = ceil($list['Paginator']->getItemCount()/$list['Paginator']->getPageSize());
        $this->render('list', array('list' => $list['Items'], 'paginator' => $list['Paginator']));
    }

    public function actionView($oid) {
        if(empty($oid)) $this->redirect(Yii::app()->createUrl('offers/list'));

        $urlData = ['oid' => $oid];
		$this->_checkUrl($urlData);
		
		$mode = '';
        switch($oid)
        {
            case Offer::FIRMS : $mode = 'firms'; break;
            case Offer::UNI: $mode = 'uni'; break;
            case Offer::LIBRARY; $mode = 'lib'; break;
            case Offer::INDEX_PAGE : $mode = 'index'; break;
            case Offer::FREE_SHIPPING : $mode = 'fs'; break;
            case Offer::ALLE_2_EURO : $mode = 'alle2'; break;
        }
		
		if(!empty($mode))
        {
            if($mode == 'index')
                $this->redirect('/');

            $url = Yii::app()->createUrl('offers/special', array('mode' => $mode));
            $this->redirect($url);
        }

        $o = new Offer;
        $offer = $o->GetOffer($oid, false, true);
        if(empty($offer)) throw new CHttpException(404);

        $this->breadcrumbs[Yii::app()->ui->item('RUSLANIA_RECOMMENDS')] = Yii::app()->createUrl('offers/list');
        $this->breadcrumbs[] = ProductHelper::GetTitle($offer);

		list($groups, $paginator) = OfferItem::model()->getList($oid);
        $this->render('view', array('offer' => $offer, 'groups' => $groups, 'paginator' => $paginator, 'url'=>Yii::app()->createUrl('offers/' . $this->action->id, $urlData)));
    }

    public function actionDownload($oid)
    {
        if(empty($oid)) $this->redirect(Yii::app()->createUrl('offers/list'));
        $o = new Offer;
        $offer = $o->GetOffer($oid, true, true);
        if(empty($offer)) throw new CHttpException(404);

        $groups = $o->GetItemsExport($oid);

        Yii::import('application.extensions.excel.Excel');
        $exporter = new ExportDataExcel('browser', $oid . '.xls');
        $exporter->initialize();
        $exporter->addRow(array('RuslaniaID', 'EAN', 'authors_ru', 'authors_en', 'title_ru', 'title_en',
                                'publisher',
                                'isbn', 'binding ', 'language', 'FIN Library code',
                                'BIC code',
                                'categories',
                                'price (VAT0)', 'pages', 'series', 'link'
                          ));
//
        $langList = Language::GetItemsLanguageList();

        Yii::app()->language = 'en';
        foreach($groups as $group=>$data)
        {
            $exporter->addRow(array(Entity::GetTitle($data['entity'])));
            foreach($data['items'] as $item)
            {
                $authorsRU = '';
                $authorsEN = '';

                if(isset($item['Authors']) && count($item['Authors']) > 0)
                {
                    $aRu = array();
                    $aEn = array();
                    foreach($item['Authors'] as $author)
                    {
                        $aRu[] = $author['title_ru'];
                        $aEn[] = $author['title_en'];
                    }

                    $authorsRU = implode(', ', $aRu);
                    $authorsEN = implode(', ', $aEn);
                }

                $languages = '';
                if(isset($item['Languages']) && count($item['Languages']) > 0)
                {
                    $langs = array();
                    foreach($item['Languages'] as $lang)
                        $langs[] = $langList[$lang['language_id']]['title_en'];

                    $languages = implode(', ', $langs);
                }


                $cat = array();
                if (!empty($item['Category'])) $cat[] = $item['Category'];
                if (!empty($item['SubCategory'])) $cat[] = $item['SubCategory'];
                $finCodes = array();
                $categories = array();
                $bicCodes = array();
                foreach($cat as $c)
                {
                    if(!empty($c['fin_codes'])) $finCodes[] = $c['fin_codes'];
                    if(!empty($c['BIC_categories'])) $bicCodes[] = $c['BIC_categories'];
                    $categories[] = $c['title_en'];
                }

                $libCode = implode(', ', $finCodes);
                $category = implode(', ', $categories);
                $bicCode = implode(', ', $bicCodes);

                $bruttoALV0 = round(($item['brutto'] * 100) / (100+$item['vat']), 2);

                $row = array(
                    $item['id'],
                    isset($item['eancode']) ? $item['eancode'] : '',
                    $authorsRU,
                    $authorsEN,
                    isset($item['title_ru']) ? $item['title_ru'] : '',
                    isset($item['title_en']) ? $item['title_en'] : '',
                    isset($item['Publisher']['title_en']) ? $item['Publisher']['title_en'] : '',
                    isset($item['isbn']) ? $item['isbn'] : '',
                    isset($item['Binding']['title_en']) ? $item['Binding']['title_en'] : '',
                    $languages,
                    $libCode,
                    $bicCode,
                    $category,
                    $bruttoALV0,
                    isset($item['numpages']) ? $item['numpages'] : '',
                    isset($item['Series']['title_en']) ? $item['Series']['title_en'] : '',
                    Yii::app()->createAbsoluteUrl('product/view',
                        array('entity' => Entity::GetUrlKey($item['entity']), 'id' => $item['id']))
                );

                $exporter->addRow($row);
            }
        }
        $exporter->finalize();
        exit;
   }


    /** функция сравнивает адрес страниц (которая должна быть и с которой реально зашли)
     * если совпадают, то возвращаю false
     * иначе редирект или 404
     * @param array $data параметры для формирования пути
     */
    private function _checkUrl($data, $langTitles = array()) {
        $path = getenv('REQUEST_URI');
        $ind = mb_strpos($path, "?", null, 'utf-8');
        $query = '';
        if ($ind !== false) {
            $query = mb_substr($path, $ind, null, 'utf-8');
            $query = preg_replace("/\blang=\d+\b/ui", '', $query);
            $query = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $query);
            if ($query === '?') $query = '';

            $path = mb_substr($path, 0, $ind, 'utf-8');
        }
        $typePage = $this->action->id;
        $this->_canonicalPath = Yii::app()->createUrl('offers/' . $typePage, $data);
		
		if ((mb_strpos($this->_canonicalPath, '?') !== false)&&!empty($query)) $query = '&' . mb_substr($query, 1, null, 'utf-8');

        foreach (Yii::app()->params['ValidLanguages'] as $lang) {
            if ($lang !== 'rut') {
                if ($lang === Yii::app()->language) $this->_otherLangPaths[$lang] = $this->_canonicalPath;
                else {
                    $_data = $data;
                    if (isset($data['title'])&&isset($langTitles[$lang])) $_data['title'] = $langTitles[$lang];
                    $_data['__langForUrl'] = $lang;
                    $this->_otherLangPaths[$lang] = Yii::app()->createUrl('offers/' . $typePage, $_data);
                }
            }
        }

        $canonicalPath = $this->_canonicalPath;
        $ind = mb_strpos($canonicalPath, "?", null, 'utf-8');
        if ($ind !== false) {
            $canonicalPath = mb_substr($canonicalPath, 0, $ind, 'utf-8');
        }

        if ($canonicalPath === $path) {
            //редирект с page=1
            $countPage1 = 0;
            $query = preg_replace("/\bpage=1\b/ui", '', $query, -1, $countPage1);
            if ($countPage1 > 0) {
                $query = preg_replace(array("/[&]{2,}/ui", "/\?&/ui"), array('&', '?'), $query);
                if ($query === '?') $query = '';
                $this->redirect($this->_canonicalPath . $query, true, 301);
            }
            //редирект с page=1
            return;
        }

        $this->_redirectOldPages($path, $this->_canonicalPath, $query, $data);
        throw new CHttpException(404);

    }

}