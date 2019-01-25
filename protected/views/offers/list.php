      <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
     <div class="container view_product">
			<div class="row">

        <div class="span10">
			<?php /*
            <h2 class="cattitle"><?=$ui->item('RUSLANIA_RECOMMENDS'); ?>:</h2>
 */ ?>
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?php
				$breadcrumbs = $this->breadcrumbs;
				$h1 = array_pop($breadcrumbs);
				unset($breadcrumbs) ;
				$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
				if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1) $h1 .= ' &ndash; ' . $ui->item('PAGES_N', $page); 
				?><?= $h1 ?></h1>
            <ul class="entity text recomends">
                <?php $i = 1;  foreach($list as $item): ?>
                    <?php $title = ProductHelper::GetTitle($item->attributes);
					
					
                    ?>
                    <li class="iconentity-<?=$item['icon_entity']; ?>">
					
						<?php
							$o = new Offer;
							$offer = $o->GetItems($item['id']);
							
							foreach($offer as $k) {
								$entity = $k['entity'];
								break;
							}
							
							//var_dump(Entity::GetTitle($entity));
							
							//echo Entity::GetTitle($entity);

							
							$s = 0;
							//var_dump($item);
							$href = Yii::app()->createUrl('offers/view', array('oid' => $item['id'], 'title' => ProductHelper::ToAscii($title)));
						?>
					
					
                        <div>
<a class="title_item_recomend" href="<?= $href ?>"><?= CHtml::encode($title) ?></a>
<span class="date_recomend"><?=Yii::app()->dateFormatter->format('dd MMM yyyy', $item['creation_date']); ?></span>
                        </div>
<?= ProductHelper::GetDescription($item->attributes); ?>
							
							<?
							if (count($offer[Entity::GetTitle($entity)]['items'])) {
								echo '<div class="items_goods_recomends">';
								echo '<div class="slider_recomend custom-slider">';
								foreach ($offer as $offer_entity) {
                                    foreach ($offer_entity['items'] as $of) {

                                        //if ($s < 7) {
                                        if (true): ?>
                                        <div class="item slider_recomend__item">
                                            <a href="<?= ProductHelper::createUrl($of) ?>" class="slider__img-block">
												<div class="img slider__img" style="background: url('<?= Picture::Get($of, Picture::SMALL) ?>') center center no-repeat; background-size: 100%; position: relative">
                                            <?php $this->renderStatusLables(Product::GetStatusProduct($of['entity'], $of['id']), '', true) ?>
                                                </div>
                                            </a>
                                        </div>
                                        <?php endif;
                                        $s++;
                                    }
                                }
								echo '</div><div class="clearfix"></div></div>';
							}
							?><div style="margin-top: 15px;"></div>
	                    <a href="<?= $href ?>" class="button_view list">
                            <span><?=$ui->item('VIEW_LIST'); ?></span>
                            <span class="fa"></span>
		                    <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"></span>
	                    </a>
							<a title="<?=htmlspecialchars($ui->item('DOWNLOAD_EXCEL_FILE')); ?>" rel="nofollow" class="download excel" href="<?=Yii::app()->createUrl('offers/download', array('oid' => $item['id'])); ?>">
								<span><?=$ui->item('DOWNLOAD_EXCEL_FILE'); ?></span>
								<span class="fa"></span>
                            </a>
					<? if (count($list) > $i) { echo '<hr />'; } $i++;?>
					</li>
                <?php
				

				endforeach; ?>
            </ul>

			<?php if (count($list) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginator)); ?>
        </div>
				<div class="span2">
					<?php $this->widget('YouView', array()); ?>
				</div>
        </div>
        </div>

<script type="text/javascript">
	$(document).ready(function () {
		scriptLoader('/new_js/slick.js').callFunction(function() {
			$('.slider_recomend').slick({
				lazyLoad: 'ondemand',
				infinite: true,
				slidesToShow: 5,
				slidesToScroll: 5,
				speed: 800,
				prevArrow: "<div class=\"btn_left slick-arrow\" style=\"display: block;\"><span class=\"fa\"></span></div>",
				nextArrow: "<div class=\"btn_right slick-arrow\" style=\"display: block;\"><span class=\"fa\"></span></div>"
			}).on('lazyLoadError', function(event, slick, image, imageSource){
				image.attr('src', '<?= Picture::srcNoPhoto() ?>');
			});
		});
	});
</script>

<?php /* верстка блока с товаром с img
<div class="item slider_recomend__item" style="position: relative; text-align: right; width: 111px;">
	<div class="img slider__img" style="width: 111px; height: 171px; overflow: hidden; display: table-cell; vertical-align: middle;">
		<a href="<?= ProductHelper::createUrl($of) ?>">
			<img src="<?= Picture::srcLoad() ?>" data-lazy="<?= Picture::Get($of, Picture::SMALL) ?>" style="max-height: 171px; max-width: 111px;">
		</a>
	</div>
</div>*/ ?>