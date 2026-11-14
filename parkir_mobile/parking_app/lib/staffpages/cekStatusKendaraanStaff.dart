import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:parking_app/staffpages/TampilDendaPage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class Cekstatuskendaraanstaff extends StatefulWidget {
  final String platNomor;

  const Cekstatuskendaraanstaff({super.key, required this.platNomor});

  @override
  State<Cekstatuskendaraanstaff> createState() =>
      _CekstatuskendaraanstaffState();
}

class _CekstatuskendaraanstaffState extends State<Cekstatuskendaraanstaff> {
  Map<String, dynamic>? dataKendaraan;
  bool loading = true;

  @override
  void initState() {
    super.initState();
    fetchDataKendaraan();
  }

  Future<void> fetchDataKendaraan() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final url =
          'http://192.168.18.13:8000/api/data-parkir/plat/${widget.platNomor}';
      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Accept': 'application/json',
          if (token != null) 'Authorization': 'Bearer $token',
        },
      );

      final result = jsonDecode(response.body);

      if (result['status'] == 'denda') {
        setState(() {
          dataKendaraan = {
            "status": "denda",
            "plat_nomor": widget.platNomor,
            "message": result["message"] ?? "",
            "merk_kendaraan": "-",
            "tipe_kendaraan": "-",
            "warna_kendaraan": "-",
            "tarif": "-",
            "nama_user": "-",
            "lokasi": "-",
          };
        });
        return;
      }

      if (result['status'] == 'success' && result['data'] != null) {
        final data = result['data'];
        setState(() {
          dataKendaraan = {
            "plat_nomor": data['plat_nomor'] ?? widget.platNomor,
            "status": data['status'] ?? "-",
            "merk_kendaraan": data['merk_kendaraan'] ?? "-",
            "tipe_kendaraan": data['tipe_kendaraan'] ?? "-",
            "warna_kendaraan": data['warna_kendaraan'] ?? "-",
            "tarif": data['tarif']?.toString() ?? "-",
            "nama_user": data['nama_user'] ?? "-",
            "kode_parkir": data['kode_parkir'] ?? "-",
            "waktu_masuk": data['waktu_masuk'] ?? "-",
            "waktu_keluar": data['waktu_keluar'] ?? "-",
            "lokasi": data['lokasi'] ?? "-",
          };
        });
        return;
      }

      showMessage("Data tidak valid.");
    } catch (e) {
      showMessage("Terjadi kesalahan: $e");
    } finally {
      setState(() => loading = false);
    }
  }

  void showMessage(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(msg)));
  }

  Widget _buildStatusSection(String status) {
    final isPaid = status == 'Sudah Dibayar';
    final isDenda = status.toLowerCase() == 'denda';

    Color iconColor =
        isPaid
            ? const Color(0xFF059669)
            : (isDenda ? Colors.redAccent : const Color(0xFFB91C1C));

    return Column(
      children: [
        Icon(
          isPaid ? Icons.check_circle_rounded : Icons.cancel_rounded,
          color: iconColor,
          size: 60,
        ),
        const SizedBox(height: 8),
        Text(
          status.toUpperCase(),
          style: TextStyle(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: iconColor,
          ),
        ),
        const SizedBox(height: 12),
        const Divider(thickness: 1.2),
      ],
    );
  }

  Widget _buildRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: TextStyle(color: Colors.grey[600], fontSize: 14)),
          Flexible(
            child: Text(
              value,
              style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
              textAlign: TextAlign.right,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildContent() {
    final status = dataKendaraan!['status'] ?? "-";
    final isDenda = status.toLowerCase() == "denda";

    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
      child: Card(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        elevation: 3,
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            children: [
              _buildStatusSection(status),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(10),
                decoration: const BoxDecoration(
                  color: Color(0xFFD1FADF),
                  shape: BoxShape.circle,
                ),
                child: const FaIcon(
                  FontAwesomeIcons.carRear,
                  size: 28,
                  color: Color(0xFF16A34A),
                ),
              ),
              const SizedBox(height: 10),
              const Text(
                'Informasi Kendaraan',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: Colors.black87,
                ),
              ),
              const SizedBox(height: 20),
              _buildRow(
                "Plat Nomor",
                dataKendaraan!["plat_nomor"]?.toString() ?? "-",
              ),
              _buildRow(
                "Merk Kendaraan",
                dataKendaraan!["merk_kendaraan"]?.toString() ?? "-",
              ),
              _buildRow(
                "Tipe Kendaraan",
                dataKendaraan!["tipe_kendaraan"]?.toString() ?? "-",
              ),
              _buildRow(
                "Warna Kendaraan",
                dataKendaraan!["warna_kendaraan"]?.toString() ?? "-",
              ),
              _buildRow(
                "Nama User",
                dataKendaraan!["nama_user"]?.toString() ?? "-",
              ),
              if (!isDenda) ...[
                _buildRow(
                  "Tarif",
                  "Rp ${dataKendaraan!["tarif"]?.toString() ?? '0'}",
                ),
                _buildRow(
                  "Waktu Masuk",
                  dataKendaraan!["waktu_masuk"]?.toString() ?? "-",
                ),
                _buildRow(
                  "Waktu Keluar",
                  dataKendaraan!["waktu_keluar"]?.toString() ?? "-",
                ),
                _buildRow(
                  "Lokasi",
                  dataKendaraan!["lokasi"]?.toString() ?? "-",
                ),
              ],
              if (isDenda)
                Padding(
                  padding: const EdgeInsets.only(top: 14.0),
                  child: Text(
                    dataKendaraan!["message"] ?? "",
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: Colors.red, fontSize: 14),
                  ),
                ),

              // ================= Button "Beri Denda" =================
              if (!isDenda)
                Padding(
                  padding: const EdgeInsets.only(top: 20.0),
                  child: ElevatedButton.icon(
                    onPressed: () {
                      // Navigasi ke halaman TampilDenda dan kirim platNomor
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder:
                              (context) => TampilDendaPage(
                                platNomor: dataKendaraan!["plat_nomor"],
                              ),
                        ),
                      );
                    },
                    icon: const Icon(
                      Icons.warning_amber_rounded,
                      color: Colors.white,
                    ),
                    label: const Text(
                      "Beri Denda",
                      style: TextStyle(color: Colors.white),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.redAccent,
                      padding: const EdgeInsets.symmetric(
                        vertical: 12.0,
                        horizontal: 20.0,
                      ),
                      textStyle: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  // ================= Fungsi beri denda =================

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF9FAFB),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 1,
        centerTitle: true,
        iconTheme: const IconThemeData(color: Colors.black),
        title: const Text(
          'Status Kendaraan',
          style: TextStyle(
            color: Colors.black87,
            fontWeight: FontWeight.w600,
            fontSize: 16,
          ),
        ),
      ),
      body:
          loading
              ? const Center(child: CircularProgressIndicator())
              : (dataKendaraan == null
                  ? const Center(child: Text("Data kendaraan tidak ditemukan."))
                  : _buildContent()),
    );
  }
}
