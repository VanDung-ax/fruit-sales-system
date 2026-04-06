import 'package:flutter/material.dart';
import '../../config/api_config.dart';
import '../../services/product_service.dart';
import '../auth/login_screen.dart';
import '../../models/user_model.dart';
import 'cart_screen.dart';
import 'profile_screen.dart';
import 'product_detail_screen.dart';
import 'categories_screen.dart';
import '../../utils/format_utils.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final ProductService _productService = ProductService();
  final TextEditingController _searchController = TextEditingController();

  List products = [];
  List categoriesList = [];
  bool isLoading = true;
  bool isSearching = false;

  bool isLoggedIn = false;
  User? currentUser;
  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();
    fetchInitialData();
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  Future<void> fetchInitialData() async {
    if (!mounted) return;
    setState(() {
      isLoading = true;
      isSearching = false;
    });
    try {
      final results = await Future.wait([
        _productService.getProducts(),
        _productService.getCategories(),
      ]);

      if (mounted) {
        setState(() {
          products = results[0];
          categoriesList = results[1];
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("Lỗi tải dữ liệu Home: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  void _onSearch(String value) async {
    if (value.trim().isEmpty) {
      fetchInitialData();
      return;
    }

    setState(() {
      isLoading = true;
      isSearching = true;
    });

    try {
      final results = await _productService.searchProducts(value);
      if (mounted) {
        setState(() {
          products = results;
          isLoading = false;
        });
      }
    } catch (e) {
      debugPrint("Lỗi tìm kiếm: $e");
      if (mounted) setState(() => isLoading = false);
    }
  }

  void logout() {
    setState(() {
      isLoggedIn = false;
      currentUser = null;
      _selectedIndex = 0;
    });
    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text("Đã đăng xuất")));
  }

  void goToLogin() async {
    final user = await Navigator.push(context, MaterialPageRoute(builder: (_) => const LoginScreen()));
    if (user != null) {
      setState(() {
        isLoggedIn = true;
        currentUser = user;
      });
    }
  }

  Future<void> _addToCart(dynamic product) async {
    if (!isLoggedIn || currentUser == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Vui lòng đăng nhập để thêm vào giỏ hàng")),
      );
      goToLogin();
      return;
    }

    try {
      final result = await _productService.addToCart(
          currentUser!.id,
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
      backgroundColor: const Color(0xFFF6F8F6),
      body: _selectedIndex == 0 ? _buildHomeContent() : _buildOtherPages(),
      bottomNavigationBar: _buildBottomNav(),
    );
  }

  Widget _buildHomeContent() {
    return SafeArea(
      child: RefreshIndicator(
        onRefresh: () async {
          _searchController.clear();
          await fetchInitialData();
        },
        child: CustomScrollView(
          slivers: [
            _buildTopHeader(),
            _buildSearchBar(),
            if (!isSearching) ...[
              _buildPromoBanners(),
              _buildSectionHeader("Loại trái cây"),
              _buildCategoriesGrid(),
            ],
            _buildSectionHeader(isSearching ? "Kết quả tìm kiếm" : "Sản phẩm nổi bật"),
            isLoading
                ? const SliverToBoxAdapter(child: Center(child: Padding(
              padding: EdgeInsets.all(30.0),
              child: CircularProgressIndicator(color: Color(0xFF13EC13)),
            )))
                : _buildProductGrid(),
          ],
        ),
      ),
    );
  }

  Widget _buildSearchBar() {
    return SliverToBoxAdapter(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
        child: TextField(
          controller: _searchController,
          onChanged: _onSearch,
          decoration: InputDecoration(
            hintText: "Tìm sản phẩm hoặc danh mục...",
            prefixIcon: const Icon(Icons.search, color: Colors.grey),
            suffixIcon: _searchController.text.isNotEmpty
                ? IconButton(
              icon: const Icon(Icons.clear, color: Colors.grey),
              onPressed: () {
                _searchController.clear();
                _onSearch("");
              },
            )
                : const Icon(Icons.tune, color: Color(0xFF13EC13)),
            filled: true,
            fillColor: Colors.white,
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(15),
              borderSide: BorderSide.none,
            ),
            contentPadding: const EdgeInsets.symmetric(vertical: 0),
          ),
        ),
      ),
    );
  }

  Widget _buildCategoriesGrid() {
    return SliverToBoxAdapter(
      child: SizedBox(
        height: 100,
        child: categoriesList.isEmpty && !isLoading
            ? const Center(child: Text("Không tìm thấy danh mục", style: TextStyle(fontSize: 12, color: Colors.grey)))
            : ListView.builder(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16),
          itemCount: categoriesList.length,
          itemBuilder: (context, index) {
            final cat = categoriesList[index];
            return _categoryItem(cat['name'] ?? "N/A", Icons.apple);
          },
        ),
      ),
    );
  }

  Widget _categoryItem(String name, IconData icon) {
    return Padding(
      padding: const EdgeInsets.only(right: 20),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(15),
            decoration: BoxDecoration(
                color: const Color(0xFF13EC13).withOpacity(0.1),
                borderRadius: BorderRadius.circular(15)
            ),
            child: Icon(icon, color: const Color(0xFF13EC13)),
          ),
          const SizedBox(height: 5),
          Text(name, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildProductGrid() {
    if (products.isEmpty) {
      return SliverToBoxAdapter(
        child: Center(child: Padding(
          padding: const EdgeInsets.all(40.0),
          child: Column(
            children: [
              Icon(Icons.search_off, size: 60, color: Colors.grey[300]),
              const SizedBox(height: 10),
              const Text("Không tìm thấy sản phẩm nào!", style: TextStyle(color: Colors.grey)),
            ],
          ),
        )),
      );
    }
    return SliverPadding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      sliver: SliverGrid(
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 15,
          crossAxisSpacing: 15,
          childAspectRatio: 0.72,
        ),
        delegate: SliverChildBuilderDelegate(
              (context, index) {
            final product = products[index];
            return GestureDetector(
              onTap: () {
                // ĐÃ CẬP NHẬT: Truyền thêm userId sang ProductDetailsScreen
                Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) => ProductDetailsScreen(
                          product: product,
                          userId: currentUser?.id ?? 0,
                        )
                    )
                );
              },
              child: Container(
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: ClipRRect(
                        borderRadius: const BorderRadius.vertical(top: Radius.circular(20)),
                        child: Image.network(
                          "${ApiConfig.imageUrl}${product['image']}",
                          headers: const {"ngrok-skip-browser-warning": "69420"},
                          fit: BoxFit.cover,
                          width: double.infinity,
                          errorBuilder: (context, error, stackTrace) =>
                              Container(color: Colors.grey[100], child: const Icon(Icons.image, color: Colors.grey)),
                        ),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.all(12),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(product['name'] ?? "", style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14), maxLines: 1, overflow: TextOverflow.ellipsis),
                          const Text("Fresh Quality", style: TextStyle(color: Colors.grey, fontSize: 10)),
                          const SizedBox(height: 5),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text(
                                  FormatUtils.formatPrice(product['price']),
                                  style: const TextStyle(color: Color(0xFF13EC13), fontWeight: FontWeight.bold)
                              ),
                              GestureDetector(
                                onTap: () => _addToCart(product),
                                child: Container(
                                  padding: const EdgeInsets.all(4),
                                  decoration: const BoxDecoration(color: Color(0xFF13EC13), shape: BoxShape.circle),
                                  child: const Icon(Icons.add, color: Colors.white, size: 18),
                                ),
                              )
                            ],
                          )
                        ],
                      ),
                    )
                  ],
                ),
              ),
            );
          },
          childCount: products.length,
        ),
      ),
    );
  }

  Widget _buildTopHeader() {
    return SliverToBoxAdapter(
        child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                    const Text("Deliver to", style: TextStyle(color: Colors.grey, fontSize: 12)),
                    Row(children: const [Icon(Icons.location_on, color: Color(0xFF13EC13), size: 16), Text(" Home, City", style: TextStyle(fontWeight: FontWeight.bold)), Icon(Icons.expand_more, size: 16),])
                  ]),
                  Row(children: [
                    _circleIconButton(Icons.notifications_none),
                    const SizedBox(width: 8),
                    isLoggedIn ? _circleIconButton(Icons.logout, color: Colors.red, onTap: logout) : _circleIconButton(Icons.person_outline, onTap: goToLogin)
                  ])
                ]
            )
        )
    );
  }

  Widget _circleIconButton(IconData icon, {Color? color, VoidCallback? onTap}) {
    return InkWell(onTap: onTap, child: Container(padding: const EdgeInsets.all(8), decoration: BoxDecoration(color: Colors.white, shape: BoxShape.circle, border: Border.all(color: Colors.grey.shade200)), child: Icon(icon, size: 22, color: color)));
  }

  Widget _buildPromoBanners() {
    return SliverToBoxAdapter(
      child: Container(
        height: 190,
        padding: const EdgeInsets.symmetric(vertical: 8),
        child: ListView(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16),
          children: [
            _buildSingleBanner(
              title: "Xoài Keo Tươi Ngon",
              subtitle: "Giòn ngọt - Đậm vị",
              assetPath: "assets/images/xoaicat.jpg",
              gradientColors: [const Color(0xFFFFD700), const Color(0xFFFFA500)],
            ),
            const SizedBox(width: 15),
            _buildSingleBanner(
              title: "Ổi Xá Lị Thanh Mát",
              subtitle: "Giàu Vitamin C",
              assetPath: "assets/images/OIle.jpg",
              gradientColors: [const Color(0xFF32CD32), const Color(0xFF006400)],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSingleBanner({
    required String title,
    required String subtitle,
    required String assetPath,
    required List<Color> gradientColors,
  }) {
    final screenWidth = MediaQuery.of(context).size.width;
    final bannerWidth = screenWidth * 0.85;

    return Container(
      width: bannerWidth,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        image: DecorationImage(
          image: AssetImage(assetPath),
          fit: BoxFit.cover,
          colorFilter: ColorFilter.mode(Colors.black.withOpacity(0.35), BlendMode.darken),
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              title,
              style: const TextStyle(
                color: Colors.white,
                fontSize: 19,
                fontWeight: FontWeight.bold,
                shadows: [Shadow(color: Colors.black45, blurRadius: 4)],
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
            const SizedBox(height: 4),
            Text(
              subtitle,
              style: const TextStyle(color: Colors.white70, fontSize: 13),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
            const SizedBox(height: 12),
            SizedBox(
              height: 34,
              child: ElevatedButton(
                onPressed: () {
                  setState(() => _selectedIndex = 1);
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: gradientColors[1],
                  shape: const StadiumBorder(),
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  elevation: 0,
                ),
                child: const Text("Mua ngay", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSectionHeader(String title) {
    return SliverToBoxAdapter(child: Padding(padding: const EdgeInsets.fromLTRB(16, 20, 16, 10), child: Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)), if(!isSearching) GestureDetector(onTap: () => setState(() => _selectedIndex = 1), child: const Text("Xem tất cả", style: TextStyle(color: Color(0xFF13EC13), fontSize: 12)))])));
  }

  Widget _buildBottomNav() {
    return BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: (index) => setState(() => _selectedIndex = index),
        type: BottomNavigationBarType.fixed,
        selectedItemColor: const Color(0xFF13EC13),
        unselectedItemColor: Colors.grey,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.home_filled), label: "Home"),
          BottomNavigationBarItem(icon: Icon(Icons.category_outlined), label: "Categories"),
          BottomNavigationBarItem(icon: Icon(Icons.shopping_cart_outlined), label: "Cart"),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: "Profile")
        ]
    );
  }

  Widget _buildOtherPages() {
    switch (_selectedIndex) {
      case 1:
        return CategoriesScreen(userId: currentUser?.id ?? 0);
      case 2: return CartScreen(userId: currentUser?.id ?? 0);
      case 3: return ProfileScreen(currentUser: currentUser, onLogout: logout);
      default: return const Center(child: Text("Page Not Found"));
    }
  }
}