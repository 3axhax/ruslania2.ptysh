<?php /*Created by Кирилл (31.10.2018 22:54)*/ ?>
<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
$breadcrumbs = $this->breadcrumbs;
$h1 = array_pop($breadcrumbs);
unset($breadcrumbs) ;
$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
?>

<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?= $h1 ?></h1>
			<div class="text">
			</div>
		</div>
	</div>
</div>
