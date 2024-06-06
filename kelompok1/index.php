<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Organizer</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-blue-600 text-white py-4">
        <h1 class="text-center text-3xl">Schedule Organizer</h1>
    </header>
    <main class="container mx-auto mt-6 p-6">
        <form action="insert.php" method="post" class="bg-white p-6 rounded shadow-md">
            <div class="mb-4">
                <label for="activity" class="block text-sm font-bold mb-2">Activity:</label>
                <input type="text" id="activity" name="activity" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="priority" class="block text-sm font-bold mb-2">Priority (1-9):</label>
                <input type="number" id="priority" name="priority" class="w-full px-3 py-2 border rounded" min="1" max="9" required>
            </div>
            <div class="mb-4">
                <label for="date" class="block text-sm font-bold mb-2">Date:</label>
                <input type="date" id="date" name="date" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="hour" class="block text-sm font-bold mb-2">Hour:</label>
                <input type="time" id="hour" name="hour" class="w-full px-3 py-2 border rounded" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Activity</button>
        </form>
        <form action="index.php" method="get" class="mt-6 bg-white p-6 rounded shadow-md">
            <div class="mb-4">
                <label for="view_date" class="block text-sm font-bold mb-2">View activities for date:</label>
                <input type="date" id="view_date" name="view_date" class="w-full px-3 py-2 border rounded" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">View Activities</button>
        </form>
        <h2 class="text-2xl font-bold mt-6">Dates with Activities</h2>
        <?php
        include 'db.php';
        $sql_dates = "SELECT DISTINCT date FROM schedule ORDER BY date ASC";
        $result_dates = $conn->query($sql_dates);
        if ($result_dates->num_rows > 0) {
            echo "<ul class='mt-4'>";
            while($row = $result_dates->fetch_assoc()) {
                echo "<li class='bg-white p-2 mb-2 rounded shadow'>" . date('F j, Y', strtotime($row["date"])) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='mt-4'>No dates with activities.</p>";
        }
        $selected_date = isset($_GET['view_date']) ? $_GET['view_date'] : date('Y-m-d');
        $sql = "SELECT * FROM schedule WHERE date = '$selected_date' ORDER BY priority ASC";
        $result = $conn->query($sql);
        echo "<h2 class='text-2xl font-bold mt-6'>Activities for " . (isset($_GET['view_date']) ? date('F j, Y', strtotime($_GET['view_date'])) : date('F j, Y')) . "</h2>";

        if ($result->num_rows > 0) {
            // Initialize an array to hold activities grouped by hour
            $activities_by_hour = array();

            // Group activities by hour
            while ($row = $result->fetch_assoc()) {
                $hour = $row['hour'];
                if (!isset($activities_by_hour[$hour])) {
                    $activities_by_hour[$hour] = array();
                }
                $activities_by_hour[$hour][] = $row;
            }

            $selected_date = isset($_GET['view_date']) ? $_GET['view_date'] : date('Y-m-d');

            // Check if the selected date is the current date
            if ($selected_date == date('Y-m-d')) {
                $current_hour = intval(date('H')); // Current hour
            } else {
                $current_hour = 0; // Set current hour to 0 for non-current dates
            }
            
            // Sort activities by hour, with the closest hour to the current time first
            uksort($activities_by_hour, function($a, $b) use ($current_hour) {
                $diff_a = (intval(explode(':', $a)[0]) - $current_hour + 24) % 24;
                $diff_b = (intval(explode(':', $b)[0]) - $current_hour + 24) % 24;
                return $diff_a - $diff_b;
            });

            // Display activities grouped by hour in a calendar-like format
            echo "<div class='grid grid-cols-1 md:grid-cols-4 gap-4'>";
            foreach ($activities_by_hour as $hour => $activities) {
                echo "<div class='bg-white p-4 rounded shadow'>";
                echo "<h3 class='text-xl font-bold'>Hour: " . $hour . "</h3>";
                echo "<ul class='mt-2'>";
                foreach ($activities as $activity) {
                    echo "<li class='mb-2'>Priority " . $activity["priority"] . ": " . $activity["activity"] . "</li>";
                }
                echo "</ul>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "<p class='mt-4'>No activities for this date.</p>";
        }
        $conn->close();
        ?>
    </main>
    <footer class="bg-blue-600 text-white text-center py-4 mt-6">
        <p>&copy; Kelompok 1</p>
    </footer>
</body>
</html>
