<?php /*Created by Кирилл (21.06.2018 21:37)*/ ?>
<div class="title_goods">
	<div class="red_checkbox" onclick="check_search($(this), 'js_avail'); $('#Search').marcoPolo('search');" style="float: right;">
		<span class="checkbox" style="height: 10px; padding-top: 2px;">
            <span class="check<?= ((bool) Yii::app()->getRequest()->getParam('avail', 1))?' active':'' ?>"></span>
        </span>
		<?= $ui->item('A_NEW_SEARCH_AVAIL'); ?>
	</div>
	<div><?=$ui->item('A_NEW_SEARCH_GOODS_TITLE'); ?></div>
</div>
