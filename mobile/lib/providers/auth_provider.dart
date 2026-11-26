import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../models/user_model.dart';

class AuthProvider extends ChangeNotifier {
  final ApiClient _apiClient = ApiClient();
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  UserModel? _user;
  bool _isLoading = false;
  String? _errorMessage;

  UserModel? get user => _user;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<bool> login(String email, String password) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await _apiClient.dio.post(
        ApiConstants.login,
        data: {
          'email': email,
          'password': password,
        },
      );

      final data = response.data;
      // Sesuaikan key token & user dengan response JSON di AuthController.php kamu
      final String token = data['token'] ?? data['access_token'] ?? data['data']['token'];
      final Map<String, dynamic> userJson = data['user'] ?? data['data']['user'];

      await _storage.write(key: 'auth_token', value: token);
      _user = UserModel.fromJson(userJson);

      _isLoading = false;
      notifyListeners();
      return true;
    } on DioException catch (e) {
      _isLoading = false;
      if (e.response != null) {
        _errorMessage = e.response?.data['message'] ?? 'Email atau kata sandi salah.';
      } else {
        _errorMessage = 'Koneksi ke server gagal. Periksa jaringan Wi-Fi/internet.';
      }
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    try {
      await _apiClient.dio.post(ApiConstants.logout);
    } catch (_) {
      // Tetap hapus sesi lokal meski offline
    } finally {
      await _storage.delete(key: 'auth_token');
      _user = null;
      notifyListeners();
    }
  }
}