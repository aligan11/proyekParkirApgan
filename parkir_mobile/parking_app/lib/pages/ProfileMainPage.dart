import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:parking_app/pages/CustomBottomNav.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:image_picker/image_picker.dart';
import 'dart:io';

class ProfileMainPage extends StatefulWidget {
  const ProfileMainPage({Key? key}) : super(key: key);

  @override
  State<ProfileMainPage> createState() => _ProfileMainPageState();
}

class _ProfileMainPageState extends State<ProfileMainPage> {
  final int _selectedIndex = 3;
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _confirmPasswordController =
      TextEditingController();

  String name = '-';
  String username = '-';
  String imageUrl = 'https://via.placeholder.com/150';
  File? _pickedImage;

  @override
  void initState() {
    super.initState();
    _loadUserProfile();
  }

  Future<void> _pickImage() async {
    final pickedFile = await ImagePicker().pickImage(
      source: ImageSource.gallery,
    );
    if (pickedFile != null) {
      setState(() {
        _pickedImage = File(pickedFile.path);
      });
    }
  }

  Future<void> _loadUserProfile() async {
    SharedPreferences prefs = await SharedPreferences.getInstance();
    setState(() {
      name = prefs.getString('nama_lengkap') ?? '-';
      username = prefs.getString('username') ?? '-';
      imageUrl =
          (prefs.getString('foto_users') ?? '').isNotEmpty
              ? 'http://192.168.18.13:8000/storage/${prefs.getString('foto_users')}'
              : 'https://via.placeholder.com/150';
    });
  }

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        backgroundColor: Colors.white,
        appBar: AppBar(
          automaticallyImplyLeading: false,
          title: Text(
            'Profile',
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
        ),
        body: Column(
          children: [
            const SizedBox(height: 24),
            Stack(
              children: [
                CircleAvatar(
                  radius: 48,
                  backgroundImage:
                      _pickedImage != null
                          ? FileImage(_pickedImage!)
                          : NetworkImage(imageUrl) as ImageProvider,
                ),
                Positioned(
                  bottom: 0,
                  right: 0,
                  child: GestureDetector(
                    onTap: _pickImage,
                    child: Container(
                      width: 24,
                      height: 24,
                      decoration: BoxDecoration(
                        color: Colors.orange,
                        shape: BoxShape.circle,
                        border: Border.all(color: Colors.white, width: 2),
                      ),
                      child: const Center(
                        child: FaIcon(
                          FontAwesomeIcons.pen,
                          color: Colors.white,
                          size: 10,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              name,
              style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 14),
            ),
            Text(
              username,
              style: const TextStyle(fontSize: 12, color: Colors.grey),
            ),
            const SizedBox(height: 16),
            Container(
              margin: const EdgeInsets.symmetric(horizontal: 24),
              decoration: BoxDecoration(
                border: Border.all(color: Colors.grey.shade300),
                borderRadius: BorderRadius.circular(8),
              ),
              child: const TabBar(
                labelColor: Colors.orange,
                unselectedLabelColor: Colors.grey,
                indicatorColor: Colors.orange,
                tabs: [Tab(text: 'Edit Profile'), Tab(text: 'Security')],
              ),
            ),
            const SizedBox(height: 8),
            Expanded(
              child: TabBarView(
                children: [
                  EditProfileTab(pickedImage: _pickedImage),
                  const SecurityTab(),
                ],
              ),
            ),
          ],
        ),
        bottomNavigationBar: CustomBottomNav(selectedIndex: _selectedIndex),
        floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
      ),
    );
  }
}

class EditProfileTab extends StatefulWidget {
  final File? pickedImage;
  const EditProfileTab({Key? key, this.pickedImage}) : super(key: key);

  @override
  State<EditProfileTab> createState() => _EditProfileTabState();
}

class _EditProfileTabState extends State<EditProfileTab> {
  final _namaController = TextEditingController();
  final _usernameController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadUserData();
  }

  Future<void> _loadUserData() async {
    final prefs = await SharedPreferences.getInstance();
    _namaController.text = prefs.getString('nama_lengkap') ?? '';
    _usernameController.text = prefs.getString('username') ?? '';
  }

  Future<void> updateProfile({
    String? password,
    String? confirmPassword,
  }) async {
    final prefs = await SharedPreferences.getInstance();
    final id = prefs.getInt('id_users');
    final token = prefs.getString('token');

    try {
      var request = http.MultipartRequest(
        'POST',
        Uri.parse('http://192.168.18.13:8000/api/update-user/$id'),
      );

      request.fields['_method'] = 'PUT';
      request.fields['nama_lengkap'] = _namaController.text;
      request.fields['username'] = _usernameController.text;

      // Kirim password jika tidak kosong
      if (password != null && password.isNotEmpty) {
        request.fields['password'] = password;
        request.fields['password_confirmation'] = confirmPassword ?? '';
      }

      if (widget.pickedImage != null) {
        request.files.add(
          await http.MultipartFile.fromPath(
            'foto_users',
            widget.pickedImage!.path,
          ),
        );
      }

      request.headers.addAll({
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();
      final decoded = json.decode(responseBody);

      if (response.statusCode == 200 && decoded['status'] == 'success') {
        final data = decoded['data'];

        await prefs.setString('nama_lengkap', data['nama_lengkap']);
        await prefs.setString('username', data['username']);
        await prefs.setString('foto_users', data['foto_users']);

        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(decoded['message'] ?? 'Berhasil update')),
        );

        setState(() {});
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(decoded['message'] ?? 'Gagal update')),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Terjadi kesalahan: $e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        children: [
          const SizedBox(height: 12),
          _buildTextField('Nama Lengkap', _namaController),
          const SizedBox(height: 12),
          _buildTextField('Username', _usernameController),
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.black,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              onPressed: updateProfile,
              child: const Text(
                'Update',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: Colors.white,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField(String label, TextEditingController controller) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 12)),
        const SizedBox(height: 4),
        TextFormField(
          controller: controller,
          decoration: InputDecoration(
            isDense: true,
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 8,
              vertical: 8,
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(6),
              borderSide: const BorderSide(color: Colors.grey),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(6),
              borderSide: const BorderSide(color: Colors.orange),
            ),
          ),
        ),
      ],
    );
  }
}

