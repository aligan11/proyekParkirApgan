import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:parking_app/pages/CustomBottomNav.dart';
import 'package:parking_app/pages/IsiUlangPage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class RiwayatTopUpPage extends StatefulWidget {
  const RiwayatTopUpPage({Key? key}) : super(key: key);

  @override
  State<RiwayatTopUpPage> createState() => _RiwayatTopUpPageState();
}

class _RiwayatTopUpPageState extends State<RiwayatTopUpPage> {
  List<dynamic> topupList = [];
  bool isLoading = true;
  int currentPage = 1;
  int lastPage = 1;
  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();
    fetchTopupHistory();
  }

  Future<void> fetchTopupHistory({int page = 1}) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token') ?? '';
    final userId = prefs.getInt('id_users') ?? 0;

    setState(() {
      isLoading = true;
    });

    final response = await http.get(
      Uri.parse(
        'http://192.168.18.13:8000/api/topup-show-saldo-all/$userId?page=$page',
      ),
      headers: {'Authorization': 'Bearer $token'},
    );

    if (response.statusCode == 200) {
      final body = jsonDecode(response.body);
      final pagination = body['data']['pagination'];

      setState(() {
        topupList = body['data']['data'];
        currentPage = pagination['current_page'];
        lastPage = pagination['last_page'];
        isLoading = false;
      });
    } else {
      setState(() => isLoading = false);
      print("Gagal fetch topup: ${response.body}");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text(
          'Riwayat TopUp Saldo',
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: Colors.black,
          ),
        ),
        backgroundColor: Colors.white,
        centerTitle: true,
        elevation: 2,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () {
            Navigator.push(
              context,
              MaterialPageRoute(builder: (context) => const IsiUlangPage()),
            );
          },
        ),
      ),
      body:
          isLoading
              ? const Center(child: CircularProgressIndicator())
              : topupList.isEmpty
              ? _buildEmptyState()
              : Column(
                children: [
                  const SizedBox(height: 12),
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Row(
                      children: [
                        _buildFilterChip('Semua', true),
                        _buildFilterChip('Diproses', false),
                        _buildFilterChip('Berhasil', false),
                        _buildFilterChip('Gagal', false),
                      ],
                    ),
                  ),
                  const SizedBox(height: 8),
                  Expanded(
                    child: ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: topupList.length,
                      itemBuilder: (context, index) {
                        final item = topupList[index];
                        return _buildTopupCard(item);
                      },
                    ),
                  ),
                  // Pagination Controls
                  Padding(
                    padding: const EdgeInsets.only(bottom: 12),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        ElevatedButton(
                          onPressed:
                              currentPage > 1
                                  ? () =>
                                      fetchTopupHistory(page: currentPage - 1)
                                  : null,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.black,
                            disabledBackgroundColor: Colors.grey.shade300,
                          ),
                          child: const Text(
                            "Previous",
                            style: TextStyle(color: Colors.white),
                          ),
                        ),
                        const SizedBox(width: 16),
                        Text(
                          "Page $currentPage / $lastPage",
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                          ),
                        ),
                        const SizedBox(width: 16),
                        ElevatedButton(
                          onPressed:
                              currentPage < lastPage
                                  ? () =>
                                      fetchTopupHistory(page: currentPage + 1)
                                  : null,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.black,
                            disabledBackgroundColor: Colors.grey.shade300,
                          ),
                          child: const Text(
                            "Next",
                            style: TextStyle(
                              color: Colors.white,
                            ), // <-- teks putih
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
      bottomNavigationBar: CustomBottomNav(selectedIndex: _selectedIndex),
      floatingActionButton: FloatingActionButton(
        onPressed: () {},
        backgroundColor: Colors.black,
        shape: const CircleBorder(),
        child: const FaIcon(FontAwesomeIcons.qrcode, color: Colors.white),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
    );
  }

  Widget _buildFilterChip(String label, bool selected) {
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: OutlinedButton(
        onPressed: () {
          // TODO: Tambahkan logika filter jika dibutuhkan
        },
        style: OutlinedButton.styleFrom(
          backgroundColor: selected ? Colors.orange.shade50 : Colors.white,
          side: BorderSide(
            color: selected ? Colors.orange : Colors.grey.shade300,
          ),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(30),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        ),
        child: Text(
          label,
          style: TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: selected ? Colors.orange : Colors.grey.shade600,
          ),
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 24),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const SizedBox(height: 40),
          Image.asset('assets/images/kaca.png', width: 96, height: 96),
          const SizedBox(height: 16),
          const Text(
            'Belum ada transaksi top up',
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.w600,
              color: Colors.black,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 8),
          const Text(
            'Silakan lakukan isi ulang saldo parkir untuk melihat riwayat transaksi Anda.',
            style: TextStyle(fontSize: 14, color: Colors.grey),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 32),
          ElevatedButton(
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (context) => const IsiUlangPage()),
              );
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.black,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(30),
              ),
              padding: const EdgeInsets.symmetric(vertical: 14),
            ),
            child: const SizedBox(
              width: double.infinity,
              child: Text(
                'Isi Ulang Sekarang',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ),
          ),
          const SizedBox(height: 12),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.orange,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(30),
              ),
              padding: const EdgeInsets.symmetric(vertical: 14),
            ),
            child: const SizedBox(
              width: double.infinity,
              child: Text(
                'Kembali ke Beranda',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTopupCard(dynamic item) {
    return Card(
      elevation: 3,
      margin: const EdgeInsets.only(bottom: 16),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              "Paket Topup: ${item['nominal']}",
              style: const TextStyle(fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 6),
            Text("Biaya Admin: Rp${item['biaya_admin']}"),
            Text("Total Dibayar: Rp${item['total_dibayar']}"),
            Text("Metode: ${item['metode_pembayaran']}"),
            const SizedBox(height: 6),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  "Status: ${item['status']}",
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    color:
                        item['status'] == 'success'
                            ? Colors.green
                            : item['status'] == 'pending'
                            ? Colors.orange
                            : Colors.red,
                  ),
                ),
                Text(
                  item['created_at'].toString().substring(0, 10),
                  style: const TextStyle(fontSize: 12, color: Colors.grey),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
