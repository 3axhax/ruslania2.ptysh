<div id="advsearch"<?php if (!empty($isHide)): ?> style="display: none;" <?php endif; ?>>

    <?php KnockoutForm::RegisterScripts(); ?>
    <?= CHtml::beginForm(Yii::app()->createUrl('site/advsearch'), 'get'); ?>

	
    <?php
    $e = intVal(@$_GET['e']);
    $cid = intVal(@$_GET['cid']);
    $title = trim(@$_GET['title']);
    $author = trim(@$_GET['author']);
    $perf = trim(@$_GET['perf']);
    $publisher = trim(@$_GET['publisher']);
    $only = trim(@$_GET['only']);
    $lang = (int)trim(@$_GET['l']);
    $year = intVal(@$_GET['year']);
    if(empty($year) || $year <= 0) $year = '';
	$binding_id = intVal(@$_GET['binding_id'.$e]);
    $director = trim(@$_GET['director']);
    ?>

    <?php 
	
		$entityList = CHtml::listData(Entity::GetEntityListForSelect(), 'ID', 'Name'); 
	
	?>
    <table width="100%" class="advsearch text">
        <tr>
            <td><?= $ui->item('SECTION'); ?>:</td>
            <td><?= CHtml::dropDownList('e', $e, $entityList, array('data-bind' => 'value: Entity')); ?></td>
        </tr>

        <tr>
            <td><?= $ui->item('Related categories'); ?>:</td>
            <td>
                <select name="cid" data-bind="options: Categories, optionsText: 'Name', optionsCaption: '---', optionsValue: 'ID', value: CID"></select>
            </td>
        </tr>
		
		<?php
			foreach (Entity::GetEntitiesList() as $id => $val):
				//echo $id;
                $bindingList = 'bindingList' . $id;
                $$bindingList = CHtml::listData(ProductHelper::GetBindingListForSelect($id), 'ID', 'Name');
                if (count($$bindingList) == 0) continue;

//				eval('$bindingList'.$id.' = CHtml::listData(ProductHelper::GetBindingListForSelect('.$id.'), \'ID\', \'Name\');');
//			if (count(${'bindingList'.$id}) == 0) continue;

		?>
		<tr data-bind="visible: Entity()==<?= $id ?>">
            <td><?php switch ($id):
                    case Entity::BOOKS: case Entity::SHEETMUSIC: ?><?= $ui->item('A_NEW_FILTER_TYPE1') ?><?php break;
                    case Entity::MUSIC: ?><?= $ui->item('A_NEW_FILTER_TYPE3') ?><?php break;
                    case Entity::PERIODIC: ?><?= $ui->item('A_NEW_TYPE_IZD') ?><?php break;
                    default: ?><?= $ui->item('A_NEW_FILTER_TYPE2') ?><?php break;
            endswitch;?>:</td>
            <td><?= CHtml::dropDownList('binding_id'.$id, $binding_id, $$bindingList, array('empty' => '---', 'class'=>'select2_series')) ?></td>
        </tr>
		
		<?php endforeach; ?>

        <tr>
            <td><?= $ui->item('CART_COL_TITLE'); ?>:</td>
            <td><?= CHtml::textField('title', $title); ?></td>
        </tr>
        <tr data-bind="visible: Entity()==<?=Entity::BOOKS?> || Entity() == <?=Entity::MUSIC; ?> || Entity() == <?=Entity::SOFT; ?> || Entity() == <?=Entity::SHEETMUSIC; ?>">
            <td><?= $ui->item('Author'); ?>:</td>
            <td><?= CHtml::textField('author', $author); ?></td>
        </tr>
        <tr data-bind="visible: Entity()==<?=Entity::VIDEO?>">
            <td><?= $ui->item('Director'); ?>:</td>
            <td><?= CHtml::textField('director', $director); ?></td>
        </tr>
        <tr data-bind="visible: Entity()==<?=Entity::AUDIO?> || Entity() == <?=Entity::MUSIC; ?>">
            <td><?= $ui->item('Performer'); ?>:</td>
            <td><?= CHtml::textField('perf', $perf, array('data-bind' => 'enable: Entity()=='.Entity::AUDIO.' || Entity() == '.Entity::MUSIC)); ?></td>
        </tr>
		
		<tr data-bind="visible: Entity()==<?=Entity::VIDEO?>">
            <td><?= $ui->item('Actor'); ?>:</td>
            <td><?= CHtml::textField('perf', $perf, array('data-bind' => 'enable: Entity()=='.Entity::VIDEO)); ?></td>
        </tr>
		
        <tr data-bind="visible: Entity()!=<?=Entity::PERIODIC?>">
            <td><?= $ui->item('Published by'); ?>:</td>
            <td><?= CHtml::textField('publisher', $publisher); ?></td>
        </tr>
        <tr>
            <td data-bind="text: vm.getLangTitle()"><?=$ui->item('CATALOGINDEX_CHANGE_LANGUAGE'); ?>:</td>
            <?php $langList = CHtml::listData(Language::GetItemsLanguageList(), 'id', 'title_'.Yii::app()->language); ?>
            <td id="language_select">
                <select name="l" data-bind="options: Langs, optionsText: 'Name', optionsCaption: '---', optionsValue: 'ID', value: LANG"></select>
                <?/*=CHtml::dropDownList('l', $lang, $langList, array('empty' => '---', 'class'=>'select2_series')); */?>
            </td>
        </tr>
        <tr data-bind="visible: Entity()!=<?=Entity::PERIODIC?>">
            <td><?=trim(sprintf($ui->item('A_NEW_YEAR'), '')); ?>:</td>
            <td><?=CHtml::textField('year', $year); ?></td>
        </tr>
        <tr data-bind="visible: Entity()!=<?=Entity::PERIODIC?>">
            <td><?= $ui->item('A_NEW_SEARCH_AVAIL'); ?>:</td>
            <td class="red_checkbox" onclick="check_search($(this));" style="height: 42px;" data-bind="click: $root.changeCategory">
                <span class="checkbox">
                    <span class="check<?= $only?' active':'' ?>"></span>
                </span>
                <?= CHtml::hiddenField('only', $only, array('class'=>'avail')); ?>
                <?/*= CHtml::checkBox('only', $only);*/ ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" class="sort order_start" value="<?= $ui->item('BTN_CATALOG_SEARCH_SUBMIT'); ?>"/></td>
        </tr>
    </table>
    <?= CHtml::endForm(); ?>
