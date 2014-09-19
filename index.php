<?php
$data = true;
header("HTTP/1.0 200 OK");
//header("Content-Length: 4");
//header("Content-Type: application/json; charset=UTF-8");
header("Content-Type: text/html; charset=utf-8");
//header("Keep-Alive: timeout=5, max=100");
//header("");
header("true");
//echo json_encode($data);
?>
<html><body>
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


	$now = time();
	$saiki = date( 'Y-m-d H:i:s', $now );

	$isi =  file_get_contents('php://input');

	var_dump(json_decode($json, true));
	$obj = json_decode($isi);



function hexdecs($hex) {
    $hex = preg_replace('/[^0-9A-Fa-f]/', '', $hex);
    $dec = hexdec($hex);
    $max = pow(2, 4 * (strlen($hex) + (strlen($hex) % 2)));
    $_dec = $max - $dec;
    return $dec > $_dec ? -$_dec : $dec;
}


function hexToStr($hex) {
    $string = '';
    for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
        $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    }
    return $string;
}


$array1 = array();
$lm1 = '00';
$lm2 = '00';
$lm = '0000';
$str_arr = '';


	
//	$pesan = $obj->{'Name'};
//	$pesan2 = $obj->{'Fields'};
	//print_r($pesan2);
//	$pesan = $pesan2[0]->{'Value'};
	$pesan =  $obj->{'Fields'}[0]->{'Value'};		// namafile
	$pesan2 = $obj->{'Fields'}[1]->{'Value'};
	$file = base64_decode($pesan2);

	//$arnama = explode(".",$pesan);
	//$pesan = $arnama[0].".str";
	$pesan = substr($pesan,0,17);

foreach (str_split($file) as $c) {
    $b = sprintf("%08b", ord($c));
    $hx1 = dechex(bindec($b));


    if (strlen($hx1) == 1) {
        $hx1 = '0' . $hx1;
    } else if (strlen($hx1) == 2) {
        $hx1 = $hx1;
    }

    $str_arr = $str_arr . $hx1;
    if ($hx1 == '0d') {
        $lm1 = $hx1;
    }

    if ($hx1 == '0a') {
        $lm2 = $hx1;
    }

    $lm = $lm1 . $lm2;

    if ($lm == '0d0a') {
        $str_arr = substr($str_arr, 0, -4);
        array_push($array1, $str_arr);

        $str_arr = '';
        $lm1 = '00';
        $lm2 = '00';
        $lm = '0000';
    }
//*/
}

	$id_modem = hexToStr($array1[0]);

	//print_r($pesan3);
	//$pesan = $pesan2[0];
	//$pesan = $obj['Fields'][0]['Value'];
	//$pesan = $pesan2[0];
	echo curPageURL();
	$con=mysqli_connect("localhost","monita","monita2011","ws");
        // Check connection
	if (mysqli_connect_errno())     {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	// cek file apa sudah ada ????
	$sql = "SELECT id,modem,file,jml FROM akses WHERE file LIKE '%$pesan%' AND modem LIKE '$id_modem'";
	$retval = mysqli_query($con,$sql);
	if(! $retval )	{
		die('Could not get data: ' . mysqli_error());
	}
	
	while($row = mysqli_fetch_array($retval, MYSQL_ASSOC))	{
		echo "hore namafile: {$row['file']} modem: {$row['modem']}<br/>";
		$fil = $row['file'];
		$mod = $row['modem'];
		$jml = $row['jml'];
		$idRow = $row['id'];
	}
	
	$rc=mysqli_num_rows($retval);
	if ($rc>0)	{
		$sql = "UPDATE akses SET jml=".($jml+1).",akses.update='$saiki' WHERE id=$idRow";
		mysqli_query($con,"$sql");
	}
	else {
		mysqli_query($con,"INSERT INTO akses (file, waktu, modem, url) VALUES ('$pesan','$saiki','$id_modem','$pesan2')");
	}


	// kirim notifikasi ke server skywave
	$sql = "select id, file, modem from akses where modem = '$id_modem' order by id desc limit 0,1;";
	$retval = mysqli_query($con,$sql);
	//$retval = mysql_query( $sql, $con );
	if(! $retval )	{
		die('Could not get data: ' . mysqli_error());
	}


	while($row = mysqli_fetch_array($retval, MYSQL_ASSOC))	{
		echo "hore namafile: {$row['file']} modem: {$row['modem']}<br/>";
		$nf = $row['file'];
		$id = $row['id'];
		$modem = $row['modem'];
	}

	$namaf = explode(".",$nf);
	$nf = $namaf[0].".str";
	$data = "\x80\x01".pack("C",strlen($nf)).$nf;

	$isidata = base64_encode($data);
	$authToken = 'Afrendy';

	mysqli_close($con);

$passwd = "STSATI2010";
$xmld = "<?xml version='1.0' encoding='utf-8'?>
	<SubmitForwardMessages xmlns='IGWS' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema'>
	<accessID>70000214</accessID>
	<password>$passwd</password>
	<messages>
	<ForwardMessage>
		<RawPayload>$isidata</RawPayload>
		<DestinationID>$id_modem</DestinationID>
		<UserMessageID>$id</UserMessageID>
	</ForwardMessage>
	</messages>
	</SubmitForwardMessages>";


$ch = curl_init('http://isatdatapro.skywave.com/GLGW/GWServices_v1/RestMessages.svc/submit_messages.xml/');
curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/xml; charset=utf-8',
        'Host: isatdatapro.skywave.com',
        'Expect: 100-continue',
        'Connection: Keep-Alive'
    ),
    CURLOPT_POSTFIELDS => $xmld
));

// Send the request
$response = curl_exec($ch);
//*/
echo "response: ".strlen($response).", isi: $response<br/>";

echo "<br>masuk database";

  //      $str = 'SGVsbG8gd29ybGQ=';
   //     echo base64_decode($str);
?>

</body></html>
