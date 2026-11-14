<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Parking System Dashboard</title>
    {{-- <link
      href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"
      rel="stylesheet"
    /> --}}
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/daisyui@2.51.3/dist/full.js"></script>

  </head>
  @extends('app')
@section('content')
  <body class="bg-gray-900 text-white">
    


    <div class="container mx-auto my-4 h-[75vh] mt-20">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-blue-600 p-4 rounded-lg shadow">
          <h5 class="font-bold">Waktu Saat Ini</h5>
          <p id="current-time" class="text-2xl">--:--:--</p>
        </div>
        <div class="bg-green-600 p-4 rounded-lg shadow">
          <h5 class="font-bold">Total Anggota</h5>
          <p id="total-members" class="text-2xl">0</p>
        </div>
        <div class="bg-yellow-500 p-4 rounded-lg shadow">
          <h5 class="font-bold">Top Up Hari Ini</h5>
          <p id="total-topups" class="text-2xl">0</p>
        </div>
        <div class="bg-red-600 p-4 rounded-lg shadow">
          <h5 class="font-bold">Pengunjung Hari Ini</h5>
          <p id="total-visitors" class="text-2xl">0</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div class="bg-gray-700 p-4 rounded-lg shadow">
          <h5 class="font-bold">Total Slot Terisi</h5>
          <p id="slots-filled" class="text-2xl">0</p>
        </div>
        <div class="bg-gray-600 p-4 rounded-lg shadow">
          <h5 class="font-bold">Total Slot Kosong</h5>
          <p id="slots-empty" class="text-2xl">0</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        <div>
          <h4 class="font-bold">Tambah Anggota</h4>
          <form id="add-member-form" class="mt-2">
            <div class="mb-4">
              <label for="member-name" class="block font-bold">Nama</label>
              <input
                type="text"
                id="member-name"
                class="border rounded-lg w-full p-2 bg-gray-800 text-white"
                required
              />
            </div>
            <div class="mb-4">
              <label for="member-id" class="block font-bold">Nomor ID</label>
              <input
                type="text"
                id="member-id"
                class="border rounded-lg w-full p-2 bg-gray-800 text-white"
                required
              />
            </div>
            <button
              type="submit"
              class="bg-blue-600 text-white rounded-lg px-4 py-2"
            >
              Tambah
            </button>
          </form>
        </div>
        <div>
          <h4 class="font-bold">Top Up Saldo</h4>
          <form id="topup-form" class="mt-2">
            <div class="mb-4">
              <label for="topup-id" class="block font-bold">Nomor ID</label>
              <input
                type="text"
                id="topup-id"
                class="border rounded-lg w-full p-2 bg-gray-800 text-white"
                required
              />
            </div>
            <div class="mb-4">
              <label for="topup-amount" class="block font-bold"
                >Jumlah Top Up</label
              >
              <input
                type="number"
                id="topup-amount"
                class="border rounded-lg w-full p-2 bg-gray-800 text-white"
                required
              />
            </div>
            <button
              type="submit"
              class="bg-green-600 text-white rounded-lg px-4 py-2"
            >
              Top Up
            </button>
          </form>
        </div>
      </div>
    </div>

    <script>
      // Display current time
      function updateTime() {
        const now = new Date();
        document.getElementById("current-time").textContent =
          now.toLocaleTimeString();
      }
      setInterval(updateTime, 1000);

      // Add member form submission
      document
        .getElementById("add-member-form")
        .addEventListener("submit", (e) => {
          e.preventDefault();
          const name = document.getElementById("member-name").value;
          const id = document.getElementById("member-id").value;
          alert(`Anggota ${name} (ID: ${id}) berhasil ditambahkan!`);
        });

      // Top up form submission
      document.getElementById("topup-form").addEventListener("submit", (e) => {
        e.preventDefault();
        const id = document.getElementById("topup-id").value;
        const amount = document.getElementById("topup-amount").value;
        alert(
          `Saldo sebesar Rp${amount} berhasil ditambahkan untuk ID: ${id}!`
        );
      });
  // Toggle dropdown
  const profileBtn = document.querySelector(".relative button");
  const dropdown = document.querySelector(".relative ul");

  profileBtn.addEventListener("click", () => {
    dropdown.classList.toggle("hidden");
  });

  document.addEventListener("click", (e) => {
    if (!profileBtn.contains(e.target)) {
      dropdown.classList.add("hidden");
    }
  });
    </script>
  </body>
  @endsection
</html>
