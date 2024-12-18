
<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; }
        .history-container { margin: 20px auto; max-width: 800px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="history-container">
        <h2>Booking History</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Pickup</th>
                    <th>Drop-off</th>
                    <th>Vehicle</th>
                    <th>Fare (Rs)</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM yango_bookings ORDER BY booking_time DESC";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['user']}</td>
                                <td>{$row['pickup_location']}</td>
                                <td>{$row['dropoff_location']}</td>
                                <td>{$row['vehicle_type']}</td>
                                <td>{$row['fare']}</td>
                                <td>{$row['booking_time']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No bookings found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-primary">Back to Booking</a>
    </div>
</body>
</html>
