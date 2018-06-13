<?php
  // expenses.php: Show the user the list of expenses.

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
    // Yes; compose a query to delete the specified expenses from the 
    // expenses table. 
    
    $query = "DELETE FROM expenses WHERE expense_id=".clean($_GET['expense_id'], 10);

  // Run the query through the connection
  if(($result = @ mysqli_query($connection, $query))==FALSE)
     showerror($connection);   
  }
  

  // See if we've arrived here after clicking the upate button; if so
  // update the expense table
  elseif(isset($_POST['update']))
  {  
    // Define an SQL query to list the expense IDs in the databse
    $query = "SELECT expense_id FROM expenses";

    // Run the query through the connection
    if (($result = @ mysqli_query($connection, $query))==FALSE)
         showerror($connection);

    // Process the submitted data for each property ID in the database
    while($row = @ mysqli_fetch_array($result))
      {
        $expense_id=$row["expense_id"];

        // Update an existing expense if there is corresponding data
        // submitted from the form.
        if(
           isset($_POST["expense_date"][$expense_id]) && 
           isset($_POST["lockbox"][$expense_id]) &&
           isset($_POST["tvshowing"][$expense_id]) &&
           isset($_POST["gifts"][$expense_id]) &&
           isset($_POST["postage"][$expense_id]) &&
           isset($_POST["dues"][$expense_id]) &&
           isset($_POST["miles_driven"][$expense_id])
          )

           update_or_insert_expense_data($connection, $expense_id);

  }  
  
  // Process the data submitted in the form fields for the new
  // expense; we had assigned this the index 0 in the HTML form.
  update_or_insert_expense_data($connection, 0);
}

// Update the data for an expense w/ the specified expense ID; for a 
// expense ID of 0, add a new expense to the db.

