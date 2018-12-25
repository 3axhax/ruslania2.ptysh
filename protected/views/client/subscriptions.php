<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="opacity"
     onclick="$('.history_subs_box .table_box').html('');$('.history_subs_box, .opacity').hide(); "></div>
<div class="history_subs_box">

	<div class="close" onclick="$('.history_subs_box .table_box').html('');$('.history_subs_box, .opacity').hide(); ">
		<img src="/new_img/close_history.png" alt=""/></div>
	<div class="title"><?= Yii::app()->ui->item('A_NEW_SUBS_POP_TITLE') ?>:</div>

	<div class="table_box">


	</div>

</div>

<div class="container cabinet">

	<div class="row">
		<div class="span10" id="cart" style="float: right;">
			<h2 class="cattitle me_left"
			    style="margin-bottom: 25px;"><?= Yii::app()->ui->item('A_NEW_SUBS_MENU_TTILE') ?></h2>


			<!-- content -->
			<div>
				<table width="100%" cellspacing="1" cellpadding="5" border="0" class="cart1 items_tbl"
				       style="margin-bottom: 10px; margin-top: 15px;">
					<thead>
					<tr>
						<th valign="middle"
						    class="cart1header1"><?= $ui->item("CART_COL_TITLE"); ?></th>
						<th valign="middle" align="center" style="width:70px;"
						    class="cart1header1" nowrap><?= $ui->item("A_NEW_SUBS_TD1_TITLE"); ?></th>
						<th valign="middle" align="center" style="width:70px;"
						    class="cart1header1" nowrap><?= $ui->item("A_NEW_SUBS_TD2_TITLE"); ?></th>
						<th valign="middle" align="center" style="width:185px;"
						    class="cart1header1" nowrap><?= $ui->item("A_NEW_SUBS_TD3_TITLE"); ?></th>
						<th valign="middle" align="center" style="width:70px;"
						    class="cart1header1" nowrap><?= $ui->item("A_NEW_SUBS_TD4_TITLE"); ?></th>
					</tr>
					</thead>
					<tbody class="items">

					<? foreach ($rows as $k => $row) :

						$month = array(
							'',
							$ui->item("A_NEW_SUBS_MONTH1"),
							$ui->item("A_NEW_SUBS_MONTH2"),
							$ui->item("A_NEW_SUBS_MONTH3"),
							$ui->item("A_NEW_SUBS_MONTH4"),
							$ui->item("A_NEW_SUBS_MONTH5"),
							$ui->item("A_NEW_SUBS_MONTH6"),
							$ui->item("A_NEW_SUBS_MONTH7"),
							$ui->item("A_NEW_SUBS_MONTH8"),
							$ui->item("A_NEW_SUBS_MONTH9"),
							$ui->item("A_NEW_SUBS_MONTH10"),
							$ui->item("A_NEW_SUBS_MONTH11"),
							$ui->item("A_NEW_SUBS_MONTH12")

						);

						?>

						<tr>
							<td valign="middle" class="cart1contents1">
								<table>
									<tr>
										<td>
											<span class="entity_icons"><i class="fa e30"></i></span>
										</td>
										<td style="padding-left: 20px;">
											<?

											$sql = 'SELECT * FROM `pereodics_catalog` WHERE id=' . $row['periodic_id'];

											$rows = Yii::app()->db->createCommand($sql)->queryRow();


											$rows['entity'] = 30;

											$url = ProductHelper::CreateUrl($rows);
											$title = ProductHelper::GetTitle($rows);


											echo '<a
                                    href="' . $url . '"
                                    class="maintxt1">' . $title . '</a>';

											?>


										</td>
									</tr>
								</table>
							</td>
							<td valign="middle" nowrap="true" align="center" class="cart1contents1 center">

								<?= date('d ' . $month[date('n', strtotime($row['subscription_start']))] . ' Y', strtotime($row['subscription_start'])) ?>

							</td>
							<td valign="middle" align="center" class="cart1contents1" nowrap>
								<?= date('d ' . $month[date('n', strtotime($row['subscription_end']))] . ' Y', strtotime($row['subscription_end'])) ?>
							</td>
							<td valign="middle" nowrap="" align="center" class="cart1contents1" nowrap>

								<?
								$sql = 'SELECT * FROM `subscriptions_sentlog` WHERE econet_id=' . $row['customer_id'] . ' AND periodic_id=' . $row['periodic_id'] . ' ORDER BY id DESC';

								$rowc = Yii::app()->db->createCommand($sql)->queryRow();

								if (!$rowc) {
									echo Yii::app()->ui->item('A_NEW_SUBS_NOTFOUND');
								} else {

									echo '№' . $rowc['number'] . ' от ' . date('d ' . $month[date('n', strtotime($rowc['sent_date']))] . ' Y H:i:s', strtotime($rowc['sent_date']));

								}
								?>


							</td>
							<td valign="middle" align="center" class="cart1contents1">
								<a href="javascript:;"
								   onclick="show_subs(<?= $row['customer_id'] ?>, <?= $row['periodic_id'] ?>,'<?= $row['address'] ?> <?= $row['postal_code'] ?>')"><img
										src="/new_img/actions_subs.png"/></a>
							</td>

						</tr>

					<? endforeach; ?>

					</tbody>

				</table>
			</div>
		</div>
		<div class="span2">

			<?php $this->renderPartial('/site/_me_left'); ?>

		</div>
		<!-- /content -->
	</div>
</div>
<script type="text/javascript">
	function show_subs(uid, sid, subsid) {
		var csrf = $('meta[name=csrf]').attr('content').split('=');

		$.post('<?=Yii::app()->createUrl('site/loadhistorysubs')?>', {uid: uid, sid: sid, YII_CSRF_TOKEN: csrf[1], subsid: subsid}, function (data) {

			$('.history_subs_box').css('top', $(window).scrollTop() + 50);

			$('.history_subs_box .table_box').html(data);

			$('.history_subs_box, .opacity').show();

		});

	}
</script>