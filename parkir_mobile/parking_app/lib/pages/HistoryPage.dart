import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:intl/intl.dart';
import 'package:parking_app/pages/CustomBottomNav.dart';

const Color orangePrimary = Color(0xFFEB6A00);
const Color darkPrimary = Color(0xFF0B1120);

class HistoryPage extends StatefulWidget {
  const HistoryPage({super.key});

  @override
  State<HistoryPage> createState() => _HistoryPageState();
}

class _HistoryPageState extends State<HistoryPage> {
  List<dynamic> riwayat = [];
  List<String> kendaraanList = [''];
  bool isLoading = true;

  int currentPage = 1;
  int lastPage = 1;
  int perPage = 5;

  int selectedIndex = 1;
  String selectedVehicle = '';
  DateTime? selectedDate;

  @override
  void initState() {
    super.initState();
    fetchRiwayat(page: 1);
  }

  Future<void> fetchRiwayat({int page = 1}) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    final userId = prefs.getInt('id_users');

    final queryParams = {
      'page': page.toString(),
      'per_page': perPage.toString(),
      if (selectedVehicle.isNotEmpty) 'plat_nomor': selectedVehicle,
      if (selectedDate != null)
        'tanggal': DateFormat('yyyy-MM-dd').format(selectedDate!),
    };

    final uri = Uri.parse(
      'http://192.168.18.13:8000/api/tampil-riwayat-kendaraan-all/$userId',
    ).replace(queryParameters: queryParams);

    try {
      final response = await http.get(
        uri,
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body)['data'];
        final List<dynamic> pageData = data['data'];

        setState(() {
          riwayat = pageData;
          kendaraanList =
              [''] +
              pageData.map((e) => e['plat_nomor'] as String).toSet().toList();
          currentPage = data['current_page'];
          lastPage = data['last_page'];
          isLoading = false;
        });
      } else {
        setState(() {
          riwayat = [];
          isLoading = false;
        });
      }
    } catch (e) {
      setState(() => isLoading = false);
    }
  }

  Future<void> _selectDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: selectedDate ?? DateTime.now(),
      firstDate: DateTime(2024),
      lastDate: DateTime.now(),
    );
    if (picked != null) {
      setState(() {
        selectedDate = picked;
        isLoading = true;
      });
      fetchRiwayat(page: 1);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [orangePrimary, Color(0xFFFFC107)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: Column(
            children: [
              _buildHeader(),
              Expanded(
                child: Container(
                  decoration: const BoxDecoration(
                    color: Color(0xFFF2F4F7),
                    borderRadius: BorderRadius.vertical(
                      top: Radius.circular(30),
                    ),
                  ),
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      children: [
                        _buildFilterCard(),
                        const SizedBox(height: 20),
                        _buildHistoryList(),
                        const SizedBox(height: 16),
                        _buildPagination(),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: CustomBottomNav(selectedIndex: selectedIndex),
      floatingActionButton: const CustomFloatingActionButton(),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
    );
  }

  // ================= HEADER =================
  Widget _buildHeader() {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: Row(
        children: const [
          Icon(Icons.history, color: Colors.white),
          SizedBox(width: 10),
          Text(
            "Riwayat Parkir",
            style: TextStyle(
              color: Colors.white,
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  // ================= FILTER =================
  Widget _buildFilterCard() {
    return _PremiumCard(
      title: "Filter Riwayat",
      child: Column(
        children: [
          TextField(
            readOnly: true,
            decoration: InputDecoration(
              hintText:
                  selectedDate == null
                      ? "Pilih Tanggal"
                      : DateFormat('yyyy-MM-dd').format(selectedDate!),
              prefixIcon: const Icon(Icons.calendar_today),
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(14),
              ),
            ),
            onTap: _selectDate,
          ),
          const SizedBox(height: 12),
          DropdownButtonFormField<String>(
            value: selectedVehicle,
            decoration: InputDecoration(
              prefixIcon: const Icon(Icons.directions_car),
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(14),
              ),
            ),
            items:
                kendaraanList
                    .map(
                      (p) => DropdownMenuItem(
                        value: p,
                        child: Text(p.isEmpty ? 'Semua Kendaraan' : p),
                      ),
                    )
                    .toList(),
            onChanged: (val) {
              setState(() {
                selectedVehicle = val ?? '';
                isLoading = true;
              });
              fetchRiwayat(page: 1);
            },
          ),
          const SizedBox(height: 12),
          DropdownButtonFormField<int>(
            value: perPage,
            decoration: InputDecoration(
              prefixIcon: const Icon(Icons.list),
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(14),
              ),
            ),
            items:
                [5, 10, 20]
                    .map(
                      (e) => DropdownMenuItem(
                        value: e,
                        child: Text("Tampilkan $e data"),
                      ),
                    )
                    .toList(),
            onChanged: (val) {
              setState(() {
                perPage = val!;
                isLoading = true;
              });
              fetchRiwayat(page: 1);
            },
          ),
        ],
      ),
    );
  }

  // ================= HISTORY LIST =================
  Widget _buildHistoryList() {
    if (isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (riwayat.isEmpty) {
      return const Text("Data parkir tidak ditemukan.");
    }

    return Column(
      children:
          riwayat.map((e) {
            final statusColor =
                e['status'] == 'Sudah Dibayar' ? Colors.green : Colors.red;
            return Container(
              margin: const EdgeInsets.only(bottom: 14),
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black12.withOpacity(0.06),
                    blurRadius: 10,
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          const FaIcon(FontAwesomeIcons.carSide, size: 14),
                          const SizedBox(width: 8),
                          Text(
                            "${e['merk_kendaraan']} (${e['plat_nomor']})",
                            style: const TextStyle(fontWeight: FontWeight.bold),
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 4,
                        ),
                        decoration: BoxDecoration(
                          color: statusColor.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          e['status'],
                          style: TextStyle(
                            color: statusColor,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Text("Masuk : ${e['waktu_masuk']}"),
                  Text("Keluar : ${e['waktu_keluar']}"),
                  Text("Durasi : ${e['durasi']}"),
                  const SizedBox(height: 4),
                  Text(
                    "Biaya : Rp ${e['tarif']}",
                    style: const TextStyle(fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            );
          }).toList(),
    );
  }

  // ================= PAGINATION =================
  Widget _buildPagination() {
    if (lastPage <= 1) return const SizedBox();

    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        IconButton(
          onPressed:
              currentPage > 1
                  ? () {
                    setState(() => isLoading = true);
                    fetchRiwayat(page: currentPage - 1);
                  }
                  : null,
          icon: const Icon(Icons.chevron_left),
        ),
        Text("Halaman $currentPage dari $lastPage"),
        IconButton(
          onPressed:
              currentPage < lastPage
                  ? () {
                    setState(() => isLoading = true);
                    fetchRiwayat(page: currentPage + 1);
                  }
                  : null,
          icon: const Icon(Icons.chevron_right),
        ),
      ],
    );
  }
}

// ================= PREMIUM CARD =================
class _PremiumCard extends StatelessWidget {
  final String title;
  final Widget child;

  const _PremiumCard({required this.title, required this.child});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(color: Colors.black12.withOpacity(0.06), blurRadius: 10),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
          ),
          const SizedBox(height: 12),
          child,
        ],
      ),
    );
  }
}
