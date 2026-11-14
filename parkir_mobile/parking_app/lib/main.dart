import 'package:flutter/material.dart';
import 'package:parking_app/pages/ParkirScreen.dart';
import 'package:parking_app/pages/PembayaranPage.dart';
import 'package:parking_app/pages/catat_kendaraan_page.dart';
import 'package:parking_app/staffpages/cekStatusKendaraanStaff.dart';

// Import halaman parkir

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Sistem Parkir',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        fontFamily: 'Inter', // Jika kamu ingin pakai font Inter
        useMaterial3: true,
      ),
      home: const ParkirScreen(),
    );
  }
}
