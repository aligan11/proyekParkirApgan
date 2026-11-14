import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:parking_app/pages/PembayaranPage.dart';
import 'package:parking_app/pages/catat_kendaraan_page.dart';

import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ScanBarcodePage extends StatefulWidget {
  const ScanBarcodePage({Key? key}) : super(key: key);

  @override
  State<ScanBarcodePage> createState() => _ScanBarcodePageState();
}

class _ScanBarcodePageState extends State<ScanBarcodePage> {
  bool _isScanned = false;

  void _onDetect(BarcodeCapture capture) async {
    if (_isScanned) return;

    final Barcode? barcode = capture.barcodes.first;
    final String? code = barcode?.rawValue;

    print('🔍 QR Scan Result: $code');

    if (code != null) {
      try {
        if (!code.trim().startsWith("{")) {
          throw const FormatException("QR bukan JSON");
        }

        final data = json.decode(code);
        print('📦 Decoded Data: $data');

        // Validasi isi QR: hanya "kode" & "signature"
        if (data['kode'] != 'GANPARKING' || data['signature'] == null) {
          throw const FormatException("Format QR tidak lengkap.");
        }

        SharedPreferences prefs = await SharedPreferences.getInstance();
        final String? token = prefs.getString('token');
        final int? userId = prefs.getInt('id_users');

        if (token == null || userId == null) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text("Token atau User ID tidak ditemukan."),
            ),
          );
          return;
        }

        setState(() {
          _isScanned = true;
        });

        // 🔐 Validasi QR di server
        final validasiResponse = await http.post(
          Uri.parse('http://192.168.18.13:8000/api/validasi-qr'),
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'Authorization': 'Bearer $token',
          },
          body: json.encode({
            'kode': data['kode'],
            'signature': data['signature'],
          }),
        );

        if (validasiResponse.statusCode == 200) {
          // ✅ QR Valid, sekarang cek status parkir
          final statusResponse = await http.post(
            Uri.parse('http://192.168.18.13:8000/api/dataParkir/$userId'),
            headers: {
              'Accept': 'application/json',
              'Authorization': 'Bearer $token',
            },
          );

          if (statusResponse.statusCode == 200) {
            final statusData = json.decode(statusResponse.body);
            final status = statusData['data']['status'];

            if (status == "Belum Dibayar") {
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(builder: (context) => const PembayaranPage()),
              );
            } else {
              // Ambil lokasi dari QR Code
              final String lokasi = data['lokasi'] ?? '';

              Navigator.pushReplacement(
                context,
                MaterialPageRoute(
                  builder:
                      (context) => CatatKendaraanPage(
                        lokasi: lokasi, // Kirim ke halaman
                      ),
                ),
              );
            }
          } else if (statusResponse.statusCode == 404) {
            // Tidak ada parkir aktif
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(
                builder:
                    (context) => CatatKendaraanPage(
                      lokasi:
                          data['lokasi'], // akan otomatis null jika field tidak ada
                    ),
              ),
            );
          } else {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text(
                  "Gagal cek status parkir: ${statusResponse.statusCode}",
                ),
              ),
            );
          }
        } else {
          final errorMsg = json.decode(validasiResponse.body)['message'];
          ScaffoldMessenger.of(
            context,
          ).showSnackBar(SnackBar(content: Text("QR tidak valid: $errorMsg")));
        }
      } catch (e) {
        print("❌ Error: $e");
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text("QR Code tidak dapat diproses: $e")),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        iconTheme: const IconThemeData(
          color: Colors.white,
        ), // ⬅️ Ubah warna panah jadi putih
        title: const Text(
          "Scan QR Code",
          style: TextStyle(color: Colors.white),
        ),
        backgroundColor: Colors.black,
      ),
      body: Stack(
        children: [
          // Kamera
          MobileScanner(
            controller: MobileScannerController(
              detectionSpeed: DetectionSpeed.normal,
              facing: CameraFacing.back,
              torchEnabled: false,
            ),
            onDetect: _onDetect,
          ),

          // Petunjuk Atas
          Align(
            alignment: Alignment.topCenter,
            child: Container(
              margin: const EdgeInsets.only(top: 24),
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color: Colors.black.withOpacity(0.7),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Text(
                "Arahkan kamera ke QR Code",
                style: TextStyle(color: Colors.white, fontSize: 16),
              ),
            ),
          ),

          // Kotak Scan Selalu Tampil
          Center(
            child: Container(
              width: 250,
              height: 250,
              decoration: BoxDecoration(
                border: Border.all(color: Colors.white, width: 3),
                borderRadius: BorderRadius.circular(16),
                color: Colors.black.withOpacity(0.2),
              ),
              child: const Center(
                child: Icon(
                  Icons.qr_code_scanner,
                  size: 64,
                  color: Colors.white70,
                ),
              ),
            ),
          ),

          // Overlay
          IgnorePointer(
            child: Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.black.withOpacity(0.6),
                    Colors.transparent,
                    Colors.black.withOpacity(0.6),
                  ],
                  stops: const [0.0, 0.5, 1.0],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
