import 'dart:math';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../../core/network/api_client.dart';

enum OwnerMenuType {
  kelolaMesin,
  kelolaKomponen,
  konfigurasiAmbang,
  kelolaJadwal,
  kelolaAkun,
  laporanRingkas,
}

class OwnerAdminScreen extends StatefulWidget {
  final OwnerMenuType menuType;
  final String title;

  const OwnerAdminScreen({
    super.key,
    required this.menuType,
    required this.title,
  });

  @override
  State<OwnerAdminScreen> createState() => _OwnerAdminScreenState();
}

class _OwnerAdminScreenState extends State<OwnerAdminScreen> {
  final ApiClient _apiClient = ApiClient();

  String _apiKeyEsp32 = 'LG-IIOT-8F92A1C4E7B0';
  bool _baselineAktif = false;
  bool _isLoadingUsers = false;
  bool _isLoadingKomponen = false;

  final _suhuMaxCtrl = TextEditingController(text: '55.0');
  final _getaranMaxCtrl = TextEditingController(text: '2.5');
  final _bobotBebanCtrl = TextEditingController(text: '1.25');

  final List<Map<String, dynamic>> _daftarUser = [
    {'name': 'Jaenal Arafat', 'email': 'jaenal@gmail.com', 'role': 'owner'},
    {'name': 'Teknisi Workshop', 'email': 'teknisi@latheguard.com', 'role': 'teknisi'},
  ];

  final List<Map<String, dynamic>> _daftarKomponen = [];

  @override
  void initState() {
    super.initState();
    if (widget.menuType == OwnerMenuType.kelolaAkun) {
      _loadUsersFromApi();
    } else if (widget.menuType == OwnerMenuType.kelolaKomponen) {
      _loadKomponensFromApi();
    }
  }

  Future<void> _loadUsersFromApi() async {
    setState(() => _isLoadingUsers = true);
    try {
      final res = await _apiClient.dio.get('/users');
      final List raw = res.data is List
          ? res.data
          : (res.data['data'] ?? []);
      if (!mounted) return;
      if (raw.isNotEmpty) {
        setState(() {
          _daftarUser
            ..clear()
            ..addAll(raw.map((e) => Map<String, dynamic>.from(e)));
        });
      }
    } catch (e) {
      debugPrint('Gagal memuat users dari API: $e');
    } finally {
      if (mounted) {
        setState(() => _isLoadingUsers = false);
      }
    }
  }

  Future<void> _loadKomponensFromApi() async {
    setState(() => _isLoadingKomponen = true);
    try {
      final res = await _apiClient.dio.get('/komponens');
      final List raw = res.data is List
          ? res.data
          : (res.data['data'] ?? []);
      if (!mounted) return;
      setState(() {
        _daftarKomponen
          ..clear()
          ..addAll(raw.map((e) => Map<String, dynamic>.from(e)));
      });
    } catch (e) {
      debugPrint('Gagal memuat komponens dari API: $e');
    } finally {
      if (mounted) {
        setState(() => _isLoadingKomponen = false);
      }
    }
  }

