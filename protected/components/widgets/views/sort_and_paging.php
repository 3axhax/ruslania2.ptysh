<?php $cnt = $this->paginatorInfo->getItemCount(); ?>
<div class="text" style="margin-top: 22px;">
    <form method="GET">
    
        <?php
        $this->widget('MyLinkPager', array('pages' => $this->paginatorInfo,
                                          'header' => '',
                                          'nextPageLabel' => $ui->item('A_NEW_PAGE_NEXT'),
                                          'prevPageLabel' => $ui->item('A_NEW_PAGE_PREV'),
                                          'firstPageLabel' => '',
                                          'lastPageLabel' => '',
                                          'separator' => '',
                                          'htmlOptions' => array('class' => 'pager')));
        ?>
        
    </form>
</div>
<div class="itemsep1"></div>
