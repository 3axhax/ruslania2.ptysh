<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container view_product">
    
    <div class="row" style="float: left">
        <div class="span10">

        <?php $this->renderPartial('is_purchased', array('item' => $item, 'entity' => $entity)); ?>
		<?php $this->renderPartial('/entity/view_product', array('item' => $item, 'entity' => $entity)); ?>
		
		</div>

    </div>
	<div class="span2">
	<?php $this->widget('YouView', array('entity'=>$entity, 'id'=>$item['id'])); ?>
	</div>

	
	
	
	
</div>
<?php $this->widget('Banners', array('type'=>'slider', 'item'=>$item, 'uid'=>$this->uid, 'sid'=>$this->sid)) ?>