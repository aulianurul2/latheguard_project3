class KomponenModel {
  final int id;
  final int mesinId;
  final String namaMesin;
  final String namaKategori;
  final String namaKomponen;
  final double jkbSaatIni;
  final double intervalServis;
  final String statusServis;

  KomponenModel({
    required this.id,
    this.mesinId = 0,
    this.namaMesin = '-',
    this.namaKategori = '-',
    required this.namaKomponen,
    required this.jkbSaatIni,
    required this.intervalServis,
    required this.statusServis,
  });

  double get persentase =>
      intervalServis > 0 ? (jkbSaatIni / intervalServis).clamp(0.0, 1.0) : 0.0;

  factory KomponenModel.fromJson(Map<String, dynamic> json) {
    final double jkb =
        double.tryParse((json['jam_terpakai'] ?? json['jkb_saat_ini'] ?? 0).toString()) ?? 0.0;
    final double interval =
        double.tryParse((json['interval_jam'] ?? json['interval_servis'] ?? 100).toString()) ?? 100.0;
    final double ratio = interval > 0 ? (jkb / interval) : 0.0;

    final String status = json['status_servis'] ??
        (ratio >= 0.9
            ? 'Perlu Servis'
            : (ratio >= 0.75 ? 'Mendekati Servis' : 'Normal'));

    final mesinObj = json['mesin'];
    final kategoriObj = json['kategori'];

    return KomponenModel(
      id: json['id'] ?? 0,
      mesinId: json['mesin_id'] ?? 0,
      namaMesin: mesinObj is Map ? (mesinObj['nama'] ?? '-') : '-',
      namaKategori: kategoriObj is Map ? (kategoriObj['nama'] ?? '-') : '-',
      namaKomponen: json['nama'] ?? json['nama_komponen'] ?? 'Komponen',
      jkbSaatIni: jkb,
      intervalServis: interval,
      statusServis: status,
    );
  }
}

class AnomaliModel {
  final int id;
  final String waktu;
  final String parameter;
  final String nilaiTerbaca;
  final String tingkatKeparahan;
  final String saranPemeriksaan;

  AnomaliModel({
    required this.id,
    required this.waktu,
    required this.parameter,
    required this.nilaiTerbaca,
    required this.tingkatKeparahan,
    required this.saranPemeriksaan,
  });

  factory AnomaliModel.fromJson(Map<String, dynamic> json) {
    return AnomaliModel(
      id: json['id'] ?? 0,
      waktu: json['waktu'] ?? json['created_at'] ?? '-',
      parameter: json['parameter'] ?? 'Getaran / Suhu',
      nilaiTerbaca: json['nilai_terbaca'] ?? '-',
      tingkatKeparahan: json['tingkat_keparahan'] ?? 'Waspada',
      saranPemeriksaan:
          json['saran_pemeriksaan'] ?? 'Periksa kondisi fisik komponen.',
    );
  }
}