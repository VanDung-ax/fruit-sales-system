import 'package:flutter/material.dart';
import '../../services/product_service.dart';

class AddressScreen extends StatefulWidget {
  final int userId;
  const AddressScreen({super.key, required this.userId});

  @override
  State<AddressScreen> createState() => _AddressScreenState();
}

class _AddressScreenState extends State<AddressScreen> {
  final _service = ProductService();
  final _formKey = GlobalKey<FormState>();

  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  final TextEditingController _addressController = TextEditingController();
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadCurrentAddress();
  }

  // Bước 1: Lấy thông tin từ đơn hàng gần nhất để hiển thị lên Form
  void _loadCurrentAddress() async {
    final result = await _service.getLastOrderInfo(widget.userId);
    if (mounted) {
      if (result['status'] == 'success') {
        final data = result['data'];
        _nameController.text = data['full_name'] ?? "";
        _phoneController.text = data['phone'] ?? "";
        _addressController.text = data['address'] ?? "";
      }
      setState(() => isLoading = false);
    }
  }

  // Bước 2: Gửi thông tin đã sửa về server để cập nhật vào đơn hàng đó
  void _handleSave() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => isLoading = true);
    final res = await _service.updateAddress(
      widget.userId,
      _nameController.text,
      _phoneController.text,
      _addressController.text,
    );

    if (mounted) {
      setState(() => isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(res['message']),
          backgroundColor: res['status'] == 'success' ? Colors.green : Colors.red,
        ),
      );
      if (res['status'] == 'success') Navigator.pop(context);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF6F8F6),
      appBar: AppBar(
        title: const Text("Địa chỉ của tôi", style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold)),
        centerTitle: true,
        backgroundColor: Colors.white,
        elevation: 0.5,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios, color: Colors.black, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFF13EC13)))
          : SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                "Thông tin này được lấy từ đơn hàng gần nhất của bạn. Bạn có thể chỉnh sửa để sử dụng cho lần thanh toán sau.",
                style: TextStyle(color: Colors.grey, fontSize: 13),
              ),
              const SizedBox(height: 25),
              _buildInputField("Họ và tên", _nameController, Icons.person_outline),
              const SizedBox(height: 15),
              _buildInputField("Số điện thoại", _phoneController, Icons.phone_android_outlined, keyboardType: TextInputType.phone),
              const SizedBox(height: 15),
              _buildInputField("Địa chỉ chi tiết", _addressController, Icons.location_on_outlined, maxLines: 3),
              const SizedBox(height: 40),
              SizedBox(
                width: double.infinity,
                height: 55,
                child: ElevatedButton(
                  onPressed: _handleSave,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF13EC13),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
                    elevation: 0,
                  ),
                  child: const Text(
                    "CẬP NHẬT ĐỊA CHỈ",
                    style: TextStyle(color: Colors.black, fontWeight: FontWeight.bold, fontSize: 16),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInputField(String label, TextEditingController controller, IconData icon,
      {TextInputType keyboardType = TextInputType.text, int maxLines = 1}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
        const SizedBox(height: 8),
        TextFormField(
          controller: controller,
          keyboardType: keyboardType,
          maxLines: maxLines,
          decoration: InputDecoration(
            prefixIcon: Icon(icon, color: const Color(0xFF13EC13)),
            filled: true,
            fillColor: Colors.white,
            contentPadding: const EdgeInsets.symmetric(vertical: 15, horizontal: 15),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide.none,
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: BorderSide.none,
            ),
          ),
          validator: (value) => (value == null || value.isEmpty) ? "Vui lòng không để trống" : null,
        ),
      ],
    );
  }
}