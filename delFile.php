<?php 
session_start();
require_once 'connection.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id!==false)
{
    $sql = "UPDATE `pdfs` SET `status` = 0 WHERE `id` = '$id'";
    if ($mysqli->query($sql))
    {
        $msg = "แก้ไขข้อมูลเรียบร้อย";
    }
    else
    {
        $msg = $mysqli->error;
    }
    $_SESSION['notiMessage'] = $msg;
    header("Location: pageManage.php");
}
else
{
    echo "Invalid value";
}