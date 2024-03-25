<?php

$tarih = date("YmdHi");
  $gun = date("d");
  $ay = date("m");
  $yil = date("Y");
  $saat = date("H:i");
  $dakika = date("i");
  $saniye = date("s");
  $ip = getenv('REMOTE_ADDR');

$aktifTema = $_SESSION['aktifTema_S'];
$sira = $_REQUEST['sira'];
$baslik = $_REQUEST['baslik'];
$link = str_replace(" ","+",$baslik); 

if ($kullaniciAdi == "")
{
    echo "<b>bolgpt için yetkisiz kullanım..</b> ip adresiniz: $ip";
    die();
}

if (!$_SESSION['aktifTema_S']) {
  $aktifTema = "default";
}

$api_key = "xxx";
$api_url = "https://api.openai.com/v1/chat/completions";

$entryCheck = "SELECT COUNT(*) basliktaki entryler";
$entryCheckResult = mysql_query($entryCheck);
if ($entryCheckResult) {
  $rowCount = mysql_fetch_assoc($entryCheckResult)['count'];
  if ($rowCount < 2) {
    echo "<b>bolgpt için Yetersiz içerik.</b>";
    die();
  }
} else {
  echo "<b>Veritabanı hatası:</b> " . mysql_error($conn);
  die();
}


$sql = "SELECT * bolgpt entryleri";
$result = mysql_query($sql);
if (mysql_num_rows($result) > 4) { //normali 9
  echo "bolgpt'nin günlük çağırma limiti dolmuş. yarın tekrar dene.";
  die();
}

if ($lastYazar == "bolgpt") 
{
  echo "Bu başlığa bolgpt yakınlarda yazmış. tekrar çağıramazsın.";
  die();
}

if ($rowCount > 0) {
$sqlFirstEntry = "SELECT basligin ilk entrysi";
$resultFirstEntry = mysql_query($sqlFirstEntry);
if (mysql_num_rows($resultFirstEntry) > 0) {
  $row = mysql_fetch_assoc($resultFirstEntry);
  $firstEntry = $row['mesaj'];
}
}

...

$prompt = "$baslik hakkında daha önce $firstEntry, $secondEntry, $thirdEntry, $fourthEntry, $fifthEntry ve $lastEntry yorumlarından yola çıkarak ve başlıktaki ortalama üslubu taklit ederek, eğlenceli ve samimi bir dille; günceli sorgulayan, olumsuz yönleri varsa eleştirel olarak onu da belirten, tartışmaya müsait ve yeri geldiğinde sivri bir dille yeni bir yorum yazabilir misin? ayrıca $baslik neyi refere ediyor ve literatürdeki karşılığı nedir onları da bizimle paylaş. yeterince ayrıntıya sahip değilsen bile, bilgi havuzundan faydalan. yorumun 3 ya da 4 cümleyi geçmesin. yazının sonunda da yazdıklarınla ilgili olduğuna emin olduğun referans bir kavramı da (bkz: referanskavram) şeklinde belirt.";

$data = array( 
  "model" => "gpt-4-1106-preview",
  "messages" => array(
    array("role" => "user", "content" => $prompt),
  ),
  "stream" => true,
);


$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);

$response = curl_exec($ch);
$rawData = $response;

if ($response === false) {
  echo "<b>cURL Hatasý: </b>" . curl_error($ch);
} else {

  $response = mb_convert_encoding($response, 'UTF-8', 'AUTO');

  $lines = explode("data: ", $response);
  $result = "";

  foreach ($lines as $line) {
    $json_start = strpos($line, '{');
    $json_end = strrpos($line, '}');

    if ($json_start !== false && $json_end !== false) {
      $json = substr($line, $json_start, $json_end - $json_start + 1);
      $data = json_decode($json, true);

      if (isset($data["choices"][0]["delta"]["content"])) {
        $result .= $data["choices"][0]["delta"]["content"];
      }
    }
  }

  $result = trim(preg_replace('/\s+/', ' ', $result));
  
  if (!empty($result)) {
    echo "<b>chatgpt entrysi basliga girildi:</b> " . $result;

        $mesaj = str_replace("<br>","/n/s",$result);
        $mesaj = str_replace("<br />"," /n/s",$mesaj);
        $mesaj = str_replace("<","&lt;",$mesaj); 
        $mesaj = str_replace(">","&gt;",$mesaj);
        $mesaj = preg_replace("'\@([0-9]{1,9})'","<b>@\\1</b>",$mesaj);   
        $mesaj = str_replace("&#039;","'",$mesaj);  
        $mesaj = mysql_real_escape_string($mesaj);

      $sorgu = "INSERT INTO entry (xxx)";
      $sorgu .= " VALUES ('xxx')";
      mysql_query($sorgu);

      echo "<br><br><b>Mesajlar başarıyla veritabanına eklendi.</b>";

      $sorgux = "UPDATE baslik SET tarih='$tarih',gun='$gun',ay='$ay',yil='$yil' WHERE id='xxx'";
      mysql_query($sorgux); 
   } else {
    echo "<br><br><b>hata:</b> gpt api için ödenen kullanım bedeli aşıldı, Bir problem yaşandı ya da GPT API şu an çalışmıyor.<br>  <br>
 <img src=\"btc.png\" style=\"width: 25x; height: 25px;\" /> <b>btc</b>: <small>16wqo1RTmtJ8t3z5zLZBdk5aqnN7jPm41L <br></small>
 <img src=\"eth.png\" style=\"width: 25px; height: 25px;\" /> <b>eth</b>: <small>0x9d44c9be49b3e47c413319021e68fb95358a6c8e <br></small>
 <img src=\"avax.png\" style=\"width: 25px; height: 25px;\" /> <b>avax</b>: <small>X-avax14d6mqddt06ltg47c5dd76gdxwq5dcpkzc0uz49 <br></small>
 <br>" . $result;


if ($kullaniciAdi == "xxx")
{  
    echo "<br><br>Veritabanı log bilgisi: " . mysql_error() . "<A href=\"xxx">Check API Status</A>";    
}
  }
}

curl_close($ch);

if ($kullaniciAdi == "xxx")
{  
 echo "<br><br><br><small><b>---- raw data  </b> $rawData -- <b>raw data </b> <br><br></small>";
}

?>

</body>
</html>
