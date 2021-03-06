<?php /*Created by Кирилл (02.05.2019 1:09)*/ ?>
<div class="news_box" style="margin-top: 40px;">
	<div class="">
		<div class="title">
			<?= Yii::app()->ui->item('A_NEW_VIEWD_ITEMS') ?>
		</div>
	</div>
	<div class="more_goods" style="overflow: hidden">
		<ul class="books">
			<?php foreach ($items as $product):
				$titleSmall = ProductHelper::GetTitle($product, 'title', 42);
				$title = ProductHelper::GetTitle($product, 'title');
				$url = ProductHelper::CreateUrl($product);
				$isAvail = ProductHelper::IsAvailableForOrder($product);
				$photoTable = Entity::GetEntitiesList()[$product['entity']]['photo_table'];
				$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
				/**@var $photoModel ModelsPhotos*/
				$photoModel = $modelName::model();
				$photoId = $photoModel->getFirstId($product['id']);
				?>
				<li class="you_view_content">
					<div class="img" style="min-height: 130px; position: relative">
						<?php Yii::app()->getController()->renderStatusLables($product['status']); ?>
						<a href="<?= $url ?>" title="<?= htmlspecialchars($title) ?>">
							<?php if (empty($photoId)): ?>
							<img alt="<?= htmlspecialchars($title) ?>" src="<?= Picture::srcLoad() ?>" lazySrc="<?= Picture::Get($product, Picture::SMALL) ?>" style="max-height: 130px;"/>
							<?php else: ?>
								<picture>
									<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $product['eancode'], 'webp') ?>" type="image/webp">
									<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $product['eancode'], 'jpg') ?>" type="image/jpeg">
									<img alt="<?= htmlspecialchars($title) ?>" src="<?= $photoModel->getHrefPath($photoId, 'o', $product['eancode'], 'jpg') ?>" style="max-height: 130px;"/>
								</picture>
							<?php endif; ?>
						</a>
					</div>

					<div class="title_book" style="height:29px;min-height:auto;margin-bottom:0;"><a href="<?= $url ?>" title="<?= $title ?>"><?= $titleSmall ?></a></div>
					<?php if ($isAvail): ?>
					<div style="height: 40px;">
						<div class="cost">
							<?php if (!empty($product['priceData'][DiscountManager::DISCOUNT])) : ?>
								<span class="without_discount">
	                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::BRUTTO]); ?>
	                </span>
								<span class="price with_discount" style="font-size: 15px;<?php if ($product['priceData'][DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> color: #42b455;<?php endif; ?>">
	                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITH_VAT]); ?><?= $product['priceData']['unit'] ?>
	                </span>
							<?php else : ?>
								<span class="price" style="font-size: 15px;">
	                    <?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITH_VAT]); ?><?= $product['priceData']['unit'] ?>
	                </span>
							<?php endif; ?>
						</div>
						<div class="nds"<?php if($product['entity'] == Entity::PERIODIC):?> style="display: none;" <?php endif; ?>><?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $product['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
					</div>
					<?php if ($product['entity'] == Entity::PERIODIC): ?>
						<a href="<?=$url;?>" class="btn_yellow fa"><span class="lang-<?= Yii::app()->getLanguage() ?>"><?= Yii::app()->ui->item('A_NEW_MORE3') ?></span></a>
					<?php else: ?>
						<div class="addcart">
							<a class="cart-action add_cart_plus" data-action="add" data-entity="<?= $product['entity'] ?>" data-id="<?= $product['id'] ?>" data-quantity="1" href="javascript:;"><span>
	<?= Yii::app()->ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART') ?></span>
							</a>
						</div>
					<?php endif; ?>
					<?php elseif ($product['entity'] != Entity::VIDEO): ?>
						<div style="margin-top: 40px;">
						<?php if (Yii::app()->user->isGuest) : ?>
							<a style="height: 30px;line-height: 30px;margin-top: 18px;padding: 0;" href="<?=
							Yii::app()->createUrl('cart/dorequest', array('entity' => Entity::GetUrlKey($product['entity']),
								'iid' => $product['id']));
							?>" class="ca request"><?= Yii::app()->ui->item('CART_COL_ITEM_MOVE_TO_ORDERED'); ?></a>
						<?php else : ?>
							<a style="height: 30px;line-height: 30px;margin-top: 18px;padding: 0;" class="cart-action request" data-action="request" data-entity="<?= $product['entity']; ?>"
							   data-id="<?= $product['id']; ?>"
							   href="<?= Yii::app()->createUrl('cart/request', array('entity' => $product['entity'], 'id' => $product['id'])); ?>"><?= Yii::app()->ui->item('CART_COL_ITEM_MOVE_TO_ORDERED'); ?></a>
						<?php endif; ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
