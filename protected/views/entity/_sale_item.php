<div class="news_box nb<?= $entity ?> sale_item">
    <div class="container">
        <div class="title">
            <a href="<?=$link?>"><?= $title?></a></span>
            <div class="pult">
                <a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_left.slick-arrow').click()" class="btn_left"><img src="/new_img/btn_left_news.png" alt=""/></a>
                <a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_right.slick-arrow').click()" class="btn_right"><img src="/new_img/btn_right_news.png" alt=""/></a>
            </div>

        </div>
    </div>
    <div class="container cnt<?= $entity ?>">
        <ul class="books">
            <?php
            foreach ($items as $item) :
            $item['entity'] = $entity;
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
                                $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/4;
                                $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/4;
                                $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/4;
                            }
                            elseif (!empty($issues['show6Months'])) {
                                $item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                                $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/2;
                                $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/2;
                                $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/2;
                            }
                            else {
                                $item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
                            }
                        }
                        ?>
                        <?php $item['status'] = Product::GetStatusProduct($entity, $item['id']);?>
                        <?php $this->renderPartial('/entity/_render_index_item', array('item' => $item, 'entity' => $entity)); ?>
                    </li>

    <?php endforeach; ?>
        </ul>
    </div>
</div>
<style>
    .slick-slide.slick-active {
        width: 199px !important;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        scriptLoader('/new_js/slick.js').callFunction(function() {
            $('.cnt<?= $entity ?> ul').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 4,
                slidesToScroll: 4
            });
        });
    });
</script>