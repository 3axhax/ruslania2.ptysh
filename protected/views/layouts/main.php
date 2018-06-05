<?php 
// session_start();
		// if(!isset($_SESSION['ert']))
		// {
			// echo 'no4';
			// $_SESSION['ert'] = '456';
		// }
		// else
			// echo 'yes4';

// echo $_SESSION['ert'];	
		
// $session = Yii::app()->session;
// echo  $session['shopcartkey'];

$url = explode('?', $_SERVER['REQUEST_URI']);
$url = trim($url[0], '/');

if (isset($_GET['langsel'])) {
	
	Yii::app()->getRequest()->cookies['langsel'] = new CHttpCookie('langsel', $_GET['langsel']);
	
}

$entity = Entity::ParseFromString($url);

if (Yii::app()->getRequest()->cookies['showSelLang']->value != '1') {

	Yii::app()->language = $this->getPreferLanguage();
}



$ui = Yii::app()->ui; ?><!DOCTYPE html><html>
    <head>
        <title><?= $this->pageTitle; ?></title>
        <meta name="keywords" content="<?=$this->pageKeywords?>">
        <meta name="description" content="<?=$this->pageDescription?>">
        <META name="verify-v1" content="eiaXbp3vim/5ltWb5FBQR1t3zz5xo7+PG7RIErXIb/M="/>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
        <link href="/new_style/jscrollpane.css" rel="stylesheet" type="text/css"/>
        <link href="/new_style/bootstrap.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="/css/template_styles.css" />
        <link rel="stylesheet" href="/css/jquery.bootstrap-touchspin.min.css">
        <link rel="stylesheet" href="/css/opentip.css">
		<link rel="stylesheet" type="text/css" href="/css/jquery-bubble-popup-v3.css"/>
        <link href="/new_style/style_site.css" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" type="text/css" href="/css/prettyPhoto.css"/>
        <script src="/new_js/js_site.js" type="text/javascript"></script>
        <script src="/new_js/jquery.js" type="text/javascript"></script>
        <script src="/new_js/jquery.mousewheel.min.js" type="text/javascript"></script>
        <meta name="csrf" content="<?= MyHTML::csrf(); ?>"/>
        <script src="/new_js/jScrollPane.js" type="text/javascript"></script>
        <script src="/new_js/slick.js" type="text/javascript" charset="utf-8"></script>
        <script src="/new_js/nouislider.js" type="text/javascript" charset="utf-8"></script>
        <link href="/new_js/nouislider.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="/js/jquery.prettyPhoto.js"></script>
		<script src="/js/common.js"></script>
		<script src="/new_js/jquery.bootstrap-touchspin.min.js"></script>
		<script src="/js/opentip.js"></script>
		<script type="text/javascript" src="/js/marcopolo.js"></script>
		<!--[if lt IE 9]>
