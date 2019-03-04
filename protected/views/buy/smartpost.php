<?php /*Created by Кирилл (03.03.2019 22:24)*/ ?>
<div class="clearfix"></div>
<div class="select_dd_box" style="margin: 20px 0; margin-left: 20px;">
	<div class="select_dd">
		<div class="p2" style="font-size: 16px; font-weight: bold;margin-bottom: 5px; display: inline-block"><?= $ui->item('CARTNEW_SMARTPOST_TITLE') ?></div>
		<div onclick="$('.info_box_smart').toggle().css('top', '6px');" class="qbtn2"> ? </div>
		<div style="background-color: #fff; position: absolute; display: none; padding: 20px; width: 580px; right: 0; z-index: 99999; border-radius: 2px; box-shadow: 0 0 10px rgba(0,0,0,0.3)" class="info_box info_box_smart"><?= $ui->item('SMARTPOST_DESC') ?></div>
		<div></div>
		<br/>
		<?= $ui->item('CARTNEW_SMARTPOST_SUBTITLE') ?>
		<div style="height: 10px;"></div>
		<input class="smartpost_index" type="text"
		       placeholder="<? $ui->item('CARTNEW_SMARTPOST_INPUT_INDEX_PLACEHOLDER') ?>" style="margin: 0;"
		       onclick="$('input[name=dtid]').slice(0,1).attr('checked', 'true')"
		       onkeyup="if (event.keyCode==13) { search_smartpost('<?= Yii::app()->createUrl('buy/loadsp') ?>', '<?= $countryId ?>', '<?= $ui->item('CARTNEW_SEARCH_PROCESS_SMARTPOST') ?>', '<?= $ui->item('BTN_SEARCH_ALT') ?>') }">
		<a href="javascript:;" class="btn btn-success start-search-smartpost" style="margin-left: 10px;" onclick="search_smartpost('<?= Yii::app()->createUrl('buy/loadsp') ?>', '<?= $countryId ?>', '<?= $ui->item('CARTNEW_SEARCH_PROCESS_SMARTPOST') ?>', '<?= $ui->item('BTN_SEARCH_ALT') ?>')"><?= $ui->item('BTN_SEARCH_ALT') ?></a>
	</div>
	<div class="box_smartpost"></div>
	<input class="sel_smartpost" id="pickpoint_address" type="hidden" value=""/>
</div>