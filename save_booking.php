<?php
include 'db.php';
session_start();

// Fetch username from session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Fetch user data
$is_new_user = false;
if ($username != 'Guest') {
    $check_user = "SELECT COUNT(*) AS total FROM yango_bookings WHERE user = '$username'";
    $user_result = mysqli_query($conn, $check_user);
    $row = mysqli_fetch_assoc($user_result);
    $is_new_user = $row['total'] == 0 ? true : false;
}

// Fetch booking history
$query = "SELECT * FROM yango_bookings ORDER BY booking_time DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
        }
        .history-container {
            margin: 40px auto;
            max-width: 1000px;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #d32f2f;
            text-align: center;
            font-weight: bold;
        }
        table {
            margin-top: 20px;
        }
        table th {
            background-color: #d32f2f;
            color: #fff;
            text-align: center;
        }
        table td {
            text-align: center;
            vertical-align: middle;
        }
        .btn-back {
            background-color: #d32f2f;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .btn-back:hover {
            background-color: #b71c1c;
            color: #ffffff;
        }
        .discount {
            background-color: #ffebee;
            color: #b71c1c;
            padding: 10px;
            border: 1px dashed #b71c1c;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="history-container">
            <!-- Greeting User -->
            <h3>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h3>

            <!-- New User Discount -->
            <?php if ($is_new_user): ?>
                <div class="discount">
                    🎉 Congratulations! As a new user, you get <strong>20% OFF</strong> on your first ride.
                </div>
            <?php endif; ?>

            <h2>📋 Booking History</h2>
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Pickup</th>
                        <th>Drop-off</th>
                        <th>Vehicle</th>
                        <th>Fare (Rs)</th>
                        <th>Booking Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $fare = $row['fare'];
                            if ($is_new_user) {
                                $fare = $fare * 0.8; // Apply 20% discount
                            }
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['user']}</td>
                                    <td>{$row['pickup_location']}</td>
                                    <td>{$row['dropoff_location']}</td>
                                    <td>" . ucfirst($row['vehicle_type']) . "</td>
                                    <td>₨ " . number_format($fare, 2) . "</td>
                                    <td>{$row['booking_time']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr>
                                <td colspan='7' class='text-center text-muted'>No bookings found</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="text-center">
                <a href="index.php" class="btn-back">← Back to Booking</a>
            </div>
        </div>
    </div>
</body>
</html>
