@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
	<div>
		<span class="text-primary fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">GAMBARAN UMUM</span>
		<h1 class="h2 fw-bold text-dark mb-1">Dashboard</h1>
		<p class="text-muted mb-0">Pantau kondisi mesin, rencanakan perawatan dengan lebih pasti.</p>
	</div>
	<div>
		<span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-3 fw-semibold">
			01–20 Nov 2026
		</span>
	</div>
</div>

<!-- RINGKASAN PERALATAN BENGKEL -->
<div class="d-flex justify-content-between align-items-center mb-2">
	<div>
		<h5 class="fw-bold text-dark mb-0">Ringkasan peralatan bengkel</h5>
		<small class="text-muted">Seluruh peralatan • Data maintenance dicatat manual</small>
	</div>
	<a href="#" class="text-primary fw-semibold small text-decoration-none">Lihat semua peralatan →</a>
</div>

<div class="row g-3 mb-3">
	<div class="col-6 col-md-4 col-xl">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<span class="text-muted small fw-medium">Total peralatan</span>
					<span class="bg-primary rounded-pill" style="width: 4px; height: 14px;"></span>
				</div>
				<h2 class="fw-bold text-primary my-2">24</h2>
				<small class="text-muted">6 kategori peralatan</small>
			</div>
		</div>
	</div>

	<div class="col-6 col-md-4 col-xl">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<span class="text-muted small fw-medium">Maintenance normal</span>
					<span class="bg-success rounded-pill" style="width: 4px; height: 14px;"></span>
				</div>
				<h2 class="fw-bold text-success my-2">18</h2>
				<small class="text-muted">75% dari seluruh peralatan</small>
			</div>
		</div>
	</div>

	<div class="col-6 col-md-4 col-xl">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<span class="text-muted small fw-medium">Mendekati jadwal</span>
					<span class="bg-warning rounded-pill" style="width: 4px; height: 14px;"></span>
				</div>
				<h2 class="fw-bold text-warning my-2">4</h2>
				<small class="text-muted">Dalam 14 hari ke depan</small>
			</div>
		</div>
	</div>

	<div class="col-6 col-md-6 col-xl">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<span class="text-muted small fw-medium">Maintenance terlambat</span>
					<span class="bg-danger rounded-pill" style="width: 4px; height: 14px;"></span>
				</div>
				<h2 class="fw-bold text-danger my-2">2</h2>
				<small class="text-muted">Perlu segera dijadwalkan</small>
			</div>
		</div>
	</div>

	<div class="col-12 col-md-6 col-xl">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<span class="text-muted small fw-medium">Mesin bubut IoT</span>
					<span class="bg-primary rounded-pill" style="width: 4px; height: 14px;"></span>
				</div>
				<h2 class="fw-bold text-primary my-2">2 / 2</h2>
				<small class="text-muted">Seluruh perangkat terhubung</small>
			</div>
		</div>
	</div>
</div>

<!-- BAR KESEHATAN MAINTENANCE -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
	<div class="card-body py-3">
		<div class="row align-items-center g-3">
			<div class="col-md-3">
				<div class="fw-bold text-dark small">Kesehatan maintenance</div>
				<small class="text-muted">24 peralatan</small>
			</div>
			<div class="col-md-6">
				<div class="progress" style="height: 8px; background-color: transparent;">
					<div class="progress-bar bg-success rounded-pill me-1" style="width: 75%"></div>
					<div class="progress-bar bg-warning rounded-pill me-1" style="width: 17%"></div>
					<div class="progress-bar bg-danger rounded-pill" style="width: 8%"></div>
				</div>
			</div>
			<div class="col-md-3 text-md-end small fw-semibold">
				<span class="text-success me-2">● Normal 18</span>
				<span class="text-warning me-2">● Segera 4</span>
				<span class="text-danger">● Terlambat 2</span>
			</div>
		</div>
	</div>
</div>

<!-- MONITORING MESIN BUBUT -->
<div class="d-flex justify-content-between align-items-center mb-2">
	<div class="d-flex align-items-center gap-2">
		<h5 class="fw-bold text-dark mb-0">Monitoring mesin bubut</h5>
		<span class="badge bg-success-subtle text-success rounded-pill" style="font-size: 10px;">KHUSUS MESIN BUBUT IoT</span>
	</div>
	<a href="#" class="text-primary fw-semibold small text-decoration-none">Buka monitoring lengkap →</a>
</div>

<div class="alert bg-primary-subtle border-0 rounded-3 d-flex flex-wrap justify-content-between align-items-center py-2 px-3 mb-3">
	<div>
		<strong class="text-dark">MB-001 / Mesin Bubut 01</strong>
		<span class="text-muted small ms-2">Krisbow KW15-604 • Area Produksi A</span>
	</div>
	<div class="small">
		<span class="text-success fw-bold me-3">● Aktif</span>
		<span class="text-muted">Diperbarui 3 detik lalu</span>
	</div>
</div>

