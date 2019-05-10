<?php
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/
class RequestController extends MyController
{
    public function actionView($rid)
    {
        $r = new Request();
        $request = $r->GetRequest($rid);
        if(!empty($request) && $request['uid'] != $this->uid) throw new CException('NotYourRequest'.$rid.'_'.$this->uid);
        if(empty($request)) throw new CHttpException(404);

        $this->breadcrumbs[Yii::app()->ui->item("A_LEFT_PERSONAL_NOTAVAIBLE_ORDERS")] = Yii::app()->createUrl('my/requests');
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('REQUEST_MSG_NUMBER'), $request['id']);
        $this->render('view', array('request' => $request));
    }

    function actionCallForm() {
        $this->renderPartial('call_form', array('model'=>SendCalls::model()));
    }
}