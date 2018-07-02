<hr />

<div class="container cartorder">
    
    Ваш заказ № <?=$number_zakaz?>. Произведите оплату, нажав на кнопку PAYYY внизу<br /><br />
    
    <?php $this->widget('PayTrailWidget', array('order' => $order)); ?>
    
</div>

