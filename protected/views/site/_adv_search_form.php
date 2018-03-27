<div id="advsearch">

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
    $lang = trim(@$_GET['l']);
    $year = intVal(@$_GET['year']);
    if(empty($year) || $year <= 0) $year = '';
	$binding_id = intVal(@$_GET['binding_id'.$e]);
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
            <td><select name="+cid"
                        data-bind="options: Categories, optionsText: 'Name',
                         optionsCaption: '---',
                        optionsValue: 'ID', value: CID"></select>
            </td>
        </tr>
		
		<?
			foreach (Entity::GetEntitiesList() as $id => $val){
				//echo $id;
				
				eval('$bindingList'.$id.' = CHtml::listData(ProductHelper::GetBindingListForSelect('.$id.'), \'ID\', \'Name\');');
				
			
		
			if (count(${'bindingList'.$id}) == 0) continue;
			
		?>
		<? echo '
		<tr data-bind="visible: Entity()=='.$id.'">
            <td>'.$ui->item('A_NEW_PEREP') .':</td>
            <td>'.CHtml::dropDownList('binding_id'.$id, $binding_id, ${'bindingList'.$id}, array('empty' => '---')) .'</td>
        </tr>';
		
		}?>

        <tr>
            <td><?= $ui->item('CART_COL_TITLE'); ?>:</td>
            <td><?= CHtml::textField('title', $title); ?></td>
        </tr>
        <tr>
            <td><?= $ui->item('Author'); ?>:</td>
            <td><?= CHtml::textField('author', $author); ?></td>
        </tr>
        <tr data-bind="visible: Entity()==<?=Entity::AUDIO?> || Entity() == <?=Entity::MUSIC; ?>">
            <td><?= $ui->item('Performer'); ?>:</td>
            <td><?= CHtml::textField('perf', $perf, array('data-bind' => 'enable: Entity()=='.Entity::AUDIO.' || Entity() == '.Entity::MUSIC)); ?></td>
        </tr>
		
		<tr data-bind="visible: Entity()==<?=Entity::VIDEO?>">
            <td><?= $ui->item('Actor'); ?>:</td>
            <td><?= CHtml::textField('perf', $perf, array('data-bind' => 'enable: Entity()=='.Entity::VIDEO)); ?></td>
        </tr>
		
        <tr>
            <td><?= $ui->item('Published by'); ?>:</td>
            <td><?= CHtml::textField('publisher', $publisher); ?></td>
        </tr>
        <tr>
            <td><?=$ui->item('CATALOGINDEX_CHANGE_LANGUAGE'); ?>:</td>
            <?php $langList = CHtml::listData(Language::GetItemsLanguageList(), 'id', 'title_'.Yii::app()->language); ?>
            <td><?=CHtml::dropDownList('l', $lang, $langList, array('empty' => '---')); ?></td>
        </tr>
        <tr>
            <td><?=trim(sprintf($ui->item('PUBLISHED_IN_YEAR'), '')); ?>:</td>
            <td><?=CHtml::textField('year', $year); ?></td>
        </tr>
        <tr>
            <td><?= $ui->item('SEARCH_IN_STOCK'); ?>:</td>
            <td><?= CHtml::checkBox('only', $only); ?></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" class="sort" value="<?= $ui->item('BTN_CATALOG_SEARCH_SUBMIT'); ?>"/></td>
        </tr>
    </table>
    <?= CHtml::endForm(); ?>
</div>
<script type="text/javascript">

    var firstTime = true;
    var VM = function ()
    {
        var self = this;
        self.Entity = ko.observable();
        self.Categories = ko.observableArray([]);
        self.CID = ko.observable();

        self.Entity.subscribe(function (e)
        {
            if (e > 0)
            {
                self.Categories.removeAll();
                self.CID(0);
                $.getJSON('site/categorylistjson', { e: e }, function (json)
                {
                    ko.mapping.fromJS(json, {}, self.Categories);
                    if (firstTime && <?=$cid; ?> > 0 && e == <?=$e; ?>) self.CID(<?=$cid; ?>);
                    firstTime = false;
                });
            }
        });
    };

    var vm = new VM();
    ko.applyBindings(vm, $('#advsearch')[0]);

</script>



