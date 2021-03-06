<hr/><?php $refresh = isset($refresh) && $refresh;
$key = isset($uiKey) ? $uiKey : 'MSG_USER_LOGIN';

?>

<div class="container">

	<h1 style="margin-bottom: 50px; text-align: center;"><?= $ui->item('CARTNEW_TITLE_PAGE_VARIANT') ?></h1>

	<div class="span6" style="margin-left: 50px;">
		<!--<h1 class="h1_reg" style="margin-top: 0px; margin-bottom: 25px;">Вход</h1>-->

		<?php
		$form = $this->beginWidget('KnockoutForm', array(
			'model' => new User,
			'action' => Yii::app()->createUrl('site/login'),
			'class' => 'registr',
			'id' => 'user-login',
			'viewModel' => 'loginVM',
			'afterAjaxSubmit' => 'doLogin',
			'beforeAjaxSubmit' => 'beforeAjax',
		));
		?>
		<h2 class="h1_reg" style="margin-top: 0px; margin-bottom: 25px;"><?= $ui->item('USER_LOGIN_ALT') ?></h2>
		<?php
		$cls = 'login2';
		if (isset($class))
			$cls = $class;
		?>

		<ul data-bind="foreach: errorStr" style='text-align: left'>
			<li><span data-bind="text: $data"></span></li>
		</ul>

		<?= $form->textField('login', array('placeholder' => $ui->item('regform_email'))); ?>
		<?= $form->passwordField('pwd', array('placeholder' => $ui->item('regform_password'))); ?>
		<a href="<?= Yii::app()->createUrl('site/register'); ?>" title="<?= $ui->item('A_REGISTER') ?>" class="reg_btn <?= Yii::app()->getLanguage() ?>"><?= $ui->item('A_REGISTER') ?></a>
		<a href="<?= Yii::app()->createUrl('site/forgot'); ?>" title="<?= $ui->item('A_REMIND_PASS'); ?>"><?= $ui->item('A_REMIND_PASS'); ?></a>

		<div
			style="margin-top: 10px;"><?= $form->submitButton($ui->item('A_SIGNIN'), array('class' => 'sort')); ?></div>

		<script>
			

			function beforeAjax(vm) {
				$('#user-login input').change();
				return true;
			}

			function doLogin(json) {
				<?php if ($refresh) : ?>
				window.location.reload();
				<?php else : ?>
				window.location.href = '<?=Yii::app()->createUrl('cart')?>doorder/';
				<?php endif; ?>
			}
		</script>


		<?php $this->endWidget(); ?>
		<div class="divider"></div>


	</div>

	<div class="span6">
		<div style="width: auto; margin: 0 auto; padding: 30px 32px; background-color: #f8f8f8;">
		<h2 class="h1_reg"
		    style="margin-top: 0px; margin-bottom: 25px;"><?= $ui->item('CARTNEW_LABEL_NO_ACCOUNT') ?></h2>
		<a href="<?= Yii::app()->createUrl('cart/noregister') ?>" class="order_start"
		   style="width: 248px; margin: 0 auto; display: block"><?= $ui->item('CARTNEW_LABEL_CONTINUE_WITHOUT_AUTHORIZATION') ?></a>

		</div>

		<div class="span6" style="margin-left: 0;">
			<div style="height: 20px;"></div>
			<h2 class="h1_reg"
			            style="margin-top: 0px; margin-bottom: 2px; font-size: 16px;"><?= $ui->item('CARTNEW_LOGIN_SOCIAL') ?></h2>
			<script src="/new_js/modules/social.js" type="text/javascript"></script>
			<div class="social_auth">
				<a href="<?= Yii::app()->createUrl('widgets/authInstagram') ?>" target="_blank" onclick="return instagramCom.getUserInfo(this);"><span class="fa instagram"></span></a>
				<a href="<?= Yii::app()->createUrl('widgets/authFacebook') ?>" target="_blank" onclick="return facebookCom.getUserInfo(this);"><span class="fa facebook"></span></a>
				<a href="<?= Yii::app()->createUrl('widgets/authVk') ?>" target="_blank" onclick="return vkCom.getUserInfo(this);"><span class="fa vk"></span></a>
				<a href="<?= Yii::app()->createUrl('widgets/authTwitter') ?>" target="_blank" onclick="return twitterCom.getUserInfo(this);"><span class="fa twitter"></span></a>
			</div>
		</div>

	</div>
	<div class="clearfix"></div>

	<div class="clearfix"></div>




<script>
	$(document).ready(function() {
		
		//yaCounter53579293.reachGoal('cart_step2');
		ym(53579293, 'reachGoal', 'cart_step2');
	})
</script>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(53579293, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/53579293" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->