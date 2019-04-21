<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
        <div class="span10">

            <?php if (count($list) == 0) : ?>

                <div class="info-box information">
                    <?=$ui->item("MSG_ADDRESS_ERROR_EMPTY"); ?>
                </div>

            <?php else : ?>


                <?=CHtml::beginForm(); ?>
                <?php foreach ($list as $address) : ?>

                    <?php

                    $isDefault = $address['if_default'];

                    switch ($address['type'])
                    {
                        case Address::ORGANIZATION:
                            $type = $ui->item("USER_ADDRESS_TYPE_BUSINESS");
                            $pic = "<i class=\"fa fa-building\" aria-hidden=\"true\" style=\"font-size: 53px; min-width: 47px; text-align: center\"></i>";
                            break;
                        case Address::PRIVATE_PERSON:
                            $type = $ui->item("USER_ADDRESS_TYPE_PERSONAL");
                            $pic = "<i class=\"fa fa-male\" aria-hidden=\"true\" style=\"font-size: 53px; min-width: 47px; text-align: center\"></i>";
                            break;
                    }

                    ?>


                    <table cellspacing="0" cellpadding="5" border="0" >
                        <tbody>
                        <tr>
                            <td valign="top"><?=$pic?></td>
                            <td class="maintxt"><b><?=$type; ?></b>: <?=CommonHelper::FormatAddress($address); ?>
                                <div class="mb6t10l5"><img width="8" height="7" border="0" src="/pic1/arr4.gif">&nbsp;<a
                                        href="<?=Yii::app()->createUrl('client/editaddress', array('aid' => $address['address_id'])); ?>" class="maintxt1"><?=$ui->item("ADDRESS_EDIT"); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<img
                                        width="8" height="7" border="0" src="/pic1/del1.gif">&nbsp;<a
                                        href="<?=Yii::app()->createUrl('client/deleteaddress', array('aid' => $address['address_id'])); ?>" class="maintxt1"><?=$ui->item("ADDRESS_DELETE"); ?></a>
                                </div>
								
								<script>
								
									function check_radio(cont, value_addr) {
										
										//alert(cont.html());
										
										$('label.red_checkbox span.check').removeClass('active');
										$('span.check', cont).addClass('active');
										
										$('.btn_save_adr').show();
										
										$('input.aid').val(value_addr);
										
									}
								
								</script>
								
                                <div><label style="vertical-align: middle; line-height: normal;" class="red_checkbox" onclick="check_radio($(this), '<?=$address['address_id']; ?>');">
								
								<span class="checkbox" style="margin-right: 5px;">
									<span class="check <?=($isDefault) ? 'active' : ''; ?>"></span>
								</span>
								
								
                                    <?=$ui->item("ADDRESS_USE_AS_DEFAULT"); ?></label></div>
                            </td>
                        </tr>
                        </tbody>
                    </table>


                <?php endforeach; ?>
				
				<input type="hidden" id="ir<?=$address['address_id']; ?>" style="vertical-align: middle;" value=""
                                            name="aid" class="aid">
				
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

                <input type="submit" class="order_start btn_save_adr" style="background-color: #5bb75b; padding: 9px; width:auto; margin-left: 15px; display: none;" value="<?=$ui->item("BTN_FORM_SAVE"); ?>"/>

                </form>

            <?php endif; ?>



            <a href="<?=Yii::app()->createUrl('my/newaddress'); ?>" class="order_start" style="background-color: #5bb75b; padding: 9px; width:auto; margin-left: 15px;"><?=$ui->item("ADD_ADDRESS_ALT"); ?></a>
            <!-- /content -->
        </div>
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
        </div>
        </div>
