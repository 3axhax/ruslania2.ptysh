<?php
$eUrl = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
$eName = Entity::GetTitle($entity);
if ($entity == Entity::PERIODIC) $eName = $ui->item('PEREODIC_NAME');
?>

<div class="news_box news_box_index nb<?= $entity ?>">
    <div class="container">
        <div class="title">
            <?=$ui->item("A_NEW_RECOMMENDATIONS_CATEGORY")?>:
            <a href="<?= $eUrl; ?>" id="enity<?= $entity ?>"><span class="title__bold"><?= $eName; ?></span></a>
            <div class="pult">
                <a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_left.slick-arrow').click()" class="btn_left"><span class="fa"></span><?php /*<img src="/new_img/btn_left_news.png" alt=""/> */ ?></a>
                <a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_right.slick-arrow').click()" class="btn_right"><span class="fa"></span><?php /*<img src="/new_img/btn_right_news.png" alt=""/> */ ?></a>
            </div>

        </div>
    </div>
    <div class="container cnt<?= $entity ?>">
        <ul class="books">
            <?php
            foreach ($group as $item) :
                    ?>
                    <li>
                        <?php $item['status'] = Product::GetStatusProduct($entity, $item['id']);?>
                        <?php
                        $item['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
                        $item['priceData']['unit'] = '';
                        if ($entity == Entity::PERIODIC) {
                            $issues = Periodic::getCountIssues($item['issues_year']);
                            if (!empty($issues['show3Months'])) {
                                $item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
                                $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO_FIN]/4;
                                $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT_FIN]/4;
                                $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT_FIN]/4;
                            }
                            elseif (!empty($issues['show6Months'])) {
                                $item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                                $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO_FIN]/2;
                                $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT_FIN]/2;
                                $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT_FIN]/2;
                            }
                            else {
                                $item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
                                $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO_FIN];
                                $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT_FIN];
                                $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT_FIN];
                            }
                        }
                        ?>
                        <?php $this->renderPartial('/entity/_render_index_item', array('item' => $item, 'entity' => $entity)); ?>
                    </li>

    <?php endforeach; ?>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        scriptLoader('/new_js/slick.js').callFunction(function(){
            var $slickBlock = $('.cnt<?= $entity ?> ul');
            var costHeight = 0;
            $slickBlock.find('.cost').each(function(id, el) {
                costHeight = Math.max(costHeight, $(el).outerHeight());
            });
            $slickBlock.find('.cost').css({height: costHeight + 'px'});
            $slickBlock.slick({
                lazyLoad: 'ondemand',
                slidesToShow: 5,
                slidesToScroll: 5
            }).on('lazyLoadError', function(event, slick, image, imageSource){
                image.attr('src', '<?= Picture::srcNoPhoto() ?>');
            });
        });
    });
</script>
