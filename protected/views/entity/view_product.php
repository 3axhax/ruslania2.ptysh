<?php
Yii::beginProfile($item['id']);
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);


$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);

//var_dump(Yii::app()->getRequest()->cookies['yourView']->value);

$arrGoods = array();

if ($serGoods) {
	$arrGoods = $serGoods;
}

if (!in_array($item['id'] . '_' . $entity, $arrGoods)) {

	$arrGoods[] = $item['id'] . '_' . $entity;
	
	Yii::app()->getRequest()->cookies['yourView'] = new CHttpCookie('yourView', serialize($arrGoods));
	
}

// запись переменной в сессию. Следующие способы использования эквивалентны:

//echo $language;

?>


<div class="row">
	<div class="span1" style="position: relative">
        <?php $this->renderStatusLables($item['status']); ?>
        <img src="<?= Picture::Get($item, Picture::BIG); ?>">
    </div>
	<div class="span11 to_cart"><h1 class="title"><?= ProductHelper::GetTitle($item); ?></h1>
	
	<? if ($item['title_original']) : ?>
	<div class="authors row" style="margin-bottom:0;">
		<span class="nameprop span1" style="overflow: hidden;
    text-align: left;
    background: none;">Оригинальное название:</span> 
		<div class="span2" style="float: left; margin-left: 5px; width: 345px;"> <?=$item['title_original']?></div>
	</div>
	<? endif; ?>
	
	<? if ($item['type']) : ?> 
	<span class="nameprop"><?=$ui->item('A_NEW_TYPE_IZD')?>:</span> <?
	
	 $binding = ProductHelper::GetTypes($entity, $item['type']);
	 
	 echo '<a href="'.
                Yii::app()->createUrl('entity/bytype', array(
                    'entity' => $entityKey,
                    'type' => $item['type'])).'">' . ProductHelper::GetTitle($binding) . '</a>';
	
	endif;
	?>
	
	<?php if (!empty($item['Authors'])) : ?>
                <div class="authors">
                    <span class="nameprop"><?= sprintf($ui->item("WRITTEN_BY"), ''); ?></span>
                    <?php
                    foreach ($item['Authors'] as $author) {
                        $authorTitle = ProductHelper::GetTitle($author);
                        $tmp[] = '<a href="' . Yii::app()->createUrl('entity/byauthor', array('entity' => $entityKey,
                                    'aid' => $author['id'],
                                    'title' => ProductHelper::ToAscii($authorTitle))) . '" class="cprop">'
                                . $authorTitle . '</a>';
                    }
                    ?>

                    <?= implode(', ', $tmp); ?>

                </div>
            <?php endif; ?>
            <?php Yii::endProfile($item['id'] . '_authors'); ?>


            <?php if (!empty($item['Performers'])) : ?>
                <div class="authors">
                    <span class="nameprop"><?= sprintf($ui->item("READ_BY"), ''); ?></span>
                    <?php
                    $tmp = array();
                    foreach ($item['Performers'] as $performer) {
                        $tmp[] = '<a href="' . Yii::app()->createUrl('entity/byperformer', array('entity' => $entityKey,
                                    'pid' => $performer['id'],
                                    'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($performer)))) . '" class="cprop">'
                                . ProductHelper::GetTitle($performer) . '</a>';
                    }

                    echo implode(', ', $tmp);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['Directors'])) : ?>
                <div class="authors">
                    <span class="nameprop"><?= sprintf($ui->item("DIRECTOR_IS"), ''); ?></span>
					<?php foreach ($item['Directors'] as $director) : ?><a href="<?=
                        Yii::app()->createUrl('entity/bydirector', array('entity' => $entityKey,
                            'did' => $director['id'],
                            'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($director))));
                        ?>"
                           class="cprop"><?= ProductHelper::GetTitle($director); ?></a>
                       <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['Actors'])) : ?>
                <div class="authors row" style="margin-bottom:0;">
					<span class="nameprop span1" style="overflow: hidden;
    text-align: left;
    background: none;"><?=sprintf($ui->item("VIDEO_ACTOR_IS"),'')?></span>
                    
					<div class="span2" style="float: left; margin-left: 20px; width: 345px;">
						<?php
						$ret = array();
						foreach ($item['Actors'] as $actor) {
							$ret[] = '<a href="' . Yii::app()->createUrl('entity/byactor', array('entity' => $entityKey,
										'aid' => $actor['id'],
										'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($actor)))) . '" class="cprop">' . ProductHelper::GetTitle($actor) . '</a>';
						}
						echo implode(', ', $ret);
						?>
					</div>
					<div class="clearfix"></div>
                </div>
            <?php endif; ?>
			
			  <?php if (!empty($item['Media'])) : ?>
               <div><span class="nameprop"><?= sprintf($ui->item("MEDIA_TYPE_OF"), ''); ?></span>
                <a class="cprop"
                   href="<?= Media::Url($item); ?>"><?= $item['Media']['title']; ?></a><?php if (!empty($item['Zone'])) : ?>, <?= sprintf($ui->item('VIDEO_ZONE'), '<b>' . $item['Zone']['title'] . '</b>'); ?>
                    <a class="pointerhand"
                       href="<?= Yii::app()->createUrl('site/static', array('page' => 'zone_info')); ?>" target="_blank">
                        <img src="/pic1/q1.gif" width="16" height="16"
                             title="<?= $ui->item("MSG_SHOW_ZONE_INFO"); ?>"
                             style="position:relative;top:-1px;left:10px;"></a><br/>

                <?php endif; ?></div>
            <?php endif; ?>
			
			
			
			<?php if (!empty($item['playtime'])) : ?>

                <div><span class="nameprop"><?= sprintf($ui->item('MSG_AUDIO_PLAYING_TIME'),'');?></span><? echo $item['playtime']; ?></div>

            <?php endif; ?>
			
			<?php if (!empty($item['cds'])) : ?>
                <div><span class="nameprop">CDs:</span> <?= $item['cds']; ?></div>
            <?php endif; ?>
			
			<?php if (!empty($items['dvds'])) : ?>
                <div><span class="nameprop">DVDs:</span> <?=$item['dvds']; ?></div>
            <?php endif; ?>

            <?php if (!empty($item['Subtitles'])) : ?>
                <div class="authors">
					<span class="nameprop"><?=sprintf($ui->item("VIDEO_CREDITS_IS"), '');?></span>
                    <?php
                    $ret = array();
                    foreach ($item['Subtitles'] as $subtitle) {
                        $ret[] = '<a href="' . Yii::app()->createUrl('entity/bysubtitle', array('entity' => $entityKey,
                                    'sid' => $subtitle['id'],
                                    'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($subtitle)))) . '" class="cprop">' . ProductHelper::GetTitle($subtitle) . '</a>';
                    }
                    echo implode(', ', $ret);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['AudioStreams'])) : ?>
                <div class="authors">
					<span class="nameprop"><?=($ui->item("AUDIO_STREAMS") . ': ') ?></span>
                    <?php
                    $ret = array();
                    foreach ($item['AudioStreams'] as $stream) {
                        $ret[] = '<a href="' . Yii::app()->createUrl('entity/byaudiostream', array('entity' => $entityKey,
                                    'sid' => $stream['id'],
                                    'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($stream)))) . '" class="cprop">' . ProductHelper::GetTitle($stream) . '</a>';
                    }
                    echo implode(', ', $ret);
                    ?>
                </div>

            <?php endif; ?>
			
			<?php if (!empty($item['Country'])) : ?>
                <div><span class="nameprop"><?= sprintf($ui->item("COUNTRY_OF_ORIGIN"),'')?></span>
                <?= ProductHelper::GetTitle($item['Country']); ?></div>
            <?php endif; ?>
			
			 <?php if (!empty($item['Languages']) && empty($item['AudioStreams'])) : ?>
                <span class="nameprop"><?= $ui->item('CATALOGINDEX_CHANGE_LANGUAGE'); ?>:</span>
                <?php
                $langs = array();
                foreach ($item['Languages'] as $lang) {
                    $langs[] = '<b>' . Language::GetTitleByID($lang['language_id']) . '</b>';
                }

                echo implode(', ', $langs);
                ?>
            <?php endif; ?>
			
			<?php if (!empty($item['format'])) : ?>
				<div><span class="nameprop">Формат:</span> <?=$item['format']; ?>
				</div>
			<?php endif; ?>
			
			 <?php if (!empty($item['Publisher'])) : ?>
                <div><?php $pubTitle = ProductHelper::GetTitle($item['Publisher']); ?><span class="nameprop">
                <?= sprintf($ui->item("PUBLISHED_BY"), ''); ?></span> <a class="cprop" href="<?=
                Yii::app()->createUrl('entity/bypublisher', array('entity' => $entityKey,
                    'pid' => $item['Publisher']['id'],
                    'title' => ProductHelper::ToAscii($pubTitle)));
                ?>"><?= $pubTitle; ?></a><?php endif; ?>
				
				
				<?php if (!empty($item['year'])) : ?>
               <div><span class="nameprop">
                <?=$ui->item('A_NEW_YEAR');?>:</span> <a href="<?=
                Yii::app()->createUrl('entity/byyear', array(
                    'entity' => $entityKey,
                    'year' => $item['year']));
                ?>"><?=$item['year']?></a></div><?php endif; ?>
				
				<?php if (!empty($item['release_year'])) : ?>
               <div><span class="nameprop">
                <?=$ui->item('A_NEW_YEAR_REAL');?>:</span> <a href="<?=
                Yii::app()->createUrl('entity/byyearrelease', array(
                    'entity' => $entityKey,
                    'year' => $item['release_year']));
                ?>"><?=$item['release_year']?></a></div><?php endif; ?>
				
				
			
			
				<?php if (!empty($item['binding_id'])) : ?>
                 <div><span class="nameprop"><?=$ui->item('A_NEW_PEREP')?>:</span> <?
					
					$row = Binding::GetBinding($entity, $item['binding_id']);
					echo $row['title_' . Yii::app()->language];
					
				 ?></div>
				<?php endif; ?>
			
			<?php if (!empty($item['numpages'])) : ?>
                 <div><span class="nameprop"><?= sprintf($ui->item('A_NEW_COUNT_PAGE'),'');?>:</span> <? echo $item['numpages']; ?></div>
				<?php endif; ?>
			
			
			<?php if (!empty($item['isbn'])) : ?>
                <div><span class="nameprop">ISBN:</span> <?= $item['isbn']; ?></div>
            <?php endif; ?>
			<?php if (!empty($item['eancode'])) : ?>
                <div><span class="nameprop">EAN:</span> <?= $item['eancode']; ?></div>
            <?php endif; ?>
			
			
			
			
			
			<?php if ($item['entity'] == Entity::PERIODIC) : ?>
			
				
            <?php if (!empty($item['size'])) : ?>
                <div><span class="nameprop"><?= sprintf($ui->item('PRINTED_SIZE'),'');?></span><? echo $item['size']; ?>
            </div><?php endif; ?>

            


            <?php if (!empty($item['Series'])) : ?>
               <div><span class="nameprop"><?= sprintf($ui->item("SERIES_IS"), ''); ?></span>
                <a class="cprop"
                   href="<?= Series::Url($item['Series']); ?>"><?= ProductHelper::GetTitle($item['Series']); ?></a></div>
               
            <?php endif; ?>

            <?php if (!empty($item['Media'])) : ?>
                <br /><span class="nameprop"><?= sprintf($ui->item("MEDIA_TYPE_OF"), ''); ?></span>
                <a class="cprop"
                   href="<?= Media::Url($item); ?>"><?= $item['Media']['title']; ?></a><?php if (!empty($item['Zone'])) : ?>, <?= sprintf($ui->item('VIDEO_ZONE'), '<b>' . $item['Zone']['title'] . '</b>'); ?>
                    <a class="pointerhand"
                       href="<?= Yii::app()->createUrl('site/static', array('page' => 'zone_info')); ?>" target="_blank">
                        <img src="/pic1/q1.gif" width="16" height="16"
                             title="<?= $ui->item("MSG_SHOW_ZONE_INFO"); ?>"
                             style="position:relative;top:4px;left:10px;"></a><br/>

                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($item['catalogue'])) : ?>
                <br /><span class="nameprop">Catalogue N:</span> <?= $item['catalogue']; ?><br/>
            <?php endif; ?>

            <?php if (!empty($item['issn'])) : ?>
                <br /><span class="nameprop">ISSN:</span> <?= $item['issn']; ?>
            <?php endif; ?>

            

            

            <?php //if (!empty($item['issues_year'])) $this->renderPartial('/entity/_issues_year', array('item' => $item)) ?>

            <?php if (!empty($item['requirements'])) : ?>
                <br /><span class="nameprop"><?= $ui->item('A_SOFT_REQUIREMENTS'); ?>:</span> <?= $item['requirements']; ?>
            <?php endif; ?>


            <?php if (!empty($item['index'])) : ?>
                <br /><span class="nameprop"><?= sprintf($ui->item("PERIOD_INDEX"), '');?></span>
                <?=$item['index']; ?>
            <?php endif ?>

            

            <!--<?php if (!empty($item['stock_id'])) : ?>
                <br /><span class="nameprop">
                <?= $ui->item('Stock_id'); ?>:</span> <?= $item['stock_id']; ?>
            <?php endif; ?>-->

			
			
			
			<? endif; ?>
			
			
			<?php
            $price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
            $isAvail = ProductHelper::IsAvailableForOrder($item);
            ?>

            <?php if (Availability::GetStatus($item) != Availability::NOT_AVAIL_AT_ALL) : ?>

                <?php if ($item['entity'] == Entity::PERIODIC) : ?>
					
					<div class="clearBoth" style="height: 10px;"></div>
					
					<?=
                    $this->renderPartial('/entity/_priceInfo', array('key' => 'PERIODIC_FIN',
                        'item' => $item,
                        'price' => $price));
                    ?>

                    <?=
                    $this->renderPartial('/entity/_priceInfo', array('key' => 'PERIODIC_WORLD',
                        'item' => $item,
                        'price' => $price));
                    ?>
					
					<div class="clearBoth"></div>
					
					
					
					<?=
                    $this->renderPartial('/entity/_issues_year', array('key' => 'ITEM',
                        'item' => $item));
                    ?>
					
					<div class="clearBoth"></div>
					

                <?php else : ?>
                    <?=
                    $this->renderPartial('/entity/_priceInfo_notperiodica', array('key' => 'ITEM',
                        'item' => $item,
                        'price' => $price));
                    ?>

                <?php endif; ?>

            <?php endif; ?>
			
			<? if ($item['entity'] != Entity::PERIODIC) :  ?>
			
			<div class="already-in-cart" style="margin-top: 30px; float: left; margin-left: 0;">
			<?php if (isset($item['AlreadyInCart'])) : ?>
					
					
                        <div style="font-size: 16px; color: #4c3a6a;">&nbsp;</div> 
						<?php if ($item['entity'] != Entity::PERIODIC) : ?>
                            <?= sprintf(Yii::app()->ui->item('ALREADY_IN_CART'), $item['AlreadyInCart']); ?>
                        <?php else : ?>
                            <?= strip_tags(Yii::app()->ui->item('PERIODIC_ALREADY_IN_CART')); ?>
                        <?php endif; ?>
					
                    <?php endif; ?>
			</div>
			<?php endif; ?>
			
			
			<div class="clearfix"></div>
			 <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                <div class="mb5" style="color:#4e7eb5; width: 200px; font-size: 13px; float: left;">
                    <?= Availability::ToStr($item); ?>
                </div>
            <?php endif; ?>
			
			
			

            <?php $quantity = ($item['entity'] == Entity::PERIODIC) ? 12 : 1; ?>

           

            

            <?php
            if ($hideButtons) {
                echo '</div>';
                echo '</div>';
                echo '<div class="clearBoth"></div>';
                return;
            };
            ?>

            
			
			<?php if ($isAvail) : ?>

					
                

                <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                    <div style="float: left; margin-top: -7px; margin-left: 10px;">
                    <?= $ui->item('CART_COL_QUANTITY'); ?>:
                    <select class="selquantity" id="sel<?= $item['entity']; ?>-<?= $item['id']; ?>"
                            style="display: inline-block; margin-bottom: 5px; width: 85px;">
                                <?php
                                for ($i = 1; $i <= 100; $i++) {
                                    echo '<option value="' . $i . '">' . $i . '</option>';
                                }
                                ?>
                    </select>
					</div>
                <?php endif; ?>
				
				
				<? if ($item['entity'] != Entity::PERIODIC) { ?>
				<div class="clearfix"></div>
				<div style="margin-top: 10px;"></div>
				<?}?>
				<?php if (!empty($item['Lookinside'])) : ?>

                <?php
                $images = array();
                $audio = array();
                $pdf = array();
                foreach ($item['Lookinside'] as $li) {
                    $ext = strtolower(pathinfo($li['resource_filename'], PATHINFO_EXTENSION));
                    if ($ext == 'jpg' || $ext == 'gif')
                        $images[] = '/pictures/lookinside/' . $li['resource_filename'];
                    elseif ($ext == 'mp3')
                        $audio[] = $li['resource_filename'];
                    else
                        $pdf[] = $li['resource_filename'];
                }
                $images = implode('|', $images);
                ?>
                
                <?php if ($item['entity'] == Entity::AUDIO) : ?>
                    <a href="javascript:;" style="width: 131px; margin-right: 30px;"  data-iid="<?= $item['id']; ?>" data-audio="<?= implode('|', $audio); ?>" class="read_book">Смотреть</a>
					
                    <div id="audioprog<?= $item['id']; ?>" class="audioprogress">
                        <img src="/pic1/isplaying.gif" class="lookinside audiostop"/><br/>
                        <span id="audionow<?= $item['id']; ?>"></span> / <span id="audiototal<?= $item['id']; ?>"></span>

                    </div>
                    <div class="clearBoth"></div>


                <?php else : ?>
					<a href="javascipt:;"
                         data-iid="<?= $item['id']; ?>"
                         data-pdf="<?= CHtml::encode(implode('|', array())); ?>"
                         data-images="<?= CHtml::encode($images); ?>" style="width: 131px; margin-right: 30px;" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>
				
                   
                        <?php if (!empty($pdf)) : ?>
                        <div id="staticfiles<?= $item['id']; ?>">
                            <b><?= $ui->item('MSG_BTN_LOOK_INSIDE'); ?></b> 
                            <ul class="staticfile">
                                <?php $pdfCounter = 1; ?>
                                <?php foreach ($pdf as $file) : ?>
                                    <?php $file2 = '/pictures/lookinside/' . $file; ?>
                                    <li>
                                        <a target="_blank" href="<?= $file2; ?>"><img
                                                src="/css/pdf.png"/><?= $pdfCounter . '.pdf'; ?></a>
                                    </li>
                                    <?php $pdfCounter++; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
				
				<? $count_add = 1;
					if ($item['entity'] == Entity::PERIODIC) {
						
						$count_add = 12;
						
					}
					
				?>
				
               <?php else : ?><?php if ($item['entity'] != Entity::VIDEO) : ?>
				<div class="clearBoth"></div>
                    <?php if (Yii::app()->user->isGuest) : ?>
<a href="<?=
                        Yii::app()->createUrl('cart/dorequest', array('entity' => Entity::GetUrlKey($item['entity']),
                            'iid' => $item['id']));
                        ?>" class="cart-action request"><?=$ui->item('CART_COL_ITEM_MOVE_TO_ORDERED'); ?></a>

                    <?php else : ?>
                        <a class="cart-action request" data-action="request" data-entity="<?= $item['entity']; ?>"
                           data-id="<?= $item['id']; ?>"
                           href="<?= Yii::app()->createUrl('cart/request', array('entity' => $item['entity'], 'id' => $item['id'])); ?>"><?=$ui->item('CART_COL_ITEM_MOVE_TO_ORDERED'); ?></a>

                    <?php endif; ?>
                
                <?php endif; ?>
				
            <?php endif; ?>
			
			<? if ($item['entity'] == Entity::PERIODIC) { ?>
				<div style="height: 18px;"></div>
				
				
				
				<?php $lookinside = false; if (!empty($item['Lookinside'])) : ?>
				<div class="link__container" style="float: left; width: 170px;">
                <?php
				$lookinside = true;
                $images = array();
                $audio = array();
                $pdf = array();
                foreach ($item['Lookinside'] as $li) {
                    $ext = strtolower(pathinfo($li['resource_filename'], PATHINFO_EXTENSION));
                    if ($ext == 'jpg' || $ext == 'gif')
                        $images[] = '/pictures/lookinside/' . $li['resource_filename'];
                    elseif ($ext == 'mp3')
                        $audio[] = $li['resource_filename'];
                    else
                        $pdf[] = $li['resource_filename'];
                }
                $images = implode('|', $images);
                ?>
                
                <?php if ($item['entity'] == Entity::AUDIO) : ?>
                    <a href="javascript:;" style="width: 131px; margin-right: 30px;"  data-iid="<?= $item['id']; ?>" data-audio="<?= implode('|', $audio); ?>" class="read_book">Смотреть</a>
					
                    <div id="audioprog<?= $item['id']; ?>" class="audioprogress">
                        <img src="/pic1/isplaying.gif" class="lookinside audiostop"/><br/>
                        <span id="audionow<?= $item['id']; ?>"></span> / <span id="audiototal<?= $item['id']; ?>"></span>

                    </div>
                    <div class="clearBoth"></div>


                <?php else : ?>
					<a href="javascipt:;"
                         data-iid="<?= $item['id']; ?>"
                         data-pdf="<?= CHtml::encode(implode('|', array())); ?>"
                         data-images="<?= CHtml::encode($images); ?>" style="width: 131px; margin-right: 30px;" class="read_book"><?=$ui->item('A_NEW_VIEW')?></a>
				
                   
                        <?php if (!empty($pdf)) : ?>
                        <div id="staticfiles<?= $item['id']; ?>" style="margin-top: 10px;">
                            <b><?= $ui->item('MSG_BTN_LOOK_INSIDE'); ?></b> 
                            <ul class="staticfile">
                                <?php $pdfCounter = 1; ?>
                                <?php foreach ($pdf as $file) : ?>
                                    <?php $file2 = '/pictures/lookinside/' . $file; ?>
                                    <li>
                                        <a target="_blank" href="<?= $file2; ?>"><img
                                                src="/css/pdf.png"/><?= $pdfCounter . '.pdf'; ?></a>
                                    </li>
                                    <?php $pdfCounter++; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
				</div>
            <?php endif; ?>
				
				
			<? } ?>
			
			<?php if ($item['entity'] == Entity::PERIODIC) : ?>

                <?php
                $ie = $item['issues_year'];

                if ($ie < 12) {
                    $inOneMonth = $ie / 12;
                    $show3Months = false;
                    $show6Months = false;

                    $tmp1 = $inOneMonth * 3;
                    if (ctype_digit("$tmp1"))
                        $show3Months = true;
                    $tmp2 = $inOneMonth * 6;
                    if (ctype_digit("$tmp2"))
                        $show6Months = true;
                }
                else {
                    $show3Months = true;
                    $show6Months = true;
                }
                ?>
				<div class="mb5 link__deliver" style="color:#0A6C9D; float: left;">
                    <?= $ui->item('MSG_DELIVERY_TYPE_4'); ?>
                </div>
				<div style="height: 23px; clear: both"></div>
                <select class="periodic" style="float: left; margin-right: 0; margin-bottom: 19px; width: 180px; font-size: 12px;">
                    <?php if ($show3Months) : ?>
                        <option value="3">3 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_2'); ?> - 3 <?= $ui->item('A_NEW_NUM'); ?></option>
                    <?php endif; ?>

                    <?php if ($show6Months) : ?>
                        <option value="6">6 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?> - 6 <?= $ui->item('A_NEW_NUMS'); ?></option>
                    <?php endif; ?>

                    <option value="12" selected="selected">
                        12 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?> - 12 <?= $ui->item('A_NEW_NUMS'); ?></option>
                </select>
				<?php if ($price[DiscountManager::TYPE_FREE_SHIPPING] && $isAvail) : ?>
                
				
				<div style="height: 1px; clear: both"></div>
				<?php endif; ?>
                <input type="hidden" value="<?= round($price[DiscountManager::WITH_VAT_WORLD] / 12, 2); ?>"
                       class="worldmonthpricevat"/>
                <input type="hidden" value="<?= round($price[DiscountManager::WITHOUT_VAT_WORLD] / 12, 2); ?>"
                       class="worldmonthpricevat0"/>
                <input type="hidden" value="<?= round($price[DiscountManager::WITH_VAT_FIN] / 12, 2); ?>"
                       class="finmonthpricevat"/>
                <input type="hidden" value="<?= round($price[DiscountManager::WITHOUT_VAT_FIN] / 12, 2); ?>"
                       class="finmonthpricevat0"/>
				
				
				<a class="cart-action add_cart" data-action="add" style="width: 132px;" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="6" href="javascript:;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></a>
				
            <?php endif;?>
			
			<?php if ($isAvail AND $entity != Entity::PERIODIC) : ?>
			
			<a class="cart-action add_cart" data-action="add" style="width: 132px;" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="6" href="javascript:;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></a>
				
			
			<? endif; ?>
			
			<a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>"<? if (!$lookinside AND $item['entity'] == 30) : echo ' style="margin-left: 20px;" '; endif; ?>
                           data-id="<?= $item['id']; ?>" class="addmark cart-action">&nbsp;<span class="tooltip"><span class="arrow"></span><?=$ui->item('BTN_SHOPCART_ADD_SUSPEND_ALT')?></span></a>
			
	
	</div>
</div>

			
			
			
<div class="clearfix"></div>
<? $comments = Comments::get_list($entity, $item['id']); ?>
<div class="tabs_container">
	<ul class="tabs">
		<li class="desc active"><a href="javascript:;"><?=$ui->item('A_NEW_DESC_TAB')?></a></li>
		<!--<li class="review"><a href="javascript:;"><?=$ui->item('A_NEW_REVIEWS_TAB')?> (<?=count($comments)?>)</a></li>-->
	</ul>
	
	<div class="tabcontent desc active">
		<?php if (isset($isList) && $isList) : ?>
                <a href="<?= $url; ?>" class="title"><?= ProductHelper::GetTitle($item); ?></a>
                <?= nl2br(strip_tags(ProductHelper::GetDescription($item, 200))); ?>
                    &nbsp;
                    <a href="<?= $url; ?>" class="badge-more"><?= $ui->item("DESCRIPTION_MORE"); ?></a>
                
            <?php else : ?>
                <?php //var_dump($item['description_ru']); ?>
                    <?= nl2br(ProductHelper::GetDescription($item)); ?>
                
            <?php endif; ?>
			
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
                <div class="blue_arrow text" style="margin-top: 25px;">
                    <span class="nameprop"><?= $ui->item('Related categories'); ?>:</span>
                    <?php foreach ($cat as $c) : ?>
                        <?php $catTitle = ProductHelper::GetTitle($c); ?>
                        <a href="<?=
                        Yii::app()->createUrl('entity/list', array('entity' => $entityKey,
                            'cid' => $c['id'],
                            'title' => ProductHelper::ToAscii($catTitle)
                        ));
                        ?>" class="catlist"><?= $catTitle; ?></a>;
                       <?php endforeach; ?>
                </div>
            <?php endif; ?>
			
			<?php if ($item['entity'] != Entity::PERIODIC) : ?>
			
			<?php if (!empty($items['dvds'])) : ?>
                <br><span class="nameprop">DVDs:</span> <?=$item['dvds']; ?>
            <?php endif; ?>

            

           


            <?php if (!empty($item['size'])) : ?>
                <br /><span class="nameprop"><?= sprintf($ui->item('PRINTED_SIZE'),'');?></span><? echo $item['size']; ?>
            <?php endif; ?>

            <?php if (!empty($item['playtime'])) : ?>

                <br /><span class="nameprop"><?= sprintf($ui->item('MSG_AUDIO_PLAYING_TIME'),'');?></span><? echo $item['playtime']; ?>

            <?php endif; ?>


            <?php if (!empty($item['Series'])) : ?>
                <br /><span class="nameprop"><?= sprintf($ui->item("SERIES_IS"), ''); ?></span>
                <a class="cprop"
                   href="<?= Series::Url($item['Series']); ?>"><?= ProductHelper::GetTitle($item['Series']); ?></a>
               
            <?php endif; ?>

          


            <?php if (!empty($item['catalogue'])) : ?>
                <br /><span class="nameprop">Catalogue N:</span> <?= $item['catalogue']; ?><br/>
            <?php endif; ?>

            <?php if (!empty($item['eancode'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['eancode']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn2'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn2']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn3'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn3']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn4'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn4']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn5'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn5']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn6'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn6']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn7'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn7']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn8'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn8']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn9'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn9']; ?>
            <?php endif; ?>
			<?php if (!empty($item['isbn10'])) : ?>
                <br /><span class="nameprop">ISBN:</span> <?= $item['isbn10']; ?>
            <?php endif; ?>

            

            

            <?php if (!empty($item['issues_year'])) $this->renderPartial('/entity/_issues_year', array('item' => $item)) ?>

            <?php if (!empty($item['requirements'])) : ?>
                <br /><span class="nameprop"><?= $ui->item('A_SOFT_REQUIREMENTS'); ?>:</span> <?= $item['requirements']; ?>
            <?php endif; ?>


            <?php if (!empty($item['index'])) : ?>
                <br /><span class="nameprop"><?= sprintf($ui->item("PERIOD_INDEX"), '');?></span>
                <?=$item['index']; ?>
            <?php endif ?>

            <?php if (!empty($item['issn'])) : ?>
                <br /><span class="nameprop">
                ISSN:</span> <?= $item['issn']; ?>
            <?php endif; ?>

            <?php if (!empty($item['stock_id'])) : ?>
                <br /><span class="nameprop">
                <?= $ui->item('Stock_id'); ?>:</span> <?= $item['stock_id']; ?>
            <?php endif; ?>
			
		<?php endif; ?>	
	</div>
	
	
	<!--
	<div class="tabcontent review">
		

		
		<div class="comments_block">
				
				<? if (!count($comments)) echo $ui->item('A_NEW_NOTREVIEWS');  ?>
				
				<? $i = 1;  foreach ($comments as $c => $comment) { ?>
					
					<?
					$month = array(
					'',
					$ui->item('A_NEW_M1'),
					$ui->item('A_NEW_M2'),
					$ui->item('A_NEW_M3'),
					$ui->item('A_NEW_M4'),
					$ui->item('A_NEW_M5'),
					$ui->item('A_NEW_M6'),
					$ui->item('A_NEW_M7'),
					$ui->item('A_NEW_M8'),
					$ui->item('A_NEW_M9'),
					$ui->item('A_NEW_M10'),
					$ui->item('A_NEW_M11'),
					$ui->item('A_NEW_M12')
					
					)
					?>
					
					<?if ($i > 1) echo '<div class="split_rev"></div>';?>
					
					<div class="name_rev"><? 
					
					$user = User::model()->findByPk($comment['user_id']);
					
					echo ( $user->first_name . ' ' . $user->last_name ); ?></div>
					<div class="date_publ_rev"><? echo ( date('d '.$month[date('n')].' Y', strtotime($comment['date_publ'])) ); ?></div>
					<div class="text_rev"><? echo ( $comment['text'] ); ?></div>
					
				<? $i++; } ?>
			
			</div>
		
		<?php if (Yii::app()->user->isGuest) : ?>
			<?=$ui->item('A_NEW_REV1')?> <a href="<?= Yii::app()->createUrl('site/register'); ?>"><?=$ui->item('A_NEW_REV2')?></a> <?=$ui->item('A_NEW_REV3')?> <a href="<?= Yii::app()->createUrl('site/login'); ?>"><?=$ui->item('A_NEW_REV4')?></a>.
		<? else :?>
		<form method="post" class="addcomment">
			
			<input type="hidden" name="entity" value="<?=$entity?>" />
			<input type="hidden" name="id" value="<?=$item['id']?>" />
			
			<textarea name="comment_text" placeholder="<?=$ui->item('A_NEW_REV5')?>" style="width: 100%; box-sizing: border-box; height: 200px;"></textarea>
			<div></div>
			<a href="javascript:;" class="order_start" onclick="addComment()"><?=$ui->item('A_NEW_REV6')?></a> <span class="info"></span>
			
		</form>
		<? endif; ?>
	</div>
