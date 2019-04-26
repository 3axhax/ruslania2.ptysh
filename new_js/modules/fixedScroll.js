(function(){
	fixedScroll = function(){
		return new _fs();
	};

	var _fs = function(){
		this.onScroll = function(){
			var scroll = this.getScrollTop();

			var heightElem = 0;

			if (this.settings.$blocks) {
				heightElem = 0;
				this.settings.$blocks.each(function(id, block) {
					heightElem += $(block).outerHeight(true);
				})
			}
			else {
				heightElem = $(this.settings.block).outerHeight(true);
			}
			if (this.blockHeight != heightElem) this.blockHeight = heightElem;

			for (var k in this.heights) {
				heightElem = this.heights[k]['elem'].outerHeight(true);
				if (heightElem != this.heights[k]['heigth']) {
					this.offsetTopDisplay = this.offsetTopDisplay + heightElem - this.heights[k]['heigth'];
					if (!this.settings.stopBlock) this.offsetTopStop = this.offsetTopStop + heightElem - this.heights[k]['heigth'];
					this.heights[k]['heigth'] = heightElem;
				}
			}
			if ((scroll + this.blockHeight + this.offsetTop) >= this.offsetTopStop) {
				if ((this.getOffsetTop() + this.blockHeight) > (this.offsetTopStop - 20)) this.positionTop();
				else this.positionBottom();
			} else if (scroll >= (this.getOffsetTop() - this.offsetTop)){
				if ((this.getOffsetTop() + this.blockHeight) > (this.offsetTopStop - 20)) this.positionTop();
				else this.positionCenter();
			} else {
				this.positionTop();
			}
		}.bind(this);
	};

	_fs.prototype = {
		offsetTopDisplay: 0,
		offsetLeft:0,
		heights:[],
		positionTop: function(){
			var position = '';
			if (this.settings.$otherBlocks) {
				position = this.settings.contentBlock.style.position;
				if (position == 'fixed' || position == 'absolute') {
					this.settings.$otherBlocks.show();
					this.settings.$blocks.first().removeClass('sp-first');
					this.settings.$blocks.last().removeClass('sp-last');
					this.settings.contentBlock.style.position = '';
					this.settings.contentBlock.style.top = '';
					this.settings.contentBlock.style.width = '';
					this.settings.contentBlock.style.left = '';
				}
			}
			else if (this.settings.block) {
				position = this.settings.block.style.position;
				if (position == 'fixed' || position == 'absolute'){
					if (this.settings.hideIsFixed) {
						if (this.settings.$blocks) {
							this.settings.$blocks.each(function(id, block) {
								$(block).hide().css({
									position: '',
									top: '',
									width: ''
								});
							})
						}
						else {
							$(this.settings.block).hide().css({
								position: '',
								top: '',
								width: ''
							});
						}
					}
					else {
						if (this.settings.$blocks) {
							this.settings.$blocks.each(function(id, block) {
								block.style.position = '';
								block.style.top = '';
								block.style.width = '';
							})
						}
						else {
							this.settings.block.style.position = '';
							this.settings.block.style.top = '';
							this.settings.block.style.width = '';
						}
					}
				}
			}
			else {
			}
		},
		positionCenter: function(){
			var position = '';
			var self = this;
			var offsetTop = self.offsetTop;
			if (this.settings.$otherBlocks) {
				position = this.settings.contentBlock.style.position;
				if (parseInt(self.settings.block.style.top, 10) != offsetTop) {
					this.settings.$otherBlocks.hide();
					this.settings.$blocks.first().addClass('sp-first');
					this.settings.$blocks.last().addClass('sp-last');
					if (position == 'fixed' || position == 'absolute') {
						$(self.settings.contentBlock).css({
							position: 'fixed',
							left: self.offsetLeft,
							top: offsetTop
						});
					}
					else {
						$(self.settings.contentBlock).css({
							position: 'fixed',
							top: offsetTop,
							left: self.offsetLeft,
							width: self.settings.width
						});
					}
				}
			}
			else if (this.settings.block) {
				position = self.settings.block.style.position;
				if (parseInt(self.settings.block.style.top, 10) != self.offsetTop){
					if (position == 'absolute' || position == 'fixed'){
						if (self.settings.$blocks) {
							self.settings.$blocks.each(function(id, block) {
								$(block).css({
									position: 'fixed',
									top: offsetTop
								});
								offsetTop += $(block).outerHeight(true);
							})
						}
						else {
							$(self.settings.block).css({
								position: 'fixed',
								top: offsetTop
							});
						}
					} else {
						if (self.settings.$blocks) {
							self.settings.$blocks.each(function(id, block) {
								$(block).css({
									position: 'fixed',
									top: offsetTop,
									width: self.settings.width
								});
								offsetTop += $(block).outerHeight(true);
							})
						}
						else {
							$(self.settings.block).css({
								position: 'fixed',
								top: offsetTop,
								width: self.settings.width
							});
						}
					}
				}
			}
			else {
			}
		},
		positionBottom: function(){
			var self = this;
			var offsetTop = self.offsetTopStop - self.blockHeight;
			if (this.settings.$otherBlocks) {
				if (parseInt(self.settings.block.style.top, 10) != offsetTop) {
					this.settings.$otherBlocks.hide();
					this.settings.$blocks.first().addClass('sp-first');
					this.settings.$blocks.last().addClass('sp-last');
					$(self.settings.contentBlock).css({
						position: 'absolute',
						top: offsetTop,
						left: self.offsetLeft,
						width: self.settings.width
					});
				}
			}
			else if (self.settings.block) {
				if (parseInt(self.settings.block.style.top, 10) != offsetTop){
					if (self.settings.$blocks) {
						self.settings.$blocks.each(function(id, block) {
							$(block).css({
								position: 'absolute',
								top: offsetTop,
								width: self.settings.width
							});
							offsetTop += $(block).outerHeight(true);
						})
					}
					else {
						$(self.settings.block).css({
							position: 'absolute',
							top: offsetTop,
							width: self.settings.width
						});
					}
				}
			}
			else {
			}
		},
		init: function(settings){
			this.settings = {
				contentBlock: null,
				stopBlock: null,
				stopIndent:0,
				block: null,
				$blocks: null,
				minOffset: 5,
				displayOffset: 0, //смещаем высоту начала показа прилепленного блока
				animation: 350, //плавность изменения
				width: 250, //ширина блока в прилепленном состоянии
				hideIsFixed: false,
				$otherBlocks: null //что бы скрывать блоки, если не пусто, то эти блоки будут none, а залипать будет contentBlock
			};

			for (var key in settings)
				if (settings.hasOwnProperty(key)) this.settings[key] = settings[key];

			this.settings.width = $(this.settings.block).outerWidth();
			this.setConsts();
			this.setOffsetTop(0);
			if (this.getOffsetTop() + this.blockHeight + this.offsetTop < this.offsetTopStop){
				this.setEvents().onScroll();
			}
		},

		setConsts: function(){
			this.offsetLeft = this.elementOffsetLeft(this.settings.contentBlock);
			var $contentBlock = $(this.settings.contentBlock);
			var self = this;

			if (this.settings.stopBlock) {
				this.offsetTopStop = this.elementOffsetTop(this.settings.stopBlock) + this.settings.stopIndent;
			}
			else {
				this.offsetTopStop = this.elementOffsetTop(this.settings.contentBlock) + $contentBlock.height();
				this.offsetTopStop += parseInt($contentBlock.css('border-top') || 0, 10) + parseInt($contentBlock.css('margin-top') || 0, 10) + parseInt($contentBlock.css('padding-top') || 0, 10);
			}
			if (this.settings.$blocks) {
				this.blockHeight = 0;
				this.settings.$blocks.each(function(id, block) {
					self.blockHeight += $(block).outerHeight(true);
				})
			}
			else {
				this.blockHeight = $(this.settings.block).outerHeight(true);
			}
			if (this.settings.displayOffset != 0) {
				this.offsetTopStop -= this.settings.displayOffset;
			}
			return this;
		},

		getOffsetTop: function() {
			if (this.offsetTopDisplay > 0) return this.offsetTopDisplay;
			var self = this;
			this.offsetTopDisplay = Array.prototype.reduce.call($(this.settings.contentBlock).children(), function(previousValue, currentValue) {
				if (currentValue.nodeName == 'SCRIPT') return previousValue;

				var $curValue = $(currentValue);
				var curHeight = $curValue.outerHeight(true);
				self.heights.push({elem: $curValue, heigth: curHeight});

				return curHeight + previousValue;
			}, 0);

			this.offsetTopDisplay += this.elementOffsetTop(this.settings.contentBlock);
			if (this.settings.displayOffset != 0) {
				this.offsetTopDisplay += this.settings.displayOffset;
			}
			return this.offsetTopDisplay;
		},

		setOffsetTop: function(height){
			this.offsetTop = height + this.settings.minOffset;
			return this;
		},
		onRelativeResize: function(){
			this.setOffsetTop(0);
			this.onScroll();
		},
		setEvents: function(){
			$(window).on('scroll', this.onScroll);
			mediator.subscribe('onRelativeTopHide', this.onRelativeResize, this)
					.subscribe('onRelativeTopShow', this.onRelativeResize, this);
			return this;
		},
		remove: function(){
			$(window).off('scroll', this.onScroll);
			mediator.unsubscribe('onRelativeTopHide', this.onRelativeResize)
					.unsubscribe('onRelativeTopShow', this.onRelativeResize);
		},

		/**@returns {Number|number} позиция Y верхнего левого угла относительно верхнеей угла страницы*/
		elementOffsetTop: function (elem) {
			if (elem.getBoundingClientRect) {
				return Math.round(elem.getBoundingClientRect().top + this.getScrollTop() - (document.documentElement.clientTop || document.body.clientTop || 0));
			} else {
				var top = 0;
				while (elem) {
					top += parseFloat(elem.offsetTop);
					elem = elem.offsetParent;
				}
				return top;
			}
		},

		/**@returns {Number|number} позиция X верхнего левого угла относительно левой стороны страницы*/
		elementOffsetLeft: function (elem) {
			if (elem.getBoundingClientRect) {
				return Math.round(elem.getBoundingClientRect().left + this.getScrollLeft() - (document.documentElement.clientLeft || document.body.clientLeft || 0))
			} else {
				var left = 0;
				while (elem) {
					left += parseFloat(elem.offsetLeft);
					elem = elem.offsetParent;
				}
				return left;
			}
		},

		getScrollTop: function () {
			return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;
		},

		getScrollLeft: function () {
			return window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft;
		}

};
}());

mediator = function(){
	var channels = {};

	function subscribe(name, cb, _context, _single){
		if (!channels[name]) channels[name] = [];
		channels[name].push({cb: cb, single: _single || false, context: _context || null});
		return mediator;
	}

	function unsubscribe(name, cb){
		if (channels[name]){
			channels[name] = channels[name].filter(function(obj){
				return !(obj.cb == cb);
			});
			if (!channels[name].length) delete channels[name];
		}
		return mediator;
	}

	function publish(name){
		if (channels[name]){
			var args = Array.prototype.slice.call(arguments, 1);
			channels[name] = channels[name].filter(function(obj){
				obj.cb.apply(obj.context, args);
				return !obj.single;
			});
			if (!channels[name].length) delete channels[name];
		}
		return mediator;
	}

	return {
		subscribe: subscribe,
		unsubscribe: unsubscribe,
		publish: publish
	};
}();
