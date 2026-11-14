import 'package:flutter/material.dart';
import 'package:lottie/lottie.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class TampilDendaPage extends StatefulWidget {
  final String platNomor;

  const TampilDendaPage({super.key, required this.platNomor});

  @override
  State<TampilDendaPage> createState() => _TampilDendaPageState();
}

class _TampilDendaPageState extends State<TampilDendaPage> {
  bool isLoading = false;
  Map<String, dynamic>? kendaraanData;

  @override
  void initState() {
    super.initState();
    fetchKendaraanData();
  }

  Future<void> fetchKendaraanData() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      if (token == null) {
        _showError(context, "Token tidak ditemukan.");
        return;
      }

      final response = await http.get(
        Uri.parse(
          'http://192.168.18.13:8000/api/kendaraan/plat/${widget.platNomor}',
        ),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final result = jsonDecode(response.body);
        setState(() {
          kendaraanData = result['data']['data'];
        });
      } else {
        _showError(
          context,
          "Gagal mengambil data kendaraan (${response.statusCode}).",
        );
      }
    } catch (e) {
      _showError(context, "Terjadi kesalahan: $e");
    }
  }

  Future<void> kirimNotifikasi(BuildContext context) async {
    if (kendaraanData == null) {
      _showError(context, "Data kendaraan belum tersedia.");
      return;
    }

    setState(() => isLoading = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      if (token == null) {
        throw Exception("Token tidak ditemukan.");
      }

      final response = await http.post(
        Uri.parse("http://192.168.18.13:8000/api/kendaraan/store"),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({
          "id_kendaraan": kendaraanData!['id'],
          "id_users": kendaraanData!['id_users'],
          "plat_nomor": kendaraanData!['plat_nomor'],
          "jenis_pelanggaran": "Parkir tanpa scan barcode",
        }),
      );

      setState(() => isLoading = false);

      if (response.statusCode == 201) {
        // Tampilkan dialog modal dengan background putih
        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (_) {
            return Dialog(
              backgroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Lottie.asset(
                      'assets/lottie/success_check.json',
                      width: 140,
                      repeat: false,
                    ),
                    const SizedBox(height: 16),
                    const Text(
                      'Denda berhasil dikirim!',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: Colors.black87,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              ),
            );
          },
        );

        // Tutup dialog dan kembali setelah 2 detik
        await Future.delayed(const Duration(seconds: 2));
        if (context.mounted) Navigator.of(context).pop(); // tutup dialog
        if (context.mounted) Navigator.of(context).pop(); // kembali
      } else {
        final res = jsonDecode(response.body);
        _showError(context, res['message'] ?? 'Gagal mengirim data.');
      }
    } catch (e) {
      setState(() => isLoading = false);
      _showError(context, 'Terjadi kesalahan: $e');
    }
  }

  void _showError(BuildContext context, String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: Colors.red),
    );
  }

  @override
  Widget build(BuildContext context) {
    final warna = kendaraanData?['warna'] ?? '-';
    final merk = kendaraanData?['merk_kendaraan'] ?? '-';

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Peringatan Denda'),
        backgroundColor: Colors.redAccent,
        foregroundColor: Colors.white,
        elevation: 2,
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Lottie.asset(
                'assets/lottie/warning.json',
                width: 180,
                repeat: false,
              ),
              const SizedBox(height: 24),
              Text(
                'Pelanggaran Parkir',
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: Colors.red[700],
                ),
              ),
              const SizedBox(height: 12),
              Text(
                'Kendaraan dengan plat:',
                style: TextStyle(fontSize: 16, color: Colors.grey[800]),
              ),
              const SizedBox(height: 4),
              Text(
                widget.platNomor,
                style: const TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: Colors.black,
                  letterSpacing: 2,
                ),
              ),
              const SizedBox(height: 16),
              Text(
                'Merk: $merk\nWarna: $warna\nDenda akan dikenakan karena tidak scan barcode saat masuk.',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 16, color: Colors.grey[700]),
              ),
              const SizedBox(height: 28),

              ElevatedButton.icon(
                onPressed: isLoading ? null : () => kirimNotifikasi(context),
                icon: const Icon(Icons.send),
                label:
                    isLoading
                        ? const SizedBox(
                          width: 20,
                          height: 20,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                        : const Text('Kirim ke Pengguna'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.black87,
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 24,
                    vertical: 16,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              ElevatedButton.icon(
                onPressed: () => Navigator.pop(context),
                icon: const Icon(Icons.arrow_back),
                label: const Text('Kembali'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.redAccent,
                  foregroundColor: Colors.white,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
