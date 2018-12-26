(function() {
    filters = function() {
        return new _Filters();
    };

    function _Filters() {}
    _Filters.prototype = {
        urls:{},
        entity:0,
        cid:0,
        init: function(options){
            this.setConst(options);
            this.setEvents();
        },
        setConst: function(options) {
            this.urls = options.urls;
            this.entity = option.entity;
            this.cid = options.cid;
        },
        setEvents: function() {
        }
    }
}());