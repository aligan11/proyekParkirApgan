import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:google_mlkit_text_recognition/google_mlkit_text_recognition.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

import 'package:lottie/lottie.dart';
import 'package:parking_app/staffpages/cekStatusKendaraanStaff.dart';
import 'package:parking_app/staffpages/TampilDendaPage.dart';

class ScanPlatOCRPage extends StatefulWidget {
  const ScanPlatOCRPage({super.key});

  @override
  State<ScanPlatOCRPage> createState() => _ScanPlatOCRPageState();
}

class _ScanPlatOCRPageState extends State<ScanPlatOCRPage> {
  bool openCameraLoading = true;
  bool platNotFound = false;
  bool checking = false;

  final TextEditingController platController = TextEditingController();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) => scanFromCamera());
  }

  @override
  void dispose() {
    platController.dispose();
    super.dispose();
  }

  // =======================
  // UTIL PLAT
  // =======================

  String normalizePlate(String plate) {
    plate = plate.replaceAll(RegExp(r'[^A-Z0-9]'), '').trim();

    final prefixMatch = RegExp(r'^[A-Z]{1,2}').firstMatch(plate);
    if (prefixMatch == null) return plate;

    final prefix = prefixMatch.group(0)!;
    String rest = plate.substring(prefix.length);

    final numMatch = RegExp(r'^[0-9]{3,4}').firstMatch(rest);
    if (numMatch == null) return plate;

    final number = numMatch.group(0)!;
    String suffix = rest.substring(number.length);

    return "$prefix $number $suffix".trim();
  }

  String fixPlateSmart(String plate) {
    final parts = plate.split(" ");
    if (parts.length < 3) return plate;

    String prefix = parts[0];
    String number = parts[1];
    String suffix = parts[2];

    final Map<String, String> map = {
      '0': 'O',
      'Q': 'O',
      'D': 'O',
      '8': 'B',
      '1': 'I',
      '5': 'S',
      '6': 'G',
      '7': 'Z',
    };

    String corrected = "";
    for (var c in suffix.split("")) {
      corrected += map[c] ?? c;
    }

    if (prefix == "BN") {
      corrected = corrected.replaceAll("P8", "PO");
      corrected = corrected.replaceAll("PB", "PO");
    }

    return "$prefix $number $corrected";
  }

  // =======================
  // CAM SCAN
  // =======================

  Future<void> scanFromCamera() async {
    setState(() {
      openCameraLoading = true;
      platNotFound = false;
    });

    final picker = ImagePicker();
    final picked = await picker.pickImage(source: ImageSource.camera);

    setState(() => openCameraLoading = false);

    if (picked == null) return Navigator.pop(context);

    final input = InputImage.fromFilePath(picked.path);
    final textRecognizer = TextRecognizer(script: TextRecognitionScript.latin);

    final result = await textRecognizer.processImage(input);
    await textRecognizer.close();

    String raw = result.text.toUpperCase();
    List<String> lines = raw.split("\n");

    final regex = RegExp(r'^[A-Z]{1,2}\s?[0-9]{3,4}\s?[A-Z0-9]{1,3}$');

    String? found;

    for (var line in lines) {
      String cleaned =
          line
              .replaceAll(RegExp(r'[^A-Z0-9 ]'), '')
              .replaceAll(RegExp(r'\s+'), ' ')
              .trim();

      if (regex.hasMatch(cleaned)) {
        found = cleaned;
        break;
      }
    }

    String finalPlate = "";

    if (found != null) {
      finalPlate = normalizePlate(found);
      finalPlate = fixPlateSmart(finalPlate);
    }

    platController.text = finalPlate;

    checkPlatNomor(finalPlate);
  }

  // =======================
  // API CHECK
  // =======================

  Future<void> checkPlatNomor(String plat) async {
    if (plat.isEmpty) {
      setState(() => platNotFound = true);
      return;
    }

    setState(() => checking = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString("token");

      final url =
          "http://192.168.18.13:8000/api/data-parkir/plat/${plat.toUpperCase()}";

      final resp = await http.get(
        Uri.parse(url),
        headers: {
          "Accept": "application/json",
          if (token != null) "Authorization": "Bearer $token",
        },
      );

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);

        if (data["status"] == "denda") {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(
              builder: (_) => TampilDendaPage(platNomor: plat.toUpperCase()),
            ),
          );
          return;
        }

        final list = data["data"]["data"] as List;

        if (list.isNotEmpty) {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(
              builder:
                  (_) => Cekstatuskendaraanstaff(platNomor: plat.toUpperCase()),
            ),
          );
          return;
        }
      }

      setState(() => platNotFound = true);
    } catch (e) {
      setState(() => platNotFound = true);
    } finally {
      setState(() => checking = false);
    }
  }

  // =======================
  // UI BUILD
  // =======================

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar:
          openCameraLoading
              ? null
              : AppBar(
                title: const Text("Scan Plat Nomor"),
                backgroundColor: Colors.black87,
                foregroundColor: Colors.white,
                elevation: 2,
              ),
      body:
          openCameraLoading
              ? _uiScanning()
              : platNotFound
              ? _uiPlatNotFound()
              : _uiProcessing(),
    );
  }

  // =======================
  // API CEK ULANG (INPUT MANUAL)
  // =======================
  Future<void> checkPlatUlang() async {
    final plat = platController.text.trim().toUpperCase();

    if (plat.isEmpty) {
      setState(() => platNotFound = true);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: const Text(
            "Nomor plat tidak boleh kosong.",
            style: TextStyle(color: Colors.white),
          ),
          backgroundColor: Colors.redAccent,
          duration: Duration(seconds: 3),
        ),
      );
      return;
    }

    setState(() => checking = true);

    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString("token");

      final url = "http://192.168.18.13:8000/api/data-parkir/plat/$plat";

      final resp = await http.get(
        Uri.parse(url),
        headers: {
          "Accept": "application/json",
          if (token != null) "Authorization": "Bearer $token",
        },
      );

      if (resp.statusCode == 200) {
        final data = jsonDecode(resp.body);

        if (data["status"] == "denda") {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(builder: (_) => TampilDendaPage(platNomor: plat)),
          );
          return;
        }

        if (data["status"] == "success") {
          Navigator.pushReplacement(
            context,
            MaterialPageRoute(
              builder:
                  (_) => Cekstatuskendaraanstaff(platNomor: plat.toUpperCase()),
            ),
          );
          return;
        }
      }

      setState(() => platNotFound = true);
    } catch (e) {
      setState(() => platNotFound = true);
    } finally {
      setState(() => checking = false);
    }
  }

  // -----------------------
  // 1. UI — LOADING SCAN
  // -----------------------

  Widget _uiScanning() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Lottie.asset('assets/lottie/scan.json', width: 180),
          const SizedBox(height: 24),
          const Text(
            "Membuka Kamera...",
            style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 12),
          const CircularProgressIndicator(),
        ],
      ),
    );
  }

  // -----------------------
  // 2. UI — PLAT NOT FOUND
  // -----------------------

  // -----------------------
  // UI — PLAT NOT FOUND
  // -----------------------

  Widget _uiPlatNotFound() {
    return LayoutBuilder(
      builder: (context, constraints) {
        return SingleChildScrollView(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: ConstrainedBox(
            constraints: BoxConstraints(minHeight: constraints.maxHeight),
            child: IntrinsicHeight(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Lottie.asset(
                    'assets/lottie/not_found.json',
                    width: 180,
                    repeat: false,
                  ),
                  const SizedBox(height: 24),

                  const Text(
                    "Plat Tidak Ditemukan",
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.redAccent,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 12),

                  const Text(
                    "Silakan periksa atau edit nomor plat Anda:",
                    style: TextStyle(fontSize: 16, color: Colors.black87),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 20),

                  TextField(
                    controller: platController,
                    textCapitalization: TextCapitalization.characters,
                    decoration: InputDecoration(
                      labelText: "Nomor Plat",
                      prefixIcon: const Icon(Icons.directions_car),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),

                  const SizedBox(height: 28),

                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed:
                          checking
                              ? null
                              : checkPlatUlang, // <-- pakai fungsi cek ulang
                      icon:
                          checking
                              ? const SizedBox(
                                width: 20,
                                height: 20,
                                child: CircularProgressIndicator(
                                  color: Colors.white,
                                  strokeWidth: 2,
                                ),
                              )
                              : const Icon(Icons.search),
                      label:
                          checking
                              ? const Text("Memeriksa...")
                              : const Text("Cek Ulang"),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.black87,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(
                          horizontal: 24,
                          vertical: 16,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 16),

                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: () {
                        setState(() {
                          platNotFound = false;
                          openCameraLoading = true;
                        });
                        scanFromCamera();
                      },
                      icon: const Icon(Icons.camera_alt),
                      label: const Text("Scan Ulang"),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.redAccent,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(
                          horizontal: 24,
                          vertical: 14,
                        ),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),

                  const Spacer(),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  // -----------------------
  // 3. UI — PROCESSING
  // -----------------------

  Widget _uiProcessing() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Lottie.asset('assets/lottie/Loading.json', width: 150),
          const SizedBox(height: 16),
          const Text(
            "Memproses plat nomor...",
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }
}
