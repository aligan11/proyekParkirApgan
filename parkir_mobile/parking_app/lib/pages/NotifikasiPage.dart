import 'dart:convert';
import 'package:parking_app/pages/DetailDendaPage.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

final Color orangePrimary = Color(0xFFF9A93B);

class NotifikasiPage extends StatefulWidget {
  const NotifikasiPage({super.key});

  @override
  State<NotifikasiPage> createState() => _NotifikasiPageState();
}

class _NotifikasiPageState extends State<NotifikasiPage> {
  List<dynamic> _dendaList = [];
  String timeAgo(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inSeconds < 60) {
      return 'baru saja';
    } else if (difference.inMinutes < 60) {
      return '${difference.inMinutes} menit yang lalu';
    } else if (difference.inHours < 24) {
      return '${difference.inHours} jam yang lalu';
    } else if (difference.inDays < 7) {
      return '${difference.inDays} hari yang lalu';
    } else {
      return '${dateTime.day}-${dateTime.month}-${dateTime.year}';
    }
  }

  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    fetchDendaData();
  }

  Future<void> fetchDendaData() async {
    setState(() => isLoading = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      final userId = prefs.getInt('id_users'); // Ambil dari SharedPreferences

      if (token == null || userId == null) {
        throw Exception('Token atau ID user tidak ditemukan.');
      }

      final response = await http.get(
        Uri.parse('http://192.168.18.13:8000/api/denda/user/$userId'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
      );

      if (response.statusCode == 200) {
        final res = jsonDecode(response.body);
        if (res['status'] == 'denda') {
          setState(() {
            _dendaList = res['data'];
            isLoading = false;
          });
        } else {
          setState(() {
            _dendaList = [];
            isLoading = false;
          });
        }
      } else {
        throw Exception(
          'Gagal memuat data denda. Kode: ${response.statusCode}',
        );
      }
    } catch (e) {
      setState(() {
        isLoading = false;
      });
      debugPrint('Error: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text(
          'Notifikasi',
          style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold),
        ),
        backgroundColor: Colors.white,
        elevation: 2,
        surfaceTintColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body:
          isLoading
              ? const Center(child: CircularProgressIndicator())
              : _dendaList.isEmpty
              ? const Center(child: Text('Tidak ada notifikasi denda.'))
              : ListView.separated(
                padding: const EdgeInsets.all(16),
                itemCount: _dendaList.length,
                separatorBuilder:
                    (context, index) => const SizedBox(height: 12),
                itemBuilder: (context, index) {
                  final denda = _dendaList[index];
                  final waktu = DateTime.tryParse(
                    denda['waktu_pelanggaran'] ?? '',
                  );
                  final formattedTime =
                      waktu != null ? timeAgo(waktu) : 'Waktu tidak diketahui';
                  return Card(
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    elevation: 4,
                    shadowColor: Colors.black12,
                    child: ListTile(
                      leading: Container(
                        padding: const EdgeInsets.all(8),
                        decoration: BoxDecoration(
                          color: orangePrimary.withOpacity(0.15),
                          shape: BoxShape.circle,
                        ),
                        child: Icon(
                          Icons.warning_amber_rounded,
                          color: orangePrimary,
                        ),
                      ),
                      title: Text(
                        'Pelanggaran: ${denda['jenis_pelanggaran']}',
                        style: const TextStyle(fontWeight: FontWeight.bold),
                      ),
                      subtitle: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Plat Nomor: ${denda['plat_nomor']}'),
                          Text('Denda: Rp ${denda['denda']}'),
                          Text('Status: ${denda['status']}'),
                          const SizedBox(height: 4),
                          Text(
                            formattedTime,
                            style: const TextStyle(
                              fontSize: 12,
                              color: Colors.grey,
                            ),
                          ),
                        ],
                      ),
                      onTap: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder:
                                (context) => DetailDendaPage(dataDenda: denda),
                          ),
                        );
                      },
                    ),
                  );
                },
              ),
    );
  }
}
