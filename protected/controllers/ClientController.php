<?php
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/

class ClientController extends MyController
{

    public function accessRules()
    {
        return array(array('allow',
                           'actions' => array('memo', 'changememo', 'me', 'pay', 'subscriptions'),
                           'users' => array('*')),

                     array('allow', 'actions' => array('orders', 'requests', 'addresses', 'deleteaddress','subscriptions',
                                                       'newaddress', 'editaddress', 'data', 'printorder'),
                           'users' => array('@')),

                     array('deny',
                           'users' => array('*')));
    }

    public function actionPay($oid)
    {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/pay', $lang);
			}
		}
		
        if(Yii::app()->user->isGuest)
        {
            $this->breadcrumbs[] = Yii::app()->ui->item('ORDER_PAYMENT');
            $this->render('login_2_pay');
            return;
        }

        $o = new Order();
        $order = $o->GetOrder($oid);
        if(empty($order)) throw new CHttpException(404);

        if($order['uid'] != $this->uid) throw new CHttpException(404);

        $this->breadcrumbs[Yii::app()->ui->item('YM_CONTEXT_PERSONAL_MAIN')] = Yii::app()->createUrl('client/me');
        $this->breadcrumbs[] = Yii::app()->ui->item('ORDER_PAYMENT');
        $this->render('pay', array('order' => $order, 'isPaid' => OrderState::IsPaid($order['States'])));
    }


    public function actionMe()
    {
		//echo $this->uid;
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/me', $lang);
			}
		}
		
		if ($_GET['order_id']) {
			
			$sql = 'UPDATE users_orders SET hide_edit_order=:int WHERE id=:id'; Yii::app()->db->createCommand($sql)->execute(array(':int' => 1, ':id' => $_GET['order_id']));
			
			
			$this->redirect(Yii::app()->createUrl('client/me'));
		}
		
        $o = new Order;
        $orders = $o->GetOrders($this->uid);
        $user = Yii::app()->user->GetModel();

        $this->breadcrumbs[] = Yii::app()->ui->item('YM_CONTEXT_PERSONAL_MAIN');
        $this->render('me', array('orders' => $orders, 'user' => $user));
    }

    public function actionOrders()
    {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/orders', $lang);
			}
		}

        $orderIds = array();
        if (($eid = (int)Yii::app()->getRequest()->getParam('eid'))&&($iid = (int)Yii::app()->getRequest()->getParam('iid'))) {
            if (($eid > 0)&&($iid > 0)) {
                $orderIds = Product::purchasedOrders($this->uid, $eid, $iid);
                if (count($orderIds) == 1) {
                    $orderId = array_pop($orderIds);
                    $this->redirect(Yii::app()->createUrl('order/view', array('oid' => $orderId)));
                }
            }
        }
        $o = new Order();
        $list = $o->GetOrders($this->uid, $orderIds);
        $this->breadcrumbs[] = Yii::app()->ui->item("YM_CONTEXT_PERSONAL_BROWSE_ORDERS");
        $this->render('orders', array('list' => $list));
    }

    public function actionMemo()
    {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/memo', $lang);
			}
		}
		
        $c = new Cart();
        $list = $c->GetMark($this->uid, $this->sid);
        $this->breadcrumbs[] = Yii::app()->ui->item("MSG_SHOPCART_SUSPENDED_ITEMS");
        $this->render('memo', array('list' => $list));
    }

    public function actionChangeMemo()
    {
        if(!Yii::app()->request->isPostRequest) throw new CHttpException(502, 'Please do POST AJAX request');
        $action = $_POST['action'];
        $entity = intVal($_POST['entity']);
        $iid = intVal($_POST['iid']);

        $actions = array('cart', 'delete', 'request');
        if(!in_array($action, $actions)) $this->ResponseJsonError('Wrong action');

        $cart = new Cart;
        $cnt = $cart->ChangeMemo($action, $entity, $iid, $this->uid, $this->sid);

        $this->ResponseJson(array('hasError' => $cnt == 0, 'count' => $cnt, 'action' => $action, 'entity' => $entity, 'iid' => $iid));
    }

    public function actionRequests()
    {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/requests', $lang);
			}
		}
		
        $uid = Yii::app()->user->id;

        $r = new Request;
        $list = $r->GetRequests($uid);

        $this->breadcrumbs[] = Yii::app()->ui->item('YM_CONTEXT_MY_REQUESTS');

        $this->render('requests', array('list' => $list));
    }

    public function actionAddresses()
    {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/addresses', $lang);
			}
		}
		
        $uid = Yii::app()->user->id;
        $a = new Address();

        if(Yii::app()->request->isPostRequest)
        {
            $aid = intVal(@$_POST['aid']);
            $ret = $a->SetDefaultAddress($uid, $aid);
            if($ret) $this->refresh();
        }

        $list = $a->GetAddresses($uid);
        $this->breadcrumbs[] = Yii::app()->ui->item('YM_CONTEXT_PERSONAL_BROWSE_ADDRESS');
        $this->render('address', array('list' => $list));
    }

    public function actionDeleteAddress($aid)
    {
        $uid = Yii::app()->user->id;
        $a = new Address();
        if($a->IsMyAddress($uid, $aid))
        {
            $a->DeleteAddress($uid, $aid);
        }

        $this->redirect(Yii::app()->createUrl('client/addresses'));
    }

    public function actionNewAddress()
    {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/newaddress', $lang);
			}
		}
		
        $model = new Address('new');
        $model->type = Address::PRIVATE_PERSON;
        $this->breadcrumbs[] = Yii::app()->ui->item('YM_CONTEXT_PERSONAL_ADD_ADDRESS');
        $this->render('new_address', array('model' => $model, 'mode' => 'new'));
    }

    public function actionEditAddress($aid)
    {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
            $this->_otherLangPaths[$lang] = Yii::app()->createUrl('client/editaddress', array('aid' => $aid, '__langForUrl'=>$lang));//Person::CreateUrl('client/editaddress', $lang);
		}
		
        $uid = Yii::app()->user->id;
        $a = new Address();
        if(!$a->IsMyAddress($uid, $aid)) throw new CHttpException(403, 'Not your address');

        $model = Address::model()->findByPk($aid);
        if(empty($model)) throw new CHttpException(404);

        $this->breadcrumbs[] = Yii::app()->ui->item('YM_CONTEXT_PERSONAL_ADD_ADDRESS');
        $this->render('new_address', array('model' => $model, 'mode' => 'edit'));
    }

    public function actionData()
    {
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/data', $lang);
			}
		}
		
        if(Yii::app()->user->isGuest) throw new CHttpException(403, 'login required');
        $uid = Yii::app()->user->id;

        if ($_GET['otv'] == '1') {

            $sql = 'UPDATE users SET network="", socuid="" WHERE id = '.$uid;

            Yii::app()->db->createCommand($sql)->execute();

        }

        $this->breadcrumbs[] = Yii::app()->ui->item("YM_CONTEXT_PERSONAL_MODIFYUSER");

        $model = User::model()->findByPk($uid);
        Debug::staticRun(array($uid, $model));
        $oldPwd = $model->pwd;
        $model->pwd = null;
        $model->scenario = 'update';



        if(isset($_POST['ajax']) && $_POST['ajax']==='address-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if(Yii::app()->request->isPostRequest)
        {
            $model->attributes = $_POST['User'];
            if($model->validate())
            {
                if(empty($model->pwd)) $model->pwd = $oldPwd;
                if($model->nothing)
                {
                    $model->mail_audio_news = 0;
                    $model->mail_books_news = 0;
                    $model->mail_maps_news = 0;
                    $model->mail_music_news = 0;
                    $model->mail_musicsheets_news = 0;
                    $model->mail_soft_news = 0;
                    $model->mail_video_news = 0;
                }
                $model->update();
                Yii::app()->user->setFlash('user', 'Updated');

                // change language to new lang
                $langCode = Language::ConvertToString($model->mail_language);
                $this->SetNewLanguage($langCode);

                $this->refresh();
            }
        }

        $this->render('modify_data', array('model' => $model));
    }

	public function actionSubscriptions(){
		
		foreach (Yii::app()->params['ValidLanguages'] as $lang) {
			if ($lang !== 'rut') {
				$this->_otherLangPaths[$lang] = Person::CreateUrl('client/subscriptions', $lang);
			}
		}
		
		$this->breadcrumbs[] = Yii::app()->ui->item('A_NEW_SUBS_MENU_TTILE');
		
		$user = User::model()->findByPk(Yii::app()->user->id);
		
		$sql = 'SELECT * FROM `subscriptions` WHERE customer_id='.(int)$user->econet_user_id. ' ORDER BY `Order_No` DESC';
		
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		
		
		$this->render('subscriptions', array('rows'=>$rows));
	}
	
    public function actionPrintOrder($oid)
    {
        $o = new Order();
        $order = $o->GetOrder($oid);
        if(empty($order)) throw new CHttpException(404);

        if($order['uid'] != $this->uid) throw new CException('Wrong order id');

        $this->layout = 'print';
        $this->render('print', array('order' => $order));
    }
}