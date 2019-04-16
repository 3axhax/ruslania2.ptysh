<?php //KnockoutForm::RegisterScripts(); ?>
								<div class="b-basket-list__empty" data-bind="visible: CartItems().length < 1"><span><?=Yii::app()->ui->item('A_NEW_CART_INFO')?></span>
							</div>
                            <div class="b-basket-list__center" data-bind="foreach: CartItems">
							
							
							
                           <!-- ko if: $index() < 3 -->                     
						<div class="b-basket-list__item">
						
									<!--<div class="alert" data-bind="attr: { class: 'alert alert'+ID()}" >
										<div style="margin: 5px;">
											<div class="title"><?=Yii::app()->ui->item('ARE_YOU_SURE'); ?></div>
											
											<div style="text-align: center; margin-top: 5px;">
												<a href="javascript:;" class="btn_yes" style="margin-right: 20px;"><?=Yii::app()->ui->item('A_NEW_BTN_YES')?></a>
												<a href="javascript:;" onclick="javascript:;" class="btn_no"><?=Yii::app()->ui->item('A_NEW_BTN_NO')?></a>
											</div>
											
										</div>
									</div>-->
						
						
                                    <div class="b-basket-list__img-wrapper">
									<div class="b-basket-list__img">
                                        <span class="entity_icons"><i class="fa" data-bind="attr: { class: 'fa e'+Entity()}"></i></span>
                                        <?php /*<img width="31" height="31" align="middle"
                                     alt="" style="vertical-align: middle"
                                     src="/pic1/cart_ibook.gif"> */ ?>
                                    </div>
                                    </div>
                                    <div class="b-basket-list__about" style="width: 180px;">
                                        <div class="b-basket-list__item-name"><a
                                    data-bind="attr: { href: Url}, text: Title"
                                    class="maintxt1">
                                </a></div>
                                        <div class="b-basket-list__price"><p class="maintxt_price">
                                <span data-bind="text: $root.ReadyPriceStr($data), visible: DiscountPercent() == 0" ></span>
                                <div data-bind="visible: DiscountPercent() > 0">
                                    <s data-bind="text: PriceOriginal"></s>
                                    <span data-bind="text: $root.ReadyPriceStr($data)"></span>
                                </div>
                                </p></div>
                                    </div>
                                    <div class="b-basket-list__calc" style="    max-width: 125px;">
                                        <a href="javascript:;" style="margin-right: 9px;" data-bind="event : { click : $root.QuantityChangedMinus }, visible: noUseChangeQuantity() == 0"></a>
                                        <input type="text" size="3" class="cart1contents1 center" style="margin: 0; width: 50px;" data-bind="value: Quantity, event : { blur : $root.QuantityChanged }, id : 'field'">
                                        <div style="display:none;width:25px;float:left;" data-bind="visible: noUseChangeQuantity() > 0">&nbsp;</div>
                                        <a href="javascript:;" style="margin-left: 9px;" data-bind="event : { click : $root.QuantityChangedPlus }, visible: noUseChangeQuantity() == 0"></a>
                                    </div>
                                    <div class="b-basket-list__cross js-close-item" data-bind="click: function(data, event) { cvm_1.RemoveFromCart(data, <?=Cart::TYPE_ORDER; ?>); }"></div>
									
									
									
									
                                </div>
						<!-- /ko -->
						
						
						
                    </div>
					
					<div class="b-basket-list__bottom">
                                <div class="b-basket-list__load-wrapp"><a class="b-basket-list__load-btn" href="<?=Yii::app()->createUrl('cart/view'); ?>"  data-bind="text: priceStrToPrice(CartItems().length-3)+' ', visible: CartItems().length > 3"></a></div>
                                <div class="b-basket-list__order-wrapp" data-bind="visible: CartItems().length > 0"><a class="b-basket-list__order-btn" href="<?=Yii::app()->createUrl('cart/view')?>"><?=Yii::app()->ui->item('CONFIRM_ORDER');?></a></div>
                            </div>
<?php /*
</div>
*/ ?>
<?php
    $assets = Yii::getPathOfAlias('webroot') . '/protected/extensions/knockout-form/assets';
    $baseUrl = Yii::app()->assetManager->publish($assets);
