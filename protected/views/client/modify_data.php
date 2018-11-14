<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

    <style>

        /* Скрываем реальный чекбокс */
        .checkbox_custom {
            display: none;
        }

        .checkbox-custom {
            position: relative;
            width: 8px;
            height: 8px;
            padding: 3px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .checkbox-custom {
            display: inline-block;
            vertical-align: middle;
            margin-top: -3px;
        }

        .checkbox_custom:checked + .checkbox-custom::before {
            content: "";
            display: block;
            position: absolute;

            top: 3px;
            right: 3px;
            bottom: 3px;
            left: 3px;
            background: #413548;
            border-radius: 2px;

            background: #ed1d24;
            width: 8px;
            height: 8px;
            display: inline-block;
            font-size: 0;

        }

    </style>

    <div class="row">
        <div class="span10">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'address-form',
                'enableAjaxValidation' => true,
                'clientOptions' => array(
                    'validateOnChange' => false,
                    'validateOnSubmit' => true,
                )
            )); ?>

            <?php if(Yii::app()->user->hasFlash('user')) : ?>
                <div class="info-box information">
                    <?=Yii::app()->user->getFlash('user'); ?>
                </div>
            <?php endif; ?>

            <?php echo $form->errorSummary($model); ?>

            <table width="100%" class="address">
                <thead>
                <th width="200px"></th>
                <th width="265px"></th>
                <th></th>
                </thead>
                <tbody>
                <tr>
                    <td><?= $ui->item("regform_email"); ?></td>
                    <td>
                        <?=$form->error($model, 'login'); ?>
                        <?= $form->emailField($model, 'login'); ?>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td><?= $ui->item("regform_password"); ?></td>
                    <td><?=$form->error($model, 'pwd'); ?>
                        <?= $form->passwordField($model, 'pwd'); ?></td>
                    <td><img src=/pic1/arr3.gif width=8 height=7> <span
                                class="smalltxt1"><?= $ui->item("MSG_REGFORM_PASSWORD_TIP_1"); ?></span></td>
                </tr>
                <tr>
                    <td><?= $ui->item("regform_repeat_password"); ?></td>
                    <td><?= $form->passwordField($model, 'pwd2'); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?= $ui->item("regform_titlename") ?></td>
                    <td><?= $form->textField($model, 'title_name'); ?></td>
                    <td><img src=/pic1/arr3.gif width=8 height=7> <span
                                class="smalltxt1"><?= $ui->item("MSG_REGFORM_TITLENAME_TIP_1"); ?></span></td>
                </tr>
                <tr>
                    <td><?= $ui->item("regform_lastname"); ?></td>
                    <td>
                        <?=$form->error($model, 'last_name'); ?>
                        <?= $form->textField($model, 'last_name'); ?></td>
                    <td><img src=/pic1/arr3.gif width=8 height=7> <span
                                class="smalltxt1"><?= $ui->item("MSG_REGFORM_LASTNAME_TIP_1"); ?></span></td>
                </tr>
                <tr>
                    <td><?= $ui->item("regform_firstname"); ?></td>
                    <td><?=$form->error($model, 'first_name'); ?>
                        <?= $form->textField($model, 'first_name'); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td><?= $ui->item("regform_middlename"); ?></td>
                    <td><?= $form->textField($model, 'middle_name'); ?></td>
                    <td></td>
                </tr>

                <tr>
                    <td>Привязка к соцсети</td>
                    <td style="padding: 5px 0;">
                        <?
                        if ($model->network) {

                            echo $model->network . '&nbsp;&nbsp;&nbsp;<a href="?otv=1">отвязать</a>';

                        } else {?>
                            <script src="//ulogin.ru/js/ulogin.js"></script>
                            <div style="width: 260px;" id="uLogin" data-ulogin="display=panel;theme=classic;fields=first_name,last_name,email;providers=vkontakte,odnoklassniki,googleplus,facebook,twitter,instagram;redirect_uri=<?=urlencode('/ulogin_mrg.php')?>;mobilebuttons=0;"></div>
                        <?}?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2" style="text-align: left">
                        <?= $ui->item("regform_messages_language"); ?>
                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td colspan="2" style="text-align: left">
                        <table cellspacing="0" cellpadding="0" border="0">
                            <tbody>
                            <tr>
                                <td class="maintxt-vat">
                                    <label>
                                        <?= $form->radioButton($model, 'mail_language', array('value' => Language::RUSSIAN, 'uncheckValue' => null, 'id' => 'ru', 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span>
                                    </label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="ru"><?= $ui->item("A_LANG_RUSSIAN"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?= $form->radioButton($model, 'mail_language', array('value' => Language::TRANSLIT, 'uncheckValue' => null, 'id' => 'tr', 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="tr"><?= $ui->item("A_LANG_TRANSLIT"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?= $form->radioButton($model, 'mail_language', array('value' => Language::FINNISH, 'uncheckValue' => null, 'id' => 'fi', 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="fi"><?= $ui->item("A_LANG_FINNISH"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?= $form->radioButton($model, 'mail_language', array('value' => Language::ENGLISH, 'uncheckValue' => null, 'id' => 'en', 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="en"><?= $ui->item("A_LANG_ENGLISH"); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="maintxt-vat">
                                    <label><?= $form->radioButton($model, 'mail_language', array('value' => Language::GERMAN, 'uncheckValue' => null, 'id' => 'de', 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="de"><?= $ui->item("A_LANG_GERMAN"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?= $form->radioButton($model, 'mail_language', array('value' => Language::FRENCH, 'uncheckValue' => null, 'id' => 'fr', 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="fr"><?= $ui->item("A_LANG_FRENCH"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?= $form->radioButton($model, 'mail_language', array('value' => Language::ESPANIOL, 'uncheckValue' => null, 'id' => 'es', 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="es"><?= $ui->item("A_LANG_ESPANIOL"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?= $form->radioButton($model, 'mail_language', array('value' => Language::SWEDISH, 'uncheckValue' => null, 'id' => 'se', 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="se"><?= $ui->item("A_LANG_SWEDISH"); ?></label>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>

                <tr>
                    <td></td>
                    <td colspan="2" style="text-align: left;">
                        <?= $ui->item("regform_mail_news"); ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">
                        <table cellspacing="0" cellpadding="0" border="0" id="cb">
                            <tbody>
                            <tr>
                                <td class="maintxt-vat">
                                    <label><?=$form->checkBox($model, 'mail_books_news', array('id' => 'books', 'uncheckValue' => null, 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="books"><?=$ui->item("A_GOTOBOOKS"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?=$form->checkBox($model, 'mail_musicsheets_news', array('id' => 'sheetmusic', 'uncheckValue' => null, 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="sheetmusic"><?=$ui->item("A_GOTOMUSICSHEETS"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?=$form->checkBox($model, 'mail_music_news', array('id' => 'music', 'uncheckValue' => null, 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="music"><?=$ui->item("Music catalog"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?=$form->checkBox($model, 'mail_audio_news', array('id' => 'audio', 'uncheckValue' => null, 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="audio"><?=$ui->item("A_GOTOAUDIO"); ?></label>
                                </td>
                            </tr>
                            <tr>
                                <td class="maintxt-vat">
                                    <label><?=$form->checkBox($model, 'mail_video_news', array('id' => 'video', 'uncheckValue' => null, 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="video"><?=$ui->item("A_GOTOVIDEO"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?=$form->checkBox($model, 'mail_maps_news', array('id' => 'maps', 'uncheckValue' => null, 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="maps"><?=$ui->item("A_GOTOMAPS"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?=$form->checkBox($model, 'mail_soft_news', array('id' => 'soft', 'uncheckValue' => null, 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="soft"><?=$ui->item("A_GOTOSOFT"); ?></label>
                                </td>
                                <td class="maintxt-vat">
                                    <label><?=$form->checkBox($model, 'nothing', array('id' => 'nothing', 'uncheckValue' => null, 'class'=>'checkbox_custom')); ?>
                                        <span class="checkbox-custom"></span></label>
                                </td>
                                <td style="padding-right: 40px; padding-left: 5px;" class="maintxt">
                                    <label for="nothing"><?=$ui->item("MSG_DO_NOT_RECEIVE_NEWS"); ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">

                        <style>

                            input.order_start {
                                display: inline-block;
                                width: 180px;
                                border-radius: 4px;
                                background-color: rgb(117, 132, 149);
                                border: 0;
                                padding: 9px 0;
                                text-align: center;
                                font-size: 14px;
                                color: rgb(255, 255, 255);
                                font-weight: bold;
                            }


                        </style>

                        <input data-bind="visible: !disableSubmitButton()" type="submit" class="order_start" style="background-color: #5bb75b; padding: 9px; width:auto;" value="Сохранить"/>

                    </td>
                </tr>
                <script type="text/javascript">
                    $(document).ready(function()
                    {
                        $('#cb').find('input:checkbox').change(function()
                        {
                            var $el = $(this);
                            var attr = $el.attr('id');
                            var checked = $el.is(':checked');
                            if(attr == 'nothing')
                            {
                                $('#cb').find('input:checkbox').prop('checked', !checked);
                                $el.prop('checked', checked);
                            }
                            else
                            {
                                if(checked) $('#nothing').prop('checked', false);
                            }
                        });

                    })
                </script>
                </tbody>
            </table>

            <?php $this->endWidget(); ?>


            <!-- /content -->
        </div>
        <div class="span2">

            <?php $this->renderPartial('/site/_me_left'); ?>

        </div>
    </div>
</div>
