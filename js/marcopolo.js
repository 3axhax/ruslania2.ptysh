(function(b,j)
{if(b.cleanData)
{var k=b.cleanData;b.cleanData=function(a)
{for(var c=0,d;(d=a[c])!=null;c++)b(d).triggerHandler("remove");k(a)}}else
{var l=b.fn.remove;b.fn.remove=function(a,c)
{return this.each(function()
{if(!c)if(!a||b.filter(a,[this]).length)b("*",this).add([this]).each(function()
{b(this).triggerHandler("remove")});return l.call(b(this),a,c)})}}
b.widget=function(a,c,d)
{var e=a.split(".")[0],f;a=a.split(".")[1];f=e+"-"+a;if(!d)
{d=c;c=b.Widget}
b.expr[":"][f]=function(h)
{return!!b.data(h,a)};b[e]=b[e]||{};b[e][a]=function(h,g)
{arguments.length&&this._createWidget(h,g)};c=new c;c.options=b.extend(true,{},c.options);b[e][a].prototype=b.extend(true,c,{namespace:e,widgetName:a,widgetEventPrefix:b[e][a].prototype.widgetEventPrefix||a,widgetBaseClass:f},d);b.widget.bridge(a,b[e][a])};b.widget.bridge=function(a,c)
{b.fn[a]=function(d)
{var e=typeof d==="string",f=Array.prototype.slice.call(arguments,1),h=this;d=!e&&f.length?b.extend.apply(null,[true,d].concat(f)):d;if(e&&d.charAt(0)==="_")return h;e?this.each(function()
{var g=b.data(this,a),i=g&&b.isFunction(g[d])?g[d].apply(g,f):g;if(i!==g&&i!==j)
{h=i;return false}}):this.each(function()
{var g=b.data(this,a);g?g.option(d||{})._init():b.data(this,a,new c(d,this))});return h}};b.Widget=function(a,c)
{arguments.length&&this._createWidget(a,c)};b.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",options:{disabled:false},_createWidget:function(a,c)
{b.data(c,this.widgetName,this);this.element=b(c);this.options=b.extend(true,{},this.options,this._getCreateOptions(),a);var d=this;this.element.bind("remove."+this.widgetName,function()
{d.destroy()});this._create();this._trigger("create");this._init()},_getCreateOptions:function()
{return b.metadata&&b.metadata.get(this.element[0])[this.widgetName]},_create:function()
{},_init:function()
{},destroy:function()
{this.element.unbind("."+this.widgetName).removeData(this.widgetName);this.widget().unbind("."+this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass+"-disabled ui-state-disabled")},widget:function()
{return this.element},option:function(a,c)
{var d=a;if(arguments.length===0)return b.extend({},this.options);if(typeof a==="string")
{if(c===j)return this.options[a];d={};d[a]=c}
this._setOptions(d);return this},_setOptions:function(a)
{var c=this;b.each(a,function(d,e)
{c._setOption(d,e)});return this},_setOption:function(a,c)
{this.options[a]=c;if(a==="disabled")this.widget()[c?"addClass":"removeClass"](this.widgetBaseClass+"-disabled ui-state-disabled").attr("aria-disabled",c);return this},enable:function()
{return this._setOption("disabled",false)},disable:function()
{return this._setOption("disabled",true)},_trigger:function(a,c,d)
{var e=this.options[a];c=b.Event(c);c.type=(a===this.widgetEventPrefix?a:this.widgetEventPrefix+a).toLowerCase();d=d||{};if(c.originalEvent)
{a=b.event.props.length;for(var f;a;)
{f=b.event.props[--a];c[f]=c.originalEvent[f]}}
this.element.trigger(c,d);return!(b.isFunction(e)&&e.call(this.element[0],c,d)===false||c.isDefaultPrevented())}}})(jQuery);(function(factory)
{if(typeof define==='function'&&define.amd)
{define(['jquery'],factory);}
else
{factory(jQuery);}}(function($,undefined)
{'use strict';var cache={};$.widget('mp.marcoPolo',{options:{cache:true,compare:false,data:{},dynamicData:{},delay:600,formatData:null,formatError:function($item,jqXHR,textStatus,errorThrown)
{return'<em>Your search could not be completed at this time.</em>';},formatItem:function(data,$item)
{return data.title||data.name;},formatMinChars:function(minChars,$item)
{return'<em>Your search must be at least <strong>'+minChars+'</strong> characters.</em>';},formatNoResults:function(q,$item)
{return'<em>No results for <strong>'+q+'</strong>.</em>';},hideOnSelect:false,label:null,minChars:1,onBlur:null,onChange:null,onError:null,onFocus:null,onMinChars:null,onNoResults:null,onRequestBefore:null,onRequestAfter:null,onResults:null,onSelect:function(data,$item)
{},param:'q',required:false,selectable:'*',selected:null,url:null},keys:{DOWN:40,ENTER:13,ESC:27,UP:38},_create:function()
{var self=this;self.$input=self.element.addClass('mp_input');self.$list=$('<ol class="mp_list" />').hide().insertAfter(self.$input);self.autocomplete=self.$input.attr('autocomplete');self.$input.attr('autocomplete','off');self.ajax=null;self.ajaxAborted=false;self.documentMouseup=null;self.focusPseudo=false;self.focusReal=false;self.mousedown=false;self.selected=null;self.selectedMouseup=false;self.timer=null;self.value=self.$input.val();self._bindInput()._bindList()._bindDocument();self._initOptions()._initSelected();},_setOption:function(option,value)
{$.Widget.prototype._setOption.apply(this,arguments);this._initOptions(option,value);},_initOptions:function(option,value)
{var self=this,allOptions=option===undefined,options={};if(allOptions)
{options=self.options;}
else
{options[option]=value;}
$.each(options,function(option,value)
{switch(option)
{case'label':self.options.label=$(value).addClass('mp_label');self._toggleLabel();break;case'selected':if(allOptions&&value)
{self.select(value,null);}
break;case'url':if(!value)
{self.options.url=self.$input.closest('form').attr('action');}
break;}});return self;},change:function(q)
{var self=this;if(q!==self.value)
{self.$input.val(q);self._change(q);if(self.focusPseudo)
{self._cancelPendingRequest()._hideAndEmptyList();}
else
{self._toggleLabel();}}},search:function(q)
{var $input=this.$input;if(q!==undefined)
{$input.val(q);}
this._request($input.val());},select:function(data,$item)
{var self=this,$input=self.$input,hideOnSelect=self.options.hideOnSelect;self.selected=data;if(hideOnSelect)
{self._hideList();}
if(data)
{self._trigger('select',[data,$item]);}
else
{$input.val('');}
if($input.val()!==self.value)
{self.value=$input.val();if(!self.focusPseudo)
{self._toggleLabel();}
self._hideAndEmptyList();}},_initSelected:function()
{var self=this,$input=self.$input,data=$input.data('selected'),value=$input.val();if(data)
{self.select(data,null);}
else if(value)
{self.select(value,null);}
return self;},destroy:function()
{var self=this,options=self.options;self.$list.remove();if(self.autocomplete!=='off')
{self.$input.removeAttr('autocomplete');}
self.$input.removeClass('mp_input');if(options.label)
{options.label.removeClass('mp_label');}
$(document).unbind('mouseup.marcoPolo',self.documentMouseup);$.Widget.prototype.destroy.apply(self,arguments);},list:function()
{return this.$list;},_bindInput:function()
{var self=this,$input=self.$input,$list=self.$list;$input.bind('focus.marcoPolo',function()
{if(self.focusReal)
{return;}
self.focusPseudo=true;self.focusReal=true;self._toggleLabel();if(self.selectedMouseup)
{self.selectedMouseup=false;}
else
{self._trigger('focus');self._request($input.val());}}).bind('keydown.marcoPolo',function(key)
{var $highlighted=$();switch(key.which)
{case self.keys.UP:key.preventDefault();self._showList()._highlightPrev();break;case self.keys.DOWN:key.preventDefault();self._showList()._highlightNext();break;case self.keys.ENTER:key.preventDefault();if(!$list.is(':visible'))
{return;}
$highlighted=self._highlighted();if($highlighted.length)
{self.select($highlighted.data('marcoPolo'),$highlighted);}
break;case self.keys.ESC:self._cancelPendingRequest()._hideList();break;}}).bind('keyup.marcoPolo',function(key)
{if($input.val()!==self.value)
{self._request($input.val());}}).bind('blur.marcoPolo',function()
{self.focusReal=false;setTimeout(function()
{if(!self.mousedown)
{self._dismiss();}},1);});return self;},_bindList:function()
{var self=this;self.$list.bind('mousedown.marcoPolo',function()
{self.mousedown=true;}).delegate('li.mp_selectable','mouseover',function()
{self._addHighlight($(this));}).delegate('li.mp_selectable','mouseout',function()
{self._removeHighlight($(this));}).delegate('li.mp_selectable','mouseup',function()
{var $item=$(this);self.select($item.data('marcoPolo'),$item);self.selectedMouseup=true;self.$input.focus();});return self;},_bindDocument:function()
{var self=this;$(document).bind('mouseup.marcoPolo',self.documentMouseup=function()
{self.mousedown=false;if(!self.focusReal&&self.$list.is(':visible'))
{self._dismiss();}});return self;},_toggleLabel:function()
{var self=this,$label=self.options.label;if($label.length)
{if(self.focusPseudo||self.$input.val())
{$label.hide();}
else
{$label.show();}}
return self;},_firstSelectableItem:function()
{return this.$list.children('li.mp_selectable:visible:first');},_lastSelectableItem:function()
{return this.$list.children('li.mp_selectable:visible:last');},_highlighted:function()
{return this.$list.children('li.mp_highlighted');},_removeHighlight:function($item)
{$item.removeClass('mp_highlighted');return this;},_addHighlight:function($item)
{this._removeHighlight(this._highlighted());$item.addClass('mp_highlighted');return this;},_highlightFirst:function()
{this._addHighlight(this._firstSelectableItem());return this;},_highlightPrev:function()
{var $highlighted=this._highlighted(),$prev=$highlighted.prevAll('li.mp_selectable:visible:first');if(!$prev.length)
{$prev=this._lastSelectableItem();}
this._addHighlight($prev);return this;},_highlightNext:function()
{var $highlighted=this._highlighted(),$next=$highlighted.nextAll('li.mp_selectable:visible:first');if(!$next.length)
{$next=this._firstSelectableItem();}
this._addHighlight($next);return this;},_showList:function()
{var $list=this.$list;if($list.children().length)
{$list.css({left:this.$input.position().left});$list.show();}
return this;},_hideList:function()
{this.$list.hide();return this;},_hideAndEmptyList:function()
{this.$list.hide().empty();return this;},_buildNoResultsList:function(q)
{var self=this,$input=self.$input,$list=self.$list,options=self.options,$item=$('<li class="mp_no_results" />'),formatNoResults;formatNoResults=options.formatNoResults&&options.formatNoResults.call($input,q,$item);if(formatNoResults)
{$item.html(formatNoResults);}
self._trigger('noResults',[q,$item]);if(formatNoResults)
{$item.appendTo($list);self._showList();}
else
{self._hideList();}
return self;},_buildResultsList:function(q,data)
{var self=this,$input=self.$input,$list=self.$list,options=self.options,selected=self.selected,compare=options.compare&&selected,compareCurrent,compareSelected,compareMatch=false,datum,$item=$(),formatItem;for(var i=0,length=data.length;i<length;i++)
{datum=data[i];$item=$('<li class="mp_item" />');formatItem=options.formatItem.call($input,datum,$item,q);$item.data('marcoPolo',datum);$item.html(formatItem).appendTo($list);if(compare)
{if(options.compare===true)
{compareCurrent=datum;compareSelected=selected;}
else
{compareCurrent=datum[options.compare];compareSelected=selected[options.compare];}
if(compareCurrent===compareSelected)
{self._addHighlight($item);compare=false;compareMatch=true;}}}
$list.children(options.selectable).addClass('mp_selectable');self._trigger('results',[data]);self._showList();if(!compareMatch)
{}
return self;},_buildSuccessList:function(q,data)
{var self=this,$input=self.$input,$list=self.$list,options=self.options;$list.empty();if(options.formatData)
{data=options.formatData.call($input,data);}
if($.isEmptyObject(data))
{self._buildNoResultsList(q);}
else
{self._buildResultsList(q,data);}
return self;},_buildErrorList:function(jqXHR,textStatus,errorThrown)
{var self=this,$input=self.$input,$list=self.$list,options=self.options,$item=$('<li class="mp_error" />'),formatError;$list.empty();formatError=options.formatError&&options.formatError.call($input,$item,jqXHR,textStatus,errorThrown);if(formatError)
{$item.html(formatError);}
self._trigger('error',[$item,jqXHR,textStatus,errorThrown]);if(formatError)
{$item.appendTo($list);self._showList();}
else
{self._hideList();}
return self;},_buildMinCharsList:function(q)
{var self=this,$input=self.$input,$list=self.$list,options=self.options,$item=$('<li class="mp_min_chars" />'),formatMinChars;if(!q.length)
{self._hideAndEmptyList();return self;}
$list.empty();formatMinChars=options.formatMinChars&&options.formatMinChars.call($input,options.minChars,$item);if(formatMinChars)
{$item.html(formatMinChars);}
self._trigger('minChars',[options.minChars,$item]);if(formatMinChars)
{$item.appendTo($list);self._showList();}
else
{self._hideList();}
return self;},_cancelPendingRequest:function()
{var self=this;if(self.ajax)
{self.ajaxAborted=true;self.ajax.abort();}
else
{self.ajaxAborted=false;}
clearTimeout(self.timer);return self;},_change:function(q)
{var self=this;self.selected=null;self.value=q;self._trigger('change',[q]);return self;},_request:function(q)
{var self=this,$input=self.$input,$list=self.$list,options=self.options;self._cancelPendingRequest();if(q!==self.value)
{self._change(q);}
self.timer=setTimeout(function()
{var param={},params={},cacheKey,$inputParent=$();if(q.length<options.minChars)
{self._buildMinCharsList(q);return self;}
param[options.param]=q;params=$.extend({},options.data,param);$.each(self.options.dynamicData,function(idx,val)
{if(val!=null&&val!=undefined)
{var v=($.isFunction(val))?val():val;if(v!=undefined&&v!=null)params[idx]=v;}});cacheKey=options.url+(options.url.indexOf('?')===-1?'?':'&')+$.param(params);if(options.cache&&cache[cacheKey])
{self._buildSuccessList(q,cache[cacheKey]);}
else
{self._trigger('requestBefore');$inputParent=$input.parent().addClass('mp_busy');self.ajax=$.ajax({url:options.url,dataType:'json',data:params,success:function(data)
{self._buildSuccessList(q,data);if(options.cache)
{cache[cacheKey]=data;}},error:function(jqXHR,textStatus,errorThrown)
{if(!self.ajaxAborted)
{self._buildErrorList(jqXHR,textStatus,errorThrown);}},complete:function(jqXHR,textStatus)
{self.ajax=null;self.ajaxAborted=false;$inputParent.removeClass('mp_busy');self._trigger('requestAfter',[jqXHR,textStatus]);}});}},options.delay);return self;},_dismiss:function()
{var self=this,$input=self.$input,$list=self.$list,options=self.options;self.focusPseudo=false;self._cancelPendingRequest()._hideAndEmptyList();if(options.required&&!self.selected)
{$input.val('');self._change('');}
self._toggleLabel()._trigger('blur');return self;},_trigger:function(name,args)
{var self=this,callbackName='on'+name.charAt(0).toUpperCase()+name.slice(1),triggerName=self.widgetEventPrefix.toLowerCase()+name.toLowerCase(),triggerArgs=$.isArray(args)?args:[],callback=self.options[callbackName];self.element.trigger(triggerName,triggerArgs);return callback&&callback.apply(self.element,triggerArgs);}});}));