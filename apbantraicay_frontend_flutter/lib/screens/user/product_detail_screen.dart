import 'package:flutter/material.dart';
import '../../config/api_config.dart';
import '../../utils/format_utils.dart';
import 'checkout_screen.dart'; // Import để chuyển sang trang thanh toán

class ProductDetailsScreen extends StatefulWidget {
  final dynamic product;
  final int userId; // Nhận userId để xử lý mua hàng

  const ProductDetailsScreen({
    super.key,
    required this.product,
    required this.userId
  });

  @override
  State<ProductDetailsScreen> createState() => _ProductDetailsScreenState();
}

class _ProductDetailsScreenState extends State<ProductDetailsScreen> {
  String selectedWeight = "1kg";
  int quantity = 1; // Số kg thực tế dùng để tính tiền

  // Hàm cập nhật khối lượng khi chọn các mốc có sẵn
  void _updateWeight(String weight) {
    setState(() {
      selectedWeight = weight;
      if (weight == "1kg") {
        quantity = 1;
      } else if (weight == "2kg") {
        quantity = 2;
      } else if (weight == "5kg") {
        quantity = 5;
      }
      // Nếu chọn "Custom", giữ nguyên quantity hiện tại để người dùng tự bấm +/-
    });
  }

  @override
  Widget build(BuildContext context) {
    // Tính tổng tiền dựa trên số lượng kg
    double unitPrice = double.tryParse(widget.product['price'].toString()) ?? 0;
    double totalPrice = unitPrice * quantity;

    return Scaffold(
      backgroundColor: const Color(0xFFF6F8F6),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios, color: Colors.black, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          "Chi tiết sản phẩm",
          style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold),
        ),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. Hình ảnh sản phẩm
            _buildProductImage(),

            Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // 2. Tên và Giá hiển thị động
                  _buildTitleAndPrice(totalPrice),

                  const SizedBox(height: 25),

                  // 3. Chọn khối lượng
                  const Text("Chọn khối lượng (kg)", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 12),
                  _buildWeightSelector(),

                  // Hiển thị bộ tăng giảm nếu người dùng chọn "Custom"
                  if (selectedWeight == "Custom") _buildCustomQuantityCounter(),

                  const SizedBox(height: 25),

                  // 4. Mô tả
                  const Text("Mô tả sản phẩm", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: 8),
                  Text(
                    "Trái cây tươi sạch từ nông trại Fruit Store, đảm bảo quy trình thu hoạch an toàn, giữ trọn độ giòn ngọt và hàm lượng dinh dưỡng cao nhất cho sức khỏe gia đình bạn.",
                    style: TextStyle(color: Colors.grey[600], height: 1.5),
                  ),

                  const SizedBox(height: 25),

                  // 5. Thông tin dinh dưỡng
                  _buildNutritionalFacts(),

