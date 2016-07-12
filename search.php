<?php
/* Namazu 検索プログラム PHP */


/*
  global variables for THIS script
 */
$idxname='/home/nmzidx/namazu-users-ja';
$LF='
';

$formmethod = 'post';
$formaction = $_SERVER['PHP_SELF'];
$max = 20;
$nmax = 1;
$key = '';
$result = '';
$bresult = 1;
$nsort = 0;
$sort = '';
$nmzid = FALSE;
$hlist = FALSE;
$nhit = -3;
$idxfiles = '';
$idxkeys = '';
$idxdate = '';

$score = FALSE;
$summary = FALSE;
$subject = FALSE;
$size = FALSE;
$from = FALSE;
$date = FALSE;
$uri = FALSE;


	/* load Namazu module */
	if ( ! extension_loaded('namazu')) {
		if ( ! dl('namazu.so')) {
			echo 'error';
			exit;
		}
	}

	/* Treat HTTP input vars */
	if ( ! empty($_POST['key'])) {
		$key = $_POST['key'];
	} else if ( ! empty($_GET['key'])) {
		$key = $_GET['key'];
	}

	if ( ! empty($_POST['max'])) {
		$max = (int)$_POST['max'];
	} else if ( ! empty($_GET['max'])) {
		$max = (int)$_GET['max'];
	}

	if ( ! empty($_POST['result'])) {
		$result = $_POST['result'];
	} else if ( ! empty($_GET['result'])) {
		$result = $_GET['result'];
	}

	if ( ! empty($_POST['sort'])) {
		$sort = $_POST['sort'];
	} else if ( ! empty($_GET['sort'])) {
		$sort = $_GET['sort'];
	}

	/* chaeck input variables */
	if ($max <= 0) {
		$max = 20;
	}
	if ($max > 1000) {
		$max = 1000;
	}

	if ($max <= 10) {
		$nmax = 0;
	} else if($max <= 20) {
		$nmax = 1;
	} else if($max <= 30) {
		$nmax = 2;
	} else if($max <= 50) {
		$nmax = 3;
	} else {
		$nmax = 4;
	}

	if ($result == 'short') {
		$bresult = 0;
	}

	/* open Namazu index */
	$nmzid = nmz_open($idxname);

	/* set search parameter */
	nmz_set_sortmethod('score');
	nmz_set_sortorder('descending');
	if ($sort == 'score') {
		$nsort = 0;
	} else if ($sort == 'date:late') {
		nmz_set_sortmethod('date');
		$nsort = 1;
	} else if ($sort == 'date:early') {
		nmz_set_sortmethod('date');
		nmz_set_sortorder('ascending');
		$nsort = 2;
	} else if ( ! empty($sort)) {
		$array = explode(':', $sort);
		if (count($array) >= 3 && $array[0] == 'field') {
			if ($array[1] == 'subject') {
				$nsort = 4;
				nmz_set_sortmethod('field:subject');
			} else if ($array[1] == 'from') {
				$nsort = 6;
				nmz_set_sortmethod('field:from');
			} else if($array[1] == 'size') {
				$nsort = 8;
				nmz_set_sortmethod('field:size');
			} else {
				$nsort = 10;
				nmz_set_sortmethod('field:uri');
			}
			if ($array[2] == 'ascending') {
				nmz_set_sortorder('ascending');
				$nsort--;
			}
		}
	}

	/* search */
	if (empty($key)) {
		$nhit = -1;
		$key = '';
	} else if (strlen($key) > 200) {
		$nhit = -2;
		$key = '';
	} else {
		$nhit = -3;
		if ($nmzid) {
			$hlist = nmz_search($nmzid, $key);
			if ($hlist) {
				$nhit = nmz_num_hits($hlist);
				$n = $nhit;
				if ($n > $max) {
					$n = $max;
				}
				$score = nmz_fetch_score($hlist, $n);
				$summary = nmz_fetch_field($hlist, 'summary', $n);
				$subject = nmz_fetch_field($hlist, 'subject', $n);
				$size = nmz_fetch_field($hlist, 'size', $n);
				$from = nmz_fetch_field($hlist, 'from', $n);
				$date = nmz_fetch_field($hlist, 'date', $n);
				$uri = nmz_fetch_field($hlist, 'uri', $n);
				nmz_free_result($hlist);
			}
		}
	}

	if ($nmzid) {
		nmz_close($nmzid);
	}

	/* fetch index statistics */
	$str = $idxname . '/NMZ.status';
	$fd = fopen($str, 'r');
	if ($fd) {
		$idxfiles = trim(strchr(fgets($fd, 200), ' '));
		$idxkeys = trim(strchr(fgets($fd, 200), ' '));
		fclose($fd);
		$ts = filemtime($str);
		if ($ts) {
			$idxdate = date('Y/m/d', $ts);
		}
	}

	$selected = 'selected ';
	$key = htmlspecialchars($key);

	header("Content-Type: text/html; charset=EUC-JP");

