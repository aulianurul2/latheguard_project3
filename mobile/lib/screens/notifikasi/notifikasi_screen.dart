import 'package:flutter/material.dart';

class NotifikasiScreen extends StatefulWidget {
  const NotifikasiScreen({super.key});

  @override
  State<NotifikasiScreen> createState() => _NotifikasiScreenState();
}

class _NotifikasiScreenState extends State<NotifikasiScreen> {
  final List<Map<String, dynamic>> _notifList = [
    {
      'id': 1,
      'judul': 'Servis Komponen Mendekati Batas',
      'pesan': 'Bearing Spindle Utama pada Mesin Bubut CNC-01 mencapai 93% JKB (465/500 Jam).',
      'waktu': '15 menit lalu',
      'tipe': 'servis',
      'dibaca': false,
    },
    {
      'id': 2,
      'judul': 'Anomali Getaran Terdeteksi',
      'pesan': 'Mesin Bubut Manual-02 mencatat getaran 3.4 mm/s (melebihi ambang 2.5 mm/s).',
      'waktu': '1 jam lalu',
      'tipe': 'anomali',
      'dibaca': false,
    },
    {
      'id': 3,
      'judul': 'Jadwal Perawatan Terlambat',
      'pesan': 'Penggantian Batu Gerinda pada Mesin Gerinda Duduk melewati jatuh tempo.',
      'waktu': 'Kemarin',
      'tipe': 'tugas',
      'dibaca': true,
    },
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifikasi Peringatan'),
        backgroundColor: const Color(0xFF1E3A8A),
        foregroundColor: Colors.white,
        actions: [
          TextButton(
            onPressed: () {
              setState(() {
                for (var n in _notifList) {
                  n['dibaca'] = true;
                }
              });
            },
            child: const Text('Baca Semua', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: _notifList.length,
        itemBuilder: (context, index) {
          final item = _notifList[index];
          final bool dibaca = item['dibaca'];
          final IconData icon = item['tipe'] == 'anomali'
              ? Icons.warning_amber_rounded
              : (item['tipe'] == 'servis' ? Icons.build_circle : Icons.event_busy);
          final Color color = item['tipe'] == 'anomali'
              ? Colors.red.shade700
              : (item['tipe'] == 'servis' ? Colors.orange.shade800 : Colors.indigo);

          return Card(
            color: dibaca ? Colors.white : Colors.blue.shade50,
            margin: const EdgeInsets.only(bottom: 10),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            child: ListTile(
              leading: CircleAvatar(
                backgroundColor: color.withValues(alpha: 0.12),
                child: Icon(icon, color: color),
              ),
              title: Text(
                item['judul'],
                style: TextStyle(
                  fontWeight: dibaca ? FontWeight.w600 : FontWeight.bold,
                  fontSize: 14,
                ),
              ),
              subtitle: Padding(
                padding: const EdgeInsets.only(top: 4),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(item['pesan'], style: const TextStyle(fontSize: 12)),
                    const SizedBox(height: 4),
                    Text(item['waktu'],
                        style: TextStyle(fontSize: 11, color: Colors.grey.shade600)),
                  ],
                ),
              ),
              onTap: () => setState(() => item['dibaca'] = true),
            ),
          );
        },
      ),
    );
  }
}