                  const SizedBox(height: 100),
                ],
              ),
            ),
          ],
        ),
      ),
      // Thanh hành động phía dưới
      bottomSheet: _buildBottomAction(totalPrice),
    );
  }

  Widget _buildProductImage() {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20),
      height: 280,
      width: double.infinity,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(30),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(30),
        child: Image.network(
          "${ApiConfig.imageUrl}${widget.product['image']}",
          fit: BoxFit.cover,
          errorBuilder: (context, error, stackTrace) => const Icon(Icons.broken_image, size: 50, color: Colors.grey),
        ),
      ),
    );
  }

  Widget _buildTitleAndPrice(double displayPrice) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          widget.product['name'] ?? "Sản phẩm",
          style: const TextStyle(fontSize: 26, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 10),
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text("Tổng thanh toán:", style: TextStyle(color: Colors.grey, fontSize: 16)),
            Text(
              FormatUtils.formatPrice(displayPrice),
              style: const TextStyle(color: Color(0xFF13EC13), fontSize: 24, fontWeight: FontWeight.bold),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildWeightSelector() {
    List<String> weights = ["1kg", "2kg", "5kg", "Custom"];
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: weights.map((w) => GestureDetector(
        onTap: () => _updateWeight(w),
        child: Container(
          width: 80,
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: selectedWeight == w ? const Color(0xFF13EC13).withOpacity(0.1) : Colors.white,
            borderRadius: BorderRadius.circular(15),
            border: Border.all(
              color: selectedWeight == w ? const Color(0xFF13EC13) : Colors.grey.shade200,
              width: 2,
            ),
          ),
          child: Center(
            child: Text(
              w,
              style: TextStyle(
                fontWeight: FontWeight.bold,
                color: selectedWeight == w ? Colors.black : Colors.grey,
              ),
            ),
          ),
        ),
      )).toList(),
    );
  }

  Widget _buildCustomQuantityCounter() {
    return Container(
      margin: const EdgeInsets.only(top: 15),
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(15),
          border: Border.all(color: Colors.grey.shade100)
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          IconButton(
            onPressed: () => setState(() { if (quantity > 1) quantity--; }),
            icon: const Icon(Icons.remove_circle_outline, color: Color(0xFF13EC13)),
          ),
          Text("$quantity kg", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
          IconButton(
            onPressed: () => setState(() { quantity++; }),
            icon: const Icon(Icons.add_circle_outline, color: Color(0xFF13EC13)),
          ),
        ],
      ),
    );
  }

  Widget _buildNutritionalFacts() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFF13EC13).withOpacity(0.05),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Column(
        children: [
          const Row(
            children: [
              Icon(Icons.eco, color: Color(0xFF13EC13)),
              SizedBox(width: 8),
              Text("Thông tin dinh dưỡng (trên 100g)", style: TextStyle(fontWeight: FontWeight.bold)),
            ],
          ),
          const SizedBox(height: 15),
          _nutritionRow("Năng lượng", "52 kcal", "Chất xơ", "2.4g"),
          const SizedBox(height: 12),
          _nutritionRow("Vitamin C", "8% DV", "Kali", "107mg"),
        ],
      ),
    );
  }

  Widget _nutritionRow(String l1, String v1, String l2, String v2) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        _nutritionItem(l1, v1),
        _nutritionItem(l2, v2),
      ],
    );
  }

  Widget _nutritionItem(String label, String value) {
    return SizedBox(
      width: 140,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey, fontSize: 13)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildBottomAction(double finalTotal) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: const BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.vertical(top: Radius.circular(25)),
      ),
      child: Row(
        children: [
          Expanded(
            child: OutlinedButton.icon(
              onPressed: () {
                // Thêm vào giỏ hàng (Basket) nếu cần
              },
              icon: const Icon(Icons.shopping_basket_outlined, color: Color(0xFF13EC13)),
              label: const Text("Giỏ hàng", style: TextStyle(color: Color(0xFF13EC13))),
              style: OutlinedButton.styleFrom(
                padding: const EdgeInsets.symmetric(vertical: 15),
                side: const BorderSide(color: Color(0xFF13EC13)),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
            ),
          ),
          const SizedBox(width: 15),
          Expanded(
            flex: 2,
            child: ElevatedButton(
              onPressed: () {
                // Tạo danh sách item giả lập để CheckoutScreen có thể đọc được
                List cartItems = [{
                  'product_id': widget.product['id'],
                  'name': widget.product['name'],
                  'image': widget.product['image'],
                  'quantity': quantity,
                  'price': widget.product['price'],
                }];

                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => CheckoutScreen(
                      userId: widget.userId,
                      totalAmount: finalTotal,
                      cartItems: cartItems,
                    ),
                  ),
                );
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF13EC13),
                padding: const EdgeInsets.symmetric(vertical: 15),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
              ),
              child: const Text(
                "Mua ngay",
                style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold),
              ),
            ),
          ),
        ],
      ),
    );
  }
}