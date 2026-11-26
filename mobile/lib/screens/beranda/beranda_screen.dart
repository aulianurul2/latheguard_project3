import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/mesin_model.dart';
import '../../providers/auth_provider.dart';
import '../../providers/dashboard_provider.dart';

class BerandaScreen extends StatefulWidget {
  const BerandaScreen({super.key});

  @override
  State<BerandaScreen> createState() => _BerandaScreenState();
}

class _BerandaScreenState extends State<BerandaScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<DashboardProvider>().loadDashboardData();
    });
  }

  Color _statusColor(String status) {
    switch (status.toLowerCase()) {
      case 'active':
        return Colors.green;
      case 'idle':
        return Colors.amber.shade700;
      case 'offline':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }

  Color _kondisiColor(String kondisi) {
    switch (kondisi.toLowerCase()) {
      case 'normal':
        return Colors.teal;
      case 'waspada':
        return Colors.orange.shade800;
      case 'kritis':
        return Colors.red.shade700;
      default:
        return Colors.blueGrey;
    }
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;
    final dashboard = context.watch<DashboardProvider>();

    return Scaffold(
      backgroundColor: const Color(0xFFF4F6F9),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1A3A75),
        foregroundColor: Colors.white,
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'LatheGuard-IIoT',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            Text(
              'Halo, ${user?.name ?? 'Pengguna'} (${user?.role.toUpperCase() ?? '-'})',
              style: const TextStyle(fontSize: 12, color: Colors.white70),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.notifications_outlined),
            tooltip: 'Notifikasi Peringatan',
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(content: Text('Membuka daftar notifikasi...')),
              );
            },
          ),
        ],
      ),
      body: dashboard.isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: () => dashboard.loadDashboardData(),
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  if (dashboard.isUsingDummy)
                    Container(
                      margin: const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.symmetric(
                          horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(
                        color: Colors.amber.shade50,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: Colors.amber.shade300),
                      ),
                      child: const Row(
                        children: [
                          Icon(Icons.info_outline,
                              size: 18, color: Colors.amber),
                          SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'Mode Preview: Menampilkan data simulasi SRS (menunggu API /mesins diisi).',
                              style: TextStyle(fontSize: 12),
                            ),
                          ),
                        ],
                      ),
                    ),

                  // 1. Ringkasan Tugas Perawatan (Jatuh Tempo & Terlambat)
                  Row(
                    children: [
                      Expanded(
                        child: _buildSummaryCard(
                          title: 'Jatuh Tempo',
                          count: dashboard.tugasJatuhTempo,
                          color: Colors.orange.shade700,
                          icon: Icons.schedule,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: _buildSummaryCard(
                          title: 'Terlambat',
                          count: dashboard.tugasTerlambat,
                          color: Colors.red.shade700,
                          icon: Icons.warning_amber_rounded,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),

                  // 2. Status Mesin Bubut Utama (IIoT)
                  const Text(
                    'Mesin Bubut Utama (IIoT)',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  ...dashboard.mesinBubutList
                      .map((mesin) => _buildMesinBubutCard(mesin)),

                  const SizedBox(height: 20),

                  // 3. Mesin Pendukung Workshop
                  const Text(
                    'Mesin Pendukung & Jadwal Terdekat',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  ...dashboard.mesinPendukungList
                      .map((mesin) => _buildMesinPendukungCard(mesin)),
                ],
              ),
            ),
    );
  }

  Widget _buildSummaryCard({
    required String title,
    required int count,
    required Color color,
    required IconData icon,
  }) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withValues(alpha: 0.3)),
      ),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: color.withValues(alpha: 0.12),
            child: Icon(icon, color: color),
          ),
          const SizedBox(width: 12),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '$count Tugas',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                  color: color,
                ),
              ),
              Text(
                title,
                style: TextStyle(fontSize: 12, color: Colors.grey.shade700),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMesinBubutCard(MesinModel mesin) {
    final statusCol = _statusColor(mesin.statusOperasional);
    final kondisiCol = _kondisiColor(mesin.tingkatKondisi);

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Text(
                    mesin.namaMesin,
                    style: const TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: statusCol.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.circle, size: 10, color: statusCol),
                      const SizedBox(width: 6),
                      Text(
                        mesin.statusOperasional,
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: statusCol,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Row(
              children: [
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: kondisiCol.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text(
                    'Kondisi: ${mesin.tingkatKondisi}',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: kondisiCol,
                    ),
                  ),
                ),
                const Spacer(),
                Text(
                  'Update: ${mesin.terakhirUpdate}',
                  style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
                ),
              ],
            ),
            const Divider(height: 20),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: [
                _buildMetricItem('JKB Hari Ini', '${mesin.jkbHariIni} Jam'),
                _buildMetricItem('Suhu', '${mesin.suhuTerkini} °C'),
                _buildMetricItem('Getaran', '${mesin.getaranTerkini} mm/s'),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMetricItem(String label, String value) {
    return Column(
      children: [
        Text(
          value,
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold),
        ),
        const SizedBox(height: 2),
        Text(
          label,
          style: TextStyle(fontSize: 11, color: Colors.grey.shade600),
        ),
      ],
    );
  }

  Widget _buildMesinPendukungCard(MesinModel mesin) {
    final isLate =
        (mesin.jadwalTerdekat ?? '').toLowerCase().contains('terlambat');

    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: ListTile(
        leading: CircleAvatar(
          backgroundColor: const Color(0xFF1A3A75).withValues(alpha: 0.1),
          child: const Icon(Icons.build_circle_outlined,
              color: Color(0xFF1A3A75)),
        ),
        title: Text(
          mesin.namaMesin,
          style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
        ),
        subtitle: Text(
          mesin.namaTugasTerdekat ?? 'Tidak ada jadwal rutin',
          style: const TextStyle(fontSize: 12),
        ),
        trailing: Container(
          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(
            color: isLate ? Colors.red.shade50 : Colors.blue.shade50,
            borderRadius: BorderRadius.circular(8),
          ),
          child: Text(
            mesin.jadwalTerdekat ?? '-',
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.bold,
              color: isLate ? Colors.red.shade700 : Colors.blue.shade800,
            ),
          ),
        ),
      ),
    );
  }
}