(function() {
	var autocompletes = {};

	autocomplete = function(idText) {
		if (autocompletes[idText]) return autocompletes[idText];
		return new _Autocomplete();
	};

	var mainProto = {
		init: function(settings) {
			this.setSettings(settings);
			this.startAutocomplete();
			autocompletes[this.settings.idText] = this;
			return this;
		},
		setSettings: function(settings){
			this.settings = {
				url: '',
				idText: '',
				idInput: null,
				data: {},
				autocompleteParams: {
					minLength: 2,
					position: { my: 'left top', at: 'left bottom+2', collision: 'none' },
					source: this.source.bind(this),
					focus: this.onFocus.bind(this),
					select: this.onSelect.bind(this),
					close: this.onClose.bind(this),
					open: this.onOpen.bind(this),
					change: this.onChange.bind(this),
					search: this.onSearch.bind(this)
				}
			};
			this.mergeObjectsRecursive(this.settings, settings);
			this.cache = {};
			this.$input = $('#' + this.settings.idText);
		},
		startAutocomplete: function(){
			this.$input.autocomplete(this.settings.autocompleteParams);
		},
		source: function(request, response){
			if (this.cache[request.term]) return response(this.cache[request.term]);

			var self = this;
			$.ajax({
				url: this.settings.url,
				dataType: 'json',
				data: self.mergeObjects(request, this.settings.data),
				success: function(data, status, xhr) {
					self.cache[request.term] = data;
					response(data);
				},
				beforeSend: function(){}
			});
		},
        onOpen: function(event, ui) {
            this.$input.autocomplete('widget').outerWidth(this.$input.outerWidth());
        },
        onFocus: function(event, ui) {},
        onSelect: function(event, ui) {},
		onClose: function(event, ui){},
		onChange: function(event, ui){},
		onSearch: function(event, ui){},
		hideWindow: function(){
			this.$input.autocomplete('widget').hide();
		},
		mergeObjectsRecursive: function (obj1, obj2){
			for (var key in obj2)
				if (obj2.hasOwnProperty(key))
					if (typeof obj1[key] == 'object' && typeof obj2[key] == 'object')
						obj1[key] = this.mergeObjectsRecursive(obj1[key], obj2[key]);
					else
						obj1[key] = obj2[key];

			return obj1;
		},
		mergeObjects: function (obj1, obj2){
			for (var key in obj2)
				if (obj2.hasOwnProperty(key)) obj1[key] = obj2[key];
			return obj1;
		}



};

	function _Autocomplete(){};
	_Autocomplete.prototype = mainProto;
}());

(function( $ ) {

var proto = $.ui.autocomplete.prototype,
	initSource = proto._initSource;

function filter( array, term ) {
	var matcher = new RegExp( $.ui.autocomplete.escapeRegex(term), "i" );
	return $.grep( array, function(value) {
		return matcher.test( $( "<div>" ).html( value.label || value.value || value ).text() );
	});
}

$.extend( proto, {
	_initSource: function() {
		if ( $.isArray(this.options.source) ) {
			this.source = function( request, response ) {
				response( filter( this.options.source, request.term ) );
			};
		} else {
			initSource.call( this );
		}
	},

	_renderItem: function( ul, item) {
		var $li = $( "<li></li>" );
		if (item.isCheck) $li.addClass('ls-check');
		$li.data( "item.autocomplete", item );
		$li.append(item.label);
		$li.appendTo( ul );
		return $li;

		//return $( "<li></li>" )
		//	.data( "item.autocomplete", item )
		//	.append(item.label)
		//	.appendTo( ul );
	}
});

})( jQuery );