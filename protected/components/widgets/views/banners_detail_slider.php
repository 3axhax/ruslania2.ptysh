<?php /*Created by Кирилл (05.07.2018 19:21)*/ ?>
<div class="slider_bg" style="background-image: none; background-color: #ccc; margin-top: 35px;">
	<div class="container slider_container"><?php /*
		<div class="btn_left"><span class="fa"></span></div>
		<div class="btn_right"><span class="fa"></span></div>
		*/ ?><div class="overflow_box">
			<div class="container_slides" style="width: 1170px;">
				<ul>
<?php foreach ($items as $item):
	$url = ProductHelper::CreateUrl($item);
	$productTitle = ProductHelper::GetTitle($item, 'title', 18);
						$c = new Cart;
						$cart = $c->GetCart($uid, $sid);
						$sCount = 0;
						// foreach ($items as $idx => $item) {
							foreach ($cart as $cartItem) {
								if ($cartItem['entity'] == $item['entity'] && $cartItem['id'] == $item['id']) {
									
									//var_dump($sid);
									
									$sCount = $cartItem['quantity'];
								}
							}
					   // }
						
						//echo $sCount;
					
					?>
	
					<li>
						<div class="span1 photo">
							<a href="<?= $url ?>"><img src="<?= Picture::srcLoad() ?>" data-lazy="<?= Picture::Get($item, Picture::SMALL) ?>" alt=""  style="max-height: 130px;"/></a>
						</div>
						<div class="span2 text">
							<div class="title"><a href="<?= $url ?>"><?= ProductHelper::GetTitle($item, 'title', 18) ?></a></div>
							<div class="cost">
								<?php if (!empty($item['priceData'][DiscountManager::DISCOUNT])): ?>
									<span class="without_discount">
										<?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::BRUTTO]) ?>
									</span>&nbsp;
									<span class="price with_discount"<?php if ($item['priceData'][DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>>
										<?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]) ?><?= $item['priceData']['unit'] ?>
									</span>
								<?php else: ?>
									<?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]) ?><?= $item['priceData']['unit'] ?>
								<?php endif; ?>
							</div>
							<div class="nds"<?php if($item['entity'] == Entity::PERIODIC):?> style="display: none;" <?php endif; ?>><?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITHOUT_VAT]).' '.$ui->item('WITHOUT_VAT') ?></div>
							<?php if ($item['entity'] == Entity::PERIODIC): ?>
							
							<a href="<?=$url;?>" class="btn_yellow fa" style="float: right; border-radius: 4px;" tabindex="0"><span style=""><?= Yii::app()->ui->item('A_NEW_MORE3') ?></span></a>
							
							                           
							
							<?php else: ?>
							<div class="addcart" style="margin-top: 10px;">
								
								<?
								//$sCount = Cart::getCountCartItem($item['id'], $item['entity'], $this->uid, $this->sid);
								?>
								
								
								<? if ($sCount > 0) : ?>
	
	<a class="count<?=$sCount?> cart-action cart_add_slider add_cart list_cart<?//if (Yii::app()->language == 'es') echo ' no_img';?> add_cart_plus cartMini<?=$item['id']?> green_cart" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" href="javascript:;" style="width: 177px; ">
                        <span style="width: auto;"><?=sprintf($ui->item('CARTNEW_IN_CART_BTN'), $sCount)?></span>
                    </a>
	
	
	
<? else : ?>
	
	
	                                <a class="cart-action add_cart_plus cartMini<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" href="javascript:;" style="width: 135px;"><span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></span></a>
	
<? endif; ?>
								
								
								
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
		scriptLoader('/new_js/slick.js').callFunction(function() {
			$('.container_slides ul').slick({
				lazyLoad: 'ondemand',
				slidesToShow: 3,
				slidesToScroll: 1
			}).on('lazyLoadError', function(event, slick, image, imageSource){
				image.attr('src', '<?= Picture::srcNoPhoto() ?>');
			});
		});
	});
</script>
