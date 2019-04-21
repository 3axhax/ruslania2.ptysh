(function($){$.jScrollPane={active:[]};$.fn.jScrollPane=function(settings)
{settings=$.extend({},$.fn.jScrollPane.defaults,settings);var rf=function(){return false;};return this.each(function()
{var $this=$(this);var paneEle=this;var currentScrollPosition=0;var paneWidth;var paneHeight;var trackHeight;var trackOffset=settings.topCapHeight;if($(this).parent().is('.jScrollPaneContainer')){currentScrollPosition=settings.maintainPosition?$this.position().top:0;var $c=$(this).parent();paneWidth=$c.innerWidth();paneHeight=$c.outerHeight();$('>.jScrollPaneTrack, >.jScrollArrowUp, >.jScrollArrowDown, >.jScollCap',$c).remove();$this.css({'top':0});}else{$this.data('originalStyleTag',$this.attr('style'));$this.css('overflow','hidden');this.originalPadding=$this.css('paddingTop')+' '+$this.css('paddingRight')+' '+$this.css('paddingBottom')+' '+$this.css('paddingLeft');this.originalSidePaddingTotal=(parseInt($this.css('paddingLeft'))||0)+(parseInt($this.css('paddingRight'))||0);paneWidth=$this.innerWidth();paneHeight=$this.innerHeight();var $container=$('<div></div>').attr({'class':'jScrollPaneContainer'}).css({'height':paneHeight+'px','width':paneWidth+'px'});if(settings.enableKeyboardNavigation){$container.attr('tabindex',settings.tabIndex);}
$this.wrap($container);$(document).bind('emchange',function(e,cur,prev)
{$this.jScrollPane(settings);});}
trackHeight=paneHeight;if(settings.reinitialiseOnImageLoad){var $imagesToLoad=$.data(paneEle,'jScrollPaneImagesToLoad')||$('img',$this);var loadedImages=[];if($imagesToLoad.length){$imagesToLoad.each(function(i,val){$(this).bind('load readystatechange',function(){if($.inArray(i,loadedImages)==-1){loadedImages.push(val);$imagesToLoad=$.grep($imagesToLoad,function(n,i){return n!=val;});$.data(paneEle,'jScrollPaneImagesToLoad',$imagesToLoad);var s2=$.extend(settings,{reinitialiseOnImageLoad:false});$this.jScrollPane(s2);}}).each(function(i,val){if(this.complete||this.complete===undefined){this.src=this.src;}});});};}
var p=this.originalSidePaddingTotal;var realPaneWidth=paneWidth-settings.scrollbarWidth-settings.scrollbarMargin-p;var cssToApply={'height':'auto','width':realPaneWidth+'px'}
if(settings.scrollbarOnLeft){cssToApply.paddingLeft=settings.scrollbarMargin+settings.scrollbarWidth+'px';}else{cssToApply.paddingRight=settings.scrollbarMargin+'px';}
$this.css(cssToApply);var contentHeight=$this.outerHeight();var percentInView=paneHeight/contentHeight;if(percentInView<.99){var $container=$this.parent();$container.append($('<div></div>').addClass('jScrollCap jScrollCapTop').css({height:settings.topCapHeight}),$('<div></div>').attr({'className':'jScrollPaneTrack'}).css({'width':settings.scrollbarWidth+'px'}).append($('<div></div>').attr({'className':'jScrollPaneDrag'}).css({'width':settings.scrollbarWidth+'px'}).append($('<div></div>').attr({'className':'jScrollPaneDragTop'}).css({'width':settings.scrollbarWidth+'px'}),$('<div></div>').attr({'className':'jScrollPaneDragBottom'}).css({'width':settings.scrollbarWidth+'px'}))),$('<div></div>').addClass('jScrollCap jScrollCapBottom').css({height:settings.bottomCapHeight}));var $track=$('>.jScrollPaneTrack',$container);var $drag=$('>.jScrollPaneTrack .jScrollPaneDrag',$container);var currentArrowDirection;var currentArrowTimerArr=[];var currentArrowInc;var whileArrowButtonDown=function()
{if(currentArrowInc>4||currentArrowInc%4==0){positionDrag(dragPosition+currentArrowDirection*mouseWheelMultiplier);}
currentArrowInc++;};if(settings.enableKeyboardNavigation){$container.bind('keydown.jscrollpane',function(e)
{switch(e.keyCode){case 38:currentArrowDirection=-1;currentArrowInc=0;whileArrowButtonDown();currentArrowTimerArr[currentArrowTimerArr.length]=setInterval(whileArrowButtonDown,100);return false;case 40:currentArrowDirection=1;currentArrowInc=0;whileArrowButtonDown();currentArrowTimerArr[currentArrowTimerArr.length]=setInterval(whileArrowButtonDown,100);return false;case 33:case 34:return false;default:}}).bind('keyup.jscrollpane',function(e)
{if(e.keyCode==38||e.keyCode==40){for(var i=0;i<currentArrowTimerArr.length;i++){clearInterval(currentArrowTimerArr[i]);}
return false;}});}
if(settings.showArrows){var currentArrowButton;var currentArrowInterval;var onArrowMouseUp=function(event)
{$('html').unbind('mouseup',onArrowMouseUp);currentArrowButton.removeClass('jScrollActiveArrowButton');clearInterval(currentArrowInterval);};var onArrowMouseDown=function(){$('html').bind('mouseup',onArrowMouseUp);currentArrowButton.addClass('jScrollActiveArrowButton');currentArrowInc=0;whileArrowButtonDown();currentArrowInterval=setInterval(whileArrowButtonDown,100);};$container.append($('<a></a>').attr({'href':'javascript:;','className':'jScrollArrowUp','tabindex':-1}).css({'width':settings.scrollbarWidth+'px','top':settings.topCapHeight+'px'}).html('Scroll up').bind('mousedown',function()
{currentArrowButton=$(this);currentArrowDirection=-1;onArrowMouseDown();this.blur();return false;}).bind('click',rf),$('<a></a>').attr({'href':'javascript:;','className':'jScrollArrowDown','tabindex':-1}).css({'width':settings.scrollbarWidth+'px','bottom':settings.bottomCapHeight+'px'}).html('Scroll down').bind('mousedown',function()
{currentArrowButton=$(this);currentArrowDirection=1;onArrowMouseDown();this.blur();return false;}).bind('click',rf));var $upArrow=$('>.jScrollArrowUp',$container);var $downArrow=$('>.jScrollArrowDown',$container);}
if(settings.arrowSize){trackHeight=paneHeight-settings.arrowSize-settings.arrowSize;trackOffset+=settings.arrowSize;}else if($upArrow){var topArrowHeight=$upArrow.height();settings.arrowSize=topArrowHeight;trackHeight=paneHeight-topArrowHeight-$downArrow.height();trackOffset+=topArrowHeight;}
trackHeight-=settings.topCapHeight+settings.bottomCapHeight;$track.css({'height':trackHeight+'px',top:trackOffset+'px'})
var $pane=$(this).css({'position':'absolute','overflow':'visible'});var currentOffset;var maxY;var mouseWheelMultiplier;var dragPosition=0;var dragMiddle=percentInView*paneHeight/2;var getPos=function(event,c){var p=c=='X'?'Left':'Top';return event['page'+c]||(event['client'+c]+(document.documentElement['scroll'+p]||document.body['scroll'+p]))||0;};var ignoreNativeDrag=function(){return false;};var initDrag=function()
{ceaseAnimation();currentOffset=$drag.offset(false);currentOffset.top-=dragPosition;maxY=trackHeight-$drag[0].offsetHeight;mouseWheelMultiplier=2*settings.wheelSpeed*maxY/contentHeight;};var onStartDrag=function(event)
{initDrag();dragMiddle=getPos(event,'Y')-dragPosition-currentOffset.top;$('html').bind('mouseup',onStopDrag).bind('mousemove',updateScroll);if($.browser.msie){$('html').bind('dragstart',ignoreNativeDrag).bind('selectstart',ignoreNativeDrag);}
return false;};var onStopDrag=function()
{$('html').unbind('mouseup',onStopDrag).unbind('mousemove',updateScroll);dragMiddle=percentInView*paneHeight/2;if($.browser.msie){$('html').unbind('dragstart',ignoreNativeDrag).unbind('selectstart',ignoreNativeDrag);}};var positionDrag=function(destY)
{$container.scrollTop(0);destY=destY<0?0:(destY>maxY?maxY:destY);dragPosition=destY;$drag.css({'top':destY+'px'});var p=destY/maxY;$this.data('jScrollPanePosition',(paneHeight-contentHeight)*-p);$pane.css({'top':((paneHeight-contentHeight)*p)+'px'});$this.trigger('scroll');if(settings.showArrows){$upArrow[destY==0?'addClass':'removeClass']('disabled');$downArrow[destY==maxY?'addClass':'removeClass']('disabled');}};var updateScroll=function(e)
{positionDrag(getPos(e,'Y')-currentOffset.top-dragMiddle);};var dragH=Math.max(Math.min(percentInView*(paneHeight-settings.arrowSize*2),settings.dragMaxHeight),settings.dragMinHeight);$drag.css({'height':dragH+'px'}).bind('mousedown',onStartDrag);var trackScrollInterval;var trackScrollInc;var trackScrollMousePos;var doTrackScroll=function()
{if(trackScrollInc>8||trackScrollInc%4==0){positionDrag((dragPosition-((dragPosition-trackScrollMousePos)/2)));}
trackScrollInc++;};var onStopTrackClick=function()
{clearInterval(trackScrollInterval);$('html').unbind('mouseup',onStopTrackClick).unbind('mousemove',onTrackMouseMove);};var onTrackMouseMove=function(event)
{trackScrollMousePos=getPos(event,'Y')-currentOffset.top-dragMiddle;};var onTrackClick=function(event)
{initDrag();onTrackMouseMove(event);trackScrollInc=0;$('html').bind('mouseup',onStopTrackClick).bind('mousemove',onTrackMouseMove);trackScrollInterval=setInterval(doTrackScroll,100);doTrackScroll();return false;};$track.bind('mousedown',onTrackClick);$container.bind('mousewheel',function(event,delta){delta=delta||(event.wheelDelta?event.wheelDelta/120:(event.detail)?-event.detail/3:0);initDrag();ceaseAnimation();var d=dragPosition;positionDrag(dragPosition-delta*mouseWheelMultiplier);var dragOccured=d!=dragPosition;return false;});var _animateToPosition;var _animateToInterval;function animateToPosition()
{var diff=(_animateToPosition-dragPosition)/settings.animateStep;if(diff>1||diff<-1){positionDrag(dragPosition+diff);}else{positionDrag(_animateToPosition);ceaseAnimation();}}
var ceaseAnimation=function()
{if(_animateToInterval){clearInterval(_animateToInterval);delete _animateToPosition;}};var scrollTo=function(pos,preventAni)
{if(typeof pos=="string"){$e=$(pos,$this);if(!$e.length)return;pos=$e.offset().top-$this.offset().top;}
ceaseAnimation();var maxScroll=contentHeight-paneHeight;pos=pos>maxScroll?maxScroll:pos;$this.data('jScrollPaneMaxScroll',maxScroll);var destDragPosition=pos/maxScroll*maxY;if(preventAni||!settings.animateTo){positionDrag(destDragPosition);}else{$container.scrollTop(0);_animateToPosition=destDragPosition;_animateToInterval=setInterval(animateToPosition,settings.animateInterval);}};$this[0].scrollTo=scrollTo;$this[0].scrollBy=function(delta)
{var currentPos=-parseInt($pane.css('top'))||0;scrollTo(currentPos+delta);};initDrag();scrollTo(-currentScrollPosition,true);$('*',this).bind('focus',function(event)
{var $e=$(this);var eleTop=0;while($e[0]!=$this[0]){eleTop+=$e.position().top;$e=$e.offsetParent();}
var viewportTop=-parseInt($pane.css('top'))||0;var maxVisibleEleTop=viewportTop+paneHeight;var eleInView=eleTop>viewportTop&&eleTop<maxVisibleEleTop;if(!eleInView){var destPos=eleTop-settings.scrollbarMargin;if(eleTop>viewportTop){destPos+=$(this).height()+15+settings.scrollbarMargin-paneHeight;}
scrollTo(destPos);}})
if(location.hash&&location.hash.length>1){setTimeout(function(){scrollTo(location.hash);},$.browser.safari?100:0);}
$(document).bind('click',function(e)
{$target=$(e.target);if($target.is('a')){var h=$target.attr('href');if(h&&h.substr(0,1)=='#'&&h.length>1){setTimeout(function(){scrollTo(h,!settings.animateToInternalLinks);},$.browser.safari?100:0);}}});function onSelectScrollMouseDown(e)
{$(document).bind('mousemove.jScrollPaneDragging',onTextSelectionScrollMouseMove);$(document).bind('mouseup.jScrollPaneDragging',onSelectScrollMouseUp);}
var textDragDistanceAway;var textSelectionInterval;function onTextSelectionInterval()
{direction=textDragDistanceAway<0?-1:1;$this[0].scrollBy(textDragDistanceAway/2);}
function clearTextSelectionInterval()
{if(textSelectionInterval){clearInterval(textSelectionInterval);textSelectionInterval=undefined;}}
function onTextSelectionScrollMouseMove(e)
{var offset=$this.parent().offset().top;var maxOffset=offset+paneHeight;var mouseOffset=getPos(e,'Y');textDragDistanceAway=mouseOffset<offset?mouseOffset-offset:(mouseOffset>maxOffset?mouseOffset-maxOffset:0);if(textDragDistanceAway==0){clearTextSelectionInterval();}else{if(!textSelectionInterval){textSelectionInterval=setInterval(onTextSelectionInterval,100);}}}
function onSelectScrollMouseUp(e)
{$(document).unbind('mousemove.jScrollPaneDragging').unbind('mouseup.jScrollPaneDragging');clearTextSelectionInterval();}
$container.bind('mousedown.jScrollPane',onSelectScrollMouseDown);$.jScrollPane.active.push($this[0]);}else{$this.css({'height':paneHeight+'px','width':paneWidth-this.originalSidePaddingTotal+'px','padding':this.originalPadding});$this[0].scrollTo=$this[0].scrollBy=function(){};$this.parent().unbind('mousewheel').unbind('mousedown.jScrollPane').unbind('keydown.jscrollpane').unbind('keyup.jscrollpane');}})};$.fn.jScrollPaneRemove=function()
{$(this).each(function()
{$this=$(this);var $c=$this.parent();if($c.is('.jScrollPaneContainer')){$this.css({'top':'','height':'','width':'','padding':'','overflow':'','position':''});$this.attr('style',$this.data('originalStyleTag'));$c.after($this).remove();}});}
$.fn.jScrollPane.defaults={scrollbarWidth:10,scrollbarMargin:5,wheelSpeed:18,showArrows:false,arrowSize:0,animateTo:false,dragMinHeight:1,dragMaxHeight:99999,animateInterval:100,animateStep:3,maintainPosition:true,scrollbarOnLeft:false,reinitialiseOnImageLoad:false,tabIndex:0,enableKeyboardNavigation:true,animateToInternalLinks:false,topCapHeight:0,bottomCapHeight:0};$(window).bind('unload',function(){var els=$.jScrollPane.active;for(var i=0;i<els.length;i++){els[i].scrollTo=els[i].scrollBy=null;}});})(jQuery);