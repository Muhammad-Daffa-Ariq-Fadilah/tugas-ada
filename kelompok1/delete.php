<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['activity_id'])) {
    $activity_id = $_POST['activity_id'];
    
    // Prepare a delete statement
    $sql = "DELETE FROM schedule WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $activity_id);
        
        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to index.php after successful deletion
            header("Location: index.php");
            exit();
        } else {
            echo "Error deleting record: " . $conn->error;
        }
        
        // Close statement
        $stmt->close();
    }
}

// Close connection
$conn->close();
?>
