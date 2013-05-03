<?php

class BitcoinPayment {
	public static function mtgox_check_post() {
		// API settings
		$wgBitcoinPaymentAPIKey = 'your_wgBitcoinPaymentAPIKey';
		$wgBitcoinPaymentAPISecret = 'your_wgBitcoinPaymentAPISecret';

		if ($_SERVER['HTTP_REST_KEY'] != $wgBitcoinPaymentAPIKey) return false;
		$post_data = file_get_contents('php://input');
		$hash = hash_hmac('sha512', $post_data, base64_decode($wgBitcoinPaymentAPISecret), true);
		if (base64_decode($_SERVER['HTTP_REST_SIGN']) != $hash) return false;

		return true;
	}

	public static function mtgox_query($path, array $req = array()) {
		// API settings
		$wgBitcoinPaymentAPIKey = 'your_wgBitcoinPaymentAPIKey';
		$wgBitcoinPaymentAPISecret = 'your_wgBitcoinPaymentAPISecret';
	 
		// generate a nonce as microtime, with as-string handling to avoid problems with 32bits systems
		$mt = explode(' ', microtime());
		$req['nonce'] = $mt[1].substr($mt[0], 2, 6);
	 
		// generate the POST data string
		$post_data = http_build_query($req, '', '&');

		$prefix = '';
		if (substr($path, 0, 2) == '2/') {
			$prefix = substr($path, 2)."\0";
		}
	 
		// generate the extra headers
		$headers = array(
			'Rest-Key: '.$wgBitcoinPaymentAPIKey,
			'Rest-Sign: '.base64_encode(hash_hmac('sha512', $prefix.$post_data, base64_decode($wgBitcoinPaymentAPISecret), true)),
		);
	 
		// our curl handle (initialize if required)
		static $ch = null;
		if (is_null($ch)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MtGox PHP client; '.php_uname('s').'; PHP/'.phpversion().')');
		}
		curl_setopt($ch, CURLOPT_URL, 'https://mtgox.com/api/'.$path);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	 
		// run the query
		$res = curl_exec($ch);
		if ($res === false) throw new Exception('Could not get reply: '.curl_error($ch));
		$dec = json_decode($res, true);
		if (!$dec) throw new Exception('Invalid data received, please make sure connection is working and requested API exists');
		return $dec;
	}
}

