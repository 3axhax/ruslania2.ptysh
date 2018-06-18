<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<style>
    label.seld {
        padding: 1.8rem 2rem 2.2rem;
        border: 1px solid #ccc;
        margin-top: 10px;
        
        position: relative;
    }
    
    label.seld .red_checkbox {
        position: absolute;
        right: 20px;
        top: 20px;
    }
    
</style>

<script>

    function check_cart_sel(cont, inputId) {
        
        $('.seld .check').removeClass('active');
        $('.seld input[type=radio]').removeAttr('checked');
        $('.seld').css('border', '1px solid #ccc');
        
        if ($('.check', cont).hasClass('active')) {
            $('.check', cont).removeClass('active');
            $(cont).css('border', '1px solid #ccc');
            if (inputId == undefined) $('.avail', cont).val('');
            else $('#'+inputId).removeAttr('checked', 'true');
        } else {
            $('.check', cont).addClass('active');
            $(cont).css('border', '1px solid #64717f');
            if (inputId == undefined) $('.avail', cont).val('1');
            else $('#'+inputId).attr('checked', 'true');
        }
        
    }

</script>

<div class="container cabinet">
    
    <div class="p1">1. Где и как вы хотите получить заказ?</div>
    
    <div class="row">
    
        <label class="seld span3" onclick="check_cart_sel($(this), 'dtype1')">
            Забрать в магазине
            <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            
            <input type="radio" id="dtype1" value="1" name="dtype" style="display: none;" />
        </label>
        
        <label class="seld span3" onclick="check_cart_sel($(this), 'dtype2')">
            Заказать доставку
             <div class="red_checkbox" style="float: right;">
            <span class="checkbox" style="height: 10px; padding-top: 2px;"><span class="check<?=$act[1]?>"></span></span> 
            </div>
            <input type="radio" id="dtype2" value="0" name="dtype" style="display: none;" />
        </label>
        
    </div>
</div>

