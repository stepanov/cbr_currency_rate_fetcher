<?php

include('./vendor/autoload.php');

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$date = date('Y-m-d', time() - 24 * 60 * 60);

$conf = DotEnv::createImmutable(__DIR__);
$conf->load();

$logger = new Logger('CurrencyRateFetcher');
$logger->pushHandler(new StreamHandler($_ENV['LOGGER_STORAGE'], Logger::DEBUG));

$logger->debug("Fetching rates for $date: " . $_ENV['CBR_CURRENCY_RATE_URL']);

$client = new nusoap_client($_ENV['CBR_CURRENCY_RATE_URL'], 'wsdl');
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = FALSE;

$response = $client->call($_ENV['CBR_CURRENCY_RATE_ACTION'], ['On_date' => $date]);

if (!empty($client->getError())) {
    $logger->error($client->getError());
    die($client->getError());
}

$result = $response['GetCursOnDateXMLResult']['ValuteData']['ValuteCursOnDate'];
if (empty($result)) {
    $logger->error('Wrong result received from the CBR service');
    die('Wrong result received from the CBR service');
}

$rates = [];
foreach($result as $item) {
    $item['Vname'] = trim($item['Vname']);
    array_push($rates, $item);
}

$jsonRates = json_encode($rates, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

$logger->info("JSON Rates: $jsonRates");

echo "$jsonRates\n";
