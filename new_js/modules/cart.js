(function() {
    cart = function() {
        return new _Cart();
    };

    function _Cart() {}
    _Cart.prototype = {
        addrFormIds:['Reg', 'Address'],
        onlyPereodic: 0,
        init: function(options) {
            this.takeInStore = document.getElementById('check_addressa'); //забрать в магазине
            this.oneAddr = document.getElementById('addr_buyer'); //адрес плательщика и получателя совпадают
            this.confirm = document.getElementById('confirm'); //согласен с условиями
            this.country = document.getElementById('Reg_country'); //страна доставки

            this.setConst(options);
            this.setEvents();
            return this;
        },
        setConst: function(options) {
            this.onlyPereodic = options.onlyPereodic;
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
            });

            $(this.oneAddr).on('click', function() {
                self.showPayerForm();
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
                    if (takeInStore) {
                        $elem.hide();
                    }
                    else {
                        //проверка для штаты
                        if ($elem.hasClass('states_list')) {
                            if ($elem.find('option').length > 1) $elem.show();
                        }
                        else $elem.show();
                    }
                }
            });
        },

        deleveryForm: function() {
            var $deliveryTypeData = $('#deliveryTypeData');
            if (this.onlyPereodic||this.takeInStore.checked) $deliveryTypeData.hide();
            else $deliveryTypeData.show();
        },
        paymentsForm: function() {
            var $paymentsData = $('#paymentsData');
            var self = this;
            $paymentsData.find('input[name=ptype]').each(function(id, el) {
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
                $(el).on('click', function(){ self.showAddressFields(idForm) });
            });
        },
        eventPayments: function () {
            var $paymentsData = $('#paymentsData');
            var self = this;
            $paymentsData.find('label').each(function(id, el) {
                $(el).on('click', function() {
                    var $this = $(this);
                    $this.siblings().removeClass('act');
                    $this.addClass('act');
                });
            });
        },
        eventDeliverys: function() {
            var self = this;
            var $deliveryTypeData = $('#deliveryTypeData');
            $deliveryTypeData.find('label').each(function(id, el) {
                $(el).on('click', function() {
                    console.log(1);
                    var $this = $(this);
                    $this.siblings().removeClass('act');
                    $this.closest('.variant').siblings().each(function(i, variant) {
                        $(variant).find('label').removeClass('act');
                    });
                    $this.addClass('act');
                    if ($this.find('input[name=dtype]').val() == '0') console.log(this);
                });
            });
            $deliveryTypeData.find('.qbtn2').each(function(id, el) {
                $(el).on('click', function() {
                    $deliveryTypeData.find('.info_box').hide();
                    $(this).siblings('.info_box').toggle();
                });
            });
        }
    };


/*
    var promocodes = function() {
        return new _Promocodes();
    };

    function _Promocodes() {}

    _Promocodes.prototype = {
        active: false,
        urlCheck: '',

        init: function() {
            this.setConst();
            this.setEvents();
            return this;
        },
        setConst: function() {
            var $promocodeBlock = $('#js_promocode');
            this.$input = $promocodeBlock.find('input[type=text]');
            this.$use = $promocodeBlock.find('input[type=checkbox]');
            this.$submit = $promocodeBlock.find('input[type=button]');
        },
        setEvents: function() {
            var self = this;
            self.$use.on('change', function(){
                if (this.checked) {
                    if (self.$input.val() != '') self.recount(self.$input.val().trim());
                    self.$input.closest('div').show();
                }
                else {
                    self.$input.closest('div').hide();
                    self.recount('');
                }

            });
            self.$submit.on('click', function() { self.recount(self.$input.val().trim()); });
        },
        getValue: function() {
            if (this.active) return this.$input.val();
            return '';
        },
        recount: function(value) {
            var self = this;
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            var $form = $('form.address.text');
//			var dtid = $form.find('input[name=dtid]:checked').val();
            var dtype = $form.find('input[name=dtype]:checked').val();
            var aid = 0;
            var $address = $form.find('select[name=id_address]');
            if ($address.length > 0) aid = $address.val();
            var cid = 0;
            var $country = $form.find('#Address_country');
            if ($country.length > 0) cid = $country.val();
            $.ajax({
                url: self.urlCheck,//Yii::app()->createUrl('cart/checkPromocode')
                data: 'promocode=' + encodeURIComponent(value) +
                    '&dtype=' + dtype +
                    '&aid=' + aid +
                    '&cid=' + cid +
                    '&' + csrf[0] + '=' + csrf[1],
                type: 'post',
                dataType : 'json',
                success: function (r) {
                    //items_cost - цена товаров
                    //js_item_{eid}_{id} - строка с товаром
                    //item_cost - цена товара
                    //delivery_cost - цена доставки
                    //itogo_cost - общая стоимость
                    $('.itogo_cost').html(r.totalPrice + ' ' + r.currency);
                    self.$input.closest('div').siblings().remove();
                    if (value != '') {
                        var $buf = self.$input.closest('div');
                        var $elem = $('<div style="font-weight: normal;"></div>');
                        if ('promocodeValue' in r.briefly) {
                            $elem.append('<span style="margin-right: 20px;">' + r.briefly['promocodeValue'] + ' ' + r.briefly['promocodeUnit'] + '</span>');
                            self.active = true;
                        }
                        else if ('message' in r.briefly) {
                            $elem.append('<span style="margin-right: 20px;">' + r.briefly['message'] + '</span>');
                            self.active = false;
                        }
                        $('<span style="color:#ed1d24; cursor: pointer;">&#10008;</span>').appendTo($elem).click(function(){ self.recount(''); });
                        if ('name' in r.briefly) $elem.append(r.briefly['name']);
                        $buf.after($elem);
                    }
                    else {
                        self.active = false;
                        self.$input.val('');
                    }
                }
            });

        }

    }
*/

}());

$(document).click(function (event) {
    if ($(event.target).closest(".qbtn2,.info_box").length) return;
    $('.info_box').hide();
    event.stopPropagation();
});

//$(document).ready(function() {
//    promocodeHandler = promocodes().init();
//});
