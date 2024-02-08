<?php /** @var WC\Module $this */ 

if( $this->wc->request->origin ){
    header('Access-Control-Allow-Origin: ' . $this->wc->request->origin);
}

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

if( $this->wc->request->accessControlRequestHeaders ){
    header('Access-Control-Allow-Headers: ' . $this->wc->request->accessControlRequestHeaders);
}

if( $this->wc->request->method === 'OPTIONS' ){
    http_response_code(200);
    exit;
}

$url = $this->wc->request->param('url', 'get');

if( !$url ){
    http_response_code(400);
    exit;
}

$parsed_url = parse_url( strtolower($url) );

if( !$parsed_url ){
    http_response_code(400);
    exit;
}

$host = $parsed_url['host'];

if( substr($host, 0, 4) === 'www.' ){
    $host = substr($host, 4);
}


$this->wc->debug->enable();
$this->wc->debug( $host );
$this->wc->dump( substr($host, 0, 1) );

$this->wc->debug( $this->wc->witch('catalog')->daughters()  );


$this->wc->dump(  $parsed_url );

//$curl = curl_init('https://api.codexradar.com/youtubeprofilerpicturedownloader/');
$curl = curl_init('https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=https://witch-case.fr/wc&key=AIzaSyAi2ffint6zkRM_xS2oOSUnqzhJ88VDPpc');
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => [
        //'authority' => 'api.codexradar.com',
        'sec-ch-ua' => '"Chromium";v="92", " Not A;Brand";v="99", "Google Chrome";v="92"',
        'accept' => '*/*',
        'sec-ch-ua-mobile' => '?0',
        'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
        'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
        //'origin' => 'https://www.codexradar.com',
        'sec-fetch-site' => 'same-site',
        'sec-fetch-mode' => 'cors',
        'sec-fetch-dest' => 'empty',
        //'referer' => 'https://www.codexradar.com/',
        'accept-language' => 'fr-FR,fr;q=0.9'
    ],
    //CURLOPT_POSTFIELDS => 'url=' . $url
]);

//$response = curl_exec($curl);

$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

//var_dump($httpcode);
$httpcode = 200; $response = 'response'; 
//die($httpcode);

if ($httpcode !== 200) {
    http_response_code(500);
    exit;
}

if (!$response) {
    http_response_code(500);
    exit;
}

/*
echo "<pre>";
var_dump($body);
echo "</pre>";


$explodedResponse = explode(PHP_EOL, $response);

foreach ($explodedResponse as $explodedResponseLineIndex => $explodedResponseLine) {
    if ($explodedResponseLine === '') {
        break;
    }
}

$responseLines = array_splice($explodedResponse, $explodedResponseLineIndex);
$responseText = implode($responseLines);

if (! $responseText) {
    http_response_code(500);
    exit;
}

$jsonResponse = json_decode($body, true);

if ($jsonResponse === null) {
    http_response_code(404);
    exit;
}
*/
http_response_code(200);
//header('content-type:application/json');
echo $body;


//$this->wc->debug->disable();

//$this->setContext('default');
$this->setContext('empty');

//$this->view();
