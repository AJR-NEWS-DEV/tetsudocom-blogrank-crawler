<?php
//パースエラー回避
//ini_set('display_errors', "On");
libxml_use_internal_errors(true);
//テンポラリファイルを作成する
$cookie= "tetsudocomcookie.txt";
//鉄道コムにログインしてランキングをクロール、Jsonで返す
$ch1 = curl_init();
curl_setopt($ch1, CURLOPT_URL, 'https://www.tetsudo.com/help/blog.html');
//クッキーを書き込むファイルを指定
curl_setopt($ch1, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
//ヘッダ
$headers = [
    'user-agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36'
];
curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
$v = curl_exec($ch1);
curl_close($ch1);
$ch2 = curl_init();
//ログインURL
curl_setopt($ch2, CURLOPT_URL, 'https://www.tetsudo.com/user/checkin/');
//クッキーを読み込むファイルを指定
curl_setopt($ch2, CURLOPT_COOKIEFILE, $cookie);
//クッキーを書き込むファイルを指定
curl_setopt($ch2, CURLOPT_COOKIEJAR, $cookie);
//リダイレクト有効
curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
//結果出力
//curl_setopt($ch2, CURLOPT_HEADER, 1); 
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1); 
//POST設定
curl_setopt($ch2, CURLOPT_POST, 1);
curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query(
    [
        "name" => "YOUR_NICK_NAME", //ニックネーム
        "pass" => "YOUR_PASSWORD", //パスワード
        "type" => "checkin",
        "done" => "https://www.tetsudo.com/help/blog.html"
    ]
));

//ヘッダ
$headers = [
    'referer: https://www.tetsudo.com/help/blog.html',
    'Content-Type: application/x-www-form-urlencoded',
    'user-agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36'
];
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);

//実行
$result = curl_exec($ch2);

//DOM解析
$domel = new DOMDocument;
$domel->loadHTML($result);
$xpath = new DOMXPath($domel);
$out = [];
$out["count"]["in"] = (int)preg_replace('/[^0-9]/', '', $xpath->query('//dd[@class="in-point"]')->item(0)->nodeValue);
$out["count"]["out"] = (int)preg_replace('/[^0-9]/', '', $xpath->query('//dd[@class="out-point"]')->item(0)->nodeValue);
$rank_nodes = $xpath->query('//li[@class="inout-rank"]')->item(0)->childNodes;
$n = 0;
foreach($rank_nodes as $node) {
    $out["rank"][$n]["cat"] = preg_replace( '(\r|\n|\t)', '', $xpath->query('//dt[@class="cat-name"]')->item($n)->nodeValue);
    $out["rank"][$n]["rank"] = (int)preg_replace('/[^0-9]/', '', $xpath->query('//dd[@class="cat-rank"]')->item($n)->nodeValue);
    $n++;
}

//echo var_dump($out);
$o = json_encode($out,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
curl_close($ch2);
//Cookieリセット
file_put_contents("tetsudocom_rank.json", $o);
file_put_contents($cookie, "");
header("Location: tetsudocom_rank.json");
