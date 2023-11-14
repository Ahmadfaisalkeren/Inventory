<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      @if (auth()->user()->role == "superadmin")
        <li class="nav-item">
            <a href="{{ route('inventories.index') }}" class="nav-link">
            <i class="nav-icon fas fa-calendar-alt"></i>
            <p>
                Inventory
            </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('sales.index') }}" class="nav-link">
            <i class="nav-icon far fa-image"></i>
            <p>
                Sales
            </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('get.purchase.index') }}" class="nav-link">
            <i class="nav-icon fas fa-columns"></i>
            <p>
                Purchases
            </p>
            </a>
        </li>

      @elseif (auth()->user()->role == 'sales')
        <li class="nav-item">
            <a href="{{ route('sales.index') }}" class="nav-link">
            <i class="nav-icon far fa-image"></i>
            <p>
                Sales
            </p>
            </a>
        </li>
      @elseif (auth()->user()->role == 'purchase')
        <li class="nav-item">
            <a href="{{ route('get.purchase.index') }}" class="nav-link">
            <i class="nav-icon fas fa-columns"></i>
            <p>
                Purchases
            </p>
            </a>
        </li>
      @elseif (auth()->user()->role == 'manager')
        <li class="nav-item">
            <a href="{{ route('sales.index') }}" class="nav-link">
            <i class="nav-icon far fa-image"></i>
            <p>
                Sales
            </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('get.purchase.index') }}" class="nav-link">
            <i class="nav-icon fas fa-columns"></i>
            <p>
                Purchases
            </p>
            </a>
        </li>
      @endif
      <li class="nav-item">
        <a href="#" class="nav-link" onclick="logoutConfirmation();">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>
                Logout
            </p>
        </a>
      </li>
    </ul>
</nav>

@push('scripts')
    <script>
        function logoutConfirmation() {
            Swal.fire({
                title: 'Ready to Leave?',
                text: 'You are sure want to Logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Logout',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
@endpush
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>
