<hr />

<div class="container cartorder">
    <h1><?= Yii::app()->ui->item('HEADER_PAYPAL') ?></h1>
    
    
    <div class="popup0 popup<?=$p['id']?>" style="background-color: rgba(0,0,0,0.3); position: fixed; left: 0; top: 0; width: 100%; height: 100%; z-index: 99999; opacity: 0.3; display: none;" onclick="$('.popup0').hide();"></div>
    <div style="background-color: rgb(255, 255, 255); position: absolute; padding: 1%; width: 88%; z-index: 99999; border-radius: 2px; box-shadow: rgba(0, 0, 0, 0.3) 0px 0px 10px; left: 5%; top: 5%; display: none; z-index: 999991;" class="popup0 popup1">
        
        <div style="position: absolute; top: 10px; right: 10px; cursor: pointer;" onclick="$('.popup0').hide();">X</div>
        
        <style> .popup0 div { width: auto !important; } </style>
        <?php include ($_SERVER['DOCUMENT_ROOT'] . '/pictures/templates-static/paypal_'.Yii::app()->language.'.html.php'); ?>
    
    </div>
    
    Произведите оплату нажав на логотип PayPal внизу. Ваш заказ № <?=$number_zakaz?><br /><br />
    
    
    
    <?php $this->widget('PayPalPayment', array('order' => $order)); ?>
    
    <div><a href="javascript:;" onclick="$('.popup0').show();">Что такое PayPal?</a></div> <br />
    
    <div>Или выберите <a style="cursor: pointer;" onclick="openPaySystems('dtype8'); $(this).css('color', '#333333'); return false;">другой способ оплаты</a></div>
    <div id="pay_systems" class="row spay" style="display: none;">
        <?php $this->renderPartial('/site/pay_systems', array()); ?>
    </div>
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