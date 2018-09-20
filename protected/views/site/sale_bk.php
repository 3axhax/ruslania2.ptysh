       <!-- content -->
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
            <!-- /content -->
<div class="container">

            <?php
            $lang = 'en';
            switch(Yii::app()->language)
            {
                case 'ru':
                case 'rut' : $lang = 'ru'; break;
                case 'fi' : $lang = 'fi'; break;
            }
?>

            <div class="sale_header">
                <?=$ui->item('SALE_HEADER'); ?>
            </div>

            <div class="sale_text">
                <?=$ui->item('SALE_TEXT'); ?>
            </div>

            <div class="sale">
                <a href="/books/bycategory/213/reduced-prices"><img src="/pic1/sale/rasprodazhaknig_<?=$lang; ?>.jpg" /></a>
                <a href="/sheetmusic/bycategory/217/sheet-music-reduced-prices"><img src="/pic1/sale/rasprodazhanot_<?=$lang; ?>.jpg" /></a>
                <a href="/maps/bycategory/8/reduced-prices"><img src="/pic1/sale/rasprodazhamap_<?=$lang; ?>.jpg" style="margin-right: 0px;" /></a>
            </div>

            <div class="sale">
                <a href="/music/bycategory/21/cd-at-reduced-prices"><img src="/pic1/sale/rasprodazhacd_<?=$lang; ?>.jpg" /></a>
                <a href="/soft/bycategory/16/reduced-prices"><img src="/pic1/sale/rasprodazhasoft_<?=$lang; ?>.jpg" /></a>
                <a href="/video/bycategory/43/dvds-at-reduced-prices"><img src="/pic1/sale/rasprodazhadvd_<?=$lang; ?>.jpg"  style="margin-right: 0px;"/></a>
            </div>
</div>