</div>
<script type="text/javascript">

    var firstTime = true;
    var VM = function () {
        var self = this;
        self.Entity = ko.observable();
        self.Categories = ko.observableArray([]);
        self.Langs = ko.observableArray([]);
        self.CID = ko.observable();
        self.LANG = ko.observable();
        self.FirstLoad = ko.observable(false);
        self.only = document.getElementById('only');

        self.Entity.subscribe(function (e) {
            if (e > 0) {
                self.Categories.removeAll();
                self.CID(0);
                self.changeCategory();
            }
        });

        self.getLangTitle = function () {
            if (self.Entity() == <?= Entity::PRINTED ?>) {
                return '<?= $ui->item('CATALOGINDEX_CHANGE_THEME') . ':'; ?>';
            }
            return '<?= $ui->item('CATALOGINDEX_CHANGE_LANGUAGE') . ':'; ?>';
        };

        self.CID.subscribe(function (cid) {
            if (cid > 0) self.changeLangs(self.Entity(), cid);
        });

        self.changeCategory = function () {
            var only = self.only.value;
            var e = self.Entity();
            if (e == <?= Entity::PERIODIC ?>) only = 0;
            if (only == "") only = 0;

            $.getJSON('<?= Yii::app()->createUrl('site/categorylistjson') ?>', { e: e, only: only }, function (json) {
                ko.mapping.fromJS(json, {}, self.Categories);
                if (firstTime && <?=$cid; ?> > 0 && e == <?=$e; ?>) self.CID(<?=$cid; ?>);
                else self.changeLangs(e, self.CID());
                firstTime = false;
            });
        };

        self.changeLangs = function(eid, cid) {
            self.Langs.removeAll();
            self.LANG(0);
            $.getJSON('<?= Yii::app()->createUrl('site/langslistjson') ?>', { eid: eid, cid: cid }, function (json) {
                ko.mapping.fromJS(json, {}, self.Langs);
                if (firstTime && <?= $lang; ?> > 0) self.LANG(<?=$lang; ?>);
            });
        };

    };

    var vm = new VM();
    ko.applyBindings(vm, $('#advsearch')[0]);

    $(document).ready(function () {
        priorityLanguages = [7, 8, 14, 9];
        sortLanguages(priorityLanguages);
    });

    function sortLanguages(priority) {
        var select = $("#language_select select");
        var listOptions = select.children().get();
        listOptions.sort(function(a, b) {
            var compA = $(a).text().toUpperCase();
            var compB = $(b).text().toUpperCase();
            return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
        });
        select.append($("#language_select select option[value='']"));
        $.each(priority, function (idx, itm) {
            select.append($("#language_select select option[value="+itm+"]"));
        });
        $.each(listOptions, function(idx, itm) {
            if (itm.value != '' && priority.indexOf(Number(itm.value)) == -1 )
            select.append(itm);
        });
    }

</script>



