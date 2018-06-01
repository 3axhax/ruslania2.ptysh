<?php

class ProductOfDay extends MyWidget
{
    public function run()
    {
        $key = 'ProductOfTheDay';
        $item = Yii::app()->dbCache->get($key);
        if ($item === false)
        {
            $sql = "SELECT * FROM product_of_day WHERE is_active=1 ORDER BY date_of DESC LIMIT 1";
            $row = Yii::app()->db->createCommand($sql)->queryRow();
            if (empty($row)) return;

            $entity = Entity::ConvertToHuman($row['entity_id']);
            $id = $row['item_id'];
            $p = new Product();
            $item = $p->GetProduct($entity, $id);
            Yii::app()->dbCache->set($key, $item, 600);
        }

        if (empty($item)) return;

        $this->render('product_of_day', array('item' => $item));
    }
}
