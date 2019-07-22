<?php /*Created by Кирилл (19.07.2019 22:39)*/ ?>
<?php
foreach($groups as $group):
	if (!empty($group['items'])):
	$entity = $group['entity'];
	$eUrl = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
	$eName = Entity::GetTitle($entity);
	if ($entity == Entity::PERIODIC) $eName = Yii::app()->ui->item('PEREODIC_NAME');
?>

<div class="news_box news_box_index nb<?= $entity ?>">
	<div class="container">
		<div class="title">
			<?= Yii::app()->ui->item("A_NEW_RECOMMENDATIONS_CATEGORY")?>:
			<a href="<?= $eUrl; ?>" id="enity<?= $entity ?>"><span class="title__bold"><?= $eName; ?></span></a>
			<div class="pult">
				<a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_left.slick-arrow').click()" class="btn_left"><span class="fa"></span><?php /*<img src="/new_img/btn_left_news.png" alt=""/> */ ?></a>
				<a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_right.slick-arrow').click()" class="btn_right"><span class="fa"></span><?php /*<img src="/new_img/btn_right_news.png" alt=""/> */ ?></a>
			</div>

		</div>
	</div>
	<div class="container cnt<?= $entity ?>">
		<ul class="books">
			<?php foreach ($group['items'] as $item) :
				$item['entity'] = $group['entity'];
				?>
				<li><?php
					$widget->viewItem($item);
				?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		scriptLoader('/new_js/slick.js').callFunction(function(){
			var $slickBlock = $('.cnt<?= $entity ?> ul');
			var costHeight = 0;
			$slickBlock.find('.cost').each(function(id, el) {
				costHeight = Math.max(costHeight, $(el).outerHeight());
			});
			$slickBlock.find('.cost').css({height: costHeight + 'px'});
			$slickBlock.slick({
				lazyLoad: 'ondemand',
				slidesToShow: 5,
				slidesToScroll: 5
			}).on('lazyLoadError', function(event, slick, image, imageSource){
				image.attr('src', '<?= Picture::srcNoPhoto() ?>');
			});
		});
	});
</script>
<?php endif; endforeach; ?>