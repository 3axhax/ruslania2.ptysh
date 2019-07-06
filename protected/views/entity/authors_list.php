<script src='/js/jquery.autocolumnlist.js'></script>			
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container view_product">


			<div class="row">

        <div class="span10">
            <?php if (empty($liveAction)) $liveAction = 'authors';
            if (($entity == Entity::SOFT || $entity == Entity::MAPS || $entity == Entity::PRINTED) &&
                mb_strtoupper($liveAction) == 'PUBLISHERS') $liveAction = 'producers';
            ?>

<h1 class="titlename"><?php 
    $breadcrumbs = $this->breadcrumbs;
    $h1 = Seo_settings::get()->getH1();
    if (empty($h1)):
        $h1 = array_pop($breadcrumbs);
        unset($breadcrumbs) ;
        $h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
        if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1) $h1 .= ' &ndash; ' . $ui->item('PAGES_N', $page);
    endif;
    ?><?= $h1 ?></h1>




            <div class="text charbox">
				<form method="get" class="search_aut">
                    <div class="loading" style="top: 8px;"><?=$ui->item('A_NEW_SEARCHING_RUR');?></div>
                    <input placeholder="<?= $ui->item('NAME_' . mb_strtoupper($liveAction) . '_BY_SEARCH') ?>" type="text" id="js_search_authors" name="qa" value="<?= Yii::app()->getRequest()->getParam('qa') ?>"/>
				    <input type="submit" value="<?= $ui->item('A_LEFT_SEARCH_WIN') ?>"/>

				</form>
                <?php if (empty($route)) $route = 'entity/authorlist';
                $lineRu = $lineOther = false;
                if (empty($chasdr)) $chasdr = '';
                $yo = false;
                foreach($abc as $item) :
				    if (trim($item['first_'.$lang]) == '') continue;
                    if (trim($item['first_'.$lang]) == 'Ё') {
                        $yo = true;
                        continue;
                    }
                    if ($yo&&!in_array(trim($item['first_'.$lang]), array('А','Б','В','Г','Д','Е'))):
                        $yo = false; ?>
                        <a class="<?=(('Ё' == $chasdr) ? 'active' : '')?>" href="<?=Yii::app()->createUrl($route,
                            array('entity' => Entity::GetUrlKey($entity), 'char' => 'Ё')); ?>">Ё</a>
                    <?php endif;
                    if (!$lineOther&&preg_match("/[^a-zа-я0-9]/ui", $item['first_'.$lang])): $lineOther = true; ?>
                        <br>
                    <?php elseif(!$lineRu&&preg_match("/[а-яё]/ui", $item['first_'.$lang])): $lineRu = true;  ?>
                        <br>
                    <?php endif; ?>
                    <a class="<?=(($item['first_'.$lang] == $chasdr) ? 'active' : '')?>" href="<?=Yii::app()->createUrl($route,
                        array('entity' => Entity::GetUrlKey($entity), 'char' => $item['first_'.$lang])); ?>"
                       ><?=$item['first_'.$lang]; ?></a>
                <?php endforeach; ?>
            </div>
			<?php if ($_GET['char'] != ''):?>
			<h2 class="title_char"><?=$_GET['char']?></h2>
			<?php endif;
            ?>
            <div class="text">
                <ul class="list authors" id="al">
                    <?php if (empty($url)) $url ='entity/byauthor'; ?>
                    <?php if (empty($idName)) $idName = 'aid';
                    $lang = Yii::app()->getLanguage();
                    if (!in_array($lang, HrefTitles::get()->getLangs($entity, $url))) $lang = 'en';
                    ?>

                    <?php foreach($list as $item) : ?>
                        <?php $title = $item['title_' . $lang];
                        if (preg_match("/\w/iu", $title)): ?>
                        <li style="margin-bottom: 10px;"><a href="<?=Yii::app()->createUrl($url,
                                array('entity' => Entity::GetUrlKey($entity),
                                      $idName => $item['id'],
                                      'title' => ProductHelper::ToAscii($title)
                                )); ?>" title="<?=$title; ?>"><?=$title; ?></a></li>
                    <?php endif; endforeach; ?>
					
                </ul>
				<div class="clearfix"></div>
                <?php if ($paginatorInfo) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
            </div>
			
            <!-- /content -->
        </div>

                <div class="span2">
                    <?php $this->widget('YouView', array()); ?>
                </div>

        </div>
        </div>

<script type="text/javascript">

    $(document).ready(function() {
        $('#al').autocolumnlist({ columns: 3});
        scriptLoader('/js/marcopolo.js').callFunction(function(){
            var dataPost = <?= json_encode(array('entity'=>$entity)) ?>;
            $('#js_search_authors').marcoPolo({
                minChars:1,
                cache : false,
                hideOnSelect: false,
                url:'/liveSearch/<?= $liveAction ?>',
                data:dataPost,
                formatItem:function (data, $item, q) {
                    return '<a class="page_detail_link" href="' + data.href + '">' + data.title + '</a>';
                }
            });
        });
    });

</script>