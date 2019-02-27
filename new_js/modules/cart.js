(function() {
    cart = function() {
        return new _Cart();
    };

    function _Cart() {}
    _Cart.prototype = {
        addrFormIds:[],
        init: function(options) {
            this.setConst(options);
            this.setEvents();
            return this;
        },
        setConst: function(options) {
            this.addrFormIds = options.addrFormIds;
        },
        setEvents: function() {
            for (var i = 0, len = this.addrFormIds.length; i < len; i++) {
                this.showAddressFields(this.addrFormIds[i]);
            }
        },

        showAddressFields: function(idForm) {
            var $form = $('#' + idForm);
            var userType = $form.find('input[type=radio].checkbox_custom:checked').val();
            if (!userType) userType = 0;
            var takeInStore = $form.find('input[type=checkbox].checkbox_custom:checked').val();
            if (!takeInStore) takeInStore = 0;
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
        }
    };


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

}());


//$(document).ready(function() {
//    promocodeHandler = promocodes().init();
//});
