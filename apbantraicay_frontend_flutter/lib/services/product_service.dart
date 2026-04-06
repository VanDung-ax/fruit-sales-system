import 'dart:convert';
import 'package:http/http.dart' as http;
import '../config/api_config.dart';

class ProductService {
  // 1. Header chuẩn cho Ngrok và làm việc với JSON
  final Map<String, String> _headers = {
    "ngrok-skip-browser-warning": "69420",
    "Content-Type": "application/json",
    "Accept": "application/json",
  };

  // Hàm bổ trợ để làm sạch URL, tránh lỗi dư dấu gạch chéo //
  String get _baseUrl {
    return ApiConfig.baseUrl.endsWith('/')
        ? ApiConfig.baseUrl
        : "${ApiConfig.baseUrl}/";
  }

  // --- 1. LẤY DANH MỤC ---
  Future<List<dynamic>> getCategories() async {
    final url = Uri.parse("${_baseUrl}get_categories.php");
    try {
      final response = await http.get(url, headers: _headers);
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi tải danh mục: $e"); }
    return [];
  }

  // --- 2. LẤY SẢN PHẨM (Mặc định hoặc theo Danh mục) ---
  Future<List<dynamic>> getProducts() async {
    final url = Uri.parse("${_baseUrl}get_products.php");
    try {
      final response = await http.get(url, headers: _headers);
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi tải sản phẩm: $e"); }
    return [];
  }

  Future<List<dynamic>> getProductsByCategory(String categoryName) async {
    final url = Uri.parse("${_baseUrl}get_products_by_category.php?category=$categoryName");
    try {
      final response = await http.get(url, headers: _headers);
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi tải sản phẩm theo danh mục: $e"); }
    return [];
  }

  // --- 3. TÌM KIẾM SẢN PHẨM ---
  Future<List<dynamic>> searchProducts(String keyword) async {
    final url = Uri.parse("${_baseUrl}search_products.php?keyword=$keyword");
    try {
      final response = await http.get(url, headers: _headers);
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi tìm kiếm: $e"); }
    return [];
  }

  // --- 4. GIỎ HÀNG (Thêm, Lấy, Cập nhật, Xóa sạch) ---
  Future<Map<String, dynamic>> addToCart(int userId, int productId, int quantity) async {
    final url = Uri.parse("${_baseUrl}add_to_cart.php");
    try {
      final response = await http.post(url, headers: _headers,
        body: jsonEncode({"user_id": userId, "product_id": productId, "quantity": quantity}),
      );
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi addToCart: $e"); }
    return {"status": "error", "message": "Lỗi kết nối"};
  }

  Future<List<dynamic>> getCart(int userId) async {
    final url = Uri.parse("${_baseUrl}get_cart.php?user_id=$userId");
    try {
      final response = await http.get(url, headers: _headers);
      return response.statusCode == 200 ? jsonDecode(response.body) : [];
    } catch (e) { print("Lỗi getCart: $e"); return []; }
  }

  Future<Map<String, dynamic>> updateCart(int cartId, String action) async {
    final url = Uri.parse("${_baseUrl}update_cart.php");
    try {
      final response = await http.post(url, headers: _headers,
        body: jsonEncode({"cart_id": cartId, "action": action}),
      );
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi updateCart: $e"); }
    return {"status": "error"};
  }

  Future<Map<String, dynamic>> clearCart(int userId) async {
    final url = Uri.parse("${_baseUrl}clear_cart.php");
    try {
      final response = await http.post(url, headers: _headers, body: jsonEncode({"user_id": userId}));
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi clearCart: $e"); }
    return {"status": "error"};
  }

  // --- 5. THANH TOÁN & KHUYẾN MÃI ---
  Future<Map<String, dynamic>> validatePromoCode(String code) async {
    final url = Uri.parse("${_baseUrl}validate_promo.php?code=$code");
    try {
      final response = await http.get(url, headers: _headers);
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi validatePromo: $e"); }
    return {"status": "error", "message": "Lỗi kết nối"};
  }

  Future<Map<String, dynamic>> placeOrder(Map<String, dynamic> orderData) async {
    final url = Uri.parse("${_baseUrl}place_order.php");
    try {
      final response = await http.post(url, headers: _headers, body: jsonEncode(orderData));
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi placeOrder: $e"); }
    return {"status": "error", "message": "Không thể đặt hàng"};
  }

  // --- 6. NGƯỜI DÙNG (Profile, Cập nhật, Thống kê đơn hàng) ---
  Future<Map<String, dynamic>> getUserProfile(int userId) async {
    final url = Uri.parse("${_baseUrl}get_user.php?id=$userId");
    try {
      final response = await http.get(url, headers: _headers);
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi getUserProfile: $e"); }
    return {"status": "error"};
  }

  Future<Map<String, dynamic>> updateProfile(int userId, String name, String email) async {
    final url = Uri.parse("${_baseUrl}update_profile.php");
    try {
      final response = await http.post(url, headers: _headers,
        body: jsonEncode({"user_id": userId, "name": name, "email": email}),
      );
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi updateProfile: $e"); }
    return {"status": "error"};
  }

  // Lấy lịch sử và stats (Dùng cho cả trang Profile để lấy Hạng/Rank)
  Future<Map<String, dynamic>> getOrderHistory(int userId) async {
    final url = Uri.parse("${_baseUrl}get_order_history.php?user_id=$userId");
    try {
      final response = await http.get(url, headers: _headers);
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi getOrderHistory: $e"); }
    return {"status": "error"};
  }

  Future<Map<String, dynamic>> getLastOrderInfo(int userId) async {
    final url = Uri.parse("${_baseUrl}get_last_order_info.php?user_id=$userId");
    try {
      final response = await http.get(url, headers: _headers);
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) { print("Lỗi getLastOrderInfo: $e"); }
    return {"status": "error"};
  }
  Future<Map<String, dynamic>> updateAddress(int userId, String name, String phone, String address) async {
    final url = Uri.parse("${_baseUrl}update_address.php");
    try {
      final response = await http.post(
        url,
        headers: _headers,
        body: jsonEncode({
          "user_id": userId,
          "full_name": name,
          "phone": phone,
          "address": address
        }),
      );
      if (response.statusCode == 200) return jsonDecode(response.body);
    } catch (e) {
      print("Lỗi updateAddress: $e");
    }
    return {"status": "error", "message": "Lỗi kết nối"};
  }
}