function update_or_insert_expense_data($connection, $expense_id)
{
 
  // Extract the data items for the expense attributes from the $_POST array
  $expense_date =clean($_POST["expense_date"][$expense_id], 128);
  $lockbox =clean($_POST["lockbox"][$expense_id], 128);
  $tvshowing =clean($_POST["tvshowing"][$expense_id], 128);
  $gifts =clean($_POST["gifts"][$expense_id], 128);
  $postage =clean($_POST["postage"][$expense_id], 128);
  $dues =clean($_POST["dues"][$expense_id], 30);
  $miles_driven =clean($_POST["miles_driven"][$expense_id], 30);

  // If the expense_id is 0, this is a new expense, so set the 
  // expense_id to be zero; MySQL will automatically assign a 
  // uniqe expense_id to the new expense.
  if($expense_id==0)
      $expense_id=0;
  
  
  // If any of the attributes are empty, don't upate the db.
  if(
    !strlen($expense_date) ||
    !strlen($lockbox) ||
    !strlen($tvshowing) ||
    !strlen($gifts) ||
    !strlen($postage) ||
    !strlen($dues) ||
    !strlen($miles_driven)
   )
   
  {
    // If this isn't the blank row for optionally adding a new expense, 
    // or if it is the blank row and the user has actually typed
    // something in, display an error msg.
    if(!empty($expense_id)
         ||
         strlen(
            $expense_date.
            $lockbox.
            $tvshowing.
            $gifts.
            $postage.
            $dues.
            $miles_driven)
      )
      echo "<font color='red'>".
           "There must be no empty fields = not updating:<br />".
           "([$expense_date], [$lockbox], [$tvshowing], [$gifts], [$postage], [$dues], [$miles_driven])".
           "<br /></font>";
  }
 else
  {
    // Add or update the expense table
    $query = "REPLACE INTO expenses".
      "(expense_id, expense_date, lockbox, tvshowing, gifts, postage, dues, miles_driven) VALUES (".
         "'$expense_id', '$expense_date', '$lockbox', '$tvshowing', '$gifts', '$postage', '$dues', '$miles_driven')";

  // Run the query through the connection
  if (@ mysqli_query($connection, $query)==FALSE)
        showerror($connection); 

  // Send browser to confirmation page
  header("Location:confirmExpense.php?Status=OK&expense_date=$expense_date");
  }
  
}

  // Show the user the expenses for editing

  // Parameters:
  // (1) An open $connection to the DBMS

  function showexpensesforedit($connection)
  {
    // Create an HTML form pointing back to this script
    echo "\n<form action='{$_SERVER["PHP_SELF"]}' method='POST'>";
    
    // Create an HTML table to neatly arrange the form inputs
    echo "\n<table border='1'>";
    echo "\n<tr>" .
	   "\n\t<th bgcolor='#b3c2bf'>Expense ID</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Expense Date</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Lockbox</th>" .
           "\n\t<th bgcolor='#b3c2bf'>TV Showing</th>" . 
           "\n\t<th bgcolor='#b3c2bf'>Gifts</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Postage</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Dues</th>" .
           "\n\t<th bgcolor='#b3c2bf'>Miles Driven</th>".
           "\n\t<th bgcolor='#b3c2bf'>Delete?</th>".
         "\n</tr>";

  // SQL query to list the properties in the database
  $query = "SELECT * FROM expenses ORDER BY expense_date";

  // Run the query through the connection
  if (($result = @ mysqli_query($connection, $query))==FALSE)
      showerror($connection);

  // Check whether we found any expenses.
  if(!mysqli_num_rows($result))
      // No; display a notice
      echo "\n\t<tr><td colspan='7' align ='center'>".
         "There are no expenses in the database</td></tr>";
  else
     // Yes; fetch the expenses a row at a time

     while($row = @ mysqli_fetch_array($result))
         // Compose the data for this expense into a row of form inputs
         // in the table.
         // Add a delete link in the last column of the row.
         
       echo  "\n<tr>".
             "\n\t<td>{$row["expense_id"]}</td>".
             "\n\t<td><input name='expense_date[{$row['expense_id']}]' ".
                 "value='{$row["expense_date"]}' size='5'       /></td>".
             "\n\t<td><input name='lockbox[{$row['expense_id']}]' ".
                 "value='{$row["lockbox"]}' size='5'        /></td>".
             "\n\t<td><input name='tvshowing[{$row['expense_id']}]' ".
                 "value='{$row["tvshowing"]}' size='5'      /></td>".
             "\n\t<td><input name='gifts[{$row['expense_id']}]' ".
                 "value='{$row["gifts"]}'size='5'       /></td>".
            "\n\t<td><input name='postage[{$row['expense_id']}]' ".
                 "value='{$row["postage"]}' size='5'    / ></td>".
            "\n\t<td><input name='dues[{$row['expense_id']}]' ".
                 "value='{$row["dues"]}' size='5'    / ></td>".
            "\n\t<td><input name='miles_driven[{$row['expense_id']}]' ".
                 "value='{$row["miles_driven"]}' size='5'    / ></td>".
            "\n\t<td><a href='{$_SERVER['PHP_SELF']}?".
                 "action=delete&expense_id={$row["expense_id"]}'>Delete</a></td>".
             "\n</tr>";

  // Display a row w/ blank form inputs to allow a property to be added
      echo "\n<tr><td>New Expense</td>" .
              "\n\t<td><input name='expense_date[0]' size='5' /></td>".
              "\n\t<td><input name='lockbox[0]' size='5'          /></td>".
              "\n\t<td><input name='tvshowing[0]' size='5'          /></td>".
              "\n\t<td><input name='gifts[0]'size='5'         /></td>".
              "\n\t<td><input name='postage[0]'size='5'         /></td>".
              "\n\t<td><input name='dues[0]' size='5'      /></td>".
               "\n\t<td><input name='miles_driven[0]' size='5'      /></td>".
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
   echo "<a href='logout.php'>Logout</a> | <a href='clients.php'>Clients</a> | <a href='properties.php'>Properties</a> | <a href='showings.php'>Showings</a>";

  // Pre-process the msg data for security
//  if(count($_GET))
//    $message = clean(@$_GET["message"], 128);

  // If there's a msg show it
//  if (!empty($message))
//    echo "\n<h3><font color=\"blue\"><em>".
//         urldecode($message)."</em></font></h3>";

   echo "\n<h3>Expenses Page</h3>";

  // Show the existing expenses for editing
  showexpensesforedit($connection);
?>
  </div>
</body>
</html>
