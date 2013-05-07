<?php
class SpecialBitcoinPayment extends SpecialPage {
	public function __construct() {
		parent::__construct('BitcoinPayment');
	}

	public function execute($par) {
		global $wgUser, $wgBitcoinPaymentFee, $wgBitcoinPaymentSSL;

		if ($par == 'callback') {
			if (!BitcoinPayment::mtgox_check_post()) {
				die("error");
			}
			if (($_POST['status'] != 'confirmed') && ($_POST['status'] != 'published')) die("not_confirmed"); // don't care
			if ($_POST['amount_int'] < $wgBitcoinPaymentFee ) die("too_low"); // amount too low
			if ($_POST['item'] != 'BTC') die('not_btc');

			$desc = $_POST['description'];
			if (substr($desc, 0, 3) != 'WP#') die('bad_desc');

			$user = User::newFromId(substr($desc, 3));
			$user->addGroup('trusted');

			return;
		}

		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		if ($wgUser->isAnon()) {
			$wikitext = 'You need to [[Special:UserLogin|login]] to access this page';
			$wikitext .= "\n\n{{BitcoinPayment|status=nologin}}";
			$output->addWikiText( $wikitext );
			return;
		}

		$groups = $wgUser->getGroups();
		if (array_search('trusted', $groups) !== false) {
			$wikitext = 'You are already trusted, thank you!';
			$wikitext .= "\n\n{{BitcoinPayment|status=done}}";
			$output->addWikiText( $wikitext );
			return;
		}

		$btc_addr = $wgUser->getOption('bitcoinpayment-addr');

		if (is_null($btc_addr)) {
//			$url = 'http'.(isset($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			if( $wgBitcoinPaymentSSL )
				$protocol = 'https';
			else
				$protocol = 'http';
			$url = $protocol.'://'.$_SERVER['HTTP_HOST'].'/wiki/Special:BitcoinPayment/callback';
			$btc_addr = BitcoinPayment::mtgox_query('2/money/bitcoin/address', array('ipn' => $url, 'description' => 'WP#'.$wgUser->getId()));
			if ($btc_addr['result'] != 'success') {
				$wikitext = 'An error occured, please retry later';
				$output->addWikiText( $wikitext );
				return;
			}
			$btc_addr = $btc_addr['data']['addr'];
			$wgUser->setOption('bitcoinpayment-addr', $btc_addr);
			$wgUser->saveSettings();
		}

		$wikitext = 'In order to be able to edit pages on this wiki, you will need to send a payment of at least 0.01 BTC to [bitcoin:'.$btc_addr.' '.$btc_addr.']';
		$wikitext .= "\n\n";
		$wikitext .= 'Please note that you will need to wait for your transfer to be confirmed.';
		$wikitext .= "\n\n{{BitcoinPayment|status=todo|addr=$btc_addr}}";

		$output->addWikiText( $wikitext );
	}
}

