<div class="container">
            <?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
            
            <div style="padding-left: 10px" class="text" id="js_wordpanel">
                <?= $data; ?>
            </div>
            <!-- /content -->
</div>
<?php if(!empty($isWordpanel)):
    /*
	config.allowedContent = true;
	config.filebrowserBrowseUrl = 'js/kcfinder/browse.php?type=files';
	config.filebrowserImageBrowseUrl = 'js/kcfinder/browse.php?type=images';
	config.filebrowserFlashBrowseUrl = 'js/kcfinder/browse.php?type=flash';
	config.filebrowserUploadUrl = 'js/kcfinder/upload.php?type=files';
	config.filebrowserImageUploadUrl = 'js/kcfinder/upload.php?type=images';
	config.filebrowserFlashUploadUrl = 'js/kcfinder/upload.php?type=flash';
	config.extraPlugins = 'oembed,widget';
	config.image_previewText = " ";
	config.toolbar = 'Basic';
	config.toolbar_Basic =
	[
	];
    */
    ?>
    <script src="/js/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        function runCKEDITOR() {
            if (CKEDITOR.instances['js_wordpanel']) CKEDITOR.instances['js_wordpanel'].destroy(true);//так надо, что бы у редактора селекты работали
            CKEDITOR.inline('js_wordpanel', {
                title: false,
                allowedContent: true,
                filebrowserBrowseUrl: 'js/kcfinder/browse.php?type=files',
                filebrowserImageBrowseUrl: 'js/kcfinder/browse.php?type=images',
                filebrowserFlashBrowseUrl: 'js/kcfinder/browse.php?type=flash',
                filebrowserUploadUrl: 'js/kcfinder/upload.php?type=files',
                filebrowserImageUploadUrl: 'js/kcfinder/upload.php?type=images',
                filebrowserFlashUploadUrl: 'js/kcfinder/upload.php?type=flash',
                extraPlugins: 'oembed,widget',
                image_previewText: " ",
                toolbar: [
                    ['Source','DocProps'],
                    ['Undo','Redo'],
                    ['Bold','Italic','Underline'],
                    ['NumberedList','BulletedList','-','Outdent','Indent'],
                    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                    '/',
                    ['Style','Format'],
                    ['Font'],
                    ['FontSize'],
                    ['TextColor'],
                    ['Link','Unlink'],
                    ['Image','oembed'],
                    ['Table','HorizontalRule','SpecialChar']
                ],
//                contentsCss: [ '/new_style/style_site.css' ]
            }).on('change', function() {
                console.log(this.getData());
            });
        }
        runCKEDITOR();
    </script>
<?php endif; ?>
