<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs));
    $breadcrumbs = $this->breadcrumbs;
    $h1 = array_pop($breadcrumbs);
    unset($breadcrumbs) ;
    $h1 = mb_strtoupper(mb_substr($h1, 0, 1, 'utf-8')) . mb_substr($h1, 1, null, 'utf-8');
?>
 
 <div class="container">
     <?php if (!empty($isWordpanel)): ?><div style="padding-left: 10px" class="text" id="js_wordpanel"><?php endif; ?>
         <?= $registerText; ?>
     <?php if (!empty($isWordpanel)): ?></div><?php endif; ?>

     <?php //echo $ui->item('REG_INFO_MESSAGE'); ?>
    <h1 class="h1_reg"><?= $h1 ?></h1>
    <?php $this->renderPartial('register_form', array('model' => $model)); ?>
</div>

<?php if(!empty($isWordpanel)): ?>
    <div class="buttonCKEDITOR"><a onclick="runCKEDITOR(); $('.buttonCKEDITOR').toggle(); return false;">Редактировать</a></div>
    <div class="buttonCKEDITOR" style="display: none;"><a onclick="if (confirm('Не сохраненные данные будут потеряны!!!')) { closeCKEDITOR(); $('.buttonCKEDITOR').toggle(); } return false;">Закрыть</a></div>
    <style>
        .cke_button_label.cke_button__inlinesave_label {display: inline;}
        .buttonCKEDITOR {position: fixed; top: 30px; right: 10px; padding: 20px; background-color: #000; opacity: 0.4;}
        .buttonCKEDITOR a { color: #fff; cursor: pointer; font-weight: bold;}
    </style>
    <script src="/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        function runCKEDITOR() {
            // contenteditable="true"
            $('#js_wordpanel').attr('contenteditable', 'true');
            var csrf = $('meta[name=csrf]').attr('content').split('=');
            var postData = {page: 'register'};
            postData[csrf[0]] = csrf[1];
            if (CKEDITOR.instances['js_wordpanel']) CKEDITOR.instances['js_wordpanel'].destroy(true);//так надо, что бы у редактора селекты работали
            CKEDITOR.inline('js_wordpanel', {
                title: false,
                allowedContent: true,
                extraAllowedContent: 'iframe[*]',
                filebrowserBrowseUrl: '/js/kcfinder/browse.php?type=files',
                filebrowserImageBrowseUrl: '/js/kcfinder/browse.php?type=images',
                filebrowserFlashBrowseUrl: '/js/kcfinder/browse.php?type=flash',
                filebrowserUploadUrl: '/js/kcfinder/upload.php?type=files',
                filebrowserImageUploadUrl: '/js/kcfinder/upload.php?type=images',
                filebrowserFlashUploadUrl: '/js/kcfinder/upload.php?type=flash',
                extraPlugins: 'oembed,widget,inlinesave,wenzgmap',
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
                    { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
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
                    for (i = 0; i <CKEIframesL; i ++ )
                        $(CKEIframes[i]).replaceWith(decodeURIComponent($(CKEIframes[i]).data('cke-realelement')));
                });
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
            }
        }
        //        runCKEDITOR();
    </script>
<?php endif; ?>
