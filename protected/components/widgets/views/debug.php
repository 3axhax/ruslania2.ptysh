<?php
/*Created by Кирилл (20.06.2018 22:17)*/
	$colors = array('#d2faf4', '#f0ebb9', '#aad28a', '#f0b9b9', '#2f95eb');
	$colors2 = array('#115b9a', '#9d3a3a', '#437818', '#948917', '#1f7266');
	$trace_num = rand(1, 10000);
?>
<meta content="text/html; charset=utf-8" http-equiv=content-type>
<style type="text/css">
	.portal_print_r{
		margin: 5px;
		position: relative;
	}

	.portal_print_r-close{
		right: 0;
		top: 5px;
		z-index: 1;
		position: absolute;
		cursor: pointer;
	}

	/*-----*/
	.portal_print_r-trace .trace-content-line {
		font-size: 11pt;
		padding: 5px 0;
		border-bottom: solid 1px rgba(100,100,100,0.5);
		position: relative;
	}

	.portal_print_r-trace .trace-toggle{
		height: 20px;
		margin-left: 3px;
	}

	.portal_print_r-trace .trace-toggle-link{
		color:#000000;
		text-decoration:none;
		border-bottom:#000000 dashed 1px;
	}

	.portal_print_r-trace .trace-content{
		padding: 5px;
		display: none;
	}

	.portal_print_r-trace .trace-content-file{
		padding: 0 10px;
	}

	.portal_print_r-trace .trace-function{
		float: left;
		width: 450px;
		padding: 5px;
	}

	.portal_print_r-trace{
		cursor: default;
	}

	.portal_print_r-trace .trace-content-with-args{
		background-color: rgba(0, 250, 0, 0.1);
	}

	.portal_print_r-trace .trace-content-with-args .trace-function{
		cursor: pointer;
	}

	.portal_print_r-trace .trace-args-content {
		margin-top: 8px;
		margin-left: 25px;
		display: none;
	}

	.portal_print_r-trace .trace-arg {
		border-bottom: solid 1px rgba(100,100,100,0.3);
		padding: 5px 0;
	}
	/*-----*/
	/*-----*/

	/*-----*/
	.portal_print_r-content .content-item{
		margin-bottom: 5px;
		position: relative;
		overflow: hidden;
		padding:5px;
		text-align: left;
	}

	.portal_print_r-content .content-item.js_closed{
		border: solid 1px rgba(250, 100, 0, 0.6)!important;
	}

	.portal_print_r-content .content-toggle{
		position: absolute;
		right: 0;
		top: 0;
		width: 40%;
		height: 100%;
		-webkit-transition-duration: 0.2s;
		-o-transition-duration: 0.2s;
		-moz-transition-duration: 0.2s;
		transition-duration: 0.2s;
	}

	.portal_print_r-content .content-toggle:hover{
		background-color: rgba(100, 200, 0, 0.1);
		cursor: pointer;
	}
	/*-----*/
	/*-----*/


	.sqlWord{
		color:#999999;
	}

	.activeLog {
		color: red;
	}

	.sqlSmallWord{
		color: RGBA(0,20,0,0.2);
	}
	.red{
		color: red;
	}