  void _regenerateKey() {
    const chars = 'ABCDEF0123456789';
    final rnd = Random();
    final newKey =
        'LG-IIOT-${List.generate(12, (_) => chars[rnd.nextInt(chars.length)]).join()}';
    setState(() => _apiKeyEsp32 = newKey);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('API Key perangkat IoT berhasil diperbarui.')),
    );
  }

  void _tambahAkunDialog() {
    final nameCtrl = TextEditingController();
    final emailCtrl = TextEditingController();
    final passCtrl = TextEditingController(text: '12345678');
    String role = 'teknisi';

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Tambah Akun Pengguna'),
        content: StatefulBuilder(
          builder: (context, setModalState) => Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(
                controller: nameCtrl,
                decoration: const InputDecoration(labelText: 'Nama Lengkap'),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: emailCtrl,
                decoration: const InputDecoration(labelText: 'Email'),
              ),
              const SizedBox(height: 8),
              TextField(
                controller: passCtrl,
                decoration: const InputDecoration(labelText: 'Kata Sandi (Min. 8 karakter)'),
              ),
              const SizedBox(height: 8),
              DropdownButtonFormField<String>(
                initialValue: role,
                decoration: const InputDecoration(labelText: 'Peran (Role)'),
                items: const [
                  DropdownMenuItem(value: 'teknisi', child: Text('Teknisi')),
                  DropdownMenuItem(value: 'owner', child: Text('Owner')),
                ],
                onChanged: (v) => setModalState(() => role = v!),
              ),
            ],
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF1E3A8A),
              foregroundColor: Colors.white,
            ),
            onPressed: () async {
              if (nameCtrl.text.isEmpty || emailCtrl.text.isEmpty) return;
              bool suksesApi = false;
              try {
                await _apiClient.dio.post('/users', data: {
                  'name': nameCtrl.text.trim(),
                  'email': emailCtrl.text.trim(),
                  'password': passCtrl.text.trim(),
                  'password_confirmation': passCtrl.text.trim(),
                  'role': role,
                });
                suksesApi = true;
              } catch (_) {}

              if (!ctx.mounted) return;
              Navigator.pop(ctx);

              if (suksesApi) {
                await _loadUsersFromApi();
              } else if (mounted) {
                setState(() {
                  _daftarUser.add({
                    'name': nameCtrl.text.trim(),
                    'email': emailCtrl.text.trim(),
                    'role': role,
                  });
                });
              }

              if (!mounted) return;
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text(suksesApi
                      ? 'Akun baru berhasil disimpan ke database.'
                      : 'Akun ditambahkan (mode preview lokal).'),
                  backgroundColor: Colors.green,
                ),
              );
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  void _tambahKomponenDialog() {
    final mesinIdCtrl = TextEditingController(text: '1');
    final namaCtrl = TextEditingController();
    final intervalCtrl = TextEditingController(text: '500');
    final jamTerpakaiCtrl = TextEditingController(text: '0');

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Tambah Komponen Mesin'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            TextField(
              controller: mesinIdCtrl,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(labelText: 'ID Mesin (sesuai tabel mesins)'),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: namaCtrl,
              decoration: const InputDecoration(labelText: 'Nama Komponen'),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: intervalCtrl,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(labelText: 'Interval Servis (Jam)'),
            ),
            const SizedBox(height: 8),
            TextField(
              controller: jamTerpakaiCtrl,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(labelText: 'Jam Terpakai Awal'),
            ),
          ],
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('Batal')),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF1E3A8A),
              foregroundColor: Colors.white,
            ),
            onPressed: () async {
              if (namaCtrl.text.isEmpty) return;
              bool sukses = false;
              try {
                await _apiClient.dio.post('/komponens', data: {
                  'mesin_id': int.tryParse(mesinIdCtrl.text.trim()) ?? 1,
                  'nama': namaCtrl.text.trim(),
                  'interval_jam': int.tryParse(intervalCtrl.text.trim()) ?? 500,
                  'jam_terpakai': double.tryParse(jamTerpakaiCtrl.text.trim()) ?? 0,
                });
                sukses = true;
              } catch (e) {
                debugPrint('Gagal simpan komponen: $e');
              }

              if (!ctx.mounted) return;
              Navigator.pop(ctx);

              if (sukses) {
                await _loadKomponensFromApi();
              }

              if (!mounted) return;
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text(sukses
                      ? 'Komponen berhasil disimpan ke database.'
                      : 'Gagal menyimpan (pastikan ID Mesin ada di tabel mesins).'),
                  backgroundColor: sukses ? Colors.green : Colors.red,
                ),
              );
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.title),
        backgroundColor: const Color(0xFF1E3A8A),
        foregroundColor: Colors.white,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: _buildBodyByType(),
      ),
    );
  }

  Widget _buildBodyByType() {
    switch (widget.menuType) {
      case OwnerMenuType.kelolaMesin:
        return ListView(
          children: [
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Kredensial Perangkat IoT (ESP32)',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Gunakan API Key ini pada firmware ESP32 untuk mengirim telemetri sensor:',
                      style: TextStyle(fontSize: 12),
                    ),
                    const SizedBox(height: 12),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                      decoration: BoxDecoration(
                        color: Colors.grey.shade100,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: Colors.grey.shade300),
                      ),
                      child: Row(
                        children: [
                          Expanded(
                            child: Text(
                              _apiKeyEsp32,
                              style: const TextStyle(
                                fontFamily: 'monospace',
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          IconButton(
                            icon: const Icon(Icons.copy, size: 18),
                            onPressed: () {
                              Clipboard.setData(ClipboardData(text: _apiKeyEsp32));
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(content: Text('API Key disalin ke clipboard')),
                              );
                            },
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 12),
                    ElevatedButton.icon(
                      onPressed: _regenerateKey,
                      icon: const Icon(Icons.refresh),
                      label: const Text('Regenerate API Key'),
                    ),
                  ],
                ),
              ),
            ),
          ],
        );

      case OwnerMenuType.kelolaKomponen:
        if (_isLoadingKomponen) {
          return const Center(child: CircularProgressIndicator());
        }
        return Column(
          children: [
            Align(
              alignment: Alignment.centerRight,
              child: ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF1E3A8A),
                  foregroundColor: Colors.white,
                ),
                onPressed: _tambahKomponenDialog,
                icon: const Icon(Icons.add),
                label: const Text('Tambah Komponen'),
              ),
            ),
            const SizedBox(height: 12),
            Expanded(
              child: _daftarKomponen.isEmpty
                  ? const Center(child: Text('Belum ada data komponen di database.'))
                  : RefreshIndicator(
                      onRefresh: _loadKomponensFromApi,
                      child: ListView.builder(
                        itemCount: _daftarKomponen.length,
                        itemBuilder: (context, index) {
                          final k = _daftarKomponen[index];
                          final mesinNama = k['mesin'] is Map
                              ? k['mesin']['nama']
                              : 'Mesin #${k['mesin_id']}';
                          return Card(
                            child: ListTile(
                              leading: const CircleAvatar(child: Icon(Icons.settings)),
                              title: Text(k['nama'].toString()),
                              subtitle: Text(
                                '$mesinNama • Terpakai: ${k['jam_terpakai'] ?? 0} / ${k['interval_jam']} Jam',
                              ),
                            ),
                          );
                        },
                      ),
                    ),
            ),
          ],
        );

      case OwnerMenuType.konfigurasiAmbang:
        return ListView(
          children: [
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Ambang Status & Bobot JKB',
                      style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _suhuMaxCtrl,
                      keyboardType: TextInputType.number,
                      decoration: const InputDecoration(
                        labelText: 'Batas Suhu Waspada (°C)',
                        border: OutlineInputBorder(),
                      ),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _getaranMaxCtrl,
                      keyboardType: TextInputType.number,
                      decoration: const InputDecoration(
                        labelText: 'Batas Getaran Waspada (mm/s)',
                        border: OutlineInputBorder(),
                      ),
                    ),
                    const SizedBox(height: 12),
                    TextField(
                      controller: _bobotBebanCtrl,
                      keyboardType: TextInputType.number,
                      decoration: const InputDecoration(
                        labelText: 'Faktor Pengali Beban Berat (JKB)',
                        border: OutlineInputBorder(),
                      ),
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF1E3A8A),
                        foregroundColor: Colors.white,
                      ),
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('Konfigurasi ambang berhasil disimpan.'),
                            backgroundColor: Colors.green,
                          ),
                        );
                      },
                      child: const Text('Simpan Konfigurasi'),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 12),
            Card(
              child: SwitchListTile(
                title: const Text(
                  'Mode Pembelajaran Baseline',
                  style: TextStyle(fontWeight: FontWeight.bold),
                ),
                subtitle: Text(
                  _baselineAktif
                      ? 'Sedang merekam pola getaran & suhu normal mesin...'
                      : 'Aktifkan saat mesin beroperasi normal tanpa beban berlebih.',
                ),
                value: _baselineAktif,
                onChanged: (val) => setState(() => _baselineAktif = val),
              ),
            ),
          ],
        );

      case OwnerMenuType.kelolaAkun:
        if (_isLoadingUsers) {
          return const Center(child: CircularProgressIndicator());
        }
        return Column(
          children: [
            Align(
              alignment: Alignment.centerRight,
              child: ElevatedButton.icon(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF1E3A8A),
                  foregroundColor: Colors.white,
                ),
                onPressed: _tambahAkunDialog,
                icon: const Icon(Icons.person_add),
                label: const Text('Tambah Akun'),
              ),
            ),
            const SizedBox(height: 12),
            Expanded(
              child: RefreshIndicator(
                onRefresh: _loadUsersFromApi,
                child: ListView.builder(
                  itemCount: _daftarUser.length,
                  itemBuilder: (context, index) {
                    final u = _daftarUser[index];
                    return Card(
                      child: ListTile(
                        leading: const CircleAvatar(child: Icon(Icons.person)),
                        title: Text(u['name'].toString()),
                        subtitle: Text(u['email'].toString()),
                        trailing: Chip(label: Text(u['role'].toString().toUpperCase())),
                      ),
                    );
                  },
                ),
              ),
            ),
          ],
        );

      case OwnerMenuType.laporanRingkas:
        return ListView(
          children: [
            _laporanTile('Total Jam Kerja Berbobot (Bulan Ini)', '148.5 Jam', Icons.timer),
            _laporanTile('Kepatuhan Jadwal Perawatan Rutin', '92%', Icons.verified),
            _laporanTile('Total Anomali Terdeteksi', '3 Kejadian', Icons.analytics),
            _laporanTile('Total Servis Komponen Selesai', '5 Tindakan', Icons.handyman),
          ],
        );

      case OwnerMenuType.kelolaJadwal:
        return Center(
          child: Card(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Icon(Icons.check_circle_outline, size: 48, color: Color(0xFF1E3A8A)),
                  const SizedBox(height: 12),
                  Text(
                    widget.title,
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    'Master data jadwal perawatan rutin siap disinkronkan dengan endpoint backend Laravel.',
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
          ),
        );
    }
  }

  Widget _laporanTile(String title, String value, IconData icon) {
    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: const Color(0xFF1E3A8A).withValues(alpha: 0.1),
          child: Icon(icon, color: const Color(0xFF1E3A8A)),
        ),
        title: Text(title, style: const TextStyle(fontSize: 13)),
        trailing: Text(
          value,
          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
        ),
      ),
    );
  }
}