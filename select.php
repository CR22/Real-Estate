<?php
// select.php. Show links to the different lists.
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

echo "<h3>Selection Page</h3>";
// Show links to the different lists.
echo "<a href='clients.php'>Clients List</a><br><br>";
echo "<a href='expenses.php'>Expense List</a><br><br>";
echo "<a href='properties.php'>Properties List</a><br><br>";
echo "<a href='showings.php'>Showings List</a><br><br>";
echo "<a href='logout.php'>Logout</a>";

?>
</div>
</body>
</html>
