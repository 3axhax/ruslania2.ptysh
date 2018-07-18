<?php
/**
 * @var $CartItems IteratorsCart в корзине, когда корзина пуста, то в итераторе лежит товар с пустыми данными для js
 * @var $EndedItems IteratorsCart в корзине, но тираж закончился
 * @var $isCart bool признак - козина (false - пуста)
 * @var $isEnded bool
 */

KnockoutForm::RegisterScripts();


$url = explode('?', $_SERVER['REQUEST_URI']);
$url = trim($url[0], '/');

$ex = explode('?', $url);

$ex = explode('/', $ex[0]);

$url = $ex;

?>
            <hr />
<div class="container cabinet">
			
			<div class="row">

			<div class=" <? if (!in_array('cart',$url)) : ?>span10<? else : ?><? endif; ?>">
			
			<h1 class="title">Корзина</h1>
            <div id="cart">
			
				<p style="display: none"data-bind="visible: CartItems().length > 0">
                                    <?//=$ui->item('new (shopping cart)'); ?>
                                </p>
			
                <div data-bind="visible: FirstLoad" class="center cartload"<?php if($isCart):?> style="display: none;"<?php endif; ?>>
                    <?=$ui->item('CART_IS_LOADING'); ?><br/><br/>
                    <img src="/pic1/loader.gif"/>
                </div>

                <!-- content -->
                <form action="/cart/doorder" method="get" onsubmit="return false;">
                <div data-bind="visible:  CartItems().length > 0">
                    
                    
                    <a href="/cart/variants" class="order_start" style="background-color: rgb(237, 32, 36); float: right; margin-top: -65px;" data-bind="visible: CartItems().length > 0 &amp;&amp; !AjaxCall() &amp;&amp; TotalVAT() > 0">
                                    <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;">Оформить заказ</span>
                                </a>
                    
                    
                    <table width="100%" cellspacing="1" cellpadding="5" border="0" class="cart1 items_tbl"
                           style="margin-bottom: 10px; margin-top: 15px;">
                        <thead>
                        <tr>
                            <th valign="middle"
                                class="cart1header1"><?= $ui->item("CART_COL_TITLE"); ?></th>
								<th valign="middle" align="center" class="cart1header1"><?=$ui->item('SHIPPING'); ?></th>
                            <!--<th valign="middle" align="center"  style="width:70px;"
                                class="cart1header1"><?= $ui->item("Price"); ?></th>-->
                            <th valign="middle" align="center"  style="width:80px;"
                                class="cart1header1"><?= $ui->item("CART_COL_QUANTITY"); ?></th>
                            <th valign="middle" align="center"  style="width:70px;"
                                class="cart1header1"><?= $ui->item("CART_COL_PRICE"); ?></th>
                         <!--   <th valign="middle" align="center"  style="width:80px;"
                                class="cart1header1"><?= $ui->item("CART_COL_ITEM_MOVE_TO_SUSPENDED"); ?></th>-->
                            <th valign="middle" align="center" style="width:70px;"
                                class="cart1header1"><?= $ui->item("CART_COL_DELETE"); ?></th>
                        </tr>
                        </thead>
                        <tbody class="items"><!-- ko foreach: CartItems -->
                        <?php foreach($CartItems as $i=>$cart): ?>
                        <tr<?php if (!empty($i)): ?> class="js_ko_not"<?php endif; ?>>
                            <td valign="middle" class="cart1contents1">
                                <table>
								<tr>
								<td>
								<img width="31" height="31" align="middle"
                                     alt="<?= htmlspecialchars($cart['Title']) ?>" style="vertical-align: middle"
                                     data-bind="attr: { alt: Title}"
                                     src="/pic1/cart_ibook.gif">
								</td>
								<td style="padding-left: 20px;">
                                    <div><a href="<?= $cart['Url'] ?>" title="<?= htmlspecialchars($cart['Title']) ?>"
                                        data-bind="attr: { href: Url, title: Title},text: Title"
                                        class="maintxt1"><?= htmlspecialchars($cart['Title']) ?>
                                    </a></div>
                                    <div data-bind="text: Authors"><?= $cart['Authors'] ?></div>
                                <p class="cartInfo" data-bind="text: InfoField, visible: InfoField() != null && InfoField().length > 0 "><?= htmlspecialchars($cart['InfoField']) ?></p>
								</td>
								</tr>
								</table>
                            </td>
							 <td valign="middle" align="center" class="cart1contents1">
                <span data-bind="text: AvailablityText"><?= htmlspecialchars($cart['AvailablityText']) ?></span>
            </td>
                           <!-- <td valign="middle" nowrap="true" align="center" class="cart1contents1 center">
                                <span data-bind="text: $root.ReadyPriceStr($data), visible: DiscountPercent() == 0"><?= ((float)$cart['DiscountPercent']>0)?'':$cart['ReadyPriceStr'] ?></span>
                                <div data-bind="visible: DiscountPercent() > 0">
                                    <s data-bind="text: PriceOriginal"><?= ((float)$cart['DiscountPercent']>0)?$cart['PriceOriginal']:'' ?></s><br/>
                                    <span data-bind="text: $root.ReadyPriceStr($data)"><?= ((float)$cart['DiscountPercent']>0)?$cart['ReadyPriceStr']:'' ?></span>
                                </div>

                            </td>-->
