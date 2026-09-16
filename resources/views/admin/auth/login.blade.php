<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - MyBDSMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #34BD93;
            --primary-dark: #2a9a7a;
            --secondary-color: #FAA03C;
            --accent-color: #FDA537;
            --text-dark: #1a1a2e;
            --text-light: #6c757d;
            --bg-light: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #34BD93 0%, #2db184 50%, #FAA03C 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);
            padding: 40px 30px;
            text-align: center;
        }

        .login-logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 15px;
            padding: 10px;
            margin: 0 auto 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .login-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .login-header h2 {
            color: white;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .login-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin: 0;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: var(--bg-light);
            border: 2px solid #e9ecef;
            border-right: none;
            color: var(--text-light);
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-left: none;
            padding: 12px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 189, 147, 0.25);
        }

        .input-group .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .input-group-text {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .form-control {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .form-check {
            margin-bottom: 20px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #e9ecef;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            font-size: 13px;
            color: var(--text-light);
            margin-left: 8px;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FAA03C 0%, #FDA537 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(250, 160, 60, 0.4);
        }

        .alert {
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background: #fee;
            border: none;
            color: #c33;
        }

        @media (max-width: 575px) {
            .login-header {
                padding: 30px 20px;
            }

            .login-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" alt="MyBDSMS Logo">
                </div>
                <h2>Admin Panel</h2>
                <p>MyBDSMS - SMS Service Provider</p>
            </div>

            <div class="login-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.authenticate') }}">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="Email address"
                                   required
                                   autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your password"
                                   required>
                        </div>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
