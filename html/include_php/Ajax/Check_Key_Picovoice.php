<?php
include "../../Configuration.php";
include("../../assets/lib_php/Net/SSH2.php");
// Nhận key từ tham số URL
$accessKey = $_GET['key'];

// Mã hóa key trước khi truyền vào URL
$accessKeyy = str_replace(' ', '+', $accessKey);
//echo $accessKeyy;
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {
    die("Không thể kết nối đến máy chủ SSH.");
}

if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {
    die("Xác thực SSH không thành công.");
}

// Sử dụng $encodedKey thay vì $accessKey trong lệnh SSH
$stream = ssh2_exec($connection, "python3 $DuognDanUI_HTML/include_php/Ajax/Check_key_Picovoice.py $accessKeyy");

if (!$stream) {
    die("Không thể khởi tạo luồng SSH.");
}

stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output =  stream_get_contents($stream_out);

echo $output;
?>