class SecurityTab extends StatefulWidget {
  const SecurityTab({super.key});

  @override
  State<SecurityTab> createState() => _SecurityTabState();
}

class _SecurityTabState extends State<SecurityTab> {
  final TextEditingController _oldPasswordController = TextEditingController();
  final TextEditingController _newPasswordController = TextEditingController();
  final TextEditingController _confirmPasswordController =
      TextEditingController();

  Future<void> updatePassword() async {
    final prefs = await SharedPreferences.getInstance();
    final id = prefs.getInt('id_users');
    final token = prefs.getString('token');

    try {
      var request = http.MultipartRequest(
        'POST',
        Uri.parse('http://192.168.18.13:8000/api/update-user/$id'),
      );

      request.fields['_method'] = 'PUT';
      request.fields['password'] = _newPasswordController.text;
      request.fields['password_confirmation'] = _confirmPasswordController.text;

      request.headers.addAll({
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      });

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();
      final decoded = json.decode(responseBody);

      if (response.statusCode == 200 && decoded['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(decoded['message'] ?? 'Password berhasil diupdate'),
          ),
        );
        _oldPasswordController.clear();
        _newPasswordController.clear();
        _confirmPasswordController.clear();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(decoded['message'] ?? 'Gagal update password'),
          ),
        );
      }
    } catch (e) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Terjadi kesalahan: $e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        children: [
          _buildTextField('Password Lama', _oldPasswordController),
          const SizedBox(height: 12),
          _buildTextField('Password Baru', _newPasswordController),
          const SizedBox(height: 12),
          _buildTextField('Konfirmasi Password', _confirmPasswordController),
          const SizedBox(height: 24),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.black,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
              onPressed: () {
                if (_newPasswordController.text !=
                    _confirmPasswordController.text) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Konfirmasi password tidak cocok'),
                    ),
                  );
                  return;
                }
                updatePassword();
              },
              child: const Text(
                'Update Password',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: Colors.white,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildTextField(String label, TextEditingController controller) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontSize: 12)),
        const SizedBox(height: 4),
        TextFormField(
          controller: controller,
          obscureText: true,
          decoration: InputDecoration(
            isDense: true,
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 8,
              vertical: 8,
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(6),
              borderSide: const BorderSide(color: Colors.grey),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(6),
              borderSide: const BorderSide(color: Colors.orange),
            ),
          ),
        ),
      ],
    );
  }
}
