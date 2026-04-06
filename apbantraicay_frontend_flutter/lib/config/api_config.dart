class ApiConfig {

  // ===== CHỌN MÔI TRƯỜNG =====
  static const bool useNgrok = true;

  // ===== LOCAL SERVER (IP laptop) =====
  static const String localIp = "10.226.170.55";

  // ===== NGROK URL =====0
  static const String ngrokUrl = "https://tomika-heavyset-alberto.ngrok-free.dev";

  // ===== ROOT URL =====
  static String get rootUrl =>
      useNgrok
          ? "$ngrokUrl/appbantraicay/"
          : "http://$localIp/appbantraicay/";

  // ===== API =====
  static String get baseUrl => "${rootUrl}api";

  // ===== IMAGE =====
  static String get imageUrl => "${rootUrl}images/";
}