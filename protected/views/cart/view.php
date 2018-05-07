<?php KnockoutForm::RegisterScripts(); ?>
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="container cabinet">
			
			<div class="row">

			<div class="span10">
			
			<h1 class="title">Корзина</h1>
			
			


            <div id="cart">
			
				<p style="display: none" data-bind="visible: CartItems().length > 0">
                                    <?=$ui->item('new (shopping cart)'); ?>
                                </p>
			
                <div data-bind="visible: FirstLoad" class="center cartload">
                    <?=$ui->item('CART_IS_LOADING'); ?><br/><br/>
                    <img src="/pic1/loader.gif"/>
                </div>

                <!-- content -->
                <div data-bind="visible:  CartItems().length > 0">
                    <table width="100%" cellspacing="1" cellpadding="5" border="0" class="cart1 items_tbl"
                           style="margin-bottom: 10px; margin-top: 15px;">
                        <thead>
                        <tr>
                            <th valign="middle"
                                class="cart1header1"><?= $ui->item("CART_COL_TITLE"); ?></th>
								<th valign="middle" align="center" class="cart1header1"><?=$ui->item('SHIPPING'); ?></th>
                            <th valign="middle" align="center"  style="width:70px;"
                                class="cart1header1"><?= $ui->item("Price"); ?></th>
                            <th valign="middle" align="center"  style="width:80px;"
                                class="cart1header1"><?= $ui->item("CART_COL_QUANTITY"); ?></th>
                            <th valign="middle" align="center"  style="width:70px;"
                                class="cart1header1"><?= $ui->item("CART_COL_SUBTOTAL_PRICE"); ?></th>
                            <th valign="middle" align="center"  style="width:80px;"
                                class="cart1header1"><?= $ui->item("CART_COL_ITEM_MOVE_TO_SUSPENDED"); ?></th>
                            <th valign="middle" align="center" style="width:70px;"
                                class="cart1header1"><?= $ui->item("CART_COL_DELETE"); ?></th>
                        </tr>
                        </thead>
                        <tbody class="items" data-bind="foreach: CartItems">
                        <tr>
                            <td valign="middle" class="cart1contents1">
                                <table>
								<tr>
								<td>
								<img width="31" height="31" align="middle"
                                     alt="" style="vertical-align: middle"
                                     src="/pic1/cart_ibook.gif">
								</td>
								
								
								
								<td style="padding-left: 20px;"><a
                                    data-bind="attr: { href: Url}, text: Title"
                                    class="maintxt1">
                                </a>
                                <p class="cartInfo" data-bind="text: InfoField, visible: InfoField() != null && InfoField().length > 0 "></p>
								</td>
								</tr>
								</table>
                            </td>
							 <td valign="middle" align="center" class="cart1contents1">
                <span data-bind="text: AvailablityText"></span>
            </td>
                            <td valign="middle" nowrap="true" align="center" class="cart1contents1 center">
                                <span data-bind="text: $root.ReadyPriceStr($data), visible: DiscountPercent() == 0"></span>
                                <div data-bind="visible: DiscountPercent() > 0">
                                    <s data-bind="text: PriceOriginal"></s><br/>
                                    <span data-bind="text: $root.ReadyPriceStr($data)"></span>
                                </div>

                            </td>
