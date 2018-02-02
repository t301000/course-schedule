<?php
require_once '../bootstrap.php';

if (!isset($_SESSION[USER_SESSION_KEY]['name']) || !isset($_SESSION[USER_SESSION_KEY]['filename'])) {
    header('Location: ./index.php');
    exit();
}

$file = FILES_DIR_PATH . '/' . $_SESSION[USER_SESSION_KEY]['filename'];
$output_filename = $_SESSION[USER_SESSION_KEY]['name'] . "老師({$year}學年度{$section}學期).pdf";
if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'. $output_filename .'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit();
}

$_SESSION['error'] = '找不到檔案，請聯絡資訊組。';
header('Location: ./index.php');
