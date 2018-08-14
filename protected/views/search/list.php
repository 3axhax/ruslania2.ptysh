<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container content_books">
<div class="row">

		<div class="listgoods span10">

<?php if (!empty($abstractInfo)): ?>
    <div style="margin-bottom: 20px;">
    <?php $dataForSearch = array('q'=>$q,'avail'=>$this->GetAvail(1));
            foreach ($abstractInfo as $eNum=>$counts): $dataForSearch['e'] = $eNum; ?>
                <div class="row_category"><?= $q ?> <span><?= $ui->item('A_NEW_SEARCH_IN_CAT') ?> <?= Entity::GetTitle($eNum) ?></span> <a href="/site/search?<?= http_build_query($dataForSearch) ?>" class="result_search_count"><?= $counts ?></a></div>
            <?php endforeach; ?>
        </div>
<?php endif; ?>

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
				
			
				
            </div>
            <ul class="items">
                <?php foreach ($products as $i) : ?>
                    <li>
						<?php //$i['status'] = Product::GetStatusProduct($i['entity'], $i['id'])?>
                        <?php $this->renderPartial('/entity/_common_item_2', array('item' => $i,
                                                                                 'isList' => true,
                                                                                 'entity' => $i['entity'])); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (count($products) > 0) $this->widget('SortAndPaging', array('paginatorInfo' => $paginatorInfo)); ?>

        </div>
    <div class="span2">
        <?php $this->widget('YouView', array()); ?>
    </div>
        </div>
        </div>
