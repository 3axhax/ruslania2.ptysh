<?php
/*Created by Кирилл (16.05.2018 21:42)
/**
 * @var $items array массив данных [ид=>название, ид=>название, ...]
 * @var $selected int ид уже выбранного
 * @var $href string
 * @var $paramName string параметр для пути
 * @var $dataParam array массив параметров для пути
*/
/*if (!isset($_GET['ha'])): ?>
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
<?php else: */?>
<div class="select_simulator"<?php if (!empty($style)): ?> style="<?= $style ?>" <?php endif; ?>>
    <select name="<?= $paramName ?>" style="width: 100%;" onchange="if (this.options[this.selectedIndex].getAttribute('url')) window.location.href=this.options[this.selectedIndex].getAttribute('url');">
    <?php foreach ($items as $id=>$name):
        $href = '';
        $selectedStr = ' selected';
        if ($selected !== $id) {
            if (empty($id)) unset($dataParam[$paramName]);
            else $dataParam[$paramName] = $id;
            $href = Yii::app()->createUrl($route, $dataParam);
            $selectedStr = '';
        }
        ?>
        <option url="<?= $href ?>" value="<?= $id ?>"<?= $selectedStr ?>><?=$name?></option>
    <?php endforeach; ?>
    </select>
</div>
<script type="text/javascript">
    scriptLoader('/new_js/modules/select2.full.js').callFunction(function(){
        function format_option_for_select2(state) {
            if (state.element) {
                if (!state.element.getAttribute('url')) return state.text; // optgroup
                return $('<a href="' + state.element.getAttribute('url') + '">' + state.text + '</a>');
            }
            return state.text;
        }
        $('div.select_simulator select')
            .select2({
                templateResult: format_option_for_select2,
                minimumResultsForSearch: Infinity
            });
    });
</script>
<?php //endif; ?>