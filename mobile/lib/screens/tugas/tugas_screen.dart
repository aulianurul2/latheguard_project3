import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/operasional_model.dart';
import '../../providers/auth_provider.dart';
import '../../providers/operasional_provider.dart';

class TugasScreen extends StatefulWidget {
  const TugasScreen({super.key});

  @override
  State<TugasScreen> createState() => _TugasScreenState();
}

class _TugasScreenState extends State<TugasScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<OperasionalProvider>().loadData();
    });
  }

  void _konfirmasiSelesai(BuildContext context, TugasPerawatanModel tugas) {
    final catatanCtrl = TextEditingController();
    final userName = context.read<AuthProvider>().user?.name ?? 'Teknisi';

    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Catat Pelaksanaan Perawatan'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('${tugas.namaMesin} — ${tugas.judulTugas}',
                style: const TextStyle(fontWeight: FontWeight.bold)),
            const SizedBox(height: 12),
            TextField(
              controller: catatanCtrl,
              maxLines: 3,
              decoration: const InputDecoration(
                labelText: 'Catatan Hasil Pengecekan (Opsional)',
                border: OutlineInputBorder(),
              ),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: const Color(0xFF1E3A8A),
              foregroundColor: Colors.white,
            ),
            onPressed: () async {
              await context
                  .read<OperasionalProvider>()
                  .selesaikanTugas(tugas, userName, catatanCtrl.text.trim());
              if (!ctx.mounted) return;
              Navigator.pop(ctx);
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Tugas selesai dicatat & masuk ke Riwayat.'),
                  backgroundColor: Colors.green,
                ),
              );
            },
            child: const Text('Tandai Selesai'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final prov = context.watch<OperasionalProvider>();

    if (prov.isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    return RefreshIndicator(
      onRefresh: () => prov.loadData(),
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: prov.tugasList.length,
        itemBuilder: (context, index) {
          final tugas = prov.tugasList[index];
          final isLate = tugas.status == 'Terlambat';
          final isDue = tugas.status == 'Jatuh Tempo';
          final badgeColor = isLate
              ? Colors.red.shade700
              : (isDue ? Colors.orange.shade800 : Colors.teal);

          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text(
                        tugas.namaMesin,
                        style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: Colors.grey.shade700),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 3),
                        decoration: BoxDecoration(
                          color: badgeColor.withValues(alpha: 0.12),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          tugas.status,
                          style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: badgeColor),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(
                    tugas.judulTugas,
                    style: const TextStyle(
                        fontSize: 15, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 10),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.event,
                              size: 16, color: Colors.grey.shade600),
                          const SizedBox(width: 4),
                          Text(
                            'Jatuh tempo: ${tugas.jatuhTempo}',
                            style: TextStyle(
                                fontSize: 12, color: Colors.grey.shade700),
                          ),
                        ],
                      ),
                      ElevatedButton.icon(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xFF1E3A8A),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 6),
                        ),
                        icon: const Icon(Icons.check, size: 16),
                        label: const Text('Selesai',
                            style: TextStyle(fontSize: 12)),
                        onPressed: () => _konfirmasiSelesai(context, tugas),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}