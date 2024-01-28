<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // No password
$database = "aaramhotel";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch data from the database
$sql = "SELECT * FROM rooms WHERE available = 1"; 
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore - Aaram Hotel</title>
    <link rel="stylesheet" href="explore_styles.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Aaram Hotel</h1>
            <nav>
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="explore.php">Explore</a></li>
                    <li><a href="aboutus.php">About Us</a></li>
                    <li><a href="signin.php">Sign In</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="main-content">
        <div class="container">
            <h2>Explore Our Rooms</h2>
            <div class="room-options">
                <?php
                // Check if there are any results
                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<div class='room'>";
                        // Set image filename based on room type
                        $image_filename = strtolower(str_replace(' ', '', $row["room_type"])) . ".jpg";
                        echo "<img src='" . $image_filename . "' alt='" . $row["room_type"] . "' width='300' height='200'>";
                        echo "<h3>" . $row["room_type"] . "</h3>";
                        echo "<p>" . $row["description"] . "</p>";
                        echo "<p>Price: £" . $row["price"] . " per night</p>";
                        echo "<a href='book.php?room_id=" . $row["room_id"] . "' class='btn book-btn'>Book Now</a>";
                        echo "</div>";
                    }
                } else {
                    echo "No available rooms.";
                }

                $conn->close();
                ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>&copy; 2024 Aaram Hotel. All rights reserved.</p>
            <p>Contact: contact@aaramhotel.com | Address: 123 Main Street, City, Country</p>
        </div>
    </footer>
</body>
</html>
