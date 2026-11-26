import 'package:flutter/material.dart';
import 'providers/mesin_provider.dart';
import 'providers/operasional_provider.dart';
import 'package:provider/provider.dart';
import 'providers/auth_provider.dart';
import 'providers/dashboard_provider.dart';
import 'screens/auth/login_screen.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => OperasionalProvider()),
        ChangeNotifierProvider(create: (_) => MesinProvider()),
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => DashboardProvider()),
      ],
      child: const LatheGuardApp(),
    ),
  );
}

class LatheGuardApp extends StatelessWidget {
  const LatheGuardApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'LatheGuard-IIoT',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF1E3A8A)),
        useMaterial3: true,
      ),
      home: const LoginScreen(),
    );
  }
}