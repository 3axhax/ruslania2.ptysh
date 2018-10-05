<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="row">
    <div class="container">
<h1><?=$ui->item('A_NEW_PERIODIC_FOR_GIFT'); ?></h1>
<hr>
        <?=$ui->item('A_NEW_PERIODIC_FOR_GIFT_TEXT');?>
<hr>
<?php $eUrl = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity))); ?>
<?php $cUrl = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cid)); ?>
        <div class="news_box nb<?= $entity ?>">
            <div class="container">
                <div class="title">
                    <?= $ui->item("A_NEW_RECOMMENDATIONS_CATEGORY") ?> <a href="<?= $eUrl; ?>" id="enity<?= $entity ?>"><span class="title__bold"><?= Entity::GetTitle($entity); ?></span></a>
                    <div class="pult">
                        <a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_left.slick-arrow').click()" class="btn_left"><img src="/new_img/btn_left_news.png" alt=""/></a>
                        <a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_right.slick-arrow').click()" class="btn_right"><img src="/new_img/btn_right_news.png" alt=""/></a>
                    </div>

                </div>
            </div>

            <script type="text/javascript">
                $(document).ready(function () {
                    $('.cnt<?= $entity ?> ul').slick({
                        lazyLoad: 'ondemand',
                        slidesToShow: 5,
                        slidesToScroll: 5
                    });
                });
            </script>

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
                            <?php $this->renderPartial('/entity/_render_index_item', array('item' => $item, 'entity' => $entity)); ?>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="news_box nb67">
            <div class="container">
                <div class="title">
                    <?= Entity::GetTitle($entity); ?>:
                        <a href="<?= $cUrl; ?>" id="enity<?= $entity ?>">
                            <span class="title__bold">
                                <?= $title_cat; ?>
                            </span>
                        </a>
                    <div class="pult">
                        <a href="javascript:;" onclick="$('.nb67 .btn_left.slick-arrow').click()" class="btn_left"><img src="/new_img/btn_left_news.png" alt=""/></a>
                        <a href="javascript:;" onclick="$('.nb67 .btn_right.slick-arrow').click()" class="btn_right"><img src="/new_img/btn_right_news.png" alt=""/></a>
                    </div>

                </div>
            </div>

            <script type="text/javascript">
                $(document).ready(function () {
                    $('.cnt67 ul').slick({
                        lazyLoad: 'ondemand',
                        slidesToShow: 5,
                        slidesToScroll: 5
                    });
                });
            </script>

            <div class="container cnt67">
                <ul class="books">!!!
                    <?php
                    foreach ($popular as $item) :
                        ?>
                        <li>
                            <?php $item['status'] = Product::GetStatusProduct($entity, $item['id']);?>
                            <?php $item['entity'] = $entity;?>
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
                            <?php $this->renderPartial('/entity/_render_index_item', array('item' => $item, 'entity' => $entity)); ?>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>