import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:parking_app/pages/IsiUlangPage.dart';

class PaymentMethodPage extends StatelessWidget {
  const PaymentMethodPage({super.key});

  Widget buildOption({
    String? imageUrl,
    String? imageAsset,
    required String title,
    String? subtitle,
    void Function()? onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey.shade300),
          borderRadius: BorderRadius.circular(16),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Row(
              children: [
                if (imageAsset != null)
                  Image.asset(imageAsset, width: 24, height: 24)
                else if (imageUrl != null)
                  Image.network(imageUrl, width: 24, height: 24),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: const TextStyle(fontWeight: FontWeight.w600),
                    ),
                    if (subtitle != null)
                      Text(
                        subtitle,
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey.shade500,
                        ),
                      ),
                  ],
                ),
              ],
            ),
            const Icon(Icons.chevron_right, color: Colors.grey),
          ],
        ),
      ),
    );
  }

  Widget buildSection(String title, List<Widget> children) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: 24),
        Text(title, style: const TextStyle(fontSize: 12, color: Colors.grey)),
        const SizedBox(height: 8),
        Column(
          children:
              children
                  .map(
                    (e) => Padding(
                      padding: const EdgeInsets.only(bottom: 12),
                      child: e,
                    ),
                  )
                  .toList(),
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 32),
          children: [
            // Header
            Row(
              children: [
                IconButton(
                  onPressed: () => Navigator.pop(context),
                  icon: const FaIcon(FontAwesomeIcons.arrowLeft),
                ),
                const SizedBox(width: 8),
                const Text(
                  'Pilih Metode Pembayaran',
                  style: TextStyle(fontWeight: FontWeight.w600, fontSize: 16),
                ),
              ],
            ),

            // E-Wallet Section
            buildSection('Qris', [
              buildOption(
                imageUrl:
                    'https://cdn.rri.co.id/berita/Lhokseumawe/o/1746967360474-InShot_20250511_192805646/bw4yol2ts5teq5t.jpeg',
                title: 'Qris',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => IsiUlangPage(title: 'qris'),
                    ),
                  );
                },
              ),
            ]),

            buildSection('E-Wallet', [
              buildOption(
                imageUrl:
                    'https://i.pinimg.com/736x/d0/19/16/d019163d861908ed0046391ebfa42ce1.jpg',
                title: 'ShopeePay / SPayLater',
                subtitle: 'Diskon s/d 12RB dan Cicilan 0%',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => IsiUlangPage(title: 'ShopeePay'),
                    ),
                  );
                },
              ),
              buildOption(
                imageAsset: 'assets/images/gopay.png',
                title: 'GoPay',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => IsiUlangPage(title: 'GoPay'),
                    ),
                  );
                },
              ),
              buildOption(
                imageAsset: 'assets/images/ovo.png',
                title: 'OVO',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => IsiUlangPage(title: 'OVO'),
                    ),
                  );
                },
              ),
              buildOption(
                imageUrl:
                    'https://storage.googleapis.com/a1aa/image/1d21a0bc-0855-4873-585a-2c8b634b7adb.jpg',
                title: 'DANA',
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (context) => IsiUlangPage(title: 'DANA'),
                    ),
                  );
                },
              ),
            ]),

            // Virtual Account Section
            buildSection('Akun Virtual', [
              buildOption(
                imageUrl:
                    'https://storage.googleapis.com/a1aa/image/c8d9615b-6af5-47eb-5f0b-587a007c76e3.jpg',
                title: 'BCA Virtual Account',
              ),
              buildOption(
                imageUrl:
                    'https://storage.googleapis.com/a1aa/image/1c81c6d2-0b8b-44f0-5aaa-9710e11e3bf1.jpg',
                title: 'BRI Virtual Account',
              ),
              buildOption(
                imageUrl:
                    'https://storage.googleapis.com/a1aa/image/e0b80445-6cf9-4052-238a-aed0cd7e2b88.jpg',
                title: 'BNI Virtual Account',
              ),
              buildOption(
                imageUrl:
                    'https://storage.googleapis.com/a1aa/image/4ea6903d-7e86-4654-fcd5-efac4eebea01.jpg',
                title: 'MANDIRI Virtual Account',
              ),
            ]),

            // Credit/Debit Section
            buildSection('Kartu Kredit/Debit', [
              buildOption(
                imageUrl:
                    'https://storage.googleapis.com/a1aa/image/fb773e9b-dded-41cf-d7ea-ebf45663f996.jpg',
                title: 'Credit/Debit Card',
              ),
            ]),
          ],
        ),
      ),
    );
  }
}
