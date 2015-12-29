<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 12/21/15
 * Time: 5:32 PM
 */

$res = shell_exec('/data/wwwroot/api_docs/docs.sh');
print_r($res);