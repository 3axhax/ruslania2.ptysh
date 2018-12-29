(function() {
    filters = function() {
        return new _Filters();
    };

    function _Filters() {}
    _Filters.prototype = {
        urls:{},
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
                    self.liveSearch(f.name, self.urls[f.name]);
                }

                //switch (f.tagName.toLowerCase()) {
                //    case 'input':
                //        switch (f.type) {
                //            case 'text':
                //                break;
                //        }
                //        break;
                //}

                //switch (f.name) {
                //    case 'cost_min': data['min_cost'] = f.value; break;
                //    case 'cost_max': data['max_cost'] = f.value; break;
                //    case 'year_min': data['ymin'] = f.value; break;
                //    case 'year_max': data['ymax'] = f.value; break;
                //    case 'binding[]':
                //        for (var j = 0, optionsLen = f.length; j < optionsLen; j++) {
                //            if (f.options[j].selected) data['binding[' + j + ']'] = f.options[j].value;
                //        }
                //        break;
                //    default:
                //        data[f.name] = f.value;
                //        break;
                //}
            }

            //console.log(this.urls, this.entity, this.cid);
            //this.show_items();
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

        liveSearch: function(field, url) {
            var self = this;
            $.cachedScript("/js/marcopolo.js").done(function() {
                var _field = field; //это поле надо потому, что для серий как-то хитро названия сделаны
                if (field == 'seria') _field = 'series';
                var find = $('.find_' + _field);
                var dataPost = {entity: self.entity, cid: self.cid};
                find.marcoPolo({
                    minChars:3,
                    cache : false,
                    hideOnSelect: true,
                    delay: 50,
                    url: self.urls['author'],
                    data:dataPost,
                    formatMinChars: false,
                    formatItem:function (data, $item, q) {
                        var $li = $('<li class="mp_list_item">' + data.title + '</li>');
                        $li.on('click', function(){select_item_mp(data.id, field, data.title, 'new_' + _field);});
                        return $li.get(0);
                    }
                });
            });
        },

        liveFindAuthorMP: function() {
            var self = this;
            find_author = $('.find_author');
            var dataPost = {entity: this.entity, cid: this.cid};
            find_author.marcoPolo({
                minChars:3,
                cache : false,
                hideOnSelect: true,
                delay: 50,
                url: self.urls['author'],
                data:dataPost,
                formatMinChars: false,
                formatItem:function (data, $item, q) {
                    var $li = $('<li class="mp_list_item">' + data.title + '</li>');
                    $li.on('click', function(){select_item_mp(data.id, 'author', data.title, 'new_author');});
                    return $li.get(0);
                }
            });
        },

//Выбор элемента MP
        select_item_mp: function(id, inp_name, title, show_inp_name) {
            $('input[name=' + inp_name + ']').val(id);
            $('input[name=' + show_inp_name + ']').val(title);
            this.show_result_count();
        },

        liveFindPublisherMP: function(entity, url, cid) {
            find_pub = $('.find_publisher');
            var dataPost = {entity: entity, cid: cid};
            find_pub.marcoPolo({
                minChars:3,
                cache : false,
                hideOnSelect: true,
                delay: 50,
                url: url,
                data:dataPost,
                formatMinChars: false,
                formatItem:function (data, $item, q) {
                    return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'publisher\', \'' + data.title + '\', \'new_publisher\')">' + data.title + '</li>';
                },
            });
        },

        liveFindPerformerMP: function(entity, url, cid) {
            find_pub = $('.find_performer');
            var dataPost = {entity: entity, cid: cid};
            find_pub.marcoPolo({
                minChars:3,
                cache : false,
                hideOnSelect: true,
                delay: 50,
                url: url,
                data:dataPost,
                formatMinChars: false,
                formatItem:function (data, $item, q) {
                    return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'performer\', \'' + data.title + '\', \'new_performer\')">' + data.title + '</li>';
                },
            });
        },

        liveFindSeriesMP: function(entity, url, cid) {
            find_series = $('.find_series');
            var dataPost = {entity: entity, cid: cid};
            find_series.marcoPolo({
                minChars:3,
                cache : false,
                hideOnSelect: true,
                delay: 50,
                url: url,
                data:dataPost,
                formatMinChars: false,
                formatItem:function (data, $item, q) {
                    return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'seria\', \'' + (data.title) + '\', \'new_series\')">' + (data.title) + '</li>';
                },
            });
        },

        liveFindDirectorsMP: function(entity, url, cid) {
            find_series = $('.find_directors');
            var dataPost = {entity: entity, cid: cid};
            find_series.marcoPolo({
                minChars:3,
                cache : false,
                hideOnSelect: true,
                delay: 50,
                url: url,
                data:dataPost,
                formatMinChars: false,
                formatItem:function (data, $item, q) {
                    return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'directors\', \'' + data.title + '\', \'new_directors\')">' + data.title + '</li>';
                },
            });
        },

        liveFindActorsMP: function(entity, url, cid) {
            find_series = $('.find_actors');
            var dataPost = {entity: entity, cid: cid};
            find_series.marcoPolo({
                minChars:3,
                cache : false,
                hideOnSelect: true,
                delay: 50,
                url: url,
                data:dataPost,
                formatMinChars: false,
                formatItem:function (data, $item, q) {
                    return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'actors\', \'' + data.title + '\', \'new_actors\')">' + data.title + '</li>';
                },
            });
        },

        getSeries: function(entity, url, cid, selected_item) {
            $(document).ready(function () {
                if (cid == undefined) cid = 0;
                select_series = $('.select2_series');
                select_series_visible = select_series.next("span").children("span").children("span");
                var frm = 'entity='+entity+'&cid='+cid;
                var csrf = $('meta[name=csrf]').attr('content').split('=');
                frm = frm + '&' + csrf[0] + '=' + csrf[1];
                $.ajax({
                    url: url,
                    type: "POST",
                    data: frm,
                    beforeSend: function(){
                        select_series_visible.addClass('disabled');
                    },
                    success: function (data) {
                        titles = JSON.parse(data);
                        for (id in titles) {
                            for (tittle in titles[id]) {
                                if (selected_item == titles[id][tittle]) {
                                    select_series
                                        .append($("<option></option>")
                                            .attr("value", id)
                                            .attr("selected", true)
                                            .text(titles[id][tittle]));
                                }
                                else {
                                    select_series
                                        .append($("<option></option>")
                                            .attr("value", id)
                                            .text(titles[id][tittle]));
                                }
                            }
                        }
                        select_series_visible.removeClass('disabled');
                        show_result_count();
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            });

        },

        getPublishers: function(entity, url, cid, selected_item) {
        $(document).ready(function () {
            if (cid == undefined) cid = 0;
            select_publishers = $('.select2_publishers');
            select_publishers_visible = select_publishers.next("span").children("span").children("span");
            var frm = 'entity=' + entity + '&cid=' + cid;
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            frm = frm + '&' + csrf[0] + '=' + csrf[1];
            $.ajax({
                url: url,
                type: "POST",
                data: frm,
                beforeSend: function () {
                    select_publishers_visible.addClass('disabled');
                },
                success: function (data) {
                    titles = JSON.parse(data);
                    for (id in titles) {
                        for (tittle in titles[id]) {
                            if (selected_item == titles[id][tittle]) {
                                select_publishers
                                    .append($("<option></option>")
                                        .attr("value", id)
                                        .attr("selected", true)
                                        .text(titles[id][tittle]));
                            }
                            else {
                                select_publishers
                                    .append($("<option></option>")
                                        .attr("value", id)
                                        .text(titles[id][tittle]));
                            }
                        }
                    }
                    select_publishers_visible.removeClass("disabled");
                    show_result_count();
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });
    }
    }
}());