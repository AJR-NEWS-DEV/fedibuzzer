<?php
require_once "_SOFTWARE_.php";
error_reporting(E_ALL);
ini_set('display_errors', 0);

//ヘッダーの設定
$header = [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36 Fedibuzzer/0.3.0",
];
 
//オプション設定
$options = [
    'http' => [
        'method' => "GET",
        'header' => implode("\r\n", $header),
    ]
];

function error($code, $message) {
    header("Content-type: application/json");
    echo json_encode([
        "status" => $code,
        "message" => $message
    ]);
    exit;
}

if (empty($url)) {
    error(400, "domain is required");
}

$real_domain = parse_url($url, PHP_URL_HOST);
if (!$real_domain) {
    $real_domain = $url;
}

switch($real_domain) {
    case "twitter.com":
        case "x.com":
            header("Content-type: application/json");
            echo json_encode([
                "status" => 200,
                "body" => [
                    "name" => "Twitter",
                    "urlScheme" => 'https://twitter.com/intent/tweet?text=__TEXT__',
                ]
            ]);
            exit;
    case "taittsuu.com":
        header("Content-type: application/json");
        echo json_encode([
            "status" => 200,
            "body" => [
                "name" => "タイッツー",
                "urlScheme" => 'https://taittsuu.com/share?text=__TEXT__',
            ]
        ]);
        exit;
}

$node_info = file_get_contents("https://{$real_domain}/.well-known/nodeinfo", false, stream_context_create($options));

if (!$node_info) {
    error(500, "server returned unknown error");
}

$node_info = json_decode($node_info, true);

if (isset($node_info["links"][0]["href"])) {
    $node_info = file_get_contents($node_info["links"][0]["href"], false, stream_context_create($options));
    if (!$node_info) {
        error(500, "server returned unknown error");
    }
    
    $node_info = json_decode($node_info, true);   
}

if (isset($node_info["software"])) {
    $server_name = mb_strtolower($node_info["software"]["name"]);
    if (!array_key_exists($server_name, FEDSHARE_SUPPORTED_SOFTWARE)) {
        error(500, "we don't support this software");
    } else {
        $info = FEDSHARE_SUPPORTED_SOFTWARE[$server_name];

        header("Content-type: application/json");
        echo json_encode([
            "status" => 200,
            "body" => [
                "name" => $info["name"],
                "urlScheme" => 'https://'. $real_domain. $info["url"],
            ]
        ]);
    }
} else {
    error(500, "server doesn't have software information");
}