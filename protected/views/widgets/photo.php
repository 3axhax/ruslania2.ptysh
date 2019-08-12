<?php /** Created by Кирилл rkv@dfaktor.ru 06.08.2019 22:41*/ ?>
<!DOCTYPE html><html>
<head>
    <meta name="csrf" content="<?= MyHTML::csrf(); ?>"/>
    <script src="/new_js/jquery.js" type="text/javascript"></script>
    <style>
        #js_photo {
            position: relative;
            width: 200px;
            height: 300px;
        }
        div.photo {
            width: initial;
            height: inherit;
        }
        #photoImg {
            width: 200px;
            height: 300px;
            object-fit: contain;
        }
        #clearImg {
            background-color: red;
            width: 20px;
            height: 20px;
            text-align: center;
            color: #fff;
            cursor: pointer;
            position: absolute;
            top: 0;
            right: -20px;
        }
        #pasteImg {
            background-color: #3d3d3d;
            width: 20px;
            height: 20px;
            text-align: center;
            color: #fff;
            cursor: pointer;
            position: absolute;
            bottom: 0;
            right: -20px;
        }
        #inputImg {
            display: none;
        }
    </style>
</head>
<body>
<div id="js_photo">
    <div class="photo js_dropzone"><img class="photo" id="photoImg" <?= (empty($src)?'':'src="' . $src . '"') ?>/></div>
    <div class="clearPhoto" id="clearImg">X</div>
    <div class="pastePhoto" id="pasteImg">V</div>
    <input type="file" name="inputImg" id="inputImg" />
</div>

<script type="text/javascript">
    (function() {
        img = function() {
            return new _Img();
        };

        function _Img() {}
        _Img.prototype = {
            urlUpload: '', urlClear: '', reloadUrl: '',
            //$img=null, $input=null, $clear=null,
            //eid=0, iid=0,

            init: function(options){
                this.setConst(options);
                this.setEvents();
                this.initDrag();
            },
            setConst: function(options) {
                this.csrf = $('meta[name=csrf]').attr('content').split('=');
                this.urlUpload = options.urlUpload;
                this.urlClear = options.urlClear;
                this.eid = options.eid;
                this.iid = options.iid;
                this.$img = $('#photoImg');
                this.$input = $('#inputImg');
                this.$clear = $('#clearImg');
                if ('reloadUrl' in options) this.reloadUrl = options.reloadUrl;
            },
            setEvents: function() {
                var self = this;

                var dropZones = $('.js_dropzone');
                dropZones[0].ondrop = function(event) {
                    <?php if (isset($_GET['ha'])):?>
                    console.log(console.log(event.dataTransfer.types));
                    <?php else: ?>
                    self.uploadFile(event.dataTransfer.files);
                    <?php endif; ?>
                };


                self.$img.closest('div').on('click', function() { self.$input.click(); });
                self.$input.on('change', function() {
                    var f = self.$input.get(0);
                    self.uploadFile(f.files);
                });

                self.$clear.on('click', function() {
                    var fd = new FormData();
                    fd.append('eid', self.eid);
                    fd.append('iid', self.iid);
                    fd.append(self.csrf[0], self.csrf[1]);
                    $.ajax({
                        url : self.urlClear,
                        type: 'POST',
                        data : fd,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response) {
                                response = JSON.parse(response);
                                if ('errors' in response) alert(response['errors'].join("\n"));
                                else {
                                    self.$input.get(0).value = null;
                                    self.$img.attr('src', '');
                                    if (self.reloadUrl != '') {
                                        var urlRedirect = self.reloadUrl + '&action=remove';
                                        if ('image' in response) urlRedirect = urlRedirect + '&image=' + response['image'];
                                        document.location.href = urlRedirect;
                                    }
                                }
                            }
                            else alert('error');
                        }
                    });
                });

                document.getElementById('pasteImg').addEventListener("paste", function(e) {
                    var items = e.clipboardData.items;
                    var files = [];
                    for( var i = 0, len = items.length; i < len; ++i ) {
                        var item = items[i];
                        if( item.kind === "file" ) {
                            files.push(item.getAsFile());
                        }
                    }
                    self.uploadFile(files);
                });
            },

            uploadFile: function(files) {
                var self = this;
                var fd = new FormData();
                fd.append(self.$input.get(0).name, files[0]);
                fd.append('eid', self.eid);
                fd.append('iid', self.iid);
                fd.append(self.csrf[0], self.csrf[1]);

                $.ajax({
                    url : self.urlUpload,
                    type: 'POST',
                    data : fd,
                    processData: false,
                    contentType: false,
                    beforeSend: function(){
                        self.$img.attr('src', '<?= Picture::srcLoad() ?>');
                    },
                    success: function(response) {
                        if (response) {
                            response = JSON.parse(response);
                            if ('errors' in response) alert(response['errors'].join("\n"));
                            else if ('src' in response) {
                                console.log(response);
                                self.$img.attr('src', response['src']);
                                if ((self.reloadUrl != '')&&('idFoto' in response)) {
                                    var urlRedirect = self.reloadUrl + '&action=new&idFoto=' + response['idFoto'];
                                    if ('image' in response) urlRedirect = urlRedirect + '&image=' + response['image'];
                                    document.location.href = urlRedirect + '&src=' + response['src'];
                                }
                            }
                        }
                        else alert('upload fail');
                    }
                });
//                    readURL(this, self.$img);
            },
            initDrag: function(){
                var $doc = $(document);
                /**css стили при наведение файла в зону*/
                $doc.bind('dragover', function(e) {
                    var dropZones = $('.js_dropzone'), currDropZone = e.target;
                    var $currDropZone = $(currDropZone);

                    if (window.dropZoneTimeout) clearTimeout(window.dropZoneTimeout);

                    do {
                        if ($currDropZone.hasClass('js_dropzone')) break;
                        currDropZone = currDropZone.parentNode;
                    } while (currDropZone != null);

                    dropZones.removeClass('hover');
                    if (currDropZone) {
                        $currDropZone.addClass('hover');
                    }

                    window.dropZoneTimeout = setTimeout(function() {
                        window.dropZoneTimeout = null;
                        dropZones.removeClass('hover');
                    }, 100);
                });

                $doc.bind('drop dragover', function (e) {
                    e.preventDefault();
                });
            }
         }
    }());
    $(document).ready(function() {
        img().init(<?= json_encode($options) ?>);
    });
</script>
</body>
</html>