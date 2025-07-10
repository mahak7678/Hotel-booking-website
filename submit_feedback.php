<?php
$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $rating = (int)$_POST['rating'];
    $comments = mysqli_real_escape_string($con, $_POST['comments']);

    $query = "INSERT INTO feedback (name, email, rating, comments) VALUES ('$name', '$email', '$rating', '$comments')";
    $result = mysqli_query($con, $query);

    if ($result) {
        echo "<script>alert('Thank you for your feedback!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Feedback submission failed. Please try again.'); window.history.back();</script>";
    }
}
?>
