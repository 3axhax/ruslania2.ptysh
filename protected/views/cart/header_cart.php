<? if ($_SERVER['REQUEST_URI'] != '/cart' AND $_SERVER['REQUEST_URI'] != '/cart/doorder') {  ?>
<?php KnockoutForm::RegisterScripts(); ?>

                <div class="b-basket-list">
							
								<div class="b-basket-list__empty" data-bind="visible: CartItems().length < 1"><span><?=$ui->item('A_NEW_CART_INFO')?></span>
							</div>
                            <div class="b-basket-list__center" data-bind="foreach: CartItems">
							
							
							
                           <!-- ko if: $index() < 3 -->                     
						<div class="b-basket-list__item">
						
									<div class="alert" data-bind="attr: { class: 'alert alert'+ID()}" >
										<div style="margin: 5px;">
											<div class="title"><?=$ui->item('ARE_YOU_SURE'); ?></div>
											
											<div style="text-align: center; margin-top: 5px;">
												<a href="javascript:;" class="btn_yes" style="margin-right: 20px;"><?=$ui->item('A_NEW_BTN_YES')?></a>
												<a href="javascript:;" onclick="javascript:;" class="btn_no"><?=$ui->item('A_NEW_BTN_NO')?></a>
											</div>
											
										</div>
									</div>
						
						
                                    <div class="b-basket-list__img-wrapper">
									<div class="b-basket-list__img"><img width="31" height="31" align="middle"
                                     alt="" style="vertical-align: middle"
                                     src="/pic1/cart_ibook.gif"></div>
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
                                        <a href="javascript:;" style="margin-right: 9px;" data-bind="event : { click : $root.QuantityChangedMinus }, visible: noUseChangeQuantity() == 0"><img src="/new_img/cart_minus.png" class="grayscale"/></a>
                                        <input type="text" size="3" class="cart1contents1 center" style="margin: 0; width: 50px;" data-bind="value: Quantity, event : { blur : $root.QuantityChanged }, id : 'field'">
                                        <div style="display:none;width:25px;float:left;" data-bind="visible: noUseChangeQuantity() > 0">&nbsp;</div>
                                        <a href="javascript:;" style="margin-left: 9px;"><img src="/new_img/cart_plus.png" data-bind="event : { click : $root.QuantityChangedPlus }, visible: noUseChangeQuantity() == 0"/></a>
                                    </div>
                                    <div class="b-basket-list__cross js-close-item" data-bind="click: function(data, event) { cvm_1.RemoveFromCart(data, <?=Cart::TYPE_ORDER; ?>); }"></div>
									
									
									
									
                                </div>
						<!-- /ko -->
						
						
						
                    </div>
					
					<div class="b-basket-list__bottom">
                                <div class="b-basket-list__load-wrapp"><a class="b-basket-list__load-btn" href="<?=Yii::app()->createUrl('cart/view'); ?>"  data-bind="text: '<?=$ui->item('A_NEW_CART_MORE_ORDER1')?> '+priceStrToPrice(CartItems().length-3)+' ', visible: CartItems().length > 3"></a></div>
                                <div class="b-basket-list__order-wrapp" data-bind="visible: CartItems().length > 0"><a class="b-basket-list__order-btn" href="<?=Yii::app()->createUrl('cart/view')?>"><?=$ui->item('CONFIRM_ORDER');?></a></div>
                            </div>
                            </div>

				
<script type="text/javascript">

    var csrf_1 = $('meta[name=csrf]').attr('content').split('=');

    var cartVM_1 = function ()
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

        self.priceStrToPrice = function(count)
        {

            var num, out;

            num = count % 100;
            if (num > 19) {
                num = num % 10;
            }

            //$out = (show) ?  $value . ' ' : '';
            switch (num) {
                case 1:
                    out = count+' товар';
                    break;
                case 2:
                case 3:
                case 4:
                    out = count+' товара';
                    break;
                default:
                    out = count+' товаров';
                    break;
            }
            return out;

        }

        self.ReadyPriceStr = function(item)
        {
			
			return item.PriceVATStr();
			
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

        self.RemoveFromCart = function(item, type)
        {
            //if(confirm('<?=$ui->item('ARE_YOU_SURE'); ?>'))
            //{
				
				$('.b-basket-list .alert.alert'+item.ID()).fadeIn(240);
				
				$('.b-basket-list .alert.alert'+item.ID()+' .btn_no').on('click', function() { $('.b-basket-list .alert.alert'+item.ID()).fadeOut(240); })
				
				$('.b-basket-list .alert.alert'+item.ID()+' .btn_yes').on('click', function() { 
				
					var obj =
                {
                    entity : item.Entity(),
                    iid : item.ID(),
                    type : type
                };
				
                obj[csrf_1[0]] = csrf_1[1];
                $.when
                    (
                        $.ajax({
                            type: "POST",
                            url: '<?=Yii::app()->createUrl('cart/remove')?>',
                            data: obj,
                            dataType: 'json',
							success: function() {
								update_header_cart();
							}
                        })
                    ).then(function(json)
                    {
                        if(!json.hasError)
                        {
                            //if(type == <?=Cart::TYPE_ORDER; ?>)  self.CartItems.remove(item);
                            //else self.RequestItems.remove(item);
							
							
							$('.b-basket-list').show();
							
                        }
                    });
				

				})
				
                
                  
				  
                
           // }
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
                decrement: 1,
                type : data.Price2Use()
            };
            post[csrf_1[0]] = csrf_1[1];

            $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json)
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
            post[csrf_1[0]] = csrf_1[1];

            $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json)
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
            post[csrf_1[0]] = csrf_1[1];

            $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json)
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
            post[csrf_1[0]] = csrf_1[1];

            $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json)
            {
//                console.log(json);
                data.Quantity(json.quantity);
            }, 'json');
        };
    };
    
    
    var cvm_1 = new cartVM_1();
    
    var first_cvm = cvm_1;

    firstState = $('#cart_renderpartial').clone();        
    
    ko.applyBindings(cvm_1, $('#cart_renderpartial')[0]);
    
    
    $(document).ready(function ()
    {
        
        var data = { language: '<?=Yii::app()->language; ?>', is_MiniCart: 1};
        $.getJSON('<?=Yii::app()->createUrl('cart/getall')?>', data, function (json)
        {
            ko.mapping.fromJS(json, {}, cvm_1);
           
            cvm_1.FirstLoad(false);
            
        });
    });

    $(document).ajaxStart(function ()
    {
        cvm_1.AjaxCall(true)
    }).ajaxComplete(function ()
        {
            cvm_1.AjaxCall(false);
			
        });
        
        

    

</script>		<? } ?>		