<?
if($_REQUEST['author'] == 'Авторизоваться')
	include('onsubmit.php');
if($_REQUEST['user'] == 'exit'){
	unset($_SESSION['user']);
	$no_user_autorise = 'Y';
	echo "<script>document.location.href = '../';</script>";
}
include('view.php');
?>