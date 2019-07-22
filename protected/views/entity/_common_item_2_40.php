<?php
//Yii::beginProfile($item['id']);
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
?>
    <div class="row" xmlns="http://www.w3.org/1999/html">
        <div class="span1 image_item" style="position: relative">
            <?php $this->renderStatusLables(Product::GetStatusProduct($item['entity'], $item['id']))?>
            <?php if (isset($isList) && $isList) : ?>
                <a href="<?= $url; ?>" title="<?= ProductHelper::GetTitle($item); ?>">
                    <img height="241" lazySrc="<?= Picture::Get($item, Picture::SMALL); ?>" src="<?= Picture::srcLoad() ?>" alt="<?= htmlspecialchars(ProductHelper::GetTitle($item)); ?>">
<?php /*
                    <img height="241" src="<?= Picture::Get($item, Picture::BIG); ?>" alt="<?= ProductHelper::GetTitle($item); ?>">
*/ ?>
                </a>
            <?php else : ?>
                <a href="<?= Picture::Get($item, Picture::BIG); ?>" id="img<?= $item['id']; ?>">
                    <img width="150"

                         src="<?= Picture::Get($item, Picture::SMALL); ?>">
                </a>
            <?php endif; ?>
        </div>
        <div class="span11">

            <a href="<?= $url; ?>" class="title"><?= ProductHelper::GetTitle($item); ?></a>

            <? if (isset($item['title_original']) && $item['title_original'] == '0000000000') : ?>
                <div><span class="nameprop">Оригинальное название:</span> <?=$item['title_original']?>

                </div>
            <? endif; ?>

            <?php if (!empty($item['Authors'])) : ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("WRITTEN_BY"), '')); ?></div>
                    <?php
                    foreach ($item['Authors'] as $author) {
                        $authorTitle = ProductHelper::GetTitle($author);
                        $tmp[] = '<a href="' . Yii::app()->createUrl('entity/byauthor', array('entity' => $entityKey,
                                'aid' => $author['id'],
                                'title' => ProductHelper::ToAscii($authorTitle))) . '" class="cprop">'
                            . $authorTitle . '</a>';
                    }
                    ?>

                    <div style="padding-left: 140px;"><?= implode(', ', $tmp); ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>
            <?php if (!empty($item['Performers'])) : ?>
                <div class="authors"  style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("READ_BY"), '')); ?></div>
                    <?php
                    $tmp = array();
                    foreach ($item['Performers'] as $performer) {
                        $tmp[] = '<a href="' . Yii::app()->createUrl('entity/byperformer', array('entity' => $entityKey,
                                'pid' => $performer['id'],
                                'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($performer)))) . '" class="cprop">'
                            . ProductHelper::GetTitle($performer) . '</a>';
                    }
                    ?>
                    <div style="padding-left: 140px;"><?= implode(', ', $tmp); ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['Directors'])) : ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("DIRECTOR_IS"), '')); ?></div>
                    <div style="padding-left: 140px;">
                        <?php foreach ($item['Directors'] as $director) : ?>
                            <a href="<?=
                            Yii::app()->createUrl('entity/bydirector', array('entity' => $entityKey,
                                'did' => $director['id'],
                                'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($director))));
                            ?>"
                               class="cprop"><?= ProductHelper::GetTitle($director); ?></a>
                        <?php endforeach; ?>
                    </div>
                    <div class="clearBoth"></div>
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
                    } ?>
                    <div style="float: left;width: 130px;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("VIDEO_ACTOR_IS"), '')) ?></div>
                    <div style="padding-left: 140px;"><?= implode(', ', $ret); ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php //https://dfaktor.bitrix24.ru/workgroups/group/130/tasks/task/view/7930/?MID=23978#com23978
            /*if (!empty($item['videoStudio'])) : ?>
                <div class="authors" style="margin-top: 0;">
                    <?php
                    $studio = $item['videoStudio'];
                    $title = ProductHelper::GetTitle($studio);
                    echo $ui->item("A_NEW_STUDIO").' ' . $title;
                    ?>
                </div>
            <?php endif;*/ ?>

            <?php if (!empty($item['Subtitles'])) : ?>
                <div class="authors" style="margin-top: 0;">
                    <?php
                    $ret = array();
                    foreach ($item['Subtitles'] as $subtitle) {
                        $ret[] = '<a href="' . Yii::app()->createUrl('entity/bysubtitle', array('entity' => $entityKey,
                                'sid' => $subtitle['id'],
                                'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($subtitle)))) . '" class="cprop">' . ProductHelper::GetTitle($subtitle) . '</a>';
                    } ?>
                    <div style="float: left;width: 130px;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("VIDEO_CREDITS_IS"), '')) ?></div>
                    <div style="padding-left: 140px;"><?= implode(', ', $ret) ?></div>
                    <div class="clearBoth"></div>
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
                    } ?>
                    <div style="float: left;width: 130px;" class="nameprop"><?= str_replace(':', '', $ui->item("AUDIO_STREAMS")) ?></div>
                    <div style="padding-left: 140px;"><?= implode(', ', $ret); ?></div>
                    <div class="clearBoth"></div>
                </div>

            <?php endif; ?>
            <?php if (!empty($item['Languages']) && empty($item['AudioStreams'])&&($entity != Entity::MUSIC)) : ?>

                <?php
                $langs = array();
                foreach ($item['Languages'] as $lang) {
                    if (!empty($lang['language_id'])) {
                        $langs[] = '<a href="' . Yii::app()->createUrl('entity/list', array('entity' => $entityKey, 'lang' => $lang['language_id'])) . '">' . (($entity != Entity::PRINTED)?Language::GetTitleByID($lang['language_id']):Language::GetTitleByID_country($lang['language_id'])) . '</a>';
                    }
                }

                if (!empty($langs)): ?>
                    <div class="authors" style="margin-top: 0;">
                        <div style="float: left;width: 130px;" class="nameprop">
                            <?=str_replace(':', '', $ui->item('CATALOGINDEX_CHANGE_LANGUAGE')); ?>
                        </div>
                        <div style="padding-left: 140px;"><?= implode(', ', $langs) ?></div>
                        <div class="clearBoth"></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if (!empty($item['Publisher'])) : ?>
                <?php $pubTitle = ProductHelper::GetTitle($item['Publisher']); ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop">
                        <?php
                        echo str_replace(':', '', sprintf($ui->item("Published by"), ''));
                        ?>
                    </div>
                    <div style="padding-left: 140px;">
                        <a class="cprop" href="<?=
                        Yii::app()->createUrl('entity/bypublisher', array('entity' => $entityKey,
                            'pid' => $item['Publisher']['id'],
                            'title' => ProductHelper::ToAscii($pubTitle)));
                        ?>"><?= $pubTitle; ?></a>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            

            
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop"><?=$ui->item('A_NEW_TYPE_IZD')?></div>
                    <?php
                    
                        $binding = ProductHelper::GetTypesPrinted($entity, $item['type']);
                    ?>
                    <div style="padding-left: 140px;"><a href="<?= Yii::app()->createUrl('entity/bytype', array(
                            'entity' => $entityKey,
                            'type' => $item['type'],
                            'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($binding)),
                        )) ?>"><?= ProductHelper::GetTitle($binding) ?></a></div>
                    <div class="clearBoth"></div>
                </div>
           

            

            <?php
            if (!empty($item['inDescription'])) $txt = nl2br($item['inDescription']);
            else $txt = nl2br(ProductHelper::GetDescription($item, 200, $url));
            if (!empty($txt)): ?>
                <div class="desc_text" style="margin-bottom: 10px;"><?= $txt ?></div>
            <?php endif; ?>

            
            


            <?php if (!empty($item['binding_id'])) : ?>
                <?php
                     $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE2');
                    
                    ?>
                    <div class="authors" style="margin-top: 0;">
                        <div style="float: left;width: 130px;" class="nameprop"><?= $label ?></div>
                        <div style="padding-left: 140px;"><a href="<?= Yii::app()->createUrl('entity/bybinding', array(
                                'entity' => $entityKey,
                                'bid' => $item['binding_id'],
                                'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($item['Binding'])),
                            )); ?>"><?= ProductHelper::GetTitle($item['Binding']) ?></a></div>
                        <div class="clearBoth"></div>
                    </div>
            <?php endif; ?>

           
            
            <?php /*if (($entity == Entity::BOOKS)&&!empty($item['Category']['BIC_categories'])): ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop">BIC-code(s)</div>
                    <div style="padding-left: 140px;"><?= $item['Category']['BIC_categories'] ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; */?>

        </div>

        <div class="span1 cart to_cart" style="overflow: hidden">


            <?php



            $price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
            $isAvail = ProductHelper::IsAvailableForOrder($item);
            ?>

            <?php if (Availability::GetStatus($item) != Availability::NOT_AVAIL_AT_ALL) : ?>

                
                    <?=
                    $this->renderPartial('/entity/_priceInfo', array('key' => 'ITEM',
                        'item' => $item,
                        'price' => $price));
                    ?>

               

            <?php endif; ?>

         
                <div class="mb5" style="color:#4e7eb5;">
                    <?= Availability::ToStr($item); ?>
                </div>
          




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
                