<!--                            <td align="center" class="center cart1contents1">-->
<!--                                <span data-bind="text: VAT"></span>%-->
<!--                            </td>-->
                            <td valign="middle" align="center" class="cart1contents1" nowrap>
                                <!--<a href="javascript:;" style="margin-right: 9px;" data-bind="event : { click : $root.QuantityChangedMinus }"><img src="/new_img/cart_minus.png" /></a>--> <input type="text" size="3" class="cart1contents1 center" style="margin: 0;" 
                                       data-bind="value: Quantity, event : { blur : $root.QuantityChanged }, id : 'field'"> <!--<a href="javascript:;" style="margin-left: 9px;"><img src="/new_img/cart_plus.png" data-bind="event : { click : $root.QuantityChangedPlus }"/></a> -->
                            </td>
                            <td valign="middle" nowrap="" align="center" class="cart1contents1">
                                <span data-bind="text: $root.LineTotalVAT($data)"></span>
                                <?=Currency::ToSign(Yii::app()->currency); ?>
                            </td>
                            <td valign="middle" align="center" class="cart1contents1">
                                <a href="javascript:;" data-bind="click: $root.ToMark"><img src="/new_img/add_mark.png" /></a>
                            </td>
                            <td valign="middle" align="center" class="cart1contents1">
                                <a href="javascript:;" data-bind="click: function(data, event) { cvm.RemoveFromCart(data, <?=Cart::TYPE_ORDER; ?>); }"><img src="/new_img/del_cart.png" /></a>
                            </td>
                        </tr>
                        </tbody>
                        <tr class="footer">
                            <td align="right" class="cart1header2" colspan="6"><div class="summa"><?=$ui->item('CART_COL_TOTAL_PRICE'); ?>, <span data-bind="text: IsVATInPrice()"></span>:
								
								<span style="font-weight: bold;" data-bind="visible: !AjaxCall()">
                                        <span data-bind="text: TotalVAT"></span>
                                        <?=Currency::ToSign(Yii::app()->currency); ?>
                                 </span>
                                <span data-bind="visible: AjaxCall"><?=$ui->item('UPDATING'); ?></span>
								</div>
							
                                <div data-bind="visible: UsingMinPrice" style="color: #999999">
                                    <?php $rates = Currency::GetRates();
                                          $rate = $rates[Yii::app()->currency];
                                    ?>
                                    <?=sprintf($ui->item('MSG_ORDER_MIN_SUMM'), ProductHelper::FormatPrice(Yii::app()->params['OrderMinPrice'] * $rate)); ?>
                                    <?php if(Yii::app()->currency != Currency::EUR) : ?>
                                        (<?=ProductHelper::FormatPrice(Yii::app()->params['OrderMinPrice'], false); ?> EUR)
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                        </tr>
                        <tr>
                            <td colspan="8" class="order_start_box" class="cart1header2" align="right">
                                <a href="/cart/doorder" class="order_start" data-bind="visible: CartItems().length > 0 && !AjaxCall() && TotalVAT() > 0">
                                    Оформить заказ
                                </a>

                                

                            </td>
                        </tr>
                    </table>


                </div>

                <table border="0" cellspacing="5" data-bind="visible: CartItems().length == 0 && !FirstLoad()">
                    <tr>
                        <td class="maintxt"><?= $ui->item("MSG_CART_ERROR_EMPTY"); ?></td>
                    </tr>
                </table>

                <div data-bind="visible:  EndedItems().length > 0" class="information info-box">
                    <p>
                    <?=$ui->item('MOVED_TO_WAITING_LIST'); ?>
                    </p>
                    <ul data-bind="foreach: EndedItems">
                        <li><span data-bind="text: Title"></span></li>
                    </ul>
                </div>
            </div>
            <!-- /content -->
        </div>

                <div class="span2">

             				<?php $this->renderPartial('/site/_me_left'); ?>

             			</div>
        </div>
        </div>
