<?php $cnt = $this->paginatorInfo->getItemCount(); ?>
<div class="text" style="margin-top: 22px;">
    <form method="GET">
    
        <?php
        $this->widget('MyPagesWidget', array('pages' => $this->paginatorInfo,
                                          'header' => '',
                                          'nextPageLabel' => 'Далее',
                                          'prevPageLabel' => 'Назад',
                                          'firstPageLabel' => '',
                                          'lastPageLabel' => '',
                                          'separator' => '',
                                          'htmlOptions' => array('class' => 'pager', 'char' => $this->pageChar)));
        ?>
        
    </form>
</div>
<div class="itemsep1"></div>
