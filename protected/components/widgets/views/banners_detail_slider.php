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
	
	
                        $price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
						
						$item['priceData']['unit'] = '';
                        if ($item['entity'] == Entity::PERIODIC) {
                            $issues = Periodic::getCountIssues($item['issues_year']);
                            if (!empty($issues['show3Months'])) {
                                $item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
                                $price[DiscountManager::BRUTTO] = $price[DiscountManager::BRUTTO]/4;
                                $price[DiscountManager::WITH_VAT] = $price[DiscountManager::WITH_VAT]/4;
                                $price[DiscountManager::WITHOUT_VAT] = $price[DiscountManager::WITHOUT_VAT]/4;
                            }
                            elseif (!empty($issues['show6Months'])) {
                                $item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                                $price[DiscountManager::BRUTTO] = $price[DiscountManager::BRUTTO]/2;
                                $price[DiscountManager::WITH_VAT] = $price[DiscountManager::WITH_VAT]/2;
                                $price[DiscountManager::WITHOUT_VAT] = $price[DiscountManager::WITHOUT_VAT]/2;
                            }
                            else {
                                $item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
                            }
                        }
                   
	
	
	
	?>
					
					<?
					
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
								<?php if (!empty($price[DiscountManager::DISCOUNT])): ?>
									<span class="without_discount"><?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]) ?></span>&nbsp;
									<span class="price with_discount"<?php if ($price[DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) ?></span>
								<?php else: ?>
									<?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) ?>
								<?php endif; ?>
							</div>
							<div class="nds"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]).' '.$ui->item('WITHOUT_VAT') ?></div>
							<?php if ($item['entity'] == Entity::PERIODIC): ?>
							
							<a href="<?=$url;?>" class="btn_yellow fa" style="width: 39px; float: right; border-radius: 4px;" tabindex="0"><span style="width: auto; margin-left: 0;  border-radius: 4px;"></span></a>
							
							<?php else: ?>
							<div class="addcart" style="margin-top: 10px;">
								
								<?
								//$sCount = Cart::getCountCartItem($item['id'], $item['entity'], $this->uid, $this->sid);
								?>
								
								
								<? if ($sCount > 0) : ?>
	
	<a class="cart-action cart_add_slider add_cart list_cart<?if (Yii::app()->language == 'es') echo ' no_img';?> add_cart_plus cartMini<?=$item['id']?> green_cart" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" href="javascript:;" style="width: 115px; float: right;  margin-top: 8px;">
                        <span style="width: auto;"><?=sprintf($ui->item('CARTNEW_IN_CART_BTN2'), $sCount)?></span>
                    </a>
	
	
	
<? else : ?>
	<a class="cart-action cart_add_slider add_cart add_cart_plus cartMini<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" href="javascript:;" style="width: 40px; float: right;  margin-top: 8px;"></a>
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
