<?php 
//start session
session_start();

// define variables and set to empty values
$name = $email = $password = $logout = "";

$logoutForm = "none";
$registerForm = "none";
$loginForm="none";
$logout = "";
$loginBtn="";
$registerBtn="";
$err="";
if ($_SERVER["REQUEST_METHOD"] == "POST") { //Determines whether request is from register/login form or from register/login/logout buttons
  
    //for username
    if(!preg_match('/[^a-zA-z0-9]/',$_POST["name"])) { //checks if there is non string or non digit character in name
        $name = test_input($_POST["name"]);
    }else {
        $err = 'Username should be alphabets and numbers only'; //generates error message
    }

       //for email
     if (isset($_POST["email"])){
    if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $email = test_input($_POST["email"]);
    }else {
        $err = 'A Valid Email Address is Required'; //generates error message
    }}
 
    //for password
   if( strlen($_POST["password"]) >= 8) {//checks length of password
    $password = test_input($_POST["password"]);
   }else {
       $err = 'password should be greater than 8 characters'; //generates error message
    
   }

   //Determines whether input is coming from the registerForm and also check if an error is in '$err' variable
   if(isset($_POST["registerForm"]) ) {
       if (empty($err)){
        $registerForm = "block";
        signUp($name, $password, $email);
       }else {
        $registerForm = "block";
      }
    
   }

   //Determines whether input is coming from the loginForm and also check if an error is in '$err' variable
   if(isset($_POST["loginForm"]) )
   {
    if (empty($err)) {
        $loginForm ="block";
        login($name,$password);
    }else {
        $loginForm ="block";
    }  
   } 
}


//to render register form or login form or to logout a user
if ($_SERVER["REQUEST_METHOD"] == "GET") {
  
    if (isset( $_REQUEST["register"])) {//renders register form and hides login form
        $registerForm = "block";
        $loginForm="none";
    }

    if (isset( $_REQUEST["login"])) {//renders login form and hides register form
        $registerForm = "none";
        $loginForm="block";
    }

    if (isset( $_REQUEST["logout"])) { //hides both register and login form
        $registerForm = "none";
        $loginForm="none";
        logout($_REQUEST["logout"]); //call to logout function
    }
}

//to validate input from form
function test_input($data) {
    global $err;
    if (!empty($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    else {
        $err = 'Do not leave any field Empty'; //generates error message
    }
}

//checks if user exists
function checker($username) {
    $arr = $_SESSION['users'];
    if(array_key_exists($username, $arr)) {
        return true;
      }else {
       return false;
      }
};

//to login a user
function login($username, $password) {
    global $logoutForm, $loginBtn, $registerBtn, $logout,$err, $loginForm;
     //Check if the session users exists.
     if(!isset($_SESSION['users'])){
        //If it doesn't, create an empty array.
        $_SESSION['users'] = array();
    }
    if(checker($username) ) 
    {
        if (($_SESSION['users'][$username]['password']===$password)){//checks if password exists for user
            echo "Login successful";
            $logoutForm = "block";
            $loginBtn = "none";
            $registerBtn = "none";
            $logout = $username;
            $loginForm = "none";
        }else{
            $err = "wrong password";//generates error message

        } 
    }else{
        $err = $username ." does not exist"; //generates error message
    }
}


//to logout a user
function logout($username) {
        echo  $username." Logout Successful";
};

//to register a user
function signUp($username, $password, $email) {
global $logoutForm, $loginBtn, $registerBtn,$err,$registerForm;
        //Check if the session users exists.
        if(!isset($_SESSION['users'])){
            //If it doesn't, create an empty array.
            $_SESSION['users'] = array();
        }

        if (isset($_SESSION['users'][$username])) {
           $err = 'username already exists'; //generates error message
            
        }else {
            if(isset($_SESSION['users'])) {
                if(array_push_assoc($username,$email ,$password)) {
                    echo "Registration Successful";
                    $logoutForm = "none";
                    $loginBtn = "block";
                    $registerBtn = "none";
                    $registerForm = "none";
                };
            }    
        }
        
};

//stores value in the session array
function array_push_assoc( $username, $email, $password){
        $_SESSION['users'][$username] = array('email'=>$email, 'password'=>$password); //multi-dimensional array is stored in Session 
        return true;
 }

?>
<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:<?php echo $registerBtn ?>;width:40%; margin-top:2%;margin-right:30%; margin-left:30%;">
<input type="hidden" name="register" value="true">
<input type="submit" value="REGISTER">
</form>
<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:<?php echo $loginBtn ?>;width:40%; margin-top:2%;margin-right:30%; margin-left:30%;">
<input type="hidden" name="login" value="true">
<input type="submit" value="LOGIN">
</form>
<form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:<?php echo $logoutForm ?>;width:40%; margin-top:2%;margin-right:30%; margin-left:30%;">
<input type="hidden" name="logout" value="<?php echo $logout ?>">
<input type="submit" value="LOG OUT">
</form>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style="display:<?php echo $registerForm ?>;border:5px solid black;padding:2%;width:40%; margin-top:5%;margin-right:30%; margin-left:30%;">
<h1 style="margin-left: 40%;margin-right:40%;margin-bottom:8%">Register</h1>
 <div>
 <label for="name" style="margin-right: 3%;">Username</label>
 <input type="text" name="name" style="width:80%; border:0px">
 <hr style="width:80%">
 </div>
 <div>
 <label for="email" style="margin-right: 3%;">Email</label>
 <input type="email" name="email" style="width:80%;border:0px">
 <hr  style="width:80%">
 </div>
 <div>
 <label for="password" style="margin-right: 3%;">Password</label>
 <input type="password" name="password" style="width:80%;border:0px">
 <hr  style="width:80%">
 </div>
 <input type="hidden" name="registerForm" value="true">
 <h3 style="color:red"><?php echo $err ?></h3>
 <input type="submit" value="Submit">
</form>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" style= "display:<?php echo $loginForm ?>;border:5px solid black;padding:2%;width:40%; margin-top:5%;margin-right:30%; margin-left:30%;">
<h1 style="margin-left: 40%;margin-right:40%;margin-bottom:8%">Login</h1>
 <div>
 <label for="name" style="margin-right: 3%;">Username</label>
 <input type="text" name="name" style="width:80%; border:0px">
 <hr style="width:80%">
 </div>
 
 <div>
 <label for="password" style="margin-right: 3%;">Password</label>
 <input type="password" name="password" style="width:80%;border:0px" required>
 <hr  style="width:80%">
 </div>
 <h3 style="color:red"><?php echo $err ?></h3>
 <input type="hidden" name="loginForm" value="true">
 <input type="submit" value="Submit">
</form>
