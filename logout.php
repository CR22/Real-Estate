<?php
    // Log out of the system by ending session and load the main page

    session_start();
    session_destroy();

    // Redirect to main page
    $message = "You have now logged out.";
    header("Location: index.php?message=" . urlencode($message));
?>
