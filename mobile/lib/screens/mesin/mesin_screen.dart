import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/detail_mesin_model.dart';
import '../../models/mesin_model.dart';
import '../../providers/dashboard_provider.dart';
import '../../providers/mesin_provider.dart';

class MesinScreen extends StatefulWidget {
  const MesinScreen({super.key});

  @override
  State<MesinScreen> createState() => _MesinScreenState();
}

class _MesinScreenState extends State<MesinScreen> {
  MesinModel? _selectedMesin;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final dashboard = context.read<DashboardProvider>();
      if (dashboard.mesinBubutList.isEmpty) {
        await dashboard.loadDashboardData();
      }
      if (!mounted) return;
      if (dashboard.mesinBubutList.isNotEmpty) {
        setState(() => _selectedMesin = dashboard.mesinBubutList.first);
        context.read<MesinProvider>().loadDetailMesin(_selectedMesin!.id);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final dashboard = context.watch<DashboardProvider>();
    final mesinProv = context.watch<MesinProvider>();
    final daftarMesin = dashboard.mesinBubutList;

    if (daftarMesin.isEmpty) {
      return const Center(child: CircularProgressIndicator());
    }

    _selectedMesin ??= daftarMesin.first;

    return DefaultTabController(
      length: 3,
      child: Column(
        children: [
          // Dropdown pemilih mesin bubut
          Container(
            color: const Color(0xFF1E3A8A),
            padding: const EdgeInsets.fromLTRB(16, 4, 16, 12),
            child: DropdownButtonFormField<MesinModel>(
              initialValue: _selectedMesin,
              dropdownColor: const Color(0xFF1E3A8A),
              iconEnabledColor: Colors.white,
              style: const TextStyle(
                  color: Colors.white, fontWeight: FontWeight.bold),
              decoration: InputDecoration(
                filled: true,
                fillColor: Colors.white.withValues(alpha: 0.12),
                contentPadding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: BorderSide.none,
                ),
              ),
              items: daftarMesin.map((m) {
                return DropdownMenuItem(
                  value: m,
                  child: Text('${m.namaMesin} (${m.statusOperasional})'),
                );
              }).toList(),
              onChanged: (val) {
                if (val != null) {
                  setState(() => _selectedMesin = val);
                  mesinProv.loadDetailMesin(val.id);
                }
              },
            ),
          ),

          // Sub-tab 3 fitur monitoring (KF-03, KF-07, KF-09)
          Container(
            color: Colors.white,
            child: const TabBar(
              labelColor: Color(0xFF1E3A8A),
              unselectedLabelColor: Colors.grey,
              indicatorColor: Color(0xFF1E3A8A),
              tabs: [
                Tab(text: 'Sensor & Grafik'),
                Tab(text: 'Komponen (JKB)'),
                Tab(text: 'Anomali'),
              ],
            ),
          ),

          Expanded(
            child: mesinProv.isLoading
                ? const Center(child: CircularProgressIndicator())
                : TabBarView(
                    children: [
                      _buildSensorGrafikTab(_selectedMesin!, mesinProv),
                      _buildKomponenTab(mesinProv.komponenList),
                      _buildAnomaliTab(mesinProv.anomaliList),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildSensorGrafikTab(MesinModel mesin, MesinProvider prov) {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        // Kartu pembacaan sensor terkini
        Row(
          children: [
            Expanded(
              child: _sensorBox(
                label: 'Suhu Spindle',
                value: '${mesin.suhuTerkini} °C',
                icon: Icons.thermostat,
                color: Colors.deepOrange,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _sensorBox(
                label: 'Getaran RMS',
                value: '${mesin.getaranTerkini} mm/s',
                icon: Icons.vibration,
                color: Colors.indigo,
              ),
            ),
          ],
        ),
        const SizedBox(height: 16),

        // Filter rentang waktu grafik (1 Jam, 24 Jam, 7 Hari - KF-03)
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text('Grafik Tren Sensor',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
            SegmentedButton<String>(
              segments: const [
                ButtonSegment(value: '1 Jam', label: Text('1J')),
                ButtonSegment(value: '24 Jam', label: Text('24J')),
                ButtonSegment(value: '7 Hari', label: Text('7H')),
              ],
              selected: {prov.selectedRange},
              onSelectionChanged: (val) => prov.setRange(val.first, mesin.id),
            ),
          ],
        ),
        const SizedBox(height: 12),

        // Visualisasi batang tren Suhu
        _buildBarChartCard(
          title: 'Tren Suhu (°C) — ${prov.selectedRange}',
          values: prov.trenSuhu,
          maxVal: 70,
          barColor: Colors.deepOrange,
          unit: '°C',
        ),
        const SizedBox(height: 12),

        // Visualisasi batang tren Getaran
        _buildBarChartCard(
          title: 'Tren Getaran (mm/s) — ${prov.selectedRange}',
          values: prov.trenGetaran,
          maxVal: 5,
          barColor: Colors.indigo,
          unit: 'mm/s',
        ),
      ],
    );
  }

  Widget _sensorBox({
    required String label,
    required String value,
    required IconData icon,
    required Color color,
  }) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(height: 8),
          Text(value,
              style: TextStyle(
                  fontSize: 18, fontWeight: FontWeight.bold, color: color)),
          Text(label,
              style: TextStyle(fontSize: 12, color: Colors.grey.shade600)),
        ],
      ),
    );
  }

