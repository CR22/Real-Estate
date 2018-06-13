<?php
  // clients.php: Show the user the list of clients.

  // Include database parameters and related functions.
   require_once("db.php");

  // Check if the user is logged in
  // (This also starts the sesion)
  logincheck();

  // Connect to the MySQL DBMS and use the real estate databsae - 
  // credentials are in the file db.php
  
  if(!($connection= @ mysqli_connect(
      $DB_hostname, $DB_username, $DB_password, $DB_databasename)))
      showerror($connection);
  
  // See if we've arrived here after clicking the delete link
  if(count($_GET) && (clean(@$_GET['action'], 10)=='delete'))
  {
    // Yes; compose a query to delete the specified property from the 
    // property table
    $query = "DELETE FROM client WHERE cust_id=".clean($_GET['cust_id'], 10);

  // Run the query through the connection
  if(($result = @ mysqli_query($connection, $query))==FALSE)
     showerror($connection);   
  }
  

  // See if we've arrived here after clicking the upate button; if so
  // update the client table
  elseif(isset($_POST['update']))
  {  
    // Define an SQL query to list the client IDs in the databse
    $query = "SELECT cust_id FROM client";

    // Run the query through the connection
    if (($result = @ mysqli_query($connection, $query))==FALSE)
         showerror($connection);

    // Process the submitted data for each client ID in the databse
    while($row = @ mysqli_fetch_array($result))
      {
        $cust_id=$row["cust_id"];

        // Update an existing Customer if there is corresponding data
        // submitted from the form.
        if(
           isset($_POST["firstname"][$cust_id]) && 
           isset($_POST["lastname"][$cust_id]) &&
           isset($_POST["emailaddress"][$cust_id]) &&
           isset($_POST["phonenumber"][$cust_id]) &&
           isset($_POST["streetaddress"][$cust_id]) &&
           isset($_POST["city"][$cust_id]) &&
           isset($_POST["state"][$cust_id]) &&
           isset($_POST["zipcode"][$cust_id]) &&
           isset($_POST["country"][$cust_id]) 
          )

           update_or_insert_client_data($connection, $cust_id);
  }  
  
  // Process the data submitted in the form fields for the new
  // Client; we had assigned this the index 0 in the HTML form.
  update_or_insert_client_data($connection, 0);
}

// Update the data for a Client w/ the specified property ID; for a 
// client ID of 0, add a new client to the db.

