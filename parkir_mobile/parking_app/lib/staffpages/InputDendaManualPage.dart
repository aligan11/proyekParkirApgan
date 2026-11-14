import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:parking_app/staffpages/staffHomePage.dart';
import 'package:parking_app/staffpages/staffProfileMainPage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class InputDendaManualPage extends StatefulWidget {
  const InputDendaManualPage({super.key});

  @override
  State<InputDendaManualPage> createState() => _InputDendaManualPageState();
}

class _InputDendaManualPageState extends State<InputDendaManualPage> {
  final TextEditingController platController = TextEditingController();
  final TextEditingController namaController = TextEditingController();
  final TextEditingController alasanController = TextEditingController();

  bool submitting = false;
  int _selectedIndex = 1;

  final String jenisPelanggaran =
      "Tidak melakukan pemindaian barcode sesuai prosedur parkir atau tidak terdaftar";

  @override
  void dispose() {
    platController.dispose();
    namaController.dispose();
    alasanController.dispose();
    super.dispose();
  }

  Future<void> submitDenda() async {
    final plat = platController.text.trim().toUpperCase();
    final nama = namaController.text.trim();
    final alasan = alasanController.text.trim();

    if (plat.isEmpty || alasan.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Plat nomor dan alasan wajib diisi."),
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    setState(() => submitting = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString("token");

      final response = await http.post(
        Uri.parse("http://192.168.18.13:8000/api/kendaraan/dendaNonUser"),
        headers: {
          "Accept": "application/json",
          "Content-Type": "application/json",
          if (token != null) "Authorization": "Bearer $token",
        },
        body: jsonEncode({
          "plat_nomor": plat,
          "nama_user": nama,
          "alasan": alasan,
        }),
      );

      final result = jsonDecode(response.body);

      if (response.statusCode == 201 && result['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text("Denda non-user berhasil disimpan."),
            backgroundColor: Colors.green,
          ),
        );
        Navigator.pushReplacement(
          context,
          PageRouteBuilder(
            transitionDuration: const Duration(milliseconds: 350),
            pageBuilder: (_, __, ___) => InputDendaManualPage(),
            transitionsBuilder: (_, animation, __, child) {
              return FadeTransition(
                opacity: CurvedAnimation(
                  parent: animation,
                  curve: Curves.easeInOut,
                ),
                child: child,
              );
            },
          ),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result['message'] ?? "Gagal menyimpan denda."),
            backgroundColor: Colors.redAccent,
          ),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text("Terjadi kesalahan: $e"),
          backgroundColor: Colors.redAccent,
        ),
      );
    } finally {
      setState(() => submitting = false);
    }
  }

  Widget _buildLabel(String text) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Text(
        text,
        style: const TextStyle(
          fontWeight: FontWeight.w600,
          fontSize: 13,
          color: Colors.black87,
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFFFF9800), Color(0xFFFFC107)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: Column(
            children: [
              const SizedBox(height: 16),
              const Text(
                "Input Denda Non-User",
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                ),
              ),
              const SizedBox(height: 16),

              Expanded(
                child: Container(
                  padding: const EdgeInsets.all(20),
                  decoration: const BoxDecoration(
                    color: Color(0xFFF2F4F7),
                    borderRadius: BorderRadius.vertical(
                      top: Radius.circular(28),
                    ),
                  ),
                  child: SingleChildScrollView(
                    child: Card(
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(20),
                      ),
                      elevation: 6,
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildLabel("Plat Nomor"),
                            TextField(
                              controller: platController,
                              textCapitalization: TextCapitalization.characters,
                              decoration: InputDecoration(
                                hintText: "Contoh: TSS123",
                                prefixIcon: const Icon(Icons.directions_car),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(16),
                                ),
                              ),
                            ),
                            const SizedBox(height: 16),

                            _buildLabel("Nama (Opsional)"),
                            TextField(
                              controller: namaController,
                              decoration: InputDecoration(
                                hintText: "Nama pemilik kendaraan",
                                prefixIcon: const Icon(Icons.person_outline),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(16),
                                ),
                              ),
                            ),
                            const SizedBox(height: 20),

                            // 🔥 JENIS PELANGGARAN (DISPLAY ONLY)
                            _buildLabel("Jenis Pelanggaran"),
                            Container(
                              width: double.infinity,
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                color: Colors.orange.shade50,
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(
                                  color: Colors.orange.shade300,
                                ),
                              ),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Icon(
                                    Icons.warning_amber_rounded,
                                    color: Colors.orange,
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Text(
                                      jenisPelanggaran,
                                      style: const TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.w600,
                                        color: Colors.black87,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),

                            const SizedBox(height: 20),

                            _buildLabel("Keterangan / Alasan"),
                            TextField(
                              controller: alasanController,
                              maxLines: 3,
                              decoration: InputDecoration(
                                hintText: "Masukkan keterangan tambahan",
                                prefixIcon: const Icon(Icons.note_alt_outlined),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(16),
                                ),
                              ),
                            ),
                            const SizedBox(height: 28),

                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton.icon(
                                onPressed: submitting ? null : submitDenda,
                                icon:
                                    submitting
                                        ? const SizedBox(
                                          width: 20,
                                          height: 20,
                                          child: CircularProgressIndicator(
                                            strokeWidth: 2,
                                            color: Colors.white,
                                          ),
                                        )
                                        : const Icon(
                                          Icons.save,
                                          color: Colors.white,
                                        ),
                                label: Text(
                                  submitting ? "Menyimpan..." : "Simpan Denda",
                                  style: const TextStyle(
                                    fontSize: 16,
                                    color: Colors.white,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.orange.shade700,
                                  padding: const EdgeInsets.symmetric(
                                    vertical: 16,
                                  ),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(18),
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ),
                ),
              ),

              BottomNavigationBar(
                currentIndex: _selectedIndex,
                selectedItemColor: Colors.orange,
                onTap: (index) {
                  if (index == 0) {
                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(builder: (_) => const StaffHomePage()),
                    );
                  } else if (index == 2) {
                    Navigator.pushReplacement(
                      context,
                      MaterialPageRoute(
                        builder: (_) => const Staffprofilemainpage(),
                      ),
                    );
                  }
                },
                items: const [
                  BottomNavigationBarItem(
                    icon: Icon(Icons.home),
                    label: "Beranda",
                  ),
                  BottomNavigationBarItem(
                    icon: Icon(Icons.gavel),
                    label: "Denda",
                  ),
                  BottomNavigationBarItem(
                    icon: Icon(Icons.person),
                    label: "Profil",
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
