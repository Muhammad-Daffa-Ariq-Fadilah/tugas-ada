<?php
include 'db.php';
$activity = $_POST['activity'];
$priority = $_POST['priority'];
$date = $_POST['date'];
$hour = $_POST['hour'];
$sql = "INSERT INTO schedule (activity, priority, date, hour) VALUES ('$activity', '$priority', '$date', '$hour')";
if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
header("Location: index.php");
exit();
?>
