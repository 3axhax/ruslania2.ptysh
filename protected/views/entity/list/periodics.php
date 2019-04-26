<?php /*Created by Кирилл (26.04.2019 22:15)*/
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
$binding = PereodicsTypes::model()->GetBinding($entity, $item['type']);
$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
$item['issues_year'] = Periodic::getCountIssues($item['issues_year']);
?>
<div class="row" style="
/* background: url(/new_img/podpiska.gif) right 0px no-repeat; */
/* background-size: 98%;*/
">
	<div class="span1 image_item" style="position: relative; width: 199px; text-align: right;">
		<?php $this->renderStatusLables($productModel->GetStatusProduct($item['entity'], $item['id']))?>
		<a href="<?= $url; ?>" title="<?= ProductHelper::GetTitle($item); ?>">
			<img height="241" lazySrc="<?= Picture::Get($item, Picture::BIG); ?>" src="<?= Picture::srcLoad() ?>" alt="<?= htmlspecialchars(ProductHelper::GetTitle($item)); ?>">
		</a>
	</div>
	<div class="span11" style="width: 305px; padding: 40px 65px 0 29px; margin: 0;">
		<a href="<?= $url; ?>" class="title" style="line-height: 27px; font-weight: bold; padding-bottom: 6px; margin: 0;"><?= ProductHelper::GetTitle($item); ?></a>
		<?php if (isset($item['type'])): ?>
			<div class="authors" style="margin-top: 16px;">
				<div style="float: left;width: 120px;" class="nameprop"><?=$ui->item('A_NEW_TYPE_IZD')?></div>
				<div style="padding-left: 130px;font-weight: 600;"><a href="<?= Yii::app()->createUrl('entity/bytype', array('entity' => $entityKey, 'type' => $item['type'])) ?>"><?= ProductHelper::GetTitle($binding) ?></a></div>
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
				<div class="authors" style="margin-top: 16px;">
					<div style="float: left;width: 120px;" class="nameprop"><?= str_replace(':', '', $ui->item('CATALOGINDEX_CHANGE_LANGUAGE')) ?></div>
					<div style="padding-left: 130px;font-weight: 600;"><?= implode(', ', $langs) ?></div>
					<div class="clearBoth"></div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<div class="desc_text" style="margin: 20px 0 0 0;letter-spacing: 0.5px;font-weight: 300;line-height: 23px;"><?= nl2br(ProductHelper::GetDescription($item, 250, $url)) ?></div>
	</div>

	<div class="span1 cart to_cart"
	     style="overflow: hidden;padding: 43px 15px 30px 30px;margin: 0;background-color: #f8f8f8;">
		<select class="periodic" style="margin-right: 0;margin-bottom: 22px;width: 242px;color: #747474;height: 38px;border: solid 2px #ececec;padding-left: 15px;padding-right: 35px;font-size: 14px;letter-spacing: -0.2px;background-color: rgba(255, 255, 255, 0);">
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
		</select>

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
		<div class="mb5" style="margin-top: 17px;color:#0A6C9D;float: left;font-weight: 600;margin-bottom: 28px;"><?= $ui->item('MSG_DELIVERY_TYPE_4') ?></div>

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
				<a class="cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$item['id']?> green_cart" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;">
					<span><?= Yii::app()->ui->item('CARTNEW_IN_CART_BTN', $item['AlreadyInCart']) ?></span>
				</a>
			<? else : ?>
				<a class="cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;">
					<span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></span>
				</a>
			<? endif; ?>
			<a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>"
			   data-id="<?= $item['id']; ?>" class="addmark cart-action" style="margin-left: 10px"><i class="fa fa-heart" aria-hidden="true"></i></a>
		</form>
	</div>

</div>
