<?php /*Created by Кирилл (15.08.2018 18:59)*/ ?>
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container content_books">
	<div class="row">
		<div class="listgoods span10">
			<?= Yii::app()->ui->item('MSG_SEARCH_ERROR_NOTHING_FOUND'); ?>
		</div>
		<div class="span2">
			<?php $this->widget('YouView', array()); ?>
		</div>
	</div>
</div>