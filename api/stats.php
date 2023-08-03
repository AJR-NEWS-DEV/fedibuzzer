<?php
$domain = $_POST['domain'];
$path = dirname(__FILE__). '/../stats/count.txt';

if (!empty($domain)) {
    if (file_exists($path)) {
        $before = explode("\n", file_get_contents($path));
    } else {
        $before = [];
    }
    if (!in_array($domain, $before)) {
        array_push($before, $domain);
        file_put_contents($path, implode("\n", $before), LOCK_EX);
    }
}

http_response_code(204);
exit;