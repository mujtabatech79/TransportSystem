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
            background-color: rgba(0,0,0,0.7);
            width: 100%;
            min-height: 100vh;
            padding: 40px;
        }

        .container {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 15px;
            max-width: 1200px;
            margin: auto;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
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
        }

        img {
            max-width: 100px;
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
        <h2>Customer Available Vehicles</h2>

        @if($availableVehicles->isEmpty())
            <p>No available vehicles at the moment.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>Driver Name</th>
                    <th>Driver email</th>
                    <th>Driver cnic</th>
                    <th>Vehicle Type</th>
                    <th>Vehicle Number</th>
                    <th>Weight Capacity</th>
                    <th>Can Carry</th>
                    <th>chasis number</th>
                    <th>vehicle image</th>
                    
                    
                </tr>
            </thead>
            <tbody>
            @foreach($availableVehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->user->name }}</td>
                    <td>{{ $vehicle->user->email }}</td>
                    <td>{{ $vehicle->user->cnic }}</td>
                    <td>{{ $vehicle->vehicle_type }}</td>
                    <td>{{ $vehicle->vehicle_number }}</td>
                    <td>{{ $vehicle->weight_capacity }}</td>
                    <td>{{ $vehicle->can_carry }}</td>
                    <td>{{ $vehicle->chassis_number }}</td>
                     <td>{{ $vehicle->vehicle_image }}</td>
                    
                 <td>
                     <form action="{{ route('trip.form') }}" method="GET">
                        <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                        <button type="submit">Book Now</button>
                    </form>
                    </td>
                  
                </tr>
            @endforeach
            </tbody>
        </table>
        @endif

        <div style="text-align: left;">
            <a href="{{ route('customer.login') }}" class="back-btn">← Back to A_dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
