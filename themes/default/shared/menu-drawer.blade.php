{{-- 全站侧栏菜单：上白（后台菜单树）下黑（联系与账号），对标 Bob's 式抽屉 --}}
<div class="lux-menu-drawer d-flex flex-column flex-grow-1">
  <div class="lux-drawer-primary flex-grow-1 overflow-auto">
    <div class="mobile-menu-wrap">
      @include('shared.menu-mobile')
    </div>
  </div>

  <div class="lux-drawer-util flex-shrink-0">
    @hook('header.menu.drawer.util.before')
    <ul class="lux-drawer-util-list list-unstyled mb-0">
      @auth('web_shop')
        <li>
          <a href="{{ shop_route('account.index') }}" class="lux-drawer-util-link">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span>{{ current_customer()->name }}</span>
          </a>
        </li>
      @else
        <li>
          <a href="{{ shop_route('login.index') }}" class="lux-drawer-util-link">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span>{{ __('shop/header.drawer_sign_in') }}</span>
          </a>
        </li>
      @endauth

      <li>
        <a href="#" class="lux-drawer-util-link lux-drawer-locations-stub" onclick="return false;">
          <i class="bi bi-geo-alt" aria-hidden="true"></i>
          <span>{{ __('shop/header.drawer_locations') }}</span>
        </a>
      </li>

      @if (system_setting('base.telephone', ''))
        <li>
          <a href="tel:{{ preg_replace('/\s+/', '', system_setting('base.telephone')) }}" class="lux-drawer-util-link">
            <i class="bi bi-telephone" aria-hidden="true"></i>
            <span>{{ system_setting('base.telephone') }}</span>
          </a>
        </li>
      @endif

      @if (system_setting('base.email', ''))
        <li>
          <a href="mailto:{{ system_setting('base.email') }}" class="lux-drawer-util-link">
            <i class="bi bi-envelope" aria-hidden="true"></i>
            <span>{{ __('shop/header.drawer_contact') }}</span>
          </a>
        </li>
      @endif

      <li>
        <a href="#" class="lux-drawer-util-link lux-drawer-chat-stub" onclick="return false;">
          <i class="bi bi-chat-dots" aria-hidden="true"></i>
          <span>{{ __('shop/header.drawer_chat') }}</span>
        </a>
      </li>
    </ul>
    @hook('header.menu.drawer.util.after')
  </div>
</div>
