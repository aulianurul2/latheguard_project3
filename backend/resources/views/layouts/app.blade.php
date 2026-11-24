<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="LatheGuard IIoT - Sistem Monitoring Bengkel">

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="{{ asset('adminkit/img/icons/icon-48x48.png') }}" />

	<title>@yield('title', 'Dashboard') - LatheGuard</title>

	<link href="{{ asset('adminkit/css/app.css') }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<style>
		/* Penyesuaian warna sidebar agar identik dengan desain LatheGuard */
		.sidebar, .sidebar-content {
			background: #13233A !important;
		}
		.sidebar-item.active .sidebar-link:hover,
		.sidebar-item.active > .sidebar-link {
			background: #1D63ED !important;
			color: #ffffff !important;
			border-radius: 8px;
			margin: 0 12px;
		}
		.sidebar-link {
			background: transparent !important;
			margin: 0 12px;
			border-radius: 8px;
		}
	</style>

	@stack('styles')
</head>

<body>
	<div class="wrapper">
		<!-- SIDEBAR -->
		<nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="{{ url('/dashboard') }}">
					<span class="align-middle fw-bold">LatheGuard</span>
					<small class="d-block text-muted" style="font-size: 10px; letter-spacing: 1px;">IIoT • SISTEM BENGKEL</small>
				</a>

				<ul class="sidebar-nav">
					<li class="sidebar-header">
						RUANG KERJA
					</li>

					<li class="sidebar-item {{ request()->is('dashboard') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/dashboard') }}">
							<i class="align-middle" data-lucide="layout-grid"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('monitoring*') ? 'active' : '' }}">
						<a class="sidebar-link" href="#">
							<i class="align-middle" data-lucide="monitor"></i> <span class="align-middle">Monitoring Mesin Bubut</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('analisis-ai*') ? 'active' : '' }}">
						<a class="sidebar-link" href="#">
							<i class="align-middle" data-lucide="sparkles"></i> <span class="align-middle">Analisis AI</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('sensor*') ? 'active' : '' }}">
						<a class="sidebar-link" href="#">
							<i class="align-middle" data-lucide="database"></i> <span class="align-middle">Data Sensor</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('peralatan*') ? 'active' : '' }}">
						<a class="sidebar-link" href="#">
							<i class="align-middle" data-lucide="building-2"></i> <span class="align-middle">Data Mesin & Peralatan</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('jadwal*') ? 'active' : '' }}">
						<a class="sidebar-link" href="#">
							<i class="align-middle" data-lucide="calendar"></i> <span class="align-middle">Jadwal Maintenance</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('riwayat*') ? 'active' : '' }}">
						<a class="sidebar-link" href="#">
							<i class="align-middle" data-lucide="history"></i> <span class="align-middle">Riwayat Maintenance</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('laporan*') ? 'active' : '' }}">
						<a class="sidebar-link" href="#">
							<i class="align-middle" data-lucide="file-text"></i> <span class="align-middle">Laporan</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('pengaturan*') ? 'active' : '' }}">
						<a class="sidebar-link" href="#">
							<i class="align-middle" data-lucide="settings"></i> <span class="align-middle">Pengaturan</span>
						</a>
					</li>
				</ul>

				<!-- Status Gateway di Bawah Sidebar -->
				<div class="sidebar-cta">
					<div class="sidebar-cta-content" style="background: rgba(255,255,255,0.06); border-radius: 10px;">
						<div class="d-flex align-items-center text-white small">
							<span class="badge bg-success rounded-circle p-1 me-2"> </span>
							<span>Gateway terhubung</span>
						</div>
					</div>
				</div>
			</div>
		</nav>

		<!-- MAIN AREA -->
		<div class="main">
			<!-- TOP NAVBAR -->
			<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle">
					<i class="hamburger align-self-center"></i>
				</a>

				<div class="d-none d-sm-inline-block ms-2">
					<div class="fw-bold text-dark">Bengkel Teknik Mesin</div>
					<small class="text-muted">Politeknik • Area Produksi A</small>
				</div>

				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align align-items-center">
						<li class="nav-item me-3">
							<span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
								● IoT terhubung
							</span>
						</li>
						<li class="nav-item me-3 d-none d-md-block">
							<span class="text-success small fw-semibold">● Daring</span>
						</li>

						<!-- Profil User -->
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
								<img src="{{ asset('adminkit/img/avatars/avatar.jpg') }}" class="avatar img-fluid rounded-circle me-2" alt="Admin" />
								<div class="text-start d-none d-sm-inline-block">
									<div class="text-dark fw-semibold lh-1">Andi Pratama</div>
									<small class="text-muted" style="font-size: 11px;">Administrator</small>
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-end">
								<a class="dropdown-item" href="#"><i class="align-middle me-1" data-lucide="user"></i> Profil</a>
								<a class="dropdown-item" href="#"><i class="align-middle me-1" data-lucide="settings"></i> Pengaturan</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item text-danger" href="#">Keluar</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>

			<!-- KONTEN UTAMA -->
			<main class="content">
				<div class="container-fluid p-0">
					@yield('content')
				</div>
			</main>

			<!-- FOOTER -->
			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
								<strong>LatheGuard IIoT</strong> &copy; {{ date('Y') }}
							</p>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>

	<script src="{{ asset('adminkit/js/app.js') }}"></script>
	@stack('scripts')
</body>

</html>