<!-- KARTU DATA SENSOR REALTIME -->
<div class="row g-3 mb-4">
	<div class="col-6 col-md-4 col-xl-2">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<small class="text-muted d-block">Status operasional</small>
				<h3 class="fw-bold text-success my-2">Aktif</h3>
				<small class="text-muted">Berputar stabil</small>
			</div>
		</div>
	</div>

	<div class="col-6 col-md-4 col-xl-2">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<small class="text-muted d-block">Suhu mesin</small>
				<h3 class="fw-bold text-primary my-2">42,5 °C</h3>
				<small class="text-muted">Termokopel • Normal</small>
			</div>
		</div>
	</div>

	<div class="col-6 col-md-4 col-xl-2">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<small class="text-muted d-block">Kecepatan sudut</small>
				<h3 class="fw-bold text-primary my-2">120 °/s</h3>
				<small class="text-muted">Giroskop • Stabil</small>
			</div>
		</div>
	</div>

	<div class="col-6 col-md-4 col-xl-2">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<small class="text-muted d-block">Sinyal suara relatif</small>
				<h3 class="fw-bold text-primary my-2">1.850 ADC</h3>
				<small class="text-muted">GY-MAX4466</small>
			</div>
		</div>
	</div>

	<div class="col-6 col-md-4 col-xl-2">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<small class="text-muted d-block">Estimasi jam operasi</small>
				<h3 class="fw-bold text-dark my-2">37,5 jam</h3>
				<small class="text-muted">Berdasarkan aktivitas</small>
			</div>
		</div>
	</div>

	<div class="col-6 col-md-4 col-xl-2">
		<div class="card border-0 shadow-sm rounded-3 h-100 mb-0">
			<div class="card-body">
				<small class="text-muted d-block">Analisis AI</small>
				<h3 class="fw-bold text-success my-2">Normal</h3>
				<small class="text-muted">Tidak ada pola anomali</small>
			</div>
		</div>
	</div>
</div>

<!-- GRAFIK TREN SENSOR -->
<div class="card border-0 shadow-sm rounded-3">
	<div class="card-body">
		<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
			<div>
				<h5 class="fw-bold text-dark mb-0">Tren sensor • Mesin Bubut 01 • 1 jam</h5>
				<small class="text-muted">Satuan sensor ditampilkan terpisah</small>
			</div>
			<div class="btn-group btn-group-sm" role="group">
				<button type="button" class="btn btn-primary">1 jam</button>
				<button type="button" class="btn btn-light">24 jam</button>
				<button type="button" class="btn btn-light">7 hari</button>
				<button type="button" class="btn btn-light">30 hari</button>
			</div>
		</div>

		<div class="row g-4">
			<div class="col-md-4">
				<div class="d-flex justify-content-between mb-2">
					<small class="text-muted fw-semibold">Suhu (°C)</small>
					<small class="text-primary fw-bold">42,5</small>
				</div>
				<div style="height: 160px;">
					<canvas id="chartSuhu"></canvas>
				</div>
			</div>

			<div class="col-md-4">
				<div class="d-flex justify-content-between mb-2">
					<small class="text-muted fw-semibold">Kecepatan sudut (°/s)</small>
					<small class="text-success fw-bold">120</small>
				</div>
				<div style="height: 160px;">
					<canvas id="chartKecepatan"></canvas>
				</div>
			</div>

			<div class="col-md-4">
				<div class="d-flex justify-content-between mb-2">
					<small class="text-muted fw-semibold">Amplitudo suara (ADC)</small>
					<small class="fw-bold" style="color: #7C3AED;">1.850</small>
				</div>
				<div style="height: 160px;">
					<canvas id="chartSuara"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
	document.addEventListener("DOMContentLoaded", function() {
		const labels = ['08.00', '', '', '', '', '08.30', '', '', '', '', '09.00'];
		const commonOptions = {
			responsive: true,
			maintainAspectRatio: false,
			plugins: { legend: { display: false } },
			elements: { point: { radius: 0 }, line: { tension: 0.3, borderWidth: 2 } },
			scales: {
				x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
				y: { border: { display: false }, ticks: { display: false }, grid: { color: '#f1f5f9' } }
			}
		};

		new Chart(document.getElementById('chartSuhu'), {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{ data: [34, 35.5, 35, 37, 36.2, 38.5, 37.8, 39.2, 41, 40.5, 42.5], borderColor: '#1D63ED' }]
			},
			options: commonOptions
		});

		new Chart(document.getElementById('chartKecepatan'), {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{ data: [105, 112, 115, 114, 118, 117, 121, 119, 120, 119, 120], borderColor: '#10B981' }]
			},
			options: commonOptions
		});

		new Chart(document.getElementById('chartSuara'), {
			type: 'line',
			data: {
				labels: labels,
				datasets: [{ data: [1400, 1750, 1300, 2100, 1500, 1200, 1800, 1450, 1650, 1900, 1850], borderColor: '#7C3AED' }]
			},
			options: commonOptions
		});
	});
</script>
@endpush