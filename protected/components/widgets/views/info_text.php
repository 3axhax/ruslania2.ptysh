<?php /*Created by Кирилл (27.09.2018 21:48)*/ ?>
<span style="display: none;" class="text" id="js_container-alert_text"><?= $text ?></span>
<script>
	$(document).ready(function () {
		var refElem = document.getElementById('js_container-alert_close');
		if (refElem) {
			var elem = document.getElementById('js_container-alert_text');
			refElem.parentNode.insertBefore(elem, refElem);
			elem.style.display = 'inline';
		}
	});
</script>

