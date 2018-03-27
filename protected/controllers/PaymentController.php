<?php

// http://docs.paytrail.com/files/payment-api-en.pdf

// OKO: http://ruslania/payment/accept/oid/1033492/tid/6?VERSIO=1&MAKSUTUNNUS=1033492&VIITE=10334928&ARKISTOINTITUNNUS=20130805593731FC1810&TARKISTE-VERSIO=1&TARKISTE=6B51B8016E75275250EA79A0D78A75EB
//'VERSIO' => string '1' (length=1)
//  'MAKSUTUNNUS' => string '1033492' (length=7)
//  'VIITE' => string '10334928' (length=8)
//  'ARKISTOINTITUNNUS' => string '20130805593731FC1810' (length=20)
//  'TARKISTE-VERSIO' => string '1' (length=1)
//  'TARKISTE' => string '6B51B8016E75275250EA79A0D78A75EB' (length=32)
//  'oid' => string '1033492' (length=7)
//  'tid' => string '6' (length=1)

class PaymentController extends MyController
{
    public function actionAccept($oid, $tid)
    {
        $o = new Order;
        $order = $o->GetOrder($oid);
        if(empty($order)) throw new CHttpException(404);

        $check = Payment::CheckPayment($oid, $tid, $_REQUEST, $order);
        $ret = 0;

        $view = 'cancel';
        if($check)
        {
            $view = 'accept';
            $uid = Yii::app()->user->id;
            $o->ChangeOrderPaymentType($uid, $oid, $tid);
            $ret = $o->AddStatus($oid, OrderState::AutomaticPaymentConfirmation);
            if(empty($ret))
            {
                CommonHelper::Log('Payment status not added '.$oid.' - '.$tid);
            }
            else if($ret == -1)
            {
                CommonHelper::Log('Payment already exists '.$oid.' - '.$tid, 'mywarnings');
            }
        }

        if($order['uid'] != $this->uid) throw new CException('Wrong order id');

        $this->breadcrumbs[Yii::app()->ui->item('ORDER_PAYMENT')] = Yii::app()->createUrl('client/pay', array('oid' => $oid));
        $this->breadcrumbs[] = $check
                                ? Yii::app()->ui->item('A_SAMPO_PAYMENT_ACCEPTED')
                                : Yii::app()->ui->item('A_SAMPO_PAYMENT_DECLINED');
        $this->render($view, array('checkResult' => $check, 'statusAdded' => $ret, 'order' => $order));
    }

    public function actionCancel($oid, $tid)
    {
        $o = new Order;
        $order = $o->GetOrder($oid);
        if(empty($order)) throw new CHttpException(404);

        if($order['uid'] != $this->uid) throw new CException('Wrong order id');

        $newOid = null;
        if($tid == Payment::Luottokunta && isset($_GET['LKPRC']) && $_GET['LKPRC'] == 300)
        {
            // http://ruslania2.com/payment/cancel/oid/7003779/tid/3?LKPRC=300
            $newOid = $o->RegenerateOrder($oid);
        }

        $this->breadcrumbs[Yii::app()->ui->item('ORDER_PAYMENT')] = Yii::app()->createUrl('client/pay', array('oid' => $oid));
        $this->breadcrumbs[] = Yii::app()->ui->item('A_SAMPO_PAYMENT_DECLINED');
        $this->render('cancel', array('order' => $order, 'newOid' => $newOid));
    }

    public function actionNotify()
    {
        $get = "NOTIFY_PAYTRAIL\n".print_r($_GET, true);
        Yii::log($get);
    }
}