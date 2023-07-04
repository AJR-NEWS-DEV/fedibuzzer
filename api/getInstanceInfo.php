<?php
require_once "_SOFTWARE_.php";

//ヘッダーの設定
$header = [
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
];
 
//オプション設定
$options = [
    'http' => [
        'method' => "GET",
        'header' => implode("\r\n", $header),
    ]
];

function error($code, $message) {
    echo json_encode([
        "status" => $code,
        "message" => $message
    ]);
    exit;
}

header("Content-type: application/json");

if (empty($_GET['domain'])) {
    error(400, "domain is required");
}

$real_domain = parse_url($_GET['domain'], PHP_URL_HOST);
if (!$real_domain) {
    $real_domain = $_GET['domain'];
}

switch($real_domain) {
    case "twitter.com":
        echo json_encode([
            "status" => 200,
            "body" => [
                "name" => "Twitter",
                "urlScheme" => 'https://twitter.com/share?text=__TEXT__',
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
    if (!array_key_exists($node_info["software"]["name"], FEDSHARE_SUPPORTED_SOFTWARE)) {
        error(500, "we don't support this software");
    } else {
        $info = FEDSHARE_SUPPORTED_SOFTWARE[$node_info["software"]["name"]];

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