class MesinModel {
  final int id;
  final String namaMesin;
  final String kodeMesin;
  final String tipeMesin; // 'bubut' atau 'pendukung'
  final String statusOperasional; // 'Off', 'Idle', 'Active', 'Offline'
  final String tingkatKondisi; // 'Normal', 'Waspada', 'Kritis', 'Belum Terkalibrasi'
  final double jkbHariIni;
  final double suhuTerkini;
  final double getaranTerkini;
  final String? jadwalTerdekat;
  final String? namaTugasTerdekat;
  final String terakhirUpdate;

  MesinModel({
    required this.id,
    required this.namaMesin,
    required this.kodeMesin,
    required this.tipeMesin,
    required this.statusOperasional,
    required this.tingkatKondisi,
    required this.jkbHariIni,
    required this.suhuTerkini,
    required this.getaranTerkini,
    this.jadwalTerdekat,
    this.namaTugasTerdekat,
    required this.terakhirUpdate,
  });

  factory MesinModel.fromJson(Map<String, dynamic> json) {
    return MesinModel(
      id: json['id'] ?? 0,
      namaMesin: json['nama_mesin'] ?? json['name'] ?? 'Mesin',
      kodeMesin: json['kode_mesin'] ?? 'MCH-01',
      tipeMesin: json['tipe_mesin'] ?? 'bubut',
      statusOperasional: json['status_operasional'] ?? 'Off',
      tingkatKondisi: json['tingkat_kondisi'] ?? 'Normal',
      jkbHariIni: (json['jkb_hari_ini'] ?? 0).toDouble(),
      suhuTerkini: (json['suhu_terkini'] ?? 0).toDouble(),
      getaranTerkini: (json['getaran_terkini'] ?? 0).toDouble(),
      jadwalTerdekat: json['jadwal_terdekat'],
      namaTugasTerdekat: json['nama_tugas_terdekat'],
      terakhirUpdate: json['terakhir_update'] ?? 'Baru saja',
    );
  }
}