<?php /*Created by Кирилл (21.06.2018 21:40)*/ ?>
<div class="row_item">
	<table>
		<tr>
			<td class="pic">
<?php
$url = ProductHelper::CreateUrl($item);
$urlPicture = ProductHelper::Link2Picture($item, true);
if (!empty($urlPicture)&&($urlPicture != 'http://ruslania.com/pictures/small/')): ?>
				<a href="<?= $url ?>"><img style="max-width: 100%;" height="86" src="<?= $urlPicture ?>" /></a>
<?php endif; ?>
			</td>
			<td class="name">
				<a href="<?= $url ?>"><?= ProductHelper::GetTitle($item) ?></a>
				<?php if (!empty($item['inDescription'])): ?>
					<div><?= Yii::app()->ui->item('IN_DESCRIPTION') ?>: <?= $item['inDescription'] ?></div>
				<?php else: ?>
				<div style="height: 18px;"></div>
				<?php endif; ?>
				<?php
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
							<span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::BRUTTO]); ?>
                </span>&nbsp;
							<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold; white-space: nowrap;">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
						<?php else : ?>
							<span class="price">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
						<?php endif; ?>
					</div>
			</td>
		</tr>
	</table>
</div>