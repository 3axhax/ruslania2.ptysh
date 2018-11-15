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
            <ul class="left_list entity text recomends">
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
							
						?>
					
					
                        <div>
<a class="title_item_recomend" href="<?=Yii::app()->createUrl('offers/view', array('oid' => $item['id'], 'title' => ProductHelper::ToAscii($title))); ?>"><?= CHtml::encode($title) ?></a>
<span class="date_recomend"><?=Yii::app()->dateFormatter->format('dd MMM yyyy', $item['creation_date']); ?></span>
                        </div>
<?=CHtml::encode(ProductHelper::GetDescription($item->attributes)); ?>
							
							<?
							if (count($offer[Entity::GetTitle($entity)]['items'])) {
								echo '<div class="items_goods_recomends">';
								echo '<div class="slider_recomend custom-slider">';
								foreach ($offer as $offer_entity) {
                                    foreach ($offer_entity['items'] as $of) {

                                        //if ($s < 7) {
                                        if (true) {
                                            echo '<div class="item slider_recomend__item">';
                                            echo '<a href="' . ProductHelper::createUrl($of) . '" class="slider__img-block">
										<div class="img slider__img" style="background: url(\'' . Picture::Get($of, Picture::SMALL) . '\') center center no-repeat; background-size: 100%; position: relative">';
                                            $this->renderStatusLables(Product::GetStatusProduct($of['entity'], $of['id']), '', true);
                                            echo '</div></a>';
                                            echo '</div>';
                                        }
                                        $s++;
                                    }
                                }
								echo '</div><div class="clearfix"></div></div>';
							}
							?><div style="margin-top: 15px;"></div>
							<a title="Download Excel file" rel="nofollow" class="dprice"
                           href="<?=Yii::app()->createUrl('offers/download', array('oid' => $item['id'])); ?>"><?=$ui->item('DOWNLOAD_EXCEL_FILE'); ?>
                            <i class="icon-download-alt"></i></a>
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