function update_or_insert_client_data($connection, $cust_id)
{
 
  // Extract the data items for the client attributes from the $_POST array
  $firstname =clean($_POST["firstname"][$cust_id], 128);
  $lastname =clean($_POST["lastname"][$cust_id], 128);
  $emailaddress =clean($_POST["emailaddress"][$cust_id], 128);
  $phonenumber =clean($_POST["phonenumber"][$cust_id], 128);
  $streetaddress = clean($_POST["streetaddress"][$cust_id], 128);
  $city = clean($_POST["city"][$cust_id], 128);
  $state =clean($_POST["state"][$cust_id], 128);
  $zipcode =clean($_POST["zipcode"][$cust_id], 128);
  $country =clean($_POST["country"][$cust_id], 128);

 
  // If the cust_id is 0, this is a new customer, so set the 
  // cust_id to be zero; MySQL will automatically assign a 
  // uniqe cust_id to the new property.
  if($cust_id==0)
      $cust_id=0;
  
  
  // If any of the attributes are empty, don't upate the db.
  if(
    !strlen($firstname) ||
    !strlen($lastname) ||
    !strlen($emailaddress) ||
    !strlen($phonenumber) ||
    !strlen($streetaddress) ||
    !strlen($city) ||
    !strlen($state) ||
    !strlen($zipcode) ||
    !strlen($country)
   )
   
  {
    // If this isn't the blank row for optionally adding a new customer, 
    // or if it is the blank row and the user has actually typed
    // something in, display an error msg.
    if(!empty($cust_id)
         ||
         strlen(
            $firstname.
            $lastname.
            $emailaddress.
            $phonenumber.
            $streetaddress.
            $city.
            $state.
            $zipcode.
            $country)
      )
      echo "<font color='red'>".
           "There must be no empty fields = not updating:<br />".
           "([$firstname], [$lastname], [$emailaddress], [$phonenumber], [$streetaddress], [$city], [$state], [$zipcode], [$country])".
           "<br /></font>";
  }
 else
  {
    // Add or update the client table
    $query = "REPLACE INTO client".
      "(cust_id, firstname, lastname, emailaddress, phonenumber, streetaddress, city, state, zipcode, country) VALUES (".
         "'$cust_id', '$firstname', '$lastname', '$emailaddress', '$phonenumber', '$streetaddress', '$city', '$state', '$zipcode', '$country')";

  // Run the query through the connection
  if (@ mysqli_query($connection, $query)==FALSE)
        showerror($connection);

    // Send browser to receipt page
  header("Location:confirmClient.php?Status=OK&lastname=$lastname"); 
  }
  
}
 
  // Show the user the customer list for editing

  // Parameters:
  // (1) An open $connection to the DBMS
  function showclientsforedit($connection)
  {
    // Create an HTML form pointing back to this script
    echo "\n<form action='{$_SERVER["PHP_SELF"]}' method='POST'>";
    
    // Create an HTML table to neatly arrange the form inputs
    echo "\n<table border='1'>";
    echo "\n<tr>" .
	   "\n\t<th bgcolor='#b3c2bf'>Client ID</th>" .
           "\n\t<th bgcolor='#b3c2bf'>First Name</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Last Name</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Email</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Phone No.</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Street Address</th>" .
           "\n\t<th bgcolor='#b3c2bf'>City</th>" .
           "\n\t<th bgcolor='#b3c2bf'>State</th>" . 
           "\n\t<th bgcolor='#b3c2bf'>Zip Code</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Country</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Delete?</th>" .
         "\n</tr>";

  // SQL query to list the properties in the database
  $query = "SELECT * FROM client ORDER BY lastname";

  // Run the query through the connection
  if (($result = @ mysqli_query($connection, $query))==FALSE)
      showerror($connection);

  // Check whether we found any Clients
  if(!mysqli_num_rows($result))
      // No; display a notice
      echo "\n\t<tr><td colspan='7' align ='center'>".
         "There are no Clients in the database</td></tr>";
  else
     // Yes; fetch the Clients a row at a time
     while($row = @ mysqli_fetch_array($result))
         // Compose the data for this Client into a row of form inputs
         // in the table.
         // Add a delete link in the last column of the row. 
         echo "\n<tr>" .
             "\n\t<td>{$row["cust_id"]}</td>".
	     "\n\t<td><input name='firstname[{$row['cust_id']}]' ".
                 "value='{$row["firstname"]}' size='10' /></td>".
             "\n\t<td><input name='lastname[{$row['cust_id']}]' ".
                 "value='{$row["lastname"]}' size='10' /></td>".
             "\n\t<td><input name='emailaddress[{$row['cust_id']}]' ".
                 "value='{$row["emailaddress"]}' size='10' /></td>".
             "\n\t<td><input name='phonenumber[{$row['cust_id']}]' ".
                 "value='{$row["phonenumber"]}' size='5' /></td>".
             "\n\t<td><input name='streetaddress[{$row['cust_id']}]' ".
                 "value='{$row["streetaddress"]}' size='15' /></td>".
             "\n\t<td><input name='city[{$row['cust_id']}]' ".
                 "value='{$row["city"]}' size= '5'    /></td>".
             "\n\t<td><input name='state[{$row['cust_id']}]' ".
                 "value='{$row["state"]}' size='5'        /></td>".
             "\n\t<td><input name='zipcode[{$row['cust_id']}]' ".
                 "value='{$row["zipcode"]}' size='5'      /></td>".
             "\n\t<td><input name='country[{$row['cust_id']}]' ".
                 "value='{$row["country"]}'size='5'       /></td>".
             "\n\t<td><a href='{$_SERVER['PHP_SELF']}?".
                 "action=delete&cust_id={$row["cust_id"]}'>Delete</a></td>".
             "\n</tr>";

  // Display a row w/ blank form inputs to allow a property to be added
      echo "\n<tr><td>New Client</td>" .
              "\n\t<td><input name='firstname[0]' size='10'     /></td>".
	      "\n\t<td><input name='lastname[0]'  size='10'     /></td>".
              "\n\t<td><input name='emailaddress[0]'size='10'    /></td>".
              "\n\t<td><input name='phonenumber[0]' size='5'    /></td>".
              "\n\t<td><input name='streetaddress[0]' size='15' /></td>".
              "\n\t<td><input name='city[0]' size='5'           /></td>".
              "\n\t<td><input name='state[0]' size='5'          /></td>".
              "\n\t<td><input name='zipcode[0]'size='5'         /></td>".
              "\n\t<td><input name='country[0]'size='5'         /></td>".
           "\n</tr>";
  
  // End the table
  echo "\n</table>";

  // Display a submit button and end the form
  echo "\n<input name='update' type='submit' value='Update data' />";
  echo "</form>";
           
  }
  
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
<div align="center">
  <h2>Carl's Real-Estate Database</h2>
<?php
  // Show a logout link and a link to the main page
   echo "<a href='logout.php'>Logout</a> | <a href='expenses.php'>Expenses</a> | <a href='properties.php'>Properties</a> | <a href='showings.php'>Showings</a>";
   echo "\n<h3>Clients Page</h3>";

  // Show the existing props for editing
  showclientsforedit($connection);
?>
</div>
</body>
</html>
