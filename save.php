<?php 
require __DIR__ . "/vendor/autoload.php";
use PHPZxing\PHPZxingDecoder;

include "fpdf182/fpdf.php";

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

function dump($txt)
{
    echo "<pre>";
    var_dump($txt);
    echo "</pre>";
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++)
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// เก็บไฟล์ไว้ใน temp ก่อน
$fileList = array();
$fileJpeg = array();
$pdf = new PDF("L","mm","A4");

foreach ($_REQUEST['canvasValue'] as $key => $value)
{
    list($list , $pureData) = explode(',', $value);

    $pureData = base64_decode($pureData);
    $name = generateRandomString();

    // $fileList[] = "tmp/$name.png";
    // file_put_contents("tmp/$name.png", $pureData);


    $fileJpeg[] = $jpegName = "tmp/$name.jpeg";
    $im = imagecreatefromstring($pureData);
    imagejpeg($im, "tmp/$name.jpeg",80);

    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    // Insert a logo in the top-left corner at 300 dpi
    // $pdf->Image($jpegName,0,0 ,-96);

    $pdf->Image($jpegName,0,0,297,210,"JPEG");

}

$pdfName = generateRandomString();

$pdf->Output("F", "tmp/$pdfName.pdf");

// อ่านบาร์โค้ดออกมา
$config = array(
    'try_harder' => true,
    'multiple_bar_codes' => true
);
$decoder = new PHPZxingDecoder();
$decoder->setJavaPath("D:/DEVELOPMENT/jdk8u275-full/bin/java.exe");
$decodedArray = $decoder->decode($fileJpeg);
if( is_array($decodedArray) )
{
    foreach ($decodedArray as $data) 
    {
        if($data instanceof PHPZxing\ZxingImage) 
        {
            dump($data->getImageValue());
        } 
        // else 
        // {
        //     echo "Bar Code cannot be read<br>";
        // }
    }
}
else
{
    if($decodedArray instanceof PHPZxing\ZxingImage) 
    {
        dump($decodedArray->getImageValue());
    } 
    // else 
    // {
    //     echo "Bar Code cannot be read<br>";
    // }
}


/**
 * @todo
 * [] ลดคุณภาพของรูปลงก่อนบันทึกเข้าไปในฐานข้อมูลเพื่อลดขนาดไฟล์
 * [] รวมรูปเป็นไฟล์ pdf 
 */
// foreach ($fileList as $file) {
//     unlink($file);
// }


// อ่านเสร็จลบไฟล์
foreach ($fileJpeg as $file)
{
    // unlink($file);
}

exit;
