class ApiConstants {
  static const String baseUrl = 'http://127.0.0.1:8000/api';

  // Auth Endpoints
  static const String login = '/login';
  static const String logout = '/logout';
  static const String profile = '/me';

  // Monitoring & Beranda Endpoints
  static const String mesins = '/mesins';
  static const String tugasPerawatan = '/tugas-perawatan';
  static const String notifikasis = '/notifikasis';
  static const String kategoris = '/kategoris';
  static const String komponens = '/komponens';
  static const String sensorData = '/sensor-data';
  static String sensorTerkini(int mesinId) => '/mesins/$mesinId/sensor-terkini';
}