?>
<html>
<head>
<title>Namazu the full text retrieval search system: &lt;<?php echo $key; ?>&gt;</TITLE>
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
<form method="<?php echo $formmethod; ?>" action="<?php echo $formaction; ?>">
<p>
<strong>検索式:</strong> 
<input type="text" name="key" SIZE="40" VALUE="<?php echo $key; ?>">
<input type="submit" name="submit" VALUE="Search!">
</p>
<p>
<strong>表示件数:</strong>
<select name="max">
<option <?php if($nmax <= 0){ echo $selected;} ?>value="10">10
<option <?php if($nmax == 1){ echo $selected;} ?>value="20">20
<option <?php if($nmax == 2){ echo $selected;} ?>value="30">30
<option <?php if($nmax == 3){ echo $selected;} ?>value="50">50
<option <?php if($nmax >= 4){ echo $selected;} ?>value="100">100
</select>
<strong>表示形式:</strong>
<select name="result">
<option <?php if($bresult){ echo $selected;} ?>value="normal">標準
<option <?php if(!$bresult){ echo $selected;} ?>value="short">簡潔
</select>
<strong>ソート:</strong>
<select name="sort">
<option <?php if($nsort <= 0){ echo $selected;} ?>value="score">スコア
<option <?php if($nsort == 1){ echo $selected;} ?>value="date:late">日付 (新しい順)
<option <?php if($nsort == 2){ echo $selected;} ?>value="date:early">日付 (古い順)
<option <?php if($nsort == 3){ echo $selected;} ?>value="field:subject:ascending">題名 (昇順)
<option <?php if($nsort == 4){ echo $selected;} ?>value="field:subject:descending">題名 (降順)
<option <?php if($nsort == 5){ echo $selected;} ?>value="field:from:ascending">著者 (昇順)
<option <?php if($nsort == 6){ echo $selected;} ?>value="field:from:descending">著者 (降順)
<option <?php if($nsort == 7){ echo $selected;} ?>value="field:size:ascending">サイズ (昇順)
<option <?php if($nsort == 8){ echo $selected;} ?>value="field:size:descending">サイズ (降順)
<option <?php if($nsort == 9){ echo $selected;} ?>value="field:uri:ascending">URI (昇順)
<option <?php if($nsort >= 10){ echo $selected;} ?>value="field:uri:descending">URI (降順)
</select>
</p>
</form>

<hr>
<h2>検索結果</h2>

<?php
if ($nhit >= 0) {
	echo '<p><strong>検索式にマッチする ' , $nhit , ' 個の文書が見つかりました。</strong></p>' , $LF;
	echo '<dl>' , $LF;
	$n = count($uri);
	for ($i = 0; $i < $n; $i++){
		echo '<dt>' , ($i+1) , '. <strong><a href="' , $uri[$i] , '">' ,
		     htmlspecialchars($subject[$i]) , '</a></strong> (score:' ,  $score[$i] , ')' , $LF ,
		     '<dd><strong>From</strong>:<em>' , htmlspecialchars($from[$i]) , '</em><br>' , $LF ,
		     '<strong>Date</strong>:<em>' , $date[$i] , '</em><br>' , $LF;
		if ($bresult) {
			echo '<dd>' , htmlspecialchars($summary[$i]) , $LF;
		}
		echo '<dd> size (' , $size[$i] , ' bytes)<br><br>' , $LF , $LF;
	}
	echo '</dl>' , $LF;
} else if ($nhit == -1) {
	echo '<strong>empty query</strong>';
} else if ($nhit == -2) {
	echo '<strong>too long query</strong>';
} else{
	echo '<strong>Search failure</strong>';
}

?>


<hr>
</html>
</body>
