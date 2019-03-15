<?php /*Created by Кирилл (15.03.2019 17:46)*/ ?>
<link rel="stylesheet" href="/new_style/font-awesome/css/font-awesome.min.css">
<style media="all">
	.fa {
		display: inline-block;
		font: normal normal normal 14px/1 FontAwesome;
		font-size: inherit;
		text-rendering: auto;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
	span.instagram:before {
		content: '\f16d';
		color: #fff;
		font-size: 30px;
	}
	body {
		margin: 0;
		padding: 2px;
		font-family: 'Open Sans', sans-serif;
		font-size: 14px;
		box-sizing: border-box;
	}

	*, *:before, *:after {
		box-sizing: inherit;
	}

	.widget-preview-wrapper {
		display: inline-block;
	}

	.widget-preview {
		overflow: hidden;
		position: relative;
		border-radius: 5px;
		border: 1px solid #c3c3c3;
		box-shadow: 0 0 5px #c3c3c3;
		background-color: rgba(255,255,255,1);
	}

	.widget-copyright a {
		text-decoration: none;
		padding: 0;
		font-size: 12px;
		color: #3c3c3c;
	}

	.widget-preview__title {
		position: relative;
		padding: 5px 0 5px 20px;
		background-color: #46729b;
		overflow: hidden;
	}

	.widget-preview__title a {
		display: block;
		width: 100%;
		/*height: 25px;*/
		color: #fff;
		font-size: 18px;
		text-decoration: none;
	}

	.widget-preview__title span {
		vertical-align: middle;
	}

	.widget-profile {
		width: 100%;
		border-collapse: collapse;
		text-align: center;
	}

	.widget-profile::after {
		display: block;
		content: '';
		clear: both;
	}

	.widget-profile td {
		border: 1px solid #c3c3c3;
	}

	.profile-image {
		padding: 8px;
		width: 80px;
		border-left: 0 !important;
	}

	.profile-image img {
		width: 60px;
		height: 60px;
	}

	.profile-title {
		background-color: #46729b;
		text-align: center;
		vertical-align: middle;
	}

	.profile-title a {
		color: #fff;
		font-size: 25px;
		text-decoration: none;
	}

	.profile-data span {
		display: block;
		font-size: 9px;
		font-weight: bold;
		color: #999;
	}

	.profile-subscribe a {
		display: inline-block;
		padding: 3px 15px;
		border: 3px solid #FFF;
		border-radius: 5px;
		box-shadow: 0 0px 2px rgba(0, 0, 0, 0.5);
		color: #fff;
		text-decoration: none;
		font-weight: bold;
		background-color: #ad4141;
	}

	.widget-body table {
		border-collapse: collapse;
		margin: 0 auto;
	}

	.widget-body tr {
		margin: 0;
		padding: 0;
	}

	.widget-body td {
		box-sizing: content-box;
		margin: 0;
		padding:  5px;
		font-size: 0;
		overflow: hidden;
		position: relative;
		width: 117px;
		height: 117px;
	}

	.widget-body img {
		position: absolute;
		top: -1000%;
		bottom: -1000%;
		margin: auto;
		display: block;
		padding: 5px;
		width: 117px;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);
		box-sizing: border-box;
	}

</style>
<div class="widget-preview-wrapper">
	<div class="widget-preview">
		<div class="widget-head">
			<div class="widget-preview__title">
				<a target="_blank" href="https://instagram.com/ruslaniabooks">
					<span class="fa instagram"></span>
					<span><?= $user['full_name'] ?></span>
				</a>
			</div>
			<table class="widget-profile">
				<tbody>
				<tr>
					<td rowspan="2" class="profile-image">
						<img src="<?= $user['profile_picture'] ?>">
					</td>
					<td id="widget-media" class="profile-data"><?= $user['counts']['media'] ?><span><?= Yii::app()->ui->item('INSTAGRAM_MEDIA') ?></span></td>
					<td id="widget-followers" class="profile-data"><?= $user['counts']['followed_by'] ?><span><?= Yii::app()->ui->item('INSTAGRAM_FOLLOWED_BY') ?></span></td>
					<td id="widget-following" class="profile-data"><?= $user['counts']['follows'] ?><span><?= Yii::app()->ui->item('INSTAGRAM_FOLLOWS') ?></span></td>
				</tr>
				<tr>
					<td colspan="3" class="profile-subscribe">
						<a target="_blank" href="https://instagram.com/ruslaniabooks">
							<?= Yii::app()->ui->item('INSTAGRAM_SUBSCRIBE') ?> ►
						</a>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
<?php /*
		<div class="widget-head">
			<table class="widget-profile">
				<tbody>
				<tr>
					<td class="profile-image">
						<img src="<?= $user['profile_picture'] ?>">
					</td>
					<td class="profile-title">
						<a target="_blank" href="https://instagram.com/ruslaniabooks">
							<span class="fa instagram"></span>
							<span><?= $user['full_name'] ?></span>
						</a>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
 */ ?>
		<div id="widget-table" class="widget-body">
			<table>
				<tbody>
				<?php for ($i=0;$i<3;$i++): ?>
				<tr>
					<?php for($j=0;$j<3;$j++): $num = $i*3+$j;
						if (!isset($images[$num])) break 2;
						?>
					<td>
						<a href="<?= $images[$num]['link'] ?>" target="_blank">
							<img src="<?= $images[$num]['images']['thumbnail']['url'] ?>">
						</a>
					</td>
					<?php endfor; ?>
				</tr>
				<?php endfor; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
