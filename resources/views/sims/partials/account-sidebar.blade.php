<aside class="acct-sidebar">
    <nav>
        <ul>
            <li>
                <a href="{{ route('account.index') }}"
                   @if(($activeNav ?? '') === 'profile') class="active" aria-current="page" @endif>
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li>
                <a href="{{ url('/account/orders') }}"
                   @if(($activeNav ?? '') === 'orders') class="active" aria-current="page" @endif>
                    <i class="fas fa-shopping-bag"></i> My Orders
                </a>
            </li>
            <li>
                <a href="{{ route('account.change-password') }}"
                   @if(($activeNav ?? '') === 'password') class="active" aria-current="page" @endif>
                    <i class="fas fa-lock"></i> Change Password
                </a>
            </li>
            <li>
                <a href="{{ url('/logout') }}"
                   onclick="event.preventDefault(); document.getElementById('acct-logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>
<form id="acct-logout-form" action="{{ url('/logout') }}" method="POST" style="display:none;">@csrf</form>
