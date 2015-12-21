<?php
$dir="/phpstudy/www/swoole/api_docs/apidoc";
$files=scandir($dir);
foreach($files as $file){
	if($file != '.' && $file != '..'){
		echo $file."\n";
	}
}
$out = shell_exec("cat /phpstudy/www/swoole/api_docs/apidoc/total_user_api.php");
echo "<pre>";
//print_r($out);
echo "</pre>";
?>
