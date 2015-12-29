<?php
$dir="/phpstudy/www/swoole/api_docs/apidoc/";
$target_dir="apidoc/";
$files=scandir($dir);
foreach($files as $file){
    if($file != '.' && $file != '..'){
        echo "<a target='showframe' href=".$target_dir.$file.">$file</a><br>";
    }
}