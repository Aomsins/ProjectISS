<?php
session_start();
if(isset($_SESSION['name'])){
    $text = $_POST['text'];
	
	$text_message = "<div class='msgln'><b class='user-name'>".$_SESSION['name']."</b> ".stripslashes(htmlspecialchars($text))." : <span class='chat-time'>".date("d/m/Y H:i:s")."</span> <br></div>";
    file_put_contents("log.html", $text_message, FILE_APPEND | LOCK_EX);
}
?>