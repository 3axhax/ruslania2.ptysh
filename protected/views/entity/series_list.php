

  <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<script src="/js/jquery.autocolumnlist.js"></script>

<div class="container cabinet">

<h1 class="titlename"><?php
    $breadcrumbs = $this->breadcrumbs;
    $h1 = Seo_settings::get()->getH1();
    if (empty($h1)):
        $h1 = array_pop($breadcrumbs);
        unset($breadcrumbs) ;
        $h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
        if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1) $h1 .= ' &ndash; ' . Yii::app()->ui->item('PAGES_N', $page);
    endif;
?><?= $h1 ?></h1>
            <div class="text">
                <ul class="list" id="al50">
                    <?php foreach($list as $item) : ?>
                        <?php $title = ProductHelper::GetTitle($item); ?>
                        <li style="margin-bottom: 10px;">
                            <a href="<?=Yii::app()->createUrl('entity/byseries',
                                array('entity' => Entity::GetUrlKey($entity),
                                      'sid' => $item['id'],
                                      'title' => ProductHelper::ToAscii($title)
                                 )); ?>" title="<?=$title;?>"><?=$title;?></a></li>
                    <?php endforeach; ?>
                </ul>
                <div class="clearfix"></div>
                <?php if ($paginatorInfo) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
            </div>

            <!-- /content -->
        </div>

<script type="text/javascript">

    $(document).ready(function()
    {
        $('#al50').autocolumnlist({ columns: 3});
    });

</script>
