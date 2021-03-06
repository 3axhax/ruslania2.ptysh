<?php
Yii::beginProfile($item['id']);
$url = ProductHelper::CreateUrl($item);
$hideButtons = isset($hideButtons) && $hideButtons;
$entityKey = Entity::GetUrlKey($entity);
$photoTable = Entity::GetEntitiesList()[$entity]['photo_table'];
$modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
/**@var $photoModel ModelsPhotos*/
$photoModel = $modelName::model();
$photoId = $photoModel->getFirstId($item['id']);


/* перенес в контроллер
$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);

//var_dump(Yii::app()->getRequest()->cookies['yourView']->value);

$arrGoods = array();

if ($serGoods) {
    $arrGoods = $serGoods;
}

if (!in_array($item['id'] . '_' . $entity, $arrGoods)) {

    $arrGoods[] = $item['id'] . '_' . $entity;

    Yii::app()->getRequest()->cookies['yourView'] = new CHttpCookie('yourView', serialize($arrGoods));

}*/

// запись переменной в сессию. Следующие способы использования эквивалентны:

//echo $language;
?>


<div class="row">
    <div class="span1" style="position: relative">
        <?php $this->renderStatusLables($item['status']); ?>
        <?php if (empty($photoId)): ?>
            <img class="img-view_product" alt="<?= ProductHelper::GetTitle($item); ?>" title="<?= ProductHelper::GetTitle($item); ?>" src="<?= Picture::Get($item, Picture::BIG); ?>">
        <?php elseif ($photoModel->isExternal($photoId)): ?>
            <img class="img-view_product" alt="<?= ProductHelper::GetTitle($item); ?>" title="<?= ProductHelper::GetTitle($item); ?>" src="<?= $photoModel->getHrefPath($photoId, 'd', $item['eancode'], 'jpg') ?>">
        <?php else: ?>
        <picture>
            <source srcset="<?= $photoModel->getHrefPath($photoId, 'd', $item['eancode'], 'webp') ?>" type="image/webp">
            <source srcset="<?= $photoModel->getHrefPath($photoId, 'd', $item['eancode'], 'jpg') ?>" type="image/jpeg">
            <img class="img-view_product" alt="<?= ProductHelper::GetTitle($item); ?>" title="<?= ProductHelper::GetTitle($item); ?>" src="<?= $photoModel->getHrefPath($photoId, 'o', $item['eancode'], 'jpg') ?>">
        </picture>
        <?php endif; ?>
        <?php if (!empty($item['Lookinside'])) : ?>
            <div style="text-align: left;background-color: #fff; margin-top: -10px;">
                <?php
                $images = array();
                $audio = array();
                $pdf = array();
                $first = array('img'=>'', 'audio'=>'', 'pdf'=>'');
                foreach ($item['Lookinside'] as $li) {
                    $ext = strtolower(pathinfo($li['resource_filename'], PATHINFO_EXTENSION));
                    if ($ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
                        if (empty($first['img'])) $first['img'] = '/pictures/lookinside/' . $li['resource_filename'];
                        $images[] = '/pictures/lookinside/' . $li['resource_filename'];
                    }
                    elseif ($ext == 'mp3') {
                        if (empty($first['audio'])) $first['audio'] = $li['resource_filename'];
                        $audio[] = $li['resource_filename'];
                    }
                    else {
                        if (empty($first['pdf'])) $first['pdf'] = $li['resource_filename'];
                        $pdf[] = $li['resource_filename'];
                    }
                }
                $images = implode('|', $images);
                ?>

                <?php if ($item['entity'] == Entity::AUDIO) : ?>
                    <a href="javascript:;" style="width: 261px; margin-right: 30px;"  data-iid="<?= $item['id']; ?>" data-audio="<?= implode('|', $audio); ?>" class="read_book">Смотреть</a>

                    <div id="audioprog<?= $item['id']; ?>" class="audioprogress">
                        <img src="/pic1/isplaying.gif" class="lookinside audiostop"/><br/>
                        <span id="audionow<?= $item['id']; ?>"></span> / <span id="audiototal<?= $item['id']; ?>"></span>

                    </div>
                    <div class="clearBoth"></div>


                <?php else : ?>

                <?php

                // var_dump($images);

                if ($images AND count($pdf)) {


                ?>

                <link rel="stylesheet" href="/css/magnific-popup.css" >
                    <script>

                        function show_popup() {
                            $.magnificPopup.open({
                                items: {
                                    src: '#periodic-price-form2', // can be a HTML string, jQuery object, or CSS selector
                                    type: 'inline'
                                }
                            });
                        }


                    </script>
                    <div id="periodic-price-form2" class="white-popup-block mfp-hide white-popup">
                        <div class="box_title box_title_ru">Галерея страниц:</div>



                        <a href="<?= CHtml::encode($first['img']); ?>" onclick="return false;"
                           data-iid="<?= $item['id']; ?>"
                           data-pdf="<?= CHtml::encode(implode('|', array())); ?>"
                           data-images="<?= CHtml::encode($images); ?>" style="width: 261px; margin: 20px auto; display: block; background: #edb421 none; padding-right: 0" class="read_book link__read">Смотреть галерею</a>

                        <div class="box_title box_title_ru">Файлы:</div>

                        <?php if (!empty($pdf)) : ?>
                            <div id="staticfiles<?= $item['id']; ?>">
                                <ul class="staticfile">
                                    <?php $pdfCounter = 1; ?>
                                    <?php foreach ($pdf as $file) : ?>
                                        <?php $file2 = '/pictures/lookinside/' . $file; ?>
                                        <li style="text-align: center; padding: 5px 0;">
                                            <a target="_blank" href="<?= $file2; ?>"><img
                                                        src="/css/pdf.png"/> <?= $file; ?></a>
                                        </li>
                                        <?php $pdfCounter++; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                    </div>


                    <a href="javascript:;" onclick="show_popup();"
                       data-iid="<?= $item['id']; ?>"
                       data-pdf=""
                       data-images="" style="width: 261px; margin-right: 30px;" target="_blank" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>

                <?php


                } else {


                if ( !$images AND !count($audio) AND count($pdf) == 1 ) :

                ?>
                <?php $file2 = '/pictures/lookinside/' . $pdf[0]; ?>


                    <a href="<?=$file2?>"
                       data-iid="<?= $item['id']; ?>"
                       data-pdf=""
                       data-images="" style="width: 261px; margin-right: 30px;" target="_blank" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>
                <?php

                elseif (!$images AND !count($audio) AND count($pdf) > 1) :
                ?>



                <link rel="stylesheet" href="/css/magnific-popup.css" >
                    <script>

                        function show_popup2() {
                            $.magnificPopup.open({
                                items: {
                                    src: '#periodic-price-form3', // can be a HTML string, jQuery object, or CSS selector
                                    type: 'inline'
                                }
                            });
                        }


                    </script>
                    <div id="periodic-price-form3" class="white-popup-block mfp-hide white-popup">


                        <div class="box_title box_title_ru">Файлы:</div>

                        <?php if (!empty($pdf)) : ?>
                            <div id="staticfiles<?= $item['id']; ?>">
                                <ul class="staticfile">
                                    <?php $pdfCounter = 1; ?>
                                    <?php foreach ($pdf as $file) : ?>
                                        <?php $file2 = '/pictures/lookinside/' . $file; ?>
                                        <li style="text-align: center; padding: 5px 0;">
                                            <a target="_blank" href="<?= $file2; ?>"><img
                                                        src="/css/pdf.png"/> <?= $file; ?></a>
                                        </li>
                                        <?php $pdfCounter++; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                    </div>


                    <a href="javascript:;" onclick="show_popup2();"
                       data-iid="<?= $item['id']; ?>"
                       data-pdf=""
                       data-images="" style="width: 261px; margin-right: 30px;" target="_blank" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>

                <?php

                elseif ($images AND !count($audio) AND !count($pdf)):

                ?>

                    <a href="<?= CHtml::encode($first['img']); ?>" onclick="return false;"
                       data-iid="<?= $item['id']; ?>"
                       data-pdf="<?= CHtml::encode(implode('|', array())); ?>"
                       data-images="<?= CHtml::encode($images); ?>" style="width: 261px; margin-right: 30px;" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>

                <?php

                endif;




                }
                    ?>




                <?php endif; ?>
                <div class="clearBoth"></div>
                <div style="height: 20px;"></div>
            </div>
        <?php endif; ?>
    </div>
    <div class="span11 to_cart"><h1 class="title"><?= ProductHelper::GetTitle($item); ?></h1>

        <? if ($item['title_original']) : ?>
            <div class="authors" style="margin-bottom:10px;">
                <div style="float: left;width: 220px;" class="nameprop"><?= str_replace(':', '', $ui->item("ORIGINAL_NAME")) ?></div>
                <div style="padding-left: 253px;"><?=$item['title_original']?></div>
                <div class="clearBoth"></div>
            </div>
        <? endif; ?>

        <?php if ($item['type'] && $entity != Entity::PRINTED) : ?>
            <span class="nameprop" style="margin-bottom: 5px;"><?=$ui->item('A_NEW_TYPE_IZD')?>:</span> <?php

            if ($item['entity'] == Entity::PERIODIC) :


                $binding = ProductHelper::GetTypesPeriodic($entity, $item['type']);



            else :

                $binding = ProductHelper::GetTypesPrinted($entity, $item['type']);

            endif;

            echo '<a href="'.
                Yii::app()->createUrl('entity/bytype', array(
                    'entity' => $entityKey,
                    'type' => $item['type'],
                    'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($binding)),
                )).'">' . ProductHelper::GetTitle($binding) . '</a>';

        endif;
        ?>

        <?php if (!empty($item['Authors'])) :
            foreach ($item['Authors'] as $author) {
                $authorTitle = ProductHelper::GetTitle($author);
                $tmp[] = '<a href="' . Yii::app()->createUrl('entity/byauthor', array('entity' => $entityKey,
                        'aid' => $author['id'],
                        'title' => ProductHelper::ToAscii($authorTitle))) . '" class="cprop">'
                    . $authorTitle . '</a>';
            }
            ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("WRITTEN_BY"), '')); ?></div>
                <div style="padding-left: 253px;"><?= implode(', ', $tmp); ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>
        <?php Yii::endProfile($item['id'] . '_authors'); ?>


        <?php if (!empty($item['Performers'])) :
            $tmp = array();
            foreach ($item['Performers'] as $performer) {
                $tmp[] = '<a href="' . Yii::app()->createUrl('entity/byperformer', array('entity' => $entityKey,
                        'pid' => $performer['id'],
                        'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($performer)))) . '" class="cprop">'
                    . ProductHelper::GetTitle($performer) . '</a>';
            }
            ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("READ_BY"), '')); ?></div>
                <div style="padding-left: 253px;"><?= implode(', ', $tmp); ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Directors'])) :
            $ret = array();
            foreach ($item['Directors'] as $director) :
                $ret[] = '<a href="' . Yii::app()->createUrl('entity/bydirector', array('entity' => $entityKey,
                        'did' => $director['id'],
                        'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($director)))) . '" class="cprop">' . ProductHelper::GetTitle($director) . '</a>';
                ?>
            <?php endforeach;
            ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("DIRECTOR_IS"), '')); ?></div>
                <div style="padding-left: 253px;"><?= implode(', ', $ret); ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Actors'])) :
            $ret = array();
            foreach ($item['Actors'] as $actor) {
                $ret[] = '<a href="' . Yii::app()->createUrl('entity/byactor', array('entity' => $entityKey,
                        'aid' => $actor['id'],
                        'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($actor)))) . '" class="cprop">' . ProductHelper::GetTitle($actor) . '</a>';
            }
            ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("VIDEO_ACTOR_IS"), '')); ?></div>
                <div style="padding-left: 253px;"><?= implode(', ', $ret); ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['videoStudio'])) :
            $studio = $item['videoStudio'];
            $studio_title = ProductHelper::GetTitle($studio);
            ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', $ui->item("A_NEW_STUDIO")); ?></div>
                <div style="padding-left: 253px;"><a href="<?= Yii::app()->createUrl('entity/bystudio', array(
                        'entity' => $entityKey,
                        'sid' => $studio['id'],
                        'title' => ProductHelper::ToAscii($studio_title),
                )) ?>" class="cprop"><?= $studio_title ?></a></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Media'])) : ?>
            <div class="authors" style="margin-bottom:5px;">
                <?php if ($entity == Entity::MUSIC):?>
                    <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("A_NEW_FILTER_TYPE3"), '')); ?></div>
                <?php else:?>
                    <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("MEDIA_TYPE_OF"), '')); ?></div>
                <?php endif;?>
                <div style="padding-left: 253px;">
                    <a class="cprop" href="<?= Media::Url($item); ?>"><?= $item['Media']['title']; ?></a>
                    <?php if (!empty($item['Zone'])) : ?>, <?= sprintf($ui->item('VIDEO_ZONE'), '<span class="title__bold">' . $item['Zone']['title'] . '</span>'); ?>
                        <a class="pointerhand"
                           href="<?= Yii::app()->createUrl('site/static', array('page' => 'zone_info')); ?>" target="_blank">
                            <img src="/pic1/q1.gif" width="16" height="16"
                                 title="<?= $ui->item("MSG_SHOW_ZONE_INFO"); ?>"
                                 style="position:relative;top:-1px;left:10px;"></a><br/>

                    <?php endif; ?>
                </div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>



        <?php if (!empty($item['playtime'])) : ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("MSG_AUDIO_PLAYING_TIME"), '')); ?></div>
                <div style="padding-left: 253px;"><?= $item['playtime'] ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['cds'])) : ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop">CDs</div>
                <div style="padding-left: 253px;"><?= $item['cds'] ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($items['dvds'])) : ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop">DVDs</div>
                <div style="padding-left: 253px;"><?= $item['dvds'] ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Subtitles'])) :
            $ret = array();
            foreach ($item['Subtitles'] as $subtitle) {
                $ret[] = '<a href="' . Yii::app()->createUrl('entity/bysubtitle', array('entity' => $entityKey,
                        'sid' => $subtitle['id'],
                        'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($subtitle)))) . '" class="cprop">' . ProductHelper::GetTitle($subtitle) . '</a>';
            }
            ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("VIDEO_CREDITS_IS"), '')); ?></div>
                <div style="padding-left: 253px;"><?= implode(', ', $ret) ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['AudioStreams'])) :
            $ret = array();
            foreach ($item['AudioStreams'] as $stream) {
                $ret[] = '<a href="' . Yii::app()->createUrl('entity/byaudiostream', array('entity' => $entityKey,
                        'sid' => $stream['id'],
                        'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($stream)))) . '" class="cprop">' . ProductHelper::GetTitle($stream) . '</a>';
            }
            ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("AUDIO_STREAMS"), '')); ?></div>
                <div style="padding-left: 253px;"><?= implode(', ', $ret) ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Country'])) : ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("COUNTRY_OF_ORIGIN"), '')); ?></div>
                <div style="padding-left: 253px;"><?= ProductHelper::GetTitle($item['Country']) ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['Languages']) && empty($item['AudioStreams'])&&($entity != Entity::MUSIC)) :
            $langs = array();
            foreach ($item['Languages'] as $lang) {
                if (!empty($lang['language_id'])) $langs[] = '<a href="' . Yii::app()->createUrl('entity/list', array(
                        'entity' => $entityKey,
                        'lang' => $lang['language_id'])) .
                    '"><span class="title__bold">' . (($entity != Entity::PRINTED)?Language::GetTitleByID($lang['language_id']):Language::GetTitleByID_country($lang['language_id'])) . '</span></a>';
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
                <div style="padding-left: 253px;"><?= $item['format'] ?></div>
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
                    ?>"><?= $pubTitle; ?></a>
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
                    ?>"><?=$item['year']?></a>
                </div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['release_year'])) : ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', $ui->item("A_NEW_YEAR_FILM")); ?></div>
                <div style="padding-left: 253px;">
                    <a href="<?= Yii::app()->createUrl('entity/byyearrelease', array(
                        'entity' => $entityKey,
                        'year' => $item['release_year']));
                    ?>"><?=$item['release_year']?></a>
                </div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['binding_id'])/*&&!empty($item['Binding']['title_' . Yii::app()->language])*/): ?>
            <div class="authors" style="margin-bottom:5px;">
                <?php
                switch ($entity) {
                    case Entity::BOOKS:case Entity::SHEETMUSIC:
                    $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE1');
                    break;
                    default:
                        $label = Yii::app()->ui->item('A_NEW_FILTER_TYPE2');
                        break;
                }
                ?>
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
                <div style="padding-left: 253px;"><?= $item['numpages'] ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($item['isbn'])&&in_array($entity, array(/*Entity::SHEETMUSIC, */Entity::BOOKS))) :
            //isbn только для книг и нот
            $name = 'ISBN';
            if ($entity == Entity::SHEETMUSIC) {$name = 'ISMN/ISBN';}
            ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop"><?= str_replace(':', '', $name) ?></div>
                <div style="padding-left: 253px;"><?= $item['isbn'] ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>
        <?php /* на конференции решили ean показывать внизу в описании
 задание https://dfaktor.bitrix24.ru/company/personal/user/836/tasks/task/view/7394/
 */
        if (!empty($item['eancode'])&&(in_array($entity, array(Entity::SHEETMUSIC, Entity::MUSIC)))) : ?>
            <div class="authors" style="margin-bottom:5px;">
                <div style="float: left;" class="nameprop">EAN</div>
                <div style="padding-left: 253px;"><?= $item['eancode'] ?></div>
                <div class="clearBoth"></div>
            </div>
        <?php endif; ?>

        <?php if ($item['entity'] == Entity::PERIODIC) : ?>


            <?php if (!empty($item['size'])) : ?>
                <div class="authors" style="margin-bottom:5px;">
                    <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item('PRINTED_SIZE'),''));?></div>
                    <div style="padding-left: 253px;"><?= $item['size'] ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['Series'])) : ?>
                <div class="authors" style="margin-bottom:5px;">
                    <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item('SERIES_IS'),''));?></div>
                    <div style="padding-left: 253px;">
                        <a class="cprop" href="<?= Series::Url($item['Series']); ?>"><?= ProductHelper::GetTitle($item['Series']); ?></a>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['Media'])) : ?>

                <div class="authors" style="margin-bottom:5px;">
                    <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item('MEDIA_TYPE_OF'),''));?></div>
                    <div style="padding-left: 253px;">
                        <a class="cprop" href="<?= Media::Url($item); ?>"><?= $item['Media']['title']; ?></a>
                        <?php if (!empty($item['Zone'])) : ?>, <?= sprintf($ui->item('VIDEO_ZONE'), '<span class="title__bold">' . $item['Zone']['title'] . '</span>'); ?>
                            <a class="pointerhand" href="<?= Yii::app()->createUrl('site/static', array('page' => 'zone_info')); ?>" target="_blank">
                                <img src="/pic1/q1.gif" width="16" height="16" title="<?= $ui->item("MSG_SHOW_ZONE_INFO"); ?>" style="position:relative;top:4px;left:10px;">
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['catalogue'])) : ?>
                <div class="authors" style="margin-bottom:5px;">
                    <div style="float: left;" class="nameprop">Catalogue N</div>
                    <div style="padding-left: 253px;"><?= $item['catalogue']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['issn'])) : ?>
                <div class="authors" style="margin-bottom:5px;">
                    <div style="float: left;" class="nameprop">ISSN</div>
                    <div style="padding-left: 253px;"><?= $item['issn']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['requirements'])) : ?>
                <div class="authors" style="margin-bottom:5px;">
                    <div style="float: left;" class="nameprop"><?= str_replace(':', '', $ui->item('A_SOFT_REQUIREMENTS')); ?></div>
                    <div style="padding-left: 253px;"><?= $item['requirements']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>


            <?php if (!empty($item['index'])) : ?>
                <div class="authors" style="margin-bottom:5px;">
                    <div style="float: left;" class="nameprop"><?= str_replace(':', '', sprintf($ui->item("PERIOD_INDEX"), '')); ?></div>
                    <div style="padding-left: 253px;"><?= $item['index']; ?></div>
                    <div class="clearBoth"></div>
                </div>
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

                <?=$item['issues_year']['description']?>

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

            <div class="already-in-cart" style="margin-top: 30px; float: left; margin-left: 33px; position: relative;">
                <?php //if (isset($item['AlreadyInCart'])) : ?>

					<div class="price_h">&nbsp;</div>
                    <div class="price_h">&nbsp;</div>
                    <?php //if ($item['entity'] != Entity::PERIODIC) : ?>
					
						<?php if ($item['entity'] != Entity::PERIODIC) : ?>
            <div class="mb5" style="color:#4e7eb5; width: 200px; font-size: 13px; ">
                <span style="position: absolute; bottom: 0px; left: 0;"><?= Availability::ToStr($item); ?></span>
            </div>
        <?php //endif; ?>
					
                        <?//= sprintf(Yii::app()->ui->item('ALREADY_IN_CART'), $item['AlreadyInCart']); ?>
                    <?php else : ?>
                        <?= strip_tags(Yii::app()->ui->item('PERIODIC_ALREADY_IN_CART')); ?>
                    <?php endif; ?>

                <?php //endif; ?>
            </div>
        <?php endif; ?>


        <div class="clearfix"></div>
        




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

			<? if ($item['entity'] != Entity::PERIODIC) { ?>
                <div class="clearfix"></div>
                <div style="margin-top: 10px;"></div>
            <?}?>


            <!--    <?php if (!empty($item['Lookinside'])&&($item['entity'] != Entity::PERIODIC)) : ?>

                <?php



                $images = array();
                $audio = array();
                $pdf = array();
                $first = array('img'=>'', 'audio'=>'', 'pdf'=>'');
                foreach ($item['Lookinside'] as $li) {
                    $ext = strtolower(pathinfo($li['resource_filename'], PATHINFO_EXTENSION));
                    if ($ext == 'jpg' || $ext == 'gif') {
                        if (empty($first['img'])) $first['img'] = '/pictures/lookinside/' . $li['resource_filename'];
                        $images[] = '/pictures/lookinside/' . $li['resource_filename'];
                    }
                    elseif ($ext == 'mp3') {
                        if (empty($first['audio'])) $first['audio'] = $li['resource_filename'];
                        $audio[] = $li['resource_filename'];
                    }
                    else {
                        if (empty($first['pdf'])) $first['pdf'] = $li['resource_filename'];
                        $pdf[] = $li['resource_filename'];
                    }
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


                <?php else :  ?>
					<a href="<?= CHtml::encode($first['img']); ?>" onclick="return false;"
                         data-iid="<?= $item['id']; ?>"
                         data-pdf="<?= CHtml::encode(implode('|', array())); ?>"
                         data-images="<?= CHtml::encode($images); ?>" style="width: 131px; margin-right: 30px;" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>
				
                   
                        <?php if (!empty($pdf)) : ?>
                        <div id="staticfiles<?= $item['id']; ?>">
                            <span class="title__bold"><?= $ui->item('MSG_BTN_LOOK_INSIDE'); ?></span> 
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
                    <div class="clearBoth"></div>
                    <div style="height: 20px;"></div>
            <?php endif; ?>
				
				<? $count_add = 1;
            if ($item['entity'] == Entity::PERIODIC) {

                $count_add = 12;

            }

            ?>-->

        <?php else : ?><?php if ($item['entity'] != Entity::VIDEO) : ?>
            <div class="clearBoth"></div>
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

        <?/* if ($item['entity'] == Entity::PERIODIC) { */?><!--
				<div style="height: 18px;"></div>
				
				
				
				<?php /*$lookinside = false; if (!empty($item['Lookinside'])) : */?>
				<div class="link__container" style="float: left; width: 170px;">
                <?php
        /*				$lookinside = true;
                        $images = array();
                        $audio = array();
                        $pdf = array();
                        $first = array('img'=>'', 'audio'=>'', 'pdf'=>'');
                        foreach ($item['Lookinside'] as $li) {
                            $ext = strtolower(pathinfo($li['resource_filename'], PATHINFO_EXTENSION));
                            if ($ext == 'jpg' || $ext == 'gif') {
                                if (empty($first['img'])) $first['img'] = '/pictures/lookinside/' . $li['resource_filename'];
                                $images[] = '/pictures/lookinside/' . $li['resource_filename'];
                            }
                            elseif ($ext == 'mp3') {
                                if (empty($first['audio'])) $first['audio'] = '/pictures/lookinside/' . $li['resource_filename'];
                                $audio[] = $li['resource_filename'];
                            }
                            else {
                                if (empty($first['pdf'])) $first['pdf'] = '/pictures/lookinside/' . $li['resource_filename'];
                                $pdf[] = $li['resource_filename'];
                            }
                        }
                        $images = implode('|', $images);
                        */?>
                
                <?php /*if ($item['entity'] == Entity::AUDIO) : */?>
                    <a href="javascript:;" style="width: 131px; margin-right: 30px;"  data-iid="<?/*= $item['id']; */?>" data-audio="<?/*= implode('|', $audio); */?>" class="read_book">Смотреть</a>
					
                    <div id="audioprog<?/*= $item['id']; */?>" class="audioprogress">
                        <img src="/pic1/isplaying.gif" class="lookinside audiostop"/><br/>
                        <span id="audionow<?/*= $item['id']; */?>"></span> / <span id="audiototal<?/*= $item['id']; */?>"></span>

                    </div>
                    <div class="clearBoth"></div>


                <?php /*else : */?>
					<a href="<?/*= CHtml::encode($first['img']); */?>" onclick="return false;"
                         data-iid="<?/*= $item['id']; */?>"
                         data-pdf="<?/*= CHtml::encode(implode('|', array())); */?>"
                         data-images="<?/*= CHtml::encode($images); */?>" style="width: 131px; margin-right: 30px;" class="read_book"><?/*=$ui->item('A_NEW_VIEW')*/?></a>
				
                   
                        <?php /*if (!empty($pdf)) : */?>
                        <div id="staticfiles<?/*= $item['id']; */?>" style="margin-top: 10px;">
                            <span class="title__bold"><?/*= $ui->item('MSG_BTN_LOOK_INSIDE'); */?></span>
                            <ul class="staticfile">
                                <?php /*$pdfCounter = 1; */?>
                                <?php /*foreach ($pdf as $file) : */?>
                                    <?php /*$file2 = '/pictures/lookinside/' . $file; */?>
                                    <li>
                                        <a target="_blank" href="<?/*= $file2; */?>"><img
                                                src="/css/pdf.png"/><?/*= $pdfCounter . '.pdf'; */?></a>
                                    </li>
                                    <?php /*$pdfCounter++; */?>
                                <?php /*endforeach; */?>
                            </ul>
                        </div>
                    <?php /*endif; */?>
                <?php /*endif; */?>
				</div>
            <?php /*endif; */?>
				
				
			--><?php /* } */?>

        <?php if ($item['entity'] == Entity::PERIODIC): ?>
            <div class="mb5 link__deliver" style="color:#0A6C9D; float: left;">
                <?= $ui->item('MSG_DELIVERY_TYPE_4'); ?>
            </div>
            <div style="height: 23px; clear: both"></div>
			
			<div class="periodics">
			
            <select class="select2_periodic periodic" style="float: left; margin-right: 0; margin-bottom: 19px; width: 180px; font-size: 12px;     margin-top: 5px;" onchange="$('.cart-action.add_cart').attr('data-quantity', $(this).val())">
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
                <option value="12"<?php if(empty($item['issues_year']['show3Months'])&&empty($item['issues_year']['show6Months'])): $count_add = 12; ?> selected="selected"<?php endif; ?>>
                    12 <?= $ui->item('MIN_FOR_X_MONTHS_Y_ISSUES_MONTH_3'); ?> - <?= $item['issues_year']['issues_year'] ?> <?= $labelForIssues12 ?></option>
            </select> </div>
            <?php if ($price[DiscountManager::TYPE_FREE_SHIPPING] && $isAvail) : ?>


                <!--<div style="height: 1px; clear: both"></div>-->
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



            <?php if (isset($item['AlreadyInCart'])) : ?>

                <a class="cart-action add_cart add_cart_plus add_cart_view green_cart cart<?=$item['id']?>" data-action="add" style="width: 132px;float: left;margin-left: 48px;" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" data-hidecount="1" href="javascript:;" onclick="searchTargets('add_cart_view_product');">
                    <span><?=$ui->item('CARTNEW_IN_CART_BTN0')?></span></a>

            <? else : ?>

                <a class="cart-action add_cart add_cart_plus add_cart_view cart<?=$item['id']?>" data-action="add" style="width: 132px;float: left;margin-left: 48px;" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="<?=$count_add?>" data-hidecount="1" href="javascript:;" onclick="searchTargets('add_cart_view_product');">
                    <span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></span></a>

            <? endif; ?>

        <?php endif;?>

        <?php if ($isAvail AND $entity != Entity::PERIODIC) : ?>
			
			<?php if ($item['entity'] != Entity::PERIODIC) : ?>
                
                    
                    <select class="select2_periodic_no_float selquantity" onchange="$('.add_cart_view').removeClass('green_cart'); $('.add_cart_view span').html('<?= htmlspecialchars($ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')) ?>');" style="height: 30px; height: 38px; margin: 0; margin-top: -1px; width: 73px; margin-right: 19px;" id="sel<?= $item['entity']; ?>-<?= $item['id']; ?>"
                            style="display: inline-block; margin-bottom: 5px; width: 85px;">
                        <?php
                        for ($i = 1; $i <= 100; $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        ?>
                    </select>
              
            <?php endif; ?>
			
			<?php if (isset($item['AlreadyInCart'])) : ?>
			
				<a class="cart-action add_cart add_cart_plus add_cart_view green_cart cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" data-hidecount="1" href="javascript:;" onclick="searchTargets('add_cart_view_product');">
			<span style="padding: 0 17px 0 20px;"><?= $ui->item('CARTNEW_IN_CART_BTN', $item['AlreadyInCart']) ?></span></a>
			
			<? else : ?>
				
				<a class="cart-action add_cart add_cart_plus add_cart_view cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" data-hidecount="1" href="javascript:;" onclick="searchTargets('add_cart_view_product');">
			<span style="padding: 0 17px 0 20px;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></span></a>
				
			<? endif; ?>
			
            


        <? endif; 
		
		$class_mark = '';
		$key_btn = 'BTN_SHOPCART_ADD_SUSPEND_ALT';
		if (Cart::isMark($item['entity'], $item['id'],Cart::TYPE_MARK, $this->uid, $this->sid)) {
			
			$class_mark = ' active';
			$key_btn = 'BTN_SHOPCART_DELETE_SUSPEND_ALT';
		}
		
		//echo $class_mark;
		
		?>

        <a href="javascript:;" data-action="mark " data-entity="<?= $item['entity']; ?>" style="margin-left: 19px;"
           data-id="<?= $item['id']; ?>" class="addmark cart-action<?=$class_mark?>"><i class="fa fa-heart" aria-hidden="true"></i><span class="tooltip"><span class="arrow"></span><?=$ui->item($key_btn)?></span></a>


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
            <?php if(!empty($item['presaleMessage'])): ?>
                <div class="presale" style="padding: 10px; margin-bottom: 20px; background-color: #edb421; color: #fff;"><?= $item['presaleMessage'] ?></div>
            <?php endif; ?>
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
                            ?>" class="catlist"><?= $catTitle; ?></a><?if ($i < count($cat)) { ?><br /><? } ?>
                        <?php endforeach; ?></div>
                    <div class="clearBoth"></div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($item['entity'] != Entity::PERIODIC) : ?>

            <?php if (!empty($items['dvds'])) : ?>
                <div class="detail-prop">
                    <div class="prop-name">DVDs</div>
                    <div class="prop-value"><?=$item['dvds']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['size'])) : ?>
                <div class="detail-prop">
                    <div class="prop-name"><?= str_replace(':', '', sprintf($ui->item('PRINTED_SIZE'),''));?></div>
                    <div class="prop-value"><? echo $item['size']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['playtime'])) : ?>

                <div class="detail-prop">
                    <div class="prop-name"><?= str_replace(':', '', sprintf($ui->item('MSG_AUDIO_PLAYING_TIME'),''));?></div>
                    <div class="prop-value"><? echo $item['playtime']; ?></div>
                    <div class="clearBoth"></div>
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

            <?php if (!empty($item['requirements'])) : ?>
                <div class="detail-prop">
                    <div class="prop-name"><?= str_replace(':', '', $ui->item('A_SOFT_REQUIREMENTS')); ?></div>
                    <div class="prop-value"><?= $item['requirements']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>


            <?php if (!empty($item['index'])) : ?>
                <div class="detail-prop">
                    <div class="prop-name"><?= str_replace(':', '', sprintf($ui->item("PERIOD_INDEX"), ''));?></div>
                    <div class="prop-value"><?=$item['index']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif ?>

            <?php if (!empty($item['issn'])) : ?>
                <div class="detail-prop">
                    <div class="prop-name">ISSN</div>
                    <div class="prop-value"><?= $item['issn']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['stock_id'])) : ?>
                <div class="detail-prop">
                    <div class="prop-name"><?= str_replace(':', '', $ui->item('Stock_id')); ?></div>
                    <div class="prop-value"><?= $item['stock_id']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (
                /*in_array($entity, array(Entity::PRINTED, Entity::MAPS, Entity::SOFT, Entity::VIDEO, ))
                || */(($entity == Entity::SHEETMUSIC)&&($item['code'] == 47))
            ) : ?>
            <?php else:
                $name = 'ISBN';
                $isbnNum = 0;
                if ($entity == Entity::SHEETMUSIC) {
                    $name = 'ISMN/ISBN';
                    $isbnNum = null;
                }
            if (!empty($item['eancode'])&&(!in_array($entity, array(Entity::MUSIC, Entity::SHEETMUSIC)))): ?>
                    <div class="detail-prop">
                        <div class="prop-name">EAN</div>
                        <div class="prop-value"><?= $item['eancode']; ?></div>
                        <div class="clearBoth"></div>
                    </div>
            <?php endif; ?>

            <?php if ((Yii::app()->getLanguage() == 'fi')&&($entity == Entity::BOOKS)&&!empty($item['Category']['fin_codes'])): ?>
                <div class="detail-prop">
                    <div class="prop-name">Kirjastoluokka</div>
                    <div class="prop-value"><?= $item['Category']['fin_codes'] ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>
            <?php if (($entity == Entity::BOOKS)&&!empty($item['Category']['BIC_categories'])): ?>
                <div class="detail-prop">
                    <div class="prop-name">BIC-code(s)</div>
                    <div class="prop-value"><?= $item['Category']['BIC_categories'] ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($item['isbn'])&&in_array($entity, array(Entity::SHEETMUSIC))) : ?>
                <div class="detail-prop">
                    <div class="prop-name"><?= $name ?></div>
                    <div class="prop-value"><?= $item['isbn']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif ?>

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
            <?php endif; ?>
        <?php else: ?>
            <?php if (!empty($item['eancode'])):
                //не поймешь, то надо то не надо https://dfaktor.bitrix24.ru/company/personal/user/836/tasks/task/view/6810/
                ?>
                <div class="detail-prop">
                    <div class="prop-name">EAN</div>
                    <div class="prop-value"><?= $item['eancode']; ?></div>
                    <div class="clearBoth"></div>
                </div>
            <?php endif; ?>
            <?php if (!empty($item['issues_year'])):
                $this->renderPartial('/entity/_issues_year', array('item' => $item)) ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php $this->widget('OffersByItem', array('entity'=>$entity, 'idItem'=>$item['id'], 'index_show'=>0)) ?>

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

<?php $this->widget('Similar', array('entity'=>$entity, 'item'=>$item)); ?>
<?php $this->widget('Banners', array('entity'=>$entity)); ?>


<script type="text/javascript">
    $(document).ready(function () {
        $('.selquantity').change(function(){

            $('.cart-action').attr('data-quantity', $('.selquantity').val());
        });
    })
</script>
