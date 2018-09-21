<?php

/*contoh
	1, 0, -1)
	seq1 = 'ACAGTCGAACG';
	seq2 = 'ACCGTCCG';
*/

include('rumus_class.php');

$match = isset($_GET['match']) ? $_GET['match'] : 0;
$mismatch = isset($_GET['mis']) ? $_GET['mis'] : 0;
$gap = isset($_GET['gap']) ? $_GET['gap'] : 0;
$seq1 = isset($_GET['seq1']) ? $_GET['seq1'] : 'A';
$seq2 = isset($_GET['seq2']) ? $_GET['seq2'] : 'B';

if(strlen($seq1) > 15) $seq1 = substr($seq1, 0, 25);
if(strlen($seq2) > 15) $seq2 = substr($seq2, 0, 25);

$hasil = new rumus($match, $mismatch, $gap);
?>
<!DOCTYPE html>
<html>
<head>
    
    
    <title>Tugas Bioinformatika</title>
    <style type="text/css">
    .track { background-color: #0099CC;font-weight: bold }
    .seq { background-color: #ffbb33;}
    .data { border-collapse: collapse }
    .data td { border: 1px solid #666; text-align: center; }
    .align td { text-align: center; }
    .config { border-collapse: collapse }
    .config td { font-size:small;border: 0px solid #ccc; text-align: left; padding: 10px;}
    </style>
</head>
<body>
<center>
<h4>Needleman Wunsch Algorithm</h4>
<form method="get" >
<table class="config">
<tr>
    <td>Match Score: </td><td><input type="text" size="4" name="match"  /></td>
    <td>Mis-match Score: </td><td><input type="text" size="4" name="mis" /></td>
    <td>Gap Penalty: </td><td><input type="text" size="4" name="gap" /></td>
</tr>
<tr>
    <td>Sequence 1: </td><td colspan="6"><input type="text" name="seq1" size="15"/></td>
</tr>
<tr>
    <td>Sequence 2: </td><td colspan="6"><input type="text" name="seq2" size="15" /></td>
</tr>
<tr >
    <td colspan="6" ><center><input type="submit" name="hitung" value="Hitung !"/></center></td>
</tr>
</table>
</form>
<hr width="50%">

<?php
	if (isset($_GET["hitung"])){
?>
<table class="config">
	<tr>
		<td>Match Score:</td> <td><b><?php echo $_GET['match']; ?></b></td>
		<td>Mis Match Score:</td> <td><b><?php echo $_GET['mis']; ?></b></td>
		<td>Gap Penalty:</td> <td><b><?php echo $_GET['gap']; ?></b></td>
	</tr>
	
	<tr>
		<td>Sequence 1:</td><td colspan="3"><b><?php echo $_GET['seq1']; ?></b></td>
	</tr>
	
	<tr>
		<td>Sequence 2:</td><td colspan="3"><b><?php echo $_GET['seq2']; ?></b></td>
	</tr>
	
	
</table>
<?php
	}
?>


<?php $hasil->renderAsHTML($seq1, $seq2); ?>
</center>
</body>
</html>