<!--                            <td align="center" class="center cart1contents1">-->
<!--                                <span data-bind="text: VAT"></span>%-->
<!--                            </td>-->
                            <td valign="middle" align="center" class="cart1contents1" nowrap>
                               <a href="javascript:;" style="margin-right: 9px;" data-bind="event : { click : $root.QuantityChangedMinus }"><img src="/new_img/cart_minus.png" class="grayscale"/></a>
                                
                                <input name="quantity[<?= (int) $cart['ID'] ?>]" type="text" size="3" class="cart1contents1 center" value="<?= (int) $cart['Quantity'] ?>" style="margin: 0;" data-bind="value: Quantity, event : { blur : $root.QuantityChanged }, id : 'field'"> <a href="javascript:;" style="margin-left: 9px;"><img src="/new_img/cart_plus.png" data-bind="event : { click : $root.QuantityChangedPlus }"/></a>
                                <input<?php if (empty($i)): ?> class="js_ko_not"<?php endif; ?> type="hidden" name="entity[<?= (int) $cart['ID'] ?>]" value="<?= (int) $cart['Entity'] ?>">
                                <input<?php if (empty($i)): ?> class="js_ko_not"<?php endif; ?> type="hidden" name="type[<?= (int) $cart['ID'] ?>]" value="<?= (int) $cart['Price2Use'] ?>">
                            </td>
                            <td valign="middle" nowrap="" align="center" class="cart1contents1">
                                <span data-bind="text: $root.LineTotalVAT($data)"><?= $cart['LineTotalVAT'] ?></span>
                                <?=Currency::ToSign(Yii::app()->currency); ?>
                            </td>
                           <!-- <td valign="middle" align="center" class="cart1contents1">
                                <a href="javascript:;" data-bind="click: $root.ToMark"><img src="/new_img/add_mark.png" /></a>
                            </td> -->
                            <td valign="middle" align="center" class="cart1contents1">
                                <a href="javascript:;" data-bind="click: function(data, event) { cvm.RemoveFromCart(data, <?=Cart::TYPE_ORDER; ?>); }"><img src="/new_img/del_cart.png" /></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- /ko --></tbody>
                        <tr class="footer">
                            <td align="right" class="cart1header2" colspan="6"><div class="summa"><?=$ui->item('CART_COL_TOTAL_PRICE'); ?>, <span data-bind="text: IsVATInPrice()"><?= $CartItems->isVATInPrice() ?></span>:
								<span style="font-weight: bold;" data-bind="visible: !AjaxCall()">
                                        <span data-bind="text: TotalVAT"><?= $CartItems->totalVAT() ?></span>
                                        <?= Currency::ToSign(Yii::app()->currency); ?>
                                </span>
                                <span data-bind="visible: AjaxCall" style="display: none;"><?=$ui->item('UPDATING'); ?></span>
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
                            <td style="padding-left: 0;"> <a href="/" style="float: left; color: #ff0000;">Продолжить покупки</a></td>
                            <td colspan="7" class="order_start_box" class="cart1header2" align="right">
                            
                            <?php if (Yii::app()->user->isGuest) { ?>
                           
                                <a href="/cart/variants" class="order_start" style="background-color: #ed2024" data-bind="visible: CartItems().length > 0 && !AjaxCall() && TotalVAT() > 0">
                                    <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?= $ui->item("CONFIRM_ORDER"); ?></span>
                                </a>
                                 
                                <?php
                                
                                } else {
                                   ?>
                                   <a href="/cart/doorder" class="order_start" style="background-color: #ed2024" data-bind="visible: CartItems().length > 0 && !AjaxCall() && TotalVAT() > 0">
                                    <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?= $ui->item("CONFIRM_ORDER"); ?></span>
                                </a>
                                   <? 
                                }
                                
                                ?>
                                
                            </td>
                        </tr>
                    </table>


                </div>
                </form>

                <table border="0" cellspacing="5" data-bind="visible: CartItems().length == 0 && !FirstLoad()"<?php if ($isCart):?> style="display: none;" <?php endif; ?>>
                    <tr>
                        <td class="maintxt"><?= $ui->item("MSG_CART_ERROR_EMPTY"); ?></td>
                    </tr>
                </table>

                <div data-bind="visible:  EndedItems().length > 0" class="information info-box"<?php if (!$isEnded):?> style="display: none;" <?php endif; ?>>
                    <p>
                    <?=$ui->item('MOVED_TO_WAITING_LIST'); ?>
                    </p>
                    <ul data-bind="foreach: EndedItems">
                        <?php foreach($EndedItems as $cart): ?>
                        <li><span data-bind="text: Title"><?= htmlspecialchars($cart['Title']) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <!-- /content -->
        </div>

                            
                             <? if (!in_array('cart',$url)) : ?>
                            
                            
                <div class="span2">

             				<?php $this->renderPartial('/site/_me_left'); ?>

             			</div>
                            
                            <? endif; ?>
        </div>
    
    
    
    <?php
    
    $o = new Offer;
    
    $groups = $o->GetItemsAll(999);
    
    //echo var_dump($groups);
    ?>
    
    <script type="text/javascript">
        $(document).ready(function () {
            $('.more_goods ul').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 4,
                slidesToScroll: 4
            });
        });
    </script>

