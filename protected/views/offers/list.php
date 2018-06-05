      <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
     <div class="container view_product">
			<div class="row">

        <div class="span10">
            <h2 class="cattitle"><?=$ui->item('RUSLANIA_RECOMMENDS'); ?>:</h2>
            <ul class="left_list entity text recomends">
                <?php $i = 1;  foreach($list as $item): ?>
                    <?php $title = ProductHelper::GetTitle($item->attributes);
					
					
                    ?>
                    <li class="iconentity-<?=$item['icon_entity']; ?>">
					
						<?
							$o = new Offer;
							$offer = $o->GetItems($item['id']);
							
							foreach($offer as $k) {
								$entity = $k['entity'];
								break;
							}
							
							//var_dump(Entity::GetTitle($entity));
							
							//echo Entity::GetTitle($entity);
							
							
							$title_item = '';
							switch ($entity) {
								case 10: $title_item = $ui->item('A_NEW_POP1'); break;
								case 15: $title_item = $ui->item('A_NEW_POP2'); break;
								case 20: $title_item = $ui->item('A_NEW_POP3'); break;
								case 22: $title_item = $ui->item('A_NEW_POP4'); break;
								case 24: $title_item = $ui->item('A_NEW_POP5'); break;
								case 30: $title_item = $ui->item('A_NEW_POP6'); break;
								case 40: $title_item = $ui->item('A_NEW_POP7'); break;
								case 50: $title_item = $ui->item('A_NEW_POP8'); break;
								case 60: $title_item = $ui->item('A_NEW_POP9'); break;
								default: $title_item = $ui->item('A_NEW_POP10'); break;
							}
							
							$s = 0;
							//var_dump($item);
							
						?>
					
					
                        <div>
<a class="title_item_recomend" href="<?=Yii::app()->createUrl('offers/view', array('oid' => $item['id'], 'title' => ProductHelper::ToAscii($title))); ?>"><?= /*$title_item*/CHtml::encode($title) ?>!</a>
<span class="date_recomend"><?=Yii::app()->dateFormatter->format('dd MMM yyyy', $item['creation_date']); ?></span>
                        </div>
<?=CHtml::encode(ProductHelper::GetDescription($item->attributes)); ?>
							
							<?
							if (count($offer[Entity::GetTitle($entity)]['items'])) {
								echo '<div class="items_goods_recomends">';
								echo '<div class="slider_recomend custom-slider">';
							foreach ($offer[Entity::GetTitle($entity)]['items'] as $of) {

                                //if ($s < 7) {
                                if (true) {
                                    echo '<div class="item slider_recomend__item">';
                                    echo '<a href="' . ProductHelper::createUrl($of) . '" class="slider__img-block">
										<div class="img slider__img" style="background: url(\'' . Picture::Get($of, Picture::SMALL) . '\') center center no-repeat; background-size: 100%; position: relative">';
                                    $this->renderStatusLables(Product::GetStatusProduct($of['entity'], $of['id']), '', true);
                                    echo '</div></a>';
                                    echo '</div>';
                                }
                                $s++;
                            }
								echo '</div><div class="clearfix"></div></div>';
							}
							?><div style="margin-top: 15px;"></div>
							<a title="Download Excel file" rel="nofollow" class="dprice"
                           href="<?=Yii::app()->createUrl('offers/download', array('oid' => $item['id'])); ?>"><?=$ui->item('DOWNLOAD_EXCEL_FILE'); ?>
                            <i class="icon-download-alt"></i></a>
					<? if (count($list) > $i) { echo '<hr />'; } $i++;?>
					</li>
                <?php
				

				endforeach; ?>
            </ul>

			<?php if (count($list) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginator)); ?>
        </div>

				<?
$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);

if ($serGoods) {

shuffle($serGoods);

?>

    <div class="span2">
        <h2 class="poht" style="margin-top: 0; margin-bottom: 20px;">
<?=$ui->item('A_NEW_VIEWD_ITEMS')?>:
</h2>


<div class="you_view">

<ul>

	<?
		$i = 1;
		foreach ($serGoods as $goods) {

			if ($i > 5) break;

			$ex = explode('_', $goods);

			$good_id = $ex[0];
			$good_entity = $ex[1];

			if ($good_id == $item['id']) continue;

			$igoods = Product::GetBaseProductInfo($good_entity, $good_id);

			$price = DiscountManager::GetPrice(Yii::app()->user->id, $igoods);

			//var_dump($igoods);
			$i++;
			?>

				<li>
					<div class="span1 photo new">
						<?php $url = ProductHelper::CreateUrl($igoods); ?>

<a href="<?=$url; ?>"><img src="<?=Picture::Get($igoods, Picture::SMALL); ?>" alt="" /></a>

					</div>
					<div class="span2 text">
						<div class="title"><a href="<?=$url; ?>"><?=ProductHelper::GetTitle($igoods, 'title', 30); ?></a></div>
						<div class="cost"><?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
        <span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;">
            <?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]); ?>
        </span>&nbsp;<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold;">
            <?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?>

        </span>

    <?php else : ?>

        <span class="price">
   <?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?>

    </span>

    <?php endif; ?></div>
						<div class="nds"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]); ?> <?=$ui->item('WITHOUT_VAT'); ?></div>
					</div>
					<div class="clearfix"></div>
				</li>

			<?

		}
	?>


</ul>

</div>

    </div>
    <? } ?>
        </div>
        </div>