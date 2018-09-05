<?php /*Created by Кирилл (05.09.2018 19:28)*/ ?>
<div class="index_menu">
	<div class="container">
		<ul>
			<!--Книги-->
			<li class="dd_box">
				<div class="click_arrow"></div>
				<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::BOOKS))); ?>"><?= $ui->item("A_GOTOBOOKS"); ?></a>
				<div class="dd_box_bg list_subcategs" style="left: 0;">
					<div class="span10">
						<ul id="books_menu">
							<?
							$availCategory = array(181, 16, 206, 211, 189, 65, 67, 202);
							$rows = Category::GetCategoryList(Entity::BOOKS, 0, $availCategory);
							foreach ($rows as $row) {
								?>
								<li>
									<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(10), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
								</li>
							<? } ?>
							<?php $row = Category::GetByIds(Entity::SHEETMUSIC, 47)[0] ?>
							<li>
								<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
							</li>
							<?php $row = Category::GetByIds(Entity::BOOKS, 213)[0] ?>
							<li id="books_sale">
								<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::BOOKS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
							</li>
							<li id="books_category">
								<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::BOOKS))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
							</li>
						</ul>
					</div>
				</div>
			</li>
			<!--Ноты-->
			<li class="dd_box">
				<div class="click_arrow"></div>
				<a class="dd"  href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC))); ?>"><?= $ui->item("A_GOTOMUSICSHEETS"); ?></a>
				<div class="dd_box_bg list_subcategs" style="left: -80px;">
					<div class="span10">
						<ul id="sheet_music_menu">
							<?
							$availCategory = array(47, 160, 249, 128, 136);
							$rows = Category::GetCategoryList(Entity::SHEETMUSIC, 0, $availCategory);
							foreach ($rows as $row) {
								?>
								<li>
									<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
								</li>
							<? } ?>
							<?php $row = Category::GetByIds(Entity::SHEETMUSIC, 217)[0] ?>
							<li id="sheet_music_sale">
								<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
							</li>
							<li id="sheet_music_category">
								<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::SHEETMUSIC))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
							</li>
						</ul>
					</div>
				</div>
			</li>
			<!--Музыка-->
			<li class="dd_box">
				<div class="click_arrow"></div>
				<a class="dd"  href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MUSIC))); ?>"><?= $ui->item("Music catalog"); ?></a>
				<div class="dd_box_bg list_subcategs" style="left: -170px;">
					<div class="span10">
						<ul id="music_menu">
							<?
							$availCategory = array(78, 74, 4, 11, 6, 17, 2, 73, 38);
							$rows = Category::GetCategoryList(Entity::MUSIC, 0, $availCategory);
							foreach ($rows as $row) {
								?>
								<li>
									<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
								</li>
							<? } ?>
							<?php $row = Category::GetByIds(Entity::MUSIC, 21)[0] ?>
							<li id="music_sale">
								<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MUSIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
							</li>
							<li id="music_category">
								<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::MUSIC))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
							</li>
						</ul>
					</div>
				</div>
			</li>
			<!--Подписка-->
			<li class="dd_box">
				<div class="click_arrow"></div>
				<a class="dd"  href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC))); ?>"><?= $ui->item("A_GOTOPEREODICALS"); ?></a>
				<div class="dd_box_bg list_subcategs" style="left: -280px;">
					<div class="span10">
						<ul id="periodic_menu">
							<?
							$availCategory = array(19, 48, 96, 67);
							$rows = Category::GetCategoryList(Entity::PERIODIC, 0, $availCategory);
							foreach ($rows as $row) {
								?>
								<li>
									<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PERIODIC), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
								</li>
							<? } ?>
							<?php $row = Category::GetByIds(Entity::PRINTED, 33)[0] ?>
							<li>
								<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
							</li>
							<li>
								<a href="<?= Yii::app()->createUrl('entity/gift', array('entity' => Entity::GetUrlKey(Entity::PERIODIC))) ?>"><?= $ui->item('A_NEW_PERIODIC_FOR_GIFT'); ?></a>
							</li>
							<li id="periodic_category">
								<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::PERIODIC))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
							</li>
						</ul>
					</div>
				</div>
			</li>
			<!--Ещё-->
			<li class="dd_box more_menu"><div class="click_arrow"></div>
				<a href="javascript:;" class="dd"><?= $ui->item('A_NEW_MORE'); ?></a>
				<div class="dd_box_bg dd_box_horizontal">
					<div class="tabs">
						<ul>
							<!--Сувениры-->
							<li class="dd_box">
								<div class="click_arrow"></div>
								<?php $row = Category::GetByIds(Entity::PRINTED, 6)[0] ?>
								<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_PRINT_PRODUCTS'); ?></a>
								<div class="dd_box_bg dd_box_bg-slim list_subcategs">
									<ul class="list_vertical">
										<?php $row = Category::GetByIds(Entity::PRINTED, 6)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 41)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 38)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 43)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 37)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
										</li>

										<li id="printed_category" style="color: aqua">
											<?= $ui->item('A_NEW_ALL_CATEGORIES'); ?>
										</li>
									</ul>
								</div>
							</li>
							<!--Видео-->
							<li class="dd_box">
								<div class="click_arrow"></div>
								<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::VIDEO))); ?>"><?= $ui->item("A_NEW_VIDEO"); ?></a>
								<div class="dd_box_bg dd_box_bg-slim list_subcategs">
									<ul class="list_vertical">
										<!--<li style="color: aqua">Музыкальные видео</li>-->
										<?php $row = Category::GetByIds(Entity::VIDEO, 109)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::VIDEO), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<li style="color: aqua">Современные русские фильмы</li>
										<li style="color: aqua">Классические русские фильмы</li>

										<?php $row = Category::GetByIds(Entity::VIDEO, 8)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::VIDEO), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/bysubtitle', array('entity' => Entity::GetUrlKey(Entity::VIDEO), 'sid' => 8, 'title' => 'finskij')) ?>"><?= $ui->item('A_NEW_VIDEO_FI_SUBTITLES'); ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::VIDEO, 43)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::VIDEO), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
										</li>
										<li id="video_category">
											<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::VIDEO))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
										</li>
									</ul>
								</div>
							</li>
							<!--Карты-->
							<li class="dd_box">
								<div class="click_arrow"></div>
								<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MAPS))); ?>"><?= $ui->item("A_GOTOMAPS"); ?></a>
								<div class="dd_box_bg dd_box_bg-slim list_subcategs">
									<ul class="list_vertical">
										<?php $row = Category::GetByIds(Entity::MAPS, 9)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MAPS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= trim(ProductHelper::GetTitle($row)) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::MAPS, 8)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::MAPS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
										</li>
										<li id="maps_category">
											<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::MAPS))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
										</li>
									</ul>
								</div>
							</li>
							<!--Мультимедиа-->
							<li class="dd_box">
								<div class="click_arrow"></div>
								<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT))); ?>" ><?= $ui->item("A_GOTOSOFT"); ?></a>
								<div class="dd_box_bg dd_box_bg-slim list_subcategs">
									<ul class="list_vertical">
										<?php $row = Category::GetByIds(Entity::SOFT, 1)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::SOFT, 20)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::SOFT, 16)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::SOFT), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
										</li>
										<li id="soft_category">
											<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::SOFT))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
										</li>
									</ul>
								</div>
							</li>
							<!--Прочее-->
							<li class="dd_box">
								<div class="click_arrow"></div>
								<a class="dd" href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED))); ?>"><?= $ui->item("A_NEW_OTHER"); ?></a>
								<div class="dd_box_bg dd_box_bg-slim list_subcategs">
									<ul class="list_vertical">
										<?php $row = Category::GetByIds(Entity::PRINTED, 2)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 3)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::BOOKS, 264)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::BOOKS), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 30)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 44)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 15)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 8)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 34)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 42)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= ProductHelper::GetTitle($row) ?></a>
										</li>
										<?php $row = Category::GetByIds(Entity::PRINTED, 37)[0] ?>
										<li>
											<a href="<?= Yii::app()->createUrl('entity/list', array('entity' => Entity::GetUrlKey(Entity::PRINTED), 'cid' => $row['id'], 'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($row)))) ?>"><?= $ui->item('A_NEW_SALE'); ?></a>
										</li>
										<li id="other_category">
											<a href="<?= Yii::app()->createUrl('entity/categorylist', array('entity' => Entity::GetUrlKey(Entity::PRINTED))) ?>"><?= $ui->item('A_NEW_ALL_CATEGORIES'); ?></a>
										</li>
									</ul>
								</div>
							</li>
						</ul>
						<div style="clear: both"></div>
					</div>
				</div>
			</li>
			<li class="yellow_item"><a href="<?= Yii::app()->createUrl('offers/special', array('mode' => 'alle2')); ?>"><?= $ui->item('A_NEW_GOODS_2'); ?></a></li>
			<li class="red_item"><a href="<?= Yii::app()->createUrl('offers/list'); ?>"><?= $ui->item('A_NEW_MENU_REK'); ?></a></li>
			<li class="red_item"><a href="<?= Yii::app()->createUrl('site/sale'); ?>"><?= $ui->item('A_NEW_DISCONT'); ?></a></li>
			<li><a href="<?= Yii::app()->createUrl('site/static', array('page'=>'ourstore')); ?>" class="home"><?= $ui->item('A_NEW_OURSTORE'); ?></a></li>
		</ul>
	</div>
</div>