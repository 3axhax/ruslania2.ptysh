<?php
/**
 * @var $CartItems IteratorsCart в корзине, когда корзина пуста, то в итераторе лежит товар с пустыми данными для js
 * @var $EndedItems IteratorsCart в корзине, но тираж закончился
 * @var $isCart bool признак - козина (false - пуста)
 * @var $isEnded bool
 */

KnockoutForm::RegisterScripts();


$ctrl = Yii::app()->getController()->id;

?>
            <hr />
			
<div class="opacity alerthtml" onclick="$('.alerthtml').hide();"></div>			



			
<div class="container cabinet">
			
			<div class="row" style="margin-left: 0">

			<div class=" <? if ($ctrl != 'cart') : ?>span10<? else : ?><? endif; ?>">
			
			<h1 class="title"><?=$ui->item('A_NEW_CART')?></h1>
            <div id="cart">
			
				<p style="display: none"data-bind="visible: CartItems().length > 0">
                                    <?//=$ui->item('new (shopping cart)'); ?>
                                </p>
			
                <div data-bind="visible: FirstLoad" class="center cartload"<?php if($isCart):?> style="display: none;"<?php endif; ?>>
                    <?=$ui->item('CART_IS_LOADING'); ?><br/><br/>
                    <img src="/pic1/loader.gif"/>
                </div>

                <!-- content -->
                <form action="<?= Yii::app()->createUrl('cart/doorder') ?>" method="get" onsubmit="return false;">
                <div data-bind="visible:  CartItems().length > 0">

                    <?php if (Yii::app()->user->isGuest) { ?>

                        <a href="<?= Yii::app()->createUrl('cart/variants')?>" class="order_start" style="background-color: #5bb75b; float: right; margin-top: -65px;" data-bind="visible: CartItems().length > 0 && !AjaxCall() && TotalVAT() > 0">
                            <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?=$ui->item('CARTNEW_CONTINUE_SHOPPING_BTN');?></span>
                        </a>

                        <?php

                    } else {
                        ?>
                        <a href="<?= Yii::app()->createUrl('cart/doorder')?>" class="order_start" style="background-color: #5bb75b; float: right; margin-top: -65px;" data-bind="visible: CartItems().length > 0 && !AjaxCall() && TotalVAT() > 0">
                            <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?=$ui->item('CARTNEW_CONTINUE_SHOPPING_BTN');?></span>
                        </a>
                        <?
                    }

                    ?>

                    

                    
                    
                    <table width="100%" cellspacing="1" cellpadding="5" border="0" class="cart1 items_tbl rows_number"
                           style="margin-bottom: 10px; margin-top: 15px;">
                        <thead>
                        <tr>
                            <th valign="middle" class="cart1header1"><?= $ui->item("CART_COL_TITLE"); ?></th>
								<th valign="middle" align="center" class="cart1header1"><?=$ui->item('CARTNEW_TITLE_TABLE_AVAILABILITY')?><?//=$ui->item('SHIPPING'); ?></th>
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
                        <?php foreach($CartItems as $i=>$cart): 
						
						
						
						?>
                        <tr<?php if (!empty($i)): ?> class="js_ko_not"<?php endif; ?>>
                            <td valign="middle" class="cart1contents1 index_number">
                                <table>
								<tr>
								<td>
                                    <span class="entity_icons"><i class="fa e<?= $cart['Entity'] ?>" data-bind="attr: { class: 'fa e'+Entity()}"></i></span>
								</td>
								<td style="padding-left: 20px;">
                                    <div>
                                        <a href="<?= $cart['Url'] ?>" title="<?= htmlspecialchars($cart['Title']) ?>"
                                        data-bind="attr: { href: Url, title: Title},text: Title"
                                        class="maintxt1" target="_blank"><?= htmlspecialchars($cart['Title']) ?>
                                    </a>
                                    </div>
                                    <div data-bind="text: ISBN"><?= $cart['ISBN'] ?></div>
                                    <!--<div data-bind="text: Authors"><?= $cart['Authors'] ?></div>-->
                                <p class="cartInfo" data-bind="text: InfoField, visible: InfoField() != null && InfoField().length > 0 "></p>
								</td>
								</tr>
								</table>
                            </td>
							 <td valign="middle" align="center" class="cart1contents1">
                <span data-bind="text: AvailablityText"><?= htmlspecialchars($cart['AvailablityText']) ?></span>
            </td>
                            <td valign="middle" align="center" class="cart1contents1 minus_plus" nowrap>
                               <a href="javascript:;" style="margin-right: 9px;" data-bind="event : { click : $root.QuantityChangedMinus }, visible: noUseChangeQuantity() == 0"><?php /*<img src="/new_img/cart_minus.png" class="grayscale"/> */?></a>
                                <input name="quantity[<?= (int) $cart['ID'] ?>]" type="text" size="3" class="cart1contents1 center" value="<?= (int) $cart['Quantity'] ?>" style="margin: 0;" data-bind="value: Quantity, event : { blur : $root.QuantityChanged }, id : 'field'">
                                <div style="width:12px;float:left;" data-bind="visible: noUseChangeQuantity() > 0">&nbsp;</div>
                                <a href="javascript:;" style="margin-left: 9px;" data-bind="event : { click : $root.QuantityChangedPlus }, visible: noUseChangeQuantity() == 0"><?php /*<img src="/new_img/cart_plus.png"/>*/ ?></a>
                                <input<?php if (empty($i)): ?> class="js_ko_not"<?php endif; ?> type="hidden" name="entity[<?= (int) $cart['ID'] ?>]" value="<?= (int) $cart['Entity'] ?>">
                                <input<?php if (empty($i)): ?> class="js_ko_not"<?php endif; ?> type="hidden" name="type[<?= (int) $cart['ID'] ?>]" value="<?= (int) $cart['Price2Use'] ?>">
                            </td>
                            <td valign="middle" nowrap="" align="center" class="cart1contents1">
                                <div data-bind="visible: DiscountPercent() > 0, text: PriceOriginal()" style="text-decoration: line-through;<?php if (empty($cart['DiscountPercent'])): ?> display: none;<?php endif; ?>">
                                    <?= $cart['PriceOriginal'] ?>
                                </div>
                                <div>
                                    <span data-bind="text: $root.LineTotalVAT($data)"><?= $cart['LineTotalVAT'] ?></span>
                                    <?=Currency::ToSign(Yii::app()->currency); ?>
                                </div>
                            </td>
                            <td valign="middle" align="center" class="cart1contents1">
                                <a href="javascript:;" onclick="$('.alerthtml',$(this).parent()).show(); $('.opacity.alerthtml').show(); $('.box_btns .btn_yes').focus()"><img src="/new_img/del_cart.png" /></a>
								
								<div class="lang_yesno_box alerthtml" style="display: none;margin-left: -181px;width: 220px;">

	<div style="text-align: center;"><?=$ui->item('ARE_YOU_SURE'); ?></div>
	<div class="box_btns">
		<a href="javascript:;" data-bind="click: function(data, event) { cvm.RemoveFromCart(data, <?=Cart::TYPE_ORDER; ?>); }" class="btn_yes"><?= $ui->item('MSG_YES') ?></a> <a href="javascript:;" onclick="$('.alerthtml').hide(); " class="btn_no"><?= $ui->item('MSG_NO') ?></a>
	</div>

