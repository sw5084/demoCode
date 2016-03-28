<?PHP
if (!isset($_POST["hideform"])){
	$hideform = false;
} else {
	$hideform = $_POST["hideform"];
}

$default = "testuser@example.com";


function loginForm ($default){
	// return form body
	$a = <<< HTML
	<head>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js">
	</script>
	<script type="text/javascript">
	$("document").ready(function(){
		$("#login").submit(function(e){
			$.ajax({
				url: 'login.php',
				type: 'post',
				data: $("#login").serialize()+"&hideform=true",
				dataType: 'json',
				success: function(data){
					if (data.status == 0){
						alert(data.message);
					} else {
						alert(data.message);
					}
				}

			});
		 	e.preventDefault();
		})
	})
	</script>
	<style>
	form{
		background-color: lightblue;
		border-radius:10px;
		width:430px;
	}
	div.bodydiv {
		position: fixed;
		top:50%;
		left:50%;
		transform: translate(-50%, -50%);
	}
	body{
		background-color: yellow;
	}
	p {
		text-align: center;
	}
	</style>
	</head>
	<body>
	<div class="bodydiv">
	
	<form id="login" method="POST" action="#">
	<fieldset>
	Username:<br/><input type="text" id="username" name="username" size=50 value="$default"><br/>
	Password:<br/><input type="password" id="password" name="password" size=50><br/>
	<input id="button" type="submit" name="submit" value="Login">
	</form>
	</fieldset>
	
HTML;
	return $a;
} 

function endBody(){
	return <<< HTML
	</div>
	</body>
HTML;
}

function validate($username, $password){
	// varify the username and password since no SQL connected
	// PDO code
	//$deConnect = new PDO('mysql:dname=db1;host=localhost;charset=utf8','user', 'pass');
	//$deConnect->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	//$deConnect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	// MySQL code
	$servername = "localhost";
	$dbuser = "dbuser";
	$dbpass = "dbpass";
	$database = "dbname";

	$conn = new mysqli($servername, $dbuser, $dbpass, $database);
	if($conn->connect_error){
		die("Connection Failed:".$conn->connect_error);
	}
	$stmt = $conn->prepare("SELECT * from userinfo where username = ?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$stmt->bind_result($id, $uname, $upass, $salt);
	$stmt->fetch();
	$stmt->close();
	$conn->close();
	if ($uname){
		// username exists, check for password match
		// get the salt and hash password to match db
		if (hash('sha256', $password . $salt) == ($upass)){
			// the hash result match db 
			return 1;
		} else {
			// does not match
			return 2;
		}
	} else {
		// username does not exist
		return 3; // login fail
	}
	// should have only one line or none
}

if (!$hideform){
	echo loginForm($default);
	
	echo endBody();
} else {
	if(isset($_POST['username']) && isset($_POST['password'])){
		if (($_POST['username']) && (($_POST['password']))){
			if (validate($_POST['username'], $_POST['password']) == 1){
				$json['status'] = 1;
				$json['message'] = "Login Successful!";
				echo json_encode($json);
			} elseif (validate($_POST['username'], $_POST['password']) == 2) {
				$json['status'] = 0;
				$json['message'] = "Wrong Username or Password. Please try again.";
				echo json_encode($json);
			} else {
				$json['status'] = 0;
				$json['message'] = "Wrong Username or Password. Please try again.";
				echo json_encode($json);
			}
		} else {
			$json['status'] = 0;
			$json['message'] = "Please enter Username and Password.";
			echo json_encode($json);
		}
	}
}
?>