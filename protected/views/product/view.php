<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container view_product">
    
    <div class="row" style="float: left">
        <div class="span10">
		
		<?php $this->renderPartial('/entity/view_product', array('item' => $item, 'entity' => $entity)); ?>
		
		</div>

    </div>
	<?php
		$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);
		if ($serGoods): shuffle($serGoods); ?>
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

		      <a href="<?=$url; ?>" title="<?=ProductHelper::GetTitle($igoods, 'title', 30); ?>"><img alt="<?=ProductHelper::GetTitle($igoods, 'title', 30); ?>" src="<?=Picture::Get($igoods, Picture::SMALL); ?>" alt="" /></a>

		  									</div>
		  									<div class="span2 text">
		  										<div class="title"><a href="<?=$url; ?>" title="<?=ProductHelper::GetTitle($igoods, 'title', 30); ?>"><?=ProductHelper::GetTitle($igoods, 'title', 30); ?></a></div>
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
	<?php endif; ?>

</div>
<?php $this->widget('Banners', array('type'=>'slider')) ?>