<?php
//Yii::beginProfile($item['id']);
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
$photoTable = Entity::GetEntitiesList()[$entity]['photo_table'];
$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
/**@var $photoModel ModelsPhotos*/
$photoModel = $modelName::model();
$photoId = $photoModel->getFirstId($item['id']);
?>
    <div class="row" xmlns="http://www.w3.org/1999/html">
        <div class="span1 image_item" style="position: relative">
            <?php $this->renderStatusLables(Product::GetStatusProduct($item['entity'], $item['id']))?>
            <?php if (isset($isList) && $isList) : ?>
                <a href="<?= $url; ?>" title="<?= ProductHelper::GetTitle($item); ?>">
                    <?php if (empty($photoId)): ?>
                        <img height="241" lazySrc="<?= Picture::Get($item, Picture::SMALL); ?>" src="<?= Picture::srcLoad() ?>" alt="<?= htmlspecialchars(ProductHelper::GetTitle($item)); ?>">
                    <?php else: ?>
                        <picture>
                            <source srcset="<?= $photoModel->getHrefPath($photoId, 'l', $item['eancode'], 'webp') ?>" type="image/webp">
                            <source srcset="<?= $photoModel->getHrefPath($photoId, 'l', $item['eancode'], 'jpg') ?>" type="image/jpeg">
                            <img src="<?= $photoModel->getHrefPath($photoId, 'o', $item['eancode'], 'jpg') ?>" alt="<?= htmlspecialchars(ProductHelper::GetTitle($item)); ?>">
                        </picture>
                    <?php endif; ?>
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
                            <?= (($entity == Entity::PRINTED) ? str_replace(':', '', $ui->item('CATALOGINDEX_CHANGE_THEME')) : str_replace(':', '', $ui->item('CATALOGINDEX_CHANGE_LANGUAGE'))) ?>
                        </div>
                        <div style="padding-left: 140px;"><?= implode(', ', $langs) ?></div>
                        <div class="clearBoth"></div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($entity != Entity::VIDEO):
                //https://dfaktor.bitrix24.ru/workgroups/group/130/tasks/task/view/7930/?MID=23978#com23978
                ?>
                <? if  (isset($item['year'])) : ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop"><?=str_replace(':', '', ($entity != Entity::VIDEO) ? $ui->item('A_NEW_YEAR') : $ui->item('A_NEW_YEAR_REAL'));?></div>
                    <div style="padding-left: 140px;"><a href="<?=Yii::app()->createUrl('entity/byyear', array('entity' => $entityKey, 'year' => $item['year'])); ?>"><?=$item['year']?></a></div>
                    <div class="clearBoth"></div>
                </div>
            <? endif; ?>
                <? if  (isset($item['release_year'])) : ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop"><?=$ui->item('A_NEW_YEAR_FILM');?></div>
                    <div style="padding-left: 140px;"><a href="<?=Yii::app()->createUrl('entity/byyearrelease', array('entity' => $entityKey, 'year' => $item['release_year'])); ?>"><?=$item['release_year']?></a></div>
                    <div class="clearBoth"></div>
                </div>
            <? endif; ?>
            <?php endif; ?>
            <?php if (!empty($item['Publisher'])) : ?>
                <?php $pubTitle = ProductHelper::GetTitle($item['Publisher']); ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop">
                        <?php
                        if ($entity == Entity::MUSIC) echo str_replace(':', '', $ui->item('A_NEW_LABEL'));
                        elseif ($entity == Entity::SOFT || $entity == Entity::MAPS || $entity == Entity::PRINTED) echo str_replace(':', '', $ui->item('A_NEW_PRODUCER'));
                        else echo str_replace(':', '', sprintf($ui->item("Published by"), ''));
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

            <?

            if (isset($item['type']) && $entity != Entity::PRINTED): ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop"><?=$ui->item('A_NEW_TYPE_IZD')?></div>
                    <?php
                    if ($item['entity'] == Entity::PERIODIC) :
                        $binding = ProductHelper::GetTypesPeriodic($entity, $item['type']);
                    else :
                        $binding = ProductHelper::GetTypesPrinted($entity, $item['type']);
                    endif; ?>
                    <div style="padding-left: 140px;"><a href="<?= Yii::app()->createUrl('entity/bytype', array(
                            'entity' => $entityKey,
                            'type' => $item['type'],
                            'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($binding)),
                        )) ?>"><?= ProductHelper::GetTitle($binding) ?></a></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif;

            ?>

            <?php if (!empty($item['eancode'])&&(in_array($entity, array(/*Entity::SHEETMUSIC, */Entity::MUSIC)))) : ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop">EAN</div>
                    <div style="padding-left: 140px;"><?=str_replace('-','',$item['eancode'])?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php
            if (!empty($item['inDescription'])) $txt = nl2br($item['inDescription']);
            else $txt = nl2br(ProductHelper::GetDescription($item, 200, $url));
            if (!empty($txt)): ?>
                <div class="desc_text" style="margin-bottom: 10px;"><?= $txt ?></div>
            <?php endif; ?>

            <?php if (($entity == Entity::BOOKS)&&!empty($item['numpages'])): ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 135px;" class="nameprop"><?= $ui->item('PAGES_ALL') ?></div>
                    <div style="padding-left: 140px;"><?= $item['numpages'] ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>
            <?php if  (!empty($item['isbn'])&&in_array($entity, array(/*Entity::SHEETMUSIC, */Entity::BOOKS))) :
                $name = 'ISBN';
                if ($entity == Entity::SHEETMUSIC) {$name = 'ISMN/ISBN';}
                ?>

                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop"><?= $name ?></div>
                    <div style="padding-left: 140px;"><?=str_replace('-','',$item['isbn'])?></div>
                    <div class="clearBoth"></div>
                </div>

            <? endif; ?>
            <?php if (!empty($item['eancode'])&&(in_array($entity, array(Entity::SHEETMUSIC/*, Entity::MUSIC*/)))) : ?>
                <div class="authors" style="margin-top: 0;">
                    <div style="float: left;width: 130px;" class="nameprop">EAN</div>
                    <div style="padding-left: 140px;"><?=str_replace('-','',$item['eancode'])?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>


            <?php if (!empty($item['binding_id'])) : ?>
                <?php
                    switch ($entity) {
                        case Entity::BOOKS:case Entity::SHEETMUSIC: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE1'); break;
                        case Entity::MUSIC: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE3'); break;
                        case Entity::PERIODIC: $label = Yii::app()->ui->item('A_NEW_TYPE_IZD'); break;
                        default: $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE2'); break;
                    }
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

            <?php if (!empty($item['stock_id'])&&(in_array($entity, array(Entity::SHEETMUSIC, Entity::MUSIC)))) : ?>
                     <div class="authors" style="margin-top: 0;">
                        <div style="float: left;width: 130px;" class="nameprop">Shelf ID</div>
                        <div style="padding-left: 140px;"><?= $item['stock_id'] ?></div>
                        <div class="clearBoth"></div>
                    </div>
            <?php endif; ?>
            <?php
            if ($entity == Entity::BOOKS):
                $cats = array();
                if (!empty($item['Category'])) {
                    $cats[] = '<a href="' . Yii::app()->createUrl('entity/list', array('entity' => $entityKey, 'cid' => $item['Category']['id'], 'title'=>ProductHelper::ToAscii(ProductHelper::GetTitle($item['Category'])))) . '">' . ProductHelper::GetTitle($item['Category']) . '</a>';
                }
                if (!empty($item['SubCategory'])) {
                    $cats[] = '<a href="' . Yii::app()->createUrl('entity/list', array('entity' => $entityKey, 'cid' => $item['SubCategory']['id'], 'title'=>ProductHelper::ToAscii(ProductHelper::GetTitle($item['SubCategory'])))) . '">' . ProductHelper::GetTitle($item['SubCategory']) . '</a>';
                }

                if (!empty($cats)): ?>
                    <div class="authors" style="margin-top: 0;">
                        <div style="float: left;width: 130px;" class="nameprop">
                            <?= $ui->item('Related categories') ?>
                        </div>
                        <div style="padding-left: 140px;"><?= implode(', ', $cats) ?></div>
                        <div class="clearBoth"></div>
                    </div>
                <?php endif; ?>
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

                <?php if ($item['entity'] == Entity::PERIODIC) :
                    $item['issues_year'] = Periodic::getCountIssues($item['issues_year']);
                    ?>
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
                /*$ie = $item['issues_year'];

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
                }*/
                ?>
                <div class="mb5" style="color:#0A6C9D; float: left;">
                    <?= $ui->item('MSG_DELIVERY_TYPE_4'); ?>
                </div>
                <div style="height: 23px; clear: both"></div>

                <select class="periodic" style="float: left; margin-right: 0; margin-bottom: 19px; width: 180px; font-size: 12px;  ">
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
                    <?php /*if ($item['issues_year']['show6Months']) : ?>
                        <option value="6"<?php if(empty($item['issues_year']['show3Months'])): $count_add = 6; ?> selected="selected"<?php endif; ?>>6 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?> - <?= $item['issues_year']['issues'] ?> <?= $item['issues_year']['label_for_issues'] ?></option>
                    <?php endif; */?>

                    <option value="12"<?php if(empty($item['issues_year']['show3Months'])&&empty($item['issues_year']['show6Months'])): $count_add = 12; ?> selected="selected"<?php endif; ?>>12 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?> - <?= $item['issues_year']['issues_year'] ?> <?= $labelForIssues12 ?></option>
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
                