-->
</div>

<? 
	
	$authors = ' author_id IN (10) ';
	
	if (!empty($item['Authors'])) {
		
		foreach ($item['Authors'] as $author) {
			
			$arrAu[] = $author['id'];
			
		}
		
		$authors = ' author_id IN ('.implode(',', $arrAu).') ';
		
	}
	
	
	
	$ids = Product::related_goods($item['Category']['id'], $entity, $item['id'], ProductHelper::GetTitle($item), $item['series_id'], $authors);

	if (count($ids)) {
?>

<script type="text/javascript">
        $(document).ready(function () {
            $('.more_goods ul').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 4,
                slidesToScroll: 4
            });
        });
    </script>

<div class="news_box" style="margin-top: 40px;">


		<div class="">
			<div class="title">
				<?=$ui->item('A_NEW_RELATION_ITEMS')?>        
				<div class="pult">
					<a href="javascript:;" onclick="$('.news_box .btn_left.slick-arrow').click()" class="btn_left"><img src="/new_img/btn_left_news.png" alt=""></a>
					<a href="javascript:;" onclick="$('.news_box .btn_right.slick-arrow').click()" class="btn_right"><img src="/new_img/btn_right_news.png" alt=""></a>
				</div>
			</div>
		</div>
		
		<div class="more_goods" style="overflow: hidden">
