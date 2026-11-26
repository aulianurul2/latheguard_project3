import 'package:flutter/material.dart';
import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../models/mesin_model.dart';

class DashboardProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();

  bool _isLoading = false;
  bool _isUsingDummy = false;
  List<MesinModel> _mesinBubutList = [];
  List<MesinModel> _mesinPendukungList = [];
  int _tugasJatuhTempo = 0;
  int _tugasTerlambat = 0;

  bool get isLoading => _isLoading;
  bool get isUsingDummy => _isUsingDummy;
  List<MesinModel> get mesinBubutList => _mesinBubutList;
  List<MesinModel> get mesinPendukungList => _mesinPendukungList;
  int get tugasJatuhTempo => _tugasJatuhTempo;
  int get tugasTerlambat => _tugasTerlambat;

  Future<void> loadDashboardData() async {
    _isLoading = true;
    notifyListeners();

    try {
      final resMesin = await _apiClient.dio.get(ApiConstants.mesins);
      final List rawMesin = resMesin.data is List
          ? resMesin.data
          : (resMesin.data['data'] ?? []);

      if (rawMesin.isNotEmpty) {
        final allMesin = rawMesin.map((e) => MesinModel.fromJson(e)).toList();
        _mesinBubutList =
            allMesin.where((m) => m.tipeMesin == 'bubut').toList();
        _mesinPendukungList =
            allMesin.where((m) => m.tipeMesin != 'bubut').toList();
        _isUsingDummy = false;
      } else {
        _loadDummyData();
      }
    } catch (_) {
      // Fallback otomatis jika MesinController di Laravel belum diisi
      _loadDummyData();
    }

    _isLoading = false;
    notifyListeners();
  }

  void _loadDummyData() {
    _isUsingDummy = true;
    _tugasJatuhTempo = 2;
    _tugasTerlambat = 1;

    _mesinBubutList = [
      MesinModel(
        id: 1,
        namaMesin: 'Mesin Bubut CNC-01 (Utama)',
        kodeMesin: 'LTH-01',
        tipeMesin: 'bubut',
        statusOperasional: 'Active',
        tingkatKondisi: 'Normal',
        jkbHariIni: 5.4,
        suhuTerkini: 42.5,
        getaranTerkini: 1.8,
        terakhirUpdate: '10 detik lalu',
      ),
      MesinModel(
        id: 2,
        namaMesin: 'Mesin Bubut Manual-02',
        kodeMesin: 'LTH-02',
        tipeMesin: 'bubut',
        statusOperasional: 'Idle',
        tingkatKondisi: 'Waspada',
        jkbHariIni: 3.2,
        suhuTerkini: 51.0,
        getaranTerkini: 3.4,
        terakhirUpdate: '1 menit lalu',
      ),
    ];

    _mesinPendukungList = [
      MesinModel(
        id: 3,
        namaMesin: 'Mesin Las Inverter',
        kodeMesin: 'SUP-01',
        tipeMesin: 'pendukung',
        statusOperasional: 'Off',
        tingkatKondisi: 'Normal',
        jkbHariIni: 0,
        suhuTerkini: 0,
        getaranTerkini: 0,
        namaTugasTerdekat: 'Cek Kabel & Kipas Pendingin',
        jadwalTerdekat: 'Besok, 09:00',
        terakhirUpdate: '-',
      ),
      MesinModel(
        id: 4,
        namaMesin: 'Mesin Gerinda Duduk',
        kodeMesin: 'SUP-02',
        tipeMesin: 'pendukung',
        statusOperasional: 'Off',
        tingkatKondisi: 'Normal',
        jkbHariIni: 0,
        suhuTerkini: 0,
        getaranTerkini: 0,
        namaTugasTerdekat: 'Penggantian Batu Gerinda',
        jadwalTerdekat: 'Terlambat 2 hari',
        terakhirUpdate: '-',
      ),
      MesinModel(
        id: 5,
        namaMesin: 'Mesin Milling & Bor',
        kodeMesin: 'SUP-03',
        tipeMesin: 'pendukung',
        statusOperasional: 'Off',
        tingkatKondisi: 'Normal',
        jkbHariIni: 0,
        suhuTerkini: 0,
        getaranTerkini: 0,
        namaTugasTerdekat: 'Pelumasan Meja Eretan',
        jadwalTerdekat: '3 hari lagi',
        terakhirUpdate: '-',
      ),
    ];
  }
}