	@if(session('success') || session('error'))
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			@if(session('success'))
				Swal.fire({
					icon: 'success',
					title: 'Berhasil!',
					text: @json(session('success')),
					showConfirmButton: false,
					timer: 2000,
					timerProgressBar: true
				});
			@endif

			@if(session('error'))
				Swal.fire({
					icon: 'error',
					title: 'Terjadi Kesalahan',
					text: @json(session('error')),
					showConfirmButton: false,
					timer: 2000,
					timerProgressBar: true
				});
			@endif
		});
	</script>
	@endif
