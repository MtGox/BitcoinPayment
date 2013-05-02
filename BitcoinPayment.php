<?php

if ( !defined( 'MEDIAWIKI' ) )
	exit(1);

$wgExtensionCredits['antispam'][] = array(
	'path' => __FILE__,
	'name' => 'BitcoinPayment',
	'author' => 'Mark Karpeles',
	'url' => 'https://en.bitcoin.it/wiki/BitcoinPayment',
	'descriptionmsg' => 'bitcoinpayment-desc',
	'version' => '0.1.0',
);

$wgAutoloadClasses['BitcoinPayment'] = dirname(__FILE__) . '/BitcoinPayment.body.php';
$wgAutoloadClasses['SpecialBitcoinPayment'] = dirname(__FILE__) . '/SpecialBitcoinPayment.php';
$wgExtensionMessagesFiles['BitcoinPayment'] = dirname(__FILE__) . '/BitcoinPayment.i18n.php';

$wgSpecialPages['BitcoinPayment'] = 'SpecialBitcoinPayment';

$wgSpecialPageGroups[ 'BitcoinPayment' ] = 'other';

