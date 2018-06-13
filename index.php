<?php
  // index.php: Show the user the database table selections
  // Log in a user w/ correct authentication details.

  // Include database parameters and related functions.
   require_once("db.php");

  // Connect to the MySQL DBMS and use the real estate databsae - 
  // credentials are in the file db.php
  
  if (!($connection= @ mysqli_connect(
      $DB_hostname, $DB_username, $DB_password, $DB_databasename)))
      showerror($connection);

  // Pre-process the authentication data from the form for security
  // and assing the username and password to the local variables
  if(count($_POST))
  {
    $username = clean($_POST["username"], 30);
    $password = clean($_POST["password"], 30);
  } 

  // Pre-process the message data for security
  if(count($_GET))
  {
    $message = clean($_GET["message"], 128);
  }

  // If no username or password has been entered, or there's a message
  // to display, show the login page
  if( empty($username) || 
      empty($password) ||
      isset($message) )
  
  {
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en"> 
<head> <!-- Document Header Starts -->
<title>Carl Root's Real Estate Database</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="stylesheet" href="css_samples.css"  type="text/css" /> 
</head>
  <body>
        <h2>Carl's Real-Estate Database</h2>

    <?php
      // If an error message is stored, show it...
      if (isset($message))
        echo "<h3><font color=\"red\">{$message}</font></h3>";
    ?>
    (if you've not logged in before, make up a username and password)
    <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="POST">
     <br />Please enter a username:
           <input type="text" name="username"
              value="<?php if(isset($_POST['username']))
                              echo $_POST['username'];?>" />
     <br />Please enter a password:
              <input type="password" name="password" />
     <br /><input type="submit" value="Log in">
    </form>
    <br />
  </body>
</html>
 <?php
}
else
{
  // Check that the username and password are each at lest four chars long
  if( (strlen($username)<4) ||
      (strlen($password)<4) )
  {
    // No, they're not, create an error msg and redirect
    // the browser to the index page to display the msg
    $message = "Please choose a username and password that are ".
        "at least four characters long";
    header("Location: index.php?message=" . urlencode($message));
    exit;
  }

  // Create a query to find any rows that match the provided username
  $query = "SELECT username, password FROM users WHERE username = '$username'";

  // Run the query through the connection
  if (($result = @ mysqli_query($connection, $query))==FALSE)
      showerror($connection);

  // Were there any matching rows?
  if (mysqli_num_rows($result) == 0)
  {
    // No, insert the username and password into the table
    $query = "INSERT INTO users SET username = '$username', password='".
       crypt($password, substr($username, 0, 2))."'";

    // Run the query through the connection
    if (($result = @ mysqli_query($connection, $query))==FALSE)
         showerror($connection);
  }
  else
  {
  // Yes, check that the supplied password is correct

  // Fetch the matching row

  // If we don't get exactly one answer, then we have a problem
  /**
  for($matchedrows=0;($row= @ mysqli_fetch_array($result));$matchedrows++);
    if($matchedrows!=1)
       die("We've just experienced a technical problem - ".
           "please notify the administrator.");
  */

  // Fetch the matching row.
  $row = @ mysqli_fetch_array($result);

  // Does the user-supplied password match the password in the table?
  if (crypt($password, substr($username, 0, 2)) != $row["password"]) 
  {
    // No, so redirect the browser to the login page with a message
    $message = "This user exists, but the password is incorrect. " .
         "Choose another username, or fix the password.";
    header("Location: index.php?message=" . urlencode($message));
    exit;
  }
 }

 // Everything went OK. Start a session, store the username ina 
 // session variable, and redirect the browser to the property list
 session_start();
 $_SESSION['username']=$username;
 $message = "Welcome {$_SESSION['username']}! Here are the properties".
       " you've been working with!"; 
  header("Location: select.php?message=" . urlencode($message));
 // header("Location: properties.php?message=" . urlencode($message));
  exit;
 }
?>

