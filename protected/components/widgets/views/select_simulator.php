<?php
/*Created by Кирилл (16.05.2018 21:42)
/**
 * @var $items array массив данных [ид=>название, ид=>название, ...]
 * @var $selected int ид уже выбранного
 * @var $href string
 * @var $paramName string параметр для пути
 * @var $dataParam array массив параметров для пути
*/
?>
<div class="select_simulator"<?php if (!empty($style)): ?> style="<?= $style ?>" <?php endif; ?>>
     <ul class="ss_select">
 <?php foreach ($items as $id=>$name):
     if ($selected !== $id):
         if (empty($id)) unset($dataParam[$paramName]);
         else $dataParam[$paramName] = $id;
     //$href = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cid));
     $href = Yii::app()->createUrl($route, $dataParam);
 ?>
         <li>
             <a href="<?= $href ?>"><?=$name?></a>
         </li>
 <?php endif; endforeach; ?>
    </ul>
	<div class="ss_selected" title="<?= htmlspecialchars($items[$selected]) ?>"><?= $items[$selected] ?></div>
</div>
