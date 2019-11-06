<?php 

// Bu Sayfa İle Turkcelle E-Fatura ve E-Arşiv Faturalarımızı Gönderebiliriz.


// Tarih Belirtme
date_default_timezone_set('Europe/Istanbul');
$bugun = date("Y-m-d\TH:i:s.u");

// Fatura İçin Token Alalım
$token = turkcell_token();
// ve Göndereceğimiz Post un Header Bilgilerini Hazırlayalım
$turkcell_curl_header = array();
$Authorization = "Authorization: Bearer ".$token;
array_push($turkcell_curl_header,$Authorization);
array_push($turkcell_curl_header,"Content-Type: application/json");

// Fatura Cari Bilgileri

// İki Seçenek Var Müşteri E Fatura mı Değil Mi

if ($e_fatura_mi==0) {
	$recordType = 0;
	$invoiceProfileType = 4;
	$prefix = "AAA"; // Fatura Eki Bilgisi
	$vergi_numrasi = "11111111111";
	$cari_ad ="İsim Soy İSim";
	
	// Eğer Müşterimiz Şahıs ise vergi numrası veya tc si 11 basamaklıdır eğer firma ise 10 basamaktır
	// Bu Değeri Öğrendikten Sonra Eğer 10 Basamak İse Soyadı olamaz 
	// 11 basamk ise 
	if (strlen($vergi_numrasi)==10) {
		$cari_soyad = null;
	}else{
		$cari_bg = explode(" ", $cari_ad);
		$cari_ad = $cari_bg['0'];
		unset($cari_bg[0]);
		$cari_soyad = implode(" ", $cari_bg);
	}

}else{
	// Eğer Müşteri E-Fatura Kullanıcı İse
	$recordType = 1;
	$invoiceProfileType = 1;
	$prefix = "SSS"; // Fatura Eki Bilgisi
	$vergi_numrasi = "11111111111";
	$cari_soyad = null;
	$cari_ad = "Firma Adı veya Müşeri Adı";
	
	
}

// Sıra Stoklarda

// Stok Listesini Oluşturmak İçin Bir Dizi Oluşturalım
$fatura_stoklar= array();
// Faturamızdaki Stokları bir Döngü İle Çekelim
$stok_sor=$db->prepare("SELECT * from fatura_stokları where islem_id='1'");
$stok_sor->execute();
while ($stok = $stok_sor->fetch(PDO::FETCH_ASSOC)){

	// Stokları Gecici Bir Diziye Aktaralım $stok_tek.
	$stok_tek['disableVatExemption'] = true;
	$stok_tek['inventoryCard'] = "Stok Adı";
	$stok_tek['amount'] = "Fatura Stok Miktarı";
	$stok_tek['discountAmount'] = 0;
	$stok_tek['lineAmount'] = "Satır Tutarı Yani Fatura Stok Miktarı Çarpı Stok Kdv Hariç Fiyatı";
	$stok_tek['vatAmount'] =  "KDV Tutar Yani Fatura Stok Miktarı Çarpı Stok Kdv Tutarı";
	$stok_tek['unitCodeId'] = "Turkcell Birim İd/ Birim Listesine Ulaşmak İçin Bu Dosyanın Dizinise Bakın";
	$stok_tek['unitPrice'] =  "Stok Kdv Hariç Tutar";
	$stok_tek['vatRate'] = "KDV Oranı";
	$stok_tek['vatExemptionReasonCode'] = null;

	// Aktarıdğımız Stok Bilgilerini Fatura Stok Listesine Ekleyelim
	array_push($fatura_stoklar,$stok_tek);
}

// Fatura Açıklama Eklemesi.
$note = "Fatura Açıklması";


