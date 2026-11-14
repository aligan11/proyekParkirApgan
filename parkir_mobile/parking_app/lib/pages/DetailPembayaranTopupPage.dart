import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

class DetailPembayaranTopupPage extends StatelessWidget {
  final Map<String, dynamic> data;

  const DetailPembayaranTopupPage({super.key, required this.data});

  @override
  Widget build(BuildContext context) {
    final nominal = data['nominal'].toString();
    final biayaAdmin = data['biaya_admin'].toString();
    final totalBayar = data['total_dibayar'].toString();
    final metode = data['metode_pembayaran'] ?? '-';
    final imageUrl = data['midtrans_qr_image_url'] ?? '';

    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: false,
        title: Text(
          'Isi Ulang',
          style: TextStyle(
            color: Colors.grey[800],
            fontSize: 14,
            fontWeight: FontWeight.w600,
          ),
        ),
        backgroundColor: Colors.white,
        centerTitle: true,
        elevation: 2,
        shadowColor: Colors.grey[300],
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.black),
          onPressed: () {
            Navigator.pop(context);
          },
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // 🔔 Reminder
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.redAccent.withOpacity(0.5)),
              ),
              child: Row(
                children: const [
                  Icon(Icons.timer_outlined, color: Colors.redAccent),
                  SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      "Segera selesaikan pembayaran Anda! Pesanan akan dibatalkan otomatis dalam 5 menit jika belum dibayar.",
                      style: TextStyle(fontSize: 13, color: Colors.redAccent),
                    ),
                  ),
                ],
              ),
            ),

            const SizedBox(height: 20),
            const Icon(Icons.qr_code_2, size: 50, color: Colors.blueAccent),
            const SizedBox(height: 10),
            const Text(
              "Scan QR Code untuk Bayar",
              style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 20,
                color: Colors.black87,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 6),
            const Text(
              "Gunakan aplikasi e-wallet Anda untuk menyelesaikan pembayaran",
              style: TextStyle(color: Colors.grey),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 20),

            Image.network(
              imageUrl,
              width: 200,
              height: 200,
              fit: BoxFit.contain,
              errorBuilder:
                  (context, error, stackTrace) =>
                      const Text("Gagal memuat QR Code"),
            ),

            const SizedBox(height: 10),
            ElevatedButton.icon(
              onPressed: () async {
                await Clipboard.setData(ClipboardData(text: imageUrl));
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('QR Code URL berhasil disalin!'),
                  ),
                );
              },
              icon: const Icon(Icons.copy),
              label: const Text("Salin QR Code URL"),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.blueAccent,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),

            const SizedBox(height: 20),
            _buildInfoRow("Nominal", "Rp$nominal"),
            _buildInfoRow("Biaya Admin", "Rp$biayaAdmin"),
            const Divider(height: 24),
            _buildInfoRow("Total Bayar", "Rp$totalBayar", isTotal: true),
            const SizedBox(height: 20),
            _buildMetodePembayaran(metode),

            const SizedBox(height: 16),
            const Text(
              "Setelah melakukan pembayaran, halaman ini akan otomatis menutup jika berhasil.",
              style: TextStyle(fontSize: 13, color: Colors.grey),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isTotal = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              color: isTotal ? Colors.black : Colors.grey,
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              color: isTotal ? Colors.black : Colors.black87,
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMetodePembayaran(String metode) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: Colors.orange.shade100,
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          const Icon(Icons.wallet, color: Colors.orange),
          const SizedBox(width: 8),
          Text(
            metode.toUpperCase(),
            style: const TextStyle(
              fontWeight: FontWeight.w600,
              color: Colors.orange,
            ),
          ),
        ],
      ),
    );
  }
}
