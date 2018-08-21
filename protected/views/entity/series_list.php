

  <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<script src="/js/jquery.autocolumnlist.js"></script>

<div class="container cabinet">
          

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
            </div>

            <!-- /content -->
        </div>

<script type="text/javascript">

    $(document).ready(function()
    {
        $('#al50').autocolumnlist({ columns: 3});
    });

</script>
