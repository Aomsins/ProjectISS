<?php
session_start();
if(isset($_GET['logout'])){    
	
	//Simple exit message 
    $logout_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-left'>". $_SESSION['name'] ."</b> has left the chat session.</span><br></div>";
    file_put_contents("log.html", $logout_message, FILE_APPEND | LOCK_EX);
	
	session_destroy();
	header("Location: chatappjava2.php"); //Redirect the user 
}
if(isset($_POST['enter'])){
    if($_POST['name'] != ""){
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
        $login_message = "<div class='msgln'><span class='left-info'>User <b class='user-name-join'>". $_SESSION['name'] ."</b> has joined the chat.</span><br></div>";
        file_put_contents("log.html", $login_message, FILE_APPEND | LOCK_EX);
    }
    else{
        echo '<span class="error">Please Type The Name!</span>';
    }
}
function loginForm(){
    echo 
    '<div id="loginform"> 
<p>WELCOME TO CHAT APP COMPLEX</p> 
<p>Please enter your name!</p> 
<form action="chatappjava2.php" method="post"> 
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
    if(!isset($_SESSION['name'])){
        loginForm();
    }
    else {
    ?>
       <div id="loginform"> 
<p>WELCOME TO CHAT APP COMPLEX</p> 
</div>
        <div id="wrapper">
            <div id="menu">
                <p class="welcome">Welcome, <b><?php echo $_SESSION['name']; ?></b></p>
                <p class="logout"><a id="exit" href="#">Leave Chat</a></p>
            </div>
            <div id="chatbox">
            <?php
            if(file_exists("log.html") && filesize("log.html") > 0){
                $contents = file_get_contents("log.html");          
                echo $contents;
            }
            ?>
            </div>
            <form name="message" action="">
                <input name="usermsg" type="text" id="usermsg" />
                <input name="sendsmsg" type="button" id="sendsmsg" value="Post To Chat" />
            </form>
        </div>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript">
            // jQuery Document 
            $(document).ready(function () {
                $("#sendsmsg").click(function () {
                  var checkmsg =ConvertMessageCheck($("#usermsg").val());
                    var clientmsg = ConvertMessage($("#usermsg").val());
                    alert(checkmsg);
                    $.post("post.php", { text: clientmsg });
                    $("#usermsg").val("");
                    return false;
                });
                function loadLog() {
                    var oldscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height before the request 
                    $.ajax({
                        url: "log.html",
                        cache: false,
                        success: function (html) {
                            $("#chatbox").html(html); //Insert chat log into the #chatbox div 
                            //Auto-scroll 
                            var newscrollHeight = $("#chatbox")[0].scrollHeight - 20; //Scroll height after the request 
                            if(newscrollHeight > oldscrollHeight){
                                $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div 
                            }	
                        }
                    });
                }
                setInterval (loadLog, 2500);
                $("#exit").click(function () {
                    var exit = confirm("Are you sure you want to end the session?");
                    if (exit == true) {
                    window.location = "chatappjava2.php?logout=true";
                    }
                });
            });
            function ConvertMessage(message)
            {
            let key = "mychatapp";
            let str = message;
     
           
            var CipherText = encryptByPlayfairCipher(str, key);
            var plainText = decryptByPlayfairCipher(CipherText, key);

                return plainText;
            }
            function ConvertMessageCheck(message)
            {
            let key = "mychatapp";
            let str = message;
     
           
            var CipherText = encryptByPlayfairCipher(str, key);
            var plainText = decryptByPlayfairCipher(CipherText, key);

                return "PlainText : " + message + " EncodedText : " + CipherText + " DecodedText : " + plainText;
            }

            
        </script>

<script>
            
            // Encrypt Part???
     
            // Function to generate the 5x5 key square
            function generateKeyTable(key, ks, keyT) {
                let i, j, k, flag = 0;
     
                // a 26 character hashmap
                // to store count of the alphabet
                let dicty = new Array(26).fill(0);
                for (i = 0; i < ks; i++) {
                    let r = key[i].charCodeAt(0) - 97;
     
                    if (key[i] != 'j') {
                        dicty[r] = 2;
                    }
     
                }
     
                dicty['j'.charCodeAt(0) - 97] = 1;
                i = 0;
                j = 0;
     
                for (k = 0; k < ks; k++) {
                    let r = key[k].charCodeAt(0) - 97;
                    if (dicty[r] == 2) {
                        dicty[r] -= 1;
                        keyT[i][j] = key[k];
                        j++;
                        if (j == 5) {
                            i++;
                            j = 0;
                        }
                    }
                }
     
                for (k = 0; k < 26; k++) {
                    if (dicty[k] == 0) {
                        keyT[i][j] = String.fromCharCode(k + 97);
                        j++;
                        if (j == 5) {
                            i++;
                            j = 0;
                        }
                    }
                }
                return keyT;
            }
     
            // Function to search for the characters of a digraph
            // in the key square and return their position
            function search(keyT, a, b, arr) {
                let i, j;
     
                if (a == 'j')
                    a = 'i';
                else if (b == 'j')
                    b = 'i';
     
                for (i = 0; i < 5; i++) {
     
                    for (j = 0; j < 5; j++) {
     
                        if (keyT[i][j] == a) {
                            arr[0] = i;
                            arr[1] = j;
                        }
                        else if (keyT[i][j] == b) {
                            arr[2] = i;
                            arr[3] = j;
                        }
                    }
                }
                return arr;
            }
     
            // Function to find the modulus with 5
            function mod5(a) {
                return (a % 5);
            }
     
            // Function to make the plain text length to be even
            function prepare(str, ptrs) {
                if (ptrs % 2 != 0) {
                    str += 'z';
                }
     
                return [str, ptrs];
            }
     
            // Function for performing the encryption
            function encrypt(str, keyT, ps) {
                let i;
                let a = new Array(4).fill(0);
                let newstr = new Array(ps);
     
                for (i = 0; i < ps; i += 2) {
                    let brr = search(keyT, str[i], str[i + 1], a);
                    let k1 = brr[0];
                    let k2 = brr[1];
                    let k3 = brr[2];
                    let k4 = brr[3];
                    if (k1 == k3) {
                        newstr[i] = keyT[k1][(k2 + 1) % 5];
                        newstr[i + 1] = keyT[k1][(k4 + 1) % 5];
                    }
                    else if (k2 == k4) {
                        newstr[i] = keyT[(k1 + 1) % 5][k2];
                        newstr[i + 1] = keyT[(k3 + 1) % 5][k2];
                    }
                    else {
                        newstr[i] = keyT[k1][k4];
                        newstr[i + 1] = keyT[k3][k2];
                    }
                }
                let res = "";
     
                for (let i = 0; i < newstr.length; i++) { res += newstr[i]; }
                return res;
            }
    
            // Function to encrypt using Playfair Cipher
            function encryptByPlayfairCipher(str, key) {
                let ps, ks;
                let keyT = new Array(5);
     
                for (let i = 0; i < 5; i++) {
                    keyT[i] = new Array(5);
                }
                str = str.trim();
                key = key.trim();
                str = str.toLowerCase();
     
                key = key.toLowerCase();
                ps = str.length;
                ks = key.length;
                [str, ps] = prepare(str, ps);
     
                let kt = generateKeyTable(key, ks, keyT);
                return encrypt(str, kt, ps);
            }
    
            // Decrypt Part
    
            function toLowerCase(plain) {
      // Convert all the characters of a string to lowercase
      return plain.toLowerCase();
    }
     
    function removeSpaces(plain) {
      // Remove all spaces in a string
      // can be extended to remove punctuation
      return plain.split(' ').join('');
    }
    
            function decrypt(str, keyT) {
    // Function to decrypt
    var ps = str.length;
    var i = 0;
    while (i < ps) {
    var a = searchDecrypt(keyT, str[i], str[i + 1]);
    if (a[0] == a[2]) {
    str = str.slice(0, i) + keyT[a[0]][mod5(a[1] - 1)] + keyT[a[0]][mod5(a[3] - 1)] + str.slice(i + 2);
    } else if (a[1] == a[3]) {
    str = str.slice(0, i) + keyT[mod5(a[0] - 1)][a[1]] + keyT[mod5(a[2] - 1)][a[1]] + str.slice(i + 2);
    } else {
    str = str.slice(0, i) + keyT[a[0]][a[3]] + keyT[a[2]][a[1]] + str.slice(i + 2);
    }
    i += 2;
    }
    return str;
    }
    
    function decryptByPlayfairCipher(str, key) {
    // Function to call decrypt
    var ks = key.length;
    key = removeSpaces(toLowerCase(key));
    str = removeSpaces(toLowerCase(str));
    var keyT = generateKeyTableDecrypt(key);
    return decrypt(str, keyT);
    }
    
    function generateKeyTableDecrypt(key) {
      // generates the 5x5 key square
      var keyT = new Array(5).fill(null).map(() => new Array(5).fill(''));
      var dicty = {};
      for (var i = 0; i < 26; i++) {
        dicty[String.fromCharCode(i + 97)] = 0;
      }
     
      for (var i = 0; i < key.length; i++) {
        if (key[i] != 'j') {
          dicty[key[i]] = 2;
        }
      }
      dicty['j'] = 1;
     
      var i = 0, j = 0, k = 0;
      while (k < key.length) {
        if (dicty[key[k]] == 2) {
          dicty[key[k]] -= 1;
          keyT[i][j] = key[k];
          j += 1;
          if (j == 5) {
            i += 1;
            j = 0;
          }
        }
        k += 1;
      }
     
      for (var k in dicty) {
        if (dicty[k] == 0) {
          keyT[i][j] = k;
          j += 1;
          if (j == 5) {
            i += 1;
            j = 0;
          }
        }
      }
     
      return keyT;
    }
    
    function searchDecrypt(keyT, a, b) {
      // Search for the characters of a digraph in the key square and return their position
      var arr = [0, 0, 0, 0];
     
      if (a == 'j') {
        a = 'i';
      } else if (b == 'j') {
        b = 'i';
      }
     
      for (var i = 0; i < 5; i++) {
        for (var j = 0; j < 5; j++) {
          if (keyT[i][j] == a) {
            arr[0] = i;
            arr[1] = j;
          } else if (keyT[i][j] == b) {
            arr[2] = i;
            arr[3] = j;
          }
        }
      }
     
      return arr;
    }
      </script>
    </body>
</html>
<?php
}
?>