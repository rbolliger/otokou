<?php
/**
 * OpenSSL Helper to use with otokou API
 *
 * @author Dave Bergomi
 */
class apiOpenSsl
{
	public $configargs;
	public $res;
	public $passPhrase;
	public $privKey;
	public $pubKey;
	
	public function apiOpenSsl() {
		if ($_SERVER['HTTP_HOST'] == 'otokou.donax.ch') {
			$this->configargs = array(
				'encrypt_key'=>true,
				'private_key_type'=>OPENSSL_KEYTYPE_RSA,
				'digest_alg'=>'sha256',
				'private_key_bits'=>4096,
				'x509_extensions'=>'usr_cert');
			}
		else {
			$this->configargs = array(
				'config' => 'D:\wamp\bin\apache\Apache2.2.11\conf\openssl.cnf',
				'encrypt_key'=>true,
				'private_key_type'=>OPENSSL_KEYTYPE_RSA,
				'digest_alg'=>'sha256',
				'private_key_bits'=>4096,
				'x509_extensions'=>'usr_cert');
		}
		
		$this->passPhrase = sha1($_SERVER['REMOTE_ADDR']);
	}
	
	public function createKeys() {
		$this->res = openssl_pkey_new($this->configargs);
		openssl_pkey_export($this->res, $this->privKey, $this->passPhrase, $this->configargs);
		$this->pubKey = openssl_pkey_get_details($this->res);
	}
	
	public function encrypt($source) {
		openssl_public_encrypt($source,$crypttext,$this->pubKey["key"]);
		return $crypttext;
	}
	
	public function decrypt($crypttext) {
		openssl_private_decrypt($crypttext,$output,$this->res); 
		return $output;
	}
}