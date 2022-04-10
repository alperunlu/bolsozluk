<?

//ROZET SİSTEMİ
/*
1 imececi: compilation başlıklarına entry girmiş
10 gece tayfası: gece entry girenler
100 ebe: en babalarda entrysi olan
1000 respectful: 100'den fazla artı vermiş

10000 9 canlı: hiç çaylaklanmamış
100000 sevilen: 2000'den fazla artı almış
1000000 şafak tayfası: sabaha karşı entry giren
10000000 temiz: hukuki sebeplerle hiç entrysi silinmemiş

100000000 bol yazar: 1000'den fazla entry girmiş
1000000000 sol frame canavarı: 100'den fazla başlık açan
10000000000 rapstar: 100'den fazla takipçisi olan yazar
100000000000 argeci: sözlükle ilgili isteklere katkı vermiş
*/

$rozet = "000000000000";

$sorguimece = "xxx";
$sor = mysql_query($sorguimece);
$kac = mysql_num_rows($sor);
if ($kac > 3) $rozet = $rozet + 1;

$sorguimece = "xxx";
$sor = mysql_query($sorguimece);
$kac = mysql_num_rows($sor);
if ($kac > 100) $rozet = $rozet + 10;


$sorguimece = "xxx";
$sor = mysql_query($sorguimece);
$kac = mysql_num_rows($sor);
if ($kac > 2000) $rozet = $rozet + 100000;


$sorguimece = "xxx";
$sor = mysql_query($sorguimece);
$kac = mysql_num_rows($sor);
if ($kac > 1000) $rozet = $rozet + 100000000;

$sorguimece = "xxx";
$sor = mysql_query($sorguimece);
$kac = mysql_num_rows($sor);
if ($kac > 100) $rozet = $rozet + 1000000000;

$sorguimece = "xxx";
$sor = mysql_query($sorguimece);
$kac = mysql_num_rows($sor);
if ($kac > 5) $rozet = $rozet + 10000000000;


$sorgurozet = "xxx";
mysql_query($sorgurozet);


//ROZET SİSTEMİ SON
?>