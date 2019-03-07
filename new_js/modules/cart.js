(function() {
    cart = function() {
        return new _Cart();
    };

    function _Cart() {}
    _Cart.prototype = {
        addrFormIds:['Reg', 'Address'],
        onlyPereodic: 0,
        activePromocode: false,
        activeSmartpost: false,
        init: function(options) {
            this.setConst(options);
            this.setEvents();
            if (this.$paymentsData.find('input[name=ptype]:checked').val() == '25') $('.paytail_payment').show();
            else $('.paytail_payment').hide();
            return this;
        },
        setConst: function(options) {
            this.takeInStore = document.getElementById('check_addressa'); //забрать в магазине
            this.delivery_address = document.getElementById('delivery_address_id');
            this.billing_address = document.getElementById('billing_address_id');
            this.oneAddr = document.getElementById('addr_buyer'); //адрес плательщика и получателя совпадают
            this.confirm = document.getElementById('confirm'); //согласен с условиями
            this.country = document.getElementById('Reg_country'); //страна доставки
            this.nVAT = document.getElementById('Reg_business_number1'); //Номер VAT компании
            this.csrf = $('meta[name=csrf]').attr('content').split('=');
            this.$deliveryTypeData = $('#deliveryTypeData');
            this.$paymentsData = $('#paymentsData');
            this.promocode = $('#promocode');
            this.$smartpostBox = $('#js_smartpostBox');

            var $promocodeBlock = $('#js_promocode');
            this.$inputPromocode = $promocodeBlock.find('input[type=text]');
            this.$submitPromocode = $promocodeBlock.find('input[type=button]');

            this.onlyPereodic = options.onlyPereodic;
            this.existPereodic = options.existPereodic;
            this.urlRecount = options.urlRecount;
            this.urlChangeCountry = options.urlChangeCountry;
            this.urlLoadStates = options.urlLoadStates;
            this.urlSubmit = options.urlSubmit;
        },
        setEvents: function() {
            var self = this;
            for (var i = 0, len = this.addrFormIds.length; i < len; i++) {
                this.showAddressFields(this.addrFormIds[i]);
                this.eventUserType(this.addrFormIds[i]);
            }
            this.deleveryForm();
            this.paymentsForm();
            this.showPayerForm();
            this.blockPay();
            this.eventPayments();
            this.eventDeliverys();

            if (this.takeInStore) $(this.takeInStore).on('click', function() {
                self.showAddressFields('Reg');
                self.deleveryForm();
                self.blockPay();
                self.paymentsForm();
            });
            if (this.delivery_address) $(this.delivery_address).on('change', function(){
                $('#Reg').hide();
                self.deleveryForm();
                self.blockPay();
                self.paymentsForm();
            });

            if (this.billing_address) $(this.billing_address).on('change', function(){
                $('#Adderss').hide();
            });

            $(this.confirm).on('click', function() {
                self.blockPay();
            });

            $(this.country).on('change', function() {
                self.blockPay();
                self.changeCountry();
            });

            $(this.oneAddr).on('click', function() {
                self.showPayerForm();
            });

            self.$submitPromocode.on('click', function() {
                self.recount(self.$inputPromocode.val().trim());
            });

            $(this.nVAT).on('blur', function() {
                self.recount(self.getPromocodeValue());
            });

            $('#Address_country').on('change', function() {
                self.showStates(this);
                self.showAddressFields('Address');
            });

            $('div.choose_address .address_add').on('click', function() {
                $(this).closest('div.choose_address').siblings('div.form').show();
            });

            $('div.form .btn-cancel').on('click', function() {
                $(this).closest('div.form').hide();
            });

            $('div.form .btn-success').on('click', function() {
                var $block = $(this).closest('div.form');
                var form = $block.find('form').get(0);
                $.ajax({
                    url: form.getAttribute('action') + '?alias=' + form.getAttribute('id'),
                    data: $(form).serialize(),
                    type: 'post',
                    dataType : 'json',
                    success: function (r) {
                        console.log(r);
                        var errors = [];
                        if ('errors' in r) {
                            for (field in r.errors) {
                                var t = document.getElementById(field);
                                if (t) errors.push(t);
                                else if (field == 'forgot_button') {
                                    t = document.getElementById('Reg_contact_email');
                                    $(t).siblings('.info_box').html(r.errors[field]).toggle();
                                    errors.push(t);
                                }
                            }
                        }
                        if (errors.length) {
                            self.viewErrors(errors);
                            console.log(errors);
                        }

                        if ('address' in r) {
                            form.reset();
                            $block.hide();
                            $('.choose_address select.address_select').each(function(i, el) {
                                $(el).append('<option value="' + r['address']['id'] + '">' + r['address']['name'] + '</option>');
                            });
                            $block.closest().find('.choose_address select.address_select option[value=' + r['address']['id'] + ']').attr("selected", "selected");
                            self.deleveryForm();
                        }

                    }
                });
            });
        },

        showAddressFields: function(idForm) {
            var $form = $('#' + idForm);
            var userType = $form.find('input[type=radio].js_userType:checked').val();
            if (!userType) userType = 0;
            var takeInStore = 0;
            if (this.takeInStore) {
                if ((idForm == 'Reg')&&this.takeInStore.checked) takeInStore = 1;
            }

            var self = this;
            $form.find('tr').each(function (id, elem) {
                var $elem = $(elem);
                switch (userType) {
                    case '1':
                        //проверка для verkkolasku
                        if ($elem.hasClass('js_firm')) {
                            if ((idForm == 'Address')&&$elem.hasClass('verkkolasku')) {
                                if ($('#Address_country').val() == '68') $elem.show();
                                else $elem.hide();
                            }
                            else $elem.show();
                        }
                        break;
                    case '2':
                        if ($elem.hasClass('js_firm')) $elem.hide();
                        break;
                }
                if ($elem.hasClass('js_delivery')) {
                    if (takeInStore&&!self.existPereodic) {
                        $elem.hide();
                    }
                    else {
                        //проверка для штаты
                        if ($elem.hasClass('states_list')) {
                            if ($elem.find('option').length > 1) $elem.show();
                            else $elem.hide();
                        }
                        else $elem.show();
                    }
                }
            });
        },

        deleveryForm: function() {
            if (this.takeInStore) {
                if (this.onlyPereodic||this.takeInStore.checked) this.$deliveryTypeData.hide();
                else this.$deliveryTypeData.show();
            }
            else if (this.delivery_address) {
                if (this.onlyPereodic||(this.delivery_address.value == '0')) this.$deliveryTypeData.hide();
                else this.$deliveryTypeData.show();
            }
        },
        paymentsForm: function() {
            var self = this;
            var $paymentDesc = $('.paytail_payment');
            var paytrail = this.$paymentsData.find('input[name=ptype][value="25"]').get(0);
            this.$paymentsData.find('input[name=ptype]').each(function(id, el) {
                switch (el.value) {
                    case '0':
                        if (self.takeInStore&&self.takeInStore.checked) $(el).closest('label').show();
                        else if (self.delivery_address&&(self.delivery_address.value == '0')) $(el).closest('label').show();
                        else {
                            $(el).closest('label').hide();
                            if (el.checked) {
                                el.checked = false;
                                paytrail.checked = true;
                                $(paytrail).closest('label').addClass('act').siblings().removeClass('act');
                                $paymentDesc.show();
                            }
                        }
                        break;
                    case '14':case '13':case '7':
                        if (self.takeInStore&&self.takeInStore.checked) {
                            $(el).closest('label').hide();
                            if (el.checked) {
                                el.checked = false;
                                paytrail.checked = true;
                                $(paytrail).closest('label').addClass('act').siblings().removeClass('act');
                                $paymentDesc.show();
                            }
                        }
                        else if (self.delivery_address&&(self.delivery_address.value == '0')) {
                            $(el).closest('label').hide();
                            if (el.checked) {
                                el.checked = false;
                                paytrail.checked = true;
                                $(paytrail).closest('label').addClass('act').siblings().removeClass('act');
                                $paymentDesc.show();
                            }
                        }
                        else $(el).closest('label').show();
                        break;
                }
            });
        },
        blockPay: function() {
            var $block = $('ol li .op');
            if (!this.confirm.checked) $block.show();
            else {
                if (this.takeInStore) {
                    if (this.takeInStore.checked||(this.country.value > 0)) $block.hide();
                    else $block.show();
                }
                else $block.hide();
            }
        },
        showPayerForm: function() {
            if (this.billing_address) {
                if (this.oneAddr.checked) $(this.billing_address).closest('div').hide();
                else $(this.billing_address).closest('div').show();
            }
            else {
                var $form = $('#Address');
                if (this.oneAddr.checked) $form.hide();
                else $form.show();
            }
        },
        eventUserType: function(idForm) {
            var self = this;
            var $form = $('#' + idForm);
            $form.find('input[type=radio].js_userType').each(function(i, el) {
                $(el).on('click', function(){
                    self.showAddressFields(idForm);
                    self.deleveryForm();
                });
            });
        },
        eventPayments: function () {
            var self = this;
            var $orderButton = $('.order_start');
            $orderButton.on('click', function() { self.sendforma(); return false; });

            var $orderPay = $orderButton.find('.js_orderPay');
            var $orderSave = $orderButton.find('.js_orderSave');
            var $paymentDesc = $('.paytail_payment');
            this.$paymentsData.find('input[name=ptype]').each(function(id, el) {
                $(el).on('click', function() {
                    var $this = $(this);
                    $this.closest('label').siblings().removeClass('act');
                    $this.closest('label').addClass('act');
                    switch(this.value) {
                        case '0': case '14':case '13': case '7':
                            $orderPay.hide();
                            $orderSave.show();
                            $paymentDesc.hide();
                            break;
                        default:
                            $orderSave.hide();
                            $orderPay.show();
                            if (this.value == '25') $paymentDesc.show();
                            else $paymentDesc.hide();
                            break;
                    }
                });
            });
        },
        eventDeliverys: function() {
            var self = this;
            this.$deliveryTypeData.find('input[name=dtype]').each(function(id, el) {
                $(el).on('click', function() {
                    var $this = $(this);
                    $this.closest('.variant').siblings().find('label').removeClass('act');
                    $this.closest('label').addClass('act');
                    if (self.takeInStore) self.takeInStore.checked = (this.value == '0');
                    if (self.delivery_address&&(this.value == '0')) $(self.delivery_address).find('option[value=0]').attr("selected", "selected");
                    self.paymentsForm();
                    if (self.activeSmartpost) {
                        if (this.value == '3') self.$smartpostBox.show();
                        else self.$smartpostBox.hide();
                    }
                    self.recount(self.getPromocodeValue());
                });
            });
            this.$deliveryTypeData.find('.qbtn2').each(function(id, el) {
                $(el).on('click', function() {
                    self.$deliveryTypeData.find('.info_box').hide();
                    $(this).siblings('.info_box').toggle();
                });
            });
        },
        getPromocodeValue: function() {
            if (this.activePromocode) return this.$inputPromocode.val().trim();
            return '';
        },

        changeCountry: function () {
            var self = this;
            var aid = 0;
            var cid = this.country.value;
            var data = {
                'aid':aid,
                'cid':cid
            };
            data[this.csrf[0]] = this.csrf[1];
            $.ajax({
                url: self.urlChangeCountry,
                data: data,
                type: 'post',
                dataType : 'json',
                success: function (r) {
                    for (i in r.tarif) {
                        var $block = self.$deliveryTypeData
                            .find('input[type=radio][value=' + r.tarif[i]['id'] + ']')
                            .closest('label');
                        $block.find('.js_xDays').html(r.tarif[i]['deliveryTime']);
                        $block.find('.js_price').html(r.tarif[i]['value']);
                        $block.siblings('.info_box').html(r.tarif[i]['description']);
                    }
                    self.$smartpostBox.html(r.smartpost);
                    if (r.smartpost == '') {
                        self.activeSmartpost = false;
                        self.$smartpostBox.hide();
                    }
                    else {
                        self.activeSmartpost = true;
                        self.$smartpostBox.show();
                    }
                }
            });
            self.recount(self.getPromocodeValue());
            self.showStates(self.country);
        },

        recount: function(value) {
            var $regForm = $('#Reg');
            var self = this;
            var dtype = this.$deliveryTypeData.find('input[name=dtype]:checked').val();
            var aid = 0;
            var cid = this.country.value;
            var userType = $regForm.find('input[type=radio].js_userType:checked').val();
            var nVAT = this.nVAT.value;//
            if (userType != 1) nVAT = '';

            var data = {
                'promocode':value,
                'dtype':dtype,
                'aid':aid,
                'cid':cid,
                'nvat':nVAT
            };
            data[this.csrf[0]] = this.csrf[1];
            $.ajax({
                url: self.urlRecount,
                data: data,
                type: 'post',
                dataType : 'json',
                success: function (r) {
                    //items_cost - цена товаров
                    //js_item_{eid}_{id} - строка с товаром
                    //item_cost - цена товара
                    //delivery_cost - цена доставки
                    //itogo_cost - общая стоимость
                    $('.itogo_cost').html(r.totalPrice + ' ' + r.currency);
                    $('.items_cost').html(r.itemsPrice + ' ' + r.currency);
                    $('.delivery_cost').html(r.deliveryPrice + ' ' + r.currency);
                    for (itemId in r.pricesValues) {
                        $('.js_' + itemId).find('.item_cost').html(r.pricesValues[itemId] + ' ' + r.currency);
                    }

                    self.$inputPromocode.closest('div').siblings().remove();
                    if (value != '') {
                        var $buf = self.$inputPromocode.closest('div');
                        var $elem = $('<div style="font-weight: normal;"></div>');
                        if ('promocodeValue' in r.briefly) {
                            $elem.append('<span style="margin-right: 20px;">' + r.briefly['promocodeValue'] + ' ' + r.briefly['promocodeUnit'] + '</span>');
                            self.activePromocode = true;
                        }
                        else if ('message' in r.briefly) {
                            $elem.append('<span style="margin-right: 20px;">' + r.briefly['message'] + '</span>');
                            self.activePromocode = false;
                        }
                        $('<span style="color:#ed1d24; cursor: pointer;">&#10008;</span>').appendTo($elem).click(function(){ self.recount(''); });
                        if ('name' in r.briefly) $elem.append(r.briefly['name']);
                        $buf.after($elem);
                    }
                    else {
                        self.activePromocode = false;
                        self.$inputPromocode.val('');
                    }
                }
            });

        },

        showStates: function (t) {
            var self = this;
            var cid = t.value;
            var data = {'cid':cid};
            data[this.csrf[0]] = this.csrf[1];
            $.ajax({
                url: self.urlLoadStates,
                data: data,
                type: 'post',
                dataType: 'json',
                success: function (r) {
                    var $statesTr = $(t).closest('tr').siblings('.states_list');
                    var $states = $statesTr.find('select');

                    $states.find('option').each(function(i, el) {
                        if (el.value > 0) el.remove();
                    });
                    var len = r.length;
                    if (len == 0) $statesTr.hide();
                    else {
                        $statesTr.show();
                        for (var i = 0; i < len; i++) {
                            $states.append('<option value="' + r[i]['id'] + '">' + r[i]['title_long'] + '</option>');
                        }
                    }
                }
            });
        },

        sendforma: function () {
            $('.error').removeClass('error');
            $('.texterror').hide();

            var self = this;
            var fd = {};
            var errors = [];
            if (!this.confirm.checked) errors.push(this.confirm);
            $('#js_orderForm').find('input[name], textarea[name], select[name]').each(function(i, f) {
                var $f = $(f);
                switch (f.type) {
                    case 'radio':case 'checkbox':
                        if (f.checked) fd[f.name] = f.value;
                    break;
                    default: fd[f.name] = f.value; break;
                }
                if ($f.is(':visible')&&((f.value == "")||(f.value == 0))&&($f.siblings('.texterror').length > 0)) {
                    errors.push(f);
                }
            });
            fd['promocode'] = this.getPromocodeValue();
            if (this.activeSmartpost && (fd['dtype'] == '3')) fd['pickpoint_address'] = $('#pickpoint_address').val();
            fd[this.csrf[0]] = this.csrf[1];
            if (errors.length) {
                self.viewErrors(errors);
            }
            else {
                $.ajax({
                    url : self.urlSubmit,
                    data: fd,
                    type: 'post',
                    dataType : 'json',
                    success: function(r) {
                        var errors = [];
                        if ('errors' in r) {
                            for (field in r.errors) {
                                var t = document.getElementById(field);
                                if (t) errors.push(t);
                                else if (field == 'forgot_button') {
                                    t = document.getElementById('Reg_contact_email');
                                    $(t).siblings('.info_box').html(r.errors[field]).toggle();
                                    errors.push(t);
                                }
                            }
                        }
                        if (errors.length) {
                            self.viewErrors(errors);
                            console.log(errors);
                        }
                        else {
                            document.location.href = r.url;
                        }
                    }
                });
            }
        },

        viewErrors: function(errors) {
            var len = errors.length;
            var firstErrorPos = 0;
            for (var i = 0; i < len; i++) {
                var $f = $(errors[i]);
                if (errors[i].name == 'confirm') {
                    var $label = $f.closest('label');
                    if (firstErrorPos == 0) firstErrorPos = parseInt($label.offset().top) - 10;
                    else firstErrorPos = Math.min(firstErrorPos, parseInt($label.offset().top) - 10);
                    $label.addClass('error')
                        .siblings('.texterror').show();
                }
                else {
                    if (firstErrorPos == 0) firstErrorPos = parseInt($f.offset().top) - 10;
                    else firstErrorPos = Math.min(firstErrorPos, parseInt($f.offset().top) - 10);
                    $f.addClass('error')
                        .siblings('.texterror').show();
                }
            }
            jQuery("html:not(:animated),body:not(:animated)").animate({scrollTop: firstErrorPos}, 120);
        }

    };

}());

