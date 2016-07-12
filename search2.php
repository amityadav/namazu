<?php
/* Namazu �����ץ���� PHP */


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
			}
		}
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
<h1>Namazu �ˤ����ʸ���������ƥ�</h1>
<p>
���ߡ�<?php echo $idxfiles; ?> �Υե����뤬����ǥå��������졢
 <?php echo $idxkeys; ?> �ĤΥ�����ɤ���Ͽ����Ƥ��ޤ���
</p>
<p>
<strong>����ǥå����κǽ�������: <?php echo $idxdate; ?></strong>
</p>

<hr>
<form method="<?php echo $formmethod; ?>" action="<?php echo $formaction; ?>">
<p>
<strong>������:</strong> 
<input type="text" name="key" SIZE="40" VALUE="<?php echo $key; ?>">
<input type="submit" name="submit" VALUE="Search!">
</p>
<p>
<strong>ɽ�����:</strong>
<select name="max">
<option <?php if($nmax <= 0){ echo $selected;} ?>value="10">10
<option <?php if($nmax == 1){ echo $selected;} ?>value="20">20
<option <?php if($nmax == 2){ echo $selected;} ?>value="30">30
<option <?php if($nmax == 3){ echo $selected;} ?>value="50">50
<option <?php if($nmax >= 4){ echo $selected;} ?>value="100">100
</select>
<strong>ɽ������:</strong>
<select name="result">
<option <?php if($bresult){ echo $selected;} ?>value="normal">ɸ��
<option <?php if(!$bresult){ echo $selected;} ?>value="short">�ʷ�
</select>
<strong>������:</strong>
<select name="sort">
<option <?php if($nsort == 0){ echo $selected;} ?>value="score">������
<option <?php if($nsort == 1){ echo $selected;} ?>value="date:late">���� (��������)
<option <?php if($nsort == 2){ echo $selected;} ?>value="date:early">���� (�Ť���)
<option <?php if($nsort == 3){ echo $selected;} ?>value="field:subject:ascending">��̾ (����)
<option <?php if($nsort == 4){ echo $selected;} ?>value="field:subject:descending">��̾ (�߽�)
<option <?php if($nsort == 5){ echo $selected;} ?>value="field:from:ascending">���� (����)
<option <?php if($nsort == 6){ echo $selected;} ?>value="field:from:descending">���� (�߽�)
<option <?php if($nsort == 7){ echo $selected;} ?>value="field:size:ascending">������ (����)
<option <?php if($nsort == 8){ echo $selected;} ?>value="field:size:descending">������ (�߽�)
<option <?php if($nsort == 9){ echo $selected;} ?>value="field:uri:ascending">URI (����)
<option <?php if($nsort == 10){ echo $selected;} ?>value="field:uri:descending">URI (�߽�)
</select>
</p>
</form>

<hr>
<h2>�������</h2>
<?php
if ($nhit >= 0) {
	echo '<p><strong>�������˥ޥå����� ' , $nhit , ' �Ĥ�ʸ�񤬸��Ĥ���ޤ�����</strong></p>' , $LF;
	$n = $nhit;
	if ($n > $max) {
		$n = $max;
	}
	echo '<dl>' , $LF;
	for ($i = 0; $i < $n; $i++){
		echo '<dt>' , ($i+1) , '. <strong><a href="' , nmz_result_field($hlist,$i,'uri') , '">' ,
		        htmlspecialchars(nmz_result_field($hlist,$i,'subject')) ,
		       '</a></strong> (score:' , nmz_result_score($hlist,$i) , ')' , $LF ,
		     '<dd><strong>From</strong>:<em>' ,
		        htmlspecialchars(nmz_result_field($hlist,$i,'from')) , '</em><br>' , $LF ,
		     '<strong>Date</strong>:<em>' , nmz_result_field($hlist,$i,'date') , '</em><br>' , $LF;
		if ($bresult) {
			echo '<dd>' , htmlspecialchars(nmz_result_field($hlist,$i,'summary')) , $LF;
		}
		echo '<dd> size (' , nmz_result_field($hlist,$i,'size') , ' bytes)<br><br>' , $LF , $LF;
	}
	echo '</dl>' , $LF;
} else if ($nhit == -1) {
	echo '<strong>empty query</strong>';
} else if ($nhit == -2) {
	echo '<strong>too long query</strong>';
} else {
	echo '<strong>Search failure</strong>';
}

if ( ! empty($hlist)) {
	nmz_free_result($hlist);
}
if ( ! empty($nmzid)) {
	nmz_close($nmzid);
}
?>


<hr>
</html>
</body>