<?php /*if (empty($item['unitweight'])):?>
                    <div class="free_delivery"><?= $ui->item('MSG_DELIVERY_TYPE_4') ?></div>
<?php endif;*/ ?>
                <form method="get" action="<?= Yii::app()->createUrl('cart/view') ?>" onsubmit="return false;">
				
					<!--<div class="already-in-cart already-in-cart<?=$item['id']?>" style="margin: 9px 0;">
                    <?php if (isset($item['AlreadyInCart'])) : ?>

                        <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                            <?= sprintf(Yii::app()->ui->item('ALREADY_IN_CART'), $item['AlreadyInCart']); ?>
                        <?php else : ?>
                            <?= strip_tags(Yii::app()->ui->item('PERIODIC_ALREADY_IN_CART')); ?>
                        <?php endif; ?>

                    <?php else : ?>&nbsp;
                    <?php endif; ?>
                </div>-->
				
                    <?php if ($item['entity'] != Entity::PERIODIC) : ?>

                        <div class="minus_plus">
                            <a href="javascript:;" onclick="minus_plus($(this), 'minus')" style="margin-right: 9px;"><?php /*<img src="/new_img/cart_minus.png" class="grayscale"> */?></a> <input name="quantity[<?= (int) $item['id'] ?>]" type="text" size="3" class="cart1contents1 center" style="margin: 0; width: 36px;" value="1" onfocus="change_input_plus_minus($(this))" onkeydown="change_input_plus_minus($(this))" onblur="change_input_plus_minus($(this))"> <a href="javascript:;" style="margin-left: 9px;" onclick="minus_plus($(this), 'plus')"><?php /*<img src="/new_img/cart_plus.png"> */?></a>
                        </div>
                    <?php endif; ?>



                    <?php if (empty($count_add)) {
                        $count_add = 1;
                        if ($item['entity'] == Entity::PERIODIC) $count_add = 12;
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
					

                    <?php $style = '10px'; if ($item['entity'] == Entity::VIDEO) { $style = '0'; echo '<div style="height: 20px;"></div>'; } ?>
                    <a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>"
                       data-id="<?= $item['id']; ?>" class="addmark cart-action" style="margin-left: <?=$style?>"><i class="fa fa-heart" aria-hidden="true"></i></a>
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
                   data-id="<?= $item['id']; ?>" class="addmark cart-action" style="margin-left: <?=$style?>"><i class="fa fa-heart" aria-hidden="true"></i></a>

            <?php endif; ?>

        </div>

    </div>
<?php
//Yii::endProfile($item['id']);