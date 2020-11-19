# Currency rate fetcher

This script fetches currency rates from the Central Bank of Russia for yesterday. It uses SOAP API and convert received data to JSON.

## Install

Clone repository and chdir there.

```
composer install
```

## Run

```
cp .env_example .env
```
```
chmod +x currency_rate_fetcher.php
```
```
./currency_rate_fetcher.php | jq
```
