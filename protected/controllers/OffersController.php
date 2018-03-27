<?php

class OffersController extends MyController
{
    public function actionSpecial($mode)
    {
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

        $titles = array('firms' => 'A_OFFERS_FRMS',
                        'lib' => 'A_OFFERS_LIBS',
                        'uni' => 'A_OFFERS_UNIVERCITY',
                        'fs' => 'FREE_SHIPPING_OFFER',
                        'alle2' => 'OFFER_ALLE_2',
        );


        $title = Yii::app()->ui->item('A_OFFERS').Yii::app()->ui->item($titles[$mode]);

        $offer = $o->GetOffer($oid, true, true);

        $this->breadcrumbs[] = $title;
        $groups = $o->GetItems($oid);
        $this->render('view', array('offer' => $offer, 'groups' => $groups));
    }

    public function actionList()
    {
        $o = new Offer;
        $list = $o->GetList();
        $this->breadcrumbs[] = Yii::app()->ui->item('RUSLANIA_RECOMMENDS');

        $this->render('list', array('list' => $list['Items'], 'paginator' => $list['Paginator']));
    }

    public function actionView($oid)
    {
        if(empty($oid)) $this->redirect(Yii::app()->createUrl('offers/list'));
		
		//var_dump($oid);
		
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

        $groups = $o->GetItems($oid);
        //$groups = $o->GetItemsV2($oid);
        $this->render('view', array('offer' => $offer, 'groups' => $groups));
    }

    public function actionDownload($oid)
    {
        if(empty($oid)) $this->redirect(Yii::app()->createUrl('offers/list'));
        $o = new Offer;
        $offer = $o->GetOffer($oid, true, true);
        if(empty($offer)) throw new CHttpException(404);

        $groups = $o->GetItems($oid);

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
}