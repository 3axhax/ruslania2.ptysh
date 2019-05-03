<?php
/*Created by Кирилл (01.05.2019 20:41)*/
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
//$item['issues_year'] = Periodic::getCountIssues($item['issues_year']);
?>
<div class="container view_product">
	<div class="periodics" style="/*background: url(/new_img/podpiska_d.jpg) -80px -70px no-repeat;*/">
		<div class="detail_block">
			<div class="image_item">
				<?php $this->renderStatusLables($item['status']); ?>
				<img class="img-view_product" alt="<?= ProductHelper::GetTitle($item); ?>" title="<?= ProductHelper::GetTitle($item); ?>" src="<?= Picture::Get($item, Picture::BIG); ?>">
				<?php if (!empty($item['Lookinside'])) $this->renderPartial('lookinside', array('item' => $item, 'entity' => $entity)); ?>
			</div>
			<div class="info_item">
				<h1 class="title"><?= ProductHelper::GetTitle($item); ?></h1>
				<?php if ($item['title_original']) : ?>
					<div class="prop">
						<div class="prop-name"><?= str_replace(':', '', $ui->item("ORIGINAL_NAME")) ?></div>
						<div class="prop-value"><?=$item['title_original']?></div>
						<div class="clearBoth"></div>
					</div>
				<? endif; ?>
				<?php if ($item['type']): ?>
					<div class="prop">
						<div class="prop-name"><?= $ui->item('A_NEW_TYPE_IZD') ?></div>
						<div class="prop-value">
							<a href="<?= Yii::app()->createUrl('entity/bytype', array('entity' => $entityKey, 'type' => $item['type'])) ?>">
								<?= ProductHelper::GetTitle(PereodicsTypes::model()->GetBinding($entity, $item['type'])) ?>
							</a>
						</div>
						<div class="clearBoth"></div>
					</div>
				<?php endif; ?>
				<?php if (!empty($item['Country'])) : ?>
					<div class="prop">
						<div class="prop-name"><?= str_replace(':', '', sprintf($ui->item("COUNTRY_OF_ORIGIN"), '')) ?></div>
						<div class="prop-value">
							<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'country'=>$item['Country']['id'])) ?>">
								<?= ProductHelper::GetTitle($item['Country']) ?>
							</a>
						</div>
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
						<div class="prop">
							<div class="prop-name"><?= str_replace(':', '', $ui->item('CATALOGINDEX_CHANGE_LANGUAGE')) ?></div>
							<div class="prop-value"><?= implode(', ', $langs) ?></div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<?php if (!empty($item['issn'])) : ?>
					<div class="prop">
						<div class="prop-name">ISSN</div>
						<div class="prop-value"><?= $item['issn'] ?></div>
						<div class="clearBoth"></div>
					</div>
				<?php endif; ?>
				<?php if (!empty($item['index'])) : ?>
					<div class="prop">
						<div class="prop-name"><?= str_replace(':', '', sprintf($ui->item("PERIOD_INDEX"), '')); ?></div>
						<div class="prop-value"><?= $item['index'] ?></div>
						<div class="clearBoth"></div>
					</div>
				<?php endif ?>
				<?php if (!empty($item['issues_year'])):
					list($month, $label_for_month, $issues, $label_for_issues, $x_issues_in_year, $issues_year) = PereodicsTypes::model()->issuesYear($item['issues_year']);
					?>
					<div class="prop">
						<div class="prop-name"><?= $ui->item('PERIODICITY') ?></div>
						<div class="prop-value"><?= sprintf($x_issues_in_year, $issues_year) ?></div>
						<div class="clearBoth"></div>
					</div>
					<div class="prop">
						<div class="prop-name"><?= $ui->item('MIN_PODPISKA') ?></div>
						<div class="prop-value"><?= $month ?> <?= $label_for_month ?> / <?= $issues ?> <?= $label_for_issues ?></div>
						<div class="clearBoth"></div>
					</div>
				<?php endif; ?>
			</div>
			<div class="price_item">
		<span class="arrow_down"><select class="periodic">
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

				<div style="font-weight: bold; letter-spacing: -0.4px; font-size: 18px;"><?= $ui->item('PRICE_WITH_DELIVERY') ?></div>
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
							<span><?= Yii::app()->ui->item('CARTNEW_IN_CART_BTN0') ?></span>
						</a>
					<? else : ?>
						<a class="cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;">
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




		<div class="clearfix"></div>
		<? $comments = Comments::get_list($entity, $item['id']); ?>
		<div class="tabs_container" style="margin-top: 54px;">
			<ul class="tabs">
				<li class="desc active"><a href="javascript:;"><?=$ui->item('A_NEW_DESC_TAB')?></a></li>
				<!--<li class="review"><a href="javascript:;"><?=$ui->item('A_NEW_REVIEWS_TAB')?> (<?=count($comments)?>)</a></li>-->
			</ul>

			<div class="tabcontent desc active">
					<?php if(!empty($item['presaleMessage'])): ?>
						<div class="presale" style="padding: 10px; margin-bottom: 20px; background-color: #edb421; color: #fff;"><?= $item['presaleMessage'] ?></div>
					<?php endif; ?>
					<?= nl2br(ProductHelper::GetDescription($item)); ?>
				<?php if ((!empty($item['age_limit_flag']) && Yii::app()->language == 'fi')) : ?>
					<?php
					$flag = $item['age_limit_flag'];
					$ret = '';
					if (($flag & 1) == 1) $ret .= '<img src="/pic1/fi-sallittu.png" width="32" height="32" alt="Sallittu" title="Sallittu" /> ';
					if (($flag & 2) == 2) $ret .= '<img src="/pic1/fi-7.png" width="32" height="32"  alt="K-7" title="K-7"/> ';
					if (($flag & 4) == 4) $ret .= '<img src="/pic1/fi-12.png" width="32" height="32"  alt="K-12" title="K-12"/> ';
					if (($flag & 8) == 8) $ret .= '<img src="/pic1/fi-16.png" width="32" height="32"  alt="K-16" title="K-16"/> ';
					if (($flag & 16) == 16) $ret .= '<img src="/pic1/fi-18.png" width="32" height="32" alt="K-18" title="K-18" /> ';
					if (($flag & 32) == 32) $ret .= '<img src="/pic1/fi-ahdistus.png" width="32" height="32" alt="Ahdistus" title="Ahdistus" /> ';
					if (($flag & 64) == 64) $ret .= '<img src="/pic1/fi-paihteet.png" width="32" height="32" alt="P&auml;ihteet" title="P&auml;ihteet" /> ';
					if (($flag & 128) == 128) $ret .= '<img src="/pic1/fi-seksi.png" width="32" height="32" alt="Seksi" title="Seksi"/> ';
					if (($flag & 256) == 256) $ret .= '<img src="/pic1/fi-vakivalta.png" width="32" height="32" alt="Vakivalta" title="Vakivalta"/> ';

					if (!empty($ret)) echo '<br/>' . $ret . '<br/>';
					?>

					<?php if ($item['agelimit'] == 12)
						echo '<br/>Vapautettu luokittelusta<br/>'; ?>

				<?php endif; ?>

				<?php
				$cat = array();
				if (!empty($item['Category']))
					$cat[] = $item['Category'];
				if (!empty($item['SubCategory']))
					$cat[] = $item['SubCategory'];
				?>
				<?php if (!empty($cat)) : ?>
					<div class="blue_arrow text" style="margin: 34px 0 23px">
						<div class="detail-prop">
							<div class="prop-name"><?= str_replace(':', '', $ui->item('Related categories')); ?></div>
							<div class="prop-value">
								<?php $i = 0; foreach ($cat as $c) : $i++; ?>
									<?php $catTitle = ProductHelper::GetTitle($c); ?>
									<a href="<?=
									Yii::app()->createUrl('entity/list', array('entity' => $entityKey,
										'cid' => $c['id'],
										'title' => ProductHelper::ToAscii($catTitle)
									));
									?>" class="catlist"><?= $catTitle; ?></a><?if ($i < count($cat)) { ?><br /><? } ?>
								<?php endforeach; ?></div>
							<div class="clearBoth"></div>
						</div>
					</div>
				<?php endif; ?>

				<?php if (!empty($item['eancode'])):
					//не поймешь, то надо то не надо https://dfaktor.bitrix24.ru/company/personal/user/836/tasks/task/view/6810/
					?>
					<div class="detail-prop">
						<div class="prop-name">EAN</div>
						<div class="prop-value"><?= $item['eancode']; ?></div>
						<div class="clearBoth"></div>
					</div>
				<?php endif; ?>

				<?php $this->widget('OffersByItem', array('entity'=>$entity, 'idItem'=>$item['id'], 'index_show'=>0)) ?>

			</div>
		</div>

		<?php $this->widget('YouView', array('entity'=>$entity, 'id'=>$item['id'], 'tpl'=>'you_view_content')); ?>
		<?php $this->widget('Similar', array('entity'=>$entity, 'item'=>$item)); ?>
		<?php $this->widget('Banners', array('entity'=>$entity)); ?>


		<script type="text/javascript">
			$(document).ready(function () {
				$('.selquantity').change(function(){

					$('.cart-action').attr('data-quantity', $('.selquantity').val());
				});
			})
		</script>
	</div>
</div>
<?php $this->widget('Banners', array('type'=>'slider', 'item'=>$item)) ?>