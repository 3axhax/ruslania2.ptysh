<?php $url = ProductHelper::CreateUrl($item); ?>

<div class="img" style="position: relative">
    <?php $this->renderStatusLables($item['status'], $size = '-sm', true)?>
    <a href="<?=$url; ?>"><img src="<?=Picture::Get($item, Picture::SMALL); ?>" alt="" /></a>
 </div>
 
	<div class="title_book"><a href="<?=$url; ?>"><?=ProductHelper::GetTitle($item, 'title'); ?></a></div>
	
	<div class="author"><?php if (!empty($item['Authors']) OR !empty($item['Performers']) OR !empty($item['Directors']) OR !empty($item['Subtitles']) OR !empty($item['AudioStreams'])) : ?>
        
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
			
			
			<?php if (empty($tmp)&&!empty($item['Subtitles'])) : ?>
        
            <?
            foreach ($item['Subtitles'] as $subtitle)
            {
                $tmp[] = ProductHelper::GetTitle($subtitle);
            }
            
            ?>
        
			<?php endif; ?>
			  
			
			
			<?php if (empty($tmp)&&!empty($item['AudioStreams'])) : ?>
        
            <?php 
            foreach ($item['AudioStreams'] as $stream)
            {
                $tmp[] = ProductHelper::GetTitle($stream);
            }
            
            ?>
       

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
        
    <?php $price = DiscountManager::GetPrice(Yii::app()->user->id, $item);

    ?>
<?php if (!empty($item['avail_for_order'])): ?>
	<div class="cost">
        <?php if (!empty($item['priceData'][DiscountManager::DISCOUNT])) : ?>
            <span style="font-size: 90%; color: #ed1d24; text-decoration: line-through;">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::BRUTTO]); ?>
                </span>&nbsp;
            <span class="price" style="color: #301c53;font-size: 18px; font-weight: bold; white-space: nowrap;">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
        <?php else : ?>
            <span class="price">
                    <?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITH_VAT]); ?><?= $item['priceData']['unit'] ?>
                </span>
        <?php endif; ?>
	</div>
    <div class="nds"><?= ProductHelper::FormatPrice($item['priceData'][DiscountManager::WITHOUT_VAT]); ?><?= $item['priceData']['unit'] ?> <?=Yii::app()->ui->item('WITHOUT_VAT'); ?></div>
<?php endif; ?>
    <?php if ($entity != Entity::PERIODIC):?>
                    <div class="addcart">
                        <a class="cart-action" data-action="add" data-entity="<?= $item['entity']; ?>"
               data-id="<?= $item['id']; ?>" data-quantity="1"
               href="javascript:;" style="width: 132px;"><?=$ui->item('CART_COL_ITEM_MOVE_TO_SHOPCART');?></a>
                    </div>
    <?php else:?>
        <div class="more">
            <a href="<?=$url?>"><?=$ui->item('A_NEW_MORE3');?></a>
    </div>
    <?php endif;?>