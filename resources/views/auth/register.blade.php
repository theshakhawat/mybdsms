<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account & Verification - MyBDSMS</title>

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
            padding: 40px 15px;
        }

        .auth-container {
            width: 100%;
            max-width: 680px;
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

        .section-title-divider {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 20px 0 15px;
            padding-bottom: 6px;
            border-bottom: 2px dashed #e9ecef;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 6px;
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
            padding: 11px 14px;
            font-size: 14px;
            transition: all 0.3s ease;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .form-control.file-input {
            border-left: 2px solid #e9ecef !important;
            border-radius: 12px !important;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-auth {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #FAA03C 0%, #FDA537 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(250, 160, 60, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
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

        .doc-preview {
            max-width: 120px;
            max-height: 80px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 3px;
            display: none;
            margin-top: 8px;
            object-fit: cover;
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
                <h2>Create an Account</h2>
                <p>Register with your verification documents to get approved</p>
            </div>

            <div class="auth-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('user.registerPost') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Basic Information -->
                    <div class="section-title-divider mt-0">
                        <i class="fas fa-user-circle"></i> Basic Information
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="name">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" placeholder="Full name" required autofocus>
                            </div>
                            @error('name')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" placeholder="name@domain.com" required>
                            </div>
                            @error('email')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="phone">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}" placeholder="01700000000" required>
                            </div>
                            @error('phone')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="profile_image">Profile Photo <span class="text-muted small">(Optional)</span></label>
                            <input type="file" class="form-control file-input @error('profile_image') is-invalid @enderror"
                                   id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png,.webp">
                            <img id="profile_image_preview" class="doc-preview">
                            @error('profile_image')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Security / Password -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <div class="input-group position-relative">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="Min 8 chars" required>
                                <button type="button" class="btn-toggle-password" onclick="togglePassword('password', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group position-relative">
                                <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                                <input type="password" class="form-control pe-5"
                                       id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required>
                                <button type="button" class="btn-toggle-password" onclick="togglePassword('password_confirmation', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Documents (KYC) -->
                    <div class="section-title-divider">
                        <i class="fas fa-id-card"></i> Verification Documents (KYC)
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="nid_front">NID Front Side <span class="text-danger">*</span></label>
                            <input type="file" class="form-control file-input @error('nid_front') is-invalid @enderror"
                                   id="nid_front" name="nid_front" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="text-muted">JPG, PNG, PDF (Max 3MB)</small>
                            <img id="nid_front_preview" class="doc-preview">
                            @error('nid_front')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 form-group">
                            <label for="nid_back">NID Back Side <span class="text-danger">*</span></label>
                            <input type="file" class="form-control file-input @error('nid_back') is-invalid @enderror"
                                   id="nid_back" name="nid_back" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="text-muted">JPG, PNG, PDF (Max 3MB)</small>
                            <img id="nid_back_preview" class="doc-preview">
                            @error('nid_back')
                                <div class="text-danger mt-1 small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="trade_license">Trade License <span class="text-muted small">(Optional for Business Accounts)</span></label>
                        <input type="file" class="form-control file-input @error('trade_license') is-invalid @enderror"
                               id="trade_license" name="trade_license" accept=".jpg,.jpeg,.png,.pdf">
                        <small class="text-muted">JPG, PNG, PDF (Max 3MB)</small>
                        <img id="trade_license_preview" class="doc-preview">
                        @error('trade_license')
                            <div class="text-danger mt-1 small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info border-0 rounded-3 p-3 mt-3">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fas fa-info-circle fa-lg mt-1 text-primary"></i>
                            <div class="small">
                                After registration, your documents will be reviewed by our admin team. Once approved, your account will be activated and you will be able to login.
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-auth">
                        <i class="fas fa-user-check"></i>
                        Register & Submit for Verification
                    </button>
                </form>

                <div class="auth-footer">
                    <p>Already have an account? <a href="{{ route('user.login') }}">Sign In</a></p>
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

        // Image Previews
        ['profile_image', 'nid_front', 'nid_back', 'trade_license'].forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                input.addEventListener('change', function(e) {
                    const preview = document.getElementById(field + '_preview');
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                        }
                        reader.readAsDataURL(file);
                    } else {
                        preview.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html>
