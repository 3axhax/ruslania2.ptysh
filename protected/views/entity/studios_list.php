<?php /*Created by Кирилл (22.10.2018 20:35)*/
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<?php if (!empty($list)): ?>
<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?php
				$breadcrumbs = $this->breadcrumbs;
				$h1 = Seo_settings::get()->getH1();
				if (empty($h1)):
					$h1 = array_pop($breadcrumbs);
					unset($breadcrumbs) ;
					$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
					if (($page = (int) Yii::app()->getRequest()->getParam('page')) > 1) $h1 .= ' &ndash; ' . Yii::app()->ui->item('PAGES_N', $page);
				endif;
				?><?= $h1 ?></h1>
			<div class="text">
				<ul class="list" id="al">
					<?php foreach($list as $item) :
						$url = Yii::app()->createUrl('/entity/bystudio', array('entity' => Entity::GetUrlKey($entity), 'sid' => $item['id'], 'title' => ProductHelper::ToAscii($item['title']))); ?>
						<li style="margin-bottom: 10px; margin-right: 20px; width: 220px; float: left;"><a href="<?= $url ?>" title="<?= htmlspecialchars($item['title']) ?>"><?= $item['title'] ?></a></li>
					<?php endforeach; ?>

				</ul>
				<div class="clearfix"></div>
				<?php if ($paginatorInfo) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

	$(document).ready(function() {
		$('#al').autocolumnlist({ columns: 3});
	});

</script>
<?php endif; ?>
