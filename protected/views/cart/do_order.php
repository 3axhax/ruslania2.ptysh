<?php KnockoutForm::RegisterScripts(); ?>
<script src="/js/jquery.leanModal.min.js"></script>

<div id="newAddress">
    <?php

    $user = Yii::app()->user->GetModel();
    $address = new Address;
    $address->receiver_title_name = $user['title_name'];
    $address->receiver_last_name = $user['last_name'];
    $address->receiver_first_name  = $user['first_name'];
    $address->receiver_middle_name = $user['middle_name'];
    $address->contact_email = $user['login'];
    $address->type = 2;
    $this->renderPartial('/site/address_form', array('model' => $address,
                                                           'mode' => 'new',
                                                           'afterAjax' => 'addrInserted')); ?>
</div>


<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>

<div class="container cabinet">

<div class="row">
        <div class="">

<?php if(empty($cartItems)) : ?>

    <?=$ui->item('MSG_CART_ERROR_EMPTY'); ?>

<?php else : ?>

<!-- cart -->
<div id="cart">
<table width="100%" cellspacing="1" cellpadding="5" border="0" class="cart1  items_tbl">
    <thead>
    <tr>
        <th width="100%" valign="middle" class="cart1header1"><?=$ui->item('CART_COL_TITLE');?></th>
        <th valign="middle" align="center" class="cart1header1"><?=$ui->item('SHIPPING'); ?></th>
        <th valign="middle" align="center" class="cart1header1" style="min-width: 70px;"><?=$ui->item('CART_COL_PRICE');?></th>
        <th valign="middle" align="center" class="cart1header1"><?=$ui->item('CART_COL_QUANTITY');?></th>
        <th valign="middle" align="center" class="cart1header1" style="min-width: 70px;"><?=$ui->item('CART_COL_SUBTOTAL_PRICE');?></th>
        <th valign="middle" align="center" class="cart1header1"><?=$ui->item('CART_COL_SUBTOTAL_WEIGHT');?></th>
    </tr>
    </thead>
    <?php $totalVAT = 0;
          $totalVAT0 = 0;
          $totalVATFin = 0;
          $totalVAT0Fin = 0;
          $totalVATWorld = 0;
          $totalVAT0World = 0;
    ?>

    <tbody class="items" data-bind="foreach: Items">
        <tr>
            <td width="100%" valign="middle" class="cart1contents1">
					
					<table>
								<tr>
								<td>
								<img width="31" height="31" align="middle"
                                     alt="" style="vertical-align: middle"
                                     src="/pic1/cart_ibook.gif">
								</td><td style="padding-left: 20px;"><a
                    title="<?=$ui->item('ITEM_MORE_INFO'); ?>"
                    data-bind="attr: { href: Url}, text: Title"
                    class="maintxt1"></a>
                                
								</td>
								</tr>
								</table>
					
					
            </td>
            <td valign="middle" align="center" class="cart1contents1">
                <span data-bind="text: AvailablityText"></span>
            </td>
            <td valign="middle" nowrap="" align="center" class="cart1contents1">
                <span data-bind="text: $root.ReadyPriceStr($data)"></span>
            </td>
            <td valign="middle" align="center"
                class="cart1contents1"><span data-bind="text: Quantity"></span></td>
            <td valign="middle" nowrap="" align="center"
                class="cart1contents1">
                <span data-bind="text: $root.LineTotalVAT($data)"></span>
                <?=Currency::ToSign(Yii::app()->currency); ?>
            </td>
            <td valign="middle" align="center" class="cart1contents1">
                <span data-bind="text: UnitWeight"></span>
            </td>
        </tr>
    </tbody>



        <tr data-bind="visible: UsingMinPrice">
            <td class="cart1header2">&nbsp;</td>
            <td align="left" class="cart1header2" colspan="5">
                <?=sprintf($ui->item('MSG_ORDER_MIN_SUMM'), ProductHelper::FormatPrice(Yii::app()->params['OrderMinPrice'])); ?>
                <?php if(Yii::app()->currency != Currency::EUR) : ?>
                    (<?=ProductHelper::FormatPrice(Yii::app()->params['OrderMinPrice'], false); ?> EUR)
                <?php endif; ?>
            </td>
        </tr>


    <tr>
        <td align="right" class="cart1header2"><?=$ui->item('CART_COL_TOTAL_PRICE'); ?>:</td>
        <td style="font-weight: bold;" colspan="5"
            class="cart1header2">
            <span data-bind="text: ItemsPrice"></span>
        </td>
    </tr>

    <tr data-bind="visible: DeliveryAddressID() > 0 && DeliveryMode() == 0">
        <td align="right" class="cart1header3"><?=sprintf($ui->item('CART_COL_TOTAL_DELIVERY_PRICE'), ''); ?>
            <span data-bind="text: DeliveryAddressText"></span>
        </td>
        <td style="font-weight: bold;" colspan="6" class="cart1header3">
            <span data-bind="visible: DeliveryTypeID() > 0">
                <span data-bind="text: DeliveryPrice"></span> <?= ProductHelper::FormatCurrency(); ?>
             </span>
            <span data-bind="visible: !(DeliveryTypeID() > 0)">
                <?=$ui->item('NO_DELIVERY_TYPE_CHOOSEN'); ?>
             </span>
        </td>
    </tr>
    <tr>
        <td align="right" class="cart1header3"><?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>:</td>
        <td style="font-weight: bold;" colspan="6" class="cart1header3"><span
                data-bind="text: TotalPrice"></span> <?= ProductHelper::FormatCurrency(); ?>

            <span data-bind="visible: WithVAT">(<?=$ui->item('WITH_VAT'); ?>)</span>
            <span data-bind="visible: !WithVAT()">(<?=$ui->item('WITHOUT_VAT'); ?>)</span>

        </td>
    </tr>

