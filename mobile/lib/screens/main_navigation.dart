import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import 'auth/login_screen.dart';
import 'beranda/beranda_screen.dart';
import 'mesin/mesin_screen.dart';
import 'notifikasi/notifikasi_screen.dart';
import 'owner/owner_admin_screen.dart';
import 'riwayat/riwayat_screen.dart';
import 'tugas/tugas_screen.dart';

class MainNavigation extends StatefulWidget {
  const MainNavigation({super.key});

  @override
  State<MainNavigation> createState() => _MainNavigationState();
}

class _MainNavigationState extends State<MainNavigation> {
  int _selectedIndex = 0;

  void _openOwnerMenu(BuildContext context, OwnerMenuType type, String title) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => OwnerAdminScreen(menuType: type, title: title),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    final List<Widget> pages = [
      const BerandaScreen(),
      const MesinScreen(),
      const TugasScreen(),
      const RiwayatScreen(),
      _buildProfilTab(context, user?.name ?? '-', user?.role ?? 'teknisi', user?.isOwner ?? false),
    ];

    return Scaffold(
      appBar: _selectedIndex == 0
          ? null
          : AppBar(
              title: const Text('LatheGuard-IIoT'),
              backgroundColor: const Color(0xFF1E3A8A),
              foregroundColor: Colors.white,
              actions: [
                IconButton(
                  icon: const Icon(Icons.notifications_outlined),
                  onPressed: () {
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => const NotifikasiScreen()),
                    );
                  },
                ),
              ],
            ),
      body: pages[_selectedIndex],
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _selectedIndex,
        onTap: (index) => setState(() => _selectedIndex = index),
        type: BottomNavigationBarType.fixed,
        selectedItemColor: const Color(0xFF1E3A8A),
        unselectedItemColor: Colors.grey.shade600,
        items: const [
          BottomNavigationBarItem(icon: Icon(Icons.dashboard_outlined), label: 'Beranda'),
          BottomNavigationBarItem(icon: Icon(Icons.precision_manufacturing_outlined), label: 'Mesin'),
          BottomNavigationBarItem(icon: Icon(Icons.assignment_outlined), label: 'Tugas'),
          BottomNavigationBarItem(icon: Icon(Icons.history_outlined), label: 'Riwayat'),
          BottomNavigationBarItem(icon: Icon(Icons.person_outline), label: 'Profil'),
        ],
      ),
    );
  }

  Widget _buildProfilTab(BuildContext context, String name, String role, bool isOwner) {
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        Card(
          child: ListTile(
            leading: const CircleAvatar(child: Icon(Icons.person)),
            title: Text(name, style: const TextStyle(fontWeight: FontWeight.bold)),
            subtitle: Text('Peran: ${role.toUpperCase()}'),
          ),
        ),
        const SizedBox(height: 16),
        if (isOwner) ...[
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 8),
            child: Text('Administrasi Owner', style: TextStyle(fontWeight: FontWeight.bold)),
          ),
          ListTile(
            leading: const Icon(Icons.build_circle_outlined),
            title: const Text('Kelola Data Mesin & Perangkat IoT'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => _openOwnerMenu(
                context, OwnerMenuType.kelolaMesin, 'Kelola Mesin & Perangkat IoT'),
          ),
          ListTile(
            leading: const Icon(Icons.settings_input_component_outlined),
            title: const Text('Kelola Komponen & Interval Servis'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => _openOwnerMenu(
                context, OwnerMenuType.kelolaKomponen, 'Kelola Komponen & Interval'),
          ),
          ListTile(
            leading: const Icon(Icons.tune_outlined),
            title: const Text('Atur Ambang, Bobot Beban & Baseline'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => _openOwnerMenu(
                context, OwnerMenuType.konfigurasiAmbang, 'Ambang Status & Baseline'),
          ),
          ListTile(
            leading: const Icon(Icons.event_repeat_outlined),
            title: const Text('Kelola Jadwal Perawatan Manual'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => _openOwnerMenu(
                context, OwnerMenuType.kelolaJadwal, 'Kelola Jadwal Perawatan'),
          ),
          ListTile(
            leading: const Icon(Icons.manage_accounts_outlined),
            title: const Text('Kelola Akun & Hak Akses'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => _openOwnerMenu(
                context, OwnerMenuType.kelolaAkun, 'Kelola Akun & Hak Akses'),
          ),
          ListTile(
            leading: const Icon(Icons.bar_chart_outlined),
            title: const Text('Laporan Ringkas'),
            trailing: const Icon(Icons.chevron_right),
            onTap: () => _openOwnerMenu(
                context, OwnerMenuType.laporanRingkas, 'Laporan Ringkas Workshop'),
          ),
          const Divider(),
        ],
        ListTile(
          leading: const Icon(Icons.logout, color: Colors.red),
          title: const Text('Keluar', style: TextStyle(color: Colors.red)),
          onTap: () async {
            await context.read<AuthProvider>().logout();
            if (!context.mounted) return;
            Navigator.pushAndRemoveUntil(
              context,
              MaterialPageRoute(builder: (_) => const LoginScreen()),
              (route) => false,
            );
          },
        ),
      ],
    );
  }
}