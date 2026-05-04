<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - GoodsMover</title>
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
            max-width: 400px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }

        .form-container h2 {
            margin-bottom: 25px;
            text-align: center;
        }

        input[type="email"], input[type="password"] {
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
        <h2>Login</h2>

        <form method="POST" action="{{ route('user.login') }}">
            @csrf

            <input type="email" name="email" placeholder="Enter Email" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <input type="password" name="password" placeholder="Enter Password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <button type="submit">Login</button>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif
        </form>
    </div>
</div>
</body>
</html>
