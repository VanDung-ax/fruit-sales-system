import 'package:intl/intl.dart';

class FormatUtils {
  /// Định dạng số thành tiền tệ Việt Nam (Ví dụ: 32000 -> 32.000)
  static String formatCurrency(dynamic price) {
    if (price == null) return "0";

    // Ép kiểu về double để xử lý chính xác
    final double value = double.tryParse(price.toString()) ?? 0;

    // Sử dụng NumberFormat với định dạng vi_VN để có dấu chấm phân cách
    final formatter = NumberFormat("#,###", "vi_VN");
    return formatter.format(value);
  }

  /// Nếu bạn muốn hiển thị kèm chữ đ hoặc VNĐ một cách đồng bộ
  static String formatPrice(dynamic price) {
    return "${formatCurrency(price)} đ";
  }
}