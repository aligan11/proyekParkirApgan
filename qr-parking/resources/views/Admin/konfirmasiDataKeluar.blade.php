@extends('app')
@section('content')
  <div class="container mx-auto mt-4">
   <div class="text-sm text-gray-500 mb-2">
    <a class="hover:underline" href="#">
     Dashboard
    </a>
    /
    <span>
     Data Kendaraan Parkir Keluar
    </span>
   </div>
  <div class="bg-white shadow-md rounded-md p-6 h-3/4 border border-gray-200 bg-[url('https://www.transparenttextures.com/patterns/brick-wall.png')] bg-repeat">

    <div class="text-center text-lg font-bold border-b-2 border-red-500 pb-2 mb-4">
     <i class="fas fa-qrcode">
     </i>
     Hasil Scan QR Code/Manual Kartu Parkir Kendaraan
        </div>
        <div class="flex space-x-4 h-3/4">

             <div class="w-1/2 bg-white  shadow rounded p-4 flex flex-col justify-center">
            <div class="text-center">
       <img alt="QR Code" class="mx-auto mb-2" height="200" src="https://oaidalleapiprodscus.blob.core.windows.net/private/org-LmQ09WWGIGwOeeA4ArnRw0x5/user-uJPET5fjNenSso8wCETWVNOp/img-XKRPGw6ZiNdOzIabBWO1SI7m.png?st=2025-04-16T00%3A56%3A05Z&amp;se=2025-04-16T02%3A56%3A05Z&amp;sp=r&amp;sv=2024-08-04&amp;sr=b&amp;rscd=inline&amp;rsct=image/png&amp;skoid=cc612491-d948-4d2e-9821-2683df3719f5&amp;sktid=a48cca56-e6da-484e-a814-9c849652bcb3&amp;skt=2025-04-15T04%3A55%3A48Z&amp;ske=2025-04-16T04%3A55%3A48Z&amp;sks=b&amp;skv=2024-08-04&amp;sig=NzsBhINAfXIbD5xBKUjvFL7EGGNrZVmuRhMJMXh2kq4%3D" width="200"/>
             <div class="text-sm">
        Mobil
       </div>
       <div class="text-sm">
        Rp. 5.000
       </div>
       <div class="text-sm mt-2">
        KODE : PARKIR-022
       </div>
      </div>
     </div>
     <div class="w-1/2 bg-white shadow rounded p-4 flex flex-col justify-between">
      <div>
       <div class="text-lg font-bold border-b-2 border-red-500 pb-2 mb-4">
        <i class="fas fa-list">
        </i>
        Data Parkir Kendaraan
       </div>
       <div class="text-sm mb-2">
        <div class="flex mb-1">
         <div class="w-1/2">
          Jenis Kendaraan
         </div>
         <div class="w-1/2">
          : Mobil
         </div>
        </div>
        <div class="flex mb-1">
         <div class="w-1/2">
          Tipe/Merek Kendaraan
         </div>
         <div class="w-1/2">
          : Xenia/Daihatsu
         </div>
        </div>
        <div class="flex mb-1">
         <div class="w-1/2">
          Warna Kendaraan
         </div>
         <div class="w-1/2">
          : Merah
         </div>
        </div>
        <div class="flex mb-1">
         <div class="w-1/2">
          Plat Nomor
         </div>
         <div class="w-1/2">
          : DB 1212 JA
         </div>
        </div>
        <div class="flex mb-1">
         <div class="w-1/2">
          Waktu Masuk Kendaraan
         </div>
         <div class="w-1/2">
          : 09:34
         </div>
        </div>
        <div class="flex mb-1">
         <div class="w-1/2">
          Hari, Tanggal Masuk Kendaraan
         </div>
         <div class="w-1/2">
          : Rabu, 29 September 2021
         </div>
        </div>
        <div class="flex mb-1">
         <div class="w-1/2">
          Total Parkir Harus Dibayar
         </div>
         <div class="w-1/2">
          : Rp. 5.000
         </div>
        </div>
       </div>
      </div>
      <div class="flex justify-between mt-4">
       <button class="bg-blue-600 text-white px-4 py-2 rounded w-1/2 mr-2">
        Bayar
       </button>
       <button class="bg-red-600 text-white px-4 py-2 rounded w-1/2 ml-2">
        Batal
       </button>
      </div>
     </div>
        </div>
         </div>
          </div>
@endsection