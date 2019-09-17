<?php /**Created by Кирилл kirill.ruh@gmail.com 11.09.2019 8:31 */ ?>
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container view_product">
	<div class="row" style="float: left">
		<div class="span10" itemscope itemtype="https://schema.org/Book">
			<?php $this->renderPartial('is_purchased', array('item' => $item, 'entity' => $entity)); ?>
			<?php
				$url = ProductHelper::CreateUrl($item);
				$title = ProductHelper::GetTitle($item);
				$entityKey = Entity::GetUrlKey($entity);
			?>
			<div class="row">
				<div class="span1" style="position: relative">
					<?php $this->renderPartial('photo', array('item' => $item, 'entity' => $entity, 'title'=>$title)); ?>
					<?php if (!empty($item['Lookinside'])) $this->renderPartial('lookinside', array('item' => $item, 'entity' => $entity)); ?>
				</div>
				<div class="span11 to_cart"><h1 class="title" itemprop="name"><?= $title ?></h1>
					<?php if ($item['title_original']) : ?>
						<div class="authors" style="margin-bottom:10px;">
							<div style="float: left;width: 220px;" class="nameprop"><?= str_replace(':', '', $ui->item("ORIGINAL_NAME")) ?></div>
							<div style="padding-left: 253px;"><?=$item['title_original']?></div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>
					<?php if (!empty($item['Authors'])) :
						foreach ($item['Authors'] as $author):
							$authorTitle = ProductHelper::GetTitle($author);
							$tmp[] = '<a href="' . Yii::app()->createUrl('entity/byauthor', array('entity' => $entityKey,
									'aid' => $author['id'],
									'title' => ProductHelper::ToAscii($authorTitle))) . '" class="cprop" itemprop="author">'
								. $authorTitle . '</a>';
						endforeach;
						?>
						<div class="authors" style="margin-bottom:5px;">
							<div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("WRITTEN_BY"), '')); ?></div>
							<div style="padding-left: 253px;"><?= implode(', ', $tmp); ?></div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>

					<?php if (!empty($item['Languages'])) :
						$langs = array();
						foreach ($item['Languages'] as $lang) {
							if (!empty($lang['language_id'])) $langs[] = '<a href="' . Yii::app()->createUrl('entity/list', array(
									'entity' => $entityKey,
									'lang' => $lang['language_id'])) .
								'"><span class="title__bold" itemprop="inLanguage">' . (($entity != Entity::PRINTED)?Language::GetTitleByID($lang['language_id']):Language::GetTitleByID_country($lang['language_id'])) . '</span></a>';
						}
						if (!empty($langs)):
							?>
							<div class="authors" style="margin-bottom:5px;">
								<div style="float: left;" class="nameprop"><?= ($entity == Entity::PRINTED) ? str_replace(':', '', $ui->item("CATALOGINDEX_CHANGE_THEME")) : str_replace(':', '', $ui->item("CATALOGINDEX_CHANGE_LANGUAGE")); ?></div>
								<div style="padding-left: 253px;"><?= implode(', ', $langs) ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; endif; ?>

					<?php if (!empty($item['format'])) : ?>
						<div class="authors" style="margin-bottom:5px;">
							<div style="float: left;" class="nameprop"><?= str_replace(':', '', $ui->item("Media")); ?></div>
							<div style="padding-left: 253px;" itemprop="bookFormat"><?= $item['format'] ?></div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>

					<?php if (!empty($item['Publisher'])) :
						$pubTitle = ProductHelper::GetTitle($item['Publisher']);?>
						<div class="authors" style="margin-bottom:5px;">
							<div style="float: left;" class="nameprop">
								<?php
								if ($entity == Entity::MUSIC) echo str_replace(':', '', $ui->item('A_NEW_LABEL'));
								elseif ($entity == Entity::SOFT || $entity == Entity::MAPS || $entity == Entity::PRINTED) echo str_replace(':', '', $ui->item('A_NEW_PRODUCER'));
								else echo str_replace(':', '', sprintf($ui->item("Published by"), ''));
								?>
							</div>
							<div style="padding-left: 253px;">
								<a class="cprop" href="<?= Yii::app()->createUrl('entity/bypublisher', array('entity' => $entityKey,
									'pid' => $item['Publisher']['id'],
									'title' => ProductHelper::ToAscii($pubTitle)));
								?>" itemprop="publisher"><?= $pubTitle; ?></a>
							</div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>

					<?php if (!empty($item['year'])) : ?>
						<div class="authors" style="margin-bottom:5px;">
							<div style="float: left;" class="nameprop"><?= ($entity != Entity::VIDEO) ? str_replace(':', '', $ui->item('A_NEW_YEAR')) : str_replace(':', '', $ui->item('A_NEW_YEAR_REAL')) ?></div>
							<div style="padding-left: 253px;">
								<a href="<?= Yii::app()->createUrl('entity/byyear', array(
									'entity' => $entityKey,
									'year' => $item['year']));
								?>" itemprop="datePublished"><?=$item['year']?></a>
							</div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>

					<?php if (!empty($item['binding_id'])/*&&!empty($item['Binding']['title_' . Yii::app()->language])*/): ?>
						<div class="authors" style="margin-bottom:5px;">
							<?php $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE1'); ?>
							<div style="float: left;" class="nameprop"><?= str_replace(':', '', $label); ?></div>
							<div style="padding-left: 253px;"><a href="<?= Yii::app()->createUrl('entity/bybinding', array(
									'entity' => $entityKey,
									'bid' => $item['binding_id'],
									'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($item['Binding'])),
								));
								?>"><?= ProductHelper::GetTitle($item['Binding']) ?></a></div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>

					<?php if (!empty($item['numpages'])) : ?>
						<div class="authors" style="margin-bottom:5px;">
							<div style="float: left;" class="nameprop"><?= str_replace(':', '', $ui->item("A_NEW_COUNT_PAGE")); ?></div>
							<div style="padding-left: 253px;" itemprop="numberOfPages"><?= $item['numpages'] ?></div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>

					<?php if (!empty($item['isbn'])) :
						$name = 'ISBN';
					?>
						<div class="authors" style="margin-bottom:5px;">
							<div style="float: left;" class="nameprop"><?= str_replace(':', '', $name) ?></div>
							<div style="padding-left: 253px;" itemprop="isbn"><?= $item['isbn'] ?></div>
							<div class="clearBoth"></div>
						</div>
					<?php endif; ?>

					<?php
					$price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
					$isAvail = ProductHelper::IsAvailableForOrder($item);
					?>
					<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<?php if (Availability::GetStatus($item) != Availability::NOT_AVAIL_AT_ALL) :
						$this->renderPartial('/entity/_priceInfo_notperiodica', array('key' => 'ITEM',
							'item' => $item,
							'price' => $price)
						);
					?>
						<meta itemprop="price" content="<?= $price[DiscountManager::BRUTTO_WORLD] ?>">
						<meta itemprop="priceCurrency" content="<?= Currency::ToStr(Yii::app()->currency) ?>">
					<?php endif; ?>


					<div class="already-in-cart" style="margin-top: 30px; float: left; margin-left: 33px; position: relative;">
						<div class="price_h">&nbsp;</div>
						<div class="price_h">&nbsp;</div>
						<div class="mb5" style="color:#4e7eb5; width: 200px; font-size: 13px; ">
							<span style="position: absolute; bottom: 0px; left: 0;"><?= Availability::ToStr($item); ?></span>
						</div>
						<link itemprop="availability" href="http://schema.org/<?= Availability::toSchema($item); ?>">
					</div>
					</div>
					<div class="clearfix"></div>
					<?php $quantity = 1; ?>
					<?php if ($isAvail) : ?>
						<div class="clearfix"></div>
						<div style="margin-top: 10px;"></div>
					<?php else : ?>
						<div class="clearBoth"></div>
						<?php if (Yii::app()->user->isGuest) : ?>
							<a href="<?=
							Yii::app()->createUrl('cart/dorequest', array('entity' => Entity::GetUrlKey($item['entity']),
								'iid' => $item['id']));
							?>" class="ca request"><?=$ui->item('CART_COL_ITEM_MOVE_TO_ORDERED'); ?></a>

						<?php endif; ?>
					<?php endif; ?>
					<?php if ($isAvail) : ?>
						<?php if (isset($item['AlreadyInCart'])) : ?>

							<a class="cart-action add_cart add_cart_plus add_cart_view green_cart cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" data-hidecount="1" href="javascript:;" onclick="searchTargets('add_cart_view_product');">
								<span style="padding: 0 17px 0 20px;"><?= $ui->item('CARTNEW_IN_CART_BTN', $item['AlreadyInCart']) ?></span></a>

						<?php else : ?>

							<a class="cart-action add_cart add_cart_plus add_cart_view cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" data-hidecount="1" href="javascript:;" onclick="searchTargets('add_cart_view_product');">
								<span style="padding: 0 17px 0 20px;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></span></a>

						<?php endif; ?>
					<?php endif;

					$class_mark = '';
					$key_btn = 'BTN_SHOPCART_ADD_SUSPEND_ALT';
					if (Cart::model()->isMark($item['entity'], $item['id'],Cart::TYPE_MARK, $this->uid, $this->sid)) {
						$class_mark = ' active';
						$key_btn = 'BTN_SHOPCART_DELETE_SUSPEND_ALT';
					}
					?>
					<a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>" style="margin-left: 19px;"
					   data-id="<?= $item['id']; ?>" class="addmark cart-action<?=$class_mark?>"><i class="fa fa-heart" aria-hidden="true"></i><span class="tooltip"><span class="arrow"></span><?=$ui->item($key_btn)?></span></a>
				</div>
			</div>
			<div class="clearfix"></div>
			<? //$comments = Comments::model()->get_list($entity, $item['id']); ?>
			<div class="tabs_container">
				<ul class="tabs">
					<li class="desc active"><a href="javascript:;"><?=$ui->item('A_NEW_DESC_TAB')?></a></li>
				</ul>
				<div class="tabcontent desc active">
					<?php if(!empty($item['presaleMessage'])): ?>
						<div class="presale" style="padding: 10px; margin-bottom: 20px; background-color: #edb421; color: #fff;"><?= $item['presaleMessage'] ?></div>
					<?php endif; ?>
					<span itemprop="description"><?= nl2br(ProductHelper::GetDescription($item)); ?></span>
					<?php
					$cat = array();
					if (!empty($item['Category'])) $cat[] = $item['Category'];
					if (!empty($item['SubCategory'])) $cat[] = $item['SubCategory'];
					?>
					<?php if (!empty($cat)) : ?>
						<div class="blue_arrow text" style="margin: 20px 0;">
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
										?>" class="catlist" itemprop="genre"><?= $catTitle; ?></a><?if ($i < count($cat)) { ?><br /><? } ?>
									<?php endforeach; ?></div>
								<div class="clearBoth"></div>
							</div>
						</div>
					<?php endif; ?>

						<?php if (!empty($item['Series'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= str_replace(':', '', sprintf($ui->item("SERIES_IS"), '')); ?></div>
								<div class="prop-value"><a class="cprop" href="<?= Series::Url($item['Series']); ?>"><?= ProductHelper::GetTitle($item['Series']); ?></a></div>
								<div class="clearBoth"></div>
							</div>

						<?php endif; ?>

						<?php if (!empty($item['catalogue'])) : ?>
							<div class="detail-prop">
								<div class="prop-name">Catalogue N</div>
								<div class="prop-value"><?= $item['catalogue']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>

						<?php if (!empty($item['stock_id'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= str_replace(':', '', $ui->item('Stock_id')); ?></div>
								<div class="prop-value" itemprop="identifier"><?= $item['stock_id']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>

						<?php $name = 'ISBN';
							$isbnNum = 0;
							if (!empty($item['eancode'])): ?>
								<div class="detail-prop">
									<div class="prop-name">EAN</div>
									<div class="prop-value"><?= $item['eancode']; ?></div>
									<div class="clearBoth"></div>
								</div>
							<?php endif; ?>

							<?php if ((Yii::app()->getLanguage() == 'fi')&&!empty($item['Category']['fin_codes'])): ?>
							<div class="detail-prop">
								<div class="prop-name">Kirjastoluokka</div>
								<div class="prop-value"><?= $item['Category']['fin_codes'] ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['Category']['BIC_categories'])): ?>
							<div class="detail-prop">
								<div class="prop-name">BIC-code(s)</div>
								<div class="prop-value"><?= $item['Category']['BIC_categories'] ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php $name = $ui->item("ALTERNATIVE") . ' ' . $name;
							if (!empty($item['isbn2'])) : ?>
								<div class="detail-prop">
									<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
									<div class="prop-value"><?= $item['isbn2']; ?></div>
									<div class="clearBoth"></div>
								</div>
							<?php endif; ?>
							<?php if (!empty($item['isbn3'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn3']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['isbn4'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn4']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['isbn5'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn5']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['isbn6'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn6']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['isbn7'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn7']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['isbn8'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn8']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['isbn9'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn9']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['isbn10'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn10']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
							<?php if (!empty($item['isbn_wrong'])) : ?>
							<div class="detail-prop">
								<div class="prop-name"><?= $name ?><?= ($isbnNum === null)?'':((++$isbnNum < 2)?'':$isbnNum) ?></div>
								<div class="prop-value"><?= $item['isbn_wrong']; ?></div>
								<div class="clearBoth"></div>
							</div>
						<?php endif; ?>
					<?php $this->widget('OffersByItem', array('entity'=>$entity, 'idItem'=>$item['id'], 'index_show'=>0)) ?>
				</div>
			</div>

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
	<div class="span2">
		<?php $this->widget('YouView', array('entity'=>$entity, 'id'=>$item['id'])); ?>
	</div>
</div>
<?php $this->widget('Banners', array('type'=>'slider', 'item'=>$item, 'uid'=>$this->uid, 'sid'=>$this->sid)) ?>
