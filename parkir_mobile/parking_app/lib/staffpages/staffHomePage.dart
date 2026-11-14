import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:parking_app/pages/LoginPage.dart';
import 'package:parking_app/staffpages/InputDendaManualPage.dart';
import 'package:parking_app/staffpages/scanPlatPage.dart';
import 'package:parking_app/staffpages/staffProfileMainPage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class StaffHomePage extends StatefulWidget {
  const StaffHomePage({super.key});

  @override
  State<StaffHomePage> createState() => _StaffHomePageState();
}

class _StaffHomePageState extends State<StaffHomePage> {
  String namaLengkap = 'Staff';
  String foto_users = '';
  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();
    _loadShared();
  }

  Future<void> _loadShared() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      namaLengkap = prefs.getString('nama_lengkap') ?? 'Staff';
      final baseUrl = 'http://192.168.18.13:8000/storage/';
      final fotoPath = prefs.getString('foto_users') ?? '';
      foto_users = '$baseUrl$fotoPath';
    });
  }

  String getGreeting() {
    final hour = DateTime.now().hour;
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
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
              // HEADER
              Padding(
                padding: const EdgeInsets.symmetric(
                  horizontal: 20,
                  vertical: 16,
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          getGreeting(),
                          style: const TextStyle(
                            color: Colors.white70,
                            fontSize: 14,
                          ),
                        ),
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
                    PopupMenuButton<String>(
                      onSelected: (value) async {
                        if (value == 'profile') {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => Staffprofilemainpage(),
                            ),
                          );
                        } else if (value == 'logout') {
                          final prefs = await SharedPreferences.getInstance();
                          await prefs.clear();
                          if (mounted) {
                            Navigator.pushAndRemoveUntil(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const LoginPage(),
                              ),
                              (route) => false,
                            );
                          }
                        }
                      },
                      itemBuilder:
                          (context) => const [
                            PopupMenuItem(
                              value: 'profile',
                              child: Text('Profil'),
                            ),
                            PopupMenuItem(
                              value: 'logout',
                              child: Text('Logout'),
                            ),
                          ],
                      child: CircleAvatar(
                        radius: 22,
                        backgroundImage: NetworkImage(
                          foto_users.isNotEmpty
                              ? foto_users
                              : 'https://ui-avatars.com/api/?name=Staff&background=fff&color=FF9800',
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              // BODY
              Expanded(
                child: Container(
                  decoration: const BoxDecoration(
                    color: Color(0xFFF2F4F7),
                    borderRadius: BorderRadius.vertical(
                      top: Radius.circular(28),
                    ),
                  ),
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(24),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // HERO CARD
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(20),
                            gradient: LinearGradient(
                              colors: [
                                Colors.orange.shade700,
                                Colors.orange.shade400,
                              ],
                            ),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(0.15),
                                blurRadius: 10,
                                offset: const Offset(0, 6),
                              ),
                            ],
                          ),
                          child: Row(
                            children: const [
                              FaIcon(
                                FontAwesomeIcons.parking,
                                color: Colors.white,
                                size: 32,
                              ),
                              SizedBox(width: 16),
                              Expanded(
                                child: Text(
                                  "Sistem Parkir Digital\nMonitoring & Penindakan",
                                  style: TextStyle(
                                    color: Colors.white,
                                    fontSize: 16,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),

                        const SizedBox(height: 28),

                        // ACTION BUTTONS
                        _ActionCard(
                          title: "Scan Plat Kendaraan",
                          subtitle:
                              "Deteksi kendaraan secara otomatis melalui kamera",
                          icon: FontAwesomeIcons.camera,
                          color: Colors.orange,
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const ScanPlatOCRPage(),
                              ),
                            );
                          },
                        ),
                        const SizedBox(height: 16),
                        _ActionCard(
                          title: "Input Denda Manual",
                          subtitle:
                              "Catat pelanggaran parkir non-user atau manual",
                          icon: FontAwesomeIcons.gavel,
                          color: Colors.redAccent,
                          onTap: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const InputDendaManualPage(),
                              ),
                            );
                          },
                        ),
                      ],
                    ),
                  ),
                ),
              ),

              // BOTTOM NAV
              BottomNavigationBar(
                currentIndex: _selectedIndex,
                selectedItemColor: Colors.orange,
                onTap: (index) {
                  if (index == 1) {
                    Navigator.push(
                      context,
                      MaterialPageRoute(
                        builder: (_) => const InputDendaManualPage(),
                      ),
                    );
                  } else if (index == 2) {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => Staffprofilemainpage()),
                    );
                  }
                },
                items: const [
                  BottomNavigationBarItem(
                    icon: Icon(Icons.home_rounded),
                    label: 'Beranda',
                  ),
                  BottomNavigationBarItem(
                    icon: Icon(Icons.gavel_rounded),
                    label: 'Denda',
                  ),
                  BottomNavigationBarItem(
                    icon: Icon(Icons.person_rounded),
                    label: 'Profil',
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

// =======================
// ACTION CARD COMPONENT
// =======================
class _ActionCard extends StatelessWidget {
  final String title;
  final String subtitle;
  final IconData icon;
  final Color color;
  final VoidCallback onTap;

  const _ActionCard({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.08),
              blurRadius: 10,
              offset: const Offset(0, 6),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: color.withOpacity(0.15),
                shape: BoxShape.circle,
              ),
              child: FaIcon(icon, color: color),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    subtitle,
                    style: const TextStyle(fontSize: 13, color: Colors.black54),
                  ),
                ],
              ),
            ),
            const Icon(Icons.chevron_right_rounded),
          ],
        ),
      ),
    );
  }
}
