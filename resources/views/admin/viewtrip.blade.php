<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Vehicles - GoodsMover</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://images.unsplash.com/photo-1607011222719-9f9fc981b1ee') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.7);
            width: 100%;
            min-height: 100vh;
            padding: 40px;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            max-width: 1200px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #f7931e;
            color: white;
        }

        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #f7931e;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .back-btn:hover {
            background-color: #ffa640;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="container">
        <h2>Trip History</h2>
        <table>
            <thead>
                <tr>
                  
                    <th>Customer name</th>
                  
                    <th>Vehicle Type</th>
                    <th>Vehicle Number</th>
                    <th>Vehicle can_carry</th>
                     <th>Vehicle weight Capacity</th>
                    <th>Pickup Location</th>
                    <th>Drop Location</th>
                    <th>Trip Date</th>
                </tr>
            </thead>
            <tbody>
            @foreach($vehicles as $vehicle)
                <tr>
                    
                    <td>{{ $vehicle->customer->name }}</td>
                 
                    <td>{{ $vehicle->vehicle->vehicle_type }}</td>
                     <td>{{ $vehicle->vehicle->vehicle_number }}</td>
                      <td>{{ $vehicle->vehicle->can_carry }}</td>
                      <td>{{ $vehicle->vehicle->weight_capacity }}</td>
                    <td>{{ $vehicle->pickup_location }}</td>
                    <td>{{ $vehicle->dropoff_location }}</td>
                    <td>{{ $vehicle->booking_date }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="text-align: left;">
            <a href="{{ route('admin.login') }}" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
