<?php
function printTree($tree, $entity, $parent = false, $lvl = 1)

{
	
    if (!is_null($tree) && count($tree) > 0)
    {
        
		if (!$parent) {
				
			echo ' <div class="b-category-list__topic">'.Yii::app()->ui->item('A_NEW_CATEGORYES').'</div><ul class="b-category-list__item-outer">';
		
		} else {
			
			
			echo '
            <ul class="b-category-list__inner-list js-slide-content-inner-list tglvl'.$lvl.'">';
		}
		
		
        
		foreach ($tree as $node)
        {
			$cross = '';
			if (!$node['children']) {
				$cross = ' cross3 ';
			}
			
			if ($parent) {

				echo '<li class="b-category-list__item-inner '.$cross.' lvl'.$lvl.'"">';
			
			} else {
				echo '<li class="b-category-list__item '.$cross.' lvl'.$lvl.'">';
			}
            echo '<a title="'.ProductHelper::GetTitle($node['payload']).'" class="b-category-list__link" href="'.Yii::app()->createUrl('entity/list',
                array('entity' => Entity::GetUrlKey($entity),
                      'cid' => $node['payload']['id'],
                      'title' => ProductHelper::ToAscii(ProductHelper::GetTitle($node['payload'])))).'">'.ProductHelper::GetTitle($node['payload']).'</a>';
					  
					  if ($node['children']) {
					  
					  if ($lvl > 1) {
					  
						  echo '
							<div class="b-category-list__cross cross2 js-slide-toggle" data-slidecontext=".lvl'.$lvl.'" data-slideclasstoggle=".lvl'.$lvl.'" data-slidetoggle=".tglvl'.($lvl+1).'"></div>';
							
						 } else {
							 echo '
							<div class="b-category-list__cross cross1 js-slide-toggle" data-slidecontext=".lvl'.$lvl.'" data-slideclasstoggle=".lvl'.$lvl.'" data-slidetoggle=".tglvl'.($lvl+1).'"></div>';
						 }
					  
					  }
            printTree($node['children'], $entity, true, ($lvl+1));
            echo '</li>';
        }
        	echo '</ul>';
		}
}
?>
<?php $this->widget('TopBar', array('breadcrumbs' => $this->breadcrumbs)); ?>
<section class="b-all-category">
	<div class="container b-all-category__wrapper">
	
		<div class="b-category-list">


		<?php printTree($tree, $entity, false); ?>
	</div>
		<div class="b-user-seen">
				<?php $this->widget('YouView', array('tpl'=>'you_view_categories')); ?>
        </div>
		

</section>