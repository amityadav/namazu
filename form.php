<?php
/* Namazu 検索プログラム PHP */
$idxname='/home/nmzidx/namazu-users-ja';
$idxfiles = '';
$idxkeys = '';
$idxdate = '';

	$str = $idxname . '/NMZ.status';
	$fd = fopen($str,'r');
	if ($fd) {
		$idxfiles = trim(strchr(fgets($fd, 200),' '));
		$idxkeys = trim(strchr(fgets($fd, 200),' '));
		fclose($fd);
		$ts = filemtime($str);
		if ($ts) {
			$idxdate = date('Y/m/d',$ts);
		}
	}

	header("Content-Type: text/html; charset=EUC-JP");

?>
<html>
<head>
<title>Namazu a full text retrieval search system</title>
</head>
<body>
<h1>Namazu による全文検索システム</h1>
<p>
現在、<?php echo $idxfiles; ?> のファイルがインデックス化され、
 <?php echo $idxkeys; ?> 個のキーワードが登録されています。
</p>
<p>
<strong>インデックスの最終更新日: <?php echo $idxdate; ?></strong>
</p>
<hr>
<form method="post" action="search.php">
<p>
<strong>検索式:</strong>
<input type="text" name="key" size="40">
<input type="submit" name="submit" value="Search!">
</p>
<p>
<strong>表示件数:</strong>
<select name="max">
<option value="10">10
<option selected value="20">20
<option value="30">30
<option value="50">50
<option value="100">100
</select>
<strong>表示形式:</strong>
<select name="result">
<option selected value="normal">標準
<option value="short">簡潔
</select>
<strong>ソート:</strong>
<select name="sort">
<option selected value="score">スコア
<option value="date:late">日付 (新しい順)
<option value="date:early">日付 (古い順)
<option value="field:subject:ascending">題名 (昇順)
<option value="field:subject:descending">題名 (降順)
<option value="field:from:ascending">著者 (昇順)
<option value="field:from:descending">著者 (降順)
<option value="field:size:ascending">サイズ (昇順)
<option value="field:size:descending">サイズ (降順)
<option value="field:uri:ascending">URI (昇順)
<option value="field:uri:descending">URI (降順)
</select>
</p>
</form>

<hr>
</body>
</html>
