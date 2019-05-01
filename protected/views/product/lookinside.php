<?php /*Created by Кирилл (01.05.2019 21:29)*/ ?>
<div style="text-align: left;background-color: #fff; margin-top: -10px;">
	<?php
	$images = array();
	$audio = array();
	$pdf = array();
	$first = array('img'=>'', 'audio'=>'', 'pdf'=>'');
	foreach ($item['Lookinside'] as $li) {
		$ext = strtolower(pathinfo($li['resource_filename'], PATHINFO_EXTENSION));
		if ($ext == 'jpg' || $ext == 'gif' || $ext == 'png') {
			if (empty($first['img'])) $first['img'] = '/pictures/lookinside/' . $li['resource_filename'];
			$images[] = '/pictures/lookinside/' . $li['resource_filename'];
		}
		elseif ($ext == 'mp3') {
			if (empty($first['audio'])) $first['audio'] = $li['resource_filename'];
			$audio[] = $li['resource_filename'];
		}
		else {
			if (empty($first['pdf'])) $first['pdf'] = $li['resource_filename'];
			$pdf[] = $li['resource_filename'];
		}
	}
	$images = implode('|', $images);
	?>

	<?php if ($item['entity'] == Entity::AUDIO) : ?>
		<a href="javascript:;" style="width: 261px; margin-right: 30px;"  data-iid="<?= $item['id']; ?>" data-audio="<?= implode('|', $audio); ?>" class="read_book">Смотреть</a>

		<div id="audioprog<?= $item['id']; ?>" class="audioprogress">
			<img src="/pic1/isplaying.gif" class="lookinside audiostop"/><br/>
			<span id="audionow<?= $item['id']; ?>"></span> / <span id="audiototal<?= $item['id']; ?>"></span>

		</div>
		<div class="clearBoth"></div>


	<?php else : ?>

	<?php

	// var_dump($images);

	if ($images AND count($pdf)) {


	?>

	<link rel="stylesheet" href="/css/magnific-popup.css" >
		<script>

			function show_popup() {
				$.magnificPopup.open({
					items: {
						src: '#periodic-price-form2', // can be a HTML string, jQuery object, or CSS selector
						type: 'inline'
					}
				});
			}


		</script>
		<div id="periodic-price-form2" class="white-popup-block mfp-hide white-popup">
			<div class="box_title box_title_ru">Галерея страниц:</div>



			<a href="<?= CHtml::encode($first['img']); ?>" onclick="return false;"
			   data-iid="<?= $item['id']; ?>"
			   data-pdf="<?= CHtml::encode(implode('|', array())); ?>"
			   data-images="<?= CHtml::encode($images); ?>" style="width: 261px; margin: 20px auto; display: block; background: #edb421 none; padding-right: 0" class="read_book link__read">Смотреть галерею</a>

			<div class="box_title box_title_ru">Файлы:</div>

			<?php if (!empty($pdf)) : ?>
				<div id="staticfiles<?= $item['id']; ?>">
					<ul class="staticfile">
						<?php $pdfCounter = 1; ?>
						<?php foreach ($pdf as $file) : ?>
							<?php $file2 = '/pictures/lookinside/' . $file; ?>
							<li style="text-align: center; padding: 5px 0;">
								<a target="_blank" href="<?= $file2; ?>"><img
										src="/css/pdf.png"/> <?= $file; ?></a>
							</li>
							<?php $pdfCounter++; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

		</div>


		<a href="javascript:;" onclick="show_popup();"
		   data-iid="<?= $item['id']; ?>"
		   data-pdf=""
		   data-images="" style="width: 261px; margin-right: 30px;" target="_blank" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>

	<?php


	} else {


	if ( !$images AND !count($audio) AND count($pdf) == 1 ) :

	?>
	<?php $file2 = '/pictures/lookinside/' . $pdf[0]; ?>


		<a href="<?=$file2?>"
		   data-iid="<?= $item['id']; ?>"
		   data-pdf=""
		   data-images="" style="width: 261px; margin-right: 30px;" target="_blank" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>
	<?php

	elseif (!$images AND !count($audio) AND count($pdf) > 1) :
	?>



	<link rel="stylesheet" href="/css/magnific-popup.css" >
		<script>

			function show_popup2() {
				$.magnificPopup.open({
					items: {
						src: '#periodic-price-form3', // can be a HTML string, jQuery object, or CSS selector
						type: 'inline'
					}
				});
			}


		</script>
		<div id="periodic-price-form3" class="white-popup-block mfp-hide white-popup">


			<div class="box_title box_title_ru">Файлы:</div>

			<?php if (!empty($pdf)) : ?>
				<div id="staticfiles<?= $item['id']; ?>">
					<ul class="staticfile">
						<?php $pdfCounter = 1; ?>
						<?php foreach ($pdf as $file) : ?>
							<?php $file2 = '/pictures/lookinside/' . $file; ?>
							<li style="text-align: center; padding: 5px 0;">
								<a target="_blank" href="<?= $file2; ?>"><img
										src="/css/pdf.png"/> <?= $file; ?></a>
							</li>
							<?php $pdfCounter++; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

		</div>


		<a href="javascript:;" onclick="show_popup2();"
		   data-iid="<?= $item['id']; ?>"
		   data-pdf=""
		   data-images="" style="width: 261px; margin-right: 30px;" target="_blank" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>

	<?php

	elseif ($images AND !count($audio) AND !count($pdf)):

	?>

		<a href="<?= CHtml::encode($first['img']); ?>" onclick="return false;"
		   data-iid="<?= $item['id']; ?>"
		   data-pdf="<?= CHtml::encode(implode('|', array())); ?>"
		   data-images="<?= CHtml::encode($images); ?>" style="width: 261px; margin-right: 30px;" class="read_book link__read"><?=$ui->item('A_NEW_VIEW')?></a>

		<?php

	endif;




	}
		?>




	<?php endif; ?>
	<div class="clearBoth"></div>
	<div style="height: 20px;"></div>
</div>

