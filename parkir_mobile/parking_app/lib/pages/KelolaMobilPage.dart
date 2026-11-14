import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import 'package:parking_app/pages/CustomBottomNav.dart';
import 'package:parking_app/pages/TambahKendaraanPage.dart';

const Color orangePrimary = Color(0xFFEB6A00);
const Color darkPrimary = Color(0xFF0B1120);

class KelolaMobilPage extends StatefulWidget {
  @override
  State<KelolaMobilPage> createState() => _KelolaMobilPageState();
}

class _KelolaMobilPageState extends State<KelolaMobilPage> {
  final int _selectedIndex = 2;
  List<Map<String, dynamic>> kendaraanList = [];

  @override
  void initState() {
    super.initState();
    fetchKendaraan();
  }

  // ================= API =================
  Future<void> fetchKendaraan() async {
    final prefs = await SharedPreferences.getInstance();
    final userId = prefs.getInt('id_users');
    final token = prefs.getString('token');
    if (userId == null || token == null) return;

    final res = await http.get(
      Uri.parse('http://192.168.18.13:8000/api/kendaraan-show/$userId'),
      headers: {'Authorization': 'Bearer $token'},
    );

    if (res.statusCode == 200) {
      final data = jsonDecode(res.body)['data']['data'] as List;
      setState(() {
        kendaraanList =
            data.map<Map<String, dynamic>>((e) {
              String tipe = (e['tipe_kendaraan'] ?? '').toLowerCase();
              String img =
                  tipe == 'motor'
                      ? 'assets/images/mutor.png'
                      : tipe == 'bus'
                      ? 'assets/images/bus.jpeg'
                      : 'assets/images/mobil.png';

              return {
                'id': e['id'],
                'nama': e['tipe_kendaraan'] ?? '-',
                'merk': e['merk_kendaraan'] ?? '-',
                'plat': e['plat_nomor'] ?? '-',
                'warna': e['warna'] ?? '-',
                'gambar': img,
                'is_aktif': e['is_aktif'],
              };
            }).toList();
      });
    }
  }

  Future<void> gunakanKendaraan(int index) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token == null) return;

    final id = kendaraanList[index]['id'];
    await http.put(
      Uri.parse('http://192.168.18.13:8000/api/kendaraan-aktifkan/$id'),
      headers: {'Authorization': 'Bearer $token'},
    );
    fetchKendaraan();
  }

  Future<void> hapusKendaraan(int index) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');
    if (token == null) return;

    final id = kendaraanList[index]['id'];
    await http.delete(
      Uri.parse('http://192.168.18.13:8000/api/kendaraan-destroy/$id'),
      headers: {'Authorization': 'Bearer $token'},
    );
    setState(() => kendaraanList.removeAt(index));
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
              _buildHeader(),
              Expanded(
                child: Container(
                  decoration: const BoxDecoration(
                    color: Color(0xFFF2F4F7),
                    borderRadius: BorderRadius.vertical(
                      top: Radius.circular(28),
                    ),
                  ),
                  child:
                      kendaraanList.isEmpty
                          ? const Center(child: Text("Belum ada kendaraan"))
                          : ListView.builder(
                            padding: const EdgeInsets.all(20),
                            itemCount: kendaraanList.length,
                            itemBuilder: (_, i) => _buildVehicleCard(i),
                          ),
                ),
              ),
            ],
          ),
        ),
      ),
      bottomNavigationBar: CustomBottomNav(selectedIndex: _selectedIndex),
      floatingActionButton: FloatingActionButton(
        backgroundColor: darkPrimary,
        onPressed:
            () => Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => TambahKendaraanPage()),
            ),
        child: const Icon(Icons.add, color: Colors.white),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
    );
  }

  Widget _buildHeader() {
    return Padding(
      padding: const EdgeInsets.all(20),
      child: Row(
        children: const [
          Icon(Icons.directions_car, color: Colors.white),
          SizedBox(width: 10),
          Text(
            "Kelola Kendaraan",
            style: TextStyle(
              color: Colors.white,
              fontSize: 20,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildVehicleCard(int index) {
    final m = kendaraanList[index];
    final isAktif = m['is_aktif'] == 'on';

    return GestureDetector(
      onLongPress: () => _showActionSheet(index),
      child: Container(
        margin: const EdgeInsets.only(bottom: 18),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(20),
          color: Colors.white,
          boxShadow: [
            BoxShadow(
              color: Colors.black12.withOpacity(0.08),
              blurRadius: 12,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.horizontal(
                left: Radius.circular(20),
              ),
              child: Image.asset(
                m['gambar'],
                width: 130,
                height: 120,
                fit: BoxFit.cover,
              ),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (isAktif)
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 4,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.green.shade100,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Text(
                          "AKTIF",
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: Colors.green,
                          ),
                        ),
                      ),
                    const SizedBox(height: 6),
                    Text(
                      m['nama'],
                      style: const TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text("Merk: ${m['merk']}"),
                    Text("Plat: ${m['plat']}"),
                    Text("Warna: ${m['warna']}"),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _showActionSheet(int index) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder:
          (_) => Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                ListTile(
                  leading: const Icon(Icons.check_circle, color: Colors.green),
                  title: const Text("Gunakan Kendaraan"),
                  onTap: () {
                    Navigator.pop(context);
                    gunakanKendaraan(index);
                  },
                ),
                ListTile(
                  leading: const Icon(Icons.delete, color: Colors.red),
                  title: const Text("Hapus Kendaraan"),
                  onTap: () {
                    Navigator.pop(context);
                    hapusKendaraan(index);
                  },
                ),
              ],
            ),
          ),
    );
  }
}