?>
<?php if ($refererRoute != 'site/advsearch' AND $refererRoute != 'site/login'): ?>
<script type="text/javascript" src="<?= $baseUrl ?>/knockout.js"></script>
<script type="text/javascript" src="<?= $baseUrl ?>/knockout.mapping.js"></script>
<script type="text/javascript" src="<?= $baseUrl ?>/knockoutPostObject.js"></script>
<?php endif; ?>
<script type="text/javascript">
    var csrf_1 = $('meta[name=csrf]').attr('content').split('=');
    var cartVM_1 = function () {
        var self = this;
        self.FirstLoad = ko.observable(true);
        self.CartItems = ko.observableArray([]);
        self.EndedItems = ko.observableArray([]);
        self.RequestItems = ko.observableArray([]);
        self.AjaxCall = ko.observable(false);

        self.IsVATInPrice = function() {
            var usingVAT = true;
            var items = self.CartItems();
            $.each(items, function (idx, item) {
                usingVAT = item.UseVAT();
            });

            return usingVAT
                ? '<?=Yii::app()->ui->item('WITH_VAT'); ?>'
                : '<?=Yii::app()->ui->item('WITHOUT_VAT'); ?>';
        };

        self.ReadyPrice = function(item) {
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

        self.priceStrToPrice = function(count) {
            var num, out;
            num = count % 100;
            if (num > 19) num = num % 10;

            //$out = (show) ?  $value . ' ' : '';
            switch (num) {
                case 1:
                    out = '<?= Yii::app()->ui->item('CARTNEW_MORE_CART_COUNT') ?>'.replace('%d', count) + ' <?= !in_array(Yii::app()->getLanguage(), array('fi', 'se'))?Yii::app()->ui->item('CARTNEW_PRODUCTS_TITLE2'):'' ?>';
//                    out = count+' <?//= Yii::app()->ui->item('CARTNEW_PRODUCTS_TITLE2') ?>//';
                    break;
                case 2:
                case 3:
                case 4:
                    out = '<?= Yii::app()->ui->item('CARTNEW_MORE_CART_COUNT') ?>'.replace('%d', count) + ' <?= !in_array(Yii::app()->getLanguage(), array('fi', 'se'))?Yii::app()->ui->item('CARTNEW_PRODUCTS_TITLE1'):'' ?>';
//                    out = count+' <?//= Yii::app()->ui->item('CARTNEW_PRODUCTS_TITLE1') ?>//';
                    break;
                default:
                    out = '<?= Yii::app()->ui->item('CARTNEW_MORE_CART_COUNT') ?>'.replace('%d', count) + ' <?= !in_array(Yii::app()->getLanguage(), array('fi', 'se'))?Yii::app()->ui->item('CARTNEW_PRODUCTS_TITLE3'):'' ?>';
//                    out = count+' <?//= Yii::app()->ui->item('CARTNEW_PRODUCTS_TITLE3') ?>//';
                    break;
            }
            return out;

        };

        self.ReadyPriceStr = function(item) {
            if(item.Entity() != <?=Entity::PERIODIC; ?>)
                return item.UseVAT() ? item.PriceVATStr() : item.PriceVAT0Str() + '<?//=Currency::ToSign()?>';
            else {
                if(item.Price2Use() == <?=Cart::FIN_PRICE; ?>) {
                    return item.UseVAT() ? (parseInt(item.Quantity()) * self.ReadyPrice(item)).toFixed(2) + ' <?=Currency::ToSign()?>' : (parseInt(item.Quantity()) * self.ReadyPrice(item)).toFixed(2) + ' <?=Currency::ToSign()?>';
                } else {
                    return item.UseVAT() ? ((item.Quantity()) * self.ReadyPrice(item)).toFixed(2) + ' <?=Currency::ToSign()?>' : ((item.Quantity()) * self.ReadyPrice(item)).toFixed(2) + ' <?=Currency::ToSign()?>';
                }
            }
        };

        self.LineTotalVAT = function (item) {
            return Math.abs(parseInt(item.Quantity()) * self.ReadyPrice(item)).toFixed(2);
        };

        self.RemoveFromCart = function(item, type) {
            var obj = {
                entity : item.Entity(),
                iid : item.ID(),
                type : type
            };
				
				//alert(item.ID());
				
				
				
                obj[csrf_1[0]] = csrf_1[1];
                $.when($.ajax({
                    type: "POST",
                    url: '<?=Yii::app()->createUrl('cart/remove')?>',
                    data: obj,
                    dataType: 'json',
                    success: function() {
                        update_header_cart();
						
						$('a.cart'+item.ID()).removeClass('green_cart');
                        $('a.cartMini'+item.ID()).removeClass('green_cart');

                        if (item.Entity() == <?= Entity::PERIODIC ?>) {
                            $('a.cart'+item.ID()).html('<span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></span>');
                            $('a.cartMini'+item.ID()).html('<span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></span>');
                        }
                        else {
                            $('a.cart'+item.ID()).html('<span style="padding: 0 17px 0 20px;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></span>');
                            $('a.cart'+item.ID()).attr('style', '');
                            $('a.cartMini'+item.ID()).html('<span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART')?></span>');
                            $('a.cartMini'+item.ID()).attr('style', '');
                        }

                        $('div.already-in-cart'+item.ID()).html('&nbsp;');
						
                    }
                })).then(function(json) {
                    if(!json.hasError) $('.b-basket-list').show();
                });
        };

        self.UsingMinPrice = ko.observable(false);

        self.TotalVAT = ko.computed(function () {
            var ret = 0;
            var items = self.CartItems();
            var sumEur = 0;
            var rate = 1;
            $.each(items, function (idx, item) {
                sumEur += Math.abs(parseInt(item.Quantity()) * self.ReadyPrice(item));
                ret +=  Math.abs(parseInt(item.Quantity()) * self.ReadyPrice(item));
                rate = parseFloat(item.Rate());
            });

            if(sumEur < <?=Yii::app()->params['OrderMinPrice']; ?>) {
                ret = <?=Yii::app()->params['OrderMinPrice']; ?> * rate;
                self.UsingMinPrice(true);
            }
            else self.UsingMinPrice(false);

            return ret.toFixed(2);
        });

		self.QuantityChangedMinus = function (data, event) {
			if (data.Quantity() != '1') {
                var post = {
                    entity: data.Entity(),
                    id: data.ID(),
                    quantity: parseInt(data.Quantity()) - 1,
                    decrement: 1,
                    type : data.Price2Use()
                };
                post[csrf_1[0]] = csrf_1[1];
			
                $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json) {
					
					var repltext = '<?=$ui->item('CARTNEW_IN_CART_BTN2')?>';
					var repltext2 = '<?=$ui->item('CARTNEW_IN_CART_BTN')?>';
					var repltext3 = '<?=$ui->item('ALREADY_IN_CART')?>';
					
					repltext = repltext.replace('%d', post['quantity']);
					repltext2 = repltext2.replace('%d', post['quantity']);
					repltext3 = repltext3.replace('%d', post['quantity']);
					
					$('a.cartMini'+data.ID()+' span').html(repltext);
					$('a.cart'+data.ID()+' span').html(repltext2);
					
					$('div.already-in-cart'+data.ID()).html(repltext3).replace;
					
                    if(json.changed){
                        data.InfoField(json.changedStr);
                        alert(json.changedStr);
                    }
                    else data.InfoField('');
                    data.Quantity(json.quantity);
                    update_header_cart();
                }, 'json');
			}
        };

		self.QuantityChangedPlus = function (data, event) {
			$('input', $(self).parent().parent()).val(parseInt($('input',$(self).parent().parent()).val()) + 1);
            var post = {
                entity: data.Entity(),
                id: data.ID(),
                quantity: parseInt(data.Quantity()) + 1,
                type : data.Price2Use()
            };
            post[csrf_1[0]] = csrf_1[1];
			
			
			
            $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json) {
				
				var repltext = '<?=$ui->item('CARTNEW_IN_CART_BTN2')?>';
					var repltext2 = '<?=$ui->item('CARTNEW_IN_CART_BTN')?>';
					var repltext3 = '<?=$ui->item('ALREADY_IN_CART')?>';
					
					repltext = repltext.replace('%d', post['quantity']);
					repltext2 = repltext2.replace('%d', post['quantity']);
					repltext3 = repltext3.replace('%d', post['quantity']);
					
					$('a.cartMini'+data.ID()+' span').html(repltext);
					$('a.cart'+data.ID()+' span').html(repltext2);
					
					$('div.already-in-cart'+data.ID()).html(repltext3).replace;
				if(json.changed) data.InfoField(json.changedStr);
                else data.InfoField('');
                data.Quantity(json.quantity);
				update_header_cart();
            }, 'json');
        };

        self.QuantityChanged = function (data, event) {
            var post = {
                entity: data.Entity(),
                id: data.ID(),
                quantity: data.Quantity(),
                type : data.Price2Use()
            };
            post[csrf_1[0]] = csrf_1[1];
			
			//alert('11');
			
            $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json) {
                
				var repltext = '<?=$ui->item('CARTNEW_IN_CART_BTN2')?>';
					var repltext2 = '<?=$ui->item('CARTNEW_IN_CART_BTN')?>';
					var repltext3 = '<?=$ui->item('ALREADY_IN_CART')?>';
					
					repltext = repltext.replace('%d', post['quantity']);
					repltext2 = repltext2.replace('%d', post['quantity']);
					repltext3 = repltext3.replace('%d', post['quantity']);
					
					$('a.cartMini'+data.ID()+' span').html(repltext);
					$('a.cart'+data.ID()+' span').html(repltext2);
					
					$('div.already-in-cart'+data.ID()).html(repltext3).replace;
				if(json.changed) data.InfoField(json.changedStr);
                else data.InfoField('');
                data.Quantity(json.quantity);
				update_header_cart();
            }, 'json');
        };

        self.RequestQuantityChanged = function (data, event) {
            var post = {
                entity: data.Entity(),
                id: data.ID(),
                quantity: data.Quantity()
            };
            post[csrf_1[0]] = csrf_1[1];

            $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json) {
                data.Quantity(json.quantity);
            }, 'json');
        };
    };
    var cvm_1 = new cartVM_1();

    $(document).ready(function() {
        update_header_cart();
        ko.applyBindings(cvm_1, document.getElementById('cart_renderpartial'));
    });

    $(document).ajaxStart(function() {
        cvm_1.AjaxCall(true)
    }).ajaxComplete(function() {
        cvm_1.AjaxCall(false);
    });
</script>