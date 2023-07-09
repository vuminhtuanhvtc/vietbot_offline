<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title><?php echo $MYUSERNAME; ?>, Check Log, Services, CMD VietBot</title>
    <link rel="shortcut icon" href="../assets/img/VietBot128.png">
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <script src="../assets/js/jquery.min.js"></script>
  <script src="../assets/js/popper.min.js"></script>
  <script src="../assets/js/bootstrap.min.js"></script>
	<style>
body {
	background-color: #6c757d;
	}
::-webkit-scrollbar {
    width: 10px; 
}
::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
    -webkit-border-radius: 10px;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    -webkit-border-radius: 10px;
    border-radius: 10px;
    background: rgb(251, 255, 7); 
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
}
.scrollable-menu {
    height: auto;
    max-height: 200px;
    overflow-x: hidden;
}
#loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    display: none;
}
#loading-icon {
    width: 30px;
    height: 30px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
#loading-message {
	position: absolute;
    color: white;
	top: 57%;
    left: 50%;
	  transform: translate(-50%, -50%);
}
	</style>
	</head>
	<body>


<?php
//Code By: Vũ Tuyển
//Facebook: https://www.facebook.com/TWFyaW9uMDAx
include "../Configuration.php";
include('Net/SSH2.php');

// Khởi tạo biến để lưu output
$output = '';

//echo "Địa chỉ IP của server là: " . $serverIP;

