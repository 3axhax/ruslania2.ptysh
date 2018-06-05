<?php
//Yii::beginProfile($item['id']);
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
?>
<div class="row">    
    <div class="span1" style="position: relative">
        <?php $this->renderStatusLables(Product::GetStatusProduct($item['entity'], $item['id']))?>
        <?php if (isset($isList) && $isList) : ?>
            <a href="<?= $url; ?>" title="<?= ProductHelper::GetTitle($item); ?>">
                <img height="241"

                     src="<?= Picture::Get($item, Picture::BIG); ?>" alt="<?= ProductHelper::GetTitle($item); ?>"></a>
            <?php else : ?>
            <a href="<?= Picture::Get($item, Picture::BIG); ?>" id="img<?= $item['id']; ?>">
                <img width="150"

                     src="<?= Picture::Get($item, Picture::SMALL); ?>">
            </a>
            <script type="text/javascript">
                $('#img<?= $item['id']; ?>').prettyPhoto({social_tools: false});
            </script>
        <?php endif; ?>
    </div>
    <div class="span11">

        <a href="<?= $url; ?>" class="title"><?= ProductHelper::GetTitle($item); ?></a>
		
		<? if ($item['title_original'] == '0000000000') : ?>
		<div><span class="nameprop">Оригинальное название:</span> <?=$item['title_original']?>
		
		</div>
		<? endif; ?>
		
        <?php if (!empty($item['Authors'])) : ?>
            <div class="authors" style="margin-top: 0;">
                <?= sprintf($ui->item("WRITTEN_BY"), ''); ?>
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
        <?php if (!empty($item['Performers'])) : ?>
            <div class="authors"  style="margin-top: 0;">
                <?= sprintf($ui->item("READ_BY"), ''); ?>
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
            <div class="authors" style="margin-top: 0;">
                <?= sprintf($ui->item("DIRECTOR_IS"), ''); ?>
                <?php foreach ($item['Directors'] as $director) : ?>
                    <a href="<?=
                    Yii::app()->createUrl('entity/bydirector', array('entity' => $entityKey,
                        'did' => $director['id'],
                        'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($director))));
                    ?>"
                       class="cprop"><?= ProductHelper::GetTitle($director); ?></a>
                   <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Actors'])) : ?>
            <div class="authors" style="margin-top: 0;">
                <?php
                $ret = array();
				
				$i = 0;
				
                foreach ($item['Actors'] as $actor) {
                    $i++;
					
					if ($i >= 6) break;
					
					$ret[] = '<a href="' . Yii::app()->createUrl('entity/byactor', array('entity' => $entityKey,
                                'aid' => $actor['id'],
                                'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($actor)))) . '" class="cprop">' . ProductHelper::GetTitle($actor) . '</a>';
                }
                echo sprintf($ui->item("VIDEO_ACTOR_IS"), implode(', ', $ret));
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Subtitles'])) : ?>
            <div class="authors" style="margin-top: 0;">
                <?php
                $ret = array();
                foreach ($item['Subtitles'] as $subtitle) {
                    $ret[] = '<a href="' . Yii::app()->createUrl('entity/bysubtitle', array('entity' => $entityKey,
                                'sid' => $subtitle['id'],
                                'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($subtitle)))) . '" class="cprop">' . ProductHelper::GetTitle($subtitle) . '</a>';
                }
                echo sprintf($ui->item("VIDEO_CREDITS_IS"), implode(', ', $ret));
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['AudioStreams'])) : ?>
            <div class="authors" style="margin-top: 0;">
                <?php
                $ret = array();
                foreach ($item['AudioStreams'] as $stream) {
                    $ret[] = '<a href="' . Yii::app()->createUrl('entity/byaudiostream', array('entity' => $entityKey,
                                'sid' => $stream['id'],
                                'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($stream)))) . '" class="cprop">' . ProductHelper::GetTitle($stream) . '</a>';
                }
                echo $ui->item("AUDIO_STREAMS") . ': ' . implode(', ', $ret);
                ?>
            </div>

        <?php endif; ?>
        <?php if (!empty($item['Languages']) && empty($item['AudioStreams'])) : ?> 
            
           
            <?php
            $langs = array();
            foreach ($item['Languages'] as $lang) {
                $langs[] = '' . Language::GetTitleByID($lang['language_id']) . '';
            }

            echo '<span class="langs">'.$ui->item('CATALOGINDEX_CHANGE_LANGUAGE'). ': '.implode(', ', $langs) . '</span>';
            ?>
        <?php endif; ?>

		<? if  ($item['year']) : ?>
			
			<br><span><?=$ui->item('A_NEW_YEAR');?>: <a href="<?=Yii::app()->createUrl('entity/byyear', array('entity' => $entityKey,
                                'year' => $item['year'])); ?>"><?=$item['year']?></a></span>
		
		<? endif; ?>
		
		<? if  ($item['release_year']) : ?>
			
			
			<div><span style="margin-left: 0;"><?=$ui->item('A_NEW_YEAR_REAL');?>: <a href="<?=Yii::app()->createUrl('entity/byyearrelease', array('entity' => $entityKey,
                                'year' => $item['release_year'])); ?>"><?=$item['release_year']?></a></span></div>
			
			
		
		<? endif; ?>
		
		<?php if (!empty($item['Publisher'])) : ?>
                <br /><?php $pubTitle = ProductHelper::GetTitle($item['Publisher']); ?><span class="nameprop">
                <?= sprintf($ui->item("Published by"), ''); ?>:</span> <a class="cprop" href="<?=
                Yii::app()->createUrl('entity/bypublisher', array('entity' => $entityKey,
                    'pid' => $item['Publisher']['id'],
                    'title' => ProductHelper::ToAscii($pubTitle)));
                ?>"><?= $pubTitle; ?></a><?php endif; ?>
		
		<?
			
			if ($item['type']) {
			?><div style="margin-top: 10px;"><span class="nameprop"><?=$ui->item('A_NEW_TYPE_IZD')?>: </span><?
			 $binding = ProductHelper::GetTypesPrinted($entity, $item['type']);
			 
			 echo '<a href="'.
                Yii::app()->createUrl('entity/bytype', array(
                    'entity' => $entityKey,
                    'type' => $item['type'])).'">' . ProductHelper::GetTitle($binding) . '</a></div>';
			}
		
		?>
		
		
        <div class="desc_text"><?= nl2br(ProductHelper::GetDescription($item, 200, $url)); ?></div>
		
		<? if  ($item['isbn']) : ?>
			
			<div style="margin-top: 16px;">ISBN: <?=str_replace('-','',$item['isbn'])?></div>
		
		<? endif; ?>
		
		
		<?php if (!empty($item['binding_id'])) : ?>
                 <?
					
					$row = Binding::GetBinding($entity, $item['binding_id']);
					echo 'Переплет: '.$row['title_' . Yii::app()->language];
					
				 ?>
				<?php endif; ?>
		
</div>

<div class="span1 cart to_cart" style="overflow: hidden">
	
		
	<?php
	
			
	
            $price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
            $isAvail = ProductHelper::IsAvailableForOrder($item);
            ?>

            <?php if (Availability::GetStatus($item) != Availability::NOT_AVAIL_AT_ALL) : ?>

                <?php if ($item['entity'] == Entity::PERIODIC) : ?>
					<div style="height: 23px; clear: both"></div>
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


                <?php else : ?>
                    <?=
                    $this->renderPartial('/entity/_priceInfo', array('key' => 'ITEM',
                        'item' => $item,
                        'price' => $price));
                    ?>

                <?php endif; ?>

            <?php endif; ?>
			
			 <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                <div class="mb5" style="color:#4e7eb5;">
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
				<div class="mb5" style="color:#0A6C9D; float: left;">
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

            <?php endif;?>
			
			<?php if ($isAvail) : ?>
					<div class="already-in-cart" style="margin: 9px 0;">
					<?php if (isset($item['AlreadyInCart'])) : ?>
					
                        <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                            <?= sprintf(Yii::app()->ui->item('ALREADY_IN_CART'), $item['AlreadyInCart']); ?>
                        <?php else : ?>
                            <?= strip_tags(Yii::app()->ui->item('PERIODIC_ALREADY_IN_CART')); ?>
                        <?php endif; ?>
					
                    <?php else : ?>&nbsp;
                    <?php endif; ?>
					</div>

    <form method="get" action="<?= Yii::app()->createUrl('cart/view') ?>" onsubmit="return false;">
                <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                    
					<div class="minus_plus">
					 <a href="javascript:;" onclick="minus_plus($(this), 'minus')" style="margin-right: 9px;"><img src="/new_img/cart_minus.png" class="grayscale"></a> <input name="quantity[<?= (int) $item['id'] ?>]" type="text" size="3" class="cart1contents1 center" style="margin: 0; width: 36px;" value="1" onfocus="change_input_plus_minus($(this))" onkeydown="change_input_plus_minus($(this))" onblur="change_input_plus_minus($(this))"> <a href="javascript:;" style="margin-left: 9px;" onclick="minus_plus($(this), 'plus')"><img src="/new_img/cart_plus.png"></a>
					 </div>
                <?php endif; ?>
				
				
				
				<? $count_add = 1;
					if ($item['entity'] == Entity::PERIODIC) {
						
						$count_add = 12;
						
					}
				?>
        <input type="hidden" name="entity[<?= (int) $item['id'] ?>]" value="<?= (int) $item['entity'] ?>">


            <a class="cart-action add_cart list_cart<?if (Yii::app()->language == 'es') echo ' no_img';?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;">
                <button type="submit" onclick="return false;" style="border: none; background: none; padding: 0; color:#fff;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></button>
            </a>

        <?php $style = '10px'; if ($item['entity'] == Entity::VIDEO) { $style = '0'; echo '<div style="height: 20px;"></div>'; } ?>
    			<a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>"
                               data-id="<?= $item['id']; ?>" class="addmark cart-action" style="margin-left: <?=$style?>">&nbsp;</a>
    </form>

        <?php else : ?><?php if ($item['entity'] != Entity::VIDEO) : ?>
                    <?php if (Yii::app()->user->isGuest) : ?>
<a href="<?=
                        Yii::app()->createUrl('cart/dorequest', array('entity' => Entity::GetUrlKey($item['entity']),
                            'iid' => $item['id']));
                        ?>" class="ca request"><?=$ui->item('CART_COL_ITEM_MOVE_TO_ORDERED'); ?></a>

                    <?php else : ?>
                        <a class="cart-action request" data-action="request" data-entity="<?= $item['entity']; ?>"
                           data-id="<?= $item['id']; ?>"
                           href="<?= Yii::app()->createUrl('cart/request', array('entity' => $item['entity'], 'id' => $item['id'])); ?>"><?=$ui->item('CART_COL_ITEM_MOVE_TO_ORDERED'); ?></a>

                    <?php endif; ?>
                
                <?php endif; ?>
                <?php $style = '10px'; if ($item['entity'] == Entity::VIDEO) { $style = '0'; echo '<div style="height: 20px;"></div>'; } ?>
            			<a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>"
                                       data-id="<?= $item['id']; ?>" class="addmark cart-action" style="margin-left: <?=$style?>">&nbsp;</a>

            <?php endif; ?>

</div>

</div>
<?php
//Yii::endProfile($item['id']);