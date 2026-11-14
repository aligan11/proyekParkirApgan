import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:parking_app/pages/DetailPembayaranTopupPage.dart';
import 'package:parking_app/pages/MidtransWebViewPage.dart';
import 'package:parking_app/pages/PaymentMethodPage.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:flutter/services.dart';

import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';

import 'package:lottie/lottie.dart';
import 'package:parking_app/pages/CustomBottomNav.dart';
import 'package:parking_app/pages/HomePage.dart';
import 'package:parking_app/pages/RIwayatTopupPage.dart';

class PaketTopup {
  final String nominal;
  final String biayaAdmin;
  final String totalBayar;
  final bool isAktif;

  PaketTopup({
    required this.nominal,
    required this.biayaAdmin,
    required this.totalBayar,
    required this.isAktif,
  });

  factory PaketTopup.fromJson(Map<String, dynamic> json) {
    return PaketTopup(
      nominal: json['nominal'],
      biayaAdmin: json['biaya_admin'],
      totalBayar: json['total_bayar'],
      isAktif: json['is_aktif'],
    );
  }
}

class IsiUlangPage extends StatefulWidget {
  final String? title;

  const IsiUlangPage({Key? key, this.title}) : super(key: key);

  @override
  _IsiUlangPageState createState() => _IsiUlangPageState();
}

PaketTopup? selectedPaket;
String metodePembayaran = 'GoPay'; // contoh default metode pembayaran

Future<void> _requestMidtransToken(
  BuildContext context, {
  required String nominal,
  required String biayaAdmin,
  required String? metode, // nullable
}) async {
  try {
    final prefs = await SharedPreferences.getInstance();
    final yourToken = prefs.getString('token');
    final userId = prefs.getInt('id_users');

    if (yourToken == null || userId == null) {
      throw Exception('Token atau ID user tidak ditemukan.');
    }

    final doubleNominal = (double.tryParse(nominal) ?? 0.0) * 1000;
    final doubleBiayaAdmin = (double.tryParse(biayaAdmin) ?? 0.0) * 1000;
    final totalDibayar = doubleNominal + doubleBiayaAdmin;

    if (doubleNominal < 1000.0 || doubleBiayaAdmin < 0.0) {
      throw Exception('Nominal atau biaya admin tidak valid.');
    }

    final response = await http.post(
      Uri.parse('http://192.168.18.13:8000/api/topup-store'),
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer $yourToken',
      },
      body: {
        'user_id': userId.toString(),
        'nominal': doubleNominal.toStringAsFixed(2),
        'biaya_admin': doubleBiayaAdmin.toStringAsFixed(2),
        'total_dibayar': totalDibayar.toStringAsFixed(2),
        'metode_pembayaran': metode ?? '', // kosong jika null
      },
    );

    final data = json.decode(response.body);

    if (response.statusCode == 201) {
      final paymentType = data['payment_type'];
      final redirectUrl = data['midtrans_redirect_url'];
      final qrImageUrl = data['midtrans_qr_image_url'];
      final snapUrl = data['midtrans_snap_url'];

      // Jika QRIS, tampilkan QR dialog
      if (paymentType == 'qris' && qrImageUrl != null) {
        showQRISDialog(
          context: context,
          imageUrl: qrImageUrl,
          nominal: nominal,
          biayaAdmin: biayaAdmin,
          totalBayar: totalDibayar.toStringAsFixed(0),
          metode: metode ?? 'QRIS',
        );
      }
      // Jika redirectUrl ada (misal gopay/shopeepay), tampilkan WebView
      else if (redirectUrl != null) {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => MidtransWebViewPage(url: redirectUrl),
          ),
        );
      }
      // Jika Snap URL ada (metode kosong)
      else if (snapUrl != null) {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => MidtransWebViewPage(url: snapUrl),
          ),
        );
      } else {
        throw Exception('Tidak ada metode pembayaran yang dapat ditampilkan.');
      }
    } else {
      showDialog(
        context: context,
        builder:
            (context) => AlertDialog(
              title: const Text('Gagal Memulai Transaksi'),
              content: Text(data['message'] ?? 'Terjadi kesalahan.'),
              actions: [
                TextButton(
                  onPressed: () => Navigator.of(context).pop(),
                  child: const Text('OK'),
                ),
              ],
            ),
      );
    }
  } catch (e) {
    showDialog(
      context: context,
      builder:
          (context) => AlertDialog(
            title: const Text('Error'),
            content: Text(e.toString()),
            actions: [
              TextButton(
                onPressed: () => Navigator.of(context).pop(),
                child: const Text('OK'),
              ),
            ],
          ),
    );
  }
}