</table>
<!-- /cart -->

<br/>

<div class="accordion" data-bind="slideVisible: DeliveryMode() == null || DeliveryMode() == 1">
    <h3 data-bind="css : { ready : DeliveryMode()==1 }">
        <span data-bind="visible: DeliveryMode() == null"><?=$ui->item('CHOOSE_DELIVERY_TYPE'); ?> </span>
        <span data-bind="visible: DeliveryMode() != null, html: DeliveryModeText"></span>
        <a href="#" class="small-link"
           data-bind="visible: DeliveryMode() != null, click: ChangeDeliveryMode"><?=$ui->item('CHANGE'); ?></a>
    </h3>

    <div class="inner" data-bind="slideVisible: DeliveryMode() == null">
        <p class="next"><?=$ui->item('CLICK_TO_NEXT'); ?></p>
        <label for="isReserved1"><input type="radio" id="isReserved1" name="dtype" value="1" data-bind="checked: DeliveryMode">
        <?=$ui->item('I_WANT_TO_BUY_IN_SHOP'); ?></label>
        <br/>
        <label for="isReserved2"><input type="radio" id="isReserved2" name="dtype" value="0" data-bind="checked: DeliveryMode">
        <?=$ui->item('I_WANT_GOODS_TO_ADDRESS'); ?></label>
    </div>
</div>


<div class="accordion" data-bind="visible: DeliveryMode() == 0">
    <h3 data-bind="css : { ready : DeliveryAddressID() > 0 }">
                    <span data-bind="visible: DeliveryAddressID() == 0"><?= $ui->item('MSG_ORDER_STEP_2_TITLE'); ?>
                        <a href="#" class="small-link"
                           data-bind="visible: DeliveryMode() != null, click: ChangeDeliveryMode"><?=$ui->item('CHANGE_DELIVERY_TYPE'); ?></a>
                    </span>

                    <span data-bind="visible: DeliveryAddressID() > 0 && DeliveryMode() == 0">
                        <?=$ui->item('DELIVERY_TO_ADDRESS'); ?>: <span data-bind="text: DeliveryAddressText" class="nobold"></span>
                        <a href="#" class="small-link" data-bind="click: ChangeDeliveryAddress"><?=$ui->item('CHANGE_DELIVERY_ADDRESS'); ?></a>
                    </span>
    </h3>

    <div class="inner" data-bind="slideVisible: DeliveryMode()==0 && DeliveryAddressID() == 0">

        <div class="info-box information" data-bind="visible: Addresses().length == 0">
            <?=$ui->item("MSG_ADDRESS_ERROR_EMPTY"); ?>
            <p><a id="nda" name="test" href="#newAddress"><?=$ui->item('YM_CONTEXT_PERSONAL_ADD_ADDRESS'); ?></a>
            </p>
        </div>

        <div data-bind="visible: Addresses().length > 0">
        <p class="next"><?=$ui->item('CLICK_ADDRESS_TO_NEXT'); ?></p>
        <ul data-bind="foreach: Addresses" style="list-style-type: none;">
            <li>
                <label><input name="did" type="radio" data-bind="click: $root.SetDeliveryAddress, checked: $root.DeliveryAddressID, attr : { value: id }" /> <span
                        data-bind="text: AddressFormatted"></span></label>
            </li>
        </ul>
        <a id="nda" name="test" href="#newAddress"><?=$ui->item('ADD_ADDRESS_ALT'); ?></a>
        </div>
    </div>
