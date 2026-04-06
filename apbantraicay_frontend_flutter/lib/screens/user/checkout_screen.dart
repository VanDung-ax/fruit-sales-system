import 'package:flutter/material.dart';
import '../../services/product_service.dart';
import '../../utils/format_utils.dart';
import '../../config/api_config.dart';

class CheckoutScreen extends StatefulWidget {
  final int userId;
  final double totalAmount;
  final List cartItems;

  const CheckoutScreen({
    super.key,
    required this.userId,
    required this.totalAmount,
    required this.cartItems
  });

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final _formKey = GlobalKey<FormState>();
  final _productService = ProductService();

  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _promoController = TextEditingController();

  String _paymentMethod = "COD";
  bool isProcessing = false;

  double _discountAmount = 0;
  String? _appliedPromoCode;
  double _finalTotal = 0;

  @override
  void initState() {
    super.initState();
    _finalTotal = widget.totalAmount;
    _loadInitialData();
  }

  Future<void> _loadInitialData() async {
    setState(() => isProcessing = true);
    final userResult = await _productService.getUserProfile(widget.userId);
    final lastOrderResult = await _productService.getLastOrderInfo(widget.userId);

    if (mounted) {
      setState(() {
        if (userResult['status'] == 'success') {
          _emailController.text = userResult['data']['email'] ?? "";
        }
        if (lastOrderResult['status'] == 'success') {
          final data = lastOrderResult['data'];
          _nameController.text = data['full_name'] ?? "";
          _phoneController.text = data['phone'] ?? "";
          _addressController.text = data['address'] ?? "";
        }
        isProcessing = false;
      });
    }
  }

