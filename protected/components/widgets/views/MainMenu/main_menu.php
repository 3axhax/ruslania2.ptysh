<?php /*Created by Кирилл (05.09.2018 19:28)*/
$ui = Yii::app()->ui;
?>
<div class="index_menu">

	<div class="container">
		<ul>

			<!--Книги-->
			<li class="dd_box"><?php $widget->viewBooks(); ?></li>

			<!--Ноты-->
			<li class="dd_box"><?php $widget->viewSheetmusic(); ?></li>

			<!--Музыка-->
			<li class="dd_box"><?php $widget->viewMusic(); ?></li>

			<!--Подписка-->
			<li class="dd_box"><?php $widget->viewPeriodics(); ?></li>

			<!--Ещё-->
			<li class="dd_box more_menu"><div class="click_arrow"></div>
				<a href="javascript:;" class="dd"><?= $ui->item('A_NEW_MORE'); ?></a>
				<div class="dd_box_bg dd_box_horizontal">

					<div class="tabs">
						<ul>

							<!--Сувениры-->
							<li class="dd_box"><?php $widget->viewSuvenirs(); ?></li>

							<!--Видео-->
							<li class="dd_box"><?php $widget->viewVideo(); ?></li>

							<!--Карты-->
							<li class="dd_box"><?php $widget->viewMaps(); ?></li>

							<!--Мультимедиа-->
							<li class="dd_box"><?php $widget->viewSoft(); ?></li>

							<!--Прочее-->
							<li class="dd_box"><?php $widget->viewPrinted(); ?></li>
						</ul>
						<div style="clear: both"></div>
					</div>
				</div>
			</li>
			<li class="yellow_item"><a href="<?= Yii::app()->createUrl('offers/special', array('mode' => 'alle2')); ?>"><?= $ui->item('A_NEW_GOODS_2'); ?></a></li>
			<li class="red_item"><a href="<?= Yii::app()->createUrl('offers/list'); ?>"><?= $ui->item('A_NEW_MENU_REK'); ?></a></li>
			<li class="red_item"><a href="<?= Yii::app()->createUrl('site/sale'); ?>"><?= $ui->item('A_NEW_DISCONT'); ?></a></li>
			<li><a href="<?= Yii::app()->createUrl('site/static', array('page'=>'ourstore')); ?>" class="home"><?= $ui->item('A_NEW_OURSTORE'); ?></a></li>
		</ul>
	</div>

</div>
<script type="text/javascript">
</script>