Future<void> _cekTopupPending(
  BuildContext context, {
  required String nominal,
  required String biayaAdmin,
  required String? metode,
}) async {
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('token');
  final userId = prefs.getInt('id_users');

  if (token == null || userId == null) {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Autentikasi gagal. Silakan login ulang.')),
    );
    return;
  }

  final url = Uri.parse(
    'http://192.168.18.13:8000/api/topup-show-saldo/$userId',
  );

  final response = await http.get(
    url,
    headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
  );

  print("STATUS TOPUP CHECK: ${response.statusCode}");
  print("BODY: ${response.body}");

  // ================================
  // 1️⃣ Jika 404 → langsung lanjut topup baru
  // ================================
  if (response.statusCode == 404) {
    _handleLanjutkanTopup(
      context,
      nominal: nominal,
      biayaAdmin: biayaAdmin,
      metode: metode,
    );
    return;
  }

  // ================================
  // 2️⃣ Jika selain 200 (401, 500) → anggap gagal & lanjut saja
  // ================================
  if (response.statusCode != 200) {
    _handleLanjutkanTopup(
      context,
      nominal: nominal,
      biayaAdmin: biayaAdmin,
      metode: metode,
    );
    return;
  }

  // ================================
  // 3️⃣ Parse JSON aman
  // ================================
  final data = json.decode(response.body);

  // Pastikan response format map
  if (data is! Map) {
    _handleLanjutkanTopup(
      context,
      nominal: nominal,
      biayaAdmin: biayaAdmin,
      metode: metode,
    );
    return;
  }

  final topup = data['data'];

  // ================================
  // 4️⃣ Jika tidak ada pending → lanjut
  // ================================
  if (topup == null || topup['status'] != 'pending') {
    _handleLanjutkanTopup(
      context,
      nominal: nominal,
      biayaAdmin: biayaAdmin,
      metode: metode,
    );
    return;
  }

  // ================================
  // 5️⃣ Jika ada pending → tampilkan dialog
  // ================================
  showDialog(
    context: context,
    builder:
        (ctx) => AlertDialog(
          title: const Text('Transaksi Tertunda'),
          content: const Text(
            'Anda memiliki transaksi top-up yang belum selesai. Silakan hapus transaksi tersebut sebelum melanjutkan.',
          ),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(ctx).pop();
              },
              child: const Text('Batal'),
            ),
            TextButton(
              onPressed: () async {
                Navigator.of(ctx).pop();

                final deleteResp = await http.delete(
                  Uri.parse(
                    'http://192.168.18.13:8000/api/topup/pending/delete',
                  ),
                  headers: {
                    'Authorization': 'Bearer $token',
                    'Accept': 'application/json',
                  },
                );

                if (deleteResp.statusCode == 200) {
                  _handleLanjutkanTopup(
                    context,
                    nominal: nominal,
                    biayaAdmin: biayaAdmin,
                    metode: metode,
                  );
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Gagal menghapus transaksi.')),
                  );
                }
              },
              child: const Text('Hapus'),
            ),
          ],
        ),
  );
}

