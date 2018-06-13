<?php
 //  action.php: Add a property to the showings list.

  // Include databse parameters and related functions
  require_once("db.php");

  // Check if the user is loggen in
  // (this also starts the session)
  logincheck();

  
  // Secure the user data
  if(count($_GET))
  {
    // Connect to the MySQL DBMS and use the realestate database - creds are 
    // in the file db.php
    if(!($connection= @ mysqli_connect(
        $DB_hostname, $DB_username, $DB_password, $DB_databasename)))
        showerror($connection);

    $property_id = clean($_GET['property_id'], 5);
    $action = clean($_GET['action'], 6);

    // Is the action something we know about?
    if($action != "add" && $action != "remove")    
      //No, it's not; perhaps someone's trying to manipulate the 
      // URL query string?
      die("Unknown action: ".$action);
 

    // The program should reach this point only if the action is add
    // or remove, since otherwise processing stops with the die()
    // instructions.

    // What did the user want us to do?
    if ($action == "add")
    { 
      // The user wants to add a new item to the showings list.

      // Update the showings table if we find the property.
     // $query = "SELECT * FROM  property WHERE property_id = {$property_id}";
        $query = "INSERT INTO showing (show_id, property_id, show_date, sale, commission) values (".
                  "0, '$property_id', NULL, 'No', 00.00)";

     // Run the query through the connection
     if (($result = @ mysqli_query($connection, $query))==FALSE)
        showerror($connection);

    // If we found the row and updated it, create a confirmation
    // message to show the user
    if (mysqli_affected_rows($connection) == 1)
    {
	$message =  "The showings have been updated.";
    } 
  }
 }
  // Redirect the browser back to showings.php
   header("Location: showings.php?message=" . urlencode($message));

  exit;
?>
