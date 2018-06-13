<?php
  // properties.php: Show the user the available properties

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
  if(count($_GET) && (clean($_GET['action'], 10)=='delete'))
  {
    // Yes; compose a query to delete the specified property from the 
    // property table. 
    // ** NOTE: This query does not delete the property ID from the showing table. 
    // ** Need to decide how we want to handle delted properties where the property_id 
    // ** is in the showing table (leave, or delete).
    $query = "DELETE FROM property WHERE property_id=".clean($_GET['property_id'], 10);

  // Run the query through the connection
  if(($result = @ mysqli_query($connection, $query))==FALSE)
     showerror($connection);   
  }
  

  // See if we've arrived here after clicking the upate button; if so
  // update the property table
  elseif(isset($_POST['update']))
  {  
    // Define an SQL query to list the property IDs in the databse
    $query = "SELECT property_id FROM property";

    // Run the query through the connection
    if (($result = @ mysqli_query($connection, $query))==FALSE)
         showerror($connection);

    // Process the submitted data for each property ID in the database
    while($row = @ mysqli_fetch_array($result))
      {
        $property_id=$row["property_id"];

        // Update an existing property if there is corresponding data
        // submitted from the form.
        if(
           isset($_POST["streetaddress"][$property_id]) && 
           isset($_POST["city"][$property_id]) &&
           isset($_POST["state"][$property_id]) &&
           isset($_POST["zipcode"][$property_id]) &&
           isset($_POST["country"][$property_id]) &&
           isset($_POST["listprice"][$property_id]) 
          )

           update_or_insert_property_data($connection, $property_id);
  }  
  
  // Process the data submitted in the form fields for the new
  // property; we had assigned this the index 0 in the HTML form.
  update_or_insert_property_data($connection, 0);
}

// Update the data for a property w/ the specified property ID; for a 
// property ID of 0, add a new property to the db.

function update_or_insert_property_data($connection, $property_id)
{
 
  // Extract the data items for the property attributes from the $_POST array
  $streetaddress =clean($_POST["streetaddress"][$property_id], 128);
  $city =clean($_POST["city"][$property_id], 128);
  $state =clean($_POST["state"][$property_id], 128);
  $zipcode =clean($_POST["zipcode"][$property_id], 128);
  $country =clean($_POST["country"][$property_id], 128);
  $listprice =clean($_POST["listprice"][$property_id], 30);

  // If the property_id is 0, this is a new property, so set the 
  // property_id to be zero; MySQL will automatically assign a 
  // uniqe property_id to the new property.
  if($property_id==0)
      $property_id=0;
  
  
  // If any of the attributes are empty, don't upate the db.
  if(
    !strlen($streetaddress) ||
    !strlen($city) ||
    !strlen($state) ||
    !strlen($zipcode) ||
    !strlen($country) ||
    !strlen($listprice)
   )
   
  {
    // If this isn't the blank row for optionally adding a new prop, 
    // or if it is the blank row and the user has actually typed
    // something in, display an error msg.
    if(!empty($property_id)
         ||
         strlen(
            $streetaddress.
            $city.
            $state.
            $zipcode.
            $country.
            $listprice)
      )
      echo "<font color='red'>".
           "There must be no empty fields = not updating:<br />".
           "([$streetaddress], [$city], [$state], [$zipcode], [$country], [$listprice])".
           "<br /></font>";
  }
 else
  {
    // Add or update the property table
    $query = "REPLACE INTO property".
      "(property_id, streetaddress, city, state, zipcode, country, listprice) VALUES (".
         "'$property_id', '$streetaddress', '$city', '$state', '$zipcode', '$country', '$listprice')";

  // Run the query through the connection
  if (@ mysqli_query($connection, $query)==FALSE)
        showerror($connection); 
  }
  
}
  // Show the user the properties for editing

  // Parameters:
  // (1) An open $connection to the DBMS
  function showpropsforedit($connection)
  {
    // Create an HTML form pointing back to this script
    echo "\n<form action='{$_SERVER["PHP_SELF"]}' method='POST'>";
    
    // Create an HTML table to neatly arrange the form inputs
    echo "\n<table border='1'>";
    echo "\n<tr>" .
	   "\n\t<th bgcolor='#b3c2bf'>Property ID</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Street Address</th>" .
           "\n\t<th bgcolor='#b3c2bf'>City</th>" .
           "\n\t<th bgcolor='#b3c2bf'>State</th>" . 
           "\n\t<th bgcolor='#b3c2bf'>Zip Code</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Country</th>" .
           "\n\t<th bgcolor='#b3c2bf'>List Price</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Add Showing?</th>".
           "\n\t<th bgcolor='#b3c2bf'>Delete?</th>".
         "\n</tr>";

  // SQL query to list the properties in the database
  $query = "SELECT * FROM property ORDER BY city";

  // Run the query through the connection
  if (($result = @ mysqli_query($connection, $query))==FALSE)
      showerror($connection);

  // Check whether we found any properties
  if(!mysqli_num_rows($result))
      // No; display a notice
      echo "\n\t<tr><td colspan='7' align ='center'>".
         "There are no properties in the database</td></tr>";
  else
     // Yes; fetch the properties a row at a time

     while($row = @ mysqli_fetch_array($result))
         // Compose the data for this property into a row of form inputs
         // in the table.
         // Add a delete link in the last column of the row.
         
       echo  "\n<tr>".
             "\n\t<td>{$row["property_id"]}</td>".
             "\n\t<td><input name='streetaddress[{$row['property_id']}]' ".
                 "value='{$row["streetaddress"]}' size='15' /></td>".
             "\n\t<td><input name='city[{$row['property_id']}]' ".
                 "value='{$row["city"]}' size='15'       /></td>".
             "\n\t<td><input name='state[{$row['property_id']}]' ".
                 "value='{$row["state"]}' size='5'        /></td>".
             "\n\t<td><input name='zipcode[{$row['property_id']}]' ".
                 "value='{$row["zipcode"]}' size='5'      /></td>".
             "\n\t<td><input name='country[{$row['property_id']}]' ".
                 "value='{$row["country"]}'size='5'       /></td>".
            "\n\t<td><input name='listprice[{$row['property_id']}]' ".
                 "value='{$row["listprice"]}' size='5'    / ></td>".
            "\n\t<td><a href=\"action.php?action=add&" .
                 "property_id={$row["property_id"]}\">Add Showing</a></td>".
            "\n\t<td><a href='{$_SERVER['PHP_SELF']}?".
                 "action=delete&property_id={$row["property_id"]}'>Delete</a></td>".
             "\n</tr>";

  // Display a row w/ blank form inputs to allow a property to be added
      echo "\n<tr><td>New Property</td>" .
              "\n\t<td><input name='streetaddress[0]' size='15' /></td>".
              "\n\t<td><input name='city[0]' size='15'          /></td>".
              "\n\t<td><input name='state[0]' size='5'          /></td>".
              "\n\t<td><input name='zipcode[0]'size='5'         /></td>".
              "\n\t<td><input name='country[0]'size='5'         /></td>".
              "\n\t<td><input name='listprice[0]' size='5'      /></td>".
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
   echo "<a href='logout.php'>Logout</a> | <a href='clients.php'>Clients</a> | <a href='expenses.php'>Expenses</a> | <a href='showings.php'>Showings</a>";
   echo "\n<h3>Properties Page</h3>";

  // Show the existing props for editing
  showpropsforedit($connection);
?>
  </div>
</body>
</html>
