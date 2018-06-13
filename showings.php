<?php
  // showings.php: Show the user the list of showings.

  // Include database parameters and related functions.
   require_once("db.php");

  // Check if the user is logged in
  // (This also starts the sesion)
  logincheck();
   
  if(!($connection= @ mysqli_connect(
      $DB_hostname, $DB_username, $DB_password, $DB_databasename)))
      showerror($connection);
 
    if (count($_GET) && (clean(@$_GET['action'], 10) == 'delete'))
    {
  //  $property_id = clean($_GET['property_id'], 5);
    $show_id = clean($_GET['show_id'], 5);
    $action = clean($_GET['action'], 6);

    // Yes; compose a query to delte the specified show id from 
    // the show table.
    $query = "DELETE FROM showing WHERE show_id=".clean($_GET['show_id'], 10);

    // Run the query through the connection
    if (($result = @ mysqli_query($connection, $query))==FALSE)
        showerror($connection);
  }

  elseif(isset($_POST['update']))
  {
    // Define an SQL query to list the showing ID's in the database.
      $query = "SELECT show_id, property_id FROM showing"; 

        // Run the query through the connection
    if (($result = @ mysqli_query($connection, $query))==FALSE)
        showerror($connection);

    // Process the submitted data for each show ID in the database.
    while($row = @ mysqli_fetch_array($result))
    {
      $show_id=$row["show_id"];
      $property_id=$row["property_id"];

      // Update an existing showing if there is corresponding data
      // submitted from the form.
      if(
       //   isset($_POST["property_id"][$show_id]) &&
          isset($_POST["show_date"][$show_id]) && 
          isset($_POST["sale"][$show_id]) &&
          isset($_POST["commission"][$show_id]) 
        )
         update_or_insert_showing_data($connection, $show_id, $property_id);
    }
      // Process the data submitted in the form fields for the new
      // showing; This is assigned the index of 0 in the action.php form.
      // Update_or_insert_showing_data($connection, 0); // Don't think I need this.
  }

  // Update showing table with the specified show ID;
  function update_or_insert_showing_data($connection, $show_id, $property_id)
  {

   // Extract the data items for the show attributes from the $_POST array
    $show_date =clean($_POST["show_date"][$show_id], 20);
    $sale =clean($_POST["sale"][$show_id], 5);
    $commission =clean($_POST["commission"][$show_id], 5);

     if($show_id==0)
      $show_id=0; // Don't think I need this.

   // If any of the attributes are empty, don't update the database.
   if(
         !strlen($show_date) || 
         !strlen($sale) ||
         !strlen($commission) ||
         $show_date == '0000-00-00'
     )
   {
    echo "<font color='red'>".
         "The show date field must contain a valid date - not updating:<br />".
         "([$show_date])".
         "<br /></font>";
   }
   else
   {
    // Add or update the showing table
    $query = "REPLACE INTO showing ".
      "(show_id, property_id, show_date, sale, commission) values (".
        "'$show_id', '$property_id', '$show_date', '$sale', '$commission')";

 //    Run the query through the connection
    if (@ mysqli_query($connection, $query)==FALSE)
        showerror($connection); 
   
   }
  }

 function showshowings($connection)
 {

    // Create an HTML form pointing back to this script
    echo "\n<form action='{$_SERVER["PHP_SELF"]}' method='POST'>";


    // Show the Listings as a table.
    echo "\n<table border=1 width=100%>";

    // Create headings for the table
    echo "\n<tr>" .
         //  "\n\t<th>Property ID</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Show Date</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Street Address</th>" .
           "\n\t<th bgcolor='#b3c2bf'>City</th>" .
           "\n\t<th bgcolor='#b3c2bf'>State</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Zip Code</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Country</th>" .
           "\n\t<th bgcolor='#b3c2bf'>List Price</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Sale</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Commission</th>" . 
           "\n\t<th bgcolor='#b3c2bf'>Delete?</th>" .
         "\n</tr>";

  // SQL query to list the properties in the database
  $query = "SELECT show_id, show_date, property_id, streetaddress, city, state, zipcode, country, listprice, show_date, sale, commission FROM showing INNER JOIN property USING (property_id) ORDER BY show_date ASC";

  // Run the query through the connection
  if (($result = @ mysqli_query($connection, $query))==FALSE)
      showerror($connection);

  // Check whether we found any showings
  if(!mysqli_num_rows($result))
      // No; display a notice
      echo "\n\t<tr><td colspan='7' align ='center'>".
         "There are no showings in the database</td></tr>";

   else
     // Yes; fetch the showings a row at a time
     while($row = @ mysqli_fetch_array($result))

         // Display the property data as a table row. 
         echo "\n<tr>" .
          //   "\n\t<td>{$row["property_id"]}</td>".
             "\n\t<td><input name='show_date[{$row['show_id']}]'".
                  "value='{$row["show_date"]}' size='8' /></td>".
             "\n\t<td>{$row["streetaddress"]}</td>".
             "\n\t<td>{$row["city"]}</td>".
             "\n\t<td>{$row["state"]}</td>".
             "\n\t<td>{$row["zipcode"]}</td>".
             "\n\t<td>{$row["country"]}</td>".
             "\n\t<td>{$row["listprice"]}</td>".
             "\n\t<td><input name='sale[{$row['show_id']}]'".
                  "value='{$row["sale"]}' size='5' /></td>".
             "\n\t<td><input name='commission[{$row['show_id']}]'".
                  "value='{$row["commission"]}' size='5' / ></td>".
             "\n\t<td><a href='{$_SERVER['PHP_SELF']}?".
                  "action=delete&show_id={$row["show_id"]}'>Delete</a></td>".
             "\n</tr>";

  // End the table
  echo "\n</table>";

  // Display a update button and end the form.
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
   echo "<a href='logout.php'>Logout</a> | <a href='clients.php'>Clients<a/> | <a href='expenses.php'>Expenses</a> | <a href='properties.php'>Properties</a>";


  // Connect to the MySQL DBMS and use the Real Estate database
  // creds are in the file db.php
  if(!($connection= @ mysqli_connect($DB_hostname, $DB_username, $DB_password, $DB_databasename)))
  showerror($connection);

//   Pre-process the message data for security
//  if(count($_GET))
//    $message = clean($_GET["message"], 128);

 // If there is a message, show it
//  if (!empty($message))
//   echo "\n<h3><font color=\"blue\"><em>".
//          urldecode($message)."</em></font></h3>";

    echo "\n<h3>Showings Page</h3>";
 
    // List the Showings
    showshowings($connection);

?>
  </div>
</body>
</html>
