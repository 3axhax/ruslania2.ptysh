(function() {
    filters = function() {
        return new _Filters();
    };

    function _Filters() {}
    _Filters.prototype = {
        urls:{},
        filterData:{},
        lang: 'en',
        entity:0,
        cid:0,
        page:0,
        loadMsg:'',
        fields: [],
        init: function(options){
            this.setConst(options);
            this.setEvents();
            return this;
        },
        setConst: function(options) {
            this.urls = options.urls;
            this.filterData = options.filterData;
            this.lang = options.lang;
            this.entity = options.entity;
            this.cid = options.cid;
            this.fields = $('form.filter').find('input[name], select[name]').get();
        },
        setEvents: function() {
            var self = this;
            $('#filter_apply').on('click', function(){ self.show_items(); });

            for (var i = 0, len = self.fields.length; i < len; i++) {
                var f = self.fields[i];
                if ((f.tagName.toLowerCase() == 'input')&&(f.type == 'hidden')&&(f.name in self.urls)) {
                    self.liveSearch(f.name);
                }
                else if ((f.tagName.toLowerCase() == 'input')&&(f.type != 'hidden')) {
                    if (f.name.indexOf('new_') !== 0) {
                        $(f).on('blur', function(){ self.show_result_count(); });
                    }
                }
                else if (f.tagName.toLowerCase() == 'select') {
                    self.similarSelect(f);
                }
            }
        },

        show_items: function() {
            var self = this;
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            var frm = $('form.filter').serialize();
            frm = frm + '&' + csrf[0] + '=' + csrf[1];

            var items_content = $('.span10 .items');

            $.ajax({
                url: self.urls['result'],
                type: "POST",
                data: frm,
                beforeSend: function(){
                    items_content.html(self.loadMsg);
                },
                success: function (r) {
                    items_content.html(r);
                    $('.box_select_result_count').hide(1);
                    lazyImageLoader.refreshImgList();
                    //$(window).scrollTop(0);
                },
                error: function (msg) {
                    console.log (msg);
                }
            });
        },

        show_result_count: function() {
            var self = this;
            var frm = $('form.filter').serialize();
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            frm = frm + '&' + csrf[0] + '=' + csrf[1];
            $.ajax({
                url: self.urls['recount'],
                type: "POST",
                data: frm,
                beforeSend: function(){
                    $('#loader-filter').html('&nbsp;(<img class="loader_gif" src="/new_img/source.gif" width="15" height="15">)');
                },
                success: function (r) {
                    $('#loader-filter').html('&nbsp;(' + r + ')');
                },
                error: function (data) {
                    console.log(data);
                }
            });
        },

        liveSearch: function(field) {
            var self = this;
            scriptLoader('/js/marcopolo.js').callFunction(function(){
                var _field = field; //это поле надо потому, что для серий как-то хитро названия сделаны
                if (field == 'seria') _field = 'series';
                var find = $('.find_' + _field);
                var dataPost = {entity: self.entity, cid: self.cid};
                find.marcoPolo({
                    minChars:3,
                    cache : false,
                    hideOnSelect: true,
                    delay: 50,
                    url: self.urls[field],
                    data:dataPost,
                    formatMinChars: false,
                    formatItem:function (data, $item, q) {
                        var $li = $('<li class="mp_list_item">' + data.title + '</li>');
                        $li.on('click', function(){self.select_item_mp(data.id, field, data.title, 'new_' + _field);});
                        return $li.get(0);
                    }
                });
            });
        },

        similarSelect: function (f) {
            var $f = $(f);
            var self = this;
            if (f.name in self.urls) {
                scriptLoader('/new_js/modules/select2.full.js').callFunction(function(){
                    $f.select2();
                    select_series_visible = $f.next("span").children("span").children("span");
                    var dataPost = {entity: self.entity, cid: self.cid};
                    var csrf = $('meta[name=csrf]').attr('content').split('=');
                    dataPost[csrf[0]] = csrf[1];
                    $.ajax({
                        url: self.urls[f.name],
                        type: "POST",
                        data: dataPost,
                        beforeSend: function(){
                            select_series_visible.addClass('disabled');
                        },
                        success: function (data) {
                            titles = JSON.parse(data);
                            var selectId = 0;
                            var $option;
                            var selectLangTitle;
                            if (f.name in self.filterData) selectId = self.filterData[f.name];
                            for (id in titles) {
                                selectLangTitle = '';
                                if (self.lang in titles[id]) selectLangTitle = self.lang;
                                else if ((selectLangTitle!='en') && ('en' in titles[id])) selectLangTitle = 'en';
                                for (titleLang in titles[id]) {
                                    $option = $('<option value="' + id + '">' + titles[id][titleLang] + '</option>');
                                    if (selectId == id) {
                                        if (selectLangTitle == '') selectLangTitle = titleLang;
                                        if (titleLang == selectLangTitle) $option.prop('selected', true);
                                    }
                                    $f.append($option);
                                }
                            }
                            select_series_visible.removeClass('disabled');
                            $f.on('change', function(){ self.show_result_count(); });
                            //if (selectId > 0) self.show_result_count();
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                });
            }
            else if ($f.hasClass('select2_periodic')) {
                scriptLoader('/new_js/modules/select2.full.js').callFunction(function(){
                    $f.select2({minimumResultsForSearch: Infinity});
                    $f.on('change', function(){ self.show_result_count(); });
                });
            }
            else {
                if ($f.data('multiple') == 'multiple') {
                    scriptLoader('/new_js/multiple-select.js').callFunction(function(){
                        $f.attr('multiple', 'multiple');
                        $f.find('option[value="0"]').remove();
                        $f.multipleSelect({
                            selectAllText: $f.data('alltext'),
                            allSelected: $f.data('alltext'),
                            placeholder: $f.data('placeholder'),
                            multipleWidth: 150,
                            width: '161px'
                        });
                        $f.on('change', function(){ self.show_result_count(); });
                    });
                }
                else $f.on('change', function(){ self.show_result_count(); });
            }
        },

//Выбор элемента MP
        select_item_mp: function(id, inp_name, title, show_inp_name) {
            $('input[name=' + inp_name + ']').val(id);
            $('input[name=' + show_inp_name + ']').val(title);
            this.show_result_count();
        }
    }
}());