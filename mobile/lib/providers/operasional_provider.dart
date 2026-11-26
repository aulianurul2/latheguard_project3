import 'package:flutter/material.dart';
import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../models/operasional_model.dart';

class OperasionalProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();

  bool _isLoading = false;
  List<TugasPerawatanModel> _tugasList = [];
  List<RiwayatModel> _riwayatList = [];

  bool get isLoading => _isLoading;
  List<TugasPerawatanModel> get tugasList => _tugasList;
  List<RiwayatModel> get riwayatList => _riwayatList;

  Future<void> loadData() async {
    _isLoading = true;
    notifyListeners();

    try {
      final resTugas = await _apiClient.dio.get(ApiConstants.tugasPerawatan);
      final List rawTugas = resTugas.data is List
          ? resTugas.data
          : (resTugas.data['data'] ?? []);

      if (rawTugas.isNotEmpty) {
        _tugasList =
            rawTugas.map((e) => TugasPerawatanModel.fromJson(e)).toList();
      } else {
        _loadDummyOperasional();
      }
    } catch (_) {
      _loadDummyOperasional();
    }

    _isLoading = false;
    notifyListeners();
  }

  void _loadDummyOperasional() {
    if (_tugasList.isEmpty) {
      _tugasList = [
        TugasPerawatanModel(
          id: 1,
          namaMesin: 'Mesin Gerinda Duduk',
          judulTugas: 'Penggantian Batu Gerinda & Cek Pelindung',
          jatuhTempo: '24 Nov 2026',
          status: 'Terlambat',
          intervalHari: 30,
        ),
        TugasPerawatanModel(
          id: 2,
          namaMesin: 'Mesin Las Inverter',
          judulTugas: 'Pembersihan Debu Internal & Cek Kabel Massa',
          jatuhTempo: '26 Nov 2026',
          status: 'Jatuh Tempo',
          intervalHari: 14,
        ),
        TugasPerawatanModel(
          id: 3,
          namaMesin: 'Mesin Milling & Bor',
          judulTugas: 'Pelumasan Meja Eretan & Cek Chuck Bor',
          jatuhTempo: '29 Nov 2026',
          status: 'Terjadwal',
          intervalHari: 14,
        ),
      ];
    }

    if (_riwayatList.isEmpty) {
      _riwayatList = [
        RiwayatModel(
          id: 1,
          tanggal: '22 Nov 2026, 14:30',
          namaMesin: 'Mesin Bubut CNC-01 (Utama)',
          jenisTindakan: 'Servis Komponen',
          komponenAtauItem: 'Oli Gearbox & Pelumas Eretan',
          catatan: 'Penggantian oli penuh & reset counter JKB komponen.',
          teknisi: 'Jaenal Arafat',
        ),
        RiwayatModel(
          id: 2,
          tanggal: '20 Nov 2026, 10:15',
          namaMesin: 'Mesin Bubut Manual-02',
          jenisTindakan: 'Kerusakan',
          komponenAtauItem: 'V-Belt & Pulley Penggerak',
          catatan: 'Belt mulai aus dan menimbulkan bunyi selip saat putaran tinggi.',
          teknisi: 'Teknisi Workshop',
        ),
        RiwayatModel(
          id: 3,
          tanggal: '18 Nov 2026, 09:00',
          namaMesin: 'Mesin Las Inverter',
          jenisTindakan: 'Perawatan Rutin',
          komponenAtauItem: 'Kipas Pendingin & Holder Elektroda',
          catatan: 'Perawatan rutin selesai tepat waktu.',
          teknisi: 'Teknisi Workshop',
        ),
      ];
    }
  }

  Future<void> selesaikanTugas(TugasPerawatanModel tugas, String teknisiNama, String catatan) async {
    try {
      await _apiClient.dio.post(
        '/tugas-perawatan/${tugas.id}/selesai',
        data: {'catatan': catatan},
      );
    } catch (_) {
      // Tetap lanjut update state lokal untuk mode simulasi
    }

    // Perbarui status tugas menjadi Terjadwal & tambahkan ke Riwayat
    final idx = _tugasList.indexWhere((t) => t.id == tugas.id);
    if (idx != -1) {
      _tugasList[idx] = TugasPerawatanModel(
        id: tugas.id,
        namaMesin: tugas.namaMesin,
        judulTugas: tugas.judulTugas,
        jatuhTempo: '${tugas.intervalHari} hari lagi',
        status: 'Terjadwal',
        intervalHari: tugas.intervalHari,
      );
    }

    _riwayatList.insert(
      0,
      RiwayatModel(
        id: DateTime.now().millisecondsSinceEpoch,
        tanggal: 'Hari ini (Baru saja)',
        namaMesin: tugas.namaMesin,
        jenisTindakan: 'Perawatan Rutin',
        komponenAtauItem: tugas.judulTugas,
        catatan: catatan.isEmpty ? 'Pelaksanaan perawatan rutin selesai.' : catatan,
        teknisi: teknisiNama,
      ),
    );
    notifyListeners();
  }

  Future<void> tambahRiwayatBaru({
    required String namaMesin,
    required String jenisTindakan,
    required String item,
    required String catatan,
    required String teknisiNama,
  }) async {
    try {
      await _apiClient.dio.post(
        '/riwayats',
        data: {
          'nama_mesin': namaMesin,
          'jenis_tindakan': jenisTindakan,
          'komponen': item,
          'catatan': catatan,
        },
      );
    } catch (_) {
      // Tetap lanjut simpan ke daftar lokal
    }

    _riwayatList.insert(
      0,
      RiwayatModel(
        id: DateTime.now().millisecondsSinceEpoch,
        tanggal: 'Hari ini (Baru saja)',
        namaMesin: namaMesin,
        jenisTindakan: jenisTindakan,
        komponenAtauItem: item,
        catatan: catatan,
        teknisi: teknisiNama,
      ),
    );
    notifyListeners();
  }
}