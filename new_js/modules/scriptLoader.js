(function() {
	var loadedScripts = {}, scripts = false;

	/**просто перечисляем наименования скриптов*/
	/**@returns {callFunction : callFunction, load : load}*/
	scriptLoader = function(__names__) {
		if (arguments.length == 1) return loadOneScript(Array.prototype.pop.call(arguments));
		return new loadMultyScript(Array.prototype.slice.call(arguments));
	};

	function loadOneScript(name){
		if (!loadedScripts[name]) {
			var src = ((name.substr(0, 7) == 'http://')||(name.substr(0, 8) == 'https://')) ? name : ('/new_js/' + name);

			var loaded = scriptLoaded(src);
			loadedScripts[name] = new oneScript({'src': src, '_loadStarted': loaded, '_loaded': loaded});
		}
		return loadedScripts[name];
	}

	function scriptLoaded(src) {
		var len;
		if (!scripts) {
			var headNodes = document.getElementsByTagName('head')[0].childNodes, loaded = false;
			scripts = [];
			for (len = headNodes.length; len--;) { //Ищем все скрипты
				if ((headNodes[len].nodeType == 1) && (headNodes[len].tagName == 'SCRIPT')) {
					if ((headNodes[len].src == src) && (!loaded)) loaded = true; //параллельно ищем src
					scripts.push(headNodes[len]);
				}
			}
			return loaded;
		}
		for (len = scripts.length; len--;)
			if (scripts[len].src == src) return true;
		return false;
	}

	function loadMultyScript(names){
		this.names = names;
		this.countLoaded = 0;
	}

	loadMultyScript.prototype = {
		load: function(){
			this.names.forEach(function(name){loadOneScript(name).load()});
		},
		callFunction: function(cb){
			var self = this;
			this.cb = cb;
			this.names.forEach(function(name){
				loadOneScript(name).callFunction(function(){
					self.endLoad();
				});
			});
		},
		endLoad: function(){
			this.countLoaded++;
			if (this.countLoaded == this.names.length) this.cb();
		}
	};

	function oneScript(params) {
		this._loaded = false;
		this._loadStarted = false;
		this.src = '';
		this._callbackStack = [];
		var i;
		for (i in params)
			if (params[i]) this[i] = params[i];
		return this;
	}

	oneScript.prototype = {
		callFunction: function(cbFunction) {
			if (this._loaded) $(document).ready.call(this, cbFunction);//Если вызвали функцию до загрузки скрипта
			else {
				if (this._loadStarted) this._pushStack(cbFunction); //В стаке функции, которые должны быть вызваны после загрузки
				else {
					this._pushStack(cbFunction);
					this.load();
				}
			}
			return this;
		},

		_callStack: function() {
			var item;
			while (item = this._callbackStack.shift()) item();
		},

		_pushStack: function(cbFunction) {
			this._callbackStack.push(cbFunction);
		},

		load: function() {
			if (!this._loadStarted) {
				var head = document.getElementsByTagName('head'), script = document.createElement('SCRIPT'), self = this;
				script.type = 'text/javascript';
				script.async = true;
				script.src = this.src;
				head[0].appendChild(script);
				this._loadStarted = true;
				script.onreadystatechange = function() {  //ie
					if ((this.readyState == 'complete') || (this.readyState == 'loaded')) {
						self._loaded = true;
						self._callStack();
						return true;
					}
				};
				script.onload = function() {
					self._loaded = true;
					self._callStack();
					return true;
				};
			}
			return this;
		}
	};
}());