Future<void> _handleLanjutkanTopup(
  BuildContext context, {
  required String nominal,
  required String biayaAdmin,
  required String? metode,
}) async {
  final prefs = await SharedPreferences.getInstance();
  final token = prefs.getString('token');
  final userId = prefs.getInt('id_users');

  if (token == null || userId == null) {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text("Token atau user ID tidak ditemukan.")),
    );
    return;
  }

  final response = await http.get(
    Uri.parse('http://192.168.18.13:8000/api/topup-show-status/$userId'),
    headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
  );

  final result = json.decode(response.body);

  if (response.statusCode == 200 && result['status'] == 'success') {
    final topupData = result['data']['data'];
    final status = topupData['status'];

    if (status == 'pending') {
      final snapUrl = topupData['midtrans_snap_url'];

      if (snapUrl != null) {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => MidtransWebViewPage(url: snapUrl),
          ),
        );
        return;
      } else {
        showDialog(
          context: context,
          builder:
              (ctx) => AlertDialog(
                title: const Text('URL Tidak Tersedia'),
                content: const Text(
                  'URL pembayaran tidak tersedia. Apakah Anda ingin menghapus transaksi lama dan memulai ulang?',
                ),
                actions: [
                  TextButton(
                    onPressed: () {
                      Navigator.of(ctx).pop();
                    },
                    child: const Text('Batal'),
                  ),
                  TextButton(
                    onPressed: () async {
                      Navigator.of(ctx).pop();
                      final deleteResp = await http.delete(
                        Uri.parse(
                          'http://192.168.18.13:8000/api/topup/pending/delete',
                        ),
                        headers: {
                          'Authorization': 'Bearer $token',
                          'Accept': 'application/json',
                        },
                      );

                      if (deleteResp.statusCode == 200) {
                        _requestMidtransToken(
                          context,
                          nominal: nominal,
                          biayaAdmin: biayaAdmin,
                          metode: metode,
                        );
                      } else {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('Gagal menghapus transaksi.'),
                          ),
                        );
                      }
                    },
                    child: const Text('Hapus'),
                  ),
                ],
              ),
        );
        return;
      }
    }
  }

  // Jika status null atau success, langsung request transaksi baru
  _requestMidtransToken(
    context,
    nominal: nominal,
    biayaAdmin: biayaAdmin,
    metode: metode,
  );
}

Future<void> showSuccessLottieDialog(
  BuildContext context, {
  VoidCallback? onClose,
}) async {
  showDialog(
    context: context,
    barrierDismissible: false, // Supaya user nggak bisa tutup sendiri
    builder:
        (_) => Dialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(20),
          ),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Lottie.asset(
                  'assets/lottie/success_check.json',
                  width: 150,
                  repeat: false,
                ),
                const SizedBox(height: 10),
                const Text(
                  'Top-up Berhasil!',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 5),
                const Text('Saldo kamu akan segera bertambah.'),
                const SizedBox(height: 20),
                ElevatedButton.icon(
                  onPressed: () {
                    Navigator.pop(context);
                    if (onClose != null) onClose();
                  },
                  icon: const Icon(Icons.check),
                  label: const Text(
                    'Oke',
                    style: TextStyle(color: Colors.white),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.green,
                  ),
                ),
              ],
            ),
          ),
        ),
  );
}

void showQRISDialog({
  required BuildContext context,
  required String imageUrl,
  required String nominal,
  required String biayaAdmin,
  required String totalBayar,
  required String metode,
}) {
  showDialog(
    context: context,
    barrierDismissible: false,
    builder:
        (_) => Dialog(
          backgroundColor: Colors.transparent,
          child: Stack(
            alignment: Alignment.center,
            children: [
              Container(
                margin: const EdgeInsets.only(top: 20),
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(24),
                ),
                child: SingleChildScrollView(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(
                        Icons.qr_code_2,
                        size: 50,
                        color: Colors.blueAccent,
                      ),
                      const SizedBox(height: 10),
                      const Text(
                        "Scan QR Code untuk Bayar",
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 20,
                          color: Colors.black87,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 6),
                      const Text(
                        "Gunakan aplikasi e-wallet Anda untuk menyelesaikan pembayaran",
                        style: TextStyle(color: Colors.grey),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 20),

                      // ✅ Notice Text
                      const Text(
                        "Perhatian:",
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: Colors.redAccent,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 4),
                      const Text(
                        "Pesanan akan dibatalkan otomatis dalam 5 menit jika pembayaran tidak diselesaikan.",
                        style: TextStyle(fontSize: 13, color: Colors.grey),
                        textAlign: TextAlign.center,
                      ),

                      const SizedBox(height: 20),

                      // QR Image
                      Image.network(
                        imageUrl,
                        width: 200,
                        height: 200,
                        fit: BoxFit.contain,
                        errorBuilder:
                            (context, error, stackTrace) =>
                                const Text("Gagal memuat QR Code"),
                      ),

                      const SizedBox(height: 10),

                      // Copy Button
                      ElevatedButton.icon(
                        onPressed: () async {
                          await Clipboard.setData(
                            ClipboardData(text: imageUrl),
                          );
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text('QR Code URL berhasil disalin!'),
                            ),
                          );
                        },
                        icon: const Icon(Icons.copy),
                        label: const Text("Salin QR Code URL"),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.blueAccent,
                          foregroundColor: Colors.white,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                      ),

                      const SizedBox(height: 20),
                      _buildInfoRow("Nominal", "Rp$nominal"),
                      _buildInfoRow("Biaya Admin", "Rp$biayaAdmin"),
                      const Divider(height: 24),
                      _buildInfoRow(
                        "Total Bayar",
                        "Rp$totalBayar",
                        isTotal: true,
                      ),
                      const SizedBox(height: 20),
                      _buildMetodePembayaran(metode),

                      const SizedBox(height: 16),
                      const Text(
                        "Setelah melakukan pembayaran, halaman ini akan otomatis menutup jika berhasil.",
                        style: TextStyle(fontSize: 13, color: Colors.grey),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 12),
                      TextButton(
                        onPressed: () => Navigator.pop(context),
                        child: const Text("Tutup"),
                      ),
                    ],
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
            fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
            fontSize: isTotal ? 16 : 14,
            color: isTotal ? Colors.black87 : Colors.grey,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
            fontSize: isTotal ? 16 : 14,
            color: isTotal ? Colors.orange : Colors.black87,
          ),
        ),
      ],
    ),
  );
}