// Kiểm tra xem có yêu cầu thực hiện lệnh "ls" hay "dir" hay "systemctl --user restart vietbot" hay "journalctl --user-unit vietbot.service" hay không
if (isset($_POST['reboot_power'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'sudo reboot');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ sudo reboot\n\n";
$output .=  stream_get_contents($stream_out);
$output .= "$GET_current_USER@$HostName:~$ >Lệnh Được Thực Hiện Thành Công";
}

//restart vietbot
if (isset($_POST['restart_vietbot'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'systemctl --user restart vietbot');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ systemctl --user restart vietbot\n\n";
$output .=  stream_get_contents($stream_out);
$output .= "$GET_current_USER@$HostName:~$ >Lệnh Được Thực Hiện Thành Công\n\n";
}
//Kiểm Tra Manual Run
if (isset($_POST['check_manual_run'])) {
	
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'ps aux | grep -i start.py');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ ps aux | grep -i start.py\n\n";
$output .=  stream_get_contents($stream_out);

}
//Chạy Manual Run
if (isset($_POST['start_manual_run'])) {
// Lệnh cần chạy
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'cd /home/pi/vietbot_offline/src && python3 start.py');
stream_set_blocking($stream, false); //false chặn kết quả của luồng stream
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ cd /home/pi/vietbot_offline/src && python3 start.py\n\n";
$output .=  "$GET_current_USER@$HostName:~$ Lệnh đã được thực thi, vui lòng đợi thiết bị khởi động\n\n";


}
//Dừng Manual Run
if (isset($_POST['stop_manual_run'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'pkill -f start.py');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ pkill -f start.py\n\n";
$output .=  stream_get_contents($stream_out);
}

//Kiểm Tra Dung Lượng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kiem_tra_dung_luong'])) {
	
	$message = "$GET_current_USER@$HostName:~ $ free -mh\n\n";
    $output = shell_exec('free -mh');
	$message .= $output;

}
//Kiểm Tra Bộ Nhớ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kiem_tra_bo_nho'])) {
	$message = "$GET_current_USER@$HostName:~ $ df -hm\n\n";
    $output = shell_exec('df -hm');
	$message .= $output;
}
//Check ifconfig
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_ifconfig'])) {
	$message = "$GET_current_USER@$HostName:~ $ ifconfig\n\n";
    $output = shell_exec('ifconfig');
	$message .= $output;
}
//Check thông tin cpu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_thong_tin_cpu'])) {
	$message = "$GET_current_USER@$HostName:~ $ lscpu\n\n";
    $output = shell_exec('lscpu');
	$message .= $output;
}
//Thông tin hệ điều hành
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thong_tin_he_dieu_hanh'])) {
	$message = "$GET_current_USER@$HostName:~ $ hostnamectl\n\n";
    $output = shell_exec('hostnamectl');
	$message .= $output;
}
//Kiểm tra auto run
if (isset($_POST['check_auto_run'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'ps aux | grep -i start.py');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ ps aux | grep -i start.py\n\n";
$output .=  stream_get_contents($stream_out);

}
//chạy auto run
if (isset($_POST['start_auto_run'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'systemctl --user start vietbot.service');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ systemctl --user start vietbot.service\n\n";
$output .=  stream_get_contents($stream_out);
	
	
}
//dừng auto run
if (isset($_POST['stop_auto_run'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'systemctl --user stop vietbot.service');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ systemctl --user stop vietbot.service\n\n";
$output .=  stream_get_contents($stream_out);

}
//Kích Hoạt auto run
if (isset($_POST['enable_auto_run'])) {
	
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'systemctl --user enable vietbot.service');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ systemctl --user enable vietbot.service\n\n";
$output .=  stream_get_contents($stream_out);

}
//Vô hiệu hóa auto run
if (isset($_POST['disable_auto_run'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'systemctl --user disable vietbot.service');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ systemctl --user disable vietbot.service\n\n";
$output .=  stream_get_contents($stream_out);

}
//Log theo thời gian thực
if (isset($_POST['journalctl_vietbot'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'journalctl --user-unit vietbot.service');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ journalctl --user-unit vietbot.service\n\n";
$output .=  stream_get_contents($stream_out);

}
//Log dịch vụ chạy tự động
if (isset($_POST['systemctl_vietbot'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die($E_rror_HOST);}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die($E_rror);}
$stream = ssh2_exec($connection, 'systemctl --user status vietbot.service');
stream_set_blocking($stream, true);
$stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ systemctl --user status vietbot.service\n\n";
$output .=  stream_get_contents($stream_out);
	
}
//Chmod
if (isset($_POST['set_full_quyen'])) {
$connection = ssh2_connect($serverIP, $SSH_Port);
if (!$connection) {die('Không thể kết nối tới máy chủ.');}
if (!ssh2_auth_password($connection, $SSH_TaiKhoan, $SSH_MatKhau)) {die('Đăng nhập không thành công.');}
$stream1 = ssh2_exec($connection, 'sudo chmod -R 0777 /var/www/html/');
$stream2 = ssh2_exec($connection, 'sudo chmod -R 0777 /home/pi/vietbot_offline/src/');
stream_set_blocking($stream1, true); stream_set_blocking($stream2, true);
$stream_out1 = ssh2_fetch_stream($stream1, SSH2_STREAM_STDIO); 
$stream_out2 = ssh2_fetch_stream($stream2, SSH2_STREAM_STDIO);
$output = "$GET_current_USER@$HostName:~$ sudo chmod -R 0777 /var/www/html/\n";
$output .= stream_get_contents($stream_out1); 
$output .= "$GET_current_USER@$HostName:~$ sudo chmod -R 0777 /home/pi/vietbot_offline/src/\n";
$output .= stream_get_contents($stream_out2);
$output .= "$GET_current_USER@$HostName:~$ >Lệnh Được Thực Hiện Thành Công";
}

//Command
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commandd'])) {
	$commandnd = @$_POST['commandnd'];
	$output = "$GET_current_USER@$HostName:~ $ $commandnd\n\n";
    $output .= shell_exec($commandnd);
}

?>
    <form  id="my-form"  method="post">
	<div class="row g-3 d-flex justify-content-center">
  <div class="col-auto"> 
  <input type="text" name="commandnd" class="form-control input-sm" placeholder="Nhập Lệnh Ở Đây" aria-label="Recipient's username" aria-describedby="basic-addon2">
</div> <div class="col-auto"> 
    <button class="btn btn-success" name="commandd" type="submit">Command</button>
 </div>
</div><br/><center>
<div class="btn-group">
  <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Kiểm Tra Log
  </button>
  <div class="dropdown-menu">
 <center><button  type="submit" name="systemctl_vietbot" class="btn btn-warning">Dịch Vụ Chạy Tự Động</button>
<div class="dropdown-divider"></div>   <button type="submit" name="journalctl_vietbot" class="btn btn-warning">Theo Thời Gian Thực</button>
 </center></div></div>

<div class="btn-group">
  <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Auto Run
  </button>
  <div class="dropdown-menu scrollable-menu">
 <center><button  type="submit" name="check_auto_run" class="btn btn-danger">Kiểm Tra Auto Run</button>
<div class="dropdown-divider"></div>  <button type="submit" name="stop_auto_run" class="btn btn-danger">Dừng Auto Run</button>
 <div class="dropdown-divider"></div>  <button type="submit" name="start_auto_run" class="btn btn-danger">Chạy Auto Run</button>
 <div class="dropdown-divider"></div>  <button type="submit" name="enable_auto_run" class="btn btn-danger">Kích Hoạt Auto Run</button>
 <div class="dropdown-divider"></div>  <button type="submit" name="disable_auto_run" class="btn btn-danger">Vô Hiệu Auto Run</button>
 </center></div></div>
	
<div class="btn-group">
  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Manual run
  </button>
  <div class="dropdown-menu scrollable-menu">
 <center><button  type="submit" name="check_manual_run" class="btn btn-primary">Kiểm Tra Manual Run</button>

<div class="dropdown-divider"></div>  <button type="submit" name="start_manual_run" class="btn btn-primary" disabled>Chạy Manual Run</button>
 <div class="dropdown-divider"></div>  <button type="submit" name="stop_manual_run" class="btn btn-primary" disabled>Dừng Manual Run</button>
 </center></div></div>

	
		<div class="btn-group">
  <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Chức Năng Khác
  </button>
  <div class="dropdown-menu scrollable-menu">
 <center><button  type="submit" name="kiem_tra_dung_luong" class="btn btn-success">Kiểm Tra Dung Lượng</button>
 <div class="dropdown-divider"></div>  <button type="submit" name="kiem_tra_bo_nho" class="btn btn-success">Kiểm Tra Bộ Nhớ</button>
  <div class="dropdown-divider"></div><button  type="submit" name="check_ifconfig" class="btn btn-success">Kiểm Tra Mạng</button>
 <div class="dropdown-divider"></div><button  type="submit" name="check_thong_tin_cpu" class="btn btn-success">Thông Tin CPU</button>
 <div class="dropdown-divider"></div><button  type="submit" name="thong_tin_he_dieu_hanh" class="btn btn-success">Thông Tin OS HĐH</button>
 <!-- <div class="dropdown-divider"></div>  <button type="submit" name="khoi_dong_os" class="btn btn-success">Khởi Động OS</button> -->
<!-- <div class="dropdown-divider"></div>  <button type="submit" name="tat_mach_xu_ly" class="btn btn-success">Tắt Mạch Xử Lý</button> -->
 </center>
  </div>
</div>
<div class="btn-group">
  <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Hệ Thống
  </button>
  <div class="dropdown-menu scrollable-menu">
 <center><button type="submit" name="restart_vietbot" class="btn btn-dark" title="Chỉ Khởi Động Lại Trợ Lý Ảo VietBot">Restart VietBot</button>
<div class="dropdown-divider"></div>  <button type="submit" name="reboot_power" class="btn btn-dark" title="Khởi Động Lại Toàn Bộ Hệ Thống">Reboot OS</button>
 <div class="dropdown-divider"></div>  <button type='submit' name='set_full_quyen' class='btn btn-dark' title='Cấp Quyền Cho Các File Và Thư Mục Cần Thiết'>Chmod 777</button>
 </center></div></div>
    </form>
    <div id="loading-overlay">
          <img id="loading-icon" src="../assets/img/Loading.gif" alt="Loading...">
		  <div id="loading-message">Đang Thực Thi...</div>
    </div>
    <br/><br/><textarea  style="width: 95%; height: 340px;" class="text-info form-control bg-dark" disabled rows="10" cols="50"><?php echo $output; ?></textarea>
</center>
 <script src="../assets/js/jquery-3.6.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#my-form').on('submit', function() {
        // Hiển thị biểu tượng loading
        $('#loading-overlay').show();

        // Vô hiệu hóa nút gửi
        $('#submit-btn').attr('disabled', true);
    });
});
</script>

</body>

</html>