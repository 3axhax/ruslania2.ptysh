<?php /*Created by Кирилл (19.07.2019 21:49)*/ ?>
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
<div class="nds"<?php if($product['entity'] == Entity::PERIODIC):?> style="<?=(($product['priceData'][DiscountManager::DISCOUNT] == '0') ? 'visibility: hidden; white-space: nowrap;' : 'display: none')?>" <?php endif; ?>><?= ProductHelper::FormatPrice($product['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $product['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
