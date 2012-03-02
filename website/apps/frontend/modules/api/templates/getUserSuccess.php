<?php
	echo htmlspecialchars_decode($api->getResponse());
	
	/*
	$os = new apiOpenSsl();
	$os->createKeys();
	echo "<br/>priv: ".$os->privKey."<br/>";
	echo "<br/>pub: ".$os->pubKey["key"]."<br/>";
	$source= "some text";
	echo "<br/>original: ".$source."<br/>";
	$crypttext = $os->encrypt($source);
	echo "<br/>encrypt: ".$crypttext."<br/>";
	$newsource = $os->decrypt($crypttext);
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
	*/
?>