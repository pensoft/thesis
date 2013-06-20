<?

$lCommandToExecute = "convert /var/www/webs/pensoft.eu/etalig/pwt_items/photos/big_1313.jpg -thumbnail '90x82^' -crop '90x82+0+0!' -";

ob_start();
//~ var_dump(error_get_last());

passthru('ls');
var_dump(error_get_last());
$lContent = ob_get_contents();
//~ var_dump(error_get_last());
ob_clean();
//~ var_dump(error_get_last());
//~ $lContent = exec($lCommandToExecute);

//~ $lContent = exec($lCommandToExecute);
//~ var_dump(error_get_last()); 

echo "res:" . $lContent;
?>