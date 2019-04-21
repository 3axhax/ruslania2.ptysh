<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<div class="row">
    <div class="container">
<h1><?=$ui->item('A_NEW_PERIODIC_FOR_GIFT'); ?></h1>
<hr>
<?php if (!empty($isWordpanel)): ?><div style="padding-left: 10px" class="text" id="js_wordpanel"><?php endif; ?>
    <?= $giftText; ?>
<?php if (!empty($isWordpanel)): ?></div><?php endif; ?>
        <?/*= $ui->item('A_NEW_PERIODIC_FOR_GIFT_TEXT');*/ ?>
<hr>
<?php $eUrl = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity)));
$hrefOffer = Yii::app()->createUrl('offers/view', array('oid' => $offer['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($offer))));
$cUrl = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cid)); ?>
        <div class="news_box nb<?= $entity ?>">
            <div class="container">
                <div class="title">
                    <?php /*<?= $ui->item("A_NEW_RECOMMENDATIONS_CATEGORY") ?> <a href="<?= $eUrl; ?>" id="enity<?= $entity ?>"><span class="title__bold"><?= Entity::GetTitle($entity); ?></span></a>*/ ?>
                    <a href="<?= $hrefOffer; ?>" id="enity<?= $entity ?>"><span class="title__bold"><?= ProductHelper::GetTitle($offer); ?></span></a>
                    <div class="pult">
                        <a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_left.slick-arrow').click()" class="btn_left"><span class="fa"></span></a>
                        <a href="javascript:;" onclick="$('.nb<?= $entity ?> .btn_right.slick-arrow').click()" class="btn_right"><span class="fa"></span></a>
                    </div>

                </div>
            </div>


            <div class="container cnt<?= $entity ?>">
                <ul class="books">
                    <?php
                    foreach ($group as $item) :
                        ?>
                        <li>
                            <?php $item['status'] = Product::GetStatusProduct($entity, $item['id']);?>
                            <?php
                            $item['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
                            $item['priceData']['unit'] = '';
                            if ($entity == Entity::PERIODIC) {
                                $issues = Periodic::getCountIssues($item['issues_year']);
                                if (!empty($issues['show3Months'])) {
                                    $item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
                                    $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/4;
                                    $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/4;
                                    $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/4;
                                }
                                elseif (!empty($issues['show6Months'])) {
                                    $item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                                    $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/2;
                                    $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/2;
                                    $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/2;
                                }
                                else {
                                    $item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
                                }
                            }
                            ?>
                            <?php $this->renderPartial('/entity/_render_index_item', array('item' => $item, 'entity' => $entity)); ?>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="news_box nb67">
            <div class="container">
                <div class="title">
                    <?= Entity::GetTitle($entity); ?>:
                        <a href="<?= $cUrl; ?>" id="enity<?= $entity ?>">
                            <span class="title__bold">
                                <?= $title_cat; ?>
                            </span>
                        </a>
                    <div class="pult">
                        <a href="javascript:;" onclick="$('.nb67 .btn_left.slick-arrow').click()" class="btn_left"><span class="fa"></span></a>
                        <a href="javascript:;" onclick="$('.nb67 .btn_right.slick-arrow').click()" class="btn_right"><span class="fa"></span></a>
                    </div>

                </div>
            </div>

            <div class="container cnt67">
                <ul class="books">
                    <?php
                    foreach ($popular as $item) :
                        ?>
                        <li>
                            <?php $item['status'] = Product::GetStatusProduct($entity, $item['id']);?>
                            <?php $item['entity'] = $entity;?>
                            <?php
                            $item['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
                            $item['priceData']['unit'] = '';
                            if ($entity == Entity::PERIODIC) {
                                $issues = Periodic::getCountIssues($item['issues_year']);
                                if (!empty($issues['show3Months'])) {
                                    $item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
                                    $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/4;
                                    $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/4;
                                    $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/4;
                                }
                                elseif (!empty($issues['show6Months'])) {
                                    $item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                                    $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/2;
                                    $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/2;
                                    $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/2;
                                }
                                else {
                                    $item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
                                }
                            }
                            ?>
                            <?php $this->renderPartial('/entity/_render_index_item', array('item' => $item, 'entity' => $entity)); ?>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </div>
        </div>


        <?php $cUrl = Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey($entity), 'cid' => $cid119)); ?>
        <div class="news_box nb119">
            <div class="container">
                <div class="title">
                    <?= Entity::GetTitle($entity); ?>:
                    <a href="<?= $cUrl; ?>" id="enity<?= $entity ?>">
                            <span class="title__bold">
                                <?= $titleCat119; ?>
                            </span>
                    </a>
                    <div class="pult">
                        <a href="javascript:;" onclick="$('.nb119 .btn_left.slick-arrow').click()" class="btn_left"><span class="fa"></span></a>
                        <a href="javascript:;" onclick="$('.nb119 .btn_right.slick-arrow').click()" class="btn_right"><span class="fa"></span></a>
                    </div>

                </div>
            </div>

            <div class="container cnt119">
                <ul class="books">
                    <?php
                    foreach ($popular119 as $item) :
                        ?>
                        <li>
                            <?php $item['status'] = Product::GetStatusProduct($entity, $item['id']);?>
                            <?php $item['entity'] = $entity;?>
                            <?php
                            $item['priceData'] = DiscountManager::GetPrice(Yii::app()->user->id, $item);
                            $item['priceData']['unit'] = '';
                            if ($entity == Entity::PERIODIC) {
                                $issues = Periodic::getCountIssues($item['issues_year']);
                                if (!empty($issues['show3Months'])) {
                                    $item['priceData']['unit'] = ' / 3 ' . Yii::app()->ui->item('MONTH_SMALL');
                                    $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/4;
                                    $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/4;
                                    $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/4;
                                }
                                elseif (!empty($issues['show6Months'])) {
                                    $item['priceData']['unit'] = ' / 6 ' . Yii::app()->ui->item('MONTH_SMALL');
                                    $item['priceData'][DiscountManager::BRUTTO] = $item['priceData'][DiscountManager::BRUTTO]/2;
                                    $item['priceData'][DiscountManager::WITH_VAT] = $item['priceData'][DiscountManager::WITH_VAT]/2;
                                    $item['priceData'][DiscountManager::WITHOUT_VAT] = $item['priceData'][DiscountManager::WITHOUT_VAT]/2;
                                }
                                else {
                                    $item['priceData']['unit'] = ' / 12 ' . Yii::app()->ui->item('MONTH_SMALL');
                                }
                            }
                            ?>
                            <?php $this->renderPartial('/entity/_render_index_item', array('item' => $item, 'entity' => $entity)); ?>
                        </li>

                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        scriptLoader('/new_js/slick.js').callFunction(function() {
            $('.cnt<?= $entity ?> ul').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 5,
                slidesToScroll: 5
            }).on('lazyLoadError', function(event, slick, image, imageSource){
                image.attr('src', '<?= Picture::srcNoPhoto() ?>');
            });
            $('.cnt67 ul').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 5,
                slidesToScroll: 5
            }).on('lazyLoadError', function(event, slick, image, imageSource){
                image.attr('src', '<?= Picture::srcNoPhoto() ?>');
            });
            $('.cnt119 ul').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 5,
                slidesToScroll: 5
            }).on('lazyLoadError', function(event, slick, image, imageSource){
                image.attr('src', '<?= Picture::srcNoPhoto() ?>');
            });
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
            var postData = {page: 'podpiska-v-podarok'};
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
