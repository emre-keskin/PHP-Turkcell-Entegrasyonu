<?php 
// Bu Sayfada Turkcell Fonksiyonları Bulunmkata.


function turkcell_token()
{
	// Bu Fonksiyon: Turkcell İşlemiz İçin Gerekli Olan Tokeni Bize Verecektir.

		// URL Kısmı Test ve Gercek Ortam İçin Farklıdır.
	$username = "username";
	$password = "password";
	$client_id = "client_id";
	$post_body = "username=".$username."&password=".$password."&client_id=".$client_id."";
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "https://core.turkcellesirket.com/v1/token",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "POST",
		CURLOPT_POSTFIELDS => $post_body,
		CURLOPT_HTTPHEADER => array(
			"Content-Type: application/x-www-form-urlencoded"
		),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		echo "cURL Error #:" . $err;
	} else {
		$token = json_decode($response, true);

		return $token['access_token'];
	}


}





?>