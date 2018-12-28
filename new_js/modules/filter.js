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
        },
        setConst: function(options) {
            this.urls = options.urls;
            this.entity = options.entity;
            this.cid = options.cid;
            this.fields = $('form.filter').find('input[name], select[name]').get();
        },
        setEvents: function() {
            console.log(this.urls, this.entity, this.cid);
            this.show_items();
        },



        show_items: function() {
            var self = this;
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            var data = {
                entity_val: self.entity,
                cid_val: self.cid,
                page: self.page
            };
            data[csrf[0]] = csrf[1];
            for (var i = 0, len = self.fields.length; i < len; i++) {
                var f = self.fields[i];
                switch (f.name) {
                    case 'cost_min': data['min_cost'] = f.value; break;
                    case 'cost_max': data['max_cost'] = f.value; break;
                    case 'year_min': data['ymin'] = f.value; break;
                    case 'year_max': data['ymax'] = f.value; break;
                    case 'binding[]':
                        for (var j = 0, optionsLen = f.length; j < optionsLen; j++) {
                            if (f.options[j].selected) data['binding[' + j + ']'] = f.options[j].value;
                        }
                        break;
                    default:
                        data[f.name] = f.value;
                        break;
                }
            }

            items_content = $('.span10 .items');

            $.ajax({
                url: self.urls['result'],
                type: "POST",
                data: data,
                beforeSend: function(){
                    //items_content.html(self.loadMsg);
                },
                success: function (r) {
                    //items_content.html(r);
                    //$('.box_select_result_count').hide(1);
                    //$(window).scrollTop(0);
                },
                error: function (msg) {
                    console.log (msg);
                }
            });
        },

        liveFindAuthorMP: function(entity, url, cid) {
            find_author = $('.find_author');
            var dataPost = {entity: entity, cid: cid};
            find_author.marcoPolo({
                minChars:3,
                cache : false,
                hideOnSelect: true,
                delay: 50,
                url: url,
                data:dataPost,
                formatMinChars: false,
                formatItem:function (data, $item, q) {
                    return '<li class="mp_list_item" onclick="select_item_mp(' + data.id + ', \'author\', \'' + data.title + '\', \'new_author\')">' + data.title + '</li>';
                },
            });
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