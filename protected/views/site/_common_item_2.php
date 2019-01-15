<?php
//Yii::beginProfile($item['id']);
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
?>
<div class="row">
    <div class="span1" style="overflow: hidden">
        <?php if (isset($isList) && $isList) : ?>
            <a href="<?= $url; ?>">
                <img height="241"

                     src="<?= Picture::Get($item, Picture::BIG); ?>"></a>
            <?php else : ?>
            <a href="<?= Picture::Get($item, Picture::BIG); ?>" id="img<?= $item['id']; ?>">
                <img width="150"

                     src="<?= Picture::Get($item, Picture::SMALL); ?>">
            </a>
        <?php endif; ?>
    </div>
    <div class="span11">

        <a href="<?= $url; ?>" class="title"><?= ProductHelper::GetTitle($item); ?></a>

        <?php if (!empty($item['Authors'])) : ?>
            <div class="authors">
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
            <div class="authors">
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
            <div class="authors">
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
            <div class="authors">
                <?php
                $ret = array();
                foreach ($item['Actors'] as $actor) {
                    $ret[] = '<a href="' . Yii::app()->createUrl('entity/byactor', array('entity' => $entityKey,
                                'aid' => $actor['id'],
                                'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($actor)))) . '" class="cprop">' . ProductHelper::GetTitle($actor) . '</a>';
                }
                echo sprintf($ui->item("VIDEO_ACTOR_IS"), implode(', ', $ret));
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Subtitles'])) : ?>
            <div class="authors">
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
            <div class="authors">
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

            echo '<div class="langs">'.$ui->item('CATALOGINDEX_CHANGE_LANGUAGE'). ': '.implode(', ', $langs) . '</div>';
            ?>
        <?php endif; ?>
        <div class="desc_text"><?= nl2br(strip_tags(ProductHelper::GetDescription($item, 300))); ?></div>
		
		
</div>

<div class="span1 cart" style="overflow: hidden">
	
		
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
                <input type="hidden" value="<?= round($price[DiscountManager::BRUTTO_WORLD] / 12, 2); ?>" class="worldmonthpriceoriginal"/>
                <input type="hidden" value="<?= round($price[DiscountManager::BRUTTO_FIN] / 12, 2); ?>" class="finmonthpriceoriginal"/>
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

                <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                    
					<div class="minus_plus">
					 <a href="javascript:;" onclick="minus_plus($(this), 'minus')" style="margin-right: 9px;"><?php /*<img src="/new_img/cart_minus.png">*/ ?></a> <input type="text" size="3" class="cart1contents1 center" style="margin: 0; width: 36px;" value="1" onfocus="change_input_plus_minus($(this))" onkeydown="change_input_plus_minus($(this))" onblur="change_input_plus_minus($(this))"> <a href="javascript:;" style="margin-left: 9px;" onclick="minus_plus($(this), 'plus')"><?php /*<img src="/new_img/cart_plus.png">*/ ?></a>
					 </div>
                <?php endif; ?>
				
				
				
				<? $count_add = 1;
					if ($item['entity'] == Entity::PERIODIC) {
						
						$count_add = 12;
						
					}
				?>
				
                <a class="cart-action add_cart<?if (Yii::app()->language == 'es') echo ' no_img';?>" data-action="add" style="width: 162px;font-size: 13px;" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></a><?php else : ?><?php if ($item['entity'] != Entity::VIDEO) : ?>
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
				
            <?php endif; ?>
				<?php $style = '10px'; if ($item['entity'] == Entity::VIDEO) { $style = '0'; echo '<div style="height: 20px;"></div>'; } ?>
				
				
			
			<a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>"
                           data-id="<?= $item['id']; ?>" class="addmark cart-action" style="margin-left: <?=$style?>">&nbsp;</a>
	
</div>

</div>
<?php
//Yii::endProfile($item['id']);