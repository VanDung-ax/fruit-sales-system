import 'package:flutter/material.dart';
import '../../models/user_model.dart';
import '../../services/product_service.dart';
import '../../utils/format_utils.dart';
import 'order_history_screen.dart';
import 'address_screen.dart';

class ProfileScreen extends StatefulWidget {
  final User? currentUser;
  final VoidCallback onLogout;

  const ProfileScreen({
    super.key,
    required this.currentUser,
    required this.onLogout
  });

  @override
  State<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final ProductService _productService = ProductService();

  // Quản lý trạng thái dữ liệu từ Backend
  int orderCount = 0;
  double totalSpent = 0;
  String userRank = "Đồng"; // Mặc định hạng ban đầu
  bool isLoading = true;

  late String currentName;
  late String currentEmail;

  @override
  void initState() {
    super.initState();
    currentName = widget.currentUser?.name ?? "Guest User";
    currentEmail = widget.currentUser?.email ?? "Not logged in";
    _loadUserStats();
  }

  // Lấy dữ liệu thống kê và hạng từ API
  Future<void> _loadUserStats() async {
    if (widget.currentUser == null) return;

    final result = await _productService.getOrderHistory(widget.currentUser!.id);
    if (mounted && result['status'] == 'success') {
      setState(() {
        orderCount = result['stats']['total_orders'];
        totalSpent = double.parse(result['stats']['total_spent'].toString());
        // Nhận hạng (Đồng, Bạc, Vàng, Kim Cương) từ Backend
        userRank = result['stats']['rank_name'] ?? "Đồng";
        isLoading = false;
      });
    }
  }

  // Hàm xác định màu sắc hiển thị theo hạng khách hàng
  Color _getRankColor() {
    switch (userRank) {
      case "Bạc":
        return Colors.blueGrey;
      case "Vàng":
        return const Color(0xFFFFD700); // Màu vàng Gold
      case "Kim Cương":
        return Colors.blueAccent;
      default:
        return Colors.orangeAccent; // Mặc định hạng Đồng
    }
  }

  void _showEditProfileDialog() {
    final nameController = TextEditingController(text: currentName);
    final emailController = TextEditingController(text: currentEmail);

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text("Cập nhật thông tin"),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(controller: nameController, decoration: const InputDecoration(labelText: "Họ tên")),
            TextField(controller: emailController, decoration: const InputDecoration(labelText: "Email")),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Hủy")),
          ElevatedButton(
              onPressed: () async {
                final res = await _productService.updateProfile(
                    widget.currentUser!.id,
                    nameController.text,
                    emailController.text
                );
                if (res['status'] == 'success') {
                  setState(() {
                    currentName = nameController.text;
                    currentEmail = emailController.text;
                  });
                  if (mounted) Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Đã cập nhật thành công!")));
                }
              },
              child: const Text("Lưu")
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF6F8F6),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: const Text("Hồ sơ cá nhân", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Column(
          children: [
            const SizedBox(height: 20),
            _buildProfileHeader(),
            const SizedBox(height: 25),
            _buildStatsRow(),
            const SizedBox(height: 30),
            _buildMenuSection(),
          ],
        ),
      ),
    );
  }

  Widget _buildProfileHeader() {
    return Column(
      children: [
        Stack(
          children: [
            Container(
              decoration: BoxDecoration(shape: BoxShape.circle, border: Border.all(color: const Color(0xFF13EC13), width: 2)),
              child: const CircleAvatar(radius: 60, backgroundImage: NetworkImage("https://via.placeholder.com/150")),
            ),
            Positioned(
              bottom: 5, right: 5,
              child: GestureDetector(
                onTap: _showEditProfileDialog,
                child: Container(
                  padding: const EdgeInsets.all(4),
                  decoration: const BoxDecoration(color: Color(0xFF13EC13), shape: BoxShape.circle),
                  child: const Icon(Icons.edit, color: Colors.white, size: 20),
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 15),
        Text(currentName, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold)),
        Text(currentEmail, style: TextStyle(color: Colors.grey[600], fontSize: 14)),
        const SizedBox(height: 10),
        // Badge hiển thị hạng thành viên
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
          decoration: BoxDecoration(
            color: _getRankColor().withOpacity(0.1),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: _getRankColor(), width: 1),
          ),
          child: Text(
            "THÀNH VIÊN ${userRank.toUpperCase()}",
            style: TextStyle(color: _getRankColor(), fontWeight: FontWeight.bold, fontSize: 12),
          ),
        ),
      ],
    );
  }

  Widget _buildStatsRow() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          _statItem(orderCount.toString(), "ĐƠN HÀNG"),
          _statItem(FormatUtils.formatPrice(totalSpent), "ĐÃ CHI"),
          // Ô hiển thị hạng động với màu sắc tương ứng
          _statItem(userRank, "HẠNG", valueColor: _getRankColor()),
        ],
      ),
    );
  }

  Widget _statItem(String value, String label, {Color? valueColor}) {
    return Container(
      width: 105,
      padding: const EdgeInsets.symmetric(vertical: 15),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)],
      ),
      child: Column(
        children: [
          Text(
              value,
              style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: valueColor ?? const Color(0xFF13EC13)
              ),
              textAlign: TextAlign.center
          ),
          const SizedBox(height: 4),
          Text(label, style: const TextStyle(fontSize: 10, color: Colors.grey, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildMenuSection() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text("HOẠT ĐỘNG CỦA TÔI", style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey)),
          const SizedBox(height: 15),
          _menuItem(Icons.shopping_bag, "Đơn hàng của tôi", "Xem lại lịch sử mua trái cây", onTap: () {
            Navigator.push(context, MaterialPageRoute(builder: (_) => OrderHistoryScreen(userId: widget.currentUser!.id)));
          }),

          // ĐÃ CẬP NHẬT: Thêm logic điều hướng cho Địa chỉ
          _menuItem(Icons.location_on, "Địa chỉ đã lưu", "Nhà riêng, công ty...", onTap: () {
            if (widget.currentUser != null) {
              Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => AddressScreen(userId: widget.currentUser!.id))
              );
            }
          }),

          const SizedBox(height: 20),
          InkWell(
            onTap: widget.onLogout,
            child: Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(color: Colors.red.withOpacity(0.05), borderRadius: BorderRadius.circular(20)),
              child: const Row( // Đã xóa const ở Row để tránh lỗi với Icon/Text con nếu có dynamic
                children: [
                  Icon(Icons.logout, color: Colors.red),
                  SizedBox(width: 15),
                  Text("Đăng xuất", style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold, fontSize: 16)),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _menuItem(IconData icon, String title, String sub, {VoidCallback? onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.only(bottom: 15),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(color: const Color(0xFF13EC13).withOpacity(0.1), borderRadius: BorderRadius.circular(12)),
              child: Icon(icon, color: const Color(0xFF13EC13), size: 24),
            ),
            const SizedBox(width: 15),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                  Text(sub, style: const TextStyle(color: Colors.grey, fontSize: 11)),
                ],
              ),
            ),
            const Icon(Icons.arrow_forward_ios, size: 14, color: Colors.grey),
          ],
        ),
      ),
    );
  }
}