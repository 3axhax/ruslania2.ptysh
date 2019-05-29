<?php /*Created by Кирилл (31.10.2018 22:54)*/ ?>
<?php
$this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
$breadcrumbs = $this->breadcrumbs;
$h1 = array_pop($breadcrumbs);
unset($breadcrumbs) ;
$h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');

KnockoutForm::RegisterScripts();
?>
<style>
	.errorSummary {	width: 400px; float: right; border-collapse: collapse; margin-bottom: 5px; padding: 10px; background-color: rgba(255, 0, 0, 0.1); }
	.errorSummary ul {list-style-type: none;}
	.errorSummary p { font-weight: bold; }
</style>
<div class="container view_product">
	<div class="row">
		<div class="span10">
			<h1 class="titlename poht" style="margin-bottom: 20px;"><?= $h1 ?></h1>

			<?php if (!empty($isWordpanel)): ?><div style="padding-left: 10px" class="text" id="js_wordpanel"><?php endif; ?>
				<?= $certificateText; ?>
			<?php if (!empty($isWordpanel)): ?></div><?php endif; ?>

			<?= CHtml::beginForm(Yii::app()->createUrl('site/certificate'), 'post'); ?>
			<?= CHtml::errorSummary($model); ?>
			<div class="text gift_certificate">
				<div class="sample"><img src="/new_img/gift1.jpg" id="gift_preview"></div>
				<div class="form">
					<div class="form_row">
						<div class="row_name"><?= Yii::app()->ui->item('CERTIFICATE_MAKET') ?></div>
						<div class="row_value"><?= CHtml::activeDropDownList($model, 'maket_id', array(1=>1,2=>2,3=>3), array('style'=>'width: 50px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_DEST_NAME') ?></div>
						<div class="row_value"><?= CHtml::activeTextField($model, 'fio_dest', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_DEST_EMAIL') ?></div>
						<div class="row_value"><?= CHtml::activeTextField($model, 'email_dest', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><?= Yii::app()->ui->item('CERTIFICATE_TXT') ?></div>
						<div class="row_value"><?= CHtml::activeTextArea($model, 'txt_dest', array('style'=>'height: 100px; width: 240px;')); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_SOURCE_NAME') ?></div>
						<div class="row_value"><?= CHtml::activeTextField($model, 'fio_source', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_SOURCE_EMAIL') ?></div>
						<div class="row_value"><?= CHtml::activeTextField($model, 'email_source', array('style'=>'width: 240px;', 'required'=>1)); ?></div>
					</div>
					<div class="form_row">
						<div class="row_name"><span>*</span><?= Yii::app()->ui->item('CERTIFICATE_NOMINAL') ?></div>
						<div class="row_value span11" style="margin: 0; width: inherit;">
							<div><?= CHtml::activeDropDownList($model, 'nominal', $nominals, array('style'=>'width: 70px;', 'class'=>'periodic')); ?>&nbsp;<?= Currency::ToSign(Yii::app()->currency) ?></div>
							<?php /*<div class="mb5 periodic_world" style="white-space:nowrap; ">
								<?php if (!empty($price[DiscountManager::DISCOUNT])) : ?>
									<span class="without_discount"><?= ProductHelper::FormatPrice($price[DiscountManager::BRUTTO_WORLD]); ?></span>
									<span class="price">
								<b class="pwvat"><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT_WORLD]); ?></b>
								<span class="pwovat"><span><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT_WORLD]); ?></span> <?= $ui->item('WITHOUT_VAT'); ?></span>
							</span>
								<?php else : ?>
									<span class="price">
								<span class="pwvat"><?= ProductHelper::FormatPrice($price[DiscountManager::WITH_VAT_WORLD]); ?></span>
								<span class="pwovat"><span><?= ProductHelper::FormatPrice($price[DiscountManager::WITHOUT_VAT_WORLD]); ?></span> <?= $ui->item('WITHOUT_VAT'); ?></span>
							</span>
								<?php endif; ?>
								<input type="hidden" value="1" class="worldmonthpriceoriginal"/>
								<input type="hidden" value="<?= round($price[DiscountManager::WITH_VAT_WORLD] / $price[DiscountManager::BRUTTO_WORLD], 2); ?>" class="worldmonthpricevat"/>
								<input type="hidden" value="<?= round($price[DiscountManager::WITHOUT_VAT_WORLD] / $price[DiscountManager::BRUTTO_WORLD], 2); ?>" class="worldmonthpricevat0"/>
							</div> */ ?>
						</div>
					</div>
				</div>
			</div>
			<?php $this->renderPartial('/payment/_payment_only_online', array('pid'=>Yii::app()->getRequest()->getPost('payment_type_id', 25))) ?>
			<div><?= CHtml::submitButton(Yii::app()->ui->item('CERTIFICATE_BUTTON_PAY'), array('style'=>'min-width: 180px; background-color: #5bb75b; border-radius: 4px; border: 0; padding: 9px 20px; text-align: center; font-size: 14px; color: #fff; font-weight: bold; float:right;', 'onclick'=>'yaCounter53579293.reachGoal(\'add_sert\');')) ?></div>
			<?= CHtml::endForm(); ?>
		</div>
	</div>
	<div style="margin-top:30px;"><?= Yii::app()->ui->item('MSG_FAIL_PAY'); ?></div>
</div>

<script type="text/javascript">
	$(function() {
		$('#Certificate_maket_id').change(function () {
			$('#gift_preview').attr('src', '/new_img/gift' + this.value + '.jpg');
		});
	});

</script>

<?php if(!empty($isWordpanel)): ?>
	<div class="buttonCKEDITOR"><a onclick="runCKEDITOR(); $('.buttonCKEDITOR').toggle(); return false;">Редактировать</a></div>
	<div class="buttonCKEDITOR" style="display: none;"><a onclick="if (confirm('Не сохраненные данные будут потеряны!!!')) { closeCKEDITOR(); $('.buttonCKEDITOR').toggle(); } return false;">Закрыть</a></div>
	<style>
		.cke_button_label.cke_button__inlinesave_label {display: inline;}
		.buttonCKEDITOR {position: fixed; top: 70px; right: 10px; padding: 20px; background-color: #000; opacity: 0.4;}
		.buttonCKEDITOR a { color: #fff; cursor: pointer; font-weight: bold;}
	</style>
	<script src="/js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
		function runCKEDITOR() {
			// contenteditable="true"
			$('#js_wordpanel').attr('contenteditable', 'true');
			var csrf = $('meta[name=csrf]').attr('content').split('=');
			var postData = {page: 'podarochnyj_sertifikat'};
			postData[csrf[0]] = csrf[1];
			if (CKEDITOR.instances['js_wordpanel']) CKEDITOR.instances['js_wordpanel'].destroy(true);//так надо, что бы у редактора селекты работали
			CKEDITOR.inline('js_wordpanel', {
				title: false,
				allowedContent: true,
				extraAllowedContent: 'iframe[*];span[*]',
				filebrowserBrowseUrl: '/js/kcfinder/browse.php?type=files',
				filebrowserImageBrowseUrl: '/js/kcfinder/browse.php?type=images',
				filebrowserFlashBrowseUrl: '/js/kcfinder/browse.php?type=flash',
				filebrowserUploadUrl: '/js/kcfinder/upload.php?type=files',
				filebrowserImageUploadUrl: '/js/kcfinder/upload.php?type=images',
				filebrowserFlashUploadUrl: '/js/kcfinder/upload.php?type=flash',
				extraPlugins: 'oembed,widget,inlinesave,wenzgmap,fontawesome,lineheight',
//                contentsCss: 'path/to/your/font-awesome.css',
				image_previewText: " ",
				toolbar: [
					{ name: 'document', items: [ /*'Save', 'Source', '-', 'inlinesave', 'NewPage', 'Preview', 'Print', '-', */'Templates' ] },
					{ name: 'clipboard', items: [ 'Save','inlinesave', 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
					{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },
					{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
					'/',
					{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
					{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
					{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
					{ name: 'insert', items: [ 'Image','oembed', 'wenzgmap', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe' ] },
					'/',
					{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize', 'FontAwesome', 'lineheight' ] },
					{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
					{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
					{ name: 'about', items: [ 'About' ] }
				],

//            toolbar: [
//                    ['Source','DocProps'],
//                    ['Save','Undo','Redo'],
//                    ['Bold','Italic','Underline'],
//                    ['NumberedList','BulletedList','-','Outdent','Indent'],
//                    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
//                    '/',
//                    ['Style','Format'],
//                    ['Font'],
//                    ['FontSize'],
//                    ['TextColor'],
//                    ['Link','Unlink'],
//                    ['Image','oembed'],
//                    ['Table','HorizontalRule','SpecialChar']
//                ],
				inlinesave: {
					postUrl: '<?= Yii::app()->createUrl('site/staticSave') ?>',
					postData: postData,
					onSave: function(editor) { console.log('clicked save', editor); return true; },
					onSuccess: function(editor, data) { console.log('save successful', editor, data); },
					onFailure: function(editor, status, request) { console.log('save failed', editor, status, request); },
					successMessage: 'Yay we saved it!',
					errorMessage: 'Something went wrong :(',
					useJSON: false,
					useColorIcon: true
				}
//                contentsCss: [ '/new_style/style_site.css' ]
			})/*.on('change', function() {
			 //                console.log(this.getData());
			 })*/.on('instanceReady', function () {
					var CKEIframes = $('.cke_iframe');
					var CKEIframesL = CKEIframes.length;
					for (i = 0; i <CKEIframesL; i ++ ) {
						$(CKEIframes[i]).replaceWith(decodeURIComponent($(CKEIframes[i]).data('cke-realelement')));
					}
				});
			CKEDITOR.dtd.$removeEmpty['span'] = 0;
		}
		function closeCKEDITOR() {
			$('#js_wordpanel').removeAttr('contenteditable');
			if (CKEDITOR.instances['js_wordpanel']) {
				CKEDITOR.instances['js_wordpanel'].destroy(true);
				var CKEIframes = $('.cke_iframe');
				var CKEIframesL = CKEIframes.length;
				for (i = 0; i <CKEIframesL; i ++ ) {
					$(CKEIframes [i]).replaceWith(decodeURIComponent($(CKEIframes[i]).data('cke-realelement')));
				}
				var ckeRemove = $('.cke_reset');
				var ckeRemoveL = ckeRemove.length;
				for (i = 0; i <ckeRemoveL; i ++ ) {
					$(ckeRemove[i]).remove();
				}
			}
		}
		//        runCKEDITOR();
	</script>
<?php endif; ?>