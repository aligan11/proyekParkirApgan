<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <script src="https://cdn.tailwindcss.com">
  </script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&amp;display=swap" rel="stylesheet"/>
  <title>Document</title>
</head>
<body class="bg-[url('https://www.transparenttextures.com/patterns/axiom-pattern.png')] bg-repeat bg-gray-100 min-h-screen">

  <nav class="bg-blue-900 text-white p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
      <a href="{{ route('home') }}"><h1 class="text-2xl font-bold">GanParking</h1></a> 
        <ul class="flex space-x-6">
            <li><a href="#" class="hover:text-gray-300">Beranda</a></li>
            <li><a href="#" class="hover:text-gray-300">Fitur</a></li>
            <li><a href="#" class="hover:text-gray-300">Kontak</a></li>
            <li><a href="#" class="hover:text-gray-300">Masuk</a></li>
        </ul>
    </div>
</nav>
  
<div class="max-w-4xl mx-auto h-[90vh] mt-10 bg-white/80 p-8 rounded-3xl shadow-2xl backdrop-blur-md">
  <h2 class="text-3xl font-bold mb-6 text-center text-gray-800 tracking-wide">Form Registrasi</h2>
  
  <form class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
    <!-- Kolom Kiri -->
    <div class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold text-sm">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-sm">Tempat Lahir</label>
        <input type="text" name="tempat_lahir" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-sm">Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-sm">Jenis Kelamin</label>
        <select name="jenis_kelamin" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required>
          <option value="">-- Pilih --</option>
          <option value="Laki-laki">Laki-laki</option>
          <option value="Perempuan">Perempuan</option>
        </select>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-sm">Agama</label>
        <select name="agama" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required>
          <option value="">-- Pilih --</option>
          <option value="Islam">Islam</option>
          <option value="Kristen">Kristen</option>
          <option value="Katolik">Katolik</option>
          <option value="Hindu">Hindu</option>
          <option value="Budha">Budha</option>
          <option value="Lainnya">Lainnya</option>
        </select>
      </div>
    </div>

    <!-- Kolom Kanan -->
    <div class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold text-sm">Alamat</label>
        <textarea name="alamat" rows="3" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required></textarea>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-sm">Username</label>
        <input type="text" name="username" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-sm">No. Handphone</label>
        <input type="tel" name="handphone" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required>
      </div>
      <div>
        <label class="block mb-1 font-semibold text-sm">Password Default</label>
        <input type="password" name="password" value="12345678" readonly class="w-full border border-gray-200 p-2.5 rounded-lg bg-gray-100 text-gray-500 cursor-not-allowed">
      </div>
      <div>
        <label class="block mb-1 font-semibold text-sm">Upload Foto</label>
        <input type="file" name="foto" accept="image/*" class="w-full border border-gray-300 p-2.5 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200" required>
      </div>
    </div>

    <!-- Tombol Submit -->
    <div class="md:col-span-2 text-center mt-4">
      <button type="submit" class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-8 py-3 rounded-md shadow-lg hover:from-blue-600 hover:to-indigo-700 transition duration-300">
        Daftar
      </button>
    </div>
  </form>
</div>




<footer class="bg-gray-100 text-gray-600 mt-10 py-6 min-h-[80px]">
  <div class="max-w-6xl mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-6 text-sm h-full">
    <p class="text-center md:text-left">&copy; 2025 Sistem Parkir | All rights reserved.</p>
    <div class="flex space-x-6">
      <a href="#" class="hover:text-blue-600 transition duration-200">Beranda</a>
      <a href="#" class="hover:text-blue-600 transition duration-200">Tentang</a>
      <a href="#" class="hover:text-blue-600 transition duration-200">Kontak</a>
    </div>
  </div>
</footer>





</body>
</html>