<hr />

<div class="container cartorder">
    
    Ваш заказ № <?=$number_zakaz?>. Произведите оплату нажав на логотип PayPal внизу<br /><br />
    
    <?php $this->widget('PayPalPayment', array('order' => $order)); ?>
    
</div>