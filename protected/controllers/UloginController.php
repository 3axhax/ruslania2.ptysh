<?php
/**
 * Created by PhpStorm.
 * User: Aleksandr Oleynik (sankes@list.ru)
 * Date: 25.09.2018
 * Time: 22:36
 */

class UloginController extends MyController {

    public function actionIndex() {

        if (Yii::app()->request->isPostRequest) {

            $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
            $user = json_decode($s, true);

            var_dump($user);

        }

    }

}