<?php
/**
 * OpenSSL Helper to use with otokou API
 *
 * encrypt test code not integrated in project
 *
 * @author Dave Bergomi
 */
 
 /*
 // some example
 	//save to file
	$os = new apiOpenSsl();
	$os->createKeys();
	echo "<br/>priv: ".$os->privKey."<br/>";
	echo "<br/>pub: ".$os->pubKey["key"]."<br/>";
	echo "<br/>pub x509: ".$os->getX509()."<br/>";
	$source= "some text";
	echo "<br/>original: ".$source."<br/>";
	$crypttext = $os->encrypt($source);
	echo "<br/>encrypt: ".$crypttext."<br/>";
	$base64_crypttext = base64_encode($crypttext);
	echo "<br/>base64: ".$base64_crypttext."<br/>";
	$unbase64_crypttext = base64_decode($base64_crypttext);
	$newsource = $os->decrypt($unbase64_crypttext);
	echo "<br/>decrypt: ".$newsource."<br/>";
	
	$handle = fopen("./pdf/prkey.txt", "w");
	fwrite($handle,$os->privKey);
	fclose($handle);
	
	$handle = fopen("./pdf/pukey.txt", "w");
	fwrite($handle,$os->pubKey["key"]);
	fclose($handle);
	
	$handle = fopen("./pdf/crypt.txt", "w");
	fwrite($handle,$crypttext);
	fclose($handle);
	
	$handle = fopen("./pdf/base64.txt", "w");
	fwrite($handle,$base64_crypttext);
	fclose($handle);
	
	
	//load from file
	$os = new apiOpenSsl();
	$os->createKeys();
	echo "<br/>priv: ".$os->privKey."<br/>";
	echo "<br/>pub: ".$os->pubKey["key"]."<br/>";
	echo "<br/>pub x509: ".$os->getX509()."<br/>";
	$source= "some text";
	echo "<br/>original: ".$source."<br/>";
	$crypttext = $os->encrypt($source);
	echo "<br/>encrypt: ".$crypttext."<br/>";
	$base64_crypttext = base64_encode($crypttext);
	echo "<br/>base64: ".$base64_crypttext."<br/>";
	$unbase64_crypttext = base64_decode($base64_crypttext);
	$newsource = $os->decrypt($unbase64_crypttext);
	echo "<br/>decrypt: ".$newsource."<br/>";
	
	$handle = fopen("./pdf/prkey.txt", "w");
	fwrite($handle,$os->privKey);
	fclose($handle);
	
	$handle = fopen("./pdf/pukey.txt", "w");
	fwrite($handle,$os->pubKey["key"]);
	fclose($handle);
	
	$handle = fopen("./pdf/crypt.txt", "w");
	fwrite($handle,$crypttext);
	fclose($handle);
	
	$handle = fopen("./pdf/base64.txt", "w");
	fwrite($handle,$base64_crypttext);
	fclose($handle);
	
	
	// read a received base64 criptext
	$base64_crypttext = "dsxEcaVC8Av9fRn9WALPQmRzAiT8VS8aO/mm0jSurEcfyf3vniCuHYTzA/s1pAU9icS28gXVjY8JGbpbS0+gX8Ran3O9Hfo9RGHuP/M23WjjC6zDZtJxAEk3b/wYcecv1mzd18bBWorcJeLkIqURLraGqJmypme7kChSwJQzZJ8=";
	//$base64_crypttext = "lG43aua2rAms5JHI1v9XekwM65zb76dma5zD9RdWjrAXy4ZEIwaSxXZHXJ/7ei3w7QSEfxVe4eeaR6nsLu2Y509VK0GZrpfOhKdA/I1m3n/q+rUnUr/12WdRp8SrJrCaruwwGm6zEUHYRMc522Jhu2OuAaLdUghYGmyQyZ2HmuM=";
	$unbase64_crypttext = base64_decode($base64_crypttext);
	
	$fp=fopen("./pdf/prkey.txt","r");
	$priv_key=fread($fp,8192);
	fclose($fp);
	// $passphrase is required if your key is encoded (suggested)
	$res = openssl_get_privatekey($priv_key,sha1($_SERVER['REMOTE_ADDR']));
	openssl_private_decrypt($unbase64_crypttext,$newsource,$res);
	echo "String decrypt : $newsource";
 
 */
class apiOpenSsl
{
	public $configargs;
	public $res;
	public $passPhrase;
	public $privKey;
	public $pubKey;
	public $dn;
	public $csr;
	public $sscert;
	
	public function apiOpenSsl() {
		if ($_SERVER['HTTP_HOST'] == 'otokou.donax.ch') {
			$this->configargs = array(
				'encrypt_key'=>true,
				'private_key_type'=>OPENSSL_KEYTYPE_RSA,
				'digest_alg'=>'sha256',
				'private_key_bits'=>1024,
				'x509_extensions'=>'usr_cert');
			}
		else {
			$this->configargs = array(
				'config' => 'D:\wamp\bin\apache\Apache2.2.11\conf\openssl.cnf',
				'encrypt_key'=>true,
				'private_key_type'=>OPENSSL_KEYTYPE_RSA,
				'digest_alg'=>'sha256',
				'private_key_bits'=>1024,
				'x509_extensions'=>'usr_cert');
		}
		
		$this->dn = array(
			"countryName" => "UK",
			"stateOrProvinceName" => "Somerset",
			"localityName" => "Glastonbury",
			"organizationName" => "The Brain Room Limited",
			"organizationalUnitName" => "PHP Documentation Team",
			"commonName" => "Wez Furlong",
			"emailAddress" => "wez@example.com"
			);
		
		$this->passPhrase = sha1($_SERVER['REMOTE_ADDR']);
	}
	
	public function createKeys() {
		$this->res = openssl_pkey_new($this->configargs);
		$this->csr = openssl_csr_new($this->dn, $this->res,$this->configargs);
		$this->sscert = openssl_csr_sign($this->csr, null, $this->res,365,$this->configargs);
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
	
	public function getX509() {
		//openssl_csr_export($this->csr, $csrout);
		openssl_x509_export($this->sscert,$output);
		//openssl_pkey_export($this->res, $pkeyout, $this->passPhrase);
		return $output;
	}
}