  // --- LOGIC THANH TOÁN QR ---
  void _showMomoQRDialog() {
    // THÔNG TIN TÀI KHOẢN CỦA BẠN (Hãy sửa tại đây)
    const String myPhone = "0768597754"; // Số điện thoại đăng ký Momo/Ngân hàng
    const String myName = "NGUYEN VAN DUNG"; // Tên không dấu

    // Tạo link VietQR động (Dùng được cho cả Momo và App Ngân hàng)
    // Cấu trúc: https://img.vietqr.io/image/<BANK_ID>-<ACCOUNT_NO>-<TEMPLATE>.png?amount=<AMOUNT>&addInfo=<DESCRIPTION>&accountName=<NAME>
    final String qrUrl = "https://img.vietqr.io/image/971005-$myPhone-compact2.png?amount=${_finalTotal.toInt()}&addInfo=Thanh toan don hang ${_nameController.text}&accountName=$myName";
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Quét mã thanh toán", textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold)),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Text("Mở App Momo hoặc Ngân hàng để quét mã bên dưới", textAlign: TextAlign.center, style: TextStyle(fontSize: 13, color: Colors.grey)),
            const SizedBox(height: 15),
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(border: Border.all(color: Colors.grey.shade200), borderRadius: BorderRadius.circular(10)),
              child: Image.network(
                qrUrl,
                width: 220,
                height: 220,
                fit: BoxFit.contain,
                errorBuilder: (context, error, stackTrace) => const Icon(Icons.qr_code_2, size: 100, color: Colors.grey),
              ),
            ),
            const SizedBox(height: 15),
            Text(FormatUtils.formatPrice(_finalTotal), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.red)),
            const Text("Nội dung: Thanh toán đơn hàng", style: TextStyle(fontSize: 12)),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Hủy", style: TextStyle(color: Colors.grey))),
          ElevatedButton(
            onPressed: () {
              Navigator.pop(context);
              _executePlaceOrder();
            },
            style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF13EC13)),
            child: const Text("Xác nhận đã chuyển", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
          ),
        ],
      ),
    );
  }

  Future<void> _applyPromo() async {
    String code = _promoController.text.trim();
    if (code.isEmpty) return;
    setState(() => isProcessing = true);
    final result = await _productService.validatePromoCode(code);
    if (mounted) {
      setState(() {
        isProcessing = false;
        if (result['status'] == 'success') {
          double val = double.tryParse(result['discount_value'].toString()) ?? 0;
          _discountAmount = result['discount_type'] == 'percentage' ? widget.totalAmount * (val / 100) : val;
          if (_discountAmount > widget.totalAmount) _discountAmount = widget.totalAmount;
          _finalTotal = widget.totalAmount - _discountAmount;
          _appliedPromoCode = code;
        } else {
          _discountAmount = 0; _finalTotal = widget.totalAmount; _appliedPromoCode = null;
        }
      });
    }
  }

  void _processOrder() {
    if (!_formKey.currentState!.validate()) return;
    if (_paymentMethod == "Momo") {
      _showMomoQRDialog();
    } else {
      _executePlaceOrder();
    }
  }

  Future<void> _executePlaceOrder() async {
    setState(() => isProcessing = true);
    final orderData = {
      "user_id": widget.userId,
      "full_name": _nameController.text,
      "phone": _phoneController.text,
      "address": _addressController.text,
      "total_amount": _finalTotal,
      "promotion_code": _appliedPromoCode,
      "payment_method": _paymentMethod,
      "items": widget.cartItems.map((item) => {
        "product_id": item['product_id'],
        "quantity": item['quantity'],
        "price": item['price']
      }).toList(),
    };
    final result = await _productService.placeOrder(orderData);
    if (mounted) {
      setState(() => isProcessing = false);
      if (result['status'] == 'success') {
        _showSuccessDialog();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(result['message'] ?? "Lỗi đặt hàng")));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF6F8F6),
      appBar: AppBar(
        title: const Text("Thanh toán", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
        centerTitle: true, backgroundColor: Colors.white, elevation: 0.5,
        leading: IconButton(onPressed: () => Navigator.pop(context), icon: const Icon(Icons.arrow_back_ios, color: Colors.black)),
      ),
      body: isProcessing
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF13EC13)))
          : Form(
        key: _formKey,
        child: ListView(
          padding: const EdgeInsets.all(20),
          children: [
            const Text("Tóm tắt đơn hàng", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            ...widget.cartItems.map((item) => _buildProductItem(item)).toList(),

            const SizedBox(height: 25),
            _buildPromoSection(),

            const SizedBox(height: 25),
            const Text("Thông tin giao hàng", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 15),
            _buildField(_nameController, "Họ tên người nhận", Icons.person),
            const SizedBox(height: 12),
            _buildField(_phoneController, "Số điện thoại", Icons.phone, keyboardType: TextInputType.phone),
            const SizedBox(height: 12),
            _buildField(_addressController, "Địa chỉ", Icons.location_on, maxLines: 2),

            const SizedBox(height: 30),
            const Text("Phương thức thanh toán", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            _buildPaymentMethodSelector(),

            const SizedBox(height: 30),
            _buildPriceSummary(),
            const SizedBox(height: 30),
            _buildConfirmButton(),
          ],
        ),
      ),
    );
  }

  Widget _buildPromoSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text("Mã khuyến mãi", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
              child: TextFormField(
                controller: _promoController,
                decoration: InputDecoration(
                  hintText: "Nhập mã khuyến mãi",
                  filled: true, fillColor: Colors.white,
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
                ),
              ),
            ),
            const SizedBox(width: 10),
            ElevatedButton(
              onPressed: _applyPromo,
              style: ElevatedButton.styleFrom(backgroundColor: Colors.black, shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))),
              child: const Text("Áp dụng", style: TextStyle(color: Colors.white)),
            )
          ],
        ),
      ],
    );
  }

  Widget _buildPaymentMethodSelector() {
    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
      child: Column(
        children: [
          RadioListTile(
            title: const Text("Thanh toán khi nhận hàng (COD)"),
            activeColor: const Color(0xFF13EC13),
            value: "COD", groupValue: _paymentMethod,
            onChanged: (v) => setState(() => _paymentMethod = v!),
          ),
          const Divider(height: 1),
          RadioListTile(
            title: const Text("Chuyển khoản / Ví Momo (QR)"),
            activeColor: const Color(0xFF13EC13),
            value: "Momo", groupValue: _paymentMethod,
            onChanged: (v) => setState(() => _paymentMethod = v!),
          ),
        ],
      ),
    );
  }

  Widget _buildPriceSummary() {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(15)),
      child: Column(
        children: [
          _rowPrice("Tạm tính:", widget.totalAmount),
          if (_discountAmount > 0) _rowPrice("Giảm giá:", -_discountAmount, color: Colors.red),
          const Divider(),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text("Tổng cộng:", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              Text(FormatUtils.formatPrice(_finalTotal), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: Color(0xFF13EC13))),
            ],
          ),
        ],
      ),
    );
  }

  Widget _rowPrice(String label, double price, {Color? color}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey)),
          Text(FormatUtils.formatPrice(price), style: TextStyle(color: color, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }

  Widget _buildField(TextEditingController controller, String label, IconData icon, {TextInputType keyboardType = TextInputType.text, int maxLines = 1}) {
    return TextFormField(
      controller: controller, keyboardType: keyboardType, maxLines: maxLines,
      decoration: InputDecoration(
        labelText: label, prefixIcon: Icon(icon, color: const Color(0xFF13EC13)),
        filled: true, fillColor: Colors.white,
        border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: BorderSide.none),
      ),
      validator: (v) => v!.isEmpty ? "Vui lòng nhập" : null,
    );
  }

  Widget _buildProductItem(dynamic item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8), padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(12)),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: Image.network("${ApiConfig.imageUrl}${item['image']}", width: 45, height: 45, fit: BoxFit.cover),
          ),
          const SizedBox(width: 12),
          Expanded(child: Text(item['name'], style: const TextStyle(fontWeight: FontWeight.bold))),
          Text("x${item['quantity']}"),
        ],
      ),
    );
  }

  Widget _buildConfirmButton() {
    return SizedBox(
      width: double.infinity, height: 55,
      child: ElevatedButton(
        onPressed: _processOrder,
        style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF13EC13), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15))),
        child: const Text("XÁC NHẬN ĐẶT HÀNG", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold, fontSize: 16)),
      ),
    );
  }

  void _showSuccessDialog() {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: const Icon(Icons.check_circle, color: Color(0xFF13EC13), size: 60),
        content: const Text("Đặt hàng thành công!", textAlign: TextAlign.center),
        actions: [
          Center(
            child: TextButton(
              onPressed: () => Navigator.of(context).popUntil((route) => route.isFirst),
              child: const Text("Về trang chủ", style: TextStyle(fontWeight: FontWeight.bold)),
            ),
          )
        ],
      ),
    );
  }
}