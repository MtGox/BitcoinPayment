# BitcoinPayment

This simple plugin allows users to join the mediawiki group "trusted" by paying.

You will need a "BitcoinPayment" template. One example is available there:

https://en.bitcoin.it/w/index.php?title=Template:BitcoinPayment&action=edit

Installation requires that you paste the following MtGox API credentials (MtGox account required) into LocalSettings.php:

```
$wgBitcoinPaymentAPIKey = 'your_API_key';
$wgBitcoinPaymentAPISecret = 'your_API_secret';
$wgBitcoinPaymentFee = 1000000;  # Fee in satoshis to become "trusted", 0.01 BTC shown as default
```
