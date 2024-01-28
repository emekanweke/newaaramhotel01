<?php
$messageColor = ''; // Default message color

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Retrieve the last user ID from the database
    $last_user_id_query = "SELECT MAX(user_id) as max_user_id FROM users";
    $result = $conn->query($last_user_id_query);
    $row = $result->fetch_assoc();
    $last_user_id = $row['max_user_id'];

    // Increment the last user ID to generate a new one
    if ($last_user_id) {
        $user_number = intval(substr($last_user_id, 3)) + 1;
    } else {
        // If there are no existing users, set the initial user number to 1
        $user_number = 1;
    }
    $new_user_id = 'USR' . sprintf('%03d', $user_number);

    // Prepare data for insertion into users table
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert into users table
    $user_sql = "INSERT INTO users (user_id, username, email, password_hash, first_name, last_name) 
            VALUES ('$new_user_id', '$username', '$email', '$password_hash', '$first_name', '$last_name')";

    if ($conn->query($user_sql) === TRUE) {
        // Prepare data for insertion into booking table
        $room_id = isset($_POST['room_id']) ? $_POST['room_id'] : '';
        $check_in_date = $_POST['check_in_date'];
        $check_out_date = $_POST['check_out_date'];
        $num_guests = $_POST['num_guests'];

        // Generate a new booking ID
        $last_booking_id_query = "SELECT MAX(booking_id) as max_booking_id FROM bookings";
        $result = $conn->query($last_booking_id_query);
        $row = $result->fetch_assoc();
        $last_booking_id = $row['max_booking_id'];

        // Increment the last booking ID to generate a new one
        if ($last_booking_id) {
            $booking_number = intval(substr($last_booking_id, 2)) + 1;
        } else {
            // If there are no existing bookings, set the initial booking number to 1
            $booking_number = 1;
        }
        $new_booking_id = 'BK' . sprintf('%04d', $booking_number);

        // Insert into booking table
        $booking_sql = "INSERT INTO bookings (booking_id, user_id, room_id, check_in_date, check_out_date, num_guests) 
                VALUES ('$new_booking_id', '$new_user_id', '$room_id', '$check_in_date', '$check_out_date', $num_guests)";

        if ($conn->query($booking_sql) === TRUE) {
            $registrationMessage = 'User registered successfully! Your booking has been confirmed. Please sign in now to view your booking.';
            $messageColor = 'green';
        } else {
            $registrationMessage = 'Error booking room!';
            $messageColor = 'red';
            echo "Error: " . $booking_sql . "<br>" . $conn->error;
        }
    } else {
        $registrationMessage = 'Error registering user!';
        $messageColor = 'red';
        echo "Error: " . $user_sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room - Aaram Hotel</title>
    <link rel="stylesheet" href="book_styles.css">
    <style>
        /* Additional inline style for message color */
        .registration-message {
            color: <?php echo $messageColor; ?>;
        }
    </style>
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
            <h2>Book Your Room</h2>
            <?php if(isset($registrationMessage)): ?>
                <div class="registration-message"><?php echo $registrationMessage; ?></div>
            <?php endif; ?>
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                <input type="hidden" name="room_id" value="<?php echo isset($_GET['room_id']) ? $_GET['room_id'] : ''; ?>">

                
                <!-- User details -->
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br>

                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required><br>

                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required><br>

                <!-- Booking details -->
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>

                <label for="check_in_date">Check-in Date:</label>
                <input type="date" id="check_in_date" name="check_in_date" required><br>

                <label for="check_out_date">Check-out Date:</label>
                <input type="date" id="check_out_date" name="check_out_date" required><br>

                <label for="num_guests">Number of Guests:</label>
                <input type="number" id="num_guests" name="num_guests" required><br>

                <input type="submit" value="Book Now">
            </form>
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