<ul class="books">

	<?

	foreach ($ids as $k) : 
	
	if ($k['entity'] == '') { $k['entity'] = $entity; }
	
	?>
	
	<?
	
	$product = Product::GetProduct($k['entity'], $k['id']);
	$url = ProductHelper::CreateUrl($product);
	
	
	echo  '	<li>
        
    <div class="img" style="min-height: 130px; position: relative">';
        $this->renderStatusLables($product['status']);
    echo '<a href="'.$url.'"><img src="'.Picture::Get($product, Picture::SMALL).'" alt=""  style="max-height: 130px;"/></a>
    </div>
 
	<div class="title_book"><a href="'.$url.'">'.ProductHelper::GetTitle($product, 'title', 42).'</a></div>';
		
		if ($product['isbn']) {
			echo '<div>ISBN: '.str_replace('-', '' ,$product['isbn']).'</div>';
		}
		
		if ($product['year']) {
			
			echo '<div>'.$ui->item('A_NEW_YEAR') . ': ' . $product['year'].'</div>';
			
		}
		
		if ($product['binding_id']) {
		
		$row = Binding::GetBinding($entity, $product['binding_id']);
		echo $row['title_' . Yii::app()->language];	
		
		}
		
		$price = DiscountManager::GetPrice(Yii::app()->user->id, $product);
	
		echo '
        
    	<div class="cost">';
		if (!empty($price[DiscountManager::DISCOUNT])) :
            echo '<span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;">'.ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]).'
            </span>&nbsp;<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold;">
                '.ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]).'
            </span>';

        else :

            echo '<span class="price">'.ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]).'
        
        </span>';

        endif;
	echo '</div>
                    <div class="nds">'. ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]) . $ui->item('WITHOUT_VAT') .'</div>
                    <div class="addcart">
                        <a class="cart-action" data-action="add" data-entity="'. $k['entity'] .'"
               data-id="'. $k['id'] .'" data-quantity="1"
               href="javascript:;">'.$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART').'</a>
                    </div>                   </li>'; ?>


					
					
		<? endforeach; ?>			
</ul>
</div>


</div>


	<? } ?>


<script type="text/javascript">
	$(document).ready(function () {
		$('.selquantity').change(function(){
			
			$('.cart-action').attr('data-quantity', $('.selquantity').val());
		});
	})
</script>	