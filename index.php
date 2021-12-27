<?php
ob_start();
session_start()
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bhallagtesenah - Get Anonymous compliments</title>

    <!-- Font Icon -->
    <link rel="stylesheet" href="YOUR_DIRECTORY/fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main css -->
    <link rel="stylesheet" href="YOUR_DIRECTORY/css/style.css">
</head>
<body>

    
<?php
$conn = new mysqli("HOSTNAME", "USERNAME", "PASSWORD", "DATABASENAME");
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->query('SET CHARACTER SET utf8');
$conn->query("SET SESSION collation_connection ='utf8_general_ci'"); 	
	
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/',$link);
$page = end($link_array);



if (!empty($_SERVER['HTTP_CLIENT_IP']))   
{
	$ip_address = $_SERVER['HTTP_CLIENT_IP'];
}
elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
{
	$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
else
{
	$ip_address = $_SERVER['REMOTE_ADDR'];
}


if($page  != "index.php" && $page != "" && $page != $_SESSION['bl_user'] ){
	$checkuser = "SELECT mid FROM user WHERE un = '$page'";
	$userresult = $conn->query($checkuser);
	if($userresult->num_rows == 1) {
	?>
	<div class="main">
        <div class="container">
            <div class="signup-content">
                <form method="POST" id="signup-form" class="signup-form">
                    <h2>Send an anonymous message to <span> <?php echo $page ; ?> </span></h2>
					<?php
						$date = date("d/m/y");
						if(isset($_POST['send'])){
							$message = trim($_POST['message']);
							if(empty($message)){
								echo "<strong style='color:red;'>Write a message, please.</strong>";
							}else{
								while($user = $userresult->fetch_assoc() ){
									$user_id = $user['mid'];
			
									$insertmssg = $conn->query("INSERT INTO messages (message, uid, date, ip) VALUES('$message', '$user_id', '$date' , '$ip_address' )");
									if($insertmssg === TRUE){
										echo "<strong style='color:green;'>Message Sended.<strong>";
									}else{
										echo "<strong style='color:red;'>Something went wrong.</strong>";										
									}
								}
							}
						}					
					?>
                    <div class="form-group">
						<textarea class="form-input" name="message" id="message" placeholder="Write a friendly message to <?php echo $page; ?>" ></textarea>
                       
                    </div>

                    <div class="form-group">
                        <input type="submit" name="send" id="send" class="form-submit submit" value="Send"/>
						 <a href="index.php" class="submit-link submit">Home</a>
                    </div>
                </form>
            </div>
        </div>	
	</div>	
	<?php
	}elseif($page == "index.php"){
		
	}elseif($page == ""){
		
	}else{
		?>
	<div class="main not_found">
        <div class="container">	
			<strong>User not found.</strong>
			<a href="index.php">Home</a>
		</div>
	</div>		
		<?php 
	}
}else{

	
if(isset($_SESSION["bl_user"])){
	
	?>
		<div class="main">
			<div class="header">
				<h3>Hey, <span><?php echo $_SESSION['bl_user'] ?></span></h3>
				<p><?php echo $_SERVER['SERVER_NAME']; ?>/index.php/<?php echo $_SESSION['bl_user'] ?></p>
			</div>
			
			<?php
				$user_name = $_SESSION["bl_user"];
				$user_id_query = $conn->query("SELECT mid FROM user WHERE un = '$user_name'");
				if($user_id_query->num_rows == 1) {


	
					if (isset($_GET['page_no']) && $_GET['page_no']!=""  && $_GET['page_no']!=0) {
						$page_no = $_GET['page_no'];
					} else {
						$page_no = 1;
					}	
					$total_records_per_page = 5;
					$offset = ($page_no-1) * $total_records_per_page;
					$previous_page = $page_no - 1;
					$next_page = $page_no + 1;
					$adjacents = "2";
					
					
					while($user_query = $user_id_query->fetch_assoc()){
						$user_iid = $user_query["mid"];
						
					// Find out how many items are in the table
					 $total_records = $conn->query("SELECT id FROM messages WHERE uid = '$user_iid'")->num_rows;
					
					$total_no_of_pages = ceil($total_records / $total_records_per_page);
					$second_last = $total_no_of_pages - 1; // total pages minus 1	
					
						$selectmsg = $conn->query("SELECT * FROM messages WHERE uid = '$user_iid' ORDER BY id DESC LIMIT $offset, $total_records_per_page");
						if($selectmsg->num_rows == 0){
							?>
						<div class="main not_found">
							<div class="container">	
								<strong>No Messages</strong>
							</div>
						</div>								
						<?php
						}else{
						?>
						<div class="all_messages">	
							<?php
							while($msg = $selectmsg->fetch_assoc()){
						?>
							<div class="single_message">
								<span class="date"><?php echo $msg['date']; ?></span>
								<p><?php echo nl2br($msg["message"]); ?></p>
							</div>
						<?php
						
							}

echo "<ul>";	
if ($total_no_of_pages <= 10){   
 for ($counter = 1; $counter <= $total_no_of_pages; $counter++){
 if ($counter == $page_no) {
 echo "<li class='active'><a>$counter</a></li>"; 
         }else{
        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                }
        }
}elseif ($total_no_of_pages > 10){

if($page_no <= 4) { 
 for ($counter = 1; $counter < 8; $counter++){ 
 if ($counter == $page_no) {
    echo "<li class='active'><a>$counter</a></li>"; 
 }else{
           echo "<li><a href='?page_no=$counter'>$counter</a></li>";
                }
}
echo "<li><a>...</a></li>";
echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
}elseif($page_no > 4 && $page_no < $total_no_of_pages - 4) { 
echo "<li><a href='?page_no=1'>1</a></li>";
echo "<li><a href='?page_no=2'>2</a></li>";
echo "<li><a>...</a></li>";
for (
     $counter = $page_no - $adjacents;
     $counter <= $page_no + $adjacents;
     $counter++
     ) { 
     if ($counter == $page_no) {
 echo "<li class='active'><a>$counter</a></li>"; 
 }else{
        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
          }                  
       }
echo "<li><a>...</a></li>";
echo "<li><a href='?page_no=$second_last'>$second_last</a></li>";
echo "<li><a href='?page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
}
	
else {
echo "<li><a href='?page_no=1'>1</a></li>";
echo "<li><a href='?page_no=2'>2</a></li>";
echo "<li><a>...</a></li>";
for (
     $counter = $total_no_of_pages - 6;
     $counter <= $total_no_of_pages;
     $counter++
     ) {
     if ($counter == $page_no) {
 echo "<li class='active'><a>$counter</a></li>"; 
 }else{
        echo "<li><a href='?page_no=$counter'>$counter</a></li>";
 }                   
     }
}
echo "</ul>";

}




						?>
</div>
<?php					
	
							
						}
					}
				
				}else{
					echo "YOUR ID IS DISABLED!!";
				}
			?>
			
		</div>
	
	<?php
	
}else{	
?>
		<div class="main">
        <div class="container">
            <div class="signup-content">
                <form method="POST" id="signup-form" class="signup-form">
                    <h2>Sign up / Sign In</h2>
                    <p class="desc">to get anonymous messages use <span>“Bhallagtesenah”</span></p> 

            		
					
<?php




    if(isset($_POST['submit'])){
       $username = strtolower($_POST['name']);
       $password = $_POST['password'];
       if(empty($username) || empty($password)) {
           echo "<strong style='color:red;'>Sob gula input de.</strong>";
       } elseif($username == "admin" || $username == "index" || $username == "index.php"  ) {
              echo "<strong>Choose an another name, please.</strong>";
       }
       else{
				$sqlcheck = $conn->query("SELECT * from user WHERE un = '$username'");
				if ($sqlcheck->num_rows > 0) {
					echo "<strong style='color:red;'>The username is already taken.!</strong>";
				 } else{
						$sql = "INSERT INTO user (un, pw, ip) VALUES ('$username', '$password', '$ip_address')";
						if ($conn->query($sql) === TRUE) {
							$_SESSION["bl_user"] =  $username;
							header("Location: index.php");

						} else {
							echo "Error: " . $sql . "<br>" . $conn->error;
						}
					}
					$conn->close();
       }
    }
	
	
   if(isset($_POST['submit2'])){
       $username2 = strtolower($_POST['name']);
       $password2 = $_POST['password'];
       if(empty($username2) || empty($password2)) {
           echo "<strong style='color:red;'>Sob gula input de 2.</strong>";
       }else{
			$logincheck = $conn->query("SELECT * from user WHERE un = '$username2' AND pw = '$password2' ");
			if ($logincheck->num_rows ==  1) {
				$sql = "UPDATE user SET ip = '$ip_address' WHERE un = '$username2'";
				if ($conn->query($sql) === TRUE) {
					$_SESSION["bl_user"] =  $username2;
					header("Location: index.php");
				}else{
					echo "<strong>Something went wrong. Try Again, please.</strong>";	
				}
			 }else{
				echo "<strong style='color:red;'>Username and Email don't match.</strong>";
			 }
            $conn->close();
       }
    }	
	
	
?>					
                    <div class="form-group">
                        <input type="text" class="form-input" name="name" id="name" placeholder="Username"/>
                    </div>
                
                    <div class="form-group">
                        <input type="text" class="form-input" name="password" id="password" placeholder="Password"/>
                        <span toggle="#password" class="zmdi zmdi-eye field-icon toggle-password"></span>
                    </div>
                    <div class="form-group">
                        <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" checked style="display:none;"/>
                        <label for="agree-term" class="label-agree-term"><span style="display:none;"><span></span></span>I agree all statements in  <a href="#" class="term-service">Terms of service</a></label>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" id="submit" class="form-submit submit" value="Sign up"/>
                         <input type="submit" name="submit2" id="submit2" class="form-submit submit" value="Sign In"/>
                    </div>
                </form>
            </div>
        </div>

    </div>
<?php } } ?>
    <!-- JS -->
    <script src="YOUR_DIRECTORY/vendor/jquery/jquery.min.js"></script>
    <script src="YOUR_DIRECTORY/js/main.js"></script>
</body>
</html>