Widget _buildMetodePembayaran(String metode) {
  return Container(
    // padding: const EdgeInsets.all(12),
    decoration: BoxDecoration(
      border: Border.all(color: Colors.grey.shade200),
      borderRadius: BorderRadius.circular(16),
      color: Colors.grey.shade50,
    ),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        const Text(
          "Metode Pembayaran",
          style: TextStyle(color: Colors.grey, fontWeight: FontWeight.w600),
        ),
        Row(
          children: [
            Container(
              width: 30,
              height: 30,
              decoration: const BoxDecoration(
                color: Color(0xFF00ADEF),
                shape: BoxShape.circle,
              ),
              child: Center(
                child: Image.network(
                  "https://storage.googleapis.com/a1aa/image/33308e76-e11a-4f00-bc81-afdbe133a986.jpg",
                  width: 14,
                  height: 14,
                  fit: BoxFit.contain,
                ),
              ),
            ),
            const SizedBox(width: 8),
            Text(metode, style: TextStyle(fontWeight: FontWeight.w600)),
          ],
        ),
      ],
    ),
  );
}

class _IsiUlangPageState extends State<IsiUlangPage> {
  int _saldo = 0;
  @override
  void initState() {
    super.initState(); // <- WAJIB ini agar tidak error
    fetchSaldo(); // <- Pa
    SystemChrome.setEnabledSystemUIMode(SystemUiMode.immersiveSticky);
  }

  void dispose() {
    SystemChrome.setEnabledSystemUIMode(
      SystemUiMode.edgeToEdge,
    ); // default mode
    super.dispose();
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
          final saldoString = json['data']['data'][0]['saldo'] ?? '0';
          final fetchedSaldo = int.tryParse(saldoString.toString()) ?? 0;

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

  int _selectedIndex = 0;
  bool showPaymentOption = false;
  String selectedTotal = '0';

  Future<String?> _getToken() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getString('token');
  }

