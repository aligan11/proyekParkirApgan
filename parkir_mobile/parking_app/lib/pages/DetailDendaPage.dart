import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:http/http.dart' as http;
import 'package:parking_app/pages/HomePage.dart';
import 'package:shared_preferences/shared_preferences.dart';

final Color orangePrimary = Color(0xFFF9A93B);

class DetailDendaPage extends StatefulWidget {
  final Map<String, dynamic> dataDenda;

  const DetailDendaPage({super.key, required this.dataDenda});

  @override
  State<DetailDendaPage> createState() => _DetailDendaPageState();
}

class _DetailDendaPageState extends State<DetailDendaPage> {
  bool isLoading = false;
  late Map<String, dynamic> dataDenda;

  @override
  void initState() {
    super.initState();
    dataDenda = widget.dataDenda;
  }

  String formatDate(String rawDate) {
    try {
      final dateTime = DateTime.parse(rawDate).toLocal();
      return DateFormat('dd MMMM yyyy, HH:mm', 'id_ID').format(dateTime);
    } catch (e) {
      return 'Waktu tidak valid';
    }
  }

  Future<void> bayarDenda() async {
    setState(() => isLoading = true);
    final prefs = await SharedPreferences.getInstance();
    final idUsers = prefs.getInt('id_users');
    final token = prefs.getString('token');

    if (idUsers == null || token == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('User tidak ditemukan di local storage')),
      );
      return;
    }

    try {
      final response = await http.put(
        Uri.parse('http://192.168.18.13:8000/api/denda/bayar'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $token',
        },
        body: jsonEncode({'id_users': idUsers, 'id_denda': dataDenda['id']}),
      );

      setState(() => isLoading = false);

      if (response.statusCode == 200) {
        final res = jsonDecode(response.body);
        setState(() {
          dataDenda['status'] = 'success';
        });

        showDialog(
          context: context,
          builder:
              (_) => AlertDialog(
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                title: const Text('Berhasil'),
                content: Text(res['message'] ?? 'Pembayaran berhasil.'),
                actions: [
                  TextButton(
                    onPressed: () => Navigator.pop(context),
                    child: const Text('Tutup'),
                  ),
                ],
              ),
        );
      } else {
        final res = jsonDecode(response.body);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(res['message'] ?? 'Gagal membayar denda')),
        );
      }
    } catch (e) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Terjadi kesalahan: $e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    final isPending = dataDenda['status'] == 'pending';

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text(
          'Detail Denda',
          style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold),
        ),
        backgroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.black),
        elevation: 1,
      ),
      body: Stack(
        children: [
          SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                Card(
                  elevation: 4,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      children: [
                        Icon(
                          Icons.warning_amber_rounded,
                          size: 48,
                          color: orangePrimary,
                        ),
                        const SizedBox(height: 12),
                        Text(
                          'Pelanggaran: ${dataDenda['jenis_pelanggaran']}',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Colors.black87,
                          ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 8),
                        Text(
                          dataDenda['keterangan'] ?? '-',
                          textAlign: TextAlign.center,
                          style: const TextStyle(color: Colors.black54),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                _buildDetailItem(
                  Icons.directions_car,
                  'Plat Nomor',
                  dataDenda['plat_nomor'],
                ),
                _buildDetailItem(
                  Icons.calendar_month,
                  'Waktu Pelanggaran',
                  formatDate(dataDenda['waktu_pelanggaran']),
                ),
                _buildDetailItem(
                  Icons.money,
                  'Nominal Denda',
                  'Rp ${dataDenda['denda']}',
                ),
                _buildStatusBadge(dataDenda['status']),
                const SizedBox(height: 30),
                if (isPending)
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: bayarDenda,
                      icon: const Icon(Icons.payment),
                      label: const Text(
                        'Bayar Denda Sekarang',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      style: ElevatedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        backgroundColor: orangePrimary,
                        foregroundColor: Colors.white,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  )
                else
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      onPressed:
                          () => Navigator.push(
                            context,
                            MaterialPageRoute(builder: (context) => HomePage()),
                          ),
                      icon: const Icon(Icons.arrow_back),
                      label: const Text(
                        'Kembali ke Halaman Sebelumnya',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        foregroundColor: orangePrimary,
                        side: BorderSide(color: orangePrimary, width: 1.8),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),
              ],
            ),
          ),
          if (isLoading) const Center(child: CircularProgressIndicator()),
        ],
      ),
    );
  }

  Widget _buildDetailItem(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: orangePrimary),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
                const SizedBox(height: 4),
                Text(value, style: const TextStyle(color: Colors.black54)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusBadge(String status) {
    final isPending = status == 'pending';
    final isSuccess = status == 'success';

    final badgeColor = isPending ? Colors.orange.shade600 : Colors.green;
    final badgeText = isPending ? 'Belum Dibayar' : 'Sudah Lunas';

    return Container(
      width: double.infinity,
      margin: const EdgeInsets.only(top: 8),
      padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 20),
      decoration: BoxDecoration(
        color: badgeColor.withOpacity(0.1),
        border: Border.all(color: badgeColor),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        children: [
          Icon(
            isPending ? Icons.hourglass_bottom : Icons.check_circle,
            color: badgeColor,
          ),
          const SizedBox(width: 12),
          Text(
            badgeText,
            style: TextStyle(
              fontWeight: FontWeight.bold,
              color: badgeColor,
              fontSize: 16,
            ),
          ),
        ],
      ),
    );
  }
}
