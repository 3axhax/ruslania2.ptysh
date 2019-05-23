<?php

class OrderController extends MyController
{
    public function actionView($oid)
    {
        $o = new Order;
        $order = $o->GetOrder($oid);
        if(!empty($order) && $order['uid'] != $this->uid) {
            $this->redirect(Yii::app()->createUrl('site/index'));
            throw new CException('NotYourOrder');
        }
        if(empty($order)) throw new CHttpException(404);

        $this->breadcrumbs[Yii::app()->ui->item("A_LEFT_PERSONAL_ORDERS")] = Yii::app()->createUrl('my/orders');
        $this->breadcrumbs[] = sprintf(Yii::app()->ui->item('ORDER_MSG_NUMBER'), $order['id']);
        $this->render('view', array('order' => $order));
    }
	
	public function actionPayTrail() {
		
		
		
		$order = new Order;
		
		$order = $order->GetOrder(7059899);
		
		$this->widget('PayTrailWidget', array('order' => $order));
		
		
		
		
	}

    public function actionChangePaymentType()
    {
        $type = intVal(@$_POST['type']);
        $oid = intVal(@$_POST['id']);

        $o = new Order;
        $o->ChangeOrderPaymentType($this->uid, $oid, $type);
    }

    public function actionCreateRequest()
    {
        $c = new Cart;
        $items = $c->GetRequest($this->uid, $this->sid);
        if(empty($items))
        {
            Yii::app()->user->setFlash('request', 'EmptyRequest');
            $this->redirect(Yii::app()->createUrl('cart/dorequest'));
        }

        $r = new Request();
        $rid = $r->CreateNewRequest($this->uid, $items, @$_POST['Notes']);

        if($rid > 0)
        {
            Yii::app()->user->setFlash('request', 'RequestCreated');
            $this->redirect(Yii::app()->createUrl('client/requests'));
        }
        else $this->redirect(Yii::app()->createUrl('cart/dorequest'));
    }

    public function actionCreate()
    {
        if(Yii::app()->request->isPostRequest)
        {
            $order = new OrderForm($this->sid);
            $order->attributes = $_POST;

            if(!$order->validate())
            {
                $retErrors = array();
                $errors = $order->getErrors();
                foreach($errors as $attr=>$error)
                    foreach($error as $e) $retErrors[] = $e;

                $this->ResponseJson(array('hasError' => true, 'Errors' => $retErrors));
            }

            $c = new Cart;
            $tmp = $c->GetCart($this->uid, $this->sid);

            $beautyItems = $c->BeautifyCart($tmp, $this->uid);
            $items = array();
            foreach($tmp as $item)
            {
                if(ProductHelper::IsAvailableForOrder($item))
                    $items[] = $item;
            }

            if(empty($items))
                $this->ResponseJson(array('hasError' => true, 'Errors' => array('EmptyCart')));

            $o = new Order;
            $id = $o->CreateNewOrder($this->uid, $this->sid, $order, $items);

            if($id > 0)
            {
                $user = User::model()->findByPk(Yii::app()->user->id);
                $order = Order::model()->findByPk($id);
                $message = new YiiMailMessage(sprintf(Yii::app()->ui->item('MSG_ORDER_PRINT_PAGE_TITLE'), $id));
                $message->view = 'thanks_for_order';
                $message->setBody(array(
                    'items' => $beautyItems,
                    'user' => $user->attributes,
                    'order' => $order->attributes,
                ), 'text/html');
                $message->addTo($user['login']);
                $message->from = 'noreply@ruslania.com';
                @Yii::app()->mail->send($message);

                Yii::app()->user->setFlash('order', Yii::app()->ui->item('ORDER_MSG_DONE'));
                $this->ResponseJson(array('hasError' => false,
                                          'url' => Yii::app()->createUrl('client/pay', array('oid' => $id))
                                    ));
            }
            else
            {
                $this->ResponseJson(array('hasError' => true, 'Errors' => array('ErrorInDb')));
            }
        }

        $this->redirect(Yii::app()->createUrl('cart/view'));
    }
}