<?php /*if (empty($item['unitweight'])):?>
                    <div class="free_delivery"><?= $ui->item('MSG_DELIVERY_TYPE_4') ?></div>
<?php endif;*/ ?>
                <form method="get" action="<?= Yii::app()->createUrl('cart/view') ?>" onsubmit="return false;">
				
					<!--<div class="already-in-cart already-in-cart<?=$item['id']?>" style="margin: 9px 0;">
                    <?php if (isset($item['AlreadyInCart'])) : ?>

                       
                            <?= sprintf(Yii::app()->ui->item('ALREADY_IN_CART'), $item['AlreadyInCart']); ?>
                        

                    
                    <?php endif; ?>
                </div>-->
				
                    

                        <div class="minus_plus">
                            <a href="javascript:;" onclick="minus_plus($(this), 'minus')" style="margin-right: 9px;"><?php /*<img src="/new_img/cart_minus.png" class="grayscale"> */?></a> <input name="quantity[<?= (int) $item['id'] ?>]" type="text" size="3" class="cart1contents1 center" style="margin: 0; width: 36px;" value="1" onfocus="change_input_plus_minus($(this))" onkeydown="change_input_plus_minus($(this))" onblur="change_input_plus_minus($(this))"> <a href="javascript:;" style="margin-left: 9px;" onclick="minus_plus($(this), 'plus')"><?php /*<img src="/new_img/cart_plus.png"> */?></a>
                        </div>
                   



                    <?php if (empty($count_add)) {
                        $count_add = 1;
                    }
                    ?>
                    <input type="hidden" name="entity[<?= (int) $item['id'] ?>]" value="<?= (int) $item['entity'] ?>">

					<?php if (isset($item['AlreadyInCart'])) : ?>

                    <a class="cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$item['id']?> green_cart" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;" onclick="searchTargets('add_cart_listing');">
                        <span><?= Yii::app()->ui->item('CARTNEW_IN_CART_BTN', $item['AlreadyInCart']) ?></span>
                    </a>
					
					<? else : ?>
					<a class="cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" href="javascript:;" onclick="searchTargets('add_cart_listing');">
                        <span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></span>
                    </a>
					
					<? endif; ?>
					

                    <?php $style = '0'; echo '<div style="height: 20px;"></div>'; ?>
                    <a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>"
                       data-id="<?= $item['id']; ?>" class="addmark cart-action" style="margin-left: <?=$style?>"><i class="fa fa-heart" aria-hidden="true"></i></a>
                </form>

            <?endif;?>

        </div>

    </div>
<?php
//Yii::endProfile($item['id']);