import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:parking_app/pages/HomePage.dart';
import 'package:parking_app/pages/MidtransWebViewPage.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:lottie/lottie.dart'; // Tambahkan di bagian import

class PembayaranPage extends StatefulWidget {
  const PembayaranPage({super.key});

  @override
  State<PembayaranPage> createState() => _PembayaranPageState();
}

class _PembayaranPageState extends State<PembayaranPage> {
  Map<String, dynamic>? parkirData;
  bool isLoading = true;
  int tarifAwal = 6000;
  int tarifPerJam = 2000;
  int _saldo = 0;

  @override
  void initState() {
    super.initState();
    fetchParkirMasuk();
    fetchSaldo();
  }

  Future<void> fetchSaldo() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userId = prefs.getInt('id_users');
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse('http://192.168.18.13:8000/api/user-show/$userId'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final json = jsonDecode(response.body);

        if (json['status'] == 'success') {
          final fetchedSaldoString = json['data']['data'][0]['saldo'] ?? "0";
          final fetchedSaldo = int.tryParse(fetchedSaldoString.toString()) ?? 0;

          setState(() {
            _saldo = fetchedSaldo;
          });

          print('✅ Saldo berhasil diambil: $_saldo');
        } else {
          print('⚠️ Gagal mengambil data saldo: ${json['message']}');
        }
      } else {
        print('❌ HTTP error: ${response.statusCode}');
      }
    } catch (e) {
      print('❌ Terjadi error saat ambil saldo: $e');
    }
  }

  Future<void> cekStatusParkir(BuildContext context, String kodeParkir) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final url = Uri.parse(
      'http://192.168.18.13:8000/api/cek-status-parkir/$kodeParkir',
    );
    print("🔍 Mengecek status untuk: $kodeParkir");

    try {
      final response = await http.get(
        url,
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      print("📥 Response status: ${response.statusCode}");
      print("📥 Body: ${response.body}");

      final body = json.decode(response.body);

      // ✅ Cek dari body['data']['status'], bukan body['status']
      final status = body['data']?['status'];

      if (status == 'Sudah Dibayar') {
        print("✅ Status Sudah Dibayar - Navigasi ke HomePage");

        if (context.mounted) {
          Navigator.push(
            context,
            MaterialPageRoute(builder: (context) => HomePage()),
          );
        } else {
          print("❌ Context tidak mounted");
        }
      } else {
        print("🕒 Status belum dibayar: $status");
      }
    } catch (e) {
      print("❌ Error cek status parkir: $e");
    }
  }

  Future<void> fetchParkirMasuk() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final url = Uri.parse('http://192.168.18.13:8000/api/parkir-masuk');

    try {
      final response = await http.get(
        url,
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final body = json.decode(response.body);
        final data =
            body['data']['data']; // Sudah 1 data yang waktu_keluar == null

        if (data != null) {
          setState(() {
            parkirData = data;
            tarifAwal = data['tarif_awal'] ?? 6000;
            tarifPerJam = data['tarif_per_jam'] ?? 2000;
            isLoading = false;
          });

          final kodeParkir = data['kode_parkir'];
          if (kodeParkir != null) {
            await cekStatusParkir(context, kodeParkir);
          }
        } else {
          setState(() {
            parkirData = null;
            isLoading = false;
          });
        }
      } else {
        print("Gagal: ${response.statusCode}");
        setState(() {
          isLoading = false;
        });
      }
    } catch (e) {
      print("Error fetching data: $e");
      setState(() {
        isLoading = false;
      });
    }
  }

  String _calculateDuration(String waktuMasuk) {
    try {
      final masuk = DateTime.parse(waktuMasuk);

      final keluar =
          DateTime.now(); // pakai DateTime lengkap, bukan dari _formatNow

      final diff = keluar.difference(masuk);

      final jam = diff.inHours;
      final menit = diff.inMinutes.remainder(60);

      if (jam == 0 && menit == 0) {
        return "1 menit";
      }

      if (jam > 0 && menit > 0) {
        return "$jam jam $menit menit";
      } else if (jam > 0) {
        return "$jam jam";
      } else {
        return "$menit menit";
      }
    } catch (e) {
      print("Gagal menghitung durasi: $e");
      return "-";
    }
  }

  int _calculateTotal(String waktuMasuk, String tarifStr) {
    try {
      final masuk = DateTime.parse(waktuMasuk);
      final keluar = DateTime.now();
      final diff = keluar.difference(masuk);

      // 🛠 Parse string "6000.00" jadi double, lalu ke int
      final tarif = double.tryParse(tarifStr)?.toInt() ?? 0;

      final tarifAwal = tarif;
      final tambahanPerJam = 2000;

      final totalJam = (diff.inMinutes / 60).ceil();
      print('⏱ Durasi: $totalJam jam, Tarif Awal: $tarifAwal');

      int total = tarifAwal;
      if (totalJam > 1) {
        total += (totalJam - 1) * tambahanPerJam;
      }

      print('💸 Total bayar: $total');
      return total;
    } catch (e) {
      print("❌ Gagal menghitung total: $e");
      return 0;
    }
  }

  String _formatNow() {
    final now = DateTime.now();
    return "${now.hour.toString().padLeft(2, '0')}:"
        "${now.minute.toString().padLeft(2, '0')}:"
        "${now.second.toString().padLeft(2, '0')}";
  }

  Future<void> bayarSekarang() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    final kodeParkir = parkirData?['kode_parkir'];
    final waktuMasuk = parkirData?['waktu_masuk'];
    final tarif = parkirData?['tarif'];

    if (kodeParkir == null || waktuMasuk == null || tarif == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Data tidak lengkap untuk melakukan pembayaran.'),
        ),
      );
      return;
    }

    final total = _calculateTotal(waktuMasuk, tarif);
    final waktuKeluar = DateTime.now().toIso8601String();

    final url = Uri.parse('http://192.168.18.13:8000/api/parkir/$kodeParkir');

    try {
      final response = await http.put(
        url,
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'waktu_keluar': waktuKeluar,
          'total': total.toString(),
        }),
      );

      final body = jsonDecode(response.body);

      if (response.statusCode == 200) {
        setState(() {
          parkirData = body['data']['parkir'];
        });

        final saldoBaru = body['data']['saldo_user_setelah_pembayaran'];
        await prefs.setInt('saldo', saldoBaru);

        /// 🔥 POPUP LOTTIE – SUKSES BAYAR
        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (context) {
            Future.delayed(const Duration(milliseconds: 1800), () {
              Navigator.of(context).pop(); // tutup popup
              Navigator.of(context).pop(); // kembali ke page sebelumnya
            });

            return Dialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    SizedBox(
                      height: 150,
                      width: 150,
                      child: Lottie.asset(
                        "assets/lottie/success_check.json",
                        repeat: false,
                      ),
                    ),
                    const SizedBox(height: 16),
                    const Text(
                      "Pembayaran Berhasil!",
                      style: TextStyle(
                        fontSize: 20,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 10),
                    const Text(
                      "Saldo Anda telah terpotong otomatis.",
                      textAlign: TextAlign.center,
                      style: TextStyle(fontSize: 14),
                    ),
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            );
          },
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(body['message'] ?? 'Gagal melakukan pembayaran.'),
          ),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Terjadi kesalahan saat update.')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final int totalBayar =
        parkirData != null &&
                parkirData!['waktu_masuk'] != null &&
                parkirData!['tarif'] != null
            ? _calculateTotal(parkirData!['waktu_masuk'], parkirData!['tarif'])
            : 0;

    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FA),
      appBar: AppBar(
        automaticallyImplyLeading: false,
        title: Text(
          'Pembayaran Parkir',
          style: TextStyle(
            color: Colors.grey[800],
            fontSize: 14,
            fontWeight: FontWeight.w600,
          ),
        ),
        backgroundColor: Colors.white,
        centerTitle: true,
        elevation: 2,
        shadowColor: Colors.grey[300],
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
      ),
      body:
          isLoading
              ? const Center(child: CircularProgressIndicator())
              : parkirData == null
              ? const Center(child: Text('Data tidak ditemukan'))
              : SingleChildScrollView(
                padding: const EdgeInsets.symmetric(
                  horizontal: 24,
                  vertical: 16,
                ),
                child: Column(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black12,
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.center,
                        children: [
                          const Icon(
                            FontAwesomeIcons.wallet,
                            color: Color(0xFFE67E22),
                            size: 48,
                          ),
                          const SizedBox(height: 12),
                          const Text(
                            'Detail Pembayaran',
                            style: TextStyle(
                              fontSize: 22,
                              fontWeight: FontWeight.bold,
                              color: Colors.black87,
                            ),
                          ),
                          const SizedBox(height: 24),
                          _buildInfoRow(
                            'Kode Parkir',
                            parkirData!['kode_parkir'] ?? '-',
                          ),
                          _buildInfoRow(
                            'Plat Nomor',
                            parkirData!['plat_nomor'] ?? '-',
                          ),
                          _buildInfoRow(
                            'Waktu Masuk',
                            parkirData!['waktu_masuk'] != null
                                ? DateFormat('HH:mm:ss').format(
                                  DateTime.parse(parkirData!['waktu_masuk']),
                                )
                                : '-',
                          ),

                          _buildInfoRow('Waktu Keluar', _formatNow()),

                          _buildInfoRow(
                            'Durasi',
                            (parkirData!['waktu_masuk'] != null)
                                ? _calculateDuration(parkirData!['waktu_masuk'])
                                : 'Sedang Proses',
                          ),

                          _buildInfoRow(
                            'Tarif Parkir',
                            parkirData!['tarif'] ?? '-',
                          ),

                          const Divider(height: 32),
                          _buildInfoRow(
                            'Total Pembayaran',
                            (parkirData!['waktu_masuk'] != null &&
                                    parkirData!['tarif'] != null)
                                ? "Rp ${_calculateTotal(parkirData!['waktu_masuk'], parkirData!['tarif']).toString()}"
                                : 'Sedang Proses',
                            isTotal: true,
                          ),

                          const SizedBox(height: 6),
                          const Align(
                            alignment: Alignment.centerRight,
                            child: Text(
                              'Tarif dihitung otomatis berdasarkan durasi parkir.',
                              style: TextStyle(
                                fontSize: 12,
                                fontStyle: FontStyle.italic,
                                color: Colors.grey,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),

                    // Hitung total pembayaran dari data parkir
                    const SizedBox(height: 32),

                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () {
                          if (_saldo < totalBayar) {
                            showDialog(
                              context: context,
                              barrierDismissible: true,
                              builder:
                                  (context) => AlertDialog(
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    content: Column(
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        SizedBox(
                                          width: 120,
                                          height: 120,
                                          child: Lottie.asset(
                                            'assets/lottie/json.json',
                                            repeat: false,
                                          ),
                                        ),
                                        const SizedBox(height: 16),
                                        const Text(
                                          'Saldo Tidak Cukup',
                                          style: TextStyle(
                                            fontWeight: FontWeight.bold,
                                            fontSize: 18,
                                          ),
                                        ),
                                        const SizedBox(height: 8),
                                        const Text(
                                          'Maaf, saldo Anda tidak mencukupi.\nSilakan top up terlebih dahulu.',
                                          textAlign: TextAlign.center,
                                        ),
                                        const SizedBox(height: 16),
                                        TextButton(
                                          onPressed: () {
                                            Navigator.push(
                                              context,
                                              MaterialPageRoute(
                                                builder:
                                                    (context) => HomePage(),
                                              ),
                                            );
                                          },
                                          child: const Text(
                                            'Kembali ke Beranda',
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                            );
                          } else {
                            bayarSekarang(); // lanjutkan pembayaran jika saldo cukup
                          }
                        },
                        icon: const Icon(FontAwesomeIcons.creditCard, size: 20),
                        label: const Text('Bayar Sekarang'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.black,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(50),
                          ),
                          textStyle: const TextStyle(fontSize: 18),
                        ),
                      ),
                    ),

                    const SizedBox(height: 16),

                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: () {
                          fetchParkirMasuk(); // atau setState untuk refresh
                        },
                        icon: const Icon(
                          FontAwesomeIcons.rotateRight,
                          size: 20,
                        ),
                        label: const Text('Refresh Now'),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color.fromARGB(
                            255,
                            216,
                            101,
                            0,
                          ),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(50),
                          ),
                          textStyle: const TextStyle(fontSize: 18),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isTotal = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              color: isTotal ? Colors.black87 : Colors.grey[600],
              fontWeight: isTotal ? FontWeight.w600 : FontWeight.normal,
              fontSize: isTotal ? 16 : 14,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              color: Colors.black87,
              fontWeight: isTotal ? FontWeight.bold : FontWeight.w500,
              fontSize: isTotal ? 16 : 14,
            ),
          ),
        ],
      ),
    );
  }
}
