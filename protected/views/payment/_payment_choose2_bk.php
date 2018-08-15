<?php $isPaid = isset($isPaid) && $isPaid; ?>

<?php if ($isPaid) : ?>

    <div class="information info-box">
        <?= $ui->item('ALREADY_PAID'); ?>
    </div>
<?php endif; ?>



<?php $this->renderPartial('/client/_one_order', array('order' => $order, 'onlyContent' => !$isPaid,
    'class' => $isPaid ? '' : 'bordered',
    'enableSlide' => !$isPaid)); ?>

    <script type="text/javascript">
        function slideContents(id) {
            $('#cnt' + id).fadeToggle();
        }
    </script>


<?php if (!$isPaid) : ?>
    <div style="margin-left: 45px;" id="payments-ve">
        <h3><?= $ui->item('PAY_ONLINE'); ?></h3>

        <p><?= $ui->item('PAY_ONLINE_CLICK'); ?></p>

        <div class="payonline">
            <ul>
                <li>
                    <?php $this->widget('PayTrailWidget', array('order' => $order,
                        'env' => Yii::app()->params['PAYMENT_ENVIRONMENT'] == 'test'
                            ? PayTrail::ENV_TEST
                            : PayTrail::ENV_PROD
                    )); ?>
                </li>
                <li>
                    <table width="100%">
                        <tr>
                            <td width="30%" valign="middle">
                                <?php $this->widget('PayPalPayment', array('order' => $order)); ?>
                            </td>
                            <td width="70%" valign="middle">
                                <?= $ui->item('MSG_PAYMENT_TYPE_12'); ?>
                            </td>
                        </tr>
                    </table>
                </li>
            </ul>
        </div>

        <div class="clearBoth"></div>

        <h3><?= $ui->item('PAY_OFFLINE'); ?></h3>

        <div class="payoffline">

            <?php $offlinePayments = Payment::GetOffilePaymentList();
            ?>
            <ul style="list-style-type: none">
                <?php foreach ($offlinePayments as $p) : ?>
                    <li>
                        <div class="wrap">
                            <div class="paymentleft">
                                <label class="ptlab"><input type="radio" data-ptid="<?= $p['ID']; ?>" name="payment"
                                                            value="<?= $p['ID']; ?>"/>
                                    <b><?= $p['Name']; ?></b></label>
                            </div>
                            <div class="paymentright">
                                <?= $p['Desc']; ?>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <input id="SBTN" type="button" class="sort" value="<?= $ui->item('SET_PAYMENT_OPTION'); ?>"/>
        </div>

    </div>

    <div id="thanks" style="display:none" class="info-box information">
        <h3><?= $ui->item('ORDER_MSG_DONE'); ?></h3>

        <p>
            <?= sprintf($ui->item('ORDER_MSG_NEW_NUMBER'), $order['id']); ?>
        </p>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            var csrf = $('meta[name=csrf]').attr('content').split('=');

            $('div.payonline form').submit(function () {
                var $form = $(this);
                var $el = $('input[type="image"]', $form);
                var type = $el.attr('data-ptid');
                var obj = {type: type, id: <?=$order['id']; ?>};
                obj[csrf[0]] = csrf[1];

                $.when(
                    $.ajax('/order/changepaymenttype',
                        {
                            type: 'POST',
                            async: false,
                            data: obj,
                            dataType: 'json'

                        })).always(function () {
                        return true;
                    })
            });

            $('label.ptlab input').on('click', function () {
                var $el = $(this);
                var type = $el.attr('data-ptid');
                var obj = {type: type, id: <?=$order['id']; ?>};
                obj[csrf[0]] = csrf[1];

                $.when(
                    $.ajax('/order/changepaymenttype',
                        {
                            type: 'POST',
                            async: false,
                            data: obj,
                            dataType: 'json'

                        })).always(function () {
//                    $('div.payoffline').slideUp();
                        return true;
                    });
                return true;
            });

            $('#SBTN').click(function () {
                var val = $('input[name="payment"]:checked').val();
                if (val != undefined) {
                    $('#payments-ve').slideUp();
                    $("html, body").animate({scrollTop: 0}, "slow");
                    $('#thanks').fadeIn();
                }
            });
        });
    </script>
<?php endif; ?>