  Future<List<PaketTopup>> fetchPaketTopup() async {
    final token = await _getToken();
    if (token == null) {
      throw Exception('Token tidak ditemukan.');
    }

    final response = await http.get(
      Uri.parse('http://192.168.18.13:8000/api/topup-show'),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
    );

    if (response.statusCode == 200) {
      final List data = json.decode(response.body)['data'];
      return data
          .map((json) => PaketTopup.fromJson(json))
          .where((paket) => paket.isAktif)
          .toList();
    } else {
      throw Exception(
        'Gagal memuat paket topup. Status: ${response.statusCode}',
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        automaticallyImplyLeading: false,
        title: Text(
          'Isi Ulang',
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
          icon: Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => HomePage()),
            );
          },
        ),
        actions: [
          IconButton(
            icon: Icon(Icons.library_books, color: Colors.black),
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => RiwayatTopUpPage()),
              );
            },
          ),
        ],
      ),
      body: Stack(
        children: [
          Padding(
            padding: EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      colors: [Colors.black, Colors.grey.shade800],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  padding: const EdgeInsets.all(16),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Saldo Parkir Anda',
                            style: TextStyle(
                              color: Colors.grey[300],
                              fontSize: 14,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'Rp ${NumberFormat.currency(locale: 'id_ID', symbol: '', decimalDigits: 0).format(_saldo)}',
                            style: const TextStyle(
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ],
                      ),
                      Icon(
                        Icons.account_balance_wallet,
                        size: 36,
                        color: Colors.white,
                      ),
                    ],
                  ),
                ),
                SizedBox(height: 24),
                Text(
                  'Pilih Nominal',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.w600,
                    color: Colors.black,
                  ),
                ),
                SizedBox(height: 12),
                Expanded(
                  child: FutureBuilder<List<PaketTopup>>(
                    future: fetchPaketTopup(),
                    builder: (context, snapshot) {
                      if (snapshot.connectionState == ConnectionState.waiting) {
                        return Center(child: CircularProgressIndicator());
                      } else if (snapshot.hasError) {
                        return Center(
                          child: Text('Gagal memuat data: ${snapshot.error}'),
                        );
                      }
                      final nominalList = snapshot.data!;
                      return GridView.count(
                        crossAxisCount: 2,
                        mainAxisSpacing: 12,
                        crossAxisSpacing: 12,
                        children:
                            nominalList.map((data) {
                              return GestureDetector(
                                onTap: () {
                                  setState(() {
                                    showPaymentOption = true;
                                    selectedTotal = data.totalBayar;
                                    selectedPaket = data;
                                  });
                                },

                                child: Container(
                                  padding: EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: Colors.grey[100],
                                    border: Border.all(
                                      color: Colors.grey[300]!,
                                    ),
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  child: Column(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            'Rp${data.nominal}',
                                            style: TextStyle(
                                              fontSize: 18,
                                              fontWeight: FontWeight.bold,
                                              color: Colors.black,
                                            ),
                                          ),
                                          SizedBox(height: 6),
                                          Text(
                                            'Biaya Admin: Rp${data.biayaAdmin}',
                                            style: TextStyle(
                                              fontSize: 13,
                                              color: Colors.grey[600],
                                            ),
                                          ),
                                          Text(
                                            'Total Dibayar: Rp${data.totalBayar}',
                                            style: TextStyle(
                                              fontSize: 13,
                                              color: Colors.grey[800],
                                              fontWeight: FontWeight.bold,
                                            ),
                                          ),
                                        ],
                                      ),
                                      Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(
                                            'Pilih',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: Colors.grey[600],
                                            ),
                                          ),
                                          Icon(
                                            Icons.chevron_right,
                                            color: Colors.orange,
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              );
                            }).toList(),
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
          if (showPaymentOption)
            Align(
              alignment: Alignment.bottomCenter,
              child: Container(
                padding: EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border(top: BorderSide(color: Colors.grey.shade300)),
                  boxShadow: [
                    BoxShadow(color: Colors.grey.shade400, blurRadius: 8),
                  ],
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    GestureDetector(
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => PaymentMethodPage(),
                          ),
                        );
                      },
                      child: Container(
                        padding: EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 8,
                        ),
                        decoration: BoxDecoration(
                          border: Border.all(
                            color: const Color.fromARGB(255, 206, 206, 206),
                          ),
                          borderRadius: BorderRadius.circular(4),
                          color: Colors.transparent,
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Text(
                                widget.title != null
                                    ? 'Metode Pembayaran: ${widget.title}'
                                    : 'Pilih Metode Pembayaran',

                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            Icon(Icons.payment),
                          ],
                        ),
                      ),
                    ),

                    SizedBox(height: 4),
                    Text(
                      '9 metode tersedia',
                      style: TextStyle(fontSize: 12, color: Colors.grey[600]),
                    ),
                    SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Total: Rp$selectedTotal',
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.orange,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(8),
                            ),
                            padding: const EdgeInsets.symmetric(
                              horizontal: 24,
                              vertical: 14,
                            ),
                          ),
                          onPressed: () {
                            final nominal = selectedPaket?.nominal ?? '';
                            final biayaAdmin = selectedPaket?.biayaAdmin ?? '';
                            final metode = widget.title;

                            _cekTopupPending(
                              context,
                              nominal: nominal,
                              biayaAdmin: biayaAdmin,
                              metode: metode,
                            );
                          },

                          child: const Text(
                            'Lanjutkan',
                            style: TextStyle(color: Colors.white),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }
}
