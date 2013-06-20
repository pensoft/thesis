<?

$lCommandToExecute = "convert /var/www/webs/pensoft.eu/etalig/pwt_items/photos/big_1313.jpg -thumbnail '90x82^' -crop '90x82+0+0!' -";

ob_start();
passthru($lCommandToExecute);
var_dump(error_get_last()); 
$lContent = ob_get_contents();
ob_clean();
//~ var_dump($_POST);
//~ $lContent = exec($lCommandToExecute);


echo "!!!!" . $lContent;
?>