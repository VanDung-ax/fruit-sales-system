# AppBanTraiCây Backend

Hệ thống backend cho ứng dụng bán trái cây online. Được xây dựng bằng PHP với mô hình MVC (Model-View-Controller).

## 📋 Mô tả dự án

AppBanTraiCây Backend là API backend cung cấp các dịch vụ cho ứng dụng mobile bán trái cây. Hệ thống bao gồm quản lý người dùng, sản phẩm, giỏ hàng, đơn hàng, và các chức năng quản trị viên.

## 🏗️ Cấu trúc dự án

```
appbantraicay_backend/
├── api/                          # Endpoint API
│   ├── add_to_cart.php
│   ├── clear_cart.php
│   ├── get_cart.php
│   ├── get_categories.php
│   ├── get_last_order_info.php
│   ├── get_order_history.php
│   ├── get_products.php
│   ├── get_products_by_category.php
│   ├── get_user.php
│   ├── login.php
│   ├── place_order.php
│   ├── register.php
│   ├── search_products.php
│   ├── update_address.php
│   ├── update_cart.php
│   ├── update_profile.php
│   ├── validate_promo.php
│   └── admin/                    # Admin endpoints
├── config/                       # Cấu hình ứng dụng
│   └── database.php             # Cấu hình kết nối database
├── controller/                   # Controllers
│   ├── category_controller.php
│   ├── dashboard_controller.php
│   ├── order_controller.php
│   ├── products_controller.php
│   ├── promotion_controller.php
│   ├── payment_controller.php
│   ├── staff_controller.php
│   └── user_controller.php
├── model/                        # Models
│   ├── category_model.php
│   ├── dashboard_model.php
│   ├── order_model.php
│   ├── payment_model.php
│   ├── products_model.php
│   ├── promotion_model.php
│   ├── staff_model.php
│   └── user_model.php
├── images/                       # Thư mục lưu trữ hình ảnh
├── database.sql                  # Script tạo database
└── README.md                     # Tài liệu dự án
```

## 🚀 Yêu cầu hệ thống

- **PHP**: >= 7.4
- **MySQL**: >= 5.7
- **Web Server**: Apache hoặc Nginx
- **Composer** (tùy chọn)

### 2. Cấu hình Database

#### Bước 1: Tạo database

```bash
mysql -u root -p
CREATE DATABASE appbantraicay;
```

#### Bước 2: Import database schema

```bash
mysql -u root -p appbantraicay < database.sql
```

### 3. Cấu hình kết nối database

Chỉnh sửa file `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'appbantraicay');
```

### 4. Cấu hình thư mục

Đảm bảo thư mục `images/` có quyền ghi:

```bash
chmod 755 images/
```

## 📡 API Endpoints

### Authentication (Xác thực)

- `POST /api/register.php` - Đăng ký tài khoản mới
- `POST /api/login.php` - Đăng nhập

### User (Người dùng)

- `GET /api/get_user.php` - Lấy thông tin người dùng
- `POST /api/update_profile.php` - Cập nhật thông tin cá nhân
- `POST /api/update_address.php` - Cập nhật địa chỉ giao hàng

### Products (Sản phẩm)

- `GET /api/get_products.php` - Lấy danh sách tất cả sản phẩm
- `GET /api/get_categories.php` - Lấy danh sách danh mục
- `POST /api/get_products_by_category.php` - Lấy sản phẩm theo danh mục
- `POST /api/search_products.php` - Tìm kiếm sản phẩm

### Cart (Giỏ hàng)

- `POST /api/add_to_cart.php` - Thêm sản phẩm vào giỏ hàng
- `GET /api/get_cart.php` - Lấy giỏ hàng
- `POST /api/update_cart.php` - Cập nhật giỏ hàng
- `POST /api/clear_cart.php` - Xóa giỏ hàng

### Orders (Đơn hàng)

- `POST /api/place_order.php` - Đặt hàng
- `GET /api/get_order_history.php` - Lấy lịch sử đơn hàng
- `GET /api/get_last_order_info.php` - Lấy thông tin đơn hàng cuối cùng

### Promotions (Khuyến mãi)

- `POST /api/validate_promo.php` - Kiểm tra mã khuyến mãi

### Admin

- Các endpoint quản trị viên nằm trong thư mục `api/admin/`

## 💾 Database Schema

Dự án sử dụng các bảng chính:

- `users` - Người dùng
- `products` - Sản phẩm
- `categories` - Danh mục sản phẩm
- `cart` - Giỏ hàng
- `orders` - Đơn hàng
- `order_items` - Chi tiết đơn hàng
- `promotions` - Khuyến mãi
- `payments` - Thanh toán

## 🔧 Cấu trúc MVC

### Model

- Chứa logic xử lý dữ liệu và truy vấn database

### Controller

- Xử lý logic nghiệp vụ và điều phối giữa model và API

### API

- Endpoint để giao tiếp với frontend

## 🔐 Bảo mật

- Luôn kiểm tra và xác thực dữ liệu đầu vào
- Sử dụng prepared statements để phòng chống SQL injection
- Bảo vệ mật khẩu với hashing
- Sử dụng HTTPS trong production

## 🐛 Khắc phục sự cố

### Lỗi kết nối database

- Kiểm tra cấu hình trong `config/database.php`
- Đảm bảo MySQL service đang chạy
- Kiểm tra tên người dùng và mật khẩu database

### Lỗi upload hình ảnh

- Kiểm tra quyền của thư mục `images/`
- Đảm bảo đủ dung lượng ổ đĩa

### Lỗi 404 API

- Kiểm tra đường dẫn endpoint
- Đảm bảo file API tồn tại

## 📚 Tài liệu thêm

- Xem file `database.sql` cho schema database
- Xem các file controller và model để hiểu logic

**Cập nhật lần cuối:** April 2026