<script src="libs/html5shiv/es5-shim.min.js"></script>
<script src="libs/html5shiv/html5shiv.min.js"></script>
<script src="libs/html5shiv/html5shiv-printshiv.min.js"></script>
<script src="libs/respond/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="/js/magnific-popup.js"></script>
        <script>
			
			function show_subs(uid, sid, subsid) {
				var csrf = $('meta[name=csrf]').attr('content').split('=');
				
				$.post('/site/loadhistorysubs', { uid : uid, sid : sid, YII_CSRF_TOKEN: csrf[1], subsid : subsid }, function(data) {
					
					$('.history_subs_box').css('top', $(window).scrollTop() + 50);
					
					$('.history_subs_box .table_box').html(data);
					
					$('.history_subs_box, .opacity').show();
					
				});
				
			}
			
            $(document).ready(function () {
				
				
				$('li.dd_box .click_arrow').click(function(){
					
					if ($(this).closest('li').hasClass('show_dd')) {
						
						$('.dd_box').removeClass('show_dd');
						if ($(this).parents().is('li.more_menu') && !$(this).parent().is('li.more_menu')) {
                            $(this).parents('li.more_menu').addClass('show_dd');
                        }
						
					} else {
						
						$('.dd_box').removeClass('show_dd');
						
						$(this).closest('li').addClass('show_dd');
						$(this).closest('li.more_menu').addClass('show_dd');

					}
					
					return false;
				})
				
				
				$('li.dd_box.more_menu').click(function(){
					
					if ($(this).hasClass('show_dd')) {
						
						$('.dd_box').removeClass('show_dd');
						
					} else {
						
						$('.dd_box').removeClass('show_dd');
						
						$(this).addClass('show_dd');
						
					}
					
					
				})
				
				$(document).click(function (event) {
				if ($(event.target).closest("li.dd_box").length)
				return;
				$('li.dd_box').removeClass('show_dd');
				event.stopPropagation();
				});
				
				
				$(document).ready(function()
    {
        $('.search_text').on('keydown', function(a)
        {
            if(a.keyCode == 13)
            {
                $('#srch').submit();
            }
        });
		
		function decline_days(num) {
        var count = num;

        num = num % 100;

        if (num > 19) {
            num = num % 10;
        }

        switch (num) {

            case 1: {
                    return count + ' <?=$ui->item('A_NEW_SEARCH_RES_COUNT3'); ?>';
                }

            case 2: case 3: case 4: {
                    return count + ' <?=$ui->item('A_NEW_SEARCH_RES_COUNT2'); ?>';
                }

            default: {
                    return count + ' <?=$ui->item('A_NEW_SEARCH_RES_COUNT1'); ?>';
                }
			}
		}
		
        $('#Search').marcoPolo({
            url:'/site/search',
            cache : false,
			hideOnSelect: false,
            dynamicData:{ avail: function() { return $('#js_avail').val(); } },
            formatItem:function (data, $item, q)
            {
					var ret = '';
					
					if (data.Counts != undefined) {
						
						for (var i = 1; i < 10; i++) {
							
							if (data.Counts.enityes[i] != undefined) {
							
								ret += '<div class="row_category">'+data.Counts.enityes[i][0] + ' <span>'+ data.Counts.enityes[i][2] +'</span> <a href="'+data.Counts.enityes[i][3]+'" class="result_search_count">'+decline_days(data.Counts.enityes[i][1])+' </a></div>';
							
							}
							
						}
						
					} else {
						
						//alert(data.length);
						
						if ( data.length > 0) {
							ret += '<div class="title_goods">'+
                                '<div class="red_checkbox" onclick="check_search($(this), \'js_avail\')" style="float: right;">';
                         			<?php
                         				$act = array();
                         				$act = array(1, ' active');
                         				if (isset($_GET['avail'])) {
                         					if ($_GET['avail'] == '1') $act = array(1, ' active');
                         					else $act = array('', '');
                         				}
                         			?>
                                ret += ''+
                                     '<span class="checkbox" style="height: 10px; padding-top: 2px;">'+
                                         '<span class="check<?=$act[1]?>"></span>'+
                                     '</span> '+
                                    //'<input type="hidden" name="avail" value="<?=$act[0]?>" class="avail">'+
                                    '<?= $ui->item('A_NEW_SEARCH_AVAIL'); ?>'+
                                '</div>'+
                                '<div><?=$ui->item('A_NEW_SEARCH_GOODS_TITLE'); ?></div></div>'+
                            '';


							
							for (var i=0; i<data.length;i++)
							{
								
								if (data[i].is_product) {
								
								var img = '';
								if (data[i].picture_url != 'http://ruslania.com/pictures/small/' && data[i].picture_url != ''){
									
									var img = '<img style="max-width: 100%;" height="86" src="'+data[i].picture_url+'" />';
									
								}
								
								ret += '<div class="row_item"><table><tr><td class="pic"><a href="'+data[i].url +'">'+img+'</a></td><td class="name"><a href="'+data[i].url+'">'+data[i].title+'</a><div style="height: 18px;"></div><span class="price">'+data[i].price+'</span></td></tr></table></div>';
								//<a class="cart-action add_cart<?if (Yii::app()->language == 'es') echo ' no_img';?>" data-action="add" style="width: 162px;font-size: 13px; margin-left: 18px; color: #fff;" data-entity="'+data[i].entity+'" data-id="'+data[i].id+'" data-quantity="1" href="javascript:;" onclick="add2Cart($(this).attr(\'data-action\'),     $(this).attr(\'data-entity\'),$(this).attr(\'data-id\'),$(this).attr(\'data-quantity\'),null,$(this));"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></a>
								
								}
                                else {
                                    ret += '<div class="row_item"><a href="'+data[i].url+'">'+data[i].title+'</a></div>';
                                }
							}
							
							
						}
						
					}
					
					//console.log(ret);
					
					return ret;
               
            }

        });
    });
				
				
				
                $.ajax({
                    url: '/cart/getcount',
                    data: 'id=1',
                    type: 'GET',
                    success: function (data) {
                        var d = JSON.parse(data);
                        //alert(data);
                        $('div.cart_count').html(d.countcart)
                        $('div.span1.cart .cost').html(d.totalPrice)
                    }
                });
				
				 $('select.periodic').change(function ()
                {
					
					//alert(1);
					
                    var $el = $(this);
                    var cart = $el.closest('.span11, .span1.cart');

                    var worldpmonthVat0 = cart.find('input.worldmonthpricevat0').val();
                    var worldpmonthVat = cart.find('input.worldmonthpricevat').val();
                    var finpmonthVat0 = cart.find('input.finmonthpricevat0').val();
                    var finpmonthVat = cart.find('input.finmonthpricevat').val();

                    var nPriceVat = (worldpmonthVat * $el.val()).toFixed(2);
                    var nPriceVat0 = (worldpmonthVat0 * $el.val()).toFixed(2);

                    var nPriceFinVat = (finpmonthVat * $el.val()).toFixed(2);
                    var nPriceFinVat0 = (finpmonthVat0 * $el.val()).toFixed(2);

                    cart.find('.periodic_world .price').html(nPriceVat + ' <?=Currency::ToSign(); ?>');
					cart.find('.periodic_world .pwovat span').html(nPriceVat0 + ' <?=Currency::ToSign(); ?>');

					cart.find('.periodic_fin .price').html(nPriceFinVat + ' <?=Currency::ToSign(); ?>');
					cart.find('.periodic_fin .pwovat span').html(nPriceFinVat0 + ' <?=Currency::ToSign(); ?>');

                    cart.find('a.add').attr('data-quantity', $el.val());
                });


				
				
            })

            $(document).ready(function () {

                $(document).click(function (event) {
                    if ($(event.target).closest(".dd_box_select").length)
                        return;
                    $('.dd_box_select .list_dd').hide();
                    event.stopPropagation();
                })

                var blockScroll1 = false;
                var blockScroll2 = false;
                var blockScroll3 = false;
                var page_authors = 1;
                var page_izda = 1;
                var page_seria = 1;
                $('.dd_box_select .list_dd.authors_dd').scroll(function () {

                    if (($(this).height() + $(this).scrollTop()) >= $('.items', $(this)).height() && !blockScroll1) {

                        blockScroll1 = true;
                        page_authors++;
                        var tthis = $(this);
                        $('.load_items', $(this)).show();
                        $('.load_items', $(this)).html('<?=$ui->item('A_NEW_LOAD'); ?>');
                        var csrf = $('meta[name=csrf]').attr('content').split('=');

                        var url = '/site/loaditemsauthors/page/' + page_authors + '/entity/' + $('.entity_val').val() + '/cid/' + $('.cid_val').val();

                        $.post(url, {YII_CSRF_TOKEN: csrf[1]}, function (data) {
                            //alert(data);
                            $('.items .rows', tthis).append(data);
                            blockScroll1 = false;
                            $('.load_items', tthis).html('');
                            $('.load_items', tthis).hide();
                        })

                    }

                })
                $('.dd_box_select .list_dd.izda_dd').scroll(function () {

                    if (($(this).height() + $(this).scrollTop()) >= $('.items', $(this)).height() && !blockScroll2) {

                        blockScroll2 = true;
                        page_izda++;
                        var tthis = $(this);
                        $('.load_items', $(this)).show();
                        $('.load_items', $(this)).html('<?=$ui->item('A_NEW_LOAD'); ?>');
                        var csrf = $('meta[name=csrf]').attr('content').split('=');

                        var url = '/site/loaditemsizda/page/' + page_izda + '/entity/' + $('.entity_val').val() + '/cid/' + $('.cid_val').val();

                        $.post(url, {YII_CSRF_TOKEN: csrf[1]}, function (data) {
                            //alert(data);
                            $('.items .rows', tthis).append(data);
                            blockScroll2 = false;
                            $('.load_items', tthis).html('');
                            $('.load_items', tthis).hide();
                        })

                    }

                })
                $('.dd_box_select .list_dd.seria_dd').scroll(function () {

                    if (($(this).height() + $(this).scrollTop()) >= $('.items', $(this)).height() && !blockScroll3) {

                        blockScroll3 = true;
                        page_seria++;
                        var tthis = $(this);
                        $('.load_items', $(this)).show();
                        $('.load_items', $(this)).html('<?=$ui->item('A_NEW_LOAD'); ?>');
                        var csrf = $('meta[name=csrf]').attr('content').split('=');

                        var url = '/site/loaditemsseria/page/' + page_seria + '/entity/' + $('.entity_val').val() + '/cid/' + $('.cid_val').val();

                        $.post(url, {YII_CSRF_TOKEN: csrf[1]}, function (data) {
                            //alert(data);
                            $('.items .rows', tthis).append(data);
                            blockScroll3 = false;
                            $('.load_items', tthis).html('');
                            $('.load_items', tthis).hide();
                        })

                    }

                })

            })
            
            function show_items() {
                
                var create_url;
                
                create_url = '/site/ggfilter/entity/'+$('form.filter input.entity_val').val()+
                    '/cid/'+$('form.filter input.cid_val').val()+'/author/'+$('form.filter .form-row input[name=author]').val()+
                    '/avail/'+$('form.filter .form-row input[name=avail]').val()+
                    '/ymin/'+$('form.filter .form-row input.year_inp_mini').val()+'/ymax/'+$('form.filter .form-row input.year_inp_max').val()+
                    '/izda/'+$('form.filter .form-row input[name=izda]').val()+
                    '/seria/'+(($('form.filter .form-row input[name=seria]').val()) ? ($('form.filter .form-row input[name=seria]').val()) : '0')+
                    '/cmin/'+$('form.filter .form-row input.cost_inp_mini').val()+'/cmax/'+$('form.filter .form-row input.cost_inp_max').val()+
                    '/langsel/'+$('form.filter input.lang').val();

                var bindings = [];
                var i = 0;
                $('.bindings input[type=checkbox]:checked').each(function() {
                    
                    bindings[i] = $(this).val();
                    
                    i++;
                });
                
                var csrf = $('meta[name=csrf]').attr('content').split('=');

                $('.span10.listgoods').html('<?=$ui->item('A_NEW_LOAD2'); ?>');
                $.post(create_url, { YII_CSRF_TOKEN: csrf[1], 'binding_id[]' : bindings,
                    search_name : $('form.filter .search.inp').val(), sort : $('form.filter .sort').val(),
                    formatVideo : $('#formatVideo').val(),
                    langVideo : $('#langVideo').val(),
                    subtitlesVideo : $('#subtitlesVideo').val(),
                }, function(data) {
                    $('.span10.listgoods').html(data);
                    $('.box_select_result_count').hide(1);
                    $(window).scrollTop(0);
                })
                
            }
            
            var mini_map_isOn = 0;
            var TimerId;
            
            function mini_cart_off() {
                if (mini_map_isOn == 1)
                {
                    $('#cart_renderpartial').toggle(100);
                }
                mini_map_isOn = 0;
            }
            $(document).ready(function () {
                $('.cart_box').click(function () {
                        $('#cart_renderpartial').toggle(100);
                        mini_map_isOn = 1-mini_map_isOn;
                        if (mini_map_isOn)
                            TimerId = setTimeout(mini_cart_off, 10000);
                        else
                            clearTimeout(TimerId);
                    })
            })
            function show_result_count(cont) {

                $('.box_select_result_count').hide(1);
                $('.loader_gif').remove();
                $('.res_count').html(' ');
                $('.box_select_result_count').removeAttr('current_box');
                $('.box_select_result_count', cont.parents('.form-row')).attr('current_box', 'yes');


                var frm = $('form.filter').serialize();
                var csrf = $('meta[name=csrf]').attr('content').split('=');

                frm = frm + '&' + csrf[0] + '=' + csrf[1];

                $.ajax({
                    url: '/site/gtfilter/',
                    type: "POST",
                    data: frm,
                    beforeSend: function(){
                        $('.box_select_result_count', cont.parents('.form-row')).show();
                        $('.box_select_result_count', cont.parents('.form-row')).append('<img class="loader_gif" src="/new_img/source.gif" width="20">');
                        $('.box_select_result_count a', cont.parents('.form-row')).hide();
                    },
                    success: function (data) {
                        if ($('.box_select_result_count', cont.parents('.form-row')).attr('current_box') == 'yes') {
                            $('.loader_gif').remove();
                            $('.box_select_result_count a', cont.parents('.form-row')).show();
                            $('.box_select_result_count img', cont.parents('.form-row')).hide();
                            if (data == '0') {
                                $('.box_select_result_count a', cont.parents('.form-row')).hide();
                            }

                            $('.box_select_result_count .res_count', cont.parents('.form-row')).html(data);
                            $('.box_select_result_count', cont.parents('.form-row')).show(1);
                        }
                    }
                });
            }

            function change_all_binding(event, binding_all = false)
            {
                if (binding_all) {
                    if (event.target.checked) {
                        otherBinding = event.target.parentElement.nextElementSibling;
                        do
                        {
                            otherBinding.firstElementChild.checked = false;
                        }
                        while (otherBinding = otherBinding.nextElementSibling)
                    }
                }
                else {
                    event.target.parentElement.parentElement.children[2].firstElementChild.checked = false;
                }
            }

            function select_item(item, inp_name) {
                var id = item.attr('rel');
                //$('.list_dd', item.parent().parent().parent().parent()).scrollTop(0);

                $('div', item.parent()).removeClass('selact');

                item.addClass('selact');

                if (!$(item).parent().parent().is('div.interactive_search')) $('.text span', item.parent().parent().parent().parent()).html(item.html());
                $('.list_dd', item.parent().parent().parent().parent()).hide();
                $('input[name=' + inp_name + ']', item.parent().parent().parent().parent()).val(id);

                show_result_count(item);
            }

            function show_sc(cont, c, lvl) {

				if (cont.css('display') == 'none') {
					$('ul.lvlcat'+lvl).hide();
					$('a.subcatlvl'+lvl).removeClass('open');
					cont.show();
					c.addClass('open');
				} else {
					$('ul.lvlcat'+lvl+' ul').hide();
					$('ul.lvlcat'+lvl+' a.open_subcat').removeClass('open');
					cont.hide();
					c.removeClass('open');
				}
				
				var liW = c.parent().width();
				
				cont.css('position', 'absolute');
				cont.css('left', (liW+20)+'px');
				cont.css('top', '0');
				cont.css('z-index', '999999');
				cont.css('background', '#fff');
				cont.css('width', '249px');
				cont.css('box-shadow', '0px 3px 10px 0px rgba(0, 0, 0, 0.15)');
				cont.css('padding', '0px 10px');
				
				
            }
			
            function add2Cart(action, eid, iid, qty, type, $el)
            {
				
                var csrf = $('meta[name=csrf]').attr('content').split('=');
                var post =
                        {
                            entity: eid,
                            id: iid,
                            quantity: qty,
                            type: type
                        };
                post[csrf[0]] = csrf[1];
				
				var seconds_to_wait = 10;


			var opentip = new Opentip($el,'',{ target: true, tipJoint: "bottom", group: "group-example", showOn: "click", hideOn: 'ondblclick', background: '#fff', borderColor : '#fff' });
				
				opentip.deactivate();
				
                $.post('/cart/'+action, post, function (json)
                {
					
					var json = JSON.parse(json);
					var opentip = new Opentip($el,'<div style="padding-right: 17px;">'+json.msg +
						'</div><div style="height: 6px;"></div><span class="timer_popup"></span> <span class="countdown">00: 10</span><a href="javascript:;" class="close_popup" onclick="$(this).parent().parent().parent().remove()"><img src="/new_img/close_popup.png" alt="" /></a>',{ target: true, tipJoint: "bottom", group: "group-example", showOn: "click", hideOn: 'ondblclick', background: '#fff', borderColor : '#fff' });
					

					opentip.show();
					
					function doCountdown()
					{
						
						var str = '';
						
						var timer = setTimeout(function()
						{
							seconds_to_wait--;
							
							if (seconds_to_wait < 10) {
								str = '00:0'+seconds_to_wait;
							} else {
								str = '00:'+seconds_to_wait;
							}
							
							if($('#opentip-'+opentip.id+' span.countdown').length>0) $('#opentip-'+opentip.id+' span.countdown').html(str);
							if(seconds_to_wait > 0) doCountdown();
							else opentip.deactivate();
						}, 1000);
					}
					
					if (json.already)
                    {
                        $('div.already-in-cart', $el.parent()).html(json.already);
                    }

					
					
					doCountdown();
					
					update_header_cart();
					
				})
            }


            $(document).ready(function () {
                sortCategoryMenu('#books_menu', '#books_category', '#books_sale');
                sortCategoryMenu('#sheet_music_menu', '#sheet_music_category', '#sheet_music_sale');
                sortCategoryMenu('#music_menu', '#music_category', '#music_sale');
                sortCategoryMenu('#periodic_menu', '#periodic_category', '#periodic_sale');
                $(document).click(function (event) {
                    if ($(event.target).closest(".select_lang").length)
                        return;
                    $('.select_lang .dd_select_lang').hide();
                    $('.select_lang').removeClass('act');
                    $('.select_lang .label_lang').removeClass('act');
                    event.stopPropagation();
                });
				
				$.fn.prettyPhoto({social_tools: false});

                $('a.read_book').click(function ()
                {
					
					
                    var $this = $(this);
                    var images = [];
                    if ($this.attr('data-images') != '')
                    {
                        images = $this.attr('data-images').split('|');
                        if (images.length > 0)
                            $.prettyPhoto.open(images, [], []);
                    }

                    //            var pdf = $this.attr('data-pdf').split('|');
                    //            if(pdf.length > 0)
                    //            {
                    //                var iid = $this.attr('data-iid');
                    //                $('#staticfiles'+iid).fadeIn();
                    //            }
                });
				
				/* $('.tabs_container .tabs li').click(function() {
					
					var $clas = $(this).attr('class').split(' ')[0];
					
					//alert($clas);
					
					$('.tabs_container .tabcontent, .tabs_container .tabs li').removeClass('active');
					
					$('.tabs_container .tabcontent.'+$clas).addClass('active');
					$('.tabs_container .tabs li.'+$clas).addClass('active');
					
				}) */
				
				
				$(document).click(function (event) {
                    if ($(event.target).closest(".span1.cart, .b-basket-list").length)
                        return;
                    $('.b-basket-list').fadeOut();
                    event.stopPropagation();
                });

                $(document).click(function (event) {
                    if ($(event.target).closest(".select_valut").length)
                        return;
                    $('.select_valut .dd_select_valut').hide();
                    $('.label_valut').removeClass('act');
                    event.stopPropagation();
                });

                var elems = $('a.cart-action');
				var $finSubButton = $('#finSubscription');
                var $worldSubButton = $('#worldSubscription');
                var $formDiv = $('#periodic-price-form');
                var $formEid = $formDiv.find('input[name="eid"]');
                var $formIid = $formDiv.find('input[name="iid"]');
                var $formQty = $formDiv.find('input[name="qty"]');
				
				
				$finSubButton.click(function ()
                {
                    $.magnificPopup.close();
                    add2Cart('add', $formEid.val(), $formIid.val(), $formQty.val(), 1, $finSubButton.data());
                });

                $worldSubButton.click(function ()
                {
                    $.magnificPopup.close();
                    add2Cart('add', $formEid.val(), $formIid.val(), $formQty.val(), 2, $worldSubButton.data());
                });
				
                elems.click(function ()
                {
                    //alert(1);
			
			
			
			
            var $el = $(this);
            var $parent = $el.closest('.to_cart');

            var entity = $el.attr('data-entity');

            if(entity == <?=Entity::PERIODIC; ?>)
            {
                var $finPrice = $('#finPrice');
                var $worldPrice = $('#worldPrice');

                var $itemFinBlock = $parent.find('.periodic_fin');
                var $itemWorldBlock = $parent.find('.periodic_world');

                if($itemWorldBlock.length && $itemFinBlock.length)
                {
                    $formEid.val($el.attr('data-entity'));
                    $formIid.val($el.attr('data-id'));
                    $formQty.val($el.attr('data-quantity'));
                    $finSubButton.data($el);
                    $worldSubButton.data($el);

                    // show dialog only if we have different prices
                    var $formTitle = $('#formTitle');
                    var $formMonths = $('#formMonths');
                    var $title = $parent.closest('.to_cart').find('h1.title');
                    $formTitle.html($title.html());

                    var $select = $parent.find('select.periodic');
                    $formMonths.html($(':selected', $select).text());

                    var finHtml = $itemFinBlock.html();
                    $finPrice.html(finHtml);
                    var worldHtml = $itemWorldBlock.html();
                    $worldPrice.html(worldHtml);
                    $.magnificPopup.open({
                        items: {
                            src: '#periodic-price-form', // can be a HTML string, jQuery object, or CSS selector
                            type: 'inline'
                        }
                    });
                    return false;
                }
            }

            add2Cart($el.attr('data-action'),
                $el.attr('data-entity'),
                $el.attr('data-id'),
                $el.attr('data-quantity'),
                null,
                $el
            );

            return false;
                });

            })

            function check_search(cont, inputId) {
                if ($('.check', cont).hasClass('active')) {
                    $('.check', cont).removeClass('active');
                    if (inputId == undefined) $('.avail', cont).val('');
                    else $('#'+inputId).val('');
                } else {
                    $('.check', cont).addClass('active');
                    if (inputId == undefined) $('.avail', cont).val('1');
                    else $('#'+inputId).val('1');
                }

            }

            function show_tab(cont, url) {
                
				if (cont.parent().hasClass('active')) {
					
					location.href = cont.attr('href');
					
					
				} else {
					
					$('.dd_box_bg .tabs li').removeClass('active');
					cont.parent().addClass('active');
					var csrf = $('meta[name=csrf]').attr('content').split('=');
					$('.dd_box_bg .content .list').html('');
					
					$.post('/site/mload' + cont.attr('href'), {YII_CSRF_TOKEN: csrf[1], id: 1}, function (data) {
						$('.dd_box_bg .content .list').html(data);
					})
				
				}
            }
			
			function update_header_cart(){
				$.ajax({
					url: '/cart/getcount',
					data: 'id=1',
					type: 'GET',
					success: function (data) {
						var d = JSON.parse(data);
						
						 var data = { language: '<?=Yii::app()->language; ?>', is_MiniCart: 1};
						$.getJSON('/cart/getall', data, function (json)
						{
							ko.mapping.fromJS(json, {}, cvm_1);
						   
							cvm_1.FirstLoad(false);
							
						});
						
						$('div.cart_count').html(d.countcart)
						$('div.span1.cart .cost').html(d.totalPrice)
					}
				});
			}	

			function addComment() {
				var csrf = $('meta[name=csrf]').attr('content').split('=');
				var ser = $('form.addcomment').serialize() + '&'+csrf[0]+'='+csrf[1];
				
				
				
				$.post('/site/addcomments/', ser, function (data) {
					
					if (data) {
						//$('.comments_block').html(data);
						$('form span.info').html('<?=$ui->item('A_NEW_REVIEW_SENT1');?>');
						$('form span.info').delay(1).show(0);
						
						$('form span.info').delay(2000).hide(0);
						
						$('.review form textarea').val('');
					} else {
						$('form span.info').html('<span style="color: #ff0000;"><?=$ui->item('A_NEW_REVIEW_SENT2');?></span>');
						$('form span.info').delay(1).show(0);
						
						$('form span.info').delay(2000).hide(0);
						
						$('.review form textarea').val('');
					}
				})
				
			}

			function sortCategoryMenu(id_category, id_category_item = false, id_sale_item = false) {
                var mylist = $(id_category);
                var listitems = mylist.children().get();
                var category_item;
                var sale_item;
                listitems.sort(function(a, b) {
                    var compA = $(a).children('a').text().toUpperCase();
                    var compB = $(b).children('a').text().toUpperCase();
                    return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
                });
                $.each(listitems, function(idx, itm) {
                    if (('#' + itm.id) == id_category_item) category_item = itm;
                    else if (('#' + itm.id) == id_sale_item) sale_item = itm;
                    else {
                        mylist.append(itm);
                        mylist.append('<div class="clearfix"></div>');
                    }
                });
                mylist.append(sale_item);
                mylist.append('<div class="clearfix"></div>');
                mylist.append(category_item);
            }
			
			
        </script>

    </head>

    <body>
    <?
	
	if ($_GET['sel'] == '1') { 
	
		$cookie = new CHttpCookie('showSelLang', '1');

		$cookie->expire = time() + (60*60*24*20000); // 20000 days

		Yii::app()->getRequest()->cookies['showSelLang'] = $cookie;

        if (isset($_GET['language'])) Yii::app()->language = $_GET['language'];

	}

    if (Yii::app()->getRequest()->cookies['showSelLang']->value == '' OR Yii::app()->getRequest()->cookies['showSelLang']->value == '0') {
	
		?>
		
		<div class="opacity_box" style="display: block;"></div>
		
		<div class="lang_yesno_box">
			
			<div class="box_title box_title_ru"><?=$ui->item('A_NEW_RUS_POPUP');?></div>
			
			<? if (Yii::app()->language != 'en') : ?>
			
                <div class="box_title box_title_en">Is your language <?= $ui->item('LANG_IN_EN');?>?</div>
			
			<? endif; ?>
			
			<div class="box_btns">
				<a href="?language=<?=Yii::app()->language?>&sel=1" class="btn_yes"><?=$ui->item('A_NEW_BTN_YES');?> <? if (Yii::app()->language != 'en') : ?>(Yes)<? endif; ?></a>
				<a href="javascript:;" onclick="$('.lang_yesno_box').hide(); $('.lang_yesno_box.select_lang').show();" class="btn_no"><?=$ui->item('A_NEW_BTN_NO');?> <? if (Yii::app()->language != 'en') : ?>(No)<? endif; ?></a>
			</div>
			
		</div>
		
		<div class="lang_yesno_box select_lang">
		
			<div class="box_title box_title_ru"><?=$ui->item('A_NEW_SELECT_LANG_TITLE');?>:</div>
            <? if (Yii::app()->language != 'en') : ?>

                <div class="box_title box_title_en">Choose your language</div>

            <? endif; ?>
			<div class="row">
				<ul class="list_languages">
					<li class="ru span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'ru'); ?>&sel=1"><?=$ui->item('A_LANG_RUSSIAN')?></a></li>
					<li class="fi span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'fi'); ?>&sel=1"><?=$ui->item('A_LANG_FINNISH')?></a></li>
					<li class="en span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'en'); ?>&sel=1"><?=$ui->item('A_LANG_ENGLISH')?></a></li>
					<li class="de span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'de'); ?>&sel=1"><?=$ui->item('A_LANG_GERMAN')?></a></li>
					<li class="fr span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'fr'); ?>&sel=1"><?=$ui->item('A_LANG_FRENCH')?></a></li>
					<li class="es span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'es'); ?>&sel=1"><?=$ui->item('A_LANG_ESPANIOL')?></a></li>
					<li class="se span1"><a href="<?= MyUrlManager::RewriteCurrent($this, 'se'); ?>&sel=1"><?=$ui->item('A_LANG_SWEDISH')?></a></li>
					
				</ul>
			</div>
		</div>
		
		<?
		
	}
	?>
	
	
        <div class="header_logo_search_cart">
        
