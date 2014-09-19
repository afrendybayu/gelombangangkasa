
<?php

function curPageURL()   {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on")  {
                $pageURL .= "s";
        }

        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") 
        {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } 
        else 
        {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
        return $pageURL;
}

if (isset($_SERVER["REQUEST_URI"])) {
	$arg = explode("/",$_SERVER["REQUEST_URI"]);
//	print_r($arg);
}
else {
	echo "tanpa argumen<br/>";
	exit;
}

$now = time();
$saiki = date( 'Y-m-d H:i:s', $now );

/*
$con=mysqli_connect("localhost","monita","monita2011","ws");
        // Check connection
if (mysqli_connect_errno())     {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

///mysqli_query($con,"INSERT INTO akses (file, waktu, modem, url) VALUES ('..','".$saiki."','--','".curPageURL()."')");
mysqli_close($con);
//*/

$conn = mysql_connect("localhost", "monita", "monita2011");
if(! $conn )    {
	die('Could not connect: ' . mysql_error());
}
//$sql = 'SELECT * FROM akses order by waktu desc';
//$sql = "select id, file from akses where modem = '01020105SKY662A' order by id desc limit 0,1";
$sql = "select id, file from akses where modem = '".$arg[2]."' order by id desc limit 0,1";

//echo "sql: $sql\n";

mysql_select_db('ws');
$retval = mysql_query( $sql, $conn );
if(! $retval )  {
	die('Could not get data: ' . mysql_error());
}

while($row = mysql_fetch_array($retval, MYSQL_ASSOC))	{
//	echo "id: {$row['id']}, file: {$row['file']}\n";
	$file = $row['file'];
}


$nx = explode("str",$file);
//print_r($nx);

$file = new StdClass();
//$file->Name = "namafile";
//$file->Name = "longText";
$file->Name = "text";
$file->Value = $nx[0]."str";

$foo = new StdClass();
$foo->SIN = "128";
$foo->MIN = "1";
//$foo->IsForward = "true";
$foo->Fields = array($file);

$json = json_encode($foo);
//echo strlen($json);


header("HTTP/1.0 200 OK");
header("Content-Length: ".strlen($json));
header("Content-Type: application/json; charset=UTF-8");
header("Server: ABAdhy-HTTPAPI/1.0");
header("Date: ".gmdate("D, d M Y H:i:s \G\M\T"));
header("true");

echo $json;

?>

