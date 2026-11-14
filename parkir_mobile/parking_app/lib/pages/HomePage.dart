import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:intl/intl.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:parking_app/pages/CustomBottomNav.dart';
import 'package:parking_app/pages/HistoryPage.dart';
import 'package:parking_app/pages/KelolaMobilPage.dart';
import 'package:parking_app/pages/NotifikasiPage.dart';
import 'package:parking_app/pages/TambahKendaraanPage.dart';
import 'package:parking_app/pages/IsiUlangPage.dart';
import 'package:shared_preferences/shared_preferences.dart';

const Color orangePrimary = Color(0xFFEB6A00);
const Color darkPrimary = Color(0xFF0B1120);

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  List<Map<String, dynamic>> kendaraanList = [];
  List<dynamic> _riwayatList = [];
  bool _loadingRiwayat = true;

  int saldo = 0;
  bool hasPendingDenda = false;
  int _selectedIndex = 0;

  String namaLengkap = 'User';
  String foto_users = '';

  String getGreeting() {
    final h = DateTime.now().hour;
    if (h < 12) return 'Selamat Pagi';
    if (h < 15) return 'Selamat Siang';
    if (h < 18) return 'Selamat Sore';
    return 'Selamat Malam';
  }

  @override
  void initState() {
    super.initState();
    _loadShared();
    fetchKendaraan();
    fetchSaldo();
    _fetchRiwayat();
    checkPendingDenda();
  }

  Future<void> _loadShared() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      namaLengkap = prefs.getString('nama_lengkap') ?? 'User';
      foto_users =
          'http://192.168.18.13:8000/storage/${prefs.getString('foto_users') ?? ''}';
    });
  }

  // ================= API =================
  Future<void> fetchSaldo() async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getInt('id_users');
    final token = prefs.getString('token');

    final res = await http.get(
      Uri.parse('http://192.168.18.13:8000/api/user-show/$userId'),
      headers: {'Authorization': 'Bearer $token'},
    );

    if (res.statusCode == 200) {
      final json = jsonDecode(res.body);
      saldo = int.parse(json['data']['data'][0]['saldo'].toString());
      setState(() {});
    }
  }

  Future<void> fetchKendaraan() async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getInt('id_users');
    final token = prefs.getString('token');

    final res = await http.get(
      Uri.parse('http://192.168.18.13:8000/api/kendaraan-show/$userId'),
      headers: {'Authorization': 'Bearer $token'},
    );

    if (res.statusCode == 200) {
      final data = jsonDecode(res.body)['data']['data'] as List;
      kendaraanList =
          data.map<Map<String, dynamic>>((e) {
            return {
              'nama': e['tipe_kendaraan'],
              'plat': e['plat_nomor'],
              'is_aktif': e['is_aktif'],
            };
          }).toList();
      setState(() {});
    }
  }

  Future<void> _fetchRiwayat() async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getInt('id_users');
    final token = prefs.getString('token');

    final res = await http.get(
      Uri.parse(
        'http://192.168.18.13:8000/api/tampil-riwayat-kendaraan/$userId',
      ),
      headers: {'Authorization': 'Bearer $token'},
    );

    if (res.statusCode == 200) {
      _riwayatList = jsonDecode(res.body)['data']['data'].take(3).toList();
    }
    _loadingRiwayat = false;
    setState(() {});
  }

  Future<void> checkPendingDenda() async {
    final prefs = await SharedPreferences.getInstance();
    final idUsers = prefs.getInt('id_users');
    final token = prefs.getString('token');

    final res = await http.get(
      Uri.parse('http://192.168.18.13:8000/api/denda/user/$idUsers'),
      headers: {'Authorization': 'Bearer $token'},
    );

    if (res.statusCode == 200) {
      final data = jsonDecode(res.body)['data'] as List;
      hasPendingDenda = data.any((e) => e['status'] == 'pending');
      setState(() {});
    }
  }

  // ================= UI =================
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
              _buildPremiumHeader(),
              Expanded(
                child: Container(
                  decoration: const BoxDecoration(
                    gradient: LinearGradient(
                      colors: [Color(0xFFF8F9FC), Color(0xFFEDEFF5)],
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                    ),
                    borderRadius: BorderRadius.vertical(
                      top: Radius.circular(30),
                    ),
                  ),
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      children: [
                        _buildSaldoCard(),
                        const SizedBox(height: 20),
                        _buildKendaraanAktifCard(),
                        const SizedBox(height: 20),
                        _buildRiwayatSection(),
                      ],
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: CustomBottomNav(selectedIndex: _selectedIndex),
      floatingActionButton: const CustomFloatingActionButton(),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
    );
  }

  // ================= HEADER =================
  Widget _buildPremiumHeader() {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  getGreeting(),
                  style: const TextStyle(color: Colors.white70),
                ),
                const SizedBox(height: 4),
                Text(
                  namaLengkap,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 22,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
          Stack(
            children: [
              IconButton(
                icon: const Icon(Icons.notifications, color: Colors.white),
                onPressed:
                    () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => NotifikasiPage()),
                    ),
              ),
              if (hasPendingDenda)
                Positioned(
                  top: 6,
                  right: 6,
                  child: Container(
                    width: 10,
                    height: 10,
                    decoration: const BoxDecoration(
                      color: Colors.red,
                      shape: BoxShape.circle,
                    ),
                  ),
                ),
            ],
          ),
          CircleAvatar(
            radius: 22,
            backgroundImage:
                foto_users.isNotEmpty
                    ? NetworkImage(foto_users)
                    : const NetworkImage(
                      'https://ui-avatars.com/api/?name=User',
                    ),
          ),
        ],
      ),
    );
  }

  // ================= SALDO =================
  Widget _buildSaldoCard() {
    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [darkPrimary, Color(0xFF1C2541)],
        ),
        borderRadius: BorderRadius.circular(22),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.25),
            blurRadius: 16,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                "Saldo Parkir",
                style: TextStyle(color: Colors.white70),
              ),
              const SizedBox(height: 8),
              Text(
                "Rp ${NumberFormat.currency(locale: 'id_ID', symbol: '', decimalDigits: 0).format(saldo)}",
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 26,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const FaIcon(FontAwesomeIcons.wallet, color: Colors.white),
        ],
      ),
    );
  }

  // ================= KENDARAAN =================
  Widget _buildKendaraanAktifCard() {
    final aktif = kendaraanList.firstWhere(
      (e) => e['is_aktif'] == 'on',
      orElse: () => {},
    );
    if (aktif.isEmpty) return const SizedBox();

    return _PremiumGradientCard(
      title: "Kendaraan Aktif",
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                aktif['nama'],
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
              Text(aktif['plat']),
            ],
          ),
          _MiniButton(
            text: "Kelola",
            onTap:
                () => Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => KelolaMobilPage()),
                ),
          ),
        ],
      ),
    );
  }

  // ================= RIWAYAT =================
  Widget _buildRiwayatSection() {
    return _PremiumGradientCard(
      title: "Riwayat Parkir",
      trailing: TextButton(
        onPressed:
            () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => HistoryPage()),
            ),
        child: const Text("Lihat Semua"),
      ),
      child:
          _loadingRiwayat
              ? const CircularProgressIndicator()
              : Column(
                children:
                    _riwayatList.map((e) {
                      final warna =
                          e['status'] == 'lunas' ? Colors.green : Colors.red;
                      return Container(
                        margin: const EdgeInsets.only(bottom: 10),
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [warna.withOpacity(0.15), Colors.white],
                          ),
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(e['kode_parkir']),
                            Text(e['status'], style: TextStyle(color: warna)),
                          ],
                        ),
                      );
                    }).toList(),
              ),
    );
  }
}

// ================= REUSABLE =================
class _PremiumGradientCard extends StatelessWidget {
  final String title;
  final Widget child;
  final Widget? trailing;

  const _PremiumGradientCard({
    required this.title,
    required this.child,
    this.trailing,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 20),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Colors.white, Color(0xFFF1F3F8)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(22),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 14,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 14,
                ),
              ),
              if (trailing != null) trailing!,
            ],
          ),
          const SizedBox(height: 12),
          child,
        ],
      ),
    );
  }
}

class _MiniButton extends StatelessWidget {
  final String text;
  final VoidCallback onTap;

  const _MiniButton({required this.text, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return ElevatedButton(
      onPressed: onTap,
      style: ElevatedButton.styleFrom(
        backgroundColor: darkPrimary,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      ),
      child: Text(text, style: TextStyle(color: Colors.white)),
    );
  }
}