<script type="text/javascript">

    var csrf = $('meta[name=csrf]').attr('content').split('=');

    var cartVM = function ()
    {
        var self = this;
        self.FirstLoad = ko.observable(true);
        self.CartItems = ko.observableArray([]);
        self.EndedItems = ko.observableArray([]);
        self.RequestItems = ko.observableArray([]);
        self.AjaxCall = ko.observable(false);

        self.IsVATInPrice = function()
        {
            var usingVAT = true;
            var items = self.CartItems();
            $.each(items, function (idx, item)
            {
                usingVAT = item.UseVAT();
            });

            return usingVAT
                ? '<?=$ui->item('WITH_VAT'); ?>'
                : '<?=$ui->item('WITHOUT_VAT'); ?>';
        };

        self.ReadyPrice = function(item)
        {
            if(item.Entity() != <?=Entity::PERIODIC; ?>)
                return item.UseVAT() ? item.PriceVAT() : item.PriceVAT0();
            else
            {
                if(item.Price2Use() == <?=Cart::FIN_PRICE; ?>)
                    return item.UseVAT() ? item.PriceVATFin() : item.PriceVAT0Fin();
                else
                    return item.UseVAT() ? item.PriceVATWorld() : item.PriceVAT0World();
            }
        };

        self.ReadyPriceStr = function(item)
        {
            if(item.Entity() != <?=Entity::PERIODIC; ?>)
                return item.UseVAT() ? item.PriceVATStr() : item.PriceVAT0Str();
            else
            {
                if(item.Price2Use() == <?=Cart::FIN_PRICE; ?>)
                    return item.UseVAT() ? item.PriceVATFinStr() : item.PriceVAT0FinStr();
                else
                    return item.UseVAT() ? item.PriceVATWorldStr() : item.PriceVAT0WorldStr();
            }
        };

        self.LineTotalVAT = function (item)
        {
            return Math.abs(parseInt(item.Quantity()) * self.ReadyPrice(item)).toFixed(2);
        };

        self.ToMark = function(item, type)
        {
            var obj =
            {
                entity : item.Entity(),
                id : item.ID()
            };
            obj[csrf[0]] = csrf[1];

            $.when
                (
                    $.ajax({
                        type: "POST",
                        url: '/cart/mark',
                        data: obj,
                        dataType: 'json'
                    })
                ).then(function(json)
                {
                    if(!json.hasError)
                    {
                    }
                });

        };

        self.RemoveFromCart = function(item, type)
        {
            if(confirm('<?=$ui->item('ARE_YOU_SURE'); ?>'))
            {
                var obj =
                {
                    entity : item.Entity(),
                    iid : item.ID(),
                    type : type
                };
                obj[csrf[0]] = csrf[1];

                $.when
                    (
                        $.ajax({
                            type: "POST",
                            url: '/cart/remove',
                            data: obj,
                            dataType: 'json'
                        })
                    ).then(function(json)
                    {
                        if(!json.hasError)
                        {
                            if(type == <?=Cart::TYPE_ORDER; ?>)  self.CartItems.remove(item);
                            else self.RequestItems.remove(item);
                            update_header_cart();
                        }
                    });
            }
        };

        self.UsingMinPrice = ko.observable(false);

        self.TotalVAT = ko.computed(function ()
        {
            var ret = 0;
            var items = self.CartItems();

            var sumEur = 0;
            var rate = 1;
            $.each(items, function (idx, item)
            {
                sumEur += Math.abs(parseInt(item.Quantity()) * self.ReadyPrice(item));
                ret +=  Math.abs(parseInt(item.Quantity()) * self.ReadyPrice(item));
                rate = parseFloat(item.Rate());
            });

            if(sumEur < <?=Yii::app()->params['OrderMinPrice']; ?>)
            {
                ret = <?=Yii::app()->params['OrderMinPrice']; ?> * rate;
                self.UsingMinPrice(true);
            }
            else self.UsingMinPrice(false);

            return ret.toFixed(2);
        });
		
		self.QuantityChangedMinus = function (data, event)
        {
			
			//alert(event.value);
			
			if (data.Quantity() != '1') {
			
            var post =
            {
                entity: data.Entity(),
                id: data.ID(),
                quantity: parseInt(data.Quantity()) - 1,
                type : data.Price2Use()
            };
            post[csrf[0]] = csrf[1];

            $.post('/cart/changequantity', post, function (json)
            {
                if(json.changed)
                    data.InfoField(json.changedStr);
                else
                    data.InfoField('');
//                console.log(json);
                data.Quantity(json.quantity);
				update_header_cart();
            }, 'json');
			
			}
        };
		
		self.QuantityChangedPlus = function (data, event)
        {
			
			//alert(event.value);
			
			$('input', $(self).parent().parent()).val(parseInt($('input',$(self).parent().parent()).val()) + 1); 
			
            var post =
            {
                entity: data.Entity(),
                id: data.ID(),
                quantity: parseInt(data.Quantity()) + 1,
                type : data.Price2Use()
            };
            post[csrf[0]] = csrf[1];

            $.post('/cart/changequantity', post, function (json)
            {
                if(json.changed)
                    data.InfoField(json.changedStr);
                else
                    data.InfoField('');
//                console.log(json);
                data.Quantity(json.quantity);
				update_header_cart();
            }, 'json');
        };
		
        self.QuantityChanged = function (data, event)
        {
            var post =
            {
                entity: data.Entity(),
                id: data.ID(),
                quantity: data.Quantity(),
                type : data.Price2Use()
            };
            post[csrf[0]] = csrf[1];

            $.post('/cart/changequantity', post, function (json)
            {
                if(json.changed)
                    data.InfoField(json.changedStr);
                else
                    data.InfoField('');
//                console.log(json);
                data.Quantity(json.quantity);
				update_header_cart();
            }, 'json');
        };

        self.RequestQuantityChanged = function (data, event)
        {
            var post =
            {
                entity: data.Entity(),
                id: data.ID(),
                quantity: data.Quantity()
            };
            post[csrf[0]] = csrf[1];

            $.post('/cart/changequantity', post, function (json)
            {
//                console.log(json);
                data.Quantity(json.quantity);
				update_header_cart();
            }, 'json');
        };
		
		
    };

    var cvm = new cartVM();
    CartFirstState = $('#cart').clone();
    
    ko.applyBindings(cvm, $('#cart')[0]);

    $(document).ready(function ()
    {
        var data = { language: '<?=Yii::app()->language; ?>'};
        $.getJSON('/cart/getall', data, function (json)
        {
            ko.mapping.fromJS(json, {}, cvm);
            cvm.FirstLoad(false);
        });
    });

    $(document).ajaxStart(function ()
    {
        cvm.AjaxCall(true)
    }).ajaxComplete(function ()
        {
            cvm.AjaxCall(false);
        });
    
    //function update_cart() {
     //   ko.applyBindings(cvm, CartFirstState[0]);
        
     //    var data = { language: '<?=Yii::app()->language; ?>', is_MiniCart: 1}; 
     //   $.getJSON('/cart/getall', data, function (json)
     //       {
      //          ko.mapping.fromJS(json, {}, cvm);
    //
    //        });
 
    //}
	


</script>







