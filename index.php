<?php
include 'db.php';
session_start();

// User session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Check if user is new
$is_new_user = false;
if ($username != 'Guest') {
    $query = "SELECT COUNT(*) as total FROM yango_bookings WHERE user = '$username'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $is_new_user = $row['total'] == 0 ? true : false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yango Ride Booking</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <style>
        :root {
            --primary-color: #d32f2f;
            --secondary-color: #ffebee;
            --accent-color: #ff5252;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--secondary-color);
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .booking-panel {
            width: 350px;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .map-container {
            flex-grow: 1;
        }

        #map {
            height: 100%;
            width: 100%;
        }

        .booking-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--primary-color);
        }

        input, select {
            padding: 10px;
            border: 1px solid var(--primary-color);
            border-radius: 5px;
        }

        .book-btn, .history-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .book-btn:hover, .history-btn:hover {
            background-color: var(--accent-color);
        }

        #status, #price-estimate {
            text-align: center;
            margin-top: 10px;
            color: var(--primary-color);
        }

        .button-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .booking-panel {
                width: 100%;
                height: auto;
            }

            #map {
                height: 50vh;
            }
           
        }
        h1 {
            color: var(--primary-color);
            text-align: center;
        }

        .welcome-user {
            color: var(--primary-color);
            text-align: center;
        }

        .discount-banner {
            background: #ffe8e8;
            color: var(--primary-color);
            padding: 10px;
            text-align: center;
            margin: 10px 0;
            border: 1px solid var(--primary-color);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="booking-panel">
            <h1 style="color: var(--primary-color); text-align: center;">Yango Rides</h1>
            <p class="welcome-user">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong> 👋</p>

<?php if ($is_new_user): ?>
    <div class="discount-banner">
        🎉 As a new user, you get <strong>20% OFF</strong> on your first ride!
    </div>
<?php endif; ?>
            <form id="rideForm" class="booking-form">
                <div class="form-group">
                    <label for="pickup">Pickup Location</label>
                    <input type="text" id="pickup" required placeholder="Enter pickup point">
                </div>
                <div class="form-group">
                    <label for="dropoff">Drop-off Location</label>
                    <input type="text" id="dropoff" required placeholder="Enter drop-off point">
                </div>
                <div class="form-group">
                    <label for="vehicleType">Select Vehicle Type</label>
                    <select id="vehicleType">
                        <option value="bike">Bike</option>
                        <option value="car">Car</option>
                    </select>
                </div>
                <p id="price-estimate"></p>
                <div class="button-container">
                    <button type="button" class="book-btn" onclick="bookRide()">Book Ride</button>
                    <button type="button" class="history-btn" onclick="viewRideHistory()">Ride History</button>
                </div>
                <p id="status"></p>
            </form>
        </div>
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script>
        var map = L.map('map').setView([24.8607, 67.0011], 12);

        // Add OpenStreetMap Tile Layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var routeControl, pickupMarker, dropoffMarker, driverMarker;
        
        // Calculate Estimated Price
        function calculateEstimatedPrice(vehicleType, distance) {
            // Base price for different vehicle types
            const basePrices = {
                'bike': 50,   // 50 rupees base price for bike
                'car': 100    // 100 rupees base price for car
            };

            // Price per kilometer
            const pricePerKm = {
                'bike': 10,   // 10 rupees per km for bike
                'car': 20     // 20 rupees per km for car
            };

            // Estimated distance (dummy calculation for this example)
            const estimatedDistance = distance || 10; // Default 10 km if no distance provided

            // Calculate total price
            const basePrice = basePrices[vehicleType];
            const distancePrice = estimatedDistance * pricePerKm[vehicleType];
            
            return basePrice + distancePrice;
        }

        // Book Ride Function
        function bookRide() {
            const pickup = document.getElementById("pickup").value;
            const dropoff = document.getElementById("dropoff").value;
            const vehicleType = document.getElementById("vehicleType").value;

            if (pickup && dropoff) {
                // Calculate and display estimated price
                const estimatedPrice = calculateEstimatedPrice(vehicleType);
                document.getElementById("price-estimate").innerText = `Estimated Price: ₨${estimatedPrice}`;

                document.getElementById("status").innerText = `Searching for a ${vehicleType}...`;

                // Simulate Driver Assignment
                setTimeout(() => {
                    assignDriver(vehicleType, estimatedPrice, pickup, dropoff);
                }, 2000);
            } else {
                alert("Please enter both Pickup and Drop-off locations.");
            }
        }

        // Assign Driver and Show Route
        function assignDriver(vehicleType, estimatedPrice, pickup, dropoff) {
            const pickupLatLng = [24.8607, 67.0011];
            const dropoffLatLng = [24.9307, 67.0877];
            const driverLatLng = [24.8707, 67.0111]; // Dummy driver starting point

            // Add Pickup and Drop-off Markers
            if (pickupMarker) map.removeLayer(pickupMarker);
            if (dropoffMarker) map.removeLayer(dropoffMarker);

            pickupMarker = L.marker(pickupLatLng).addTo(map).bindPopup("Pickup Location").openPopup();
            dropoffMarker = L.marker(dropoffLatLng).addTo(map).bindPopup("Drop-off Location");

            // Add Driver Marker
            if (driverMarker) map.removeLayer(driverMarker);
            driverMarker = L.marker(driverLatLng, {icon: L.icon({
                iconUrl: vehicleType === 'bike' ? 'https://cdn-icons-png.flaticon.com/512/189/189662.png' : 'https://cdn-icons-png.flaticon.com/512/743/743589.png',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            })}).addTo(map).bindPopup("Driver on the way!");

            document.getElementById("status").innerText = `Driver assigned! Your ${vehicleType} is on the way.`;

            // Save ride to local storage
            saveRideToHistory(pickup, dropoff, vehicleType, estimatedPrice);

            // Show Route
            if (routeControl) map.removeControl(routeControl);
            routeControl = L.Routing.control({
                waypoints: [
                    L.latLng(driverLatLng),
                    L.latLng(pickupLatLng),
                    L.latLng(dropoffLatLng)
                ],
                routeWhileDragging: true
            }).addTo(map);
        }

        // Save Ride to History
        function saveRideToHistory(pickup, dropoff, vehicleType, price) {
    const formData = new FormData();
    formData.append('pickup', pickup);
    formData.append('dropoff', dropoff);
    formData.append('vehicleType', vehicleType);
    formData.append('fare', price);

    // Send data to save_booking.php
    fetch('save_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}



        // View Ride History
        function viewRideHistory() {
            // Redirect to history page
            window.location.href = 'save_booking.php';
        }
    </script>
</body>
</html>