</div>

    <div class="accordion" data-bind="visible: DeliveryMode() == 0 && DeliveryAddressID() > 0">
        <h3 data-bind="css : { ready : BillingAddressID() > 0 }">
            <span data-bind="visible: BillingAddressID() == 0"><?=$ui->item('CHOOSE_BILLING_ADDRESS2'); ?></span>
            <span data-bind="visible: BillingAddressID() > 0"><?=$ui->item('BILL_RECEIPT_TO_ADDRESS'); ?>:
                    <span data-bind="text: BillingAddressText" class="nobold"></span>
                        <a href="#" class="small-link" data-bind="click: ChangeBillingAddress"><?=$ui->item('CHOOSE_ANOTHER_BILLING_ADDRESS'); ?></a>
                    </span>

        </h3>

        <div class="inner"
             data-bind="slideVisible: (DeliveryAddressID() != 0) && (DeliveryMode()==0) && (BillingAddressID() == 0)">
            <p class="next"><?=$ui->item('CLICK_ADDRESS_TO_NEXT'); ?></p>

            <ul data-bind="foreach: Addresses"
                style="list-style-type: none;">
                <li>
                    <label><input name="bid" type="radio" data-bind="click: $root.SetBillingAddress, attr : { value: id }, checked: $root.BillingAddressID "/> <span
                            data-bind="text: AddressFormatted"></span></label>
                </li>
            </ul>

            <a id="nba" name="test" href="#newAddress"><?=$ui->item('NEW_BILLING_ADDRESS'); ?></a>
        </div>
    </div>


<div class="accordion" data-bind="visible: DeliveryMode() == 0  && DeliveryAddressID() > 0 && BillingAddressID() > 0">
    <h3 data-bind="css : { ready : DeliveryTypeID() > 0 }">
        <span data-bind="visible: DeliveryTypeID() == 0"><?=$ui->item('CHOOSE_DELIVERY_TYPE'); ?></span>
                    <span data-bind="visible: DeliveryTypeID() != 0"><?=$ui->item('DELIVERY_TYPE'); ?>:
                        <span data-bind="text: DeliveryTypeText" class="nobold"></span>
                        <a href="#" class="small-link" data-bind="click: ChangeDeliveryType"><?=$ui->item('CHANGE_DELIVERY_TYPE'); ?></a>
                    </span>
    </h3>

    <div class="inner"
         data-bind="slideVisible: DeliveryAddressID()>0 && DeliveryMode()==0 && DeliveryTypeID()==0">
        <p class="next"><?=$ui->item('CLICK_TO_NEXT'); ?></p>
        <ul data-bind="foreach: DeliveryTypes" style="list-style-type: none;">
            <li><label><input type="radio" name="dtid"
                              data-bind="checked: $root.DeliveryTypeID, value: id"/> <span
                        data-bind="text: type"></span> +<span data-bind="text: value().toFixed(2)"></span> <span data-bind="text: currencyName"></span></label>
                (<?=$ui->item('ORDER_DELIVERY_CLASS_TIME'); ?> <span data-bind="text: deliveryTime"></span> <?=sprintf($ui->item('X_DAYS_3'), ''); ?>)
            </li>
        </ul>
    </div>
</div>



