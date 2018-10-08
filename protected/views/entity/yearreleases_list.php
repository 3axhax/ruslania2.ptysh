<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<?php if (!empty($list)): ?>
<div class="container view_product">
	<div class="row">
        <div class="span10">
            <h1 class="titlename poht" style="margin-bottom: 20px;"><?php
                $breadcrumbs = $this->breadcrumbs;
                $h1 = array_pop($breadcrumbs);
                unset($breadcrumbs) ;
                $h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
                ?><?= $h1 ?></h1>
            <div class="text">
                <ul class="list" id="al">
                    <?php foreach($list as $year): $year = (int) $year;
 	                    $url = Yii::app()->createUrl('/entity/byyearrelease', array('entity' => Entity::GetUrlKey($entity), 'year' => $year)); ?>
                        <li style="margin-bottom: 10px; margin-right: 50px; width: 100px; float: left;"><a href="<?= $url ?>" title="<?= $year ?>"><?= $year ?></a></li>
                    <?php endforeach; ?>

                </ul>
				<div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
