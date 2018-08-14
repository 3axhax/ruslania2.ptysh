<?php /*Created by Кирилл (21.06.2018 18:42)*/ ?>
<?php $dataForSearch = array('q'=>$q,'avail'=>$this->GetAvail(1));
foreach ($abstractInfo as $eNum=>$counts):
	$dataForSearch['e'] = $eNum; ?>
    <div class="row_category">
	    <?= $q ?>
	    <span><?= $ui->item('A_NEW_SEARCH_IN_CAT') ?> <?= Entity::GetTitle($eNum) ?></span>
	    <a href="<?= Yii::app()->createUrl('search/general', $dataForSearch) ?>" class="result_search_count"><?= $counts ?></a>
    </div>
<?php endforeach; ?>