<script src='/js/jquery.autocolumnlist.js'></script>			
			<script type="text/javascript">
    $(document).ready(function() {
        $('.container_slides ul').slick({
            lazyLoad: 'ondemand',
            slidesToShow: 3,
            slidesToScroll: 1
        });
    });
    </script>

	<!--<div class="slider_bg" style="margin-bottom: 40px;">
        
        <div class="container slider_container">
            <div class="btn_left"><img src="/new_img/btn_left.png" /></div>
            <div class="btn_right"><img src="/new_img/btn_right.png" /></div>
            <div class="overflow_box">
                <div class="container_slides" style="width: 1170px;">
                
                <ul>
                    <li>
                        <div class="span1 photo new">
                            <div class="new_block">Новинка!</div>
                            <img src="/new_img/book.png" alt=""/>
                        </div>
                        <div class="span2 text">
                            
                            <div class="title"><a href="">Relapse</a></div>
                            <div class="cost">15.30 €</div>
                            <div class="nds">87.27 € без НДС</div>
                            <a href="" class="btn_yellow">Подробнее</a>
                            
                        </div>
                    </li>
                    <li>
                        <div class="span1 photo new">
                            <div class="new_block">Новинка!</div>
                            <img src="/new_img/book.png" alt=""/>
                        </div>
                        <div class="span2 text">
                            
                            <div class="title"><a href="">Relapse</a></div>
                            <div class="cost">15.30 €</div>
                            <div class="nds">87.27 € без НДС</div>
                            <a href="" class="btn_yellow">Подробнее</a>
                            
                        </div>
                    </li>
                    <li>
                        <div class="span1 photo akciya">
                            <div class="new_block">Акция</div>
                            <img src="/new_img/book.png" alt=""/>
                        </div>
                        <div class="span2 text">
                            <div class="title"><a href="">Диета для гурманов. План питания от доктора...</a></div>
                            <div class="cost"><span class="z">15.30</span> <span class="n">15.30  €</span></div>
                            <div class="nds">87.27 € без НДС</div>
                            <a href="" class="btn_yellow">Подробнее</a>
                        </div>
                    </li>
                    <li>
                        <div class="span1 photo">
                            <img src="/new_img/book.png" alt=""/>
                        </div>
                        <div class="span2 text">
                            <div class="title"><a href="">Диета для гурманов. План питания от доктора...</a></div>
                            <div class="cost">15.30 €</div>
                            <div class="nds">87.27 € без НДС</div>
                            <a href="" class="btn_yellow">Подробнее</a>
                        </div>
                    </li>
                </ul>
                
            </div>
            </div>
            
        </div>
        
    </div>-->

<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container view_product">


			<div class="row">

        <div class="span10">






            <div class="text charbox">
			    <?php if (empty($liveAction)) $liveAction = 'authors'?>
				<form method="get" class="search_aut">
                    <div class="loading" style="top: 8px;"><?=$ui->item('A_NEW_SEARCHING_RUR');?></div>
                    <input placeholder="<?= $ui->item('NAME_' . mb_strtoupper($liveAction) . '_BY_SEARCH') ?>" type="text" id="js_search_authors" name="qa" value="<?= Yii::app()->getRequest()->getParam('qa') ?>"/>
				    <input type="submit" value="Поиск"/>

				</form>
                <script type="text/javascript">
                    $(document).ready(function() {
                        var dataPost = <?= json_encode(array('entity'=>$entity)) ?>;
//                        var csrf = $('meta[name=csrf]').attr('content').split('=');
//                        dataPost[csrf[0]] = csrf[1];
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
                </script>

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
                            array('entity' => Entity::GetUrlKey($entity), 'char' => 'Ё')); ?>"
                            >Ё</a>
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
			<h1 class="title_char"><?=$_GET['char']?></h1>
			<?php endif; ?>
            <div class="text">
                <ul class="list authors" id="al">
                    <?php if (empty($url)) $url ='/entity/byauthor'; ?>
                    <?php if (empty($idName)) $idName = 'aid'; ?>
                    <?php foreach($list as $item) : ?>
                        <?php $title = $item['title_'.$lang];
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

    $(document).ready(function()
    {
        $('#al').autocolumnlist({ columns: 3});
    });

</script>