<?php $mess = Yii::app()->ui->item('MSG_MAIN_WELCOME_INTERNATIONAL_ORDERS');
    if ($mess):
?>
		<div class="alert_bg" style="display: block">
            <div class="container">
                <span class="text" id="js_container-alert_bg"></span>
                <span class="close_alert" onclick="$(this).parent().parent().remove()"><img src="/new_img/close_alert.png" /></span>
            </div>
        </div>
<?php endif; ?>

        <div class="light_gray_menu">
            <div class="container">
                <ul>
				<!--<li style="padding-right: 0px;"><img src="/new_img/flag.png" /></li>-->
                    <li style="border: 0;padding-left: 10px;"><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>" class=""><?=$ui->item('A_NEW_ABOUTUS');?></a></li>
                    <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'contact')); ?>"><?=$ui->item('YM_CONTEXT_CONTACTUS')?></a></li>
                    <li><span class="telephone2"><a href="whatsapp://send?phone=+358503889439"><img src="/new_img/telephone2.png" alt="" /></a></span><a href="tel:+35892727070"><span class="telephone">+358 92727070</span></a></li>
                    <li><a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank"><span class="adrs">Bulevardi 7, FI-00120 Helsinki, Finland</span></a></li>
                    <li><?=$ui->item('A_NEW_TITLE_TOP');?></li>
					
					<?php if (Yii::app()->user->isGuest) : ?>
					
                    <li class="menu_right none_right_padding"><a href="<?= Yii::app()->createUrl('site/login'); ?>"><?=$ui->item('A_SIGNIN')?></a></li>
                    <li class="menu_right none_border"><a href="<?= Yii::app()->createUrl('site/register'); ?>"><?=$ui->item('A_REGISTER')?></a></li>
					<? else :?>
					<li class="menu_right none_right_padding"><a href="<?= Yii::app()->createUrl('site/logout'); ?>"><?= $ui->item('YM_CONTEXT_PERSONAL_LOGOUT'); ?></a></li>
					<li class="menu_right none_border "><a href="<?=Yii::app()->createUrl('client/me'); ?>"><?= $ui->item('YM_CONTEXT_PERSONAL_MAIN'); ?></a></li>
                    
					<?endif;?>
                </ul>
            </div>
        </div>

			<div class="container">
                <div class="row">
                    <div class="span1 logo">
                        <a href="/"><img src="/new_img/logo.png" alt=""/></a>
                    </div>
                    <div class="span10">
                        <form method="get" action="/site/search" id="srch">
                            <div class="search_box">
								<div class="loading"><?=$ui->item('A_NEW_SEARCHING_RUR');?></div>
                                <input type="text" name="q" class="search_text" placeholder="<?=$ui->item('A_NEW_PLACEHOLDER_SEARCH');?>" id="Search" value="<?=$_GET['q']?>"/>
                                <input type="submit" class="search_run" value=""><!--<img src="/new_img/btn_search.png" class="search_run" alt="" onclick="$('#srch').submit()"/>-->
                            </div>

                            <div class="pult">

                                <ul>
                                    <li class="sm"><a href="/advsearch<? if ($entity) { echo '?e='.$entity; } elseif ($_GET['e']) { echo '?e='.$_GET['e']; }?>" class="search_more"> <?=$ui->item('Advanced search')?></a></li>
                                    <input type="hidden" name="avail" id="js_avail" value="<?=$act[0]?>" class="avail">
