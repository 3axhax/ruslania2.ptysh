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
		<h1><?=$ui->item('A_NEW_SALE_H1')?><br><?=$ui->item('A_NEW_SALE_COUNT')?></h1>
		<?=$ui->item('A_NEW_SALE_PAGE_TITLE')?><br><?=$ui->item('A_NEW_SALE_PAGE_DESC')?>
                <br /><br />
                <?php
                if ($items) {
                    foreach ($items as $row) {
                        if (count($row['items']) == 0) {
                            continue;
                        }


                        $this->renderPartial('/entity/_sale_item', array('items' => $row['items'], 'entity' => $row['Entity'], 'title' => $row['name'], 'link' => $row['url']));

                    }
                }
                ?>
                
    </div>
	<div class="span2">
	<?php $this->widget('YouView', array()); ?>
	</div>

</div>
</div>