</style>
<div class="portal_print_r">
	<div class="portal_print_r-trace">
		<div class="trace-toggle"><a class="trace-toggle-link" onclick="print_r.toggle('_debug_trace_<?=$trace_num?>');">stack trace</a></div>
		<div class="trace-content" id="_debug_trace_<?=$trace_num?>">
			<?php foreach ($trace as $idTrace => $data): ?>
				<div class="trace-content-line <?=!empty($data['args'])? 'trace-content-with-args' : ''?>" title="<?=!empty($data['file']) ? $data['file'] . ':' . $data['line'] : ''?>">
					<div class="trace-function" <?= !empty($data['args']) ? 'onclick="print_r.toggle(\'_func_args_' . $trace_num . $idTrace.'\');"' : ''?>>
						<?= (!empty($data['class']) ? ($data['class']) : '') . (!empty($data['type']) ? $data['type'] : '') . $data['function'] . '(' . count($data['args']).')'?>
					</div>
					<div class="trace-content-file">
						<?php if (!empty($data['file'])): ?>
							<?=$data['file'] . ':' . $data['line']?>
						<?php endif; ?>
					</div>
				</div>
				<div class="trace-args-content" id="_func_args_<?= $trace_num . $idTrace ?>">
					<?php foreach ($data['args'] as $idArg => $argData): ?>
						<div class="trace-arg">
							<pre><span style='color:<?=$colors2[$idArg%count($colors2)]?>;'><?php (!$argData ? var_dump($argData) : print_r($argData))?></span></pre>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach;?>
		</div>
	</div>
	<div class="portal_print_r-content">
		<?php foreach ($args as $i => $arg):  ?>
			<div class="content-item js_portalPrint_rContentItem" style="background:<?=$colors[$i%count($colors)]?>; border: solid 1px <?=$colors[$i%count($colors)]?>;">
				<pre class="js_print_rText"><?php
					if (!$arg): var_dump($arg); else:
						echo htmlspecialchars(@print_r($arg, true), ENT_COMPAT);
					endif;
					?></pre>
				<div class="content-toggle" onclick="print_r.toggleContent(this)"></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<script type="text/javascript">
	if (typeof $ == 'undefined') document.write('\<script type="text/javascript" src="//code.jquery.com/jquery-1.11.1.min.js"><\/script>');
	print_r = function(){

		var $document = $(document);

		$(document).on('mousedown', '.js_portalPrint_rContentItem', function(e){
			if (e.which == 2){
				log.openSql($(this).find('.js_print_rText').html());
				return false;
			}
		});

		function toggle(id){
			$('#' + id).slideToggle('fast');
		}

		function toggleContent(target){
			var $content = $(target).closest('.js_portalPrint_rContentItem');
			if ($content.height() >= 26){
				if ($content.hasClass('js_closed')) {
					$content.css('height', '');
					$content.removeClass('js_closed');
				}
				else $content.animate({height: 26}, function(){$content.addClass('js_closed')});
			}
			return false;
		}

		return {
			toggle: toggle,
			toggleContent: toggleContent
		}
	}();
	log = function(){
		//распарсивание SQL запроса

		function openSql(sql){
			popupManager.popup.html(prepareSqlString(sql)).show();
		}

		function prepareSqlString(str) {
			var br = ['select', '(?:delete )?from', '(?:on duplicate key )?update(?: ignore)?', 'create view', 'create(?: temporary)? table', 'set',
				'insert(?: low_priority| ignore)? into', 'where', 'order by', 'limit', 'group by', 'optimize table',
				'having', '(?:left |inner |full )?join', 'load data infile',
				'(?:replace )?into table', 'lines terminated by', 'fields terminated by', 'truncate'
			];
			var span = ['like', 'values', 'not exists', 'desc', 'asc'];
			str = str.replace(/(\&gt;|\&lt;)?=|\&gt;|\&lt;/g, '<span class="red">$&</span>');
			str = str.replace(new RegExp("(?:[>'^]|\\b)(" + br.join('|') + ") ", 'ig'), appendBr);
			str = str.replace(new RegExp(" (" + span.join('|') + ") ", 'ig'), inSpan);
			str = str.replace(/,/g, '<span class="red">$&</span> ');
			str = str.replace(/(\;)((\s)*?<br><span class="sqlWord">)/g, '$1<br>$2');
			str = str.replace(new RegExp("\\((<br><span class=\"sqlWord\">select.*?from.*?)\\)", 'ig'), clearSpan);//вложенные селекты
			str = str.replace(new RegExp("<br><span class=\"sqlWord\">(" + br.join('|') + ").*?(' at line| clause)", 'i'), clearSpan);//часть запроса в ошибке
			return str;
		}

		function appendBr(str, q, offset) {
			var result = /\W/.test(str.substr(0, 1)) ? str.substr(0, 1) : '';
			q = '<br>' + inSpan(q.toUpperCase()) + '<br>' + "&nbsp&nbsp";
			return result + q + ' ';
		}

		function inSpan(str) {
			return '<span class="sqlWord">' + str + '</span>';
		}

		function clearSpan(str) {
			return str.replace(/<br>/g, '').replace(/class="sqlWord"/g, 'class="sqlSmallWord"').replace(/&nbsp&nbsp/g, ' ');
		}

		return {
			openSql: openSql
		};
	}();
</script>
