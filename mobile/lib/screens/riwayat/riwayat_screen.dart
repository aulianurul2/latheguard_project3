import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/operasional_provider.dart';

class RiwayatScreen extends StatefulWidget {
  const RiwayatScreen({super.key});

  @override
  State<RiwayatScreen> createState() => _RiwayatScreenState();
}

class _RiwayatScreenState extends State<RiwayatScreen> {
  String _filterJenis = 'Semua';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<OperasionalProvider>().loadData();
    });
  }

  void _bukaFormCatatTindakan(BuildContext context) {
    String selectedMesin = 'Mesin Bubut CNC-01 (Utama)';
    String selectedJenis = 'Servis Komponen';
    final itemCtrl = TextEditingController();
    final catatanCtrl = TextEditingController();
    final userName = context.read<AuthProvider>().user?.name ?? 'Teknisi';

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => Padding(
        padding: EdgeInsets.fromLTRB(
          16,
          20,
          16,
          MediaQuery.of(ctx).viewInsets.bottom + 20,
        ),
        child: StatefulBuilder(
          builder: (context, setModalState) => Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Text(
                'Catat Servis / Kerusakan Baru',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 14),
              DropdownButtonFormField<String>(
                initialValue: selectedMesin,
                decoration: const InputDecoration(
                  labelText: 'Pilih Mesin',
                  border: OutlineInputBorder(),
                ),
                items: const [
                  DropdownMenuItem(
                      value: 'Mesin Bubut CNC-01 (Utama)',
                      child: Text('Mesin Bubut CNC-01 (Utama)')),
                  DropdownMenuItem(
                      value: 'Mesin Bubut Manual-02',
                      child: Text('Mesin Bubut Manual-02')),
                  DropdownMenuItem(
                      value: 'Mesin Las Inverter',
                      child: Text('Mesin Las Inverter')),
                  DropdownMenuItem(
                      value: 'Mesin Gerinda Duduk',
                      child: Text('Mesin Gerinda Duduk')),
                ],
                onChanged: (v) => setModalState(() => selectedMesin = v!),
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                initialValue: selectedJenis,
                decoration: const InputDecoration(
                  labelText: 'Jenis Tindakan',
                  border: OutlineInputBorder(),
                ),
                items: const [
                  DropdownMenuItem(
                      value: 'Servis Komponen',
                      child: Text('Servis Komponen (Reset JKB)')),
                  DropdownMenuItem(
                      value: 'Perawatan Rutin',
                      child: Text('Perawatan Rutin')),
                  DropdownMenuItem(
                      value: 'Kerusakan',
                      child: Text('Laporan Kerusakan')),
                ],
                onChanged: (v) => setModalState(() => selectedJenis = v!),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: itemCtrl,
                decoration: const InputDecoration(
                  labelText: 'Nama Komponen / Bagian Mesin',
                  hintText: 'Contoh: Bearing Spindle Utama',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 12),
              TextField(
                controller: catatanCtrl,
                maxLines: 2,
                decoration: const InputDecoration(
                  labelText: 'Detail Tindakan / Kondisi Kerusakan',
                  border: OutlineInputBorder(),
                ),
              ),
              const SizedBox(height: 16),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF1E3A8A),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                ),
                onPressed: () async {
                  if (itemCtrl.text.trim().isEmpty) return;
                  await context.read<OperasionalProvider>().tambahRiwayatBaru(
                        namaMesin: selectedMesin,
                        jenisTindakan: selectedJenis,
                        item: itemCtrl.text.trim(),
                        catatan: catatanCtrl.text.trim(),
                        teknisiNama: userName,
                      );
                  if (!ctx.mounted) return;
                  Navigator.pop(ctx);
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Catatan tindakan berhasil disimpan.'),
                      backgroundColor: Colors.green,
                    ),
                  );
                },
                child: const Text('Simpan Catatan'),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Color _jenisColor(String jenis) {
    switch (jenis) {
      case 'Servis Komponen':
        return Colors.blue.shade800;
      case 'Kerusakan':
        return Colors.red.shade700;
      default:
        return Colors.teal.shade700;
    }
  }

  @override
  Widget build(BuildContext context) {
    final prov = context.watch<OperasionalProvider>();
    final filtered = _filterJenis == 'Semua'
        ? prov.riwayatList
        : prov.riwayatList
            .where((r) => r.jenisTindakan == _filterJenis)
            .toList();

    return Scaffold(
      floatingActionButton: FloatingActionButton.extended(
        backgroundColor: const Color(0xFF1E3A8A),
        foregroundColor: Colors.white,
        onPressed: () => _bukaFormCatatTindakan(context),
        icon: const Icon(Icons.add),
        label: const Text('Catat Tindakan'),
      ),
      body: Column(
        children: [
          // Filter chip jenis riwayat (KF-13)
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 4),
            child: Row(
              children: ['Semua', 'Servis Komponen', 'Perawatan Rutin', 'Kerusakan']
                  .map((jenis) => Padding(
                        padding: const EdgeInsets.only(right: 8),
                        child: ChoiceChip(
                          label: Text(jenis),
                          selected: _filterJenis == jenis,
                          onSelected: (_) =>
                              setState(() => _filterJenis = jenis),
                        ),
                      ))
                  .toList(),
            ),
          ),
          Expanded(
            child: ListView.builder(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 80),
              itemCount: filtered.length,
              itemBuilder: (context, index) {
                final item = filtered[index];
                final badgeColor = _jenisColor(item.jenisTindakan);

                return Card(
                  margin: const EdgeInsets.only(bottom: 12),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12)),
                  child: Padding(
                    padding: const EdgeInsets.all(14),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 8, vertical: 3),
                              decoration: BoxDecoration(
                                color: badgeColor.withValues(alpha: 0.12),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                item.jenisTindakan,
                                style: TextStyle(
                                    fontSize: 11,
                                    fontWeight: FontWeight.bold,
                                    color: badgeColor),
                              ),
                            ),
                            Text(
                              item.tanggal,
                              style: TextStyle(
                                  fontSize: 11, color: Colors.grey.shade600),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(
                          '${item.namaMesin} • ${item.komponenAtauItem}',
                          style: const TextStyle(
                              fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                        const SizedBox(height: 4),
                        Text(item.catatan,
                            style: const TextStyle(fontSize: 13)),
                        const SizedBox(height: 8),
                        Text(
                          'Oleh: ${item.teknisi}',
                          style: TextStyle(
                              fontSize: 11,
                              fontStyle: FontStyle.italic,
                              color: Colors.grey.shade700),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}