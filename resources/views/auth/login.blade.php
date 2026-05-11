@extends('partials.layouts.master-auth')

@section('title', 'Sign In | SIMAK ')

@section('css')
    @include('partials.head-css', ['auth' => 'layout-auth'])
@endsection

@section('content')

    <!-- START -->
    <div class="account-pages">
        <img src="{{ asset('assets/images/auth/auth_bg.jpeg') }}" alt="auth_bg" class="auth-bg light">
        <img src="{{ asset('assets/images/auth/auth_bg_dark.jpg') }}" alt="auth_bg_dark" class="auth-bg dark">
        <div class="container">
            <div class="justify-content-center row gy-0">

                <div class="col-lg-6 auth-banners">
                    <div class="bg-login card card-body m-0 h-100 border-0" style="background: linear-gradient(180deg, #1a1a2e 0%, #0f3460 100%);">
                        <svg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                            <!-- Background gradient -->
                            <linearGradient id="bgGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color:#1a1a2e;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#0f3460;stop-opacity:1" />
                            </linearGradient>
                            
                            <!-- Gradient definitions -->
                            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                            </linearGradient>
                            <linearGradient id="grad2" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#f093fb;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#f5576c;stop-opacity:1" />
                            </linearGradient>
                            <linearGradient id="grad3" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#4facfe;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#00f2fe;stop-opacity:1" />
                            </linearGradient>
                            <linearGradient id="grad4" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#43e97b;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#38f9d7;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        
                        <!-- Background -->
                        <rect width="400" height="400" fill="url(#bgGrad)"/>
                        
                        <!-- Title Section -->
                        <text x="200" y="35" font-family="Arial, sans-serif" font-size="28" font-weight="bold" 
                                fill="url(#grad1)" text-anchor="middle">
                            SIMAK
                            <animate attributeName="opacity" values="1;0.8;1" dur="3s" repeatCount="indefinite"/>
                        </text>
                        
                        <text x="200" y="60" font-family="Arial, sans-serif" font-size="14" 
                                fill="#a0a0b0" text-anchor="middle" opacity="0.9">
                            Sistem Informasi Monitoring & Analisa Kinerja
                        </text>
                        
                        <!-- Trophy Icon (Top Left) -->
                        <g transform="translate(80, 120)">
                            <circle cx="0" cy="0" r="45" fill="url(#grad1)" opacity="0.2">
                            <animate attributeName="r" values="45;50;45" dur="2s" repeatCount="indefinite"/>
                            </circle>
                            <path d="M-15,-20 L-15,-10 Q-15,0 -10,5 L-10,15 L-15,15 L-15,20 L15,20 L15,15 L10,15 L10,5 Q15,0 15,-10 L15,-20 Z" 
                                fill="url(#grad1)" stroke="#fff" stroke-width="2">
                            <animateTransform attributeName="transform" type="rotate" 
                                                values="0 0 0;-5 0 0;5 0 0;0 0 0" dur="3s" repeatCount="indefinite"/>
                            </path>
                            <ellipse cx="0" cy="22" rx="18" ry="3" fill="url(#grad1)" opacity="0.6"/>
                            <circle cx="-20" cy="-15" r="8" fill="none" stroke="url(#grad1)" stroke-width="2">
                            <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="20" cy="-15" r="8" fill="none" stroke="url(#grad1)" stroke-width="2">
                            <animate attributeName="opacity" values="1;0.3;1" dur="2s" repeatCount="indefinite"/>
                            </circle>
                        </g>
                        
                        <!-- Target Icon (Top Right) -->
                        <g transform="translate(320, 120)">
                            <circle cx="0" cy="0" r="45" fill="url(#grad2)" opacity="0.2">
                            <animate attributeName="opacity" values="0.2;0.4;0.2" dur="2.5s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="0" cy="0" r="25" fill="none" stroke="url(#grad2)" stroke-width="3"/>
                            <circle cx="0" cy="0" r="15" fill="none" stroke="url(#grad2)" stroke-width="3"/>
                            <circle cx="0" cy="0" r="5" fill="url(#grad2)">
                            <animate attributeName="r" values="5;7;5" dur="1.5s" repeatCount="indefinite"/>
                            </circle>
                            <path d="M0,-35 L0,-25" stroke="url(#grad2)" stroke-width="2">
                            <animateTransform attributeName="transform" type="rotate" 
                                                from="0 0 0" to="360 0 0" dur="4s" repeatCount="indefinite"/>
                            </path>
                        </g>
                        
                        <!-- Growth Chart Icon (Bottom Left) -->
                        <g transform="translate(80, 270)">
                            <circle cx="0" cy="0" r="45" fill="url(#grad3)" opacity="0.2"/>
                            <rect x="-25" y="10" width="10" height="15" fill="url(#grad3)" rx="2">
                            <animate attributeName="height" values="15;20;15" dur="2s" repeatCount="indefinite"/>
                            <animate attributeName="y" values="10;5;10" dur="2s" repeatCount="indefinite"/>
                            </rect>
                            <rect x="-8" y="0" width="10" height="25" fill="url(#grad3)" rx="2">
                            <animate attributeName="height" values="25;30;25" dur="2s" begin="0.3s" repeatCount="indefinite"/>
                            <animate attributeName="y" values="0;-5;0" dur="2s" begin="0.3s" repeatCount="indefinite"/>
                            </rect>
                            <rect x="9" y="-10" width="10" height="35" fill="url(#grad3)" rx="2">
                            <animate attributeName="height" values="35;40;35" dur="2s" begin="0.6s" repeatCount="indefinite"/>
                            <animate attributeName="y" values="-10;-15;-10" dur="2s" begin="0.6s" repeatCount="indefinite"/>
                            </rect>
                            <path d="M-20,15 L-3,5 L12,-5" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round">
                            <animate attributeName="stroke-dasharray" values="0,100;50,0" dur="2s" repeatCount="indefinite"/>
                            </path>
                        </g>
                        
                        <!-- Star Rating Icon (Bottom Right) -->
                        <g transform="translate(320, 270)">
                            <circle cx="0" cy="0" r="45" fill="url(#grad4)" opacity="0.2">
                            <animate attributeName="r" values="45;48;45" dur="3s" repeatCount="indefinite"/>
                            </circle>
                            <g>
                            <path d="M0,-20 L5,-6 L20,-6 L8,3 L13,17 L0,8 L-13,17 L-8,3 L-20,-6 L-5,-6 Z" 
                                    fill="url(#grad4)" stroke="#fff" stroke-width="2">
                                <animateTransform attributeName="transform" type="scale" 
                                                values="1;1.15;1" dur="2s" repeatCount="indefinite"/>
                                <animate attributeName="opacity" values="1;0.7;1" dur="2s" repeatCount="indefinite"/>
                            </path>
                            </g>
                        </g>
                        
                        <!-- Center Speedometer Icon -->
                        <g transform="translate(200, 200)">
                            <circle cx="0" cy="0" r="50" fill="url(#grad1)" opacity="0.1">
                            <animate attributeName="r" values="50;55;50" dur="3s" repeatCount="indefinite"/>
                            </circle>
                            <path d="M-30,10 Q-30,-20 0,-35 Q30,-20 30,10 Z" fill="none" stroke="url(#grad1)" stroke-width="3"/>
                            <circle cx="-20" cy="-5" r="3" fill="url(#grad1)"/>
                            <circle cx="0" cy="-20" r="3" fill="url(#grad1)"/>
                            <circle cx="20" cy="-5" r="3" fill="url(#grad1)"/>
                            <line x1="0" y1="0" x2="15" y2="-15" stroke="url(#grad2)" stroke-width="3" stroke-linecap="round">
                            <animateTransform attributeName="transform" type="rotate" 
                                                values="-45 0 0;45 0 0;-45 0 0" dur="4s" repeatCount="indefinite"/>
                            </line>
                            <circle cx="0" cy="0" r="5" fill="url(#grad2)"/>
                        </g>
                        
                        <!-- Floating particles -->
                        <circle cx="150" cy="50" r="3" fill="url(#grad3)" opacity="0.6">
                            <animate attributeName="cy" values="50;40;50" dur="3s" repeatCount="indefinite"/>
                            <animate attributeName="opacity" values="0.6;0.2;0.6" dur="3s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="250" cy="350" r="3" fill="url(#grad2)" opacity="0.6">
                            <animate attributeName="cy" values="350;340;350" dur="2.5s" repeatCount="indefinite"/>
                            <animate attributeName="opacity" values="0.6;0.2;0.6" dur="2.5s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="50" cy="200" r="2" fill="url(#grad4)" opacity="0.5">
                            <animate attributeName="cx" values="50;60;50" dur="4s" repeatCount="indefinite"/>
                            <animate attributeName="opacity" values="0.5;0.1;0.5" dur="4s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="350" cy="180" r="2" fill="url(#grad1)" opacity="0.5">
                            <animate attributeName="cx" values="350;340;350" dur="3.5s" repeatCount="indefinite"/>
                            <animate attributeName="opacity" values="0.5;0.1;0.5" dur="3.5s" repeatCount="indefinite"/>
                        </circle>
                        </svg>

                        <div class="auth-contain">
                            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                                        class="active" aria-current="true" aria-label="Slide 1"></button>
                                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                                        aria-label="Slide 2"></button>
                                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                                        aria-label="Slide 3"></button>
                                </div>
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <div class="text-center text-white my-4 p-4">
                                            <h3 class="text-white">Manajemen Pegawai</h3>
                                            <p class="mt-3">
                                                <!-- Manage your application seamlessly. Log in to access your dashboard and configure settings. -->
                                                 SiMAK sudah memetakan akses masing-masing cabang, untuk memudahkan proses dan monitoring data.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <div class="text-center text-white my-4 p-4">
                                            <h3 class="text-white">Generate Dokumen</h3>
                                            <p class="mt-3">
                                                SIMAK mampu melakukan generate form simkp pegawai.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <div class="text-center text-white my-4 p-4">
                                            <h3 class="text-white">Perhitungan Talenta</h3>
                                            <p class="mt-3">
                                                SIMAK mampu melakukan perhitungan nilai kompetensi masing pegawai yang dikonversi menjadi talenta. 
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="auth-box card card-body m-0 h-100 border-0 justify-content-center">
                        <div class="mb-5 text-center">
                            <img height="60" alt="Logo" src="{{ asset('assets/images/pcnnew.png') }}">
                            <!-- <p class="text-muted mb-0 fs-18">Pemerintah kabupaten Nunukan</p> -->
                             <!-- <h4 class="fw-normal mt-1">Pemerintah kabupaten Nunukan</h4> -->
                        </div>
                        <div class="mb-5 text-center"></div>
                        <div class="mb-5 text-center">
                            <h4 class="fw-normal">Welcome to <span class="fw-bold text-primary">SIMAK</span></h4>
                            <p class="text-muted mb-0">
                                <!-- Sistem Informasi Surat Perjalanan Dinas -->
                                Silahkan login menggunakan akun anda.
                            </p>
                        </div>
                        <form method="POST" action="{{ url('/login') }}" class="form-custom mt-10" id="formLogin">
                            @csrf

                            <div class="mb-5">
                                <label class="form-label" for="username">Username<span class="text-danger ms-1">*</span>
                                </label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
                            </div>

                            <div class="mb-5">
                                <label class="form-label" for="password">Password<span
                                        class="text-danger ms-1">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="Enter your password" data-visible="false">
                                    <a class="input-group-text bg-transparent toggle-password" href="javascript:;"
                                        data-target="password">
                                        <i class="ri-eye-off-line text-muted toggle-icon"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="row mb-5">
                                <div class="col-sm-6">
                                    <div class="form-check form-check-sm d-flex align-items-center gap-2 mb-0">
                                        <input class="form-check-input" type="checkbox" value="remember-me"
                                            id="remember-me">
                                        <label class="form-check-label" for="remember-me">
                                            Remember me
                                        </label>
                                    </div>
                                </div>
                                <a href="auth-reset-password" class="col-sm-6 text-end">
                                    <span class="fs-14 text-muted">
                                        Forgot your password?
                                    </span>
                                </a>
                            </div>

                            <a href="index">
                                <button type="submit" class="btn btn-primary w-100">Login</button>

                                <!-- <button type="submit" class="btn btn-primary rounded-2 w-100 btn-loader">
                                    <span class="indicator-label">
                                        Sign In
                                    </span>
                                    <span class="indicator-progress flex gap-2 justify-content-center w-100">
                                        <span>Please Wait...</span>
                                        <i class="ri-loader-2-fill"></i>
                                    </span>
                                </button> -->
                            </a>
                            <!-- <div class="center-hr my-10 text-nowrap text-muted">Or with email</div>

                            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2">
                                <button type="button" class="btn btn-outline-facebook icon-btn">
                                    <i class="ri-facebook-fill"></i>
                                </button>
                                <button type="button" class="btn btn-outline-google icon-btn">
                                    <i class="ri-google-fill"></i>
                                </button>
                                <button type="button" class="btn btn-outline-twitter icon-btn">
                                    <i class="ri-twitter-fill"></i>
                                </button>
                                <button type="button" class="btn btn-outline-instagram icon-btn">
                                    <i class="ri-instagram-fill"></i>
                                </button>
                            </div>
                            <p class="mb-0 mt-5 text-muted text-center">
                                Don't have an account ?
                                <a href="auth-signup" class="text-primary fw-medium text-decoraton-underline ms-1">
                                    Sign up
                                </a>
                            </p> -->
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('js')

    <!-- App js -->
    <script type="module" src="{{ asset('assets/js/app.js') }}"></script>
@endsection
