<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
     <div class="container listgoods content_books" style="margin-top: 0;">
    
    <div class="row">
        
        <div class="span10">
            <h1 class="titlename poht" style="margin-bottom: 20px;"><?php
                $breadcrumbs = $this->breadcrumbs;
                $h1 = array_pop($breadcrumbs);
                unset($breadcrumbs) ;
                $h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
            ?><?= $h1 ?></h1>
<?php $desc = ProductHelper::GetDescription($offer); ?>

            <?php if(!empty($desc)) : ?>
                <p class="text"><?=nl2br($desc); ?></p>
            <?php endif; ?>

            <?php foreach($groups as $group=>$data) : ?>
                <table height="30" cellspacing="0" cellpadding="0" border="0" style="vertical-align:top" class="text">
                    <tr>
                        <td colspan="3">
                            <div class="itemsep1">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="31" class="maintxt" style="padding-top: 2px;padding-bottom: 2px;padding-left: 2px;padding-right: 5px;">
                            <span class="entity_icons"><i class="fa e<?= $data['entity'] ?>"></i></span>
                            <?php /*
                            <img width="31" height="31" border="0" src="/pic1/cart_ibook.gif">
 */ ?>
                        </td>
                        <td width="100%" class="maintxt" style="padding: 2px;"><a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($data['entity']))); ?>" class="ctitle"><?=Entity::GetTitle($data['entity']); ?></a></td>
                    </tr>
                </table>

                <ul class="items">
                    <?php foreach($data['items'] as $item) : ?>
                        <?php
                            $item['entity'] = $data['entity'];
                            $item['status'] = Product::GetStatusProduct($item['entity'], $item['id']);
                        ?>
                        <li>
                            <?php $this->renderPartial('/entity/_common_item_2',
                                array('item' => $item,
                                      'entity' => $data['entity'], 'isList' => true)); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>


            <?php endforeach; ?>
            <?php if ($paginator) $this->widget('SortAndPaging', array('paginatorInfo' => $paginator)); ?>
        </div>
        <div class="span2">
            <?php $this->widget('YouView', array()); ?>
        </div>
        </div>
        </div>