// Sıra Geldi Oluşturduğumuz Bilgileri Turkcell İçin Oluşmuş Diziye Eklemeye
// Bazı Değişkenlere Değer Ataması Yapılmadı İsterseniz Atama Yapabilirsiniz veya Değişkeni Silip null Yazabilirsiniz.
$fatura = 
[
	'invoiceId' => "00000000-0000-0000-0000-000000000000",
	'status' => 20,
	'isNew' => true,
	'xsltCode' => null,
	'localReferenceId' => null,
	'useManualInvoiceId' =>false,
	'addressBook' => [
		'id' => 91,
		'customerId' => "842084f8-8301-4d28-89cf-1828b3449c49",
		'isArchive' => false,
		'createdDate' => $bugun,
		'alias' => "urn:mail:".$cari_email,
		'identificationNumber' => $vergi_numrasi,
		'name' => $cari_ad,
		'registerNumber' => null,
		'receiverPersonSurName' => $cari_soyad,
		'receiverStreet' => $adres,
		'receiverBuildingName' => null,
		'receiverBuildingNumber' => null,
		'receiverDoorNumber' => null,
		'receiverSmallTown' => $mahalle,
		'receiverDistrict' => $ilce,
		'receiverZipCode' => null,
		'receiverCity' => $sehir,
		'receiverCountry' => "Türkiye",
		'receiverCountryId' => 1,
		'receiverPhoneNumber' => $telefon,
		'receiverFaxNumber' => null,
		'receiverEmail' => $cari_email,
		'receiverWebSite' => null,
		'receiverTaxOffice' => $cari_vergi_dairesi,
		'isDeleted' => false,
		'type' => 1,
		'status' => 1,
		'updatedDate' => $bugun,
		'isSaveAddress' => false
	],
	'eArsivInfo' => null,
	'invoiceLines' => $fatura_stoklar,
	'isSend' => false,
	'recordType' => $recordType,
	'note' => $note,
	'generalInfoModel' =>
	[
		'ettn' => null,
		'prefix' => $prefix,
		'customizationId' => null,
		'invoiceNumber' => null,
		'slipNumber' => null,
		'invoiceProfileType' => $invoiceProfileType,
		'issueDate' => $bugun,
		'type' => 1,
		'returnInvoiceNumber' => null,
		'returnInvoiceDate' => null,
		'currencyId' => 1,
		'exchangeRate' => 0,
		'despatchNumber' => null,
		'despatchType' => 0,
		'totalAmount' => null
	],
	'invoiceTotalsModel' => [
		'lineExtensionAmount' => null,
		'taxExclusiveAmount' => null,
		'taxInclusiveAmount' => null,
		'allowanceTotalAmount' => null,
		'payableAmount' => null,
		'amountInWords' => null
	],
	'paymentMeansModel' => [
		'paymentMeansCode' => 0,
		'paymentDueDate' => null,
		'paymentChannelCode' => null,
		'instructionNote' => null,
		'payeeFinancialAccountId' => null
	],
	'paymentTermsModel' => [
		'amount' => null,
		'note' => null,
		'penaltySurchargePercent' => null
	],
	'orderInfoModel' => [
		'orderNumber' => null,
		'orderDate' => null,
		'invoiceDocumentModel' => [
			'invoiceId' => null,
			'documentId' => null,
			'documentType' => null,
			'documentBase64' => null,
			'bytes' => null,
			'fileName' => null,
			'documentDate' => null,
			'isFileExist' => false,
			'documentDateInString' => "",
			'dispatcherNameSurname' => null,
			'shipmentDate' => null
		]
	],
	'archiveInfoModel' => [
		'isInternetSale' => false,
		'websiteUrl' => null,
		'shipmentSenderTcknVkn' => null,
		'shipmentSendType' => "ELEKTRONIK",
		'shipmentSenderName' => null,
		'shipmentSenderSurname' => null,
		'shipmentDate' => null,
		'hideDespatchMessage' => false,
		'subscriptionType' => null,
		'subscriptionTypeNumber' => null
	],
	'relatedDespatchList' => [ ],
	'additionalInvoiceTypeInfo' => [
		'accountingCostType' => null,
		'taxPayerCode' => null,
		'taxPayerName' => null,
		'documentNumber' => null
	],
	'buyerCustomerInfoModel' => [
		'firstName' => $cari_ad,
		'familyName' => $cari_soyad,
		'nationality' => null,
		'touristCountry' => null,
		'touristCity' => null,
		'touristDistrict' => null,
		'financialInstitutionName' => null,
		'passportNumber' => null,
		'financialAccountId' => null,
		'currencyCode' => null,
		'paymentNote' => null,
		'issueDate' => $bugun,
		'companyId' => null,
		'registrationName' => null,
		'partyName' => null,
		'buyerStreet' => null,
		'buyerBuildingName' => null,
		'buyerBuildingNumber' => null,
		'buyerDoorNumber' => null,
		'buyerSmallTown' => null,
		'buyerDistrict' => null,
		'buyerZipCode' => null,
		'buyerCity' => null,
		'buyerCountry' => null,
		'buyerPhoneNumber' => null,
		'buyerFaxNumber' => null,
		'buyerEmail' => null,
		'buyerWebSite' => null,
		'buyerTaxOffice' => null
	],
	'taxRepresentativePartyInfoModel' => [
		'representativeVkn' => null,
		'representativeAlias' => null,
		'representativeCitySubdivisionName' => null,
		'representativeCity' => null,
		'representativeCountry' => null
	]

];


// Şuan $fatura diye bir dizimiz var bu dizi json formatına çevilerim
$fatura_json = json_encode($fatura, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

// Oluşan json datasını görme için açıklama satını kaldırın
// echo $fatura_json;

// sıra geldi faturayı Turkcelle Göndermeye URL Kısma Test veya Gerçek Orama Göre Değişir

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://efaturaservice.turkcellesirket.com/v1/outboxInvoice/create",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => $fatura_json,
	CURLOPT_HTTPHEADER => $turkcell_curl_header,
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
	// Bir Hata Var ise Görelim
	echo "cURL Error #:" . $err;
} else {

	// $response Bir Json Verisidir Bu Veriyi Diziye çevirelim
  // echo $response;
	$fat_durum = json_decode($response, true |  JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	// Evet Bakalım Ne Olmuş
	echo "<pre>";
	echo print_r($fat_durum);
	echo "</pre>";


	
}
?>