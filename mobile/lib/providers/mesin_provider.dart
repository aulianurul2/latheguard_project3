import 'package:flutter/material.dart';
import '../core/constants/api_constants.dart';
import '../core/network/api_client.dart';
import '../models/detail_mesin_model.dart';

class MesinProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();

  bool _isLoading = false;
  String _selectedRange = '1 Jam'; // '1 Jam', '24 Jam', '7 Hari'
  List<double> _trenSuhu = [];
  List<double> _trenGetaran = [];
  List<KomponenModel> _komponenList = [];
  List<AnomaliModel> _anomaliList = [];

  bool get isLoading => _isLoading;
  String get selectedRange => _selectedRange;
  List<double> get trenSuhu => _trenSuhu;
  List<double> get trenGetaran => _trenGetaran;
  List<KomponenModel> get komponenList => _komponenList;
  List<AnomaliModel> get anomaliList => _anomaliList;

  void setRange(String range, int mesinId) {
    _selectedRange = range;
    loadDetailMesin(mesinId);
  }

  Future<void> loadDetailMesin(int mesinId) async {
    _isLoading = true;
    notifyListeners();

    try {
      final resKomponen = await _apiClient.dio.get(ApiConstants.komponens);
      final List rawKomp = resKomponen.data is List
          ? resKomponen.data
          : (resKomponen.data['data'] ?? []);

      if (rawKomp.isNotEmpty) {
        _komponenList = rawKomp.map((e) => KomponenModel.fromJson(e)).toList();
      } else {
        _loadDummyDetail(mesinId);
      }
    } catch (_) {
      _loadDummyDetail(mesinId);
    }

    _isLoading = false;
    notifyListeners();
  }

  void _loadDummyDetail(int mesinId) {
    // Simulasi data grafik sesuai rentang waktu yang dipilih
    if (_selectedRange == '1 Jam') {
      _trenSuhu = [40.2, 41.0, 41.8, 42.1, 42.5, 42.3, 42.5];
      _trenGetaran = [1.5, 1.6, 1.7, 1.9, 1.8, 1.7, 1.8];
    } else if (_selectedRange == '24 Jam') {
      _trenSuhu = [36.0, 38.5, 42.0, 45.2, 44.0, 41.5, 42.5];
      _trenGetaran = [1.2, 1.5, 1.8, 2.3, 2.0, 1.7, 1.8];
    } else {
      _trenSuhu = [39.0, 40.5, 41.2, 43.8, 44.5, 42.0, 42.5];
      _trenGetaran = [1.4, 1.6, 1.7, 2.1, 2.4, 1.9, 1.8];
    }

    _komponenList = [
      KomponenModel(
        id: 1,
        namaKomponen: 'Bearing Spindle Utama',
        jkbSaatIni: 465,
        intervalServis: 500,
        statusServis: 'Perlu Servis',
      ),
      KomponenModel(
        id: 2,
        namaKomponen: 'V-Belt & Pulley Penggerak',
        jkbSaatIni: 230,
        intervalServis: 300,
        statusServis: 'Mendekati Servis',
      ),
      KomponenModel(
        id: 3,
        namaKomponen: 'Oli Gearbox & Pelumas Eretan',
        jkbSaatIni: 95,
        intervalServis: 250,
        statusServis: 'Normal',
      ),
      KomponenModel(
        id: 4,
        namaKomponen: 'Pompa & Sirkulasi Coolant',
        jkbSaatIni: 60,
        intervalServis: 200,
        statusServis: 'Normal',
      ),
    ];

    _anomaliList = mesinId == 2
        ? [
            AnomaliModel(
              id: 1,
              waktu: 'Hari ini, 10:15 WIB',
              parameter: 'Getaran Spindle (3.4 mm/s)',
              nilaiTerbaca: 'Melebihi ambang baseline (2.5 mm/s)',
              tingkatKeparahan: 'Waspada',
              saranPemeriksaan:
                  'Periksa kekencangan baut chuck, keseimbangan benda kerja, dan pelumasan bearing spindle.',
            ),
          ]
        : [
            AnomaliModel(
              id: 2,
              waktu: 'Kemarin, 15:40 WIB',
              parameter: 'Suhu Motor (48.2 °C)',
              nilaiTerbaca: 'Fluktuasi ringan saat beban pemakanan berat',
              tingkatKeparahan: 'Waspada',
              saranPemeriksaan:
                  'Pastikan ventilasi motor bersih dari serpihan gram besi.',
            ),
          ];
  }
}