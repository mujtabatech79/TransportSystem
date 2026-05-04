<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Trip - GoodsMover</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://images.unsplash.com/photo-1607011222719-9f9fc981b1ee') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            color: white;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.75);
            min-height: 100vh;
            padding: 50px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: none;
            margin-bottom: 15px;
        }

        button {
            padding: 12px 20px;
            background-color: #f7931e;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #ffa640;
        }

        .back-btn {
            display: inline-block;
            margin-top: 20px;
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="container">
        
        <h2>Book Your Trip</h2>

        <form action="{{ route('trip.submit') }}" method="POST">
            @csrf
            
            <input type="hidden" name="vehicle_id" value="{{ request('vehicle_id') }}">
            
            <label for="trip_date">Trip Date:</label>
            <input type="date" name="trip_date" id="trip_date" required>
            
            <label for="pickup_location">Pickup Location:</label>
            <input type="text" name="pickup_location" id="pickup_location" required>

            <label for="drop_location">Drop Location:</label>
            <input type="text" name="drop_location" id="drop_location" required>

            <button type="submit">Confirm Booking</button>
        </form>

        <a href="{{ route('customer.login') }}" class="back-btn">← Back to Vehicles</a>
    </div>
</div>
</body>
</html>