<div class="accordion">
    <h3 data-bind="css : { ready : Notes() != null && Notes().length > 0 }"><?=$ui->item('NOTES_AND_CONFIRMATION'); ?></h3>

    <div class="inner"
         data-bind="slideVisible: (DeliveryAddressID() != 0 && DeliveryMode()==0 && DeliveryTypeID() > 0 && BillingAddressID() != 0) || DeliveryMode()==1 ">
        <p><?=$ui->item('ORDER_MSG_USER_COMMENTS'); ?>:</p>
        <textarea data-bind="value: Notes" class="notesta"></textarea>

        <p><?=$ui->item('ITOGO'); ?>:</p>
        <dl>
            <dt><?=$ui->item('PRICE_WITHOUT_DELIVERY'); ?>:</dt>
            <dd>
                <span data-bind="text: ItemsPrice"></span>
            </dd>

            <dt data-bind="visible: DeliveryMode() == 0"><?=$ui->item('ITOGO_DELIVERY_PRICE'); ?>:</dt>
            <dd data-bind="visible: DeliveryMode() == 0"><span
                    data-bind="text: DeliveryPrice"></span> <?= ProductHelper::FormatCurrency(); ?></dd>

            <dt data-bind="visible: DeliveryMode() == 1"><?=$ui->item('CART_COL_SUBTOTAL_DELIVERY'); ?>:</dt>
            <dd data-bind="visible: DeliveryMode() == 1"><?=$ui->item('I_WANT_TO_BUY_IN_SHOP'); ?></dd>

            <dt><?=$ui->item('CART_COL_TOTAL_FULL_PRICE'); ?>:</dt>
            <dd><span data-bind="text: TotalPrice"></span> <?= ProductHelper::FormatCurrency(); ?></dd>
        </dl>
        <br/>
        <input type="checkbox" id="confirm" data-bind="checked: Confirm"> <label for="confirm">
            <?=$ui->item('MSG_CONDITIONS_PHASE1'); ?>
        </label><br/>
        <br/>
        <div class="errorOrder" data-bind="slideVisible: Errors().length > 0">
            <h4><?=$ui->item('ERRORS_IN_ORDER_PROCESSINIG'); ?></h4>
            <ul data-bind="foreach: Errors">
                <li data-bind="text: $data"></li>
            </ul>
        </div>
        <br/>
        <button data-bind="enable: Confirm, click: DoOrder, visible: !Processing()"><?=$ui->item('CONFIRM_ORDER'); ?></button>
        <button disabled="disabled" data-bind="visible: Processing"><?=$ui->item('ORDER_PROCESSING'); ?></button>

    </div>
