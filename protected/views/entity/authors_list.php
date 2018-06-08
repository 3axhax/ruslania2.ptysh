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
			
				<form method="get" class="search_aut">
                    <input type="text" name="qa" value="<?=$_GET['qa']?>"/>
<?php /*
				<input type="hidden" name="qa" value="<?=$_GET['qa']?>"/>
                <input type="text" name="new_qa" class="find_author interactive_find" autocomplete="off" disabled value="<?= Yii::app()->getRequest()->getParam('qa') ?>" placeholder="Поиск автора">
                    <script>
                        var author_search = [];
                        console.log('lang=<?=$lang?>');
                        $.ajax({
                            url: '/entity/getauthordata',
                            data: {entity: '<?=$entity?>', lang: '<?=$lang?>', cid: '<?=$cid?>'},
                            type: 'GET',
                            beforeSend: function () {
                                $(".find_author").attr('disabled', true);
                                $(".find_author").val('Загрузка...');
                            },
                            success: function (data) {
                                author_search = JSON.parse(data);
                                var search_auth = [];
                                $.each(author_search, function(index, value) {
                                    if ((value != '') && (value != null) ) search_auth[index] = value;
                                });
                                interactiveSearch('.find_author', search_auth, 'qa', '.search_result_author');
                                $(".find_author").attr('disabled', false);
                                $(".find_author").val('');
                            },
                            error: function (data) {
                                console.log("Error response");
                            },
                        });
                    </script>
*/ ?>
				<input type="submit" value="Поиск"/>

				</form>
			
                <?php foreach($abc as $item) : ?>
				<?if (trim($item['first_'.$lang]) == '') continue;?>
                    <a class="<?=(($item['first_'.$lang] == $_GET['char']) ? 'active' : '')?>" href="<?=Yii::app()->createUrl('entity/authorlist',
                        array('entity' => Entity::GetUrlKey($entity), 'char' => $item['first_'.$lang])); ?>"
                       ><?=$item['first_'.$lang]; ?></a>
                    <?if (trim($item['first_'.$lang]) == 'Z') echo '<br>';?>
                <?php endforeach; ?>
            </div>
			<? if ($_GET['char'] != '') {?>
			<h1 class="title_char"><?=$_GET['char']?></h1>
			<?}?>
            <div class="text">
                <ul class="list authors" id="al">
                    <?php $url ='/entity/byauthor'; ?>
                    <?php $idName = isset($idName) ? $idName : 'aid'; ?>
                    <?php foreach($list as $item) : ?>
                        <?php $title = $item['title_'.$lang]; ?>
                        <li style="margin-bottom: 10px;"><a href="<?=Yii::app()->createUrl($url,
                                array('entity' => Entity::GetUrlKey($entity),
                                      $idName => $item['id'],
                                      'title' => ProductHelper::ToAscii($title)
                                )); ?>" title="<?=$title; ?>"><?=$title; ?></a></li>
                    <?php endforeach; ?>
					
                </ul>
				<div class="clearfix"></div>
            </div>
			
            <!-- /content -->
        </div>

                <?
        		$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);

        		if ($serGoods) {

        			shuffle($serGoods);

        		?>

                <div class="span2">
                    <h2 class="poht" style="margin-top: 0; margin-bottom: 20px;">
        				<?=$ui->item('A_NEW_VIEWD_ITEMS')?>:
        			</h2>


        			<div class="you_view">

        				<ul>

        					<?
        						$i = 1;
        						foreach ($serGoods as $goods) {

        							if ($i > 5) break;

        							$ex = explode('_', $goods);

        							$good_id = $ex[0];
        							$good_entity = $ex[1];

        							if ($good_id == $item['id']) continue;

        							$igoods = Product::GetBaseProductInfo($good_entity, $good_id);

        							$price = DiscountManager::GetPrice(Yii::app()->user->id, $igoods);

        							//var_dump($igoods);
        							$i++;
        							?>

        								<li>
        									<div class="span1 photo new">
        										<?php $url = ProductHelper::CreateUrl($igoods); ?>

            <a href="<?=$url; ?>" title="<?=ProductHelper::GetTitle($igoods, 'title', 30); ?>"><img src="<?=Picture::Get($igoods, Picture::SMALL); ?>" alt="<?=ProductHelper::GetTitle($igoods, 'title', 30); ?>" /></a>

        									</div>
        									<div class="span2 text">
        										<div class="title"><a href="<?=$url; ?>"><?=ProductHelper::GetTitle($igoods, 'title', 30); ?></a></div>
        										<div class="cost"><?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
                    <span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;">
                        <?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]); ?>
                    </span>&nbsp;<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold;">
                        <?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?>

                    </span>

                <?php else : ?>

                    <span class="price">
               <?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?>

                </span>

                <?php endif; ?></div>
        										<div class="nds"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]); ?> <?=$ui->item('WITHOUT_VAT'); ?></div>
        									</div>
        									<div class="clearfix"></div>
        								</li>

        							<?

        						}
        					?>


        				</ul>

        			</div>

                </div>
                <? } ?>



        </div>
        </div>

<script type="text/javascript">

    $(document).ready(function()
    {
        $('#al').autocolumnlist({ columns: 3});
    });

</script>