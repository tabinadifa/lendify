@php
	$userName = optional(auth()->user())->name ?? session('user_name') ?? 'Pengguna';
@endphp

<header class="d-flex align-items-center justify-content-between gap-3 py-3 px-4 bg-white border-bottom shadow-sm flex-wrap">
	<button type="button" class="btn btn-outline-secondary d-lg-none" aria-label="Buka menu" onclick="toggleSidebar()">
		<i class="bi bi-list"></i>
	</button>

	<div class="dropdown ms-auto">
		<button class="btn btn-link text-decoration-none text-dark dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
			<i class="bi bi-person-circle fs-5 text-secondary"></i>
			<span class="fw-semibold">{{ $userName }}</span>
		</button>
		<ul class="dropdown-menu dropdown-menu-end shadow border-0">
			<li>
				<a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('profile') }}">
					<i class="bi bi-person"></i>
					<span>Profile</span>
				</a>
			</li>
			<li><hr class="dropdown-divider my-1"></li>
			<li>
				<button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger" onclick="confirmLogout(event)">
					<i class="bi bi-box-arrow-right"></i>
					<span>Logout</span>
				</button>
			</li>
		</ul>
	</div>
</header>
