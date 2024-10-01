		if (strpos($mesaj, '@') !== false) {

		preg_match('/\@(\d+)/', $mesaj, $matches);
		$mention = $matches[1];
		$mlow = $mention - 1 ;
		
		$listele = mysql_query("SELECT * FROM entry WHERE `baslikno`=$id and `statu` != 'silindi' ORDER BY `id` asc limit $mlow,$mention");
		if (mysql_num_rows($listele)>0){
		$kayit = mysql_fetch_assoc($listele);
		
		$id=$kayit["id"];
		
	}

	$mesaj = str_replace("@$mention", "#$id", $mesaj);

}
