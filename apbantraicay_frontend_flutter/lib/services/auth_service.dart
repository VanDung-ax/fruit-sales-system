import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';
import '../models/user_model.dart';

class AuthService {
  // 1. Header quan trọng: Vượt rào Ngrok và định dạng dữ liệu để PHP nhận được $_POST
  final Map<String, String> _headers = {
    "ngrok-skip-browser-warning": "69420",
    "Content-Type": "application/x-www-form-urlencoded",
    "Accept": "application/json",
  };

  Future<User?> login(String email, String password) async {
    // 2. Tự động xử lý dấu gạch chéo để tránh lỗi URL //login.php
    final String cleanBaseUrl = ApiConfig.baseUrl.endsWith('/')
        ? ApiConfig.baseUrl
        : "${ApiConfig.baseUrl}/";
    final url = Uri.parse("${cleanBaseUrl}login.php");

    try {
      print("--- Đang gọi Login ---");
      print("URL: $url");

      final response = await http.post(
        url,
        headers: _headers,
        body: {
          "email": email,
          "password": password,
        },
      );

      // 3. Log phản hồi từ Server (Dũng xem ở Debug Console)
      print("Status Code: ${response.statusCode}");
      print("Nội dung Server trả về: ${response.body}");

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data["status"] == "success") {
          return User.fromJson(data["user"]);
        }
      }
    } catch (e) {
      print("Lỗi hệ thống (Login): $e");
    }
    return null;
  }

  Future<bool> register(String name, String email, String password) async {
    final String cleanBaseUrl = ApiConfig.baseUrl.endsWith('/')
        ? ApiConfig.baseUrl
        : "${ApiConfig.baseUrl}/";
    final url = Uri.parse("${cleanBaseUrl}register.php");

    try {
      print("--- Đang gọi Register ---");
      print("URL: $url");

      final response = await http.post(
        url,
        headers: _headers,
        body: {
          "name": name,
          "email": email,
          "password": password,
        },
      );

      print("Status Code: ${response.statusCode}");
      print("Nội dung Server trả về: ${response.body}");

      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        return data["status"] == "success";
      }
    } catch (e) {
      print("Lỗi hệ thống (Register): $e");
    }
    return false;
  }
}