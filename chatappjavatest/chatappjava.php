<?php
session_start();
if (isset($_GET['logout'])) {

  //Simple exit message 
  $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>" . $_SESSION['name'] . "</b> has left the chat session.</span><br></div>";
  file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);

  session_destroy();
  header("Location: chatappjava.php"); //Redirect the user 
}
if (isset($_POST['enter'])) {
  if ($_POST['name'] != "") {
    $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
    $login_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-join'>" . $_SESSION['name'] . "</b> has joined the chat.</span><br></div>";
    file_put_contents("log.html", $login_message, FILE_APPEND | LOCK_EX);
  } else {
    echo '<span class="error">Please Type The Name!</span>';
  }
}
function loginForm()
{
  echo
    '<div id="loginform"> 
<p>WELCOME TO CHAT APP COMPLEX</p> 
<p>Please enter your name!</p> 
<form action="chatappjava.php" method="post"> 
<label for="name">Name &mdash;</label> 
<input type="text" name="name" id="name" /> 
<input type="submit" name="enter" id="enter" value="Go To Chat!" /> 
</form> 
</div>';
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>My Chat Application</title>
  <meta name="description" content="My Chat Application" />
  <link rel="stylesheet" href="mystyles.css">
</head>
 

<body>
  <?php
  if (!isset($_SESSION['name'])) {
    loginForm();
  } else {
    ?>
    <div id="loginform"> 
<p>WELCOME TO CHAT APP COMPLEX</p> 
</div>
    <div id="wrapper">
      <div id="menu">
        <p class="welcome">Welcome : <b>
            <?php echo $_SESSION['name']; ?>
          </b></p>
        <p class="logout"><a id="exit" href="#">Leave Chat</a></p>
      </div>
      <div id="chatbox">
        <?php
        if (file_exists("log.html") && filesize("log.html") > 0) {
          $contents = file_get_contents("log.html");
          echo $contents;
        }
        ?>
      </div>
      <form name="message" action="">
        <input name="usermsg" type="text" id="usermsg" />
        <input name="sendsmsg" type="button" id="sendsmsg" value="Post The Chat" />
      </form>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
      // jQuery Document 
      $(document).ready(function () {
        // Function To Run the message
        $("#sendsmsg").click(function () {
          processKey();
          var encodeCipherText = EncodeText($("#usermsg").val());
          var decodeCipherText = DecodeText(encodeCipherText);
          var clientmsg = decodeCipherText;
          alert("Plain Text : " + $("#usermsg").val() + " Encoded Text : " + encodeCipherText + " Decoded Text : " + decodeCipherText);
          $.post("post.php", { text: clientmsg });
          $("#usermsg").val("");
          return false;
        });
        // Function to get the message
        function loadLog() {
          var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20; 
          $.ajax({
            url: "log.html",
            cache: false,
            success: function (html) {
              $("#chatbox").html(html); 
              //Auto-scroll 
              var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; 
              if (newscrollHeight > oldscrollHeight) {
                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); 
              }
            }
          });
        }
        setInterval(loadLog, 2000);
        $("#exit").click(function () {
          var exit = confirm("Are you sure to leave the chat?");
          if (exit == true) {
            window.location = "chatappjava.php?logout=true";
          }
        });
      });

      // Playfair Cipher Session;
      var isChet = false;
      var isEnd = false;
      var flag = false;
      var flagX = false;
      var flagAdd = false;

      function processKey() {
        var key =  "mychatapp";
        key = key.toUpperCase().replace(/\s/g, '').replace(/J/g, "I");
        var result = [];
        var temp = '';
        var alphabet = 'ABCDEFGHIKLMNOPQRSTUVWXYZ';
        for (var i = 0; i < key.length; i++) {
          if (alphabet.indexOf(key[i]) !== -1) {
            alphabet = alphabet.replace(key[i], '');
            temp += key[i];
          }
        }
        temp += alphabet;
        var result = [];
        temp = temp.split('');
        while (temp[0]) {
          result.push(temp.splice(0, 5));
        }
        return result;
      }

      // Encode The Text
      function EncodeText(messages) {
        var keyresult = processKey();
        var res = [];
        var str = messages;
        var textPhrase, separator;
        str = str.toUpperCase().replace(/\s/g, '').replace(/J/g, "I");
        if (str.length === 0) {
          textPhrase = "";
        }
        else {
          textPhrase = str[0];
        }
        var help = 0; flagAdd = false;
        for (var i = 1; i < str.length; i++) {
          if (str[i - 1] === str[i]) {
            if (str[i] === 'X') {
              separator = 'Q';
            }
            else {
              separator = 'X';
            }
            textPhrase += separator + str[i];
            help = 1;
          }
          else {
            textPhrase += str[i];
          }
          if (help === 1) {
            flagAdd = true;
          }
        }

        if (textPhrase.length % 2 !== 0) {
          if (textPhrase[textPhrase.length - 1] === 'X') {
            textPhrase += 'Q';
            isEnd = true;
            flagX = false;
          }
          else {
            textPhrase += 'X';
            isEnd = true;
            flagX = true;
          }
        }

        var t = [];
        var enCodeStr = '';
        for (var i = 0; i < textPhrase.length; i += 2) {
          var pair1 = textPhrase[i];
          var pair2 = textPhrase[i + 1];
          var p1i, p1j, p2i, p2j;
          for (var stroka = 0; stroka < keyresult.length; stroka++) {
            for (var stolbec = 0; stolbec < keyresult[stroka].length; stolbec++) {
              if (keyresult[stroka][stolbec] == pair1) {
                p1i = stroka;
                p1j = stolbec;
              }
              if (keyresult[stroka][stolbec] == pair2) {
                p2i = stroka;
                p2j = stolbec;
              }
            }
          }
          var coord1 = '', coord2 = '';

          if (p1i === p2i) {
            if (p1j === 4) {
              coord1 = keyresult[p1i][0];
            }
            else {
              coord1 = keyresult[p1i][p1j + 1];
            }
            if (p2j === 4) {
              coord2 = keyresult[p2i][0];
            }
            else {
              coord2 = keyresult[p2i][p2j + 1]
            }
          }
          if (p1j === p2j) {
            if (p1i === 4) {
              coord1 = keyresult[0][p1j];
            }
            else {
              coord1 = keyresult[p1i + 1][p1j];
            }
            if (p2i === 4) {
              coord2 = keyresult[0][p2j];
            }
            else {
              coord2 = keyresult[p2i + 1][p2j]
            }
          }
          if (p1i !== p2i && p1j !== p2j) {
            coord1 = keyresult[p1i][p2j];
            coord2 = keyresult[p2i][p1j];
          }
          enCodeStr = enCodeStr + coord1 + coord2;
        }
        return enCodeStr;
      }

      // Decode The Text
      function DecodeText(messages) {
        var deCodeStr = '';
        var text = '';
        var text1 = messages;

        var keyresult = processKey();
        for (var i = 0; i < text1.length; i += 2) {
          var pair1 = text1[i];
          var pair2 = text1[i + 1];
          var p1i, p1j, p2i, p2j;
          for (var stroka = 0; stroka < keyresult.length; stroka++) {
            for (var stolbec = 0; stolbec < keyresult[stroka].length; stolbec++) {
              if (keyresult[stroka][stolbec] == pair1) {
                p1i = stroka;
                p1j = stolbec;
              }
              if (keyresult[stroka][stolbec] == pair2) {
                p2i = stroka;
                p2j = stolbec;
              }
            }
          }
          var coord1 = '', coord2 = '';

          if (p1i === p2i) {
            if (p1j === 0) {
              coord1 = keyresult[p1i][4];
            }
            else {
              coord1 = keyresult[p1i][p1j - 1];
            }
            if (p2j === 0) {
              coord2 = keyresult[p2i][4];
            }
            else {
              coord2 = keyresult[p2i][p2j - 1]
            }
          }
          if (p1j === p2j) {
            if (p1i === 0) {
              coord1 = keyresult[4][p1j]
            }
            else {
              coord1 = keyresult[p1i - 1][p1j];
            }
            if (p2i === 0) {
              coord2 = keyresult[4][p2j];
            }
            else {
              coord2 = keyresult[p2i - 1][p2j]
            }
          }
          if (p1i !== p2i && p1j !== p2j) {
            coord1 = keyresult[p1i][p2j];
            coord2 = keyresult[p2i][p1j];
          }
          text = text + coord1 + coord2;
        }
        text = text.split('');

        for (var i = 0; i < text.length; i++) {
          var count;
          if (flagAdd) {
            if (text[i] === text[i + 2] && (text[i + 1] === 'X' || text[i + 1] === 'Q')) {
              count = i + 1;
              text.splice(count, 1);
            }
          }
          else if (flagAdd && isEnd && (flagX || !flagX)) {
            if (text[i - 2] === text[i] && (text[i - 1] === 'X' || text[i - 1] === 'Q'))
              count = i + 1;
            text.splice(count, 1);
          }
          else if (!flagAdd) {
            break;
          }
        }
        if (flagX) {
          text.pop();
        }
        if (isEnd && !flagX) {
          text.pop();
        }
        text = text.join('');
        return text;
      }

    </script>
  </body>
  </html>
  <?php
  }
  ?>