<?php /*Created by Кирилл (27.07.2018 21:15)*/ ?>
<div class="news_box" style="margin-top: 40px;">
	<div class="">
		<div class="title">
			<?= Yii::app()->ui->item('A_NEW_RELATION_ITEMS') ?>
			<div class="pult">
				<a onclick="$('.news_box .btn_left.slick-arrow').click(); return false;" class="btn_left"><span class="fa"></span></a>
				<a onclick="$('.news_box .btn_right.slick-arrow').click(); return false;" class="btn_right"><span class="fa"></span></a>
			</div>
		</div>
	</div>
	<div class="more_goods" style="overflow: hidden">
		<ul class="books">
		<?php foreach ($items as $product):
			$titleSmall = ProductHelper::GetTitle($product, 'title', 42);
			$title = ProductHelper::GetTitle($product, 'title');
			$url = ProductHelper::CreateUrl($product);
			$photoTable = Entity::GetEntitiesList()[$product['entity']]['photo_table'];
			$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
			/**@var $photoModel ModelsPhotos*/
			$photoModel = $modelName::model();
			$photoId = $photoModel->getFirstId($product['id']);
			?>
			<li>
				<div class="img" style="min-height: 130px; position: relative">
					<?php Yii::app()->getController()->renderStatusLables($product['status']); ?>
					<div style="display: table-cell;vertical-align: middle; height: 150px;"><a href="<?= $url ?>" title="<?= htmlspecialchars($title) ?>">
							<?php if (empty($photoId)): ?>
								<img alt="<?= htmlspecialchars($title) ?>" src="<?= Picture::srcLoad() ?>" data-lazy="<?= Picture::Get($product, Picture::SMALL) ?>" style="max-height: 130px;"/>
							<?php else: ?>
								<picture>
									<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $product['eancode'], 'webp') ?>" type="image/webp">
									<source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $product['eancode'], 'jpg') ?>" type="image/jpeg">
									<img src="<?= $photoModel->getHrefPath($photoId, 'o', $product['eancode'], 'jpg') ?>" alt="<?=htmlspecialchars($title); ?>" />
								</picture>
							<?php endif; ?>
					</a></div>
				</div>

				<div class="title_book" style="height:29px;min-height:auto;margin-bottom:0;"><a href="<?= $url ?>" title="<?= $title ?>"><?= $titleSmall ?></a></div>
<div class="params"<?php if ($paramsHeight): ?> style="height: <?= $paramsHeight ?>px;" <?php endif; ?>>
				<?php if (!empty($product['Authors'])):
					$author = array_shift($product['Authors']);
					?>
					<div><?= ProductHelper::GetTitle($author) ?></div>
				<?php endif; ?>

				<?php if (!empty($product['isbn'])&&!in_array($product['entity'], array(Entity::SHEETMUSIC/*, Entity::MUSIC*/))): ?>
				<div>ISBN: <?= str_replace('-', '' ,$product['isbn']) ?></div>
				<?php endif; ?>
				<?php if (!empty($product['eancode'])&&in_array($product['entity'], array(Entity::SHEETMUSIC/*, Entity::MUSIC*/))) : ?>
				<div>EAN: <?= str_replace('-', '' ,$product['eancode']) ?></div>
				<?php endif; ?>

				<?php if ($product['year']): ?>
				<div><?= Yii::app()->ui->item('A_NEW_YEAR') ?>: <?= $product['year'] ?></div>
				<?php endif; ?>

				<?php if ($product['binding_id']): ?>
				<div title="<?= htmlspecialchars(ProductHelper::GetTitle($product['Binding'])) ?>"><?= ProductHelper::GetTitle($product['Binding']) ?></div>
				<?php endif; ?>
</div>

				<?php //$price = DiscountManager::GetPrice(Yii::app()->user->id, $product); ?>
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

						<?php /*if (!empty($price[DiscountManager::DISCOUNT])): ?>
				<span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;"><?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]) ?>
	            </span>&nbsp;<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold;">
	                <?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) ?>
	            </span>
						<?php else: ?>
				<span class="price"><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) ?></span>
						<?php endif*/ ?>
					</div>
					<div class="nds"<?php if($product['entity'] == Entity::PERIODIC):?> style="display: none;" <?php endif; ?>><?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $product['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
				</div>
				<?php /*<div class="nds"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]) . Yii::app()->ui->item('WITHOUT_VAT') ?></div> */ ?>
				<?php if ($product['entity'] == Entity::PERIODIC): ?>
					<a href="<?=$url;?>" class="btn_yellow fa"><span class="lang-<?= Yii::app()->getLanguage() ?>"><?= Yii::app()->ui->item('A_NEW_MORE3') ?></span></a>
				<?php else: ?>
				<div class="addcart">
				
				
					<a class="cart-action add_cart_plus" data-action="add" data-entity="<?= $product['entity'] ?>" data-id="<?= $product['id'] ?>" data-quantity="1" href="javascript:;"><span>
	<?= Yii::app()->ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART') ?></span>
					</a>
				</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		scriptLoader('/new_js/slick.js').callFunction(function() {
			$('.more_goods ul').slick({
				lazyLoad: 'ondemand',
				slidesToShow: <?= ($eid == Entity::PERIODIC)?5:4 ?>,
				slidesToScroll: <?= ($eid == Entity::PERIODIC)?5:4 ?>
			}).on('lazyLoadError', function(event, slick, image, imageSource){
				image.attr('src', '<?= Picture::srcNoPhoto() ?>');
			});
		});
	});
</script>