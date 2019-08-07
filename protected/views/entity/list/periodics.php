<?php /*Created by Кирилл (26.04.2019 22:15)*/
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
$binding = PereodicsTypes::model()->GetBinding($entity, $item['type']);
$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
$item['issues_year'] = Periodic::getCountIssues($item['issues_year']);
$label = $productModel->GetStatusProduct($item['entity'], $item['id']);
/**@var $photoModel ModelsPhotos*/
$photoModel = Pereodics_photos::model();
$photoId = $photoModel->getFirstId($item['id']);
?>
<div class="row <?= Entity::GetUrlKey(Entity::PERIODIC) ?>">
	<div class="image_item">
		<?php $this->renderStatusLables($label) ?>
		<a href="<?= $url; ?>" title="<?= ProductHelper::GetTitle($item); ?>">
			<?php if (empty($photoId)): ?>
				<img height="241" lazySrc="<?= Picture::Get($item, Picture::SMALL); ?>" src="<?= Picture::srcLoad() ?>" alt="<?= htmlspecialchars(ProductHelper::GetTitle($item)); ?>">
			<?php else: ?>
				<picture>
					<source srcset="<?= $photoModel->getHrefPath($photoId, 'l', $item['eancode'], 'webp') ?>" type="image/webp">
					<source srcset="<?= $photoModel->getHrefPath($photoId, 'l', $item['eancode'], 'jpg') ?>" type="image/jpeg">
					<img src="<?= $photoModel->getHrefPath($photoId, 'o', $item['eancode'], 'jpg') ?>" alt="<?= htmlspecialchars(ProductHelper::GetTitle($item)); ?>">
				</picture>
			<?php endif; ?>
		</a>
	</div>
	<div class="info_item">
		<a href="<?= $url; ?>" class="title"><?= ProductHelper::GetTitle($item); ?></a>
		<?php if (isset($item['type'])): ?>
			<div class="prop">
				<div class="prop-name"><?=$ui->item('A_NEW_TYPE_IZD')?></div>
				<div class="prop-value"><a href="<?= Yii::app()->createUrl('entity/bytype', array('entity' => $entityKey, 'type' => $item['type'])) ?>"><?= ProductHelper::GetTitle($binding) ?></a></div>
				<div class="clearBoth"></div>
			</div>
		<?php endif; ?>
		<?php if (!empty($item['Languages'])):
			$langs = array();
			foreach ($item['Languages'] as $lang) {
				if (!empty($lang['language_id'])) {
					$langs[] = '<a href="' . Yii::app()->createUrl('entity/list', array('entity' => $entityKey, 'lang' => $lang['language_id'])) . '">' . (($entity != Entity::PRINTED)?Language::GetTitleByID($lang['language_id']):Language::GetTitleByID_country($lang['language_id'])) . '</a>';
				}
			}
			if (!empty($langs)): ?>
				<div class="prop" style="margin-top: 16px;">
					<div class="prop-name"><?= str_replace(':', '', $ui->item('CATALOGINDEX_CHANGE_LANGUAGE')) ?></div>
					<div class="prop-value"><?= implode(', ', $langs) ?></div>
					<div class="clearBoth"></div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<div class="desc_text"><?= nl2br(ProductHelper::GetDescription($item, 100, $url)) ?></div>
	</div>

	<div class="price_item">
		<span class="arrow_down select"><select class="periodic">
			<?php if ($item['issues_year']['show3Months']) : $count_add = 3; ?>
				<option value="3" selected="selected">3 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_2'); ?> - <?= $item['issues_year']['issues'] ?> <?= $item['issues_year']['label_for_issues'] ?></option>
			<?php endif; ?>
			<?php if ($item['issues_year']['show6Months']) :
				$labelForIssues6 = $item['issues_year']['label_for_issues'];
				$issues6 = $item['issues_year']['issues'];
				if (!empty($item['issues_year']['show3Months'])):
					$issues6 = $item['issues_year']['issues']*2;
					if (in_array($issues6, array(2, 4))): $labelForIssues6 = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_ISSUE_2");
					else: $labelForIssues6 = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_ISSUE_3");
					endif;
				else: $count_add = 6;
				endif;
				?>
				<option value="6"<?php if(empty($item['issues_year']['show3Months'])): $count_add = 6; ?> selected="selected"<?php endif; ?>>6 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?> - <?= $issues6 ?> <?= $labelForIssues6 ?></option>
			<?php endif;
			$labelForIssues12 = $item['issues_year']['label_for_issues'];
			if (in_array($item['issues_year']['issues_year'], array(2, 3, 4))): $labelForIssues12 = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_ISSUE_2");
			elseif ($item['issues_year']['issues_year'] > 1): $labelForIssues12 = $ui->item("MIN_FOR_X_MONTHS_Y_ISSUES_ISSUE_3");
			endif;
			?>
			<option value="12"<?php if(empty($item['issues_year']['show3Months'])&&empty($item['issues_year']['show6Months'])): $count_add = 12; ?> selected="selected"<?php endif; ?>>12 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?> - <?= $item['issues_year']['issues_year'] ?> <?= $labelForIssues12 ?></option>
		</select></span>

		<div style="font-weight: bold;letter-spacing: 1px;"><?= $ui->item('PRICE_WITH_DELIVERY') ?></div>
		<?php $this->renderPartial('/entity/list/_priceInfo', array(
			'key' => 'PERIODIC_FIN',
			'item' => $item,
			'price' => $price,
		)); ?>
		<?php $this->renderPartial('/entity/list/_priceInfo', array(
			'key' => 'PERIODIC_WORLD',
			'item' => $item,
			'price' => $price,
		)); ?>
		<div class="clearBoth" style="margin-bottom: 28px;"></div>
		<?php /*<div class="free_delivery"><?= $ui->item('MSG_DELIVERY_TYPE_4') ?></div> */ ?>

		<input type="hidden" value="<?= round($price[DiscountManager::BRUTTO_WORLD] / 12, 2); ?>" class="worldmonthpriceoriginal"/>
		<input type="hidden" value="<?= round($price[DiscountManager::BRUTTO_FIN] / 12, 2); ?>" class="finmonthpriceoriginal"/>
		<input type="hidden" value="<?= round($price[DiscountManager::WITH_VAT_WORLD] / 12, 2); ?>" class="worldmonthpricevat"/>
		<input type="hidden" value="<?= round($price[DiscountManager::WITHOUT_VAT_WORLD] / 12, 2); ?>" class="worldmonthpricevat0"/>
		<input type="hidden" value="<?= round($price[DiscountManager::WITH_VAT_FIN] / 12, 2); ?>" class="finmonthpricevat"/>
		<input type="hidden" value="<?= round($price[DiscountManager::WITHOUT_VAT_FIN] / 12, 2); ?>" class="finmonthpricevat0"/>


		<form method="get" action="<?= Yii::app()->createUrl('cart/view') ?>" onsubmit="return false;">
			<?php if (empty($count_add)) $count_add = 12; ?>
			<input type="hidden" name="entity[<?= (int) $item['id'] ?>]" value="<?= (int) $item['entity'] ?>">
			<?php if (isset($item['AlreadyInCart'])) : ?>
				<a class="cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$item['id']?> green_cart" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;"  onclick="searchTargets('add_cart_listing');">
					<span><?= Yii::app()->ui->item('CARTNEW_IN_CART_BTN0') ?></span>
				</a>
			<? else : ?>
				<a class="cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;" onclick="searchTargets('add_cart_listing');">
					<span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></span>
				</a>
			<? endif; ?>
			<span class="notes">
				<a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" class="addmark cart-action" style="margin-left: 10px">
					<i class="fa fa-heart" aria-hidden="true"></i>
				</a>
				<span class="notes-block favorite"><?= $ui->item('BTN_SHOPCART_ADD_SUSPEND_ALT') ?></span>
			</span>
		</form>
	</div>

</div>
