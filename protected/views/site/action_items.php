<div class="slider_bg">
	<div class="container slider_container"><?php /*
		<div class="btn_left"><img src="/new_img/btn_left.png" /></div>
		<div class="btn_right"><img src="/new_img/btn_right.png" /></div>
		*/?><div class="overflow_box">
			<div class="container_slides" style="width: 1170px;">
				<ul>
				<?
				foreach ($actionItems as $actionItem)
				{
					
					$sCount = Cart::getCountCartItem($actionItem['item_id'], $actionItem['entity'], $this->uid, $this->sid);
					
					
					$product = Product::GetProduct($actionItem['entity'], $actionItem['item_id']);
					
					$url = ProductHelper::CreateUrl($product);						
					$productTitle = ProductHelper::GetTitle($product, 'title');
					$productTitleSmall = ProductHelper::GetTitle($product, 'title', 18);
					$productPicture = Picture::Get($product, Picture::SMALL);

					$product['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $product);
					$product['priceData']['unit'] = '';
					if ($product['entity'] == Entity::PERIODIC) {
						$issues = Periodic::getCountIssues($product['issues_year']);
						if (!empty($issues['show3Months'])) {
							$product['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
							$product['priceData'][DiscountManager::BRUTTO] = $product['priceData'][DiscountManager::BRUTTO_FIN]/4;
							$product['priceData'][DiscountManager::WITH_VAT] = $product['priceData'][DiscountManager::WITH_VAT_FIN]/4;
							$product['priceData'][DiscountManager::WITHOUT_VAT] = $product['priceData'][DiscountManager::WITHOUT_VAT_FIN]/4;
						}
						elseif (!empty($issues['show6Months'])) {
							$product['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
							$product['priceData'][DiscountManager::BRUTTO] = $product['priceData'][DiscountManager::BRUTTO_FIN]/2;
							$product['priceData'][DiscountManager::WITH_VAT] = $product['priceData'][DiscountManager::WITH_VAT_FIN]/2;
							$product['priceData'][DiscountManager::WITHOUT_VAT] = $product['priceData'][DiscountManager::WITHOUT_VAT_FIN]/2;
						}
						else {
							$product['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
							$product['priceData'][DiscountManager::BRUTTO] = $product['priceData'][DiscountManager::BRUTTO_FIN];
							$product['priceData'][DiscountManager::WITH_VAT] = $product['priceData'][DiscountManager::WITH_VAT_FIN];
							$product['priceData'][DiscountManager::WITHOUT_VAT] = $product['priceData'][DiscountManager::WITHOUT_VAT_FIN];
						}
					}

					$actionTitle = '';
					if($product['status'] == 'new')
					{
						$actionTitle = '<div class="new_block">'.Yii::app()->ui->item('IN_NEW').'</div>';
						$actionTitleClass = ' new';
					}
					elseif($product['status'] == 'sale')
					{
						$actionTitle = '<div class="new_block">'.Yii::app()->ui->item('IN_SALE').'</div>';
						$actionTitleClass = ' akciya';
					}
					elseif($product['status'] == 'recommend')
					{
						$actionTitle = '<div class="new_block">'.Yii::app()->ui->item('IN_OFFERS').'</div>';
						$actionTitleClass = ' rec';
					}
					?>
						<li>
							<div class="span1 photo<?=$actionTitleClass;?>">
								<?=$actionTitle;?>
								<a title="<?= htmlspecialchars($productTitle) ?>" href="<?=$url;?>"><img src="<?= Picture::srcLoad() ?>" data-lazy="<?=$productPicture;?>" alt="<?= htmlspecialchars($productTitle) ?>" style="max-height: 130px;"/></a>
							</div>
							<div class="span2 text">
								
								<div class="title"><a title="<?= htmlspecialchars($productTitle) ?>" href="<?=$url;?>"><?=$productTitleSmall;?></a></div>
								<div class="cost">
									<?php if (!empty($product['priceData'][DiscountManager::DISCOUNT])) : ?>
										<span class="without_discount">
                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::BRUTTO]); ?>
                </span>&nbsp;
										<span class="price with_discount entity-<?= $product['entity'] ?>"<?php if ($product['priceData'][DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>>
                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITH_VAT]); ?><?= $product['priceData']['unit'] ?>
                </span>
									<?php else : ?>
										<span class="price">
                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITH_VAT]); ?><?= $product['priceData']['unit'] ?>
                </span>
									<?php endif; ?>
								</div>
								
								
								
								<div class="nds"<?php if($product['entity'] == Entity::PERIODIC):?> style="<?=(($product['priceData'][DiscountManager::DISCOUNT] == '0') ? 'visibility: hidden;' : 'display: none')?>" <?php endif; ?>><?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $product['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
								<?php if ($product['entity'] == Entity::PERIODIC): ?>
								
									<?php /*<a href="<?=$url;?>" class="btn_yellow fa" style="width: 39px; float: right; border-radius: 4px;"><span style="width: auto; margin-left: 0;  border-radius: 4px;"></span></a> */?>
									<a href="<?=$url;?>" class="btn_yellow fa" style="float: right; border-radius: 4px;" tabindex="0"><span class="lang-<?= Yii::app()->getLanguage() ?>"><?= Yii::app()->ui->item('A_NEW_MORE3') ?></span></a>
								<?php else: ?>
									<div class="addcart" style="margin-top: 10px;">
								<?file_put_contents($_SERVER['DOCUMENT_ROOT'].'/protected/runtime/1.txt', print_r($product,1))?>	
		
<? if ($sCount > 0) : ?>

	<a class="count<?=$sCount?> cart-action cart_add_slider add_cart list_cart<?//if (Yii::app()->language == 'es') echo ' no_img';?> add_cart_plus cartMini<?=$product['id']?> green_cart" data-action="add" data-entity="<?= $product['entity']; ?>" data-id="<?= $product['id']; ?>" data-quantity="1" href="javascript:;" style="width: 177px; ">
		<span style="width: auto;"><?=sprintf($ui->item('CARTNEW_IN_CART_BTN'), $sCount)?></span>
	</a>
	<?php /*
	<a class="cart-action cart_add_slider add_cart list_cart<?if (Yii::app()->language == 'es') echo ' no_img';?> add_cart_plus cartMini<?=$product['id']?> green_cart" data-action="add" data-entity="<?= $product['entity']; ?>" data-id="<?= $product['id']; ?>" data-quantity="1" href="javascript:;" style="width: 115px; float: right;  margin-top: 8px;">
                        <span style="width: auto;"><?=sprintf($ui->item('CARTNEW_IN_CART_BTN2'), $sCount)?></span>
                    </a>
	*/ ?>
	
	
<? else : ?>
	<a class="cart-action add_cart_plus cartMini<?=$product['id']?>" data-action="add" data-entity="<?= $product['entity']; ?>" data-id="<?= $product['id']; ?>" data-quantity="1" href="javascript:;" style="width: 135px;"><span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></span></a>
	<?php /*<a class="cart-action cart_add_slider add_cart add_cart_plus cartMini<?=$product['id']?>" data-action="add" data-entity="<?= $product['entity']; ?>" data-id="<?= $product['id']; ?>" data-quantity="1" href="javascript:;" style="width: 40px; float: right;  margin-top: 8px;"></a> */ ?>
<? endif; ?>
		
										
										
										
										
									</div>
								<?php endif; ?>
							</div>
						</li>						
					<?
				}
				?>
			</ul>                
		</div>
		</div>            
	</div>						        
</div>
<script type="text/javascript">
	$(document).ready(function() {
		scriptLoader('/new_js/slick.js').callFunction(function(){
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
