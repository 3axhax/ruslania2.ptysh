<hr />


<div class="container cartorder">

    Спасибо за заказ! Ваш заказ № <?=$number_zakaz?>. Произведите оплату, выбрав чем хотите оплатить<br /><br />
    <div>Выбранный способ оплаты: <?= Yii::app()->ui->item('HEADER_PAYTRAIL') ?></div>
   
    
    <div style="margin: 15px 0;">
    <div>Выбрать <a style="cursor: pointer;" onclick="openPaySystems('dtype25'); $(this).css('color', '#333333'); return false;">другой способ оплаты</a></div>
    <div id="pay_systems" class="row spay" style="display: none; ">
        <?php $this->renderPartial('/site/pay_systems', array()); ?>
    </div>
    </div>



    <?php $this->widget('PayTrailWidget', array('order' => $order)); ?>

    <div class="clearBoth"></div>

    <div style="margin: 15px 0;">
        Если у Вас остались вопросы по оформленному заказу или способам оплаты, звоните по номеру <a href="tel:+35892727070">+358 9 2727070</a> по будням с 9 до 18 ч., по субботам с 10 до 16 ч (по финскому времени GMT +2, летом GMT +3).
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
