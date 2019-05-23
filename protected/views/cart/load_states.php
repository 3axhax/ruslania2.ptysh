<select name="Address<?=$_POST['tbl']?>[state_id]" onclick="" style="width: 220px;"><option value="">---</option><?php

foreach ($items as $item) {
    
    ?>


    <option value="<?=$item['id']?>"><?=$item['title_long']?></option>
    

<?php
    
}

?></select>