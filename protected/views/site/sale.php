<!-- content -->
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<!-- /content -->



<?php
$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);

//var_dump(Yii::app()->getRequest()->cookies['yourView']->value);

$arrGoods = array();

if ($serGoods) {
    $arrGoods = $serGoods;
}

?>
<style>
    .sale_item .container { width: 901px; }
    .sale_item { margin-top: 30px; }
</style>

<div class="container view_product">
    
    
    
    <div class="row" style="float: left">
        <div class="span10" style="width: 901px; margin-left: 20px;">

            <div class="sale_info_block">
                <div class="maxcount"><?=$ui->item('A_NEW_SALE_COUNT')?></div>
                <div class="header"><div><?=$ui->item('A_NEW_SALE_H1')?></div></div>
                <div class="desc">
                    <div><?=$ui->item('A_NEW_SALE_PAGE_TITLE')?></div>
                    <div><?=$ui->item('A_NEW_SALE_PAGE_DESC')?></div>
                </div>
            </div>

            <?php /*
        <h1><?=$ui->item('A_NEW_SALE_H1')?><br><?=$ui->item('A_NEW_SALE_COUNT')?></h1>
		<?=$ui->item('A_NEW_SALE_PAGE_TITLE')?><br><?=$ui->item('A_NEW_SALE_PAGE_DESC')?>
                <br /><br /> */?>
                <?php
            if (!empty($items)) {
                $i = 0;
                if (count($items) > 1): ?>
                    <div class="tab-container" style="margin-top: 40px;"><?php foreach ($items as $eid=>$row): ?>
                            <div><a href="<?= $row['url'] ?>"><?= Entity::GetTitle($eid) ?></a></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="clearBoth"></div>
                <?php else: $i = 1; ?>
                <?php endif;
                foreach ($items as $row) {
                    if (count($row['items']) == 0) {
                        continue;
                    }
                    $this->renderPartial('/entity/_sale_item', array('items' => $row['items'], 'entity' => $row['Entity'], 'title' => $row['name'], 'link' => $row['url'], 'i'=>$i));
                    $i++;
                }
            }
                ?>
                
    </div>
	<div class="span2">
	<?php $this->widget('YouView', array()); ?>
	</div>

</div>
</div>