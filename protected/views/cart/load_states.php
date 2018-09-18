<select name="Address[state_id]" onclick="save_form();"><option value="">---</option><?php

foreach ($items as $item) {
    
    ?>


    <option value="<?=$item['id']?>"><?=$item['title_long']?></option>
    

<?php
    
}

?></select>