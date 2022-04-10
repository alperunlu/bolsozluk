<?php

//ROZET GÖSTERME
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

$sor = mysql_fetch_array(mysql_query("xxx"));
$rozet= $sor["rozet"];

$imece = 0;
$gececi = 0;
$sevilen = 0;
$bolyazar = 0;
$solfc = 0;
$argeci = 0;

if ($rozet>=1) $imece = substr($rozet, -1, 1); 
if ($rozet>=10) $gececi = substr($rozet, -2, 1);
if ($rozet>=100000) $sevilen = substr($rozet, -6, 1); 
if ($rozet>=100000000) $bolyazar = substr($rozet, -9, 1); 
if ($rozet>=1000000000) $solfc = substr($rozet, -10, 1); 
if ($rozet>=10000000000) $argeci = substr($rozet, -11, 1);
?>



  <table>
  <thead>
    <tr>
      <th><? if($verified=="1"){ echo "<img src=\"https://cdn2.iconfinder.com/data/icons/essentials-volume-i/128/verified-gold-512.png\" title=\"onaylanmış hesap\" width=32 height=32> <font size=1>";}?> 
        <? if($kimse){ echo "<a href=\"sozluk.php?process=word&q=$kimdirbu\" title=\"$kimdirbu\" target=main><font size=1>$kimdirbu </A> -  $profilinfo";}else{echo "böyle biri yok";die;exit;}?>
        <? if($verified=="1"){ echo "<img src=\"https://cdn2.iconfinder.com/data/icons/essentials-volume-i/128/verified-gold-512.png\" title=\"onaylanmış hesap\" width=32 height=32> <font size=1>";}?>
<?if (($rozet!="0") and ($rozet!="000000000000")) {?>

<br><br><? if($imece=="1"){ echo "<img src=\"http://www.bolsozluk.com/bolrozet/imececi.png\" title=\"imececi\" width=32 height=32> <font size=1>";}
if($gececi=="1"){ echo "<img src=\"http://www.bolsozluk.com/bolrozet/gececi.png\" title=\"gece tayfası\" width=32 height=32> <font size=1>";}
if($sevilen=="1"){ echo "<img src=\"http://www.bolsozluk.com/bolrozet/sevilen.png\" title=\"sevilen\" width=32 height=32> <font size=1>";}
if($bolyazar=="1"){ echo "<img src=\"http://www.bolsozluk.com/bolrozet/bolyazar.png\" title=\"bol yazar\" width=32 height=32> <font size=1>";}
if($solfc=="1"){ echo "<img src=\"http://www.bolsozluk.com/bolrozet/solfc.png\" title=\"sol frame canavarı\" width=32 height=32> <font size=1>";}
if($argeci=="1"){ echo "<img src=\"http://www.bolsozluk.com/bolrozet/argeci.png\" title=\"argeci\" width=32 height=32> <font size=1>";}
}

/*
<?php
$rozet = 11100100011;

$imece = 0;
$gececi = 0;
$sevilen = 0;
$bolyazar = 0;
$solfc = 0;
$argeci = 0;

if ($rozet>=1) $imece = substr($rozet, -1, 1); 
if ($rozet>=10) $gececi = substr($rozet, -2, 1);
if ($rozet>=100000) $sevilen = substr($rozet, -6, 1); 
if ($rozet>=100000000) $bolyazar = substr($rozet, -9, 1); 
if ($rozet>=1000000000) $solfc = substr($rozet, -10, 1); 
if ($rozet>=10000000000) $argeci = substr($rozet, -11, 1);

echo "<br>imece: ";
echo $imece;
echo "<br> gececi: ";
echo $gececi;
echo "<br> sevilen: ";
echo $sevilen;
echo "<br> bolyazar: ";
echo $bolyazar;
echo "<br> solfc: ";
echo $solfc;
echo "<br> argeci: ";
echo $argeci;

?>
*/

?>
