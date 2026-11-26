class UserModel {
  final int id;
  final String name;
  final String email;
  final String role; // 'owner' atau 'teknisi'

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
  });

  bool get isOwner => role.toLowerCase() == 'owner';
  bool get isTeknisi => role.toLowerCase() == 'teknisi';

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? json['nama'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'teknisi',
    );
  }
}