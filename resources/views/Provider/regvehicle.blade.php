<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Vehicle - GoodsMover</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: url('https://images.unsplash.com/photo-1612817159948-986d79c9bfe9') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.75);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #f7931e;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #ffa640;
        }

        .error {
            color: #ff6b6b;
            font-size: 14px;
            margin-top: -15px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="overlay">
<form method="POST" action="{{ route('vehicle_register') }}" enctype="multipart/form-data" class="form-box">
    @csrf

    <h2>Register Vehicle</h2>

    <label for="vehicle_number">Vehicle Number</label>
    <input type="text" name="vehicle_number" placeholder="ABC-123" required>
    @error('vehicle_number') <div class="error">{{ $message }}</div> @enderror

    <label for="chassis_number">Chassis Number</label>
    <input type="text" name="chassis_number" placeholder="Enter chassis number" required>
    @error('chassis_number') <div class="error">{{ $message }}</div> @enderror

    <label for="vehicle_type">Vehicle Type</label>
    <input type="text" name="vehicle_type" placeholder="Truck / Loader " required>
    @error('vehicle_type') <div class="error">{{ $message }}</div> @enderror

    <label for="can_carry">Can Carry</label>
    <input type="text" name="can_carry" placeholder="E.g. Paints / Furniture / Stones" required>
    @error('can_carry') <div class="error">{{ $message }}</div> @enderror

    <label for="weight_capacity">Weight Capacity (kg)</label>
    <input type="number" name="weight_capacity" placeholder="e.g. 1500" required>
    @error('weight_capacity') <div class="error">{{ $message }}</div> @enderror

    <!-- NEW: Vehicle Image -->
    <label for="vehicle_image">Vehicle Picture (jpg, png)</label>
    <input type="file" name="vehicle_image" accept="image/*" required>
    @error('vehicle_image') <div class="error">{{ $message }}</div> @enderror

    <!-- NEW: Smart Card Image -->
    <label for="smartcard_image">Smart Card / RC Picture (jpg, png)</label>
    <input type="file" name="smartcard_image" accept="image/*" required>
    @error('smartcard_image') <div class="error">{{ $message }}</div> @enderror

    <button type="submit">Submit Vehicle Info</button>
</form>
</div>
</body>
</html>
