<?php 
require __DIR__ . "/vendor/autoload.php";
include "connection.php";
include "fpdf182/fpdf.php";

use PHPZxing\PHPZxingDecoder;

$hn = $_POST['hn'];
$dateTM = $_POST['dateTreatment'];

$hn = filter_input(INPUT_POST, 'hn', FILTER_SANITIZE_STRING);
$dateTM = filter_input(INPUT_POST, 'dateTreatment', FILTER_SANITIZE_STRING);

if ( empty($hn) || empty($dateTM) )
{
    echo "กรุณากรอกข้อมูล HN และ วันที่ทำการรักษา ให้ครบ";
    exit;
}

if (!file_exists("filePdf/$hn")) { 
    mkdir("fileImage/$hn");
    mkdir("filePdf/$hn");
}

$defaultTmPath = "filePdf/$hn/$dateTM";
if (!file_exists($defaultTmPath)) { 
    mkdir("fileImage/$hn/$dateTM");
    mkdir($defaultTmPath);
}

class PDF extends FPDF
{
    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// เก็บไฟล์ไว้ใน temp ก่อน
$fileJpeg = array();
$jpegTemp = array();



$tempPath = "tmp/";

foreach ($_REQUEST['canvasValue'] as $key => $value)
{
    list($list , $pureData) = explode(',', $value);

    $pureData = base64_decode($pureData);
    $name = generateRandomString();

    // ลดคุณภาพไฟล์เพือ่ประหยัดเนื้อที่
    $fileJpeg[] = $jpegName = "$name.jpeg";
    $im = imagecreatefromstring($pureData);

    $jpegTemp[] = $tmp = $tempPath.$jpegName;
    
    imagejpeg($im, $tmp, 80);
}

// อ่านบาร์โค้ดออกมา
$decoder = new PHPZxingDecoder();
$decoder->setJavaPath(javaFullPath);
$decodedArray = $decoder->decode($jpegTemp);
if( is_array($decodedArray) )
{
    foreach ($decodedArray as $data) 
    {
        if($data instanceof PHPZxing\ZxingImage) 
        {
            $hn = $data->getImageValue();
        }
    }
}
else
{
    if($decodedArray instanceof PHPZxing\ZxingImage) 
    {
        $hn = $decodedArray->getImageValue();
    } 
}

$pdf = new PDF("L","mm","A4");
$backupJpeg = array();
foreach ($fileJpeg as $file)
{
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    // Insert a logo in the top-left corner at 300 dpi
    // $pdf->Image($jpegName,0,0 ,-96);

    // เพิ่มรูปเข้าไปใน pdf
    $pdf->Image("tmp/".$file, 0, 0, 297, 210, "JPEG");

    $backupJpeg[] = $jpegImage = "fileImage/$hn/$dateTM/$file";

    // ก็อปรูปไปไว้ใน folder ทำเป็น backup ไว้ก่อน
    if (copy("tmp/".$file, $jpegImage) === true) { 
        unlink("tmp/".$file);
    } 
}
$pdfName = generateRandomString();
$pdfPathFile = "$defaultTmPath/$pdfName.pdf";
$pdf->Output("F", $pdfPathFile);

$sql = "INSERT INTO `pdfs` (`id`, `dateSave`, `dateTM`, `hn`, `file`, `creator`, `lastSave`, `editor`) VALUES ( NULL, NOW(), ?, ?, ?, '', NOW(), '' );";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $dateTM, $hn, $pdfPathFile);
// $v1 = $dateTM;
// $v2 = $pdfPathFile;
$stmt->execute();
$last_id = $mysqli->insert_id;
$stmt->close();

foreach ($backupJpeg as $key => $jpeg) {
    $sql = "INSERT INTO `images` (`id`, `file`, `pdfId`) VALUES (NULL, ?, ?);";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("si", $v3, $v4);
    $v3 = $jpeg;
    $v4 = $last_id;
    $stmt->execute();
    $stmt->close();
}

$_SESSION['notiMessage'] = "บันทึกข้อมูลเรียบร้อย";
header('Location: index.php');

exit;
