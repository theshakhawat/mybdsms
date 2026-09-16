<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="MyBDSMS - Premium SMS Service Provider in Bangladesh. We provide Non-Masking, Transactional SMS, OTP, Promotional SMS, and Bulk SMS services at competitive rates.">
    <meta name="keywords" content="bulk sms, bulk sms bangladesh, OTP sms, verification sms, bulk sms provider, sms provider in bangladesh, a2p sms, sms marketing, non-masking sms, mybdsms">
    <meta name="author" content="Freelancer Digital Expert">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MyBDSMS - Premium SMS Service Provider in Bangladesh')</title>

    {{-- <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}"> --}}

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')
</head>
<body>
    <!-- ===============================================
         Preloader
         =============================================== -->
    <div id="preloader">
        <div class="preloader-content">
            <img src="{{ asset('assets/images/logo.jpeg') }}" alt="MyBDSMS Logo" class="preloader-logo">
            <div class="preloader-spinner">
                <div class="spinner-circle"></div>
                <div class="spinner-circle"></div>
                <div class="spinner-circle"></div>
            </div>
            <p class="preloader-text">Loading...</p>
        </div>
    </div>

    <!-- ===============================================
         Header / Navigation
         =============================================== -->
    @include('components.header')

    <!-- ===============================================
         Main Content
         =============================================== -->
    @yield('content')

    <!-- ===============================================
         Footer
         =============================================== -->
    @include('components.footer')

    <!-- ===============================================
         Back to Top Button
         =============================================== -->
    <button id="back-to-top" aria-label="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- ===============================================
         Scripts
         =============================================== -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>