</div>


    <script type="text/javascript">

    ko.bindingHandlers.slideVisible = {
        update: function (element, valueAccessor, allBindingsAccessor)
        {
            // First get the latest data that we're bound to
            var value = valueAccessor(), allBindings = allBindingsAccessor();

            // Next, whether or not the supplied model property is observable, get its current value
            var valueUnwrapped = ko.utils.unwrapObservable(value);

            // Grab some more data from another binding property
            var duration = allBindings.slideDuration || 400; // 400ms is default duration unless otherwise specified

            // Now manipulate the DOM element
            if (valueUnwrapped == true)
                $(element).slideDown(duration); // Make the element visible
            else
                $(element).slideUp(duration);   // Make the element invisible
        }
    };

    var csrf = $('meta[name=csrf]').attr('content').split('=');

    var orderVM = function () {
        var self = this;
        self.Items = ko.observableArray([]);
        self.ReadyPrice = function (item) {
            if (item.Entity() != <?=Entity::PERIODIC; ?>)
                return self.WithVAT() ? item.PriceVAT().toFixed(2) : item.PriceVAT0().toFixed(2);
            else {
                var price2use = item.Price2Use();
                var da = self.DeliveryAddress();

                if (da != undefined && da != null)
                {
                    var code = ($.isFunction(da.code)) ? da.code() : da.code;
                    price2use = code == 'FI' ? <?=Cart::FIN_PRICE; ?> : <?=Cart::WORLD_PRICE; ?>;
                }

                if (price2use == <?=Cart::FIN_PRICE; ?>) {
                    return self.WithVAT() ? item.PriceVATFin() : item.PriceVAT0Fin();
                }
                else {
                    return self.WithVAT() ? item.PriceVATWorld() : item.PriceVAT0World();
                }
            }
        };

        self.ReadyPriceStr = function (item)
        {
            if (item.Entity() != <?=Entity::PERIODIC; ?>)
                return self.WithVAT() ? item.PriceVATStr() : item.PriceVAT0Str();
            else {
                var price2use = item.Price2Use();
                var da = self.DeliveryAddress();

                if (da != undefined && da != null) {
                    var code = ($.isFunction(da.code)) ? da.code() : da.code;
                    price2use = code == 'FI' ? <?=Cart::FIN_PRICE; ?> : <?=Cart::WORLD_PRICE; ?>;
                }

                if (price2use == <?=Cart::FIN_PRICE; ?>) {
                    return self.WithVAT() ? item.PriceVATFinStr() : item.PriceVAT0FinStr();
                }
                else {
                    return self.WithVAT() ? item.PriceVATWorldStr() : item.PriceVAT0WorldStr();
                }
            }
        };

        self.LineTotalVAT = function (item)
        {
            return Math.abs(parseInt(item.Quantity()) * self.ReadyPrice(item)).toFixed(2);
        };

        self.WithVAT = ko.observable(true);
        self.DeliveryMode = ko.observable(null);
        self.DeliveryModeText = ko.computed(function ()
        {
            var mode = self.DeliveryMode();
            if (mode == 1) return '<?=CHtml::encode($ui->item('I_WANT_TO_BUY_IN_SHOP')); ?>';
            else return  '<?=CHtml::encode($ui->item('I_WANT_GOODS_TO_ADDRESS')); ?>';//'Заказ будет доставлен на выбранный ниже адрес';
        });
        self.ChangeDeliveryMode = function ()
        {
            self.DeliveryMode(null);
            self.WithVAT(true);
            self.DeliveryAddressID(0);
            self.DeliveryAddress(null);
            self.BillingAddressID(0);
        };

        self.Addresses = ko.observableArray([]);
        self.DeliveryAddress = ko.observable();
        self.DeliveryAddressID = ko.observable(0);
        self.BillingAddressID = ko.observable(0);
        self.DeliveryAddressText = ko.computed(function ()
        {
            var did = self.DeliveryAddressID();
            if (did > 0)
            {
                var add = ko.utils.arrayFirst(self.Addresses(), function (item)
                {
                    return item.address_id() == did;
                });

                return add.AddressFormatted();
            }
            return '';
        });
        self.BillingAddressText = ko.computed(function ()
        {
            var did = self.BillingAddressID();
            if (did > 0)
            {
                var add = ko.utils.arrayFirst(self.Addresses(), function (item)
                {
                    return item.address_id() == did;
                });

                return add.AddressFormatted();
            }
            return '';
        });

        self.ChangeDeliveryAddress = function ()
        {
            self.DeliveryAddressID(0);
        };
        self.ChangeBillingAddress = function ()
        {
            self.DifferentBillingAddress(null);
            self.BillingAddressID(0);
        };

        self.DifferentBillingAddress = ko.observable(null);


        self.DeliveryTypes = ko.observableArray([]);
        self.DeliveryTypeID = ko.observable(0);
        self.DeliveryTypeText = ko.computed(function ()
        {
            var t = self.DeliveryTypeID();
            if (t > 0)
            {
                var add = ko.utils.arrayFirst(self.DeliveryTypes(), function (item)
                {
                    return item.id() == t;
                });

                return add.type();
            }
            return '';
        });

        self.ChangeDeliveryType = function ()
        {
            self.DeliveryTypeID(0);
        };

        self.UsingMinPrice = ko.observable(false);
        self.Notes = ko.observable();
        self.Confirm = ko.observable(false);

        self.SetDeliveryAddress = function (data)
        {
            self.DeliveryAddressID(data.address_id());
            self.WithVAT(data.WithVAT());
            self.DeliveryTypeID(0);
            self.DeliveryAddress(data);
        };

        self.SetBillingAddress = function (data)
        {
            self.BillingAddressID(data.address_id());
        };

        self.DeliveryPrice = ko.computed(function ()
        {
            var ret = 0;
            var t = self.DeliveryTypeID();
            if (t > 0)
            {
                var add = ko.utils.arrayFirst(self.DeliveryTypes(), function (item)
                {
                    return item.id() == t;
                });
                ret = add.value();
            }

            return ret.toFixed(2);
        });

        self.ItemsPrice = ko.computed(function()
        {
            var ret = 0;
            var items = self.Items();

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

        self.TotalPrice = ko.computed(function ()
        {
            var ret = self.ItemsPrice();
            return (parseFloat(ret) + parseFloat(self.DeliveryPrice())).toFixed(2);
        });

        ko.computed(function ()
        {
            var dm = self.DeliveryMode();
            if (dm == 1) return; // isReserved

            $.getJSON('/site/myaddresses', function (json)
            {
                ko.mapping.fromJS(json, {}, ovm);
                if(json.Addresses.length == 1)
                {
                    if(self.DeliveryAddressID() == 0)
                    {
                        var withVAT = json.Addresses[0].WithVAT;
                        self.DeliveryAddressID(json.Addresses[0].id);
                        self.WithVAT(withVAT);
                        self.DeliveryAddress(json.Addresses[0]);
                    }
                }
            });
        });

        ko.computed(function ()
        {
            var d = self.DeliveryAddressID();
            if (d > 0)
            {
                var address = ko.utils.arrayFirst(self.Addresses(), function (item)
                {
                    return item.id() == d;
                });

                if(address != null)
                    self.WithVAT(address.WithVAT());

                $.getJSON('/site/getdeliverytypes', { aid: d }, function (json)
                {
                    ko.mapping.fromJS(json, {}, ovm);
                    if(self.Addresses().length == 1)
                    {
                        var aid = self.Addresses()[0].id();
                        self.DifferentBillingAddress(0);
                        self.BillingAddressID(aid);
                    }
                    if(self.DeliveryTypes().length == 1)
                    {
                        self.DeliveryTypeID(self.DeliveryTypes()[0].id());
                    }
                });
            }
        });

        ko.computed(function ()
        {
            var t = self.DifferentBillingAddress();
            if (t == 0) self.BillingAddressID(self.DeliveryAddressID());
            else self.BillingAddressID(0);
        });

        self.Processing = ko.observable(false);
        self.Errors = ko.observableArray([]);

        self.DoOrder = function ()
        {
            var data = {
                DeliveryMode: parseInt(self.DeliveryMode()),
                DeliveryAddressID: parseInt(self.DeliveryAddressID()),
                BillingAddressID: parseInt(self.BillingAddressID()),
                DeliveryTypeID: parseInt(self.DeliveryTypeID()),
                Notes: $.trim(self.Notes()),
                CurrencyID : <?=Yii::app()->currency; ?>,
                Mandate : '<?=OrderForm::GenMandate(); ?>'
            };
            data[csrf[0]] = csrf[1];

            self.Errors.removeAll();
            self.Processing(true);
            $.when($.ajax({
                    url: '/order/create',
                    type: 'POST',
                    data: data,
                    dataType: 'json'
                })).then(function (json)
                {
                    self.Processing(false);
                    if (json.hasError)
                    {
                        $.each(json.Errors, function (idx, value)
                        {
                            self.Errors.push(value);
                        });
                    }
                    else
                    {
                        window.location.href = json.url;
                    }
                }, function (json)
                {
//                    console.log('fail', json);
                    self.Processing(false);
                })

        };
    };

    var ovm = new orderVM();
    ko.applyBindings(ovm, $('#cart')[0]);

    $.getJSON('/cart/doorderjson', function(json)
    {
        ko.mapping.fromJS(json, {}, ovm);
    });


    function addrInserted(addrData)
    {
        $.getJSON('/site/myaddresses', function (json)
        {
//            console.log(json);
            ko.mapping.fromJS(json, {}, ovm);
            $('#lean_overlay').trigger('click');

            if(json.Addresses.length > 0)
            {
                $.each(json.Addresses, function(idx, addr)
                {
                    if(addr.id == addrData.id)
                    {
                        ovm.DeliveryAddressID(addr.id);
                        return;
                    }
                });
            }

        });
    }

    $(document).ready(function ()
    {
        $('#nda, #nba').leanModal();
    });

    //    $(document).ajaxStart(function()
    //    {
    //        cvm.AjaxCall(true)
    //    }).ajaxComplete(function()
    //        {
    //            cvm.AjaxCall(false);
    //        });


    </script>



<?php endif; ?>

<!-- /content -->
</div>
</div>
   
</div>
</div>



