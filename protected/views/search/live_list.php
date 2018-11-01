<?php /*Created by Кирилл (21.06.2018 21:40)*/ ?>
<div class="row_item">
	<table>
		<tr>
			<td class="pic">
<?php
$url = ProductHelper::CreateUrl($item);
$urlPicture = ProductHelper::Link2Picture($item, true);
if (!empty($urlPicture)&&($urlPicture != 'http://ruslania.com/pictures/small/')): ?>
				<a href="<?= $url ?>"><img style="max-width: 100%;max-height:86px;" src="<?= $urlPicture ?>" /></a>
<?php endif; ?>
			</td>
			<td class="name">
				<a href="<?= $url ?>"><?= ProductHelper::GetTitle($item) ?></a>
				<?php if (!empty($item['inDescription'])): ?>
					<div><?= Yii::app()->ui->item('IN_DESCRIPTION') ?>: <?= $item['inDescription'] ?></div>
				<?php /*else: ?>
				<div style="height: 18px;"></div>
				<?php */endif; ?>
				<?php if ($item['avail_for_order'] > 0):
				$item['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
				$item['priceData']['unit'] = '';
				if ($item['entity'] == Entity::PERIODIC):
					$issues = Periodic::getCountIssues($item['issues_year']);
					if (!empty($issues['show3Months'])) {
						$item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
						$item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/4;
						$item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/4;
						$item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/4;
					}
					elseif (!empty($issues['show6Months'])) {
						$item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
						$item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/2;
						$item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/2;
						$item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/2;
					}
					else {
						$item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
					}
				endif; ?>
					<div class="cost">
						<?php if (!empty($item['priceData'][DiscountManager::DISCOUNT])) : ?>
							<span class="without_discount">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::BRUTTO]); ?>
                </span>&nbsp;
							<span class="price with_discount">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
						<?php else : ?>
							<span class="price">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
						<?php endif; ?>
					</div>
				<?php if ($item['entity'] != Entity::PERIODIC): ?>
					<div style="color: #747474; font-size: 10px;"><?= Availability::ToStr($item) ?><?php endif; ?></div>
				<?php else: ?>
					<div style="color: #747474; font-size: 10px;""><?= Availability::ToStr($item) ?></div>
				<?php endif; ?>

			</td>
		</tr>
	</table>
</div>