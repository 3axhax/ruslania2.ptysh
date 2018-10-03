<?php KnockoutForm::RegisterScripts(); ?>
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
        <div class="span10">
			
			<h1 class="title"><?= Yii::app()->ui->item("MSG_SHOPCART_SUSPENDED_ITEMS"); ?></h1>
			
            <!-- content -->
            <table width="100%" cellspacing="1" cellpadding="5" border="0" class="cart1 memo_items">
                <tbody>
                <tr>
                    <th width="100%" valign="middle" class="cart1header1"><?=$ui->item("CART_COL_TITLE"); ?></th>
                    <th valign="middle" nowrap="" align="center" class="cart1header1"><?=$ui->item("CART_COL_ITEM_AVAIBILITY"); ?></th>
                    <th valign="middle" nowrap="" align="center" class="cart1header1"><?=$ui->item("CART_COL_ITEM_MOVE_TO_SHOPCART"); ?></th>
                    <th valign="middle" nowrap="" align="center" class="cart1header1"><?=$ui->item("CART_COL_ITEM_MOVE_TO_ORDERED"); ?></th>
                    <th valign="middle" align="center" class="cart1header1"><?=$ui->item("CART_COL_DELETE"); ?></th>
                </tr>
                <?php foreach($list as $item) : ?>
                <?php $url = ProductHelper::CreateUrl($item);
                      $isAvail = ProductHelper::IsAvailableForOrder($item);
                    ?>

                <tr id="row<?=$item['entity'].'_'.$item['id']; ?>">
                    <td width="100%" valign="middle" class="cart1contents1">
                        <span class="entity_icons"><i class="fa e<?= $item['entity'] ?>"></i></span>
                        <?php /*
                        <img width="31" height="31" align="middle" alt="" style="vertical-align: middle"
                            src="/pic1/cart_ibook.gif">
                        */ ?>
                        &nbsp;&nbsp;<a
                            title="<?=$ui->item("ITEM_MORE_INFO"); ?>"
                            href="<?=$url; ?>" class="maintxt1"><?=ProductHelper::GetTitle($item); ?></a></td>

                    <td valign="middle" align="center" class="cart1contents1">
                        <?=Availability::ToStr($item); ?>
                    </td>
                    <td valign="middle" align="center" class="cart1contents1">
                        <?php if($isAvail) : ?>
                        <a href="javascript:;" class="action" data-action="cart" data-entity="<?=$item['entity']; ?>" data-iid="<?=$item['id']; ?>"><img src="/new_img/cart_ico.png" /></a>
                        <?php endif; ?>
                    </td>
                    <td valign="middle" align="center" class="cart1contents1">
                        <?php if(!$isAvail && ($item['entity'] != Entity::VIDEO && $item['entity'] != Entity::AUDIO)) : ?>
                        <a href="javascript:;" class="action" data-action="request" data-entity="<?=$item['entity']; ?>" data-iid="<?=$item['id']; ?>"><img src="/new_img/inf_ico.png"/></a>
                        <?php endif; ?>
                    </td>
                    <td valign="middle" align="center" class="cart1contents1">
                        <a href="javascript:;" class="action" data-action="delete" data-entity="<?=$item['entity']; ?>" data-iid="<?=$item['id']; ?>"><img src="/new_img/del_cart.png"/></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>



            <!-- /content -->
        </div>
    <div class="span2">

                <?php $this->renderPartial('/site/_me_left'); ?>

            </div>
        </div>
        </div>
<script type="text/javascript">

    var csrf = $('meta[name=csrf]').attr('content').split('=');
    $(document).ready(function ()
    {
        var button = '<img src="/pic1/loader.gif" />';
        $('a.action').click(function()
        {
            var $el = $(this);
            var obj =
            {
                action : $el.attr('data-action'),
                entity : $el.attr('data-entity'),
                iid : $el.attr('data-iid')
            };
            obj[csrf[0]] = csrf[1];

            $el.hide().parent().append(button);
            $.when
            (
                $.ajax({
                    type: "POST",
                    url: '/my/changememo',
                    data: obj,
                    dataType: 'json'
                })
            ).then(function(json)
            {
                $el.show().parent().children('img').remove();
                if(!json.hasError)
                {
                    var key = '#row' + json.entity + '_' + json.iid;
                    $(key).fadeOut();
                }
            });
        });
    });

</script>







