import 'package:flutter/material.dart';
import '../../config/api_config.dart';
import '../../services/product_service.dart';
import '../../utils/format_utils.dart';
import 'product_detail_screen.dart';

class CategoriesScreen extends StatefulWidget {
  final int userId;

  const CategoriesScreen({super.key, required this.userId});

  @override
  State<CategoriesScreen> createState() => _CategoriesScreenState();
}

class _CategoriesScreenState extends State<CategoriesScreen> {
  final ProductService _productService = ProductService();
  List categories = [];
  List products = [];
  String selectedCategoryName = "";
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _fetchCategories();
  }

  // Lấy danh mục từ Backend
  Future<void> _fetchCategories() async {
    try {
      final data = await _productService.getCategories();
      if (mounted && data.isNotEmpty) {
        setState(() {
          categories = data;
          selectedCategoryName = categories[0]['name'].toString();
          _fetchProductsByCategory(selectedCategoryName);
        });
      }
    } catch (e) {
      debugPrint("Lỗi tải danh mục: $e");
    }
  }

  // Lấy sản phẩm theo tên danh mục
  Future<void> _fetchProductsByCategory(String categoryName) async {
    setState(() => isLoading = true);
    try {
      final data = await _productService.getProductsByCategory(categoryName);
      if (mounted) {
        setState(() {
          products = data;
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("Lỗi tải sản phẩm: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  // Hàm thêm vào giỏ hàng
  Future<void> _addToCart(dynamic product) async {
    if (widget.userId == 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Vui lòng đăng nhập để thêm vào giỏ hàng")),
      );
      return;
    }

    try {
      final result = await _productService.addToCart(
          widget.userId,
          int.parse(product['id'].toString()),
          1
      );

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text(result['message'] ?? "Đã thêm vào giỏ hàng")),
        );
      }
    } catch (e) {
      debugPrint("Lỗi thêm giỏ hàng: $e");
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text("Phân loại trái cây", style: TextStyle(fontWeight: FontWeight.bold)),
        centerTitle: true,
        elevation: 0.5,
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
      ),
      body: Row(
        children: [
          _buildCategorySidebar(),
          Expanded(child: _buildProductList()),
        ],
      ),
    );
  }

  Widget _buildCategorySidebar() {
    return Container(
      width: 100,
      color: Colors.grey[100],
      child: ListView.builder(
        itemCount: categories.length,
        itemBuilder: (context, index) {
          final cat = categories[index];
          bool isSelected = selectedCategoryName == cat['name'];

          return GestureDetector(
            onTap: () {
              setState(() => selectedCategoryName = cat['name']);
              _fetchProductsByCategory(selectedCategoryName);
            },
            child: Container(
              padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 10),
              decoration: BoxDecoration(
                color: isSelected ? Colors.white : Colors.transparent,
                border: isSelected ? const Border(
                    left: BorderSide(color: Color(0xFF13EC13), width: 4)
                ) : null,
              ),
              child: Text(
                cat['name'],
                style: TextStyle(
                  fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                  color: isSelected ? const Color(0xFF13EC13) : Colors.black54,
                  fontSize: 13,
                ),
                textAlign: TextAlign.center,
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildProductList() {
    if (isLoading) return const Center(child: CircularProgressIndicator(color: Color(0xFF13EC13)));
    if (products.isEmpty) return const Center(child: Text("Không có sản phẩm trong mục này"));

    return GridView.builder(
      padding: const EdgeInsets.all(12),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        childAspectRatio: 0.72,
        mainAxisSpacing: 12,
        crossAxisSpacing: 12,
      ),
      itemCount: products.length,
      itemBuilder: (context, index) => _buildProductCard(products[index]),
    );
  }

  Widget _buildProductCard(dynamic product) {
    return GestureDetector(
      onTap: () => Navigator.push(
          context,
          MaterialPageRoute(
              builder: (_) => ProductDetailsScreen(
                product: product,
                userId: widget.userId, // ĐÃ CẬP NHẬT: Truyền userId vào đây
              )
          )
      ),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(15),
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 8)],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Expanded(
              child: ClipRRect(
                borderRadius: const BorderRadius.vertical(top: Radius.circular(15)),
                child: Image.network(
                  "${ApiConfig.imageUrl}${product['image']}",
                  width: double.infinity,
                  fit: BoxFit.cover,
                  errorBuilder: (context, error, stackTrace) =>
                  const Center(child: Icon(Icons.image_not_supported, color: Colors.grey)),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(10.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                      product['name'] ?? "",
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis
                  ),
                  const SizedBox(height: 6),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        FormatUtils.formatPrice(product['price']),
                        style: const TextStyle(
                            color: Color(0xFF13EC13),
                            fontWeight: FontWeight.bold,
                            fontSize: 12
                        ),
                      ),
                      GestureDetector(
                        onTap: () => _addToCart(product),
                        child: Container(
                          padding: const EdgeInsets.all(4),
                          decoration: const BoxDecoration(
                            color: Color(0xFF13EC13),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(Icons.add, color: Colors.white, size: 16),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}