Stripe.setPublishableKey('pk_test_B8MwXuaz10DDZVcF6QJQTki0');

Stripe.applePay.checkAvailability(function(available) {
    if (available) {
    }
    else {
        $('input[type=radio][name=ptype][value="27"]')
            .attr("disabled", "disabled")
            .closest('label')
            .find('.not_supported').show();
    }
});

(function() {
    repay = function() {
        return new _Repay();
    };
    function _Repay() {}
    _Repay.prototype = {
        orderId: 0,
        ptype: 0,
        dtype: 0,
        action: '',
        init: function(options) {
            this.setConst(options);
            this.setEvents();
            return this;
        },
        setConst: function(options) {
            this.csrf = $('meta[name=csrf]').attr('content').split('=');
            this.$paymentsData = $('#paymentsData');
            this.orderId = options.orderId;
            this.ptype = options.ptype;
            this.dtype = options.dtype;
            this.action = options.action;
            if ((this.dtype > 0)&&(this.ptype == 0)) this.ptype = 25;
            this.urlSubmit = options.urlSubmit;
        },
        setEvents: function() {
            var self = this;
            this.paymentsForm();
            this.eventPayments();
        },
        paymentsForm: function() {
            var self = this;
            var $orderPay = $('.js_orderPay');
            var $orderSave = $('.js_orderSave');
            var $paymentDesc = $('.paytail_payment');
            this.$paymentsData.find('input[name=ptype]').each(function(id, el) {
                switch (el.value) {
                    case '0':
                        if (self.dtype > 0) $(el).attr("disabled", "disabled").closest('.variant').hide();
                        else if (el.value == '0') {
                            el.checked = true;
                            $orderPay.hide();
                            $orderSave.show();
                            $paymentDesc.hide();
                        }
                        break;
                    default:
                        if (parseInt(el.value) == this.ptype) {
                            el.checked = true;
                            if ((this.ptype == 13)||(this.ptype == 14)||(this.ptype == 7)) {
                                $orderPay.hide();
                                $orderSave.show();
                                $paymentDesc.hide();
                            }
                            else {
                                $orderSave.hide();
                                $orderPay.show();
                                if (this.ptype == 25) $paymentDesc.show();
                                else $paymentDesc.hide();
                            }
                        }
                        break;
                }
            });
        },
        eventPayments: function () {
            var self = this;
            var $orderButton = $('.order_start');
            $orderButton.on('click', function() { self.sendforma(); return false; });

            var $orderPay = $('.js_orderPay');
            var $orderSave = $('.js_orderSave');
            var $paymentDesc = $('.paytail_payment');
            this.$paymentsData.find('.qbtn2').each(function(id, el) {
                $(el).on('click', function() {
                    var $t = $(this);
                    self.$paymentsData.find('.info_box').hide();
                    $t.siblings('.info_box').toggle().css({top: (this.offsetTop + 30), left: (this.offsetLeft - 300)});
                });
            });
            this.$paymentsData.find('input[name=ptype]').each(function(id, el) {
                $(el).on('click', function() {
                    var $this = $(this);
                    $this.closest('.variant').siblings().find('label').removeClass('act');
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

        paypal: function(form) {
            $(form).appendTo(this.$paymentsData).submit();
        },

        paytrail: function(form) {
            $(form).appendTo(this.$paymentsData).submit();
        },

        applepay: function(idOrder, urls, paymentRequest) {
            var self = this;
            var session = Stripe.applePay.buildSession(paymentRequest,
                function(result, completion) {
                    var data = {
                        'token':result.token.id,
                        'orderId':idOrder
                    };
                    data[self.csrf[0]] = self.csrf[1];
                    $.post(urls.charges, data).done(function() {
                        completion(ApplePaySession.STATUS_SUCCESS);
                        // You can now redirect the user to a receipt page, etc.
                        window.location.href = urls.accept;
                    }).fail(function() {
                        completion(ApplePaySession.STATUS_FAILURE);
                    });

                }, function(error) {
                    console.log(error.message);
                });

            session.oncancel = function() {
                window.location.href = urls.cancel;
            };

            session.begin();
        },

        sendforma: function () {
            var self = this;
            var fd = {
                orderId:this.orderId,
                ptype:this.$paymentsData.find('input[name=ptype]:checked').val(),
                action:this.action
            };
            fd[this.csrf[0]] = this.csrf[1];
            $.ajax({
                url : self.urlSubmit,
                data: fd,
                type: 'post',
                dataType : 'json',
                success: function(r) {
                    switch (parseInt(fd['ptype'])) {
                        case 8: self.paypal(r.form); break;
                        //case 25: self.paytrail(r.form); break;
                        case 27: self.applepay(r.idOrder, r.urls, r.paymentRequest); break;
                        default: document.location.href = r.url; break;
                    }
                }
            });
        }
    };

    cart = function() {
        return new _Cart();
    };

    function _Cart() {}
    _Cart.prototype = {
        addrFormIds:['Reg', 'Address'],
        onlyPereodic: 0,
        activePromocode: false,
        activeSmartpost: false,
        userSocialId: 0,
        init: function(options) {
            if ('userData' in options) {
                this.setValues(options['userData']);
            }
            this.setConst(options);
            this.setEvents();
            if ((this.$paymentsData.find('input[name=ptype]:checked').val() == '25')&&this.confirm.checked) $('.paytail_payment').show();
            else $('.paytail_payment').hide();
            return this;
        },

        setValues: function(userData) {
             for (userField in userData) {
                switch (userField) {
                    case 'users_socials_id': this.userSocialId = parseInt(userData['users_socials_id']); break;
                    case 'email': $('.js_contactEmail').val(userData['email']); break;
                    case 'is_business':
                        if (userData['is_business'] > 0) {
                            $('form#Reg').find('input.js_userType[value="1"]').get(0).checked = true;
                        }
                        break;
                    case 'business_title':case 'receiver_first_name':case 'receiver_last_name':
                        if (userData[userField] != "") $('#Reg_' + userField).val(userData[userField]);
                        break;
                }
            }
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

            this.onlyPereodic = options.onlyPereodic;
            this.existPereodic = options.existPereodic;
            this.urlRecount = options.urlRecount;
            this.urlChangeCountry = options.urlChangeCountry;
            this.urlGetCountry = options.urlGetCountry;
            this.urlLoadStates = options.urlLoadStates;
            this.urlSubmit = options.urlSubmit;

            if (this.delivery_address) {
                if (this.delivery_address.value == '0') $('.delivery_people').show();
                else this.changeCountry();
            }

            var $promocodeBlock = $('#js_promocode');
            this.$inputPromocode = $promocodeBlock.find('input[type=text]');
            this.$submitPromocode = $promocodeBlock.find('input[type=button]');
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
                if (this.checked) {
                    self.$paymentsData.find('input[name=ptype][value="0"]').get(0).checked = true;
                    $('.js_orderPay').hide();
                    $('.js_orderSave').show();
                }
                self.showAddressFields('Reg');
                self.deleveryForm();
                self.blockPay();
                self.paymentsForm();
                self.recount(self.getPromocodeValue());
            });
            if (this.delivery_address) $(this.delivery_address).on('change', function(){
                $('#Reg').closest('div.form').hide();
                self.deleveryForm();
                self.blockPay();
                self.paymentsForm();
                if (this.value == "0") {
                    $('.delivery_people').show();
                    self.$paymentsData.find('input[name=ptype][value="0"]').get(0).checked = true;
                    $('.js_orderPay').hide();
                    $('.js_orderSave').show();
                }
                else $('.delivery_people').hide();
                self.changeCountry();
            });

            if (this.billing_address) $(this.billing_address).on('change', function(){
                $('#Adderss').hide();
            });

            $(this.confirm).on('click', function() {
                if (self.payAllow()) {
                    var $label = $(self.confirm).closest('label');
                    self.scrollTo(parseInt($label.offset().top) - 10);//5 + $label.outerHeight(true));
                }
                self.blockPay();
            });

            $(this.country).on('change', function() {
                self.blockPay();
                self.changeCountry();
                self.fillPhoneCode(this);
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
                self.fillPhoneCode(this);
            });

            $('div.choose_address .address_add').on('click', function() {
                $(this).closest('div.choose_address').siblings('div.form').show().find('.btn-cancel').show();
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
                        }

                        if ('address' in r) {
                            form.reset();
                            $block.hide();
                            $('.choose_address select.address_select').each(function(i, el) {
                                $(el).append('<option value="' + r['address']['id'] + '">' + r['address']['name'] + '</option>');
                            });
                            $block.closest('li')
                                .find('.choose_address select.address_select option[value=' + r['address']['id'] + ']')
                                .attr("selected", "selected")
                                .closest('.choose_address').show();
                            //console.log($block.closest('div').find('.choose_address'));
                            //$block.closest('div').find('.choose_address').show();
                            //$block.closest().find('.choose_address').show()
                            //    .find('select.address_select option[value=' + r['address']['id'] + ']')
                            //    .attr("selected", "selected");
                            self.deleveryForm();
                        }

                    }
                });
            });
        },

        fillPhoneCode: function(t) {
            var self = this;
            var data = {'id_country': t.value};
            data[this.csrf[0]] = this.csrf[1];
            $.ajax({
                url: self.urlGetCountry,
                data: data,
                type: 'post',
                dataType: 'json',
                success: function (r) {
                    if ('phone_code' in r) {
                        $(t).closest('table.address').find('input.js_contactPhone').val('+' + r['phone_code']);
                    }
                }
            });
        },

        showAddressFields: function(idForm) {
            var $form = $('#' + idForm);
            var userType = $form.find('input[type=radio].js_userType:checked').val();
            if (!userType) userType = 0;
            if (userType == '1') {
                $('.js_userName').hide();
                $('.js_firmName').show();
            }
            else {
                $('.js_userName').show();
                $('.js_firmName').hide();
            }
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
                else {
                    var dtype = 0;
                    var econom;
                    this.$deliveryTypeData.show().find('input[name=dtype]').each(function (id, el) {
                        if (el.checked) dtype = parseInt(el.value);
                        if (el.value == "3") econom = el;
                        $(el).removeAttr("disabled").closest('label').removeClass('deny');
                    });
                    if ((dtype == 0)&&econom) econom.checked = true;
                }
            }
            else if (this.onlyPereodic) this.$deliveryTypeData.hide();
        },
        paymentsForm: function() {
            var self = this;
            var $paymentDesc = $('.paytail_payment');
            var paytrail = this.$paymentsData.find('input[name=ptype][value="25"]').get(0);
            this.$paymentsData.find('input[name=ptype]').each(function(id, el) {
                switch (el.value) {
                    case '0':
                        if (self.takeInStore&&self.takeInStore.checked) $(el).closest('.variant').show();
                        else if (self.delivery_address&&(self.delivery_address.value == '0')) $(el).closest('.variant').show();
                        else {
                            $(el).closest('.variant').hide();
                            if (el.checked) {
                                el.checked = false;
                                paytrail.checked = true;
                                $('.js_orderPay').show();
                                $('.js_orderSave').hide();
                                $(paytrail).closest('label').addClass('act')
                                    .closest('.variant').siblings().find('label').removeClass('act');
                                $paymentDesc.show();
                            }
                        }
                        break;
                    /*case '14':*/case '13':case '7':
                        if (self.takeInStore&&self.takeInStore.checked) {
                            $(el).closest('.variant').hide();
                            if (el.checked) {
                                el.checked = false;
                                paytrail.checked = true;
                                $('.js_orderPay').show();
                                $('.js_orderSave').hide();
                                $(paytrail).closest('label').addClass('act')
                                    .closest('.variant').siblings().find('label').removeClass('act');
                                $paymentDesc.show();
                            }
                        }
                        else if (self.delivery_address&&(self.delivery_address.value == '0')) {
                            $(el).closest('.variant').hide();
                            if (el.checked) {
                                el.checked = false;
                                paytrail.checked = true;
                                $('.js_orderPay').show();
                                $('.js_orderSave').hide();
                                $(paytrail).closest('label').addClass('act')
                                    .closest('.variant').siblings().find('label').removeClass('act');
                                $paymentDesc.show();
                            }
                        }
                        else $(el).closest('.variant').show();
                        break;
                }
            });
        },
        blockPay: function() {
            var $block = $('ol li .op');
            if (this.payAllow()) {
                $block.hide();
                if ((this.$paymentsData.find('input[name=ptype]:checked').val() == '25')&&this.confirm.checked) $('.paytail_payment').show();
                else $('.paytail_payment').hide();
            }
            else {
                $block.show();
                $('.paytail_payment').hide();
            }
            //if (!this.confirm.checked) $block.show();
            //else {
            //    if (this.takeInStore) {
            //        if (this.takeInStore.checked||(this.country.value > 0)) $block.hide();
            //        else $block.show();
            //    }
            //    else $block.hide();
            //}
        },
        showPayerForm: function() {
            var $form = $('#Address');
            if (this.billing_address) {
                var $adr = $(this.billing_address);
                if (this.oneAddr.checked) {
                    $adr.closest('div').hide();
                    $form.closest('div').hide();
                }
                else {
                    if ($adr.children('option').length > 0) {
                        $adr.closest('div').show();
                        $form.closest('div').hide().find('.btn-cancel').show();
                    }
                    else {
                        $adr.closest('div').hide();
                        $form.closest('div').show().find('.btn-cancel').hide();
                    }
                }
            }
            else {
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

            var $orderPay = $('.js_orderPay');
            var $orderSave = $('.js_orderSave');
            var $paymentDesc = $('.paytail_payment');
            this.$paymentsData.find('.qbtn2').each(function(id, el) {
                $(el).on('click', function() {
                    var $t = $(this);
                    self.$paymentsData.find('.info_box').hide();
                    $t.siblings('.info_box').toggle().css({top: (this.offsetTop + 30), left: (this.offsetLeft - 300)});
                });
            });
            this.$paymentsData.find('input[name=ptype]').each(function(id, el) {
                $(el).on('click', function() {
                    var $this = $(this);
                    $this.closest('.variant').siblings().find('label').removeClass('act');
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
                    if (this.value == '0') {
                        if (self.takeInStore) self.takeInStore.checked = true;
                        if (self.delivery_address) {
                            $(self.delivery_address).find('option[value=0]').attr("selected", "selected");
                            $(this).closest('.variant').siblings('.variant').each(function(num, variant){
                                $(variant).find('input[name=dtype]')
                                    .attr("disabled", "disabled")
                                    .closest('label').addClass('deny');
                            });
                        }

                        self.$paymentsData.find('label').each(function (num, label) {
                            var $el = $(label);
                            var ptype = $el.find('input[name=ptype]').get(0);
                            if (ptype) {
                                if (ptype.value == "0") {
                                    ptype.checked = true;
                                    $el.addClass('act');
                                }
                                else $el.removeClass('act');
                            }
                        });
                        $('.js_orderPay').hide();
                        $('.js_orderSave').show();
                    }
                    else {
                        if (self.takeInStore) self.takeInStore.checked = false;
                    }
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
            $('span.qbtn2').each(function(id, el) {
                $(el).on('click', function() {
                    var $t = $(this);
                    $t.siblings('.info_box').toggle().css({top: ($t.offset().top + 30), left: ($t.offset().left - 300)});
                });
            });
        },
        getPromocodeValue: function() {
            if (this.activePromocode) return this.$inputPromocode.val().trim();
            return '';
        },

        changeCountry: function () {
            var self = this;
            if (!this.onlyPereodic) {
                var aid = 0;
                var cid = 0;
                if (self.delivery_address) aid = self.delivery_address.value;
                else {
                    if (this.takeInStore && !this.takeInStore.checked) cid = self.country.value;
                    else if (this.onlyPereodic) cid = self.country.value;
                }
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
            }
            self.recount(self.getPromocodeValue());
            self.showStates(self.country);
        },

        recount: function(value) {
            var $regForm = $('#Reg');
            var self = this;
            var dtype = this.$deliveryTypeData.find('input[name=dtype]:checked').val();
            var aid = 0;
            var cid = 0;
            if (self.delivery_address) aid = self.delivery_address.value;
            else {
                if (this.takeInStore && !this.takeInStore.checked) cid = self.country.value;
                else if (this.onlyPereodic) cid = self.country.value;
            }
            if ((aid == 0)&&(cid == 0)) dtype = 0;
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
                    $('.delivery_name').html(r.deliveryName);
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
            if (this.userSocialId > 0) fd['userSocialId'] = this.userSocialId;
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
                    if (!$f.hasClass('address_select')) errors.push(f);
                }
            });
            fd['promocode'] = this.getPromocodeValue();
            if (this.takeInStore) {
                if (this.takeInStore.checked) fd['dtype'] = 0;
            }
            else if (this.delivery_address) {
                if (this.delivery_address.value == '0') fd['dtype'] = 0;
            }
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
                        }
                        else {
                            switch (parseInt(fd['ptype'])) {
                                case 8: self.paypal(r.form); break;
                                //case 25: self.paytrail(r.form); break;
                                case 27: self.applepay(r.idOrder, r.urls, r.paymentRequest); break;
                                default: document.location.href = r.url; break;
                            }
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
            this.scrollTo(firstErrorPos);
        },

        payAllow: function() {
            if (!this.confirm.checked) return false;

            if (this.takeInStore) {
                if (this.takeInStore.checked||(this.country.value > 0)) return true;
            }
            else if (this.delivery_address) return this.confirm.checked;
            else return this.country.value > 0;

            return false;
        },

        scrollTo: function(top) { scrollTo(top); },

        paypal: function(form) {
            $(form).appendTo('#js_orderForm').submit();
        },

        paytrail: function(form) {
            $(form).appendTo('#js_orderForm').submit();
        },

        applepay: function(idOrder, urls, paymentRequest) {
            var self = this;
            var session = Stripe.applePay.buildSession(paymentRequest,
                function(result, completion) {
                    var data = {
                        'token':result.token.id,
                        'orderId':idOrder
                    };
                    data[self.csrf[0]] = self.csrf[1];
                    $.post(urls.charges, data).done(function() {
                        completion(ApplePaySession.STATUS_SUCCESS);
                        // You can now redirect the user to a receipt page, etc.
                        window.location.href = urls.accept;
                    }).fail(function() {
                        completion(ApplePaySession.STATUS_FAILURE);
                    });

                }, function(error) {
                    console.log(error.message);
                });

            session.oncancel = function() {
                window.location.href = urls.cancel;
            };

            session.begin();
        }

    };

}());

function scrollTo(top) {
    jQuery("html:not(:animated),body:not(:animated)").animate({scrollTop: top}, 120);
}

function search_smartpost(loadUrl, countryId, loadName, buttonName) {
    var csrf = $('meta[name=csrf]').attr('content').split('=');
    $('.start-search-smartpost').html(loadName);
    var country = 'FI';
    if (countryId == 62) country = 'EE';
    $('.box_smartpost').html('').hide();
    $('.sel_smartpost').html('');
    $.post(loadUrl, {ind: $('.smartpost_index').val(), YII_CSRF_TOKEN: csrf[1], country: country}, function (data) {
        if (data) {
            $('.box_smartpost').show().html(data);
        } else {
            $('.box_smartpost').html('').hide();
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
    scrollTo(parseInt($('#js_smartpostBox').offset().top));
}

$(document).click(function (event) {
    if ($(event.target).closest(".qbtn2,.info_box").length) return;
    $('.info_box').hide();
    event.stopPropagation();
});
