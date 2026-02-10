@php
	$currentRoute = request()->route()?->getName();
	$role = auth()->user()->role ?? null;
	$isPeminjamanMenu = str_starts_with($currentRoute ?? '', 'peminjaman.');
	$isPengembalianMenu = str_starts_with($currentRoute ?? '', 'pengembalian.');
	$isFileManagerMenu = str_starts_with($currentRoute ?? '', 'filemanager.');
@endphp

<div class="col-md-2 sidebar p-3">
	<div class="d-flex align-items-center mb-4 px-2">
		<div class="me-2" style="width: 40px; height: 40px;">
			<img src="{{ asset('storage/uploads/logo/Lendify.png') }}" alt="Lendify" class="img-fluid rounded-circle" style="object-fit: cover; width: 40px; height: 40px;">
		</div>
		<h5 class="mb-0 fw-bold">Lendify</h5>
	</div>
	
	<p class="text-muted px-2 mb-3" style="font-size: 0.75rem;">MENU</p>
	<nav class="nav flex-column">
		<a class="nav-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}" data-nav-link>
			<i class="bi bi-speedometer2 me-2"></i>Dashboard
		</a>
		
		@if($role === 'admin')
			<a class="nav-link {{ $currentRoute === 'user.list' ? 'active' : '' }}" href="{{ route('user.list') }}" data-nav-link><i class="bi bi-people me-2"></i>Kelola Pengguna</a>
			<a class="nav-link {{ $currentRoute === 'kategori.list' ? 'active' : '' }}" href="{{ route('kategori.list') }}" data-nav-link><i class="bi bi-tags me-2"></i>Kategori</a>
			<a class="nav-link {{ $currentRoute === 'alat.list' ? 'active' : '' }}" href="{{ route('alat.list') }}" data-nav-link><i class="bi bi-box-seam me-2"></i>Alat</a>
			<a class="nav-link {{ $isPeminjamanMenu ? 'active' : '' }}" href="{{ route('peminjaman.list') }}" data-nav-link><i class="bi bi-clipboard-check me-2"></i>Peminjaman</a>
			<a class="nav-link {{ $isPengembalianMenu ? 'active' : '' }}" href="{{ route('pengembalian.list') }}" data-nav-link><i class="bi bi-arrow-return-left me-2"></i>Pengembalian</a>
			<a class="nav-link" href="#" data-nav-link><i class="bi bi-clock-history me-2"></i>Log Aktifitas</a>
		@elseif($role === 'petugas')
			<a class="nav-link {{ $currentRoute === 'petugas.peminjaman.list' ? 'active' : '' }}" href="{{ route('petugas.peminjaman.list') }}" data-nav-link><i class="bi bi-check-circle me-2"></i>Menyetujui Peminjaman</a>
			<a class="nav-link {{ $currentRoute === 'petugas.pengembalian.list' ? 'active' : '' }}" href="{{ route('petugas.pengembalian.list') }}" data-nav-link><i class="bi bi-eye me-2"></i>Memantau Pengembalian</a>
			<a class="nav-link" href="#" data-nav-link><i class="bi bi-printer me-2"></i>Mencetak Laporan</a>
		@elseif($role === 'peminjam')
			<a class="nav-link" href="#" data-nav-link><i class="bi bi-list-ul me-2"></i>Daftar Alat</a>
			<a class="nav-link" href="#" data-nav-link><i class="bi bi-plus-circle me-2"></i>Ajukan Peminjaman</a>
			<a class="nav-link" href="#" data-nav-link><i class="bi bi-arrow-return-right me-2"></i>Pengembalian Saya</a>
			<a class="nav-link" href="#" data-nav-link><i class="bi bi-clock-history me-2"></i>Riwayat Peminjaman</a>
		@endif
	</nav>

	<p class="text-muted px-2 mb-3 mt-4" style="font-size: 0.75rem;">GENERAL</p>
	<nav class="nav flex-column">
		<a class="nav-link {{ $currentRoute === 'profile' ? 'active' : '' }}" href="{{ route('profile') }}" data-nav-link>
			<i class="bi bi-person-circle me-2"></i>Profil
		</a>
				
		<form id="logoutForm" method="POST" action="{{ route('auth.logout') }}" class="d-none">
			@csrf
		</form>
		<a class="nav-link" href="#" onclick="confirmLogout(event)" data-ignore-active>
			<i class="bi bi-box-arrow-right me-2"></i>Logout
		</a>
	</nav>
</div>

<style>
	.sidebar {
		background-color: white;
		min-height: 100vh;
		box-shadow: 2px 0 10px rgba(0,0,0,0.05);
	}

	.nav-item {
		margin: 0.25rem 0;
	}

	.nav-link {
		color: #6B7280;
		padding: 0.75rem 1.25rem;
		border-radius: 0.5rem;
		transition: all 0.3s;
		text-decoration: none;
		display: block;
	}

	.nav-link:hover, .nav-link.active {
		background-color: #E8F5E9;
		color: #2D6F4E;
	}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
	const sidebar = document.querySelector('.sidebar');
	if (!sidebar) return;
	const navLinks = sidebar.querySelectorAll('[data-nav-link]');
	navLinks.forEach(link => {
		link.addEventListener('click', function () {
			navLinks.forEach(item => item.classList.remove('active'));
			this.classList.add('active');
		});
	});
});
</script>
@endpush