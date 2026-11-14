import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:parking_app/pages/HomePage.dart';
import 'dart:convert';
import 'package:geolocator/geolocator.dart';

import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:geocoding/geocoding.dart';

import 'package:shared_preferences/shared_preferences.dart';

class CatatKendaraanPage extends StatefulWidget {
  final String? lokasi; // Opsional, bisa null

  const CatatKendaraanPage({Key? key, this.lokasi}) : super(key: key);

  @override
  State<CatatKendaraanPage> createState() => _CatatKendaraanPageState();
}

class _CatatKendaraanPageState extends State<CatatKendaraanPage> {
  Map<String, dynamic>? kendaraanAktif;

  @override
  void initState() {
    super.initState();
    loadKendaraanAktif();
    fetchKodeParkir(); // <-
    getCurrentLocation(); // ambil lokasi
  }

  String kodeParkir = 'KD'; // default
  String? currentLocation;

  Future<void> catatKendaraanMasuk() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    final userId = prefs.getInt('id_users');

    if (token == null || userId == null || kendaraanAktif == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Data tidak lengkap untuk mencatat kendaraan.')),
      );
      return;
    }

    final int kendaraanId = int.tryParse(kendaraanAktif!['id'].toString()) ?? 0;
    final int tarifId =
        int.tryParse(kendaraanAktif!['id_tarif2'].toString()) ?? 0;

    final String kodeParkirFinal = '$kodeParkir';

    final body = {
      "user_id": userId,
      "kode_parkir": kodeParkirFinal,

      "id_merk_kendaraan": kendaraanId,
      "id_tipe_kendaraan": kendaraanId,
      "id_plat_nomor": kendaraanId,
      "id_warna_kendaraan": kendaraanId,
      "id_tarif": tarifId,
      "waktu_masuk": DateTime.now().toIso8601String(),
      "lokasi": currentLocation ?? "Tidak diketahui",
      "tempat": widget.lokasi,

      "status": "Belum Dibayar",
    };

    print('Mengirim data catat kendaraan:');
    print(jsonEncode(body));

    try {
      final response = await http.post(
        Uri.parse('http://192.168.18.13:8000/api/catat-waktu-masuk'),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode(body),
      );

      print('Status Code: ${response.statusCode}');
      print('Response Body: ${response.body}');

      if (response.statusCode == 201) {
        final result = jsonDecode(response.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? 'Berhasil mencatat.')),
        );

        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => HomePage()),
        );
      } else {
        final errorMsg =
            jsonDecode(response.body)['message'] ??
            'Gagal mencatat kendaraan. Silakan cek kembali data.';
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text(errorMsg)));
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Terjadi kesalahan saat mengirim data.')),
      );
      print("Error: $e");
    }
  }

  Future<void> getCurrentLocation() async {
    bool serviceEnabled;
    LocationPermission permission;

    serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      print('Layanan lokasi tidak aktif');
      return;
    }

    permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
      if (permission == LocationPermission.denied) {
        print('Izin lokasi ditolak');
        return;
      }
    }

    if (permission == LocationPermission.deniedForever) {
      print('Izin lokasi ditolak permanen');
      return;
    }

    try {
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );

      print('Posisi: ${position.latitude}, ${position.longitude}');

      final placemarks = await placemarkFromCoordinates(
        position.latitude,
        position.longitude,
      );

      if (placemarks.isNotEmpty) {
        final place = placemarks.first;

        String lokasi = 'Tidak diketahui';

        if (place.locality != null && place.locality!.isNotEmpty) {
          lokasi = place.locality!;
        } else if (place.subAdministrativeArea != null &&
            place.subAdministrativeArea!.isNotEmpty) {
          lokasi = place.subAdministrativeArea!;
        } else if (place.administrativeArea != null &&
            place.administrativeArea!.isNotEmpty) {
          lokasi = place.administrativeArea!;
        } else if (place.country != null && place.country!.isNotEmpty) {
          lokasi = place.country!;
        }

        // Hilangkan kata "Kecamatan " kalau ada
        lokasi = lokasi.replaceAll(
          RegExp(r'Kecamatan\s+', caseSensitive: false),
          '',
        );

        print('Lokasi ditemukan: $lokasi');

        setState(() {
          currentLocation = lokasi;
        });
      } else {
        print('Tidak ada placemark ditemukan.');
        setState(() {
          currentLocation = 'Tidak diketahui';
        });
      }
    } catch (e) {
      print('Terjadi error saat ambil lokasi atau geocoding: $e');
      setState(() {
        currentLocation = 'Tidak diketahui';
      });
    }
  }

  Future<void> fetchKodeParkir() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    if (token == null) {
      print('Token tidak ditemukan.');
      return;
    }

    final url = Uri.parse(
      'http://192.168.18.13:8000/api/tampil-property/parkir',
    );

    try {
      final response = await http.get(
        url,
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'text/plain', // karena responnya string biasa
        },
      );

      print('Status: ${response.statusCode}');
      print('Body: ${response.body}');

      if (response.statusCode == 200) {
        setState(() {
          kodeParkir = response.body.trim(); // hapus spasi ekstra
        });
      } else {
        print('Gagal fetch kode_parkir: ${response.statusCode}');
      }
    } catch (e) {
      print('Error saat fetch kode_parkir: $e');
    }
  }

  Future<void> loadKendaraanAktif() async {
    final data = await fetchKendaraanAktif();
    setState(() {
      kendaraanAktif = data;
    });
  }

  Future<Map<String, dynamic>?> fetchKendaraanAktif() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    int? userId = prefs.getInt('id_users');
    String? token = prefs.getString('token');

    if (userId == null || token == null) return null;

    final response = await http.get(
      Uri.parse('http://192.168.18.13:8000/api/kendaraan-show/$userId'),
      headers: {'Authorization': 'Bearer $token', 'Accept': 'application/json'},
    );

    if (response.statusCode == 200) {
      final jsonData = json.decode(response.body);
      final List<dynamic> dataList = jsonData['data']['data'];

      final aktif = dataList.firstWhere(
        (item) => item['is_aktif'] == 'on',
        orElse: () => null,
      );

      return aktif;
    } else {
      return null;
    }
  }

  @override
  Widget build(BuildContext context) {
    final mobil = kendaraanAktif;

    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      appBar: AppBar(
        automaticallyImplyLeading: false,
        title: Text(
          'Catat Kendaraan',
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
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body:
          mobil == null
              ? Center(child: Text("Tidak ada kendaraan aktif."))
              : Padding(
                padding: const EdgeInsets.symmetric(
                  horizontal: 24,
                  vertical: 20,
                ),
                child: Column(
                  children: [
                    Container(
                      width: double.infinity,
                      constraints: BoxConstraints(minHeight: 280),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        border: Border.all(color: Colors.grey.shade300),
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black12.withOpacity(0.08),
                            blurRadius: 10,
                            offset: Offset(0, 6),
                          ),
                        ],
                      ),
                      padding: EdgeInsets.symmetric(
                        horizontal: 20,
                        vertical: 24,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Center(
                            child: Column(
                              children: [
                                Container(
                                  padding: EdgeInsets.all(12),
                                  decoration: BoxDecoration(
                                    color: Color(0xFFFFE9D5),
                                    shape: BoxShape.circle,
                                  ),
                                  child: FaIcon(
                                    FontAwesomeIcons.car,
                                    size: 32,
                                    color: Color.fromARGB(255, 243, 138, 33),
                                  ),
                                ),
                                SizedBox(height: 10),
                                Text(
                                  'Detail Kendaraan',
                                  style: TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                    letterSpacing: 0.5,
                                    color: Colors.black87,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          SizedBox(height: 28),
                          _buildInfoRow('Kode Kendaraan', kodeParkir),

                          _buildInfoRow(
                            'Merk Kendaraan',
                            mobil['merk_kendaraan'] ?? 'Tidak diketahui',
                          ),
                          _buildInfoRow(
                            'Plat Nomor',
                            mobil['plat_nomor'] ?? '-',
                          ),
                          _buildInfoRow(
                            'Warna Kendaraan',
                            mobil['warna'] ?? '-',
                          ),
                          _buildInfoRow(
                            'Tarif Parkir',
                            mobil['id_tarif'] != null
                                ? 'Rp.${NumberFormat('#,###', 'id_ID').format(double.tryParse(mobil['id_tarif'].toString())?.round() ?? 0)}'
                                : '-',
                          ),
                          _buildInfoRow(
                            'Jenis Kendaraan',
                            mobil['tipe_kendaraan'] ?? '-',
                          ),
                          _buildInfoRow(
                            'Tempat',
                            widget.lokasi ?? 'tidak terdeteksi..',
                          ),
                          _buildInfoRow(
                            'Lokasi',
                            currentLocation ?? 'Mengambil lokasi...',
                          ),

                          _buildInfoRow(
                            'Waktu Masuk',
                            DateFormat('HH:mm:ss').format(DateTime.now()),
                            isBold: true,
                          ),
                        ],
                      ),
                    ),

                    SizedBox(height: 40),
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton.icon(
                        onPressed: catatKendaraanMasuk,

                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.black,
                          foregroundColor: Colors.white,
                          elevation: 5,
                          padding: EdgeInsets.symmetric(
                            vertical: 16,
                            horizontal: 24,
                          ),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(50),
                          ),
                          shadowColor: Colors.black26,
                        ),
                        icon: FaIcon(FontAwesomeIcons.checkCircle, size: 18),
                        label: Text(
                          'Catat Kendaraan Masuk',
                          style: TextStyle(
                            fontWeight: FontWeight.w600,
                            fontSize: 16,
                          ),
                        ),
                      ),
                    ),
                    SizedBox(height: 20),
                  ],
                ),
              ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isBold = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Text(
              label,
              style: TextStyle(color: Colors.grey.shade600, fontSize: 13),
            ),
          ),
          Expanded(
            child: Text(
              value,
              textAlign: TextAlign.right,
              style: TextStyle(
                fontSize: isBold ? 16 : 15,
                fontWeight: isBold ? FontWeight.bold : FontWeight.w600,
                color: Colors.black87,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
