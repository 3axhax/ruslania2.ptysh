<?php /*Created by Кирилл (18.07.2019 22:11)*/ ?>
<div class="slider_bg"><div class="container slider_container"><div class="overflow_box">
	<div class="container_slides" style="width: 1170px;">
		<ul>
<?php foreach ($actionItems as $actionItem):
	$product = $actionItem['product'];

	$url = ProductHelper::CreateUrl($product);
	$productTitle = ProductHelper::GetTitle($product, 'title');
	$productTitleSmall = ProductHelper::GetTitle($product, 'title', 18);
	$productPicture = Picture::Get($product, Picture::SMALL);
	$actionTitle = '';
	$actionTitleClass = '';
	if($product['status'] == 'new') {
		$actionTitle = '<div class="new_block">'.Yii::app()->ui->item('IN_NEW').'</div>';
		$actionTitleClass = ' new';
	}
	elseif($product['status'] == 'sale') {
		$actionTitle = '<div class="new_block">'.Yii::app()->ui->item('IN_SALE').'</div>';
		$actionTitleClass = ' akciya';
	}
	elseif($product['status'] == 'recommend') {
		$actionTitle = '<div class="new_block">'.Yii::app()->ui->item('IN_OFFERS').'</div>';
		$actionTitleClass = ' rec';
	}
	$photoTable = Entity::GetEntitiesList()[$product['entity']]['photo_table'];
	$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
	/**@var $photoModel ModelsPhotos*/
	$photoModel = $modelName::model();
	$photoId = $photoModel->getFirstId($product['id']);
?>
			<li>
				<div class="span1 photo<?=$actionTitleClass;?>">
					<?=$actionTitle;?>
					<a title="<?= htmlspecialchars($productTitle) ?>" href="<?=$url;?>">
						<?php if (empty($photoId)): ?>
							<img src="<?= Picture::srcLoad() ?>" data-lazy="<?=$productPicture;?>" alt="<?= htmlspecialchars($productTitle) ?>" style="max-height: 130px;"/>
						<?php else: ?>
							<picture class="main-bannerImg">
								<source srcset="<?= $photoModel->getHrefPath($photoId, 'sb', $product['eancode'], 'webp') ?>" type="image/webp">
								<source srcset="<?= $photoModel->getHrefPath($photoId, 'sb', $product['eancode'], 'jpg') ?>" type="image/jpeg">
								<img src="<?= $productPicture ?>" alt="<?= htmlspecialchars($productTitle) ?>" style="max-height: 130px;"/>
							</picture>
						<?php endif; ?>
					</a>
				</div>
				<div class="span2 text">
					<div class="title"><a title="<?= htmlspecialchars($productTitle) ?>" href="<?=$url;?>"><?=$productTitleSmall;?></a></div>
					{PRICE_<?= $product['entity'] ?>_<?= $product['id'] ?>}
	<?php if ($product['entity'] == Entity::PERIODIC): ?>
						<a href="<?=$url;?>" class="btn_yellow fa" style="float: right; border-radius: 4px;" tabindex="0"><span class="lang-<?= Yii::app()->getLanguage() ?>"><?= Yii::app()->ui->item('A_NEW_MORE3') ?></span></a>
	<?php else: ?>
						<div class="addcart" style="margin-top: 10px;">{CART_BUTTON_<?= $product['entity']; ?>_<?= $product['id']; ?>}</div>
	<?php endif; ?>
				</div>
			</li>
<?php endforeach; ?>
		</ul>
	</div>
</div></div></div>
<script type="text/javascript">
	$(document).ready(function() {
		scriptLoader('/new_js/slick.js').callFunction(function(){
			$('.container_slides ul').slick({
				lazyLoad: 'ondemand',
				slidesToShow: 3,
				slidesToScroll: 1
			}).on('lazyLoadError', function(event, slick, image, imageSource){
				image.attr('src', '<?= Picture::srcNoPhoto() ?>');
			});
		});
	});
</script>
