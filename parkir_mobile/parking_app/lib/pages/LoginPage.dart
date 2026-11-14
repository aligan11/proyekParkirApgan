import 'package:flutter/material.dart';
import 'package:parking_app/pages/CreateAccountPage.dart';
import 'package:parking_app/pages/HomePage.dart';
import 'package:parking_app/staffPages/StaffHomePage.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:lottie/lottie.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  _LoginPageState createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  bool isLoading = false;
  final TextEditingController userController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  bool passwordVisible = false;

  String? loginError; // 🔴 error message

  @override
  void initState() {
    super.initState();
    _checkLoginStatus();
  }

  Future<void> _checkLoginStatus() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    String? token = prefs.getString('token');
    String? role = prefs.getString('role');

    if (token != null && token.isNotEmpty && role != null && role.isNotEmpty) {
      if (!mounted) return;

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder:
              (context) =>
                  role == 'staff' ? const StaffHomePage() : const HomePage(),
        ),
      );
    }
  }

  void _handleLogin() async {
    final username = userController.text.trim();
    final password = passwordController.text.trim();

    setState(() {
      loginError = null;
    });

    if (username.isEmpty || password.isEmpty) {
      setState(() {
        loginError = 'Username dan password wajib diisi';
      });
      return;
    }

    setState(() => isLoading = true);

    try {
      final response = await http.post(
        Uri.parse('http://192.168.18.13:8000/api/login'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'username': username, 'password': password}),
      );

      final jsonResponse = jsonDecode(response.body);

      if ((response.statusCode == 200 || response.statusCode == 201) &&
          jsonResponse['status'] == 'success') {
        final token = jsonResponse['data']['token'];
        final user = jsonResponse['data']['user'];
        final role = user['role'] ?? '';

        SharedPreferences prefs = await SharedPreferences.getInstance();
        await prefs.setString('token', token);
        await prefs.setString('nama_lengkap', user['nama_lengkap'] ?? '');
        await prefs.setString('username', user['username'] ?? '');
        await prefs.setString('foto_users', user['foto_users'] ?? '');
        await prefs.setInt('id_users', user['id_users']);
        await prefs.setInt(
          'saldo',
          int.tryParse(user['saldo']?.toString() ?? '0') ?? 0,
        );
        await prefs.setString('role', role);

        if (!context.mounted) return;

        showDialog(
          context: context,
          barrierDismissible: false,
          builder: (_) {
            Future.delayed(const Duration(seconds: 2), () {
              Navigator.pop(context);
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(
                  builder:
                      (context) =>
                          role == 'staff'
                              ? const StaffHomePage()
                              : const HomePage(),
                ),
              );
            });

            return AlertDialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
              content: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  SizedBox(
                    width: 100,
                    height: 100,
                    child: Lottie.asset(
                      'assets/lottie/success_check.json',
                      repeat: false,
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'Login berhasil!',
                    style: TextStyle(fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            );
          },
        );
      } else {
        setState(() {
          loginError = 'Username atau password Anda salah';
        });
      }
    } catch (e) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Terjadi kesalahan server')));
    }

    setState(() => isLoading = false);
  }

  @override
  Widget build(BuildContext context) {
    final yellowColor = Colors.amber[700];
    final errorColor = Colors.redAccent;

    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Welcome back',
                  style: TextStyle(fontSize: 28, fontWeight: FontWeight.w700),
                ),
                const SizedBox(height: 6),
                Text(
                  'Masukkan akun Anda untuk mengakses sistem parkir',
                  style: TextStyle(color: Colors.grey[600]),
                ),
                const SizedBox(height: 24),
                Center(
                  child: Image.asset('assets/images/parking3.png', height: 160),
                ),
                const SizedBox(height: 28),

                // Username
                TextField(
                  controller: userController,
                  onChanged: (_) => setState(() => loginError = null),
                  decoration: InputDecoration(
                    hintText: 'Email atau username',
                    prefixIcon: const Icon(Icons.person_outline),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: BorderSide(
                        color: loginError != null ? errorColor : yellowColor!,
                        width: 2,
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 16),

                // Password
                TextField(
                  controller: passwordController,
                  obscureText: !passwordVisible,
                  onChanged: (_) => setState(() => loginError = null),
                  decoration: InputDecoration(
                    hintText: 'Password',
                    prefixIcon: const Icon(Icons.lock_outline),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: BorderSide(
                        color: loginError != null ? errorColor : yellowColor!,
                        width: 2,
                      ),
                    ),
                    suffixIcon: IconButton(
                      icon: Icon(
                        passwordVisible
                            ? Icons.visibility
                            : Icons.visibility_off,
                      ),
                      onPressed: () {
                        setState(() {
                          passwordVisible = !passwordVisible;
                        });
                      },
                    ),
                  ),
                ),

                // 🔴 Error message
                if (loginError != null) ...[
                  const SizedBox(height: 10),
                  Text(
                    loginError!,
                    style: const TextStyle(
                      color: Colors.redAccent,
                      fontSize: 13,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],

                const SizedBox(height: 24),

                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: isLoading ? null : _handleLogin,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.black87,
                      padding: const EdgeInsets.symmetric(vertical: 18),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(30),
                      ),
                    ),
                    child:
                        isLoading
                            ? const CircularProgressIndicator(
                              color: Colors.white,
                            )
                            : const Text(
                              'Login',
                              style: TextStyle(color: Colors.white),
                            ),
                  ),
                ),

                const SizedBox(height: 28),
                Center(
                  child: GestureDetector(
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(builder: (_) => CreateAccountPage()),
                      );
                    },
                    child: Text(
                      'Belum punya akun? Daftar',
                      style: TextStyle(
                        color: yellowColor,
                        fontWeight: FontWeight.bold,
                        decoration: TextDecoration.underline,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
