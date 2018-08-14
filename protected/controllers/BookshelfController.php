<?php

class BookshelfController extends MyController
{
    public function actionList()
    {
        $this->_checkUrl(array());
        $this->breadcrumbs[] = Yii::app()->ui->item('BOOKSHELF_LIST');

        $dp = new CActiveDataProvider('Bookshelf', array(
            'criteria' => array(
                'condition' => 'is_visible=1',
                'order' => 'date_of DESC'
            ),
            'pagination'=>array('pageSize'=> 20),
        ));

        $this->render('list', array('dp' => $dp));
    }

    public function actionView($id)
    {
        $this->_checkUrl(array('id'=>$id));

        $model = Bookshelf::model()->findByPk($id);

        if(empty($model))
            throw new HttpException(404);

        $o = new Offer;
        $groups = $o->GetItems($model['offer_id']);

        $this->breadcrumbs[Yii::app()->ui->item('BOOKSHELF_LIST')] = Yii::app()->createUrl('bookshelf/list');
        $this->breadcrumbs[] = $model['title'];

        $this->render('view', array('model' => $model, 'groups' => $groups));;
    }

    /** функция сравнивает адрес страниц (которая должна быть и с которой реально зашли)
     * если совпадают, то возвращаю false
     * иначе редирект или 404
     * @param array $data параметры для формирования пути
     */
    private function _checkUrl($data) {
        $path = urldecode(getenv('REQUEST_URI'));
        $ind = mb_strpos($path, "?", null, 'utf-8');
        $query = '';
        if ($ind !== false) {
            $query = mb_substr($path, $ind, null, 'utf-8');
            $path = substr($path, 0, $ind);
        }
        $typePage = $this->action->id;

        $this->_canonicalPath = Yii::app()->createUrl('bookshelf/' . $typePage, $data);
//        Debug::staticRun(array($this->_canonicalPath, $path));
        foreach (Yii::app()->params['ValidLanguages'] as $lang) {
            if ($lang !== 'rut') {
                if ($lang === Yii::app()->language) $this->_otherLangPaths[$lang] = $this->_canonicalPath;
                else {
                    $_data = $data;
                    if (isset($data['title'])&&isset($langTitles[$lang])) $_data['title'] = $langTitles[$lang];
                    $_data['__langForUrl'] = $lang;
                    $this->_otherLangPaths[$lang] = Yii::app()->createUrl('bookshelf/' . $typePage, $_data);
                }
            }
        }

        if ((mb_strpos($this->_canonicalPath, '?') !== false)&&!empty($query)) $query = '&' . mb_substr($query, 1, null, 'utf-8');
        $canonicalPath = $this->_canonicalPath;
        $ind = mb_strpos($canonicalPath, "?", null, 'utf-8');
        if ($ind !== false) {
            $canonicalPath = mb_substr($canonicalPath, 0, $ind, 'utf-8');
        }
        if ($canonicalPath === $path) return;

        $this->_redirectOldPages($path, $this->_canonicalPath, $query);
        throw new CHttpException(404);

    }

}
