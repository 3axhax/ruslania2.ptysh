<script type="text/javascript">
    $(document).ready(function() {
        $('.container_slides ul').slick({
            lazyLoad: 'ondemand',
            slidesToShow: 3,
            slidesToScroll: 1
        });
    });
</script>
	
<div class="slider_bg">                        
	<div class="container slider_container">
		<div class="btn_left"><img src="/new_img/btn_left.png" /></div>
		<div class="btn_right"><img src="/new_img/btn_right.png" /></div>
		<div class="overflow_box">
			<div class="container_slides" style="width: 1170px;">
				<ul>
				<?
				foreach ($actionItems as $actionItem)
				{
					$product = Product::GetProduct($actionItem['entity'], $actionItem['item_id']);
					
					$url = ProductHelper::CreateUrl($product);						
					$productTitle = ProductHelper::GetTitle($product, 'title', 18);
					$productPicture = Picture::Get($product, Picture::SMALL);

					$product['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $product);
					$product['priceData']['unit'] = '';
					if ($entity == Entity::PERIODIC) {
						$issues = Periodic::getCountIssues($product['issues_year']);
						if (!empty($issues['show3Months'])) {
							$product['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
							$product['priceData'][DiscountManager::BRUTTO] = $product['priceData'][DiscountManager::BRUTTO]/4;
							$product['priceData'][DiscountManager::WITH_VAT] = $product['priceData'][DiscountManager::WITH_VAT]/4;
							$product['priceData'][DiscountManager::WITHOUT_VAT] = $product['priceData'][DiscountManager::WITHOUT_VAT]/4;
						}
						elseif (!empty($issues['show6Months'])) {
							$product['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
							$product['priceData'][DiscountManager::BRUTTO] = $product['priceData'][DiscountManager::BRUTTO]/2;
							$product['priceData'][DiscountManager::WITH_VAT] = $product['priceData'][DiscountManager::WITH_VAT]/2;
							$product['priceData'][DiscountManager::WITHOUT_VAT] = $product['priceData'][DiscountManager::WITHOUT_VAT]/2;
						}
						else {
							$product['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
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
								<a href="<?=$url;?>"><img src="<?=$productPicture;?>" alt=""  style="max-height: 130px;"/></a>
							</div>
							<div class="span2 text">
								
								<div class="title"><a href="<?=$url;?>"><?=$productTitle;?></a></div>
								<div class="cost">
									<?php if (!empty($product['priceData'][DiscountManager::DISCOUNT])) : ?>
										<span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;">
                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::BRUTTO]); ?>
                </span>&nbsp;
										<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold; white-space: nowrap;">
                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITH_VAT]); ?><?= $product['priceData']['unit'] ?>
                </span>
									<?php else : ?>
										<span class="price">
                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITH_VAT]); ?><?= $product['priceData']['unit'] ?>
                </span>
									<?php endif; ?>
								</div>
								<div class="nds"><?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $product['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
								<?php if ($product['entity'] == Entity::PERIODIC): ?>
									<a href="<?=$url;?>" class="btn_yellow"><?= Yii::app()->ui->item('A_NEW_MORE3') ?></a>
								<?php else: ?>
									<div class="addcart" style="margin-top: 10px;">
										<a class="cart-action add_cart" data-action="add" data-entity="<?= $product['entity']; ?>" data-id="<?= $product['id']; ?>" data-quantity="1" href="javascript:;" style="width: 103px;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></a>
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