<div class="news_box" style="margin-top: 150px;">


		<div class="">
			<div class="title">
				Добавьте выгодные предложения в корзину      
				<div class="pult">
					<a href="javascript:;" onclick="$('.news_box .btn_left.slick-arrow').click()" class="btn_left"><img src="/new_img/btn_left_news.png" alt=""></a>
					<a href="javascript:;" onclick="$('.news_box .btn_right.slick-arrow').click()" class="btn_right"><img src="/new_img/btn_right_news.png" alt=""></a>
				</div>
			</div>
		</div>
    
    <div class="more_goods" style="overflow: hidden">
    <ul class="books">
    <?php

	foreach ($groups as $k) : 
	
	if ($k['entity'] == '') { $k['entity'] = 10; }
	
	?>
	
	<?php
	
	$product = Product::GetProduct($k['entity'], $k['id']);
	$url = ProductHelper::CreateUrl($product);
	
	
	echo  '	<li>
        
    <div class="img" style="min-height: 130px; position: relative">';
        $this->renderStatusLables($product['status']);
    echo '<a href="'.$url.'" title="'.ProductHelper::GetTitle($product, 'title', 42).'"><img title="'.ProductHelper::GetTitle($product, 'title', 42).'" alt="'.ProductHelper::GetTitle($product, 'title', 42).'" src="'.Picture::Get($product, Picture::SMALL).'" alt=""  style="max-height: 130px;"/></a>
    </div>
 
	<div class="title_book"><a href="'.$url.'" title="'.ProductHelper::GetTitle($product, 'title', 42).'">'.ProductHelper::GetTitle($product, 'title', 42).'</a></div>';
		
		if ($product['isbn']) {
			echo '<div>ISBN: '.str_replace('-', '' ,$product['isbn']).'</div>';
		}
		
		if ($product['year']) {
			
			echo '<div>'.$ui->item('A_NEW_YEAR') . ': ' . $product['year'].'</div>';
			
		}
		
		if ($product['binding_id']) {
		
		$row = Binding::GetBinding($k['entity'], $product['binding_id']);
		echo $row['title_' . Yii::app()->language];	
		
		}
		
		$price = DiscountManager::GetPrice(Yii::app()->user->id, $product);
	
		echo '
        
    	<div class="cost">';
		if (!empty($price[DiscountManager::DISCOUNT])) :
            echo '<span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;">'.ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]).'
            </span>&nbsp;<span class="price" style="color: #301c53;font-size: 18px; font-weight: bold;">
                '.ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]).'
            </span>';

        else :

            echo '<span class="price">'.ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]).'
        
        </span>';

        endif;
	echo '</div>
                    <div class="nds">'. ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]) . $ui->item('WITHOUT_VAT') .'</div>
                    <div class="addcart">
                        <a class="cart-action" data-action="add" data-entity="'. $k['entity'] .'"
               data-id="'. $k['id'] .'" data-quantity="1"
               href="javascript:;">'.$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART').'</a>
                    </div>                   </li>'; ?>


					
					
		<?php endforeach; ?>			