function search_smartpost(loadUrl, countryId, loadName, buttonName) {
    var csrf = $('meta[name=csrf]').attr('content').split('=');
    $('.start-search-smartpost').html(loadName);
    var country = 'FI';
    if (countryId == 62) country = 'EE';
    $('.box_smartpost').html('');
    $('.sel_smartpost').html('');
    $('.box_smartpost').hide();
    $.post(loadUrl, {ind: $('.smartpost_index').val(), YII_CSRF_TOKEN: csrf[1], country: country}, function (data) {
        if (data) {
            $('.box_smartpost').show();
            $('.box_smartpost').html(data);
        } else {
            $('.box_smartpost').html('');
            $('.box_smartpost').hide();
        }
        $('.start-search-smartpost').html(buttonName);
    });

}

function select_smartpost_row(cont, buttonName, txt) {
    var $cont = $(cont);

    $('.row_smartpost').hide().removeClass('act');
    $('.close_points').hide();
    $('.more_points').show();
    $cont.closest('.row_smartpost').addClass('act').show();

    //$('.address.addr2, label.addr_buyer').hide();

    $('.btn.btn-success', $cont.closest('.row_smartpost')).html(buttonName);

    $('#pickpoint_address').val(txt);
    $('.smartpost_action').toggle();

}

$(document).click(function (event) {
    if ($(event.target).closest(".qbtn2,.info_box").length) return;
    $('.info_box').hide();
    event.stopPropagation();
});
