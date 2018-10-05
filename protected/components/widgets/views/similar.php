<?php /*Created by Кирилл (27.07.2018 21:15)*/ ?>
<div class="news_box" style="margin-top: 40px;">
	<div class="">
		<div class="title">
			<?= Yii::app()->ui->item('A_NEW_RELATION_ITEMS') ?>
			<div class="pult">
				<a onclick="$('.news_box .btn_left.slick-arrow').click(); return false;" class="btn_left"><img src="/new_img/btn_left_news.png" alt=""></a>
				<a onclick="$('.news_box .btn_right.slick-arrow').click(); return false;" class="btn_right"><img src="/new_img/btn_right_news.png" alt=""></a>
			</div>
		</div>
	</div>
	<div class="more_goods" style="overflow: hidden">
		<ul class="books">
		<?php foreach ($items as $product):
			$title = ProductHelper::GetTitle($product, 'title', 42);
			$url = ProductHelper::CreateUrl($product);
			?>
			<li>
				<div class="img" style="min-height: 130px; position: relative">
					<?php Yii::app()->getController()->renderStatusLables($product['status']); ?>
					<a href="<?= $url ?>" title="<?= $title ?>">
						<img title="<?= $title ?>" alt="<?= $title ?>" src="<?= Picture::Get($product, Picture::SMALL) ?>" style="max-height: 130px;"/>
					</a>
				</div>

				<div class="title_book"<?php if (!empty($product['Authors'])): ?> style="height:29px;min-height:auto;margin-bottom:0;" <?php endif; ?>><a href="<?= $url ?>" title="<?= $title ?>"><?= $title ?></a></div>

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
				<?= ProductHelper::GetTitle($product['Binding']) ?>
				<?php endif; ?>

				<?php //$price = DiscountManager::GetPrice(Yii::app()->user->id, $product); ?>
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

					<?php /*if (!empty($price[DiscountManager::DISCOUNT])): ?>
			<span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;"><?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]) ?>
            </span>&nbsp;<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold;">
                <?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) ?>
            </span>
					<?php else: ?>
			<span class="price"><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]) ?></span>
					<?php endif*/ ?>
				</div>
				<div class="nds"><?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $product['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
				<?php /*<div class="nds"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]) . Yii::app()->ui->item('WITHOUT_VAT') ?></div> */ ?>
				<?php if ($product['entity'] == Entity::PERIODIC): ?>
					<a href="<?=$url;?>" class="btn_yellow"><?= Yii::app()->ui->item('A_NEW_MORE3') ?></a>
				<?php else: ?>
				<div class="addcart">
					<a class="cart-action" data-action="add" data-entity="<?= $product['entity'] ?>" data-id="<?= $product['id'] ?>" data-quantity="1" href="javascript:;">
	<?= Yii::app()->ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART') ?>
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
		$('.more_goods ul').slick({
			lazyLoad: 'ondemand',
			slidesToShow: 4,
			slidesToScroll: 4
		});
	});
</script>