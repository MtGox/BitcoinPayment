# BitcoinPayment

This simple plugin allows users to join the mediawiki group "trusted" by paying.

You will need a "BitcoinPayment" template. One example is available there:

https://en.bitcoin.it/w/index.php?title=Template:BitcoinPayment&action=edit

Installation requires that you paste the following into LocalSettings.php.

```
#MtGox API credentials (account required with API key with Deposit and Merchant privileges)
$wgBitcoinPaymentAPIKey = 'your_API_key';
$wgBitcoinPaymentAPISecret = 'your_API_secret';
$wgBitcoinPaymentSSL = true;

# Fee in satoshis to become "trusted", 0.001 BTC shown as default
$wgBitcoinPaymentFee = 100000; 

# register bitcoin: uri syntax for wikitext parsing
$wgUrlProtocols[] = "bitcoin:";

# make pages editable only for trusted and administrative users.
$wgGroupPermissions['*']['edit']                = false;
$wgGroupPermissions['administrator']['edit']    = true;
$wgGroupPermissions['bureaucrat']['edit']       = true;
$wgGroupPermissions['trusted']['edit']          = true;
```


