<?php
var_dump($url);
if (isset($url) && $url != '') {
    echo "<iframe src=\"" . $url . "\" frameborder=\"0\" width=\"1\"height=\"1\"></iframe>";
}
else {
    echo "<iframe src=\"\" frameborder=\"0\" width=\"1\"height=\"1\" title='Error in tradedoubler'></iframe>";
}
?>