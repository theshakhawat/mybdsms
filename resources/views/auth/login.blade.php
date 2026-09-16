<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Login - MyBDSMS</title>

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
            padding: 25px 15px;
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            background: linear-gradient(135deg, #34BD93 0%, #FAA03C 100%);
            padding: 35px 30px;
            text-align: center;
            position: relative;
        }

        .auth-logo {
            width: 75px;
            height: 75px;
            background: white;
            border-radius: 16px;
            padding: 10px;
            margin: 0 auto 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .auth-header h2 {
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .auth-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13.5px;
            margin: 0;
        }

        .auth-body {
            padding: 35px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
            display: block;
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: var(--bg-light);
            border: 2px solid #e9ecef;
            border-right: none;
            color: var(--text-light);
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
            padding: 10px 14px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-left: none;
            padding: 12px 14px;
            font-size: 14px;
            transition: all 0.3s ease;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .form-check {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #ced4da;
            margin-top: 0;
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            font-size: 13.5px;
            color: var(--text-light);
            margin-left: 8px;
            cursor: pointer;
        }

        .btn-auth {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #FAA03C 0%, #FDA537 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(250, 160, 60, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(250, 160, 60, 0.45);
            color: white;
        }

        .btn-toggle-password {
            background: transparent;
            border: none;
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            color: var(--text-light);
            cursor: pointer;
        }

        .auth-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: var(--text-light);
        }

        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .auth-footer a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .home-link {
            text-align: center;
            margin-top: 15px;
        }

        .home-link a {
            color: rgba(255, 255, 255, 0.85);
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .home-link a:hover {
            color: white;
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13.5px;
            border: none;
        }

        .alert-danger {
            background: #ffebe9;
            color: #d93025;
        }

        .alert-success {
            background: #e6f4ea;
            color: #137333;
        }

        .invalid-feedback {
            font-size: 12.5px;
            margin-top: 4px;
        }

        @media (max-width: 575px) {
            .auth-header {
                padding: 25px 20px;
            }

            .auth-body {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="{{ asset('assets/images/logo.jpeg') }}" alt="MyBDSMS Logo">
                </div>
                <h2>Welcome Back!</h2>
                <p>Login to manage your SMS campaigns & account</p>
            </div>

            <div class="auth-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('user.loginPost') }}">
                    @csrf

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
                                   placeholder="Enter your email"
                                   required
                                   autofocus>
                        </div>
                        @error('email')
                            <div class="text-danger mt-1 small">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group position-relative">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password"
                                   class="form-control pe-5 @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Enter your password"
                                   required>
                            <button type="button" class="btn-toggle-password" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger mt-1 small">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-auth">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="{{ route('user.register') }}">Create Account</a></p>
                </div>
            </div>
        </div>

        <div class="home-link">
            <a href="{{ route('home') }}">
                <i class="fas fa-arrow-left me-1"></i> Back to Homepage
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

