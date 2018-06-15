            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container content_books">
<div class="row">

		<div class="listgoods span10">

<?php if (!empty($abstractInfo)): ?>
    <div style="margin-bottom: 20px;">
    <?php $dataForSearch = array('q'=>$q,'avail'=>$this->GetAvail(1));
            foreach ($abstractInfo as $eNum=>$counts): $dataForSearch['e'] = $eNum; ?>
                <div class="row_category"><?= $q ?> <span><?= $ui->item('A_NEW_SEARCH_IN_CAT') ?> <?= Entity::GetTitle($eNum) ?></span> <a href="/site/search?<?= http_build_query($dataForSearch) ?>" class="result_search_count"><?= $counts ?></a></div>
            <?php endforeach; ?>
        </div>
<?php endif; ?>

            <?php if (!empty($items)) : ?>
                <p><?=$ui->item('DID_YOU_MEAN'); ?></p>
                <ul class="items">
                    <?php foreach ($items as $i) : ?>
                        <li>
                            <a href="<?= $i['url']; ?>"><?= $i['title']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>


            <div class="text" style="margin-top: 7px;">
                <?= sprintf($ui->item("X items found"), $paginatorInfo->getItemCount()); ?>
				
			
				
            </div>
            <ul class="items">
                <?php foreach ($products as $i) : ?>
                    <li>
						<?php $i['status'] = Product::GetStatusProduct($i['entity'], $i['id'])?>
                        <?php $this->renderPartial('/entity/_common_item_2', array('item' => $i,
                                                                                 'isList' => true,
                                                                                 'entity' => $i['entity'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (count($products) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>

        </div>

	<?
$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);

if ($serGoods) {

shuffle($serGoods);

?>

 <div class="span2">
     <h2 class="poht" style="margin-top: 0; margin-bottom: 20px;"><?= $ui->item('A_NEW_VIEWD_ITEMS'); ?>:</h2>


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
