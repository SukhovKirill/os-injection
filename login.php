<?php

session_start();

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydb";

$conn = new mysqli($servername, $username, $password, $dbname);

$allowed_ips = array("127.0.0.1"); // Здесь перечисленны разрешенные IP-адреса

$client_ip = $_SERVER['REMOTE_ADDR']; // Получаем IP-адрес текущего пользователя

if (!in_array($client_ip, $allowed_ips)) {
    die("Доступ запрещен"); // Завершаем выполнение скрипта, если IP-адрес не в списке разрешенных
}

if($conn===false)
{
	die("connection error");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

	//Защищенный от OS-injection. Без этого кода будет не защищенным.
	$username = $conn->real_escape_string($_POST['username']);
	$password = $conn->real_escape_string($_POST['password']);
	$sql = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
	$sql->bind_param("ss", $username, $password);
	$sql->execute();
	$result = $sql->get_result();
	$row = $result->fetch_assoc();
	//

    //$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    //$result = mysqli_query($conn,$sql);
    //$row=mysqli_fetch_array($result);

    if($row["role"]=="user")
	{	

		$_SESSION["username"]=$username;

		header("location:index.php");
	}

	elseif($row["role"]=="admin")
	{

		$_SESSION["username"]=$username;
		
		header("location:admin.php");
	}

	else
	{
		echo "username or password incorrect";
	}
}

$conn->close();

?>

<form method="post" action="login.php">
    <input type="text" name="username" placeholder="Имя пользователя" required><br>
    <input type="password" name="password" placeholder="Пароль" required><br>
    <input type="submit" value="Войти">
</form>