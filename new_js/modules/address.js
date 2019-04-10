(function() {
    address = function() {
        return new _Address();
    };

    function _Address() {}
    _Address.prototype = {
        init: function(options) {
            if ('userData' in options) {
                this.setValues(options['userData']);
            }
            this.fillByUrl(options.formId);
            this.setConst(options);
            this.setEvents();
            return this;
        },

        setValues: function(userData) {
            for (userField in userData) {
                switch (userField) {
                    case 'email': $('.js_contactEmail').val(userData['email']); break;
                    break;
                }
            }
        },

        fillByUrl: function(formId) {
            var urlData = window.location.search.replace('?','').split('&').reduce(
                function(p,e){
                    var a = e.split('=');
                    if (a[1]) p[decodeURIComponent(a[0])] = decodeURIComponent(a[1].replace(/\+/g, ' '));
                    return p;
                },
                {}
            );
            $('#' + formId).find('input[name], textarea[name], select[name]').each(function(i, f) {
                switch (f.type) {
                    case 'radio':
                        if ((f.name in urlData)&&(f.value == urlData[f.name])) f.checked = true;
                        break;
                    case 'checkbox':
                        if (f.name in urlData) f.checked = true;
                        break;
                    case 'hidden': break;
                    default:
                        if (f.name in urlData) {
                            switch (f.tagName) {
                                case 'SELECT':
                                    $(f).find('option[value=' + urlData[f.name] + ']').attr("selected", "selected");
                                    break;
                                case 'TEXTAREA':
                                    f.innerHTML = urlData[f.name];
                                    break;
                                default:
                                    f.value = urlData[f.name];
                                    break;
                            }
                        }
                        break;
                }
            });
        },

        setConst: function(options) {
            this.formId = options.formId;

            this.country = document.getElementById(this.formId + '_country'); //страна доставки
            this.csrf = $('meta[name=csrf]').attr('content').split('=');

            this.urlChangeCountry = options.urlChangeCountry;
            this.urlGetCountry = options.urlGetCountry;
            this.urlLoadStates = options.urlLoadStates;
            this.urlRedirect = options.urlRedirect;

            if (this.delivery_address) {
                if (this.delivery_address.value == '0') {
                }
                else this.changeCountry();
            }
        },
        setEvents: function() {
            var self = this;
            this.showAddressFields();
            this.eventUserType();

            $(this.country).on('change', function() {
                self.changeCountry();
                self.fillPhoneCode(this);
            });
            $('#send-forma').on('click', function(){ self.sendforma(); });
            this.changeLangOrCurrency();
        },

        changeLangOrCurrency: function() {
            var self = this;
            var getFormData = function() {
                var fd = {};
                $('#' + self.formId).find('input[name], textarea[name], select[name]').each(function(i, f) {
                    switch (f.type) {
                        case 'radio':case 'checkbox':
                        if (f.checked) fd[f.name] = f.value;
                        break;
                        case 'hidden': break;
                        default: fd[f.name] = f.value; break;
                    }
                });
                return fd;
            };
            $('.dd_select_valut').find('div.label_valut a').each(function(i, el){
                $(el).on('click', function() {
                    var valute = 0;
                    this.search.replace('?','').split('&').reduce(
                        function(p,e){
                            var a = e.split('=');
                            if (a[0] == 'currency') valute = a[1];
                        },
                        {}
                    );
                    var fd = getFormData();
                    fd['currency'] = valute;
                    this.setAttribute('href', this.pathname + '?' + $.param(fd));
                });
            });
            $('.dd_select_lang').find('span.lang a').each(function(i, el){
                $(el).on('click', function() {
                    var fd = getFormData();
                    this.setAttribute('href', this.pathname + '?' + $.param(fd));
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

        showAddressFields: function() {
            var $form = $('#' + this.formId);
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
                if ((this.formId == 'Reg')&&this.takeInStore.checked) takeInStore = 1;
            }

            var self = this;
            $form.find('tr').each(function (id, elem) {
                var $elem = $(elem);
                switch (userType) {
                    case '1':
                        //проверка для verkkolasku
                        if ($elem.hasClass('js_firm')) {
                            if ((self.formId == 'Address')&&$elem.hasClass('verkkolasku')) {
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

        eventUserType: function() {
            var self = this;
            var $form = $('#' + this.formId);
            $form.find('input[type=radio].js_userType').each(function(i, el) {
                $(el).on('click', function(){
                    self.showAddressFields(self.formId);
                });
            });
        },
        changeCountry: function () {
            this.showStates(this.country);
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
            var form = document.getElementById(this.formId);
            $(form).find('input[name], textarea[name], select[name]').each(function(i, f) {
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
            fd[this.csrf[0]] = this.csrf[1];
            if (errors.length) {
                self.viewErrors(errors);
            }
            else {
                $.ajax({
                    url : form.getAttribute('action') + '?alias=' + form.getAttribute('id'),
                    data: fd,
                    type: 'post',
                    dataType : 'json',
                    success: function(r) {
                        var errors = [];
                        if ('errors' in r) {
                            for (field in r.errors) {
                                var t = document.getElementById(field);
                                if (t) errors.push(t);
                            }
                        }
                        if (errors.length) {
                            self.viewErrors(errors);
                        }
                        else document.location.href = self.urlRedirect;
                    }
                });
            }
        },

        viewErrors: function(errors) {
            var len = errors.length;
            var firstErrorPos = 0;
            var errorVisible = false;
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
                    if ($f.is(':visible')) errorVisible = true;
                    $f.addClass('error')
                        .siblings('.texterror').show();
                }
            }
            if (!errorVisible) {
                console.log(errors);
                alert('Something went wrong');
            }
            this.scrollTo(firstErrorPos);
        },

        scrollTo: function(top) {
            jQuery("html:not(:animated),body:not(:animated)").animate({scrollTop: top}, 120);
        }
    };

}());