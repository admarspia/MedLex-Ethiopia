<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    if($username === "admin" && $password === "1234"){
        echo "Login successful";
    } else {
        echo "Invalid login";
    }

} else {
    echo "Please submit the form first.";
}
?>