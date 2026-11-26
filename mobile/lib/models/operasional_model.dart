class TugasPerawatanModel {
  final int id;
  final String namaMesin;
  final String judulTugas;
  final String jatuhTempo;
  final String status; // 'Terlambat', 'Jatuh Tempo', 'Terjadwal'
  final int intervalHari;

  TugasPerawatanModel({
    required this.id,
    required this.namaMesin,
    required this.judulTugas,
    required this.jatuhTempo,
    required this.status,
    required this.intervalHari,
  });

  factory TugasPerawatanModel.fromJson(Map<String, dynamic> json) {
    return TugasPerawatanModel(
      id: json['id'] ?? 0,
      namaMesin: json['nama_mesin'] ?? 'Mesin Pendukung',
      judulTugas: json['judul_tugas'] ?? json['nama_tugas'] ?? 'Perawatan Rutin',
      jatuhTempo: json['jatuh_tempo'] ?? '-',
      status: json['status'] ?? 'Terjadwal',
      intervalHari: json['interval_hari'] ?? 30,
    );
  }
}

class RiwayatModel {
  final int id;
  final String tanggal;
  final String namaMesin;
  final String jenisTindakan; // 'Servis Komponen', 'Perawatan Rutin', 'Kerusakan'
  final String komponenAtauItem;
  final String catatan;
  final String teknisi;

  RiwayatModel({
    required this.id,
    required this.tanggal,
    required this.namaMesin,
    required this.jenisTindakan,
    required this.komponenAtauItem,
    required this.catatan,
    required this.teknisi,
  });

  factory RiwayatModel.fromJson(Map<String, dynamic> json) {
    return RiwayatModel(
      id: json['id'] ?? 0,
      tanggal: json['tanggal'] ?? json['created_at'] ?? '-',
      namaMesin: json['nama_mesin'] ?? 'Mesin',
      jenisTindakan: json['jenis_tindakan'] ?? 'Servis Komponen',
      komponenAtauItem: json['komponen'] ?? json['item'] ?? '-',
      catatan: json['catatan'] ?? '-',
      teknisi: json['teknisi'] ?? 'Teknisi',
    );
  }
}