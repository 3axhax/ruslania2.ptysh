(function() {
    cart = function() {
        return new _Cart();
    };

    function _Cart() {}
    _Cart.prototype = {
        addrFormIds:['Reg', 'Address'],
        onlyPereodic: 0,
        activePromocode: false,
        init: function(options) {
            this.setConst(options);
            this.setEvents();
            return this;
        },
        setConst: function(options) {
            this.takeInStore = document.getElementById('check_addressa'); //забрать в магазине
            this.oneAddr = document.getElementById('addr_buyer'); //адрес плательщика и получателя совпадают
            this.confirm = document.getElementById('confirm'); //согласен с условиями
            this.country = document.getElementById('Reg_country'); //страна доставки
            this.nVAT = document.getElementById('Reg_business_number1'); //Номер VAT компании
            this.csrf = $('meta[name=csrf]').attr('content').split('=');
            this.$deliveryTypeData = $('#deliveryTypeData');
            this.$paymentsData = $('#paymentsData');
            this.promocode = $('#promocode');

            var $promocodeBlock = $('#js_promocode');
            this.$inputPromocode = $promocodeBlock.find('input[type=text]');
            this.$submitPromocode = $promocodeBlock.find('input[type=button]');

            this.onlyPereodic = options.onlyPereodic;
            this.existPereodic = options.existPereodic;
            this.urlRecount = options.urlRecount;
            this.urlChangeCountry = options.urlChangeCountry;
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

            $(this.takeInStore).on('click', function() {
                self.showAddressFields('Reg');
                self.deleveryForm();
                self.blockPay();
                self.paymentsForm();
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
        },

        showAddressFields: function(idForm) {
            var $form = $('#' + idForm);
            var userType = $form.find('input[type=radio].js_userType:checked').val();
            if (!userType) userType = 0;
            var takeInStore = 0;
            if ((idForm == 'Reg')&&this.takeInStore.checked) takeInStore = 1;

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
            if (this.onlyPereodic||this.takeInStore.checked) this.$deliveryTypeData.hide();
            else this.$deliveryTypeData.show();
        },
        paymentsForm: function() {
            var self = this;
            this.$paymentsData.find('input[name=ptype]').each(function(id, el) {
                switch (el.value) {
                    case '0':
                        if (self.takeInStore&&self.takeInStore.checked) $(el).closest('label').show();
                        else $(el).closest('label').hide();
                        break;
                    case '14':case '13':case '7':
                    if (self.takeInStore&&self.takeInStore.checked) $(el).closest('label').hide();
                    else $(el).closest('label').show();
                        break;
                }
            });
        },
        blockPay: function() {
            var $block = $('ol li .op');
            if (!this.confirm.checked) $block.show();
            else {
                if (this.takeInStore.checked||(this.country.value > 0)) $block.hide();
                else $block.show();
            }
        },
        showPayerForm: function() {
            var $form = $('#Address');
            if (this.oneAddr.checked) $form.hide();
            else $form.show();
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
            var $orderPay = $orderButton.find('.js_orderPay');
            var $orderSave = $orderButton.find('.js_orderSave');
            this.$paymentsData.find('input[name=ptype]').each(function(id, el) {
                $(el).on('click', function() {
                    var $this = $(this);
                    $this.closest('label').siblings().removeClass('act');
                    $this.closest('label').addClass('act');
                    switch(this.value) {
                        case '0': case '14':case '13': case '7':
                            $orderPay.hide();
                            $orderSave.show();
                            break;
                        default:
                            $orderSave.hide();
                            $orderPay.show();
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
                        self.takeInStore.checked = true;
                        self.paymentsForm();
                    }
                    else {
                        self.takeInStore.checked = false;
                        self.paymentsForm();
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
                        console.log($block, r.tarif[i]['deliveryTime'], r.tarif[i]['value']);
                        $block.find('.js_xDays').html(r.tarif[i]['deliveryTime']);
                        $block.find('.js_price').html(r.tarif[i]['value']);
                    }
                }
            });
            self.recount(self.getPromocodeValue());
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

        }

    };

}());

$(document).click(function (event) {
    if ($(event.target).closest(".qbtn2,.info_box").length) return;
    $('.info_box').hide();
    event.stopPropagation();
});
