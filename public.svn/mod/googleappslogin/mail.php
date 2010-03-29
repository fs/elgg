<?

$mail = !empty($_POST['mail']) ? $_POST['mail'] : null;
$message = "This is a test message from spottes.thinkglobalschool.com";

// Send
if ($mail) {
	if (mail($mail, 'Test mail', $message)) {
		echo '<b style="color:green;">OK! Message sent.</b>';
	} else {
		echo '<b style="color:red;">ERROR!</b>';
	}
}

?>

<form method="post">
<input type="text" name="mail" />
<input type="submit" value="send" />
</form>

