<hr />


<div class="container cartorder">

    <h1><?=$ui->item('CARTNEW_PAYTRAIL_H1')?></h1>


    <?=$ui->item('CARTNEW_PAYPAL_THANK_ORDER')?> <br /><br />
    <div>
        <?=$ui->item('CARTNEW_YOUR_SELECT')?>: <?= Yii::app()->ui->item('HEADER_PAYTRAIL') ?><br /><br />

   
   <?=sprintf($ui->item('CARTNEW_PAYTRAIL_TEXT1'), $number_zakaz, ProductHelper::FormatPrice($order['full_price'], $order['currency_id']))?>
   
   <br /><br />

	<div style="margin: 15px 0;">
    <div>
		<?=sprintf($ui->item('CARTNEW_PAYTRAIL_TEXT2'), 'dtype25')?>
	</div>
    <div id="pay_systems" class="row spay" style="display: none; ">
        <?php $this->renderPartial('/site/pay_systems', array()); ?>
    </div>
    </div>

	<div style="height: 20px;"></div>
    <a href="<?= Yii::app()->createUrl('/view/'.$number_zakaz)?>" class="order_start" style="background-color: #28618E;  margin-top: -65px;">
                            <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?=$ui->item('CARTNEW_FINAL_BTN_VIEW_ORDER')?></span>
                        </a>
    <div style="height: 20px;"></div>

    <?php $this->widget('PayTrailWidget', array('order' => $order)); ?>

    <div class="clearBoth"></div>

    <div style="margin: 15px 0;">
        <?=$ui->item('CARTNEW_FINAL_ORDER_TEXT')?>
    </div>
   
</div>
<script type="text/javascript">
    function openPaySystems(inputId) {
        var $ptypeP = $('#' + inputId).parent();
        $ptypeP.css('border', '1px solid #64717f').addClass('act');
        $('input[type=radio]', $ptypeP).attr('checked', 'true');
        $('.check', $ptypeP).addClass('active');
        $('#pay_systems').show();
    }
    function check_cart_sel(cont,cont2,inputId) {
        document.location.href = '<?= Yii::app()->createUrl('cart/orderPay') ?>?id=<?= $number_zakaz ?>&ptype=' + document.getElementById(inputId).value;
    }
</script>
