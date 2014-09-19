<html>
<head>
<title>Read log web</title></head>

<body>
<h2>Query hanya dibatasi sebanyak 25 saja</h2>
<?php

	$conn = mysql_connect("localhost", "monita", "monita2011");
	if(! $conn )	{
	  die('Could not connect: ' . mysql_error());
	}
	$sql = 'SELECT * FROM akses order by waktu desc limit 0,25';

	mysql_select_db('ws');
	$retval = mysql_query( $sql, $conn );
	if(! $retval )	{
	  die('Could not get data: ' . mysql_error());
	}
?>
	<table border="1"><tr><td>ID</td><td width="100px">Waktu</td><td width="100px">Update</td>
		<td>Jml</td><td>Modem</td><td>file</td><td>URL</td></tr>
<?php

	while($row = mysql_fetch_array($retval, MYSQL_ASSOC))	{
		$isi = substr($row['url'],0,60)." ...";
		$upd = $row['update'] ?: '-';
		echo "<tr><td>{$row['id']}</td><td>{$row['waktu']}</td><td>{$upd}</td><td>{$row['jml']}</td>".
			 "<td>{$row['modem']}</td><td>{$row['file']}</td><td>{$isi}</td></tr>";
	} 


?>
	</table><br/>
<?php
	echo "Fetched data successfully\n";
	mysql_close($conn);

?>	
	
</body></html>

