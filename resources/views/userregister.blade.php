<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - GoodsMover</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://images.unsplash.com/photo-1564013799919-ab600027ffc6') no-repeat center center fixed;
            background-size: cover;
            color: white;
        }

        .overlay {
            background-color: rgba(0,0,0,0.7);
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background: rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }

        .form-container h2 {
            margin-bottom: 25px;
            text-align: center;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border: none;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #f7931e;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        button:hover {
            background-color: #ffa640;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: -15px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="form-container">
        <h2>User Registration</h2>

        <form method="POST" action="{{ route('user.register') }}">
            @csrf

            <input type="text" name="name" placeholder="Full Name" value="{{ old('name') }}" required>
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <input type="text" name="cnic" placeholder="CNIC" value="{{ old('cnic') }}" required>
            @error('cnic') <div class="error">{{ $message }}</div> @enderror

            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="provider" {{ old('role') == 'provider' ? 'selected' : '' }}>Service Provider</option>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
            @error('role') <div class="error">{{ $message }}</div> @enderror

            <input type="password" name="password" placeholder="Password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

            <button type="submit">Register</button>
        </form>
    </div>
</div>
</body>
</html>
