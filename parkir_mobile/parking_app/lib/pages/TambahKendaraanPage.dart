import 'dart:ffi';

import 'package:flutter/material.dart';
import 'package:parking_app/pages/CustomBottomNav.dart';
import 'package:parking_app/pages/KelolaMobilPage.dart';
import 'package:flutter/services.dart';
import 'package:lottie/lottie.dart'; // Pastikan ini di bagian atas file

import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class TambahKendaraanPage extends StatefulWidget {
  @override
  _TambahKendaraanPageState createState() => _TambahKendaraanPageState();
}

class UpperCaseTextFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    return newValue.copyWith(
      text: newValue.text.toUpperCase(),
      selection: newValue.selection,
    );
  }
}

class _TambahKendaraanPageState extends State<TambahKendaraanPage> {
  final int _selectedIndex = 2;
  final _formKey = GlobalKey<FormState>();

  int? selectedJenis;
  final _platController = TextEditingController();
  final _warnaController = TextEditingController();
  final _merkController = TextEditingController();

  List<Map<String, dynamic>> listJenis = [];

  Future<void> fetchJenisKendaraan() async {
    try {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      if (token == null) {
        throw Exception('Token tidak ditemukan di SharedPreferences');
      }

      final response = await http.get(
        Uri.parse('http://192.168.18.13:8000/api/kendaraan-index'),
        headers: {
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      print("Status Code: ${response.statusCode}");
      print("Response: ${response.body}");

      if (response.statusCode == 200) {
        final jsonData = jsonDecode(response.body);
        final List<dynamic> data = jsonData['data'];

        setState(() {
          listJenis =
              data.map<Map<String, dynamic>>((item) {
                return {
                  'id': item['id'],
                  'nama_kendaraan': item['nama_kendaraan'],
                };
              }).toList();
        });
      } else {
        throw Exception('Gagal mengambil data jenis kendaraan');
      }
    } catch (e) {
      print('Error: $e');
    }
  }

  Future<void> _submitForm() async {
    if (_formKey.currentState!.validate()) {
      final jenis = selectedJenis;
      final plat = _platController.text.trim();
      final warna = _warnaController.text.trim();
      final merk = _merkController.text.trim();

      SharedPreferences prefs = await SharedPreferences.getInstance();
      final userId = prefs.getInt('id_users');
      final token = prefs.getString('token');

      final url = Uri.parse('http://192.168.18.13:8000/api/kendaraan');

      try {
        final response = await http.post(
          url,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer $token',
          },
          body: jsonEncode({
            'id_jenis': jenis,
            'merk_kendaraan': merk,
            'plat_nomor': plat,
            'warna': warna,
            'user_id': userId,
          }),
        );

        print('Status Code: ${response.statusCode}');
        print('Body: ${response.body}');

        final contentType = response.headers['content-type'] ?? '';
        if (!contentType.contains('application/json')) {
          throw Exception(
            'Respons dari server bukan JSON.\n\n${response.body}',
          );
        }

        final responseData = jsonDecode(response.body);

        if (response.statusCode == 201 && responseData['status'] == 'success') {
          // Tampilkan dialog success
          showDialog(
            context: context,
            barrierDismissible: false,
            builder:
                (context) => Dialog(
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Lottie.asset(
                          'assets/lottie/success_check.json',
                          width: 120,
                          repeat: false,
                        ),
                        const SizedBox(height: 16),
                        const Text(
                          'Berhasil Ditambahkan!',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 16),
                        ElevatedButton(
                          onPressed: () {
                            Navigator.pop(context); // tutup dialog
                            Navigator.pushReplacement(
                              context,
                              MaterialPageRoute(
                                builder: (_) => KelolaMobilPage(),
                              ),
                            );
                          },
                          child: const Text('Oke'),
                        ),
                      ],
                    ),
                  ),
                ),
          );
        } else {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(
                responseData['message'] ?? 'Gagal menambahkan kendaraan',
              ),
            ),
          );
        }
      } catch (e) {
        print('ERROR: $e');
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Terjadi kesalahan: ${e.toString()}')),
        );
      }
    }
  }

  @override
  void initState() {
    super.initState();
    fetchJenisKendaraan();
  }

  @override
  void dispose() {
    _platController.dispose();
    _warnaController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: false,
        title: Text(
          'Tambah Kendaraan',
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
            Navigator.pop(context);
          },
        ),
      ),
      backgroundColor: Colors.white,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Center(
                  child: Column(
                    children: const [
                      Icon(
                        Icons.directions_car,
                        size: 48,
                        color: Colors.orange,
                      ),
                      SizedBox(height: 10),
                      Text(
                        'Tambah Kendaraan',
                        style: TextStyle(
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                          color: Colors.black87,
                        ),
                      ),
                      SizedBox(height: 4),
                      Text(
                        'Isi data kendaraanmu dengan benar',
                        style: TextStyle(fontSize: 13, color: Colors.grey),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 30),

                // Dropdown Jenis Kendaraan
                DropdownButtonFormField<int>(
                  value: selectedJenis,
                  decoration: const InputDecoration(
                    labelText: 'Jenis Kendaraan',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.all(Radius.circular(16)),
                    ),
                  ),
                  items:
                      listJenis.isNotEmpty
                          ? listJenis.map((item) {
                            return DropdownMenuItem<int>(
                              value: item['id'], // <-- Kirim ID
                              child: Text(item['nama_kendaraan']),
                            );
                          }).toList()
                          : [
                            const DropdownMenuItem<int>(
                              value: null,
                              child: Text('Memuat data...'),
                            ),
                          ],
                  onChanged: (value) {
                    setState(() {
                      selectedJenis = value; // <-- Simpan ID ke selectedJenis
                    });
                  },
                  validator:
                      (value) =>
                          value == null
                              ? 'Jenis kendaraan harus dipilih'
                              : null,
                ),

                const SizedBox(height: 20),

                // Warna Kendaraan
                TextFormField(
                  controller: _merkController,
                  decoration: const InputDecoration(
                    labelText: 'Merk Kendaraan',
                    hintText: 'Contoh: Toyota, Ducati',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.all(Radius.circular(16)),
                    ),
                  ),
                  validator:
                      (value) =>
                          value!.isEmpty
                              ? 'Merk kendaraan tidak boleh kosong'
                              : null,
                ),

                const SizedBox(height: 20),

                // Plat Nomor
                TextFormField(
                  controller: _platController,
                  textCapitalization:
                      TextCapitalization
                          .characters, // kapital otomatis saat diketik
                  inputFormatters: [
                    UpperCaseTextFormatter(), // memastikan huruf besar semua
                  ],
                  decoration: const InputDecoration(
                    labelText: 'Plat Nomor',
                    hintText: 'Contoh: B 1234 XYZ',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.all(Radius.circular(16)),
                    ),
                  ),
                  validator:
                      (value) =>
                          value!.isEmpty
                              ? 'Plat nomor tidak boleh kosong'
                              : null,
                ),

                const SizedBox(height: 20),

                // Warna Kendaraan
                TextFormField(
                  controller: _warnaController,
                  decoration: const InputDecoration(
                    labelText: 'Warna Kendaraan',
                    hintText: 'Contoh: Hitam, Putih',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.all(Radius.circular(16)),
                    ),
                  ),
                  validator:
                      (value) =>
                          value!.isEmpty
                              ? 'Warna kendaraan tidak boleh kosong'
                              : null,
                ),
                const SizedBox(height: 30),

                // Tombol Simpan
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: _submitForm,
                    icon: const Icon(Icons.add, color: Colors.white),
                    label: const Text(
                      'Tambah Kendaraan',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                        color: Colors.white,
                      ),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.black87,
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
      bottomNavigationBar: CustomBottomNav(selectedIndex: _selectedIndex),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
    );
  }
}
