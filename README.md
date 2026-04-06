# 🍎 Fruitly - Hệ Thống Bán Trái Cây Toàn Diện

**Fruitly** là một giải pháp thương mại điện tử hoàn chỉnh cho cửa hàng trái cây, kết hợp ứng dụng di động (Flutter) cho người dùng và hệ thống quản trị mạnh mẽ (PHP Admin).

## 📁 Cấu Trúc Dự Án

```
fruit-sales-system/
├── apbantraicay_frontend_flutter/    # Ứng dụng di động (Flutter)
│   ├── lib/                          # Source code chính
│   ├── android/                      # Cấu hình Android
│   ├── ios/                          # Cấu hình iOS
│   ├── web/                          # Cấu hình Web
│   ├── windows/                      # Cấu hình Windows
│   ├── linux/                        # Cấu hình Linux
│   ├── macos/                        # Cấu hình macOS
│   ├── test/                         # Unit tests
│   ├── assets/                       # Hình ảnh, font, tài nguyên
│   ├── pubspec.yaml                  # Quản lý dependencies (Dart/Flutter)
│   └── README.md                     # Tài liệu chi tiết Frontend
│
├── appbantraicay_backend/            # API Backend (PHP)
│   ├── api/                          # Endpoints API
│   ├── controller/                   # Business logic
│   ├── model/                        # Database models
│   ├── config/                       # Cấu hình ứng dụng
│   ├── images/                       # Thư mục upload hình ảnh
│   ├── database.sql                  # SQL script tạo database
│   └── README.md                     # Tài liệu chi tiết Backend
│
└── README.md                         # Tài liệu tổng quát (file này)
```

## 🎯 Tính Năng Chính

### 📱 Frontend (Flutter Mobile App)

- ✅ Xác thực người dùng (Đăng nhập, Đăng ký)
- ✅ Duyệt sản phẩm theo danh mục
- ✅ Tìm kiếm nâng cao
- ✅ Chi tiết sản phẩm với hình ảnh
- ✅ Giỏ hàng toàn diện
- ✅ Áp dụng mã khuyến mãi/Voucher
- ✅ Thanh toán (COD, Ví điện tử)
- ✅ Lịch sử đơn hàng
- ✅ Quản lý địa chỉ giao hàng
- ✅ Hệ thống thành viên
- ✅ Hồ sơ cá nhân

### 🛡️ Backend (PHP Admin Panel)

- ✅ Dashboard thống kê doanh thu
- ✅ Quản lý sản phẩm (CRUD)
- ✅ Quản lý danh mục
- ✅ Quản lý đơn hàng
- ✅ Quản lý khuyến mãi & Voucher
- ✅ Quản lý người dùng
- ✅ Quản lý nhân viên & Phân quyền
- ✅ Xử lý thanh toán
- ✅ Quản lý kho hàng

## 🛠️ Công Nghệ Sử Dụng

### Frontend

- **Flutter** - Framework xây dựng ứng dụng multi-platform
- **Dart** - Ngôn ngữ lập trình
- **Shared Preferences** - Lưu trữ dữ liệu cục bộ
- **HTTP Client** - Giao tiếp API

### Backend

- **PHP 7.4+** - Ngôn ngữ lập trình server
- **MySQL 5.7+** - Database
- **Apache/Nginx** - Web server
- **MVC Pattern** - Mô hình thiết kế

### Tools & Infrastructure

- **ngrok** - Tunneling cho local development
- **Laragon** - LAMP stack (Linux/Apache/MySQL/PHP)
- **Git** - Version control
- **Android Studio** - IDE cho Android
- **VS Code** - Code editor

## 📋 Yêu Cầu Hệ Thống

### Để chạy Backend

- **PHP**: >= 7.4
- **MySQL**: >= 5.7
- **Web Server**: Apache hoặc Nginx
- **Composer** (tùy chọn)

### Để chạy Frontend

- **Flutter SDK**: >= 3.0
- **Dart SDK**: Kèm theo Flutter
- **Android SDK** (cho Android)
