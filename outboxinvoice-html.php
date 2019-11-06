<?php 
// Turkcell Token Alabilmek İçin Sayfası Dahil Ediyoruz
include "turkcell-function.php";

$fatura_id = 55;


//Token Oluştu
$token = turkcell_token();
// ve Göndereceğimiz Post un Header Bilgilerini Hazırlayalım
$turkcell_curl_header = array();
$Authorization = "Authorization: Bearer ".$token;
array_push($turkcell_curl_header,$Authorization);
array_push($turkcell_curl_header,"Content-Type: application/json");

// URL Test ve Gerçek Ortama Göre Değişir.
$url = "https://efaturaservice.turkcellesirket.com/v1/outboxinvoice/html?id=".$fatura_id."&isStandartXslt=false";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => $turkcell_curl_header,
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  // Hata Var Mı
  echo "cURL Error #:" . $err;
} else {
  // Gelen Data HTML Formatında Direk Ekrana Basıyorum
  echo $response;
}

 ?>

