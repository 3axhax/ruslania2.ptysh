<?php /*Created by Кирилл (05.07.2018 19:21)*/ ?>
<div class="slider_bg" style="background-image: none; background-color: #ccc; margin-top: 35px;">
	<div class="container slider_container">
		<div class="btn_left"><img src="/new_img/btn_left.png" /></div>
		<div class="btn_right"><img src="/new_img/btn_right.png" /></div>
		<div class="overflow_box">
			<div class="container_slides" style="width: 1170px;">
				<ul>
<?php foreach ($items as $item):
	$url = ProductHelper::CreateUrl($item);
	$productTitle = ProductHelper::GetTitle($item, 'title', 18);
	$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
	?>
					<li>
						<div class="span1 photo">
							<a href="<?= $url ?>"><img src="<?= Picture::Get($item, Picture::SMALL) ?>" alt=""  style="max-height: 130px;"/></a>
						</div>
						<div class="span2 text">
							<div class="title"><a href="<?= $url ?>"><?= ProductHelper::GetTitle($item, 'title', 18) ?></a></div>
							<div class="cost">
								<?php if (!empty($price[DiscountManager::DISCOUNT])): ?>
									<span class="without_discount"><?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]) ?></span>&nbsp;
									<span class="price with_discount"<?php if ($price[DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) ?></span>
								<?php else: ?>
									<?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) ?>
								<?php endif; ?>
							</div>
							<div class="nds"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]).' '.$ui->item('WITHOUT_VAT') ?></div>
							<?php if ($item['entity'] == Entity::PERIODIC): ?>
							<a href="<?=$url;?>" class="btn_yellow"><?= Yii::app()->ui->item('A_NEW_MORE3') ?></a>
							<?php else: ?>
							<div class="addcart" style="margin-top: 10px;">
								<a class="cart-action add_cart" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" href="javascript:;" style="width: 103px;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></a>
							</div>
							<?php endif; ?>
						</div>
					</li>
<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('.container_slides ul').slick({
			lazyLoad: 'ondemand',
			slidesToShow: 3,
			slidesToScroll: 1
		});
	});
</script>
