<?php

session_start();

//if (isset($_POST['out'])) {
session_destroy();
header("location: ../login.php");
//}
