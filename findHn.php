<?php 

require __DIR__ . "/vendor/autoload.php";
include "connection.php";
include "fpdf182/fpdf.php";

use PHPZxing\PHPZxingDecoder;

$name = generateRandomString();
$dataIMG = $_REQUEST['dataIMG'];

list($list , $pureData) = explode(',', $dataIMG);

$im = imagecreatefromstring(base64_decode($pureData));

$tmpName = "tmp/$name.jpeg";
$testIMG = imagejpeg($im, $tmpName, 80);

$resJSON = array('resStatus' => false);

// อ่านบาร์โค้ดออกมา
$decoder = new PHPZxingDecoder();
$decoder->setJavaPath($javaFullPath);
$decodedArray = $decoder->decode($tmpName);

if($decodedArray instanceof PHPZxing\ZxingImage) 
{
    $hn = $decodedArray->getImageValue();
    $resJSON = array('resStatus' => true, 'hn' => $hn);
} 

unlink($tmpName);

header('Content-Type: application/json');
echo json_encode($resJSON);
exit;