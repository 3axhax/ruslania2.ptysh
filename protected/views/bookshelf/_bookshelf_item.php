<?php
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
?>
    <div class="wrapper text">
    <div class="wrapmiddle">
    <div class="middle text" style="height:auto!important;">
    <?php if (isset($isList) && $isList) : ?>
        <a href="<?= $url; ?>" class="title" title="<?= ProductHelper::GetTitle($item); ?>"><?= ProductHelper::GetTitle($item); ?></a>
        <div class="binfo">
        </div>
    <?php else : ?>
        <h1 class="title"><?= ProductHelper::GetTitle($item); ?></h1>
    <?php endif; ?>

    <?php if (!empty($item['Authors'])) : ?>
        <div class="authors">
            <?= sprintf($ui->item("WRITTEN_BY"), ''); ?>
            <?php foreach ($item['Authors'] as $author)
            {
                $authorTitle = ProductHelper::GetTitle($author);
                $tmp[] = '<a href="' . Yii::app()->createUrl('entity/byauthor',
                        array('entity' => $entityKey,
                            'aid' => $author['id'],
                            'title' => ProductHelper::ToAscii($authorTitle))) . '" class="cprop">'
                    . $authorTitle . '</a>';
            } ?>

            <?= implode(', ', $tmp); ?>

        </div>
    <?php endif; ?>


    <?php if (!empty($item['Performers'])) : ?>
        <div class="authors">
            <?= sprintf($ui->item("READ_BY"), ''); ?>
            <?php
            $tmp = array();
            foreach ($item['Performers'] as $performer)
            {
                $tmp[] = '<a href="' . Yii::app()->createUrl('entity/byperformer',
                        array('entity' => $entityKey,
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
                Yii::app()->createUrl('entity/bydirector',
                    array('entity' => $entityKey,
                        'did' => $director['id'],
                        'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($director)))); ?>"
                   class="cprop"><?= ProductHelper::GetTitle($director); ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($item['Actors'])) : ?>
        <div class="authors">
            <?php $ret = array();
            foreach ($item['Actors'] as $actor)
            {
                $ret[] = '<a href="' . Yii::app()->createUrl('entity/byactor',
                        array('entity' => $entityKey,
                            'aid' => $actor['id'],
                            'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($actor)))) . '" class="cprop">' . ProductHelper::GetTitle($actor) . '</a>';
            }
            echo sprintf($ui->item("VIDEO_ACTOR_IS"), implode(', ', $ret));
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($item['Subtitles'])) : ?>
        <div class="authors">
            <?php $ret = array();
            foreach ($item['Subtitles'] as $subtitle)
            {
                $ret[] = '<a href="' . Yii::app()->createUrl('entity/bysubtitle',
                        array('entity' => $entityKey,
                            'sid' => $subtitle['id'],
                            'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($subtitle)))) . '" class="cprop">' . ProductHelper::GetTitle($subtitle) . '</a>';
            }
            echo sprintf($ui->item("VIDEO_CREDITS_IS"), implode(', ', $ret));
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($item['AudioStreams'])) : ?>
        <div class="authors">
            <?php $ret = array();
            foreach ($item['AudioStreams'] as $stream)
            {
                $ret[] = '<a href="' . Yii::app()->createUrl('entity/byaudiostream',
                        array('entity' => $entityKey,
                            'sid' => $stream['id'],
                            'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($stream)))) . '" class="cprop">' . ProductHelper::GetTitle($stream) . '</a>';
            }
            echo $ui->item("AUDIO_STREAMS") . ': ' . implode(', ', $ret);
            ?>
        </div>

    <?php endif; ?>

    <?php if (!empty($items['dvds'])) : ?>
        <br>DVDs: <?= $item['dvds']; ?>
    <?php endif; ?>

    <?php if (!empty($item['numpages'])) : ?>
        <?= sprintf($ui->item('X_PAGES_3'), $item['numpages']); ?>,
    <?php endif; ?>

    <?php if (!empty($item['Binding'])) : ?>
        <?php $tBinding = ProductHelper::GetTitle($item['Binding']); ?>
        <a href="<?= Yii::app()->createUrl('entity/bybinding',
            array('entity' => Entity::GetUrlKey($entity),
                'bid' => $item['Binding']['id'],
                'title' => ProductHelper::ToAscii($tBinding))); ?>" class="cprop"><?= $tBinding; ?></a><br/>
    <?php endif; ?>

    <?php if (!empty($item['Publisher'])) : ?>
        <?php $pubTitle = ProductHelper::GetTitle($item['Publisher']); ?>
        <?= sprintf($ui->item("PUBLISHED_BY"), ''); ?> <a class="cprop" href="<?=
        Yii::app()->createUrl('entity/bypublisher',
            array('entity' => $entityKey,
                'pid' => $item['Publisher']['id'],
                'title' => ProductHelper::ToAscii($pubTitle))); ?>"><?= $pubTitle; ?></a><?php endif; ?>


    <?php if (!empty($item['year'])) : ?>
        <a class="cprop" href="<?= Yii::app()->createUrl('entity/byyear', array(
            'entity' => $entityKey,
            'year' => $item['year'])); ?>"><?= sprintf($ui->item('IN_YEAR'), $item['year']); ?></a>
        <br/>
    <?php endif; ?>

    <?php if (!empty($item['size'])) : ?>
        <?= sprintf($ui->item('PRINTED_SIZE', $item['size'])); ?><br/>
    <?php endif; ?>

    <?php if (!empty($item['playtime'])) : ?>

        <?= sprintf($ui->item('MSG_AUDIO_PLAYING_TIME'), $item['playtime']); ?><br/>

    <?php endif; ?>


    <?php if (!empty($item['Series'])) : ?>
        <?= sprintf($ui->item("SERIES_IS"), ''); ?>
        <a class="cprop"
           href="<?= Series::Url($item['Series']); ?>"><?= ProductHelper::GetTitle($item['Series']); ?></a>
        <br/>
    <?php endif; ?>

    <?php if (!empty($item['Media'])) : ?>
        <?= sprintf($ui->item("MEDIA_TYPE_OF"), ''); ?>
        <a class="cprop"
           href="<?= Media::Url($item); ?>"><?= $item['Media']['title']; ?></a><?php if (!empty($item['Zone'])) : ?>, <?= sprintf($ui->item('VIDEO_ZONE'), '<b>' . $item['Zone']['title'] . '</b>'); ?>
            <a class="pointerhand"
               href="<?= Yii::app()->createUrl('site/static', array('page' => 'zone_info')); ?>" target="_blank">
                <img src="/pic1/q1.gif" width="16" height="16"
                     title="<?= $ui->item("MSG_SHOW_ZONE_INFO"); ?>"
                     style="position:relative;top:4px;left:10px;"></a><br/>

        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($item['Languages']) && empty($item['AudioStreams'])) : ?>
        <br/>
        <?= $ui->item('CATALOGINDEX_CHANGE_LANGUAGE'); ?>:
        <?php
        $langs = array();
        foreach ($item['Languages'] as $lang)
        {
            $langs[] = '<b>' . Language::GetTitleByID($lang['language_id']) . '</b>';
        }

        echo implode(', ', $langs) . '<br/>';

        ?>
    <?php endif; ?>


    <?php if (!empty($item['MagazineType'])) : ?>
        <a href="<?=
        Yii::app()->createUrl('entity/bymagazinetype', array('entity' => $entityKey,
            'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($item['MagazineType'])),
            'tid' => $item['MagazineType']['id'])); ?>"
           class="cprop"><?= ProductHelper::GetTitle($item['MagazineType']); ?></a>
        <br/>
    <?php endif; ?>

    <?php if (!empty($item['cds'])) : ?>
        CDs: <?= $item['cds']; ?><br/>
    <?php endif; ?>

    <?php if (!empty($item['catalogue'])) : ?>
        Catalogue N: <?= $item['catalogue']; ?><br/>
    <?php endif; ?>

    <?php if (!empty($item['eancode'])) : ?>
        Ean: <?= $item['eancode']; ?>
    <?php endif; ?>

    <?php if (!empty($item['isbn'])) : ?>
        <br/>
        ISBN: <?= $item['isbn']; ?>
    <?php endif; ?>

    <?php if (!empty($item['Country'])) : ?>
        <br/>
        <?= sprintf($ui->item("COUNTRY_OF_ORIGIN", ProductHelper::GetTitle($item['Country']))); ?>
    <?php endif; ?>

    <?php if (!empty($item['issues_year'])) $this->renderPartial('/entity/_issues_year', array('item' => $item)) ?>

    <?php if (!empty($item['requirements'])) : ?>
        <br/><?= $ui->item('A_SOFT_REQUIREMENTS'); ?>: <?= $item['requirements']; ?>
    <?php endif; ?>


    <?php if (!empty($item['index'])) : ?>
        <br/>
        <?= sprintf($ui->item("PERIOD_INDEX"), $item['index']); ?>
    <?php endif ?>

    <?php if (!empty($item['issn'])) : ?>
        <br/>
        ISSN: <?= $item['issn']; ?>
    <?php endif; ?>

    <?php if (!empty($item['stock_id'])) : ?>
        <br/>
        <?= $ui->item('Stock_id'); ?>: <?= $item['stock_id']; ?>
    <?php endif; ?>


    <?php $cat = array();
    if (!empty($item['Category'])) $cat[] = $item['Category'];
    if (!empty($item['SubCategory'])) $cat[] = $item['SubCategory'];
    ?>
    <?php if (!empty($cat)) : ?>
        <div class="blue_arrow text">
            <img height="7" width="8" src="/pic1/bluearr1.gif"/> <?= $ui->item('Related categories'); ?>:
            <?php foreach ($cat as $c) : ?>
                <?php $catTitle = ProductHelper::GetTitle($c); ?>
                <a href="<?=
                Yii::app()->createUrl('entity/list', array('entity' => $entityKey,
                    'cid' => $c['id'],
                    'title' => ProductHelper::ToAscii($catTitle)
                )); ?>" class="catlist"><?= $catTitle; ?></a>;
            <?php endforeach; ?>

            <?php
            $finCodes = array();
            $bicCodes = array();
            foreach ($cat as $c)
            {
                if (array_key_exists('fin_codes', $c) && !empty($c['fin_codes'])) $finCodes[] = $c['fin_codes'];
                if (array_key_exists('BIC_categories', $c) && !empty($c['BIC_categories'])) $bicCodes[] = $c['BIC_categories'];
            }
            ?>
            <?php if (Yii::app()->language == 'fi' && !empty($finCodes)) : ?>
                <br/>Kirjastoluokka: <?= implode(', ', $finCodes); ?>
            <?php endif; ?>
            <?php if (!empty($bicCodes)) : ?>
                <br/>BIC-code(s): <?= implode(', ', $bicCodes); ?>
            <?php endif; ?>


        </div>
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

    <?php if (!empty($item['Lookinside'])) : ?>

        <?php
        $images = array();
        $audio = array();
        $pdf = array();
        foreach ($item['Lookinside'] as $li)
        {
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
        <br/>
        <?php if ($item['entity'] == Entity::AUDIO) : ?>
            <img src="/pic1/<?= $ui->item('MSG_BTN_CD_HEAR_IT_PICTURE'); ?>"
                 class="lookinside liaudio" data-iid="<?= $item['id']; ?>"
                 data-audio="<?= implode('|', $audio); ?>"
                />
            <div id="audioprog<?= $item['id']; ?>" class="audioprogress">
                <img src="/pic1/isplaying.gif" class="lookinside audiostop"/><br/>
                <span id="audionow<?= $item['id']; ?>"></span> / <span id="audiototal<?= $item['id']; ?>"></span>

            </div>
            <div class="clearBoth"></div>


        <?php else : ?>
            <img src="/pic1/<?= $ui->item('MSG_BTN_LOOK_INSIDE_PICTURE'); ?>"
                 class="lookinside liimg"
                 data-iid="<?= $item['id']; ?>"
                 data-pdf="<?= CHtml::encode(implode('|', array())); ?>"
                 data-images="<?= CHtml::encode($images); ?>"/>
            <?php if (!empty($pdf)) : ?>
                <div id="staticfiles<?= $item['id']; ?>">
                    <b><?= $ui->item('MSG_BTN_LOOK_INSIDE'); ?></b> <!--  -->
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

    </div>
    </div>
    <div class="left">
        <?php if (isset($isList) && $isList) : ?>
            <a href="<?= $url; ?>"  title="<?= $authorTitle; ?>">
                <img width="150"

                     src="<?= Picture::Get($item, Picture::SMALL); ?>" alt="<?= $authorTitle; ?>"></a>
        <?php else : ?>
            <a href="<?= Picture::Get($item, Picture::BIG); ?>" title="<?= $authorTitle; ?>" id="img<?= $item['id']; ?>">
                <img width="150"

                     src="<?= Picture::Get($item, Picture::SMALL); ?>" alt="<?= $authorTitle; ?>">
            </a>
        <?php endif; ?>
    </div>
    <div class="right">


        <div class="to_cart text">


            <?php $price = DiscountManager::GetPrice(Yii::app()->user->id, $item);
            $isAvail = ProductHelper::IsAvailableForOrder($item);
            ?>

            <?php if (Availability::GetStatus($item) != Availability::NOT_AVAIL_AT_ALL) : ?>

                <?php if ($item['entity'] == Entity::PERIODIC) : ?>

                    <?= $this->renderPartial('/entity/_priceInfo', array('key' => 'PERIODIC_FIN',
                        'item' => $item,
                        'price' => $price)); ?>

                    <?= $this->renderPartial('/entity/_priceInfo', array('key' => 'PERIODIC_WORLD',
                        'item' => $item,
                        'price' => $price)); ?>



                <?php else : ?>
                    <?= $this->renderPartial('/entity/_priceInfo', array('key' => 'ITEM',
                        'item' => $item,
                        'price' => $price)); ?>

                <?php endif; ?>

            <?php endif; ?>

            <?php $quantity = ($item['entity'] == Entity::PERIODIC) ? 12 : 1; ?>

            <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                <div class="mb5" style="color:#0A6C9D">
                    <?= Availability::ToStr($item); ?>
                </div>
            <?php endif; ?>

            <?php if ($price[DiscountManager::TYPE_FREE_SHIPPING] && $isAvail) : ?>
                <div class="mb5" style="color:#0A6C9D">
                    <?= $ui->item('MSG_DELIVERY_TYPE_4'); ?>
                </div>

            <?php endif; ?>

            <?php if ($hideButtons)
            {
                echo '</div>';
                echo '</div>';
                echo '<div class="clearBoth"></div>';
                return;
            }; ?>

            <?php if ($item['entity'] == Entity::PERIODIC) : ?>

                <?php $ie = $item['issues_year'];

                if ($ie < 12)
                {
                    $inOneMonth = $ie / 12;
                    $show3Months = false;
                    $show6Months = false;

                    $tmp1 = $inOneMonth * 3;
                    if (ctype_digit("$tmp1")) $show3Months = true;
                    $tmp2 = $inOneMonth * 6;
                    if (ctype_digit("$tmp2")) $show6Months = true;
                }
                else
                {
                    $show3Months = true;
                    $show6Months = true;
                }


                ?>

                <select class="periodic">
                    <?php if($show3Months) : ?>
                        <option value="3">3 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_2'); ?></option>
                    <?php endif; ?>

                    <?php if($show6Months) : ?>
                        <option value="6">6 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?></option>
                    <?php endif; ?>

                    <option value="12" selected="selected">
                        12 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?></option>
                </select><br/><br/>
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

            <?php endif; ?>

            <?php if ($isAvail) : ?>

                <span class="already-in-cart">
                <?php if (isset($item['AlreadyInCart'])) : ?>
                    <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                        <?= sprintf(Yii::app()->ui->item('ALREADY_IN_CART'), $item['AlreadyInCart']); ?><br/>
                    <?php else : ?>
                        <?= strip_tags(Yii::app()->ui->item('PERIODIC_ALREADY_IN_CART')); ?><br/><br/>
                    <?php endif; ?>
                <?php endif; ?>
                </span>

                <?php if ($item['entity'] != Entity::PERIODIC) : ?>
                    <br/>
                    <?= $ui->item('CART_COL_QUANTITY'); ?>:
                    <select class="selquantity" id="sel<?= $item['entity']; ?>-<?= $item['id']; ?>"
                            style="display: inline-block; margin-bottom: 5px;">
                        <?php for ($i = 1; $i <= 100; $i++)
                        {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <a class="cart-action add" data-action="add" data-entity="<?= $item['entity']; ?>"
                   data-quantity=<?= $quantity; ?> data-id="<?= $item['id']; ?>"
                   href="<?= Yii::app()->createUrl('cart/add', array('entity' => $item['entity'], 'id' => $item['id'])); ?>"><img
                        src="/pic1/<?= $ui->item('ADD_TO_BASKET_PICTURE'); ?>"
                        alt="<?= $ui->item('ADD_TO_BASKET_ALT'); ?>"/></a><br/>

            <?php else : ?>

                <?php if ($item['entity'] != Entity::VIDEO) : ?>
                    <?php if (Yii::app()->user->isGuest) : ?>

                        <a href="<?=
                        Yii::app()->createUrl('cart/dorequest',
                            array('entity' => Entity::GetUrlKey($item['entity']),
                                'iid' => $item['id'])); ?>"><img
                                src="/pic1/<?= $ui->item('BTN_SHOPCART_LEAVE_ORDER_PICTURE'); ?>"
                                alt="<?= $ui->item('BTN_SHOPCART_LEAVE_ORDER_ALT'); ?>"/></a>

                    <?php else : ?>
                        <a class="cart-action" data-action="request" data-entity="<?= $item['entity']; ?>"
                           data-id="<?= $item['id']; ?>"
                           href="<?= Yii::app()->createUrl('cart/request', array('entity' => $item['entity'], 'id' => $item['id'])); ?>"><img
                                src="/pic1/<?= $ui->item('BTN_SHOPCART_LEAVE_ORDER_PICTURE'); ?>"
                                alt="<?= $ui->item('BTN_SHOPCART_LEAVE_ORDER_ALT'); ?>"/></a>

                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>


            <a class="cart-action" data-action="mark" data-entity="<?= $item['entity']; ?>"
               data-id="<?= $item['id']; ?>"
               href="<?= Yii::app()->createUrl('cart/mark', array('entity' => $item['entity'], 'id' => $item['id'])); ?>"><img
                    src="/pic1/<?= $ui->item('BTN_SHOPCART_ADD_SUSPEND_PICTURE'); ?>"
                    alt="<?= $ui->item('BTN_SHOPCART_ADD_SUSPEND_ALT'); ?>"/></a><br/>
        </div>


    </div>
    <div class="clearBoth"></div>
    </div>

