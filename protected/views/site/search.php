            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container content_books">
<div class="row">

		<div class="listgoods span10">
		
            <?php if (!empty($items)) : ?>
                <p><?=$ui->item('DID_YOU_MEAN'); ?></p>
                <ul class="items">
                    <?php foreach ($items as $i) : ?>
                        <li>
                            <a href="<?= $i['url']; ?>"><?= $i['title']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>


            <div class="text" style="margin-top: 7px;">
                <?= sprintf($ui->item("X items found"), $paginatorInfo->getItemCount()); ?>
				
				<?
				if ($paginatorInfo->getItemCount() == 0) {
				$arr = json_decode(file_get_contents('http://speller.yandex.net/services/spellservice.json/checkText?text='.urlencode($_GET['q'])));
?><?

function textswitch ($text)
{
	$str_search = array("q","w","e","r","t","y","u","i","o","p","[","]",
   "a","s","d","f","g","h","j","k","l",";","'",
   "z","x","c","v","b","n","m",",",".","Q","W","E","R","T","Y","U","I","O","P","[","]",
   "A","S","D","F","G","H","J","K","L",";","'",
   "Z","X","C","V","B","N","M",",","."
   
   );
	$str_replace = array(
   "й","ц","у","к","е","н","г","ш","щ","з","х","ъ",
   "ф","ы","в","а","п","р","о","л","д","ж","э",
   "я","ч","с","м","и","т","ь","б","ю","Й","Ц","У","К","Е","Н","Г","Ш","Щ","З","Х","Ъ",
   "Ф","Ы","В","А","П","Р","О","Л","Д","Ж","Э",
   "Я","Ч","С","М","И","Т","Ь","Б","Ю"
   );
   return str_replace($str_search, $str_replace, $text);
}

$arr2 = json_decode(file_get_contents('http://speller.yandex.net/services/spellservice.json/checkText?text='.urlencode(textswitch($_GET['q']))));

if ($arr[0]->s) {
	?><br /><br />Возможно вы имели в виду:<br /><?
	foreach($arr[0]->s as $k) {
		
		$sql = 'SELECT * FROM `all_authorslist` WHERE title_'.Yii::app()->language. ' LIKE "%'.$k.'%" LIMIT 1';
		
		$res = Yii::app()->db->createCommand($sql)->queryAll();
		
		if (!count($res)) continue;
		
		echo '<div style="margin: 10px 0;"><a href="/site/search/?q='.$k.'&avail='.$_GET['avail'].'">' . $k . '</a></div>';
	}
} elseif (textswitch($_GET['q']) != $_GET['q']) {
	?><br /><br />Возможно вы имели в виду:<br />
	
	<? echo '<div style="margin: 10px 0;"><a href="/site/search/?q='.textswitch($_GET['q']).'&avail='.$_GET['avail'].'">' . textswitch($_GET['q']) . '</a></div>';?>
	
	<? if ($arr2[0]->s) {
	foreach($arr2[0]->s as $k) {
		
		$sql = 'SELECT * FROM `all_authorslist` WHERE title_'.Yii::app()->language. ' LIKE "%'.$k.'%" LIMIT 1';
		
		$res = Yii::app()->db->createCommand($sql)->queryAll();
		
		if (!count($res)) continue;
		
		echo '<div style="margin: 10px 0;"><a href="/site/search/?q='.$k.'&avail='.$_GET['avail'].'">' . $k . '</a></div>';
	}
	}
}
				}
?>
				
				
            </div>
            <ul class="items">
                <?php foreach ($products as $i) : ?>
                    <li>
						<?php $i['status'] = Product::GetStatusProduct($i['entity'], $i['id'])?>
                        <?php $this->renderPartial('/entity/_common_item_2', array('item' => $i,
                                                                                 'isList' => true,
                                                                                 'entity' => $i['entity'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (count($products) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>

        </div>

	<?
$serGoods = unserialize(Yii::app()->getRequest()->cookies['yourView']->value);

if ($serGoods) {

shuffle($serGoods);

?>

 <div class="span2">
     <h2 class="poht" style="margin-top: 0; margin-bottom: 20px;"><?= $ui->item('A_NEW_VIEWD_ITEMS'); ?>:</h2>


<div class="you_view">

<ul>

<?
$i = 1;
foreach ($serGoods as $goods) {

if ($i > 5) break;

$ex = explode('_', $goods);

$good_id = $ex[0];
$good_entity = $ex[1];

if ($good_id == $item['id']) continue;

$igoods = Product::GetBaseProductInfo($good_entity, $good_id);

$price = DiscountManager::GetPrice(Yii::app()->user->id, $igoods);

//var_dump($igoods);
$i++;
?>

	<li>
		<div class="span1 photo new">
			<?php $url = ProductHelper::CreateUrl($igoods); ?>

<a href="<?=$url; ?>"><img src="<?=Picture::Get($igoods, Picture::SMALL); ?>" alt="" /></a>

		</div>
		<div class="span2 text">
			<div class="title"><a href="<?=$url; ?>"><?=ProductHelper::GetTitle($igoods, 'title', 30); ?></a></div>
			<div class="cost"><?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
     <span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;">
         <?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]); ?>
     </span>&nbsp;<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold;">
         <?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?>

     </span>

 <?php else : ?>

     <span class="price">
<?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]); ?>

 </span>

 <?php endif; ?></div>
			<div class="nds"><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]); ?> <?=$ui->item('WITHOUT_VAT'); ?></div>
		</div>
		<div class="clearfix"></div>
	</li>

<?

}
?>


</ul>

</div>

 </div>
 <? } ?>


        </div>
        </div>
