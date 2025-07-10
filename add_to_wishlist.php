<?php
session_start();

$con = mysqli_connect('localhost', 'root', 'Password@123', 'ecommerce');

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['room_id'])) {
    $roomId = (int) $_POST['room_id'];
    $sessionId = session_id();

    $checkQuery = $con->prepare("SELECT 1 FROM wishlist WHERE room_id = ? AND session_id = ?");
    $checkQuery->bind_param("is", $roomId, $sessionId);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows === 0) {
        $insertQuery = $con->prepare("INSERT INTO wishlist (session_id, room_id) VALUES (?, ?)");
        $insertQuery->bind_param("si", $sessionId, $roomId);

        if ($insertQuery->execute()) {
            echo "Room added to wishlist!";
        } else {
            echo "Error adding to wishlist: " . $con->error;
        }

        $insertQuery->close();
    } else {
        echo "This room is already in your wishlist!";
    }

    $checkQuery->close();
}

mysqli_close($con);
?>
