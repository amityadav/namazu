<?php
/* Namazu �����ץ���� PHP */
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
<h1>Namazu �ˤ����ʸ���������ƥ�</h1>
<p>
���ߡ�<?php echo $idxfiles; ?> �Υե����뤬����ǥå��������졢
 <?php echo $idxkeys; ?> �ĤΥ�����ɤ���Ͽ����Ƥ��ޤ���
</p>
<p>
<strong>����ǥå����κǽ�������: <?php echo $idxdate; ?></strong>
</p>
<hr>
<form method="post" action="search.php">
<p>
<strong>������:</strong>
<input type="text" name="key" size="40">
<input type="submit" name="submit" value="Search!">
</p>
<p>
<strong>ɽ�����:</strong>
<select name="max">
<option value="10">10
<option selected value="20">20
<option value="30">30
<option value="50">50
<option value="100">100
</select>
<strong>ɽ������:</strong>
<select name="result">
<option selected value="normal">ɸ��
<option value="short">�ʷ�
</select>
<strong>������:</strong>
<select name="sort">
<option selected value="score">������
<option value="date:late">���� (��������)
<option value="date:early">���� (�Ť���)
<option value="field:subject:ascending">��̾ (����)
<option value="field:subject:descending">��̾ (�߽�)
<option value="field:from:ascending">���� (����)
<option value="field:from:descending">���� (�߽�)
<option value="field:size:ascending">������ (����)
<option value="field:size:descending">������ (�߽�)
<option value="field:uri:ascending">URI (����)
<option value="field:uri:descending">URI (�߽�)
</select>
</p>
</form>

<hr>
</body>
</html>
