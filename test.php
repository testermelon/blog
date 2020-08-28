<?php

include("src/data_interface.php");
include("src/data_handling.php");


$list = get_list_items("contents/posts/","musik/","no");
var_dump($list);
echo "<br>";
echo "<br>";
$list = datalist_sort($list, "date", true);
var_dump($list);

?>