<?php /*
                                    <li class="chb">
                                        <div class="checkbox_box" onclick="check_search($(this))">
											<?
											
												$act = array();
												
												$act = array(1, ' active');
												
												if (isset($_GET['avail'])) {
													
													if ($_GET['avail'] == '1') {
														$act = array(1, ' active');
													} else {
														$act = array('', '');
													}
													
												}
											?>
										
                                            <span class="checkbox">
                                                <span class="check<?=$act[1]?>"></span>
                                            </span> <input type="hidden" name="avail" value="<?=$act[0]?>" class="avail"><?= $ui->item('A_NEW_SEARCH_AVAIL'); ?>
                                        </div>
                                    </li>
*/?>
                                    <li class="langs">
                                        <div class="select_lang">
											<?
											$arrLangsTitle = array(
												'ru' => $ui->item('A_LANG_RUSSIAN'),
												'rut' => $ui->item('A_LANG_TRANSLIT'),
												'fi' => $ui->item('A_LANG_FINNISH'),
												'en' => $ui->item('A_LANG_ENGLISH'),
												'de' => $ui->item('A_LANG_GERMAN'),
												'fr' => $ui->item('A_LANG_FRENCH'),
												'es' => $ui->item('A_LANG_ESPANIOL'),
												'se' => $ui->item('A_LANG_SWEDISH')
											);
											?>
                                            <div class="dd_select_lang">
                                                <div class="lable_empty" onclick="$('.dd_select_lang').toggle(); $('.label_lang.view_lang').toggleClass('act').parent().toggleClass('act')"></div>
                                                <?php foreach ($arrLangsTitle as $k=>$v):?>
                                                    <div class="label_lang">
                                                        <span class="lang <?= $k ?>"><a href="<?= MyUrlManager::RewriteCurrent($this, $k); ?>"><?=$v?></a></span>
                                                    </div>
                                                <?php endforeach; ?>
                                           </div>

                                            <div class="label_lang view_lang" onclick="$('.dd_select_lang').toggle(); $(this).toggleClass('act'); $(this).parent().toggleClass('act')">
                                                <span class="lang <?=Yii::app()->language;?>"><a href="javascript:;"><?=$arrLangsTitle[Yii::app()->language]; ?></a> <span class="dd"></span></span>
                                            </div>

                                        </div>
                                    </li>
                                    <li class="valuts">

                                        <div class="select_valut">
											<? $arrVCalut = array(
												
												'1' => array('euro','Euro'),
												'2' => array('usd','USD'),
												'3' => array('gbp','GBP'),
												
											); ?>
                                           <div class="dd_select_valut">
                                                <div class="lable_empty" onclick="$('.dd_select_valut').toggle(); $('.label_valut.select').toggleClass('act')"></div>
                                                <div class="label_valut">
                                                    <a href="<?= MyUrlManager::RewriteCurrency($this, Currency::EUR); ?>"><span style="width: 17px; display: inline-block; text-align: center">&euro;</span><span class="valut" style="margin-left: 10px;">Euro</span></a>
                                                </div>
                                                <div class="label_valut">
                                                    <a href="<?= MyUrlManager::RewriteCurrency($this, Currency::USD); ?>"><span style="width: 17px; display: inline-block; text-align: center">$</span><span class="valut" style="margin-left: 10px;">USD</span></a>
                                                </div>
                                                <div class="label_valut">
                                                    <a href="<?= MyUrlManager::RewriteCurrency($this, Currency::GBP); ?>"><span style="width: 17px; display: inline-block; text-align: center">£</span><span class="valut" style="margin-left: 10px;">GBP</span></a>
                                                </div>
                                            </div>
                                            <div class="label_valut select" onclick="$('.dd_select_valut').toggle(); $(this).toggleClass('act')">
                                                <a href="javascript:;"><span class="valut <?=$arrVCalut[(string)Yii::app()->currency][0]?>"><?=$arrVCalut[(string)Yii::app()->currency][1]?><span class="dd"></span></span></a>
                                            </div>
                                        </div>

                                    </li>
                                </ul>

                            </div>
                        </form>
                    </div>
                    <div class="span1 cart" >
                        
                        
                        <div class="span1">

                            <?= $ui->item('A_NEW_CART'); ?>:
                            <div class="cost"></div>

                        </div>
                        <div class="span2 js-slide-toggle" data-slidetoggle=".b-basket-list" data-slideeffect="fade" data-slidecontext=".span1.cart" >

                            <div class="cart_box" ><img src="/new_img/cart.png" alt=""/></div>
                            <div class="cart_count"></div>

                        </div>
                        
                           
								<?php  $this->renderPartial('/cart/header_cart'); ?>   
                          
                   
                       

                    </div>
                </div>



            </div>
			<div style="height: 10px;"></div>
        <script>
            $(document).ready(function () {
                $('a', $('.dd_box .tabs li')[0]).click();
                // $('li.dd_box .content').jScrollPane({scrollbarWidth:18, showArrows:true});
				$('.dd_box').removeClass('show_dd');
            })
        </script>
        <div class="index_menu">

            <div class="container">
                <ul>

                    <!--Книги-->
                    <li class="dd_box">
					<div class="click_arrow"></div>
                        <a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::BOOKS))); ?>"><?= $ui->item("A_GOTOBOOKS"); ?></a>
                        <div class="dd_box_bg list_subcategs" style="left: 0;">
							
							<div class="span10">
								
								<ul id="books_menu">
									<?
                                    $availCategory = array(181, 16, 206, 211, 189, 65, 67, 202);
                                    $rows = Category::GetCategoryList(Entity::BOOKS, 0, $availCategory);
                                    foreach ($rows as $row) {
									?>
									<li> 
										<a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(10), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
									</li>
									<? } ?>
                                    <?php $row = Category::GetByIds(Entity::SHEETMUSIC, 47)[0]?>
                                    <li>
										<a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                    </li>
                                    <?php $row = Category::GetByIds(Entity::BOOKS, 213)[0]?>
                                    <li id="books_sale">
                                        <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::BOOKS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=$ui->item('A_NEW_SALE');?></a>
                                    </li>
                                    <li id="books_category">
                                        <a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::BOOKS)))?>"><?=$ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
                                    </li>
                                </ul>
								
							</div>
							
							<!--<div class="span2">
								
								<img src="/new_img/banner.png" />
								
							</div>-->
							
                        </div>
					</li>

                    <!--Ноты-->
					<li class="dd_box">
                        <div class="click_arrow"></div>
                        <a class="dd"  href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC))); ?>"><?= $ui->item("A_GOTOMUSICSHEETS"); ?></a>
					    <div class="dd_box_bg list_subcategs" style="left: -80px;">
							
							<div class="span10">
								
								<ul id="sheet_music_menu">
									<?
                                    $availCategory = array(47, 160, 249, 128, 136);
                                    $rows = Category::GetCategoryList(Entity::SHEETMUSIC, 0, $availCategory);
                                    foreach ($rows as $row) {
                                        ?>
									<li> 
										<a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
									</li>
									<? } ?>
                                    <?php $row = Category::GetByIds(Entity::SHEETMUSIC, 217)[0]?>
                                    <li id="sheet_music_sale">
                                        <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=$ui->item('A_NEW_SALE');?></a>
                                    </li>
                                    <li id="sheet_music_category">
                                        <a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC)))?>"><?=$ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
                                    </li>
									
								</ul>
								
							</div>
							
							<!--<div class="span2">
								
								<img src="/new_img/banner.png" />
								
							</div>-->
                        </div>
					</li>

                    <!--Музыка-->
                    <li class="dd_box">
                        <div class="click_arrow"></div>
                        <a class="dd"  href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MUSIC))); ?>"><?= $ui->item("Music catalog"); ?></a>
					
					    <div class="dd_box_bg list_subcategs" style="left: -170px;">
							
							<div class="span10">
								
								<ul id="music_menu">
									<?
                                    $availCategory = array(78, 74, 4, 11, 6, 17, 2, 73, 38);
                                    $rows = Category::GetCategoryList(Entity::MUSIC, 0, $availCategory);
									foreach ($rows as $row) {
									?>
									<li> 
										<a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
									</li>
									<? } ?>
                                    <?php $row = Category::GetByIds(Entity::MUSIC, 21)[0]?>
                                    <li id="music_sale">
                                        <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=$ui->item('A_NEW_SALE');?></a>
                                    </li>
                                    <li id="music_category">
                                        <a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::MUSIC)))?>"><?=$ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
                                    </li>
                                </ul>
                            </div>
                        <!--<div class="span2">
								
								<img src="/new_img/banner.png" />
								
							</div>-->
                    </div>
                    </li>

                    <!--Подписка-->
                    <li class="dd_box">
                        <div class="click_arrow"></div>
                        <a class="dd"  href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC))); ?>"><?= $ui->item("A_GOTOPEREODICALS"); ?></a>
					
					    <div class="dd_box_bg list_subcategs" style="left: -280px;">
							
							<div class="span10">
								
								<ul id="periodic_menu">
									<?
                                    $availCategory = array(19, 48, 96, 67);
									$rows = Category::GetCategoryList(Entity::PERIODIC, 0, $availCategory);
									foreach ($rows as $row) {
									?>
									<li> 
										<a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
									</li>
									<? } ?>

                                    <?php $row = Category::GetByIds(Entity::PRINTED, 33)[0]?>
                                    <li>
                                        <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                    </li>

                                    <li>
                                        <a href="<?=Yii::app()->createUrl('entity/gift', array('entity' => Entity::GetUrlKey(Entity::PERIODIC)))?>"><?=$ui->item('A_NEW_PERIODIC_FOR_GIFT'); ?></a>
                                    </li>


                                    <li id="periodic_category">
                                        <a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::PERIODIC)))?>"><?=$ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
                                    </li>
									
								</ul>
								
							</div>
							<!--<div class="span2">
								
								<img src="/new_img/banner.png" />
								
							</div>-->
							
						 </div>
					
					</li>

                    <!--Ещё-->
                    <li class="dd_box more_menu"><div class="click_arrow"></div>
                        <a href="javascript:;" class="dd"><?= $ui->item('A_NEW_MORE'); ?></a>
                        <div class="dd_box_bg dd_box_horizontal">

                            <div class="tabs">
                                <ul>

                                    <!--Сувениры-->
                                    <li class="dd_box">
                                        <div class="click_arrow"></div>
                                        <?php $row = Category::GetByIds(Entity::PRINTED, 6)[0]?>
                                        <a class="dd" href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?= $ui->item('A_NEW_PRINT_PRODUCTS'); ?></a>
                                        <div class="dd_box_bg dd_box_bg-slim list_subcategs">
                                            <ul class="list_vertical">
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 6)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 41)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 38)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 43)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 37)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=$ui->item('A_NEW_SALE');?></a>
                                                </li>

                                                <li id="printed_category" style="color: aqua">
                                                    <?=$ui->item('A_NEW_ALL_CATEGORIES'); ?>
                                                </li>

                                            </ul>
                                        </div>
                                    </li>

                                    <!--Видео-->
                                    <li class="dd_box">
                                        <div class="click_arrow"></div>
                                        <a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::VIDEO))); ?>"><?= $ui->item("A_NEW_VIDEO"); ?></a>
                                        <div class="dd_box_bg dd_box_bg-slim list_subcategs">
                                            <ul class="list_vertical">
                                                <!--<li style="color: aqua">Музыкальные видео</li>-->
                                                <?php $row = Category::GetByIds(Entity::VIDEO, 109)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::VIDEO), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <li style="color: aqua">Современные русские фильмы</li>
                                                <li style="color: aqua">Классические русские фильмы</li>

                                                <?php $row = Category::GetByIds(Entity::VIDEO, 8)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::VIDEO), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/bysubtitle', array('entity' => Entity::GetUrlKey(Entity::VIDEO), 'sid' => 8, 'title' => 'finskij'))?>"><?=$ui->item('A_NEW_VIDEO_FI_SUBTITLES');?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::VIDEO, 43)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::VIDEO), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=$ui->item('A_NEW_SALE');?></a>
                                                </li>
                                                <li id="video_category">
                                                    <a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::VIDEO)))?>"><?=$ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <!--Карты-->
                                    <li class="dd_box">
                                        <div class="click_arrow"></div>
                                        <a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MAPS))); ?>"><?= $ui->item("A_GOTOMAPS"); ?></a>
                                        <div class="dd_box_bg dd_box_bg-slim list_subcategs">
                                            <ul class="list_vertical">
                                                <?php $row = Category::GetByIds(Entity::MAPS, 9)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MAPS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=trim(ProductHelper::GetTitle($row))?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::MAPS, 8)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MAPS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=$ui->item('A_NEW_SALE');?></a>
                                                </li>
                                                <li id="maps_category">
                                                    <a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::MAPS)))?>"><?=$ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <!--Мультимедиа-->
                                    <li class="dd_box">
                                        <div class="click_arrow"></div>
                                        <a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT))); ?>" ><?= $ui->item("A_GOTOSOFT"); ?></a>

                                        <div class="dd_box_bg dd_box_bg-slim list_subcategs">
                                            <ul class="list_vertical">
                                                <?php $row = Category::GetByIds(Entity::SOFT, 1)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::SOFT, 20)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::SOFT, 16)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=$ui->item('A_NEW_SALE');?></a>
                                                </li>
                                                <li id="soft_category">
                                                    <a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::SOFT)))?>"><?=$ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
                                                </li>

                                            </ul>
                                        </div>
                                    </li>

                                    <!--Прочее-->
                                    <li class="dd_box">
                                        <div class="click_arrow"></div>
                                        <a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED))); ?>"><?= $ui->item("A_NEW_OTHER"); ?></a>

                                        <div class="dd_box_bg dd_box_bg-slim list_subcategs">
                                            <ul class="list_vertical">
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 2)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 3)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::BOOKS, 264)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::BOOKS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 30)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 44)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 15)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 8)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 34)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 42)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=ProductHelper::GetTitle($row)?></a>
                                                </li>
                                                <?php $row = Category::GetByIds(Entity::PRINTED, 37)[0]?>
                                                <li>
                                                    <a href="<?=Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii($row['title_en'])))?>"><?=$ui->item('A_NEW_SALE');?></a>
                                                </li>
                                                <li id="other_category">
                                                    <a href="<?=Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::PRINTED)))?>"><?=$ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>

                                    <!--<li><a href="<?/*=Yii::app()->createUrl('entity/authorlist', array('entity' => Entity::GetUrlKey(Entity::BOOKS))); */?>"><?/*=$ui->item('A_LEFT_AUDIO_AZ_PROPERTYLIST_AUTHORS'); */?></a></li>-->
										
                                </ul>
                                <div style="clear: both"></div>
                            </div>

                            <!--    Баннеры     -->

                            <!--<div class="content">
                                <div class="list">
                                    <ul>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                    </ul>
                                    <ul>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                        <li><a href=""><img src="/new_img/splin.png" alt=""/></a></li>
                                    </ul>
                                    <div style="clear: both"></div>
                                </div>
                            </div>-->

                        </div>
                    </li>

                    <li class="yellow_item"><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'for-alle2')); ?>"><?=$ui->item('A_NEW_GOODS_2'); ?></a></li>
                    <li class="red_item"><a href="<?= Yii::app()->createUrl('offers/list'); ?>"><?=$ui->item('A_NEW_MENU_REK'); ?></a></li>
                    <li class="red_item"><a href="<?= Yii::app()->createUrl('site/sale'); ?>"><?=$ui->item('A_NEW_DISCONT'); ?></a></li>
                    <li><a href="<?= Yii::app()->createUrl('ourstore'); ?>" class="home"><?=$ui->item('A_NEW_OURSTORE'); ?></a></li>
                </ul>
            </div>

        </div>

        </div>
		

        <?= $content; ?>


        <div class="footer">

            <div class="container">

                <div class="row">
                    <div class="span1">
                        <a href="" title="Ruslania"><img src="/new_img/logo_footer.png" alt="Ruslania" /></a>
                        <div class="text">
                            <?=$ui->item('A_NEW_DESC_FOOTER'); ?>
                            <a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>"><?=$ui->item('A_NEW_MORE_ABOUTUS'); ?></a>
                        </div>
                        <div class="contacts">

                            <div class="maps_ico"><a href="https://www.google.ru/maps/place/Bulevardi+7,+00120+Helsinki,+%D0%A4%D0%B8%D0%BD%D0%BB%D1%8F%D0%BD%D0%B4%D0%B8%D1%8F/@60.1647306,24.9368011,17z/data=!4m13!1m7!3m6!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!2zQnVsZXZhcmRpIDcsIDAwMTIwIEhlbHNpbmtpLCDQpNC40L3Qu9GP0L3QtNC40Y8!3b1!8m2!3d60.1650084!4d24.9382766!3m4!1s0x468df4ac3683d5f5:0x726f6797fa44dde1!8m2!3d60.1650084!4d24.9382766" target="_blank">Ruslania Books Corp. Bulevardi 7, FI-00120 Helsinki, Finland</a></div>
                            <div class="phone_ico"><a href="tel:+35892727070">+358 9 2727070</a></div>
                            <div class="mail_ico">generalsupports@ruslania.com</div>

                        </div>
                        <div class="social_icons">

                            <a href="https://vk.com/ruslaniabooks"><img src="/new_img/vk.png" alt="" /></a>
                            <a href="https://www.facebook.com/RuslaniaBooks/"><img src="/new_img/fb.png" alt="" /></a>
                            <a href="https://twitter.com/RuslaniaKnigi"><img src="/new_img/tw.png" alt="" /></a>
                            <!--<a href=""><img src="/new_img/gp.png" alt="" /></a>-->

                        </div>
                    </div>
                    <div class="span2">
                        <div class="span1">
                            <ul>
                                <li class="title"><?=$ui->item('A_NEW_ABOUTUS'); ?></li>

                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'aboutus')); ?>"><?= $ui->item("A_ABOUTUS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'csr')); ?>"><?= $ui->item("A_CSR"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions')); ?>"><?= $ui->item("MSG_CONDITIONS_OF_USE"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions_order')); ?>"><?= $ui->item("YM_CONTEXT_CONDITIONS_ORDER_ALL"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'conditions_subscription')); ?>"><?= $ui->item("YM_CONTEXT_CONDITIONS_ORDER_PRD"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'contact')); ?>"><?= $ui->item("YM_CONTEXT_CONTACTUS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'legal_notice')); ?>"><?= $ui->item("YM_CONTEXT_LEGAL_NOTICE"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'faq')); ?>"><?= $ui->item("A_FAQ"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'sitemap')); ?>"><?= $ui->item("A_SITEMAP"); ?></a></li>
                            </ul>
                        </div><div class="span1">
                            <ul>
                                <li class="title"><?=$ui->item('A_NEW_OURPREDL'); ?></li>

                                <li><a href="<?= Yii::app()->createUrl('site/sale'); ?>"><?= $ui->item("MENU_SALE"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('offers/list'); ?>"><?= $ui->item("RUSLANIA_RECOMMENDS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'offers_partners')); ?>"><?= $ui->item("A_OFFERS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/static', array('page' => 'offers_partners')); ?>">– <?= $ui->item("A_OFFERS_PARTNERS"); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('offers/special', array('mode' => 'uni')); ?>">– <?= $ui->item("A_OFFERS_UNIVERCITY"); ?></a></li>
                            </ul>
                        </div><div class="span1">
                            <ul>
                                <li class="title"><?=$ui->item('A_NEW_USERS'); ?></li>
								
								<?php if (Yii::app()->user->isGuest) : ?>
								
                                <li><a href="<?= Yii::app()->createUrl('site/register'); ?>"><?= $ui->item('A_REGISTER'); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('site/login'); ?>"><?= $ui->item('A_SIGNIN'); ?></a></li>
                                <li><a href="<?= Yii::app()->createUrl('cart/view'); ?>"><?= $ui->item('A_SHOPCART'); ?></a></li>
                                
                                <!-- <li><a href="">Выход</a></li>-->
								<?php else : ?>
									<li><a href="<?=Yii::app()->createUrl('client/me'); ?>"><?= $ui->item('YM_CONTEXT_PERSONAL_MAIN'); ?></a></li>
									<li><a href="<?= Yii::app()->createUrl('cart/view'); ?>"><?= $ui->item('A_SHOPCART'); ?></a></li>
									<li><a href="/my/memo"><?=$ui->item('A_NEW_MY_FAVORITE'); ?></a></li>
									<li><a href="<?= Yii::app()->createUrl('site/logout'); ?>"><?= $ui->item('YM_CONTEXT_PERSONAL_LOGOUT'); ?></a></li>
								<?endif;?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row payment">

                    <div class="span1">
                        <img src="https://img.paytrail.com/?id=34135&type=horizontal&cols=18&text=0&auth=b6c2c7566147a60e" width="770" alt="Ruslania, PayTrail" />
                    </div>
                    <div class="span2">

                        <img src="/new_img/payment2.png" alt="secures by thawte" />
						<!-- <img src="https://seal.thawte.com/getthawteseal?at=0&sealid=1&dn=RUSLANIA.COM&lang=en&gmtoff=-180" alt="" /> -->
                        <img src="/new_img/payment3.png" alt="PayPal verified" />
                        <img src="/new_img/buyer_protection.jpg" alt="PayPal buyer protection" />
                        <img src="/new_img/payment4.png" alt="verified by visa MasterCard" />

                    </div>

                </div>

                <div class="copyright">

                    2017 © <b>Ruslania</b> - All rights Reserved

                </div>

            </div>

        </div>

		
		<link rel="stylesheet" href="/css/magnific-popup.css" >
		<div id="periodic-price-form" class="white-popup-block mfp-hide white-popup">
    <h2><?=$ui->item('PERIODIC_POPUP_HEADER'); ?></h2>
    <h4 id="formTitle"></h4>
    <h5 id="formMonths"></h5>
    <input type="hidden" name="eid" value=""/>
    <input type="hidden" name="iid" value=""/>
    <input type="hidden" name="qty" value=""/>

    <div class="periodic_choice">
        <div id="finPrice"></div>
        <button id="finSubscription"><?=$ui->item('PERIODIC_POPUP_FINLAND_BUTTON'); ?></button>
    </div>
    <div class="periodic_choice">
        <div id="worldPrice"></div>
        <button id="worldSubscription"><?=$ui->item('PERIODIC_POPUP_WORLD_BUTTON'); ?></button>
    </div>
    <div class="clearfix"></div>
</div>

    <?php if ($mess): ?>
    <script>
        $(document).ready(function () {
        document.getElementById('js_container-alert_bg').innerHTML = '<?= htmlspecialchars($mess) ?>';
        })
    </script>
    <?php endif; ?>
    </body>
</html>