</ul>
</div>


</div>
</div>



<script type="text/javascript">

    var csrf = $('meta[name=csrf]').attr('content').split('=');

    var cartVM = function ()
    {
        $('.js_ko_not').remove();
        var self = this;
        self.FirstLoad = ko.observable(false);
        ko.mapping.fromJS(<?= json_encode(array(
            'CartItems'=>$isCart?$CartItems->getArrayCopy():array(),
            'EndedItems'=>$isEnded?$EndedItems->getArrayCopy():array(),
        )) ?>, {}, self);

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
                        url: '<?=Yii::app()->createUrl('cart/')?>mark',
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
                            url: '<?=Yii::app()->createUrl('cart/')?>remove',
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

            $.post('<?=Yii::app()->createUrl('cart/')?>changequantity', post, function (json)
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

            $.post('<?=Yii::app()->createUrl('cart/')?>changequantity', post, function (json)
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

            $.post('<?=Yii::app()->createUrl('cart/')?>changequantity', post, function (json)
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

            $.post('<?=Yii::app()->createUrl('cart/')?>changequantity', post, function (json)
            {
//                console.log(json);
                data.Quantity(json.quantity);
				update_header_cart();
            }, 'json');
        };
		
		
    };

    var cvm = new cartVM();
//    CartFirstState = $('#cart').clone();
    
    ko.applyBindings(cvm, $('#cart')[0]);
//    $(document).ready(function () {
//        var data = { language: '<?//=Yii::app()->language; ?>//'};
//        $.getJSON('/cart/getall', data, function (json)
//        {
//            ko.mapping.fromJS(<?//= json_encode(array(
//                'CartItems'=>$isCart?$CartItems->getArrayCopy():array(),
//                'EndedItems'=>$isEnded?$EndedItems->getArrayCopy():array(),
//            )) ?>//, {}, cvm);
//            cvm.FirstLoad(false);
//        console.log(cvm.CartItems);
//        });
//    });
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


<div style="height: 20px;"></div>