</div>
								
                            </td>
                        </tr>
						
						<? $weight = $weight + $cart['UnitWeight'];?>
						
                        <?php endforeach; ?>
                        <!-- /ko --></tbody>
                        <tr class="footer">
                            <td align="right" class="cart1header2" colspan=
							"6">
							
							<?//=$weight?>
							
							<div class="summa">
							
							<? if ($weight > 0) : ?>
							
							<?
							echo $ui->item('CART_COL_TOTAL_PRICE'); ?>, <span data-bind="text: IsVATInPrice()"><?= $CartItems->isVATInPrice() ?></span>:
							
							<? else : ?>
							
							<?
							echo $ui->item('CART_COL_TOTAL_PRICE2'); ?> <span data-bind="text: IsVATInPrice()"><?= $CartItems->isVATInPrice() ?></span>:
							
							<? endif; ?>
							
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
						
							<? 
				
				/*$url_ref = end(explode('/', trim($_SERVER['HTTP_REFERER'], '/')));
				
				$arr_cart_url = array('variants', 'noregister', 'doorder');
				
				if (in_array($url_ref, $arr_cart_url)) {
					
					$_SERVER['HTTP_REFERER'] = '/';
					
				}*/

				?>
						
                            <td style="padding-left: 0;"> <a href="<?= $hrefContinueShopping ?>" style="float: left; color: #ff0000; font-size: 14px;" onclick="searchTargets('continue_pokupka');"><?=$ui->item('CARTNEW_CONTINUE_SHOPPING');?></a></td>
                            <td colspan="7" class="order_start_box" class="cart1header2" align="right">

                                <?php if (Yii::app()->user->isGuest) { ?>

                                    <a href="<?= Yii::app()->createUrl('cart/variants')?>" class="order_start" style="background-color: #5bb75b;" data-bind="visible: CartItems().length > 0 && !AjaxCall() && TotalVAT() > 0">
                                        <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?=$ui->item('CARTNEW_CONTINUE_SHOPPING_BTN');?></span>
                                    </a>

                                    <?php

                                } else {
                                    ?>
                                    <a href="<?= Yii::app()->createUrl('cart/doorder')?>" class="order_start" style="background-color: #5bb75b; " data-bind="visible: CartItems().length > 0 && !AjaxCall() && TotalVAT() > 0">
                                        <span style="border: none; background: none; padding: 0; color:#fff; font-weight: bold;"><?=$ui->item('CARTNEW_CONTINUE_SHOPPING_BTN');?></span>
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

                            
                             <? if ($ctrl != 'cart') : ?>
                            
                            
                <div class="span2">

             				<?php $this->renderPartial('/site/_me_left'); ?>

             			</div>
                            
                            <? endif; ?>
        </div>
    
    
    
    <?php
    
    $o = new Offer;
    
    $groups = $o->GetItemsAll(999);
    
    //echo var_dump($groups);
	
	if ( $groups ) {
	
    ?>
    
<div class="news_box" style="margin-top: 60px;">


		<div class="">
			<div class="title">
				<?=$ui->item('CARTNEW_ADD_CART_2EURO_TITLE')?>      
				<div class="pult">
					<a href="javascript:;" onclick="$('.news_box .btn_left.slick-arrow').click()" class="btn_left"><span class="fa"></span></a>
					<a href="javascript:;" onclick="$('.news_box .btn_right.slick-arrow').click()" class="btn_right"><span class="fa"></span></a>
				</div>
			</div>
		</div>
    
    <div class="more_goods" style="overflow: hidden">
    <ul class="books basket">
    <?php
	foreach ($groups as $k) :
	
	if ($k['entity'] == '') { $k['entity'] = 10; }
	
	?>
	
	<?php
	
	$product = Product::GetProduct($k['entity'], $k['id']);
	$url = ProductHelper::CreateUrl($product); ?>
	
	
	<li>
    <div class="img" style="min-height: 130px; position: relative"><?php
        $this->renderStatusLables($product['status']);
    echo '<a href="'.$url.'" title="'.htmlspecialchars(ProductHelper::GetTitle($product, 'title', 42)).'" target="_blank">';
        $photoTable = Entity::GetEntitiesList()[$product['entity']]['photo_table'];
        $modelName = mb_strtoupper(mb_substr($photoTable, 0, 1, 'utf-8'), 'utf-8') . mb_substr($photoTable, 1, null, 'utf-8');
        /**@var $photoModel ModelsPhotos*/
        $photoModel = $modelName::model();
        $photoId = $photoModel->getFirstId($product['id']);
        ?>
        <?php if (empty($photoId)): ?>
        <img alt="<?= htmlspecialchars(ProductHelper::GetTitle($product, 'title', 42)) ?>" src="<?= Picture::Get($product, Picture::SMALL) ?>" data-lazy="<?= Picture::Get($product, Picture::SMALL) ?>" style="max-height: 130px;"/>
        <?php else: ?>
        <picture>
            <source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $product['eancode'], 'webp') ?>" type="image/webp">
            <source srcset="<?= $photoModel->getHrefPath($photoId, 'si', $product['eancode'], 'jpg') ?>" type="image/jpeg">
            <img src="<?= $photoModel->getHrefPath($photoId, 'o', $product['eancode'], 'jpg') ?>" alt="<?= htmlspecialchars(ProductHelper::GetTitle($product, 'title', 42)) ?>"  style="max-height: 130px;"/>
        </picture>
        <?php endif; ?>

    <?php echo '</a>
    </div>
 
	<div class="title_book" style="width: auto;"><a href="'.$url.'" title="'.htmlspecialchars(ProductHelper::GetTitle($product, 'title', 42)).'" target="_blank">'.ProductHelper::GetTitle($product, 'title', 42).'</a></div>';
		
		if ($product['isbn']) {
			echo '<div>ISBN: '.str_replace('-', '' ,$product['isbn']).'</div>';
		}
    else {
    echo '<div style="visibility: hidden;">ISBN:</div>';
    }
		
		if ($product['year']) {
			
			echo '<div>'.$ui->item('A_NEW_YEAR') . ': ' . $product['year'].'</div>';
			
		}
        else {
        echo '<div style="visibility: hidden;">YEAR</div>';
        }
		
		if ($product['binding_id']) {
		
		$row = Binding::GetBinding($k['entity'], $product['binding_id']);
		echo $row['title_' . Yii::app()->language];	
		
		}
        else {
        echo '<div style="visibility: hidden;">binding</div>';
        }

		$price = DiscountManager::GetPrice(Yii::app()->user->id, $product);
		echo '
        
    	<div class="cost">';
		if (!empty($price[DiscountManager::DISCOUNT])) :
            echo '<span class="without_discount">'.ProductHelper::FormatPrice($price[DiscountManager::BRUTTO]).'
            </span>&nbsp;<span class="price with_discount"' . (($price[DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL)?' style="color: #42b455;"':'') . '>
                '.ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]).'
            </span>';

        else :

            echo '<span class="price">'.ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT]).'
        
        </span>';

        endif;
	echo '</div>
                    <div class="nds">'. ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT]) . ' ' . $ui->item('WITHOUT_VAT') .'</div>
                    <div class="addcart">
                        <a class="cart-action" data-action="add" data-entity="'. $k['entity'] .'"
               data-id="'. $k['id'] .'" data-quantity="1"
               href="javascript:;"  onclick="searchTargets(\'add_cart_cart\');">'.$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART').'</a>
                    </div>                   </li>'; ?>


					
					
		<?php endforeach; ?>
</ul>
</div>


</div>
</div>

<? } ?>

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
			
			//$('.opacity, .lang_yesno_box').show();
			
			
			//return 0;
			
           
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
                           // update_header_cart();
                        }
                    });
					
					
					$('.alerthtml').hide();
            
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
            post[csrf[0]] = csrf[1];

            $.post('<?=Yii::app()->createUrl('cart/changequantity')?>', post, function (json) {
                if(json.changed) {
                    data.InfoField(json.changedStr);
                    if (json.origQuantity == 0) {
                        $(event.target).closest('tr').find('.alerthtml').show();
                        $('.opacity.alerthtml').show();
						$('.box_btns .btn_yes').focus();
                    }
                }
                else {
                    data.InfoField('');
                }
//                console.log(json);
                data.Quantity(json.quantity);
				//update_header_cart();
            }, 'json');
			
			}
            else {
                $(event.target).closest('tr').find('.alerthtml').show();
                $('.opacity.alerthtml').show();
				$('.box_btns .btn_yes').focus()
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
				//update_header_cart();
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
		//update_header_cart();
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

    $(document).ready(function () {
		
		
		$('body').keydown(function(e) {
			
			if ($('.alerthtml').css('display') == 'block') {
			
				//37 и 39
				
				if (e.keyCode == 37) {
					
					if ($('.btn_yes:focus').css('border') == undefined) {
						
						$('.btn_yes').focus();
						
					} else {
						
						$('.btn_no').focus();
						
					}
					
				}
				
				if (e.keyCode == 39) {
					
					if ($('.btn_no:focus').css('border') == undefined) {
						
						$('.btn_no').focus();
						
					} else {
						
						$('.btn_yes').focus();
						
					}
					
				}
			
			}
			
			
		})
		
		
		
		ym(53579293, 'reachGoal', 'cart_step1');
		
        scriptLoader('/new_js/slick.js').callFunction(function() {
            $('.more_goods ul').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 5,
                slidesToScroll: 5
            }).on('lazyLoadError', function(event, slick, image, imageSource){
                image.attr('src', '<?= Picture::srcNoPhoto() ?>');
            });
        });
    });

</script>


<div style="height: 20px;"></div>
	