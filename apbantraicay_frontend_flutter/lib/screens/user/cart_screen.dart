import 'package:flutter/material.dart';
import '../../config/api_config.dart';
import '../../services/product_service.dart';
import '../../utils/format_utils.dart';
import 'checkout_screen.dart';
class CartScreen extends StatefulWidget {
  final int userId;

  const CartScreen({super.key, required this.userId});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  final ProductService _productService = ProductService();
  List cartItems = [];
  bool isLoading = true;
  double totalAmount = 0;

  @override
  void initState() {
    super.initState();
    _loadCartData();
  }

  // Tải dữ liệu giỏ hàng từ backend
  Future<void> _loadCartData() async {
    if (widget.userId == 0) {
      setState(() {
        isLoading = false;
        cartItems = [];
      });
      return;
    }

    setState(() => isLoading = true);
    try {
      final data = await _productService.getCart(widget.userId);
      if (mounted) {
        setState(() {
          cartItems = data;
          _calculateTotal();
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("Lỗi tải giỏ hàng: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  // Tính tổng tiền dựa trên số lượng và giá
  void _calculateTotal() {
    double total = 0;
    for (var item in cartItems) {
      double price = double.tryParse(item['price'].toString()) ?? 0;
      int qty = int.tryParse(item['quantity'].toString()) ?? 0;
      total += price * qty;
    }
    totalAmount = total;
  }

  // Xử lý Tăng/Giảm/Xóa đồng bộ với update_cart.php
  Future<void> _updateCartStatus(int cartId, String action) async {
    try {
      final result = await _productService.updateCart(cartId, action);
      if (result['status'] == 'success') {
        _loadCartData(); // Tải lại để đồng bộ dữ liệu mới nhất
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Thao tác thất bại")),
        );
      }
    } catch (e) {
      debugPrint("Lỗi cập nhật giỏ hàng: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF6F8F6),
      appBar: AppBar(
        title: const Text("Giỏ hàng của tôi", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
        centerTitle: true,
        backgroundColor: Colors.white,
        elevation: 0,
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF13EC13)))
          : cartItems.isEmpty
          ? _buildEmptyCart()
          : Column(
        children: [
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: cartItems.length,
              itemBuilder: (context, index) => _buildCartItem(cartItems[index]),
            ),
          ),
          _buildOrderSummary(),
        ],
      ),
    );
  }

  Widget _buildEmptyCart() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.shopping_cart_outlined, size: 80, color: Colors.grey[300]),
          const SizedBox(height: 16),
          const Text("Giỏ hàng đang trống", style: TextStyle(color: Colors.grey, fontSize: 16)),
        ],
      ),
    );
  }

  Widget _buildCartItem(dynamic item) {
    int cartId = int.parse(item['cart_id'].toString()); // ID của dòng trong bảng cart

    return Container(
      margin: const EdgeInsets.only(bottom: 15),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 10)],
      ),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: Image.network(
              "${ApiConfig.imageUrl}${item['image']}",
              width: 80, height: 80, fit: BoxFit.cover,
              errorBuilder: (context, error, stackTrace) => const Icon(Icons.image, size: 50),
            ),
          ),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item['name'] ?? "", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                const SizedBox(height: 4),
                Text(
                  FormatUtils.formatPrice(item['price']),
                  style: const TextStyle(color: Color(0xFF13EC13), fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              GestureDetector(
                onTap: () => _updateCartStatus(cartId, 'delete'),
                child: const Icon(Icons.delete_outline, color: Colors.red, size: 22),
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  _qtyButton(Icons.remove, () => _updateCartStatus(cartId, 'decrease')),
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 12),
                    child: Text("${item['quantity']}", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  ),
                  _qtyButton(Icons.add, () => _updateCartStatus(cartId, 'increase')),
                ],
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _qtyButton(IconData icon, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(4),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey.shade300),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Icon(icon, size: 18, color: Colors.black),
      ),
    );
  }

  Widget _buildOrderSummary() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text("Tổng thanh toán:", style: TextStyle(fontSize: 16, color: Colors.grey)),
              Text(
                FormatUtils.formatPrice(totalAmount),
                style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Color(0xFF13EC13)),
              ),
            ],
          ),
          const SizedBox(height: 20),
          SizedBox(
            width: double.infinity,
            height: 50,
            child: ElevatedButton(
                onPressed: () {
                  Navigator.push(context, MaterialPageRoute(builder: (context) => CheckoutScreen(
                    userId: widget.userId,
                    totalAmount: totalAmount,
                    cartItems: cartItems,
                  )));
                },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF13EC13),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
              child: const Text("THANH TOÁN NGAY", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      ),
    );
  }
}