  Widget _buildBarChartCard({
    required String title,
    required List<double> values,
    required double maxVal,
    required Color barColor,
    required String unit,
  }) {
    return Card(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(title,
                style:
                    const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
            const SizedBox(height: 14),
            SizedBox(
              height: 120,
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: values.map((v) {
                  final factor = (v / maxVal).clamp(0.1, 1.0);
                  return Column(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      Text(v.toStringAsFixed(1),
                          style: const TextStyle(fontSize: 10)),
                      const SizedBox(height: 4),
                      Container(
                        width: 22,
                        height: 85 * factor,
                        decoration: BoxDecoration(
                          color: barColor,
                          borderRadius: BorderRadius.circular(6),
                        ),
                      ),
                    ],
                  );
                }).toList(),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildKomponenTab(List<KomponenModel> list) {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: list.length,
      itemBuilder: (context, index) {
        final item = list[index];
        final pct = item.persentase;
        final barColor = pct >= 0.9
            ? Colors.red
            : (pct >= 0.75 ? Colors.orange : Colors.teal);

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
                    Expanded(
                      child: Text(item.namaKomponen,
                          style: const TextStyle(
                              fontWeight: FontWeight.bold, fontSize: 14)),
                    ),
                    Text(
                      '${(pct * 100).toStringAsFixed(0)}%',
                      style: TextStyle(
                          fontWeight: FontWeight.bold, color: barColor),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                ClipRRect(
                  borderRadius: BorderRadius.circular(6),
                  child: LinearProgressIndicator(
                    value: pct,
                    minHeight: 8,
                    backgroundColor: Colors.grey.shade200,
                    color: barColor,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      'JKB: ${item.jkbSaatIni.toInt()} / ${item.intervalServis.toInt()} Jam',
                      style:
                          TextStyle(fontSize: 12, color: Colors.grey.shade700),
                    ),
                    Text(
                      item.statusServis,
                      style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: barColor),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildAnomaliTab(List<AnomaliModel> list) {
    if (list.isEmpty) {
      return const Center(
          child: Text('Tidak ada anomali terdeteksi pada mesin ini.'));
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: list.length,
      itemBuilder: (context, index) {
        final item = list[index];
        final isKritis = item.tingkatKeparahan.toLowerCase() == 'kritis';
        final color = isKritis ? Colors.red.shade700 : Colors.orange.shade800;

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
                  children: [
                    Icon(Icons.warning_amber_rounded, color: color),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(item.parameter,
                          style: const TextStyle(
                              fontWeight: FontWeight.bold, fontSize: 14)),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 3),
                      decoration: BoxDecoration(
                        color: color.withValues(alpha: 0.12),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(item.tingkatKeparahan,
                          style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: color)),
                    ),
                  ],
                ),
                const SizedBox(height: 6),
                Text(item.waktu,
                    style:
                        TextStyle(fontSize: 11, color: Colors.grey.shade600)),
                const SizedBox(height: 6),
                Text(item.nilaiTerbaca, style: const TextStyle(fontSize: 13)),
                const Divider(height: 16),
                Text(
                  'Saran Pemeriksaan: ${item.saranPemeriksaan}',
                  style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
                      color: Colors.blueGrey.shade800),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}