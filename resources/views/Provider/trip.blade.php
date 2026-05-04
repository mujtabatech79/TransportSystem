<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Trips - GoodsMover</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            padding: 40px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            max-width: 900px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background-color: #f7931e;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Trips Booked by Customers</h2>

    @if($bookings->isEmpty())
        <p style="text-align: center;">No bookings yet.</p>
    @else
    <h1>Congratulations you have a trip</h1>
        <table>
            <thead>
                <tr>
                    <th>vehicle image</th>
                    <th>Customer Name</th>
                    <th>Vehicle Name</th>
                    <th>Vehicle Number</th>
                    <th>Weight carry</th>
                    <th>Can carry</th>
                    <th>Booking Date</th>
                    <th>Pickup Location</th>
                    <th>Drop Location</th>
                    <th>customer email</th>
                    <th>custome cnic</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td>{{ $booking->vehicle->vehicl_image }}</td>
                        <td>{{ $booking->customer->name }}</td>
                         <td>{{ $booking->vehicle->vehicle_type }}</td>
                         <td>{{ $booking->vehicle->vehicle_number }}</td>
                         <td>{{ $booking->vehicle->weight_capacity }}</td>
                          <td>{{ $booking->vehicle->can_carry }}</td>
                        <td>{{ $booking->booking_date }}</td>
                        <td>{{ $booking->pickup_location }}</td>
                        <td>{{ $booking->dropoff_location }}</td>
                        <td>{{ $booking->customer->email }}</td>
                        <td>{{ $booking->customer->cnic }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>
