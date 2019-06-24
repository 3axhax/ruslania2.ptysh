<?php $url = ProductHelper::CreateUrl($item); ?>

<div class="img" style="position: relative">
    <?php $this->renderStatusLables($item['status'], $size = '-sm', true)?>
    <a title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" href="<?=$url; ?>"><img src="<?= Picture::srcLoad() ?>" data-lazy="<?=Picture::Get($item, Picture::SMALL); ?>"  фде="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>" /></a>
 </div>
 
    <div class="title_book">
        <a href="<?=$url; ?>" title="<?=htmlspecialchars(ProductHelper::GetTitle($item, 'title')); ?>">
                <?=ProductHelper::GetTitle($item, 'title'); ?><span class="gradient_link"></span></a>

    </div>

    <?php if($entity != Entity::PERIODIC):?>
	<div class="author"><?php if (!empty($item['Authors']) OR !empty($item['Performers']) OR !empty($item['Directors'])) : ?>

            <?php $tmp = array(); if (!empty($item['Authors'])) : ?>
            <?php foreach ($item['Authors'] as $author)
            {
                $authorTitle = ProductHelper::GetTitle($author);
                $tmp[] = $authorTitle;
            } ?>
			<?php endif; ?>
			<?php if (empty($tmp)&&!empty($item['Performers'])) : ?>
			<?php
            
            foreach ($item['Performers'] as $performer)
            {
                $tmp[] = ProductHelper::GetTitle($performer);
            }?>
			<?php endif; ?>
			
			<?php if (empty($tmp)&&!empty($item['Directors'])) : ?>
			
            
            <?php foreach ($item['Directors'] as $director) : ?>
                <? $tmp[] = ProductHelper::GetTitle($director); ?>
            <?php endforeach; ?>
       
			<?php endif; ?>
			
			<?php if (empty($tmp)&&!empty($item['Directors'])) : ?>
			<?php foreach ($item['Directors'] as $director) : ?>
               <? $tmp[] = ProductHelper::GetTitle($director); ?>
            <?php endforeach; ?>
			<?php endif; ?>
			
			<? if (!empty($tmp)) {
                if (count($tmp) > 1) {

                    echo $tmp[0] . ',...';

                } else {

                    echo implode(', ', array_unique($tmp));

                }
            }

            ?>




    <?php endif; ?>
	 </div>
    <?php endif;?>
        
    <?php $price = DiscountManager::GetPrice(Yii::app()->user->id, $item);

    ?>
<?php if (!empty($item['avail_for_order'])): ?>
	<div class="cost">
        <?php if (!empty($item['priceData'][DiscountManager::DISCOUNT])) : ?>
            <span class="without_discount">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::BRUTTO]); ?>
                </span>&nbsp;
            <span class="price with_discount"<?php if ($item['priceData'][DiscountManager::DISCOUNT_TYPE] == DiscountManager::TYPE_PERSONAL):?> style="color: #42b455;" <?php endif; ?>>
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
        <?php else : ?>
            <span class="price">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
        <?php endif; ?>
	</div>
    <div class="nds"<?php if($item['entity'] == Entity::PERIODIC):?> style="display: none;" <?php endif; ?>><?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $item['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
<?php endif; ?>
    <?php if ($entity != Entity::PERIODIC):?>
                    <div class="addcart">
					
					<?
					
					$sCount = Cart::getCountCartItem($item['id'], $item['entity'], $this->uid, $this->sid);
					
					if ($sCount > 0) :
					?>
					<a class="fa cart-action add_cart list_cart add_cart_plus add_cart_view cart<?=$item['id']?> green_cart" data-action="add" data-entity="<?= $item['entity']; ?>" data-id="<?= $item['id']; ?>" data-quantity="1" href="javascript:;"  style="width: 132px;" onclick="searchTargets('add_cart_index');">
                        <span><?=sprintf($ui->item('CARTNEW_IN_CART_BTN'), $sCount)?></span>
                    </a>
					
					<? else : ?>
					
					<a class="cart-action add_cart_plus list_cart cart<?=$item['id']?>" data-action="add" data-entity="<?= $item['entity']; ?>"
               data-id="<?= $item['id']; ?>" data-quantity="1"
               href="javascript:;" style="width: 132px;" onclick="searchTargets('add_cart_index');"><span><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></span></a>
					<? endif; ?>
					
                      
			   
			   
			   
			   
                    </div>
    <?php else:?>
        <div class="more">
            <a class="fa" href="<?=$url?>"><span><?=$ui->item('A_NEW_MORE3');?></span></a>
    </div>
    <?php endif;?>