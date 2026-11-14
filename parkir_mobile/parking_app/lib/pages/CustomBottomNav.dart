import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:parking_app/pages/HistoryPage.dart';
import 'package:parking_app/pages/HomePage.dart';
import 'package:parking_app/pages/KelolaMobilPage.dart';
import 'package:parking_app/pages/ProfileMainPage.dart';
import 'package:parking_app/pages/ScanBarcodePage.dart';

class CustomBottomNav extends StatelessWidget {
  final int selectedIndex;

  const CustomBottomNav({Key? key, this.selectedIndex = 0}) : super(key: key);

  void _handleTap(BuildContext context, int index) {
    switch (index) {
      case 0:
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => HomePage()),
        );
        break;
      case 1:
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => HistoryPage()),
        );
        break;
      case 2:
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => KelolaMobilPage()),
        );
        break;
      case 3:
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => ProfileMainPage()),
        );
        break;
    }
  }

  @override
  Widget build(BuildContext context) {
    return BottomAppBar(
      color: const Color(0xFF0B1120),
      shape: const CircularNotchedRectangle(),
      notchMargin: 8,
      child: SizedBox(
        height: 64,
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceAround,
          children: [
            _NavItem(
              icon: FontAwesomeIcons.house,
              label: "Home",
              isSelected: selectedIndex == 0,
              onTap: () => _handleTap(context, 0),
            ),
            _NavItem(
              icon: FontAwesomeIcons.clockRotateLeft,
              label: "History",
              isSelected: selectedIndex == 1,
              onTap: () => _handleTap(context, 1),
            ),
            const SizedBox(width: 48),
            _NavItem(
              icon: FontAwesomeIcons.car,
              label: "Kelola",
              isSelected: selectedIndex == 2,
              onTap: () => _handleTap(context, 2),
            ),
            _NavItem(
              icon: FontAwesomeIcons.user,
              label: "Profil",
              isSelected: selectedIndex == 3,
              onTap: () => _handleTap(context, 3),
            ),
          ],
        ),
      ),
    );
  }
}

class _NavItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final bool isSelected;
  final VoidCallback onTap;

  const _NavItem({
    Key? key,
    required this.icon,
    required this.label,
    this.isSelected = false,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          FaIcon(
            icon,
            color: isSelected ? Colors.amber : Colors.white,
            size: 20,
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: TextStyle(
              color: isSelected ? Colors.amber : Colors.white,
              fontSize: 11,
            ),
          ),
        ],
      ),
    );
  }
}

// Floating Action Button yang sudah dibungkus ke class reusable
class CustomFloatingActionButton extends StatelessWidget {
  const CustomFloatingActionButton({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return FloatingActionButton(
      onPressed: () {
        Navigator.push(
          context,
          MaterialPageRoute(builder: (context) => const ScanBarcodePage()),
        );
      },
      backgroundColor: const Color.fromARGB(255, 0, 0, 0),
      shape: const CircleBorder(),
      child: const FaIcon(FontAwesomeIcons.qrcode, color: Colors.white),
    );
  }
}
