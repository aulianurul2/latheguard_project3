<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="LatheGuard IIoT - Sistem Monitoring Bengkel">
	<meta name="csrf-token" content="{{ csrf_token() }}">

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

		/* Merapikan pagination agar sejajar horizontal tanpa bullet */
		.pagination {
			display: flex !important;
			flex-direction: row !important;
			list-style: none !important;
			padding-left: 0 !important;
			margin-bottom: 0 !important;
			gap: 4px;
			align-items: center;
		}

		.pagination .page-item {
			list-style: none !important;
		}

		.pagination .page-link {
			display: inline-flex !important;
			align-items: center !important;
			justify-content: center !important;
			min-width: 34px;
			height: 34px;
			padding: 0 10px !important;
			font-size: 13px !important;
			line-height: 1 !important;
			color: #495057;
			background-color: #fff;
			border: 1px solid #dee2e6;
			border-radius: 6px !important;
			text-decoration: none;
		}

		.pagination .page-item.active .page-link {
			color: #fff !important;
			background-color: #1D63ED !important;
			border-color: #1D63ED !important;
			font-weight: 600;
		}

		.pagination .page-item.disabled .page-link {
			color: #adb5bd !important;
			background-color: #f8f9fa !important;
			pointer-events: none;
		}

		.pagination .page-link:hover {
			background-color: #f1f5f9;
		}

		.pagination svg {
			width: 14px !important;
			height: 14px !important;
		}
			</style>

	@stack('styles')
</head>

@php
	// Ambil notifikasi yang belum dibaca (Peringatan Servis & Anomali)
	$notifikasiBelumDibaca = \Illuminate\Support\Facades\Schema::hasTable('log_anomali_notifikasis')
		? \Illuminate\Support\Facades\DB::table('log_anomali_notifikasis')
			->where('is_read', false)
			->orderByDesc('created_at')
			->limit(5)
			->get()
		: collect();

	$jumlahNotif = $notifikasiBelumDibaca->count();

	// Cek koneksi IoT berdasarkan data masuk dalam 1 menit terakhir
	$iotOnline = \Illuminate\Support\Facades\Schema::hasColumn('mesins', 'last_seen_at')
		? \App\Models\Mesin::where('last_seen_at', '>=', now()->subMinute())->exists()
		: false;
@endphp

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
						<a class="sidebar-link" href="{{ url('/monitoring') }}">
							<i class="align-middle" data-lucide="monitor"></i> <span class="align-middle">Monitoring Mesin Bubut</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('analisis-ai*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/analisis-ai') }}">
							<i class="align-middle" data-lucide="sparkles"></i> <span class="align-middle">Analisis AI</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('sensor*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/sensor') }}">
							<i class="align-middle" data-lucide="database"></i> <span class="align-middle">Data Sensor</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('peralatan*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/peralatan') }}">
							<i class="align-middle" data-lucide="building-2"></i> <span class="align-middle">Data Mesin & Peralatan</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('komponen*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/komponen') }}">
							<i class="align-middle" data-lucide="wrench"></i> <span class="align-middle">Komponen</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('kategori*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/kategori') }}">
							<i class="align-middle" data-lucide="tags"></i> <span class="align-middle">Kategori</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('jadwal*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/jadwal') }}">
							<i class="align-middle" data-lucide="calendar"></i> <span class="align-middle">Jadwal Maintenance</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('riwayat*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/riwayat') }}">
							<i class="align-middle" data-lucide="history"></i> <span class="align-middle">Riwayat Maintenance</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('laporan*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/laporan') }}">
							<i class="align-middle" data-lucide="file-text"></i> <span class="align-middle">Laporan</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->is('pengaturan*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ url('/pengaturan') }}">
							<i class="align-middle" data-lucide="settings"></i> <span class="align-middle">Pengaturan</span>
						</a>
					</li>
				</ul>

				<!-- Status Gateway di Bawah Sidebar -->
				<div class="sidebar-cta">
					<div class="sidebar-cta-content" style="background: rgba(255,255,255,0.06); border-radius: 10px;">
						<div class="d-flex align-items-center text-white small">
							<span class="badge {{ $iotOnline ? 'bg-success' : 'bg-secondary' }} rounded-circle p-1 me-2"> </span>
							<span>{{ $iotOnline ? 'Gateway terhubung' : 'Gateway siaga' }}</span>
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
							@if($iotOnline)
								<span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
									● IoT terhubung
								</span>
							@else
								<span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">
									● IoT terputus
								</span>
							@endif
						</li>
						<li class="nav-item me-3 d-none d-md-block">
							<span class="text-success small fw-semibold">● Daring</span>
						</li>

						<!-- Notifikasi Peringatan Servis & Anomali -->
						<li class="nav-item dropdown me-2">
							<a class="nav-icon dropdown-toggle" href="#" id="alertsDropdown" data-bs-toggle="dropdown">
								<div class="position-relative">
									<i class="align-middle" data-lucide="bell"></i>
									@if($jumlahNotif > 0)
										<span class="indicator bg-danger">{{ $jumlahNotif }}</span>
									@endif
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-lg dropdown-menu-end py-0" aria-labelledby="alertsDropdown">
								<div class="dropdown-menu-header">
									{{ $jumlahNotif }} Peringatan Aktif
								</div>
								<div class="list-group">
									@forelse($notifikasiBelumDibaca as $notif)
										<a href="{{ $notif->tipe === 'Peringatan Servis' ? url('/jadwal') : url('/analisis-ai') }}" class="list-group-item">
											<div class="row g-0 align-items-center">
												<div class="col-2">
													<i class="{{ $notif->tingkat_kondisi === 'Kritis' ? 'text-danger' : 'text-warning' }}" data-lucide="alert-triangle"></i>
												</div>
												<div class="col-10">
													<div class="text-dark fw-semibold">{{ $notif->judul }}</div>
													<div class="text-muted small mt-1">{{ \Illuminate\Support\Str::limit($notif->rekomendasi, 75) }}</div>
													<div class="text-muted small mt-1">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
												</div>
											</div>
										</a>
									@empty
										<div class="list-group-item text-center text-muted small py-3">
											Tidak ada peringatan servis atau anomali
										</div>
									@endforelse
								</div>
							</div>
						</li>

						<!-- Profil User -->
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
								<img src="{{ asset('adminkit/img/avatars/avatar.jpg') }}" class="avatar img-fluid rounded-circle me-2" alt="Admin" />
								<div class="text-start d-none d-sm-inline-block">
									<div class="text-dark fw-semibold lh-1">{{ Auth::user()->name ?? 'Owner' }}</div>
									<small class="text-muted text-capitalize" style="font-size: 11px;">{{ Auth::user()->role ?? 'Administrator' }}</small>
								</div>
							</a>
							<div class="dropdown-menu dropdown-menu-end">
								<a class="dropdown-item" href="{{ url('/pengaturan') }}"><i class="align-middle me-1" data-lucide="settings"></i> Pengaturan</a>
								<div class="dropdown-divider"></div>
								<form action="{{ route('logout') }}" method="POST">
									@csrf
									<button type="submit" class="dropdown-item text-danger">Keluar</button>
								</form>
							</div>
						</li>
					</ul>
				</div>
			</nav>

			<!-- KONTEN UTAMA -->
			<main class="content">
				<div class="container-fluid p-0">
					@if(session('success'))
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							{{ session('success') }}
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					@endif

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