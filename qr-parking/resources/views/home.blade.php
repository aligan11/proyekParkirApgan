<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - {{ $nama_aplikasi ?? 'GANPARKING' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: "class"
        };
    </script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: "Roboto", sans-serif;
        }
    </style>
</head>

<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-500 ease-in-out">
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Login',
                html: `{!! implode('<br>', $errors->all()) !!}`,
                confirmButtonColor: '#d33'
            });
        </script>
    @endif

    <!-- Navbar -->
    <nav
        class="text-white p-4 shadow-md dark:bg-gray-900 bg-white bg-[url('https://www.transparenttextures.com/patterns/axiom-pattern.png')] bg-repeat">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-parking text-[#d32f2f] text-2xl mr-2"></i>
                <h1 class="text-2xl font-bold tracking-wider text-[#d32f2f]">{{ $nama_aplikasi ?? 'GANPARKING' }}</h1>
            </div>
            <div id="theme-toggle"
                class="flex items-center space-x-2 bg-gray-100 dark:bg-gray-800 rounded-full px-4 py-2 shadow-md cursor-pointer transition-colors"
                onclick="toggleMode()">
                <i id="theme-icon" class="fas fa-moon text-yellow-500 dark:text-yellow-300"></i>
                <span id="modeText" class="text-sm font-semibold">Night Mode</span>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="relative">
        <div
            class="w-full h-[90vh] bg-[url('https://www.transparenttextures.com/patterns/axiom-pattern.png')] bg-repeat flex items-center justify-center">
            <div
                class="max-w-5xl w-full bg-gray-100 dark:bg-gray-800 rounded-xl shadow-xl flex flex-col md:flex-row overflow-hidden">
                <!-- Left Side -->
                <div class="w-full md:w-1/2 p-10 md:px-16 md:py-14 flex flex-col">
                    <h6 class="text-[10px] text-gray-600 dark:text-gray-100 font-semibold tracking-widest mb-2">WELCOME
                        TO</h6>

                    <!-- App Branding -->
                    <div class="flex items-center mb-6">
                        @if ($logo_aplikasi)
                            <img src="{{ asset('storage/' . $logo_aplikasi) }}" alt="Logo"
                                class="w-10 h-10 rounded-full mr-3">
                        @else
                            <i class="fas fa-parking text-[#d32f2f] text-2xl mr-2"></i>
                        @endif
                        <div>
                            <h1 class="text-[#d32f2f] font-extrabold text-xl tracking-wide">
                                {{ $nama_aplikasi ?? 'GANPARKING' }}</h1>
                        </div>
                    </div>

                    <p class="text-[11px] text-gray-600 dark:text-gray-100 mb-8 leading-tight">Login to access the
                        real-time monitoring & management system.</p>

                    <form method="POST" action="{{ route('loginAdmin') }}" class="flex flex-col space-y-4">
                        @csrf
                        <div class="relative">
                            <input name="username"
                                class="w-full border border-gray-300 rounded-full py-2 pl-10 pr-4 text-xs text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#d32f2f]"
                                placeholder="Username" type="text" />
                            <i
                                class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                        </div>
                        <div class="relative">
                            <input name="password" id="passwordInput"
                                class="w-full border border-gray-300 rounded-full py-2 pl-10 pr-10 text-xs text-gray-600 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#d32f2f]"
                                placeholder="Password" type="password" />
                            <i
                                class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                            <button id="togglePassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"
                                type="button">
                                <i id="eyeIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        <!-- Role Select -->
                       

                        <button
                            class="bg-[#d32f2f] text-white text-xs font-semibold rounded-full py-2 mt-1 hover:bg-[#b71c1c] transition-colors"
                            type="submit">SIGN IN</button>

                        <!-- Info Admin -->
                        <div class="flex items-center my-6">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span
                                class="mx-4 text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-widest">INFO
                                ADMIN</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>

                        <div class="text-[11px] text-gray-600 dark:text-gray-300 leading-snug space-y-2">
                            <p><i class="fas fa-user-shield mr-1 text-[#d32f2f]"></i> Akses hanya untuk admin & petugas
                                resmi dari <strong>{{ $owner_parkir ?? 'Gan Company' }}</strong>.</p>
                            <p><i class="fas fa-map-marker-alt mr-1 text-[#d32f2f]"></i>
                                {{ $alamat_perusahaan ?? 'Jl. Default No. 1, Jakarta' }}</p>
                            <p><i class="fas fa-phone-alt mr-1 text-[#d32f2f]"></i>
                                {{ $no_perusahaan ?? '08123456789' }}</p>
                            <p><i class="fas fa-envelope mr-1 text-[#d32f2f]"></i> Email bantuan: <a
                                    href="mailto:{{ $email_perusahaan }}"
                                    class="underline hover:text-[#b71c1c]">{{ $email_perusahaan ?? 'support@ganparking.id' }}</a>
                            </p>
                        </div>



                        <!-- Motivation -->
                        <div class="mt-4 text-[11px] italic text-gray-500 dark:text-gray-400 text-center">
                            <i class="fas fa-quote-left mr-1"></i>“Menjadi admin bukan hanya mengelola, tapi menjaga
                            kepercayaan.”<i class="fas fa-quote-right ml-1"></i>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 text-[10px] text-center text-gray-400 dark:text-gray-500">
                            &copy; 2025 {{ $nama_aplikasi ?? 'GANPARKING' }} - {{ $owner_parkir ?? 'Gan Company' }}
                        </div>
                    </form>
                </div>

                <!-- Right Side -->
                <div class="w-full md:w-1/2 relative overflow-hidden rounded-r-xl">
                    <div
                        class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/brick-wall.png')] bg-repeat bg-red-700 rounded-r-xl flex flex-col items-center justify-center px-8 text-center text-white">
                        <i class="fas fa-warehouse text-4xl mb-3"></i>
                        <h2 class="text-3xl font-extrabold mb-2 tracking-wide">PARK SMARTER</h2>
                        <p class="text-[11px] max-w-xs leading-tight">Manage your parking with real-time data and
                            barcode validation sy   stem — simple, secure, and fast.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const toggleBtn = document.getElementById("theme-toggle");
        const htmlTag = document.documentElement;
        const icon = document.getElementById("theme-icon");
        const text = document.getElementById("modeText");

        toggleBtn.addEventListener("click", () => {
            htmlTag.classList.toggle("dark");
            if (htmlTag.classList.contains("dark")) {
                icon.classList.remove("fa-sun");
                icon.classList.add("fa-moon");
                text.innerText = "Night Mode";
                text.style.color ="#fff";

            } else {
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
                text.innerText = "Light Mode";
                text.style.color ="#000";
            }
        });

        window.addEventListener("DOMContentLoaded", () => {
            if (htmlTag.classList.contains("dark")) {
                icon.classList.remove("fa-sun");
                icon.classList.add("fa-moon");
            } else {
                icon.classList.remove("fa-moon");
                icon.classList.add("fa-sun");
            }
        });

        const passwordInput = document.getElementById("passwordInput");
        const togglePassword = document.getElementById("togglePassword");
        const eyeIcon = document.getElementById("eyeIcon");

        togglePassword.addEventListener("click", function() {
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            eyeIcon.classList.toggle("fa-eye");
            eyeIcon.classList.toggle("fa-eye-slash");
        });
    </script>
</body>

</html>
