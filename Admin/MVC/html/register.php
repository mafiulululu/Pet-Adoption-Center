<?php
// Since the registration logic has been merged into login.php,
// we redirect any access to register.php back to login.php
header("Location: login.php?tab=register");
exit();
?>
