<header @class(['lux-header-over-hero' => request()->routeIs('shop.home.index')])>
  @hook('header.before')
  <div class="top-wrap">
    <div class="container-fluid header-top-inner">
      <div class="header-top-left left d-flex align-items-center">
        @hookwrapper('header.top.currency')
        @if (currencies()->count() > 1)
          <div class="dropdown">
            <a class="btn dropdown-toggle ps-0" href="javascript:void(0)" role="button" id="currency-dropdown"
               data-toggle="dropdown"
               aria-expanded="false">
              @foreach (currencies() as $currency)
                @if ($currency->code == current_currency_code())
                  @if ($currency->symbol_left)
                    {{ $currency->symbol_left }}
                  @endif
                  {{ $currency->name }}
                  @if ($currency->symbol_right)
                    {{ $currency->symbol_right }}
                  @endif
                @endif
              @endforeach
            </a>

            <div class="dropdown-menu" aria-labelledby="currency-dropdown">
              @foreach (currencies() as $currency)
                <a class="dropdown-item"
                   href="{{ shop_route('currency.switch', [$currency->code]) }}">
                  @if ($currency->symbol_left)
                    {{ $currency->symbol_left }}
                  @endif
                  {{ $currency->name }}
                  @if ($currency->symbol_right)
                    {{ $currency->symbol_right }}
                  @endif
                </a>
              @endforeach
            </div>
          </div>
        @endif
        @endhookwrapper

        @hookwrapper('header.top.language')
        @if (count($languages) > 1)
          <div class="dropdown">
            <a class="btn dropdown-toggle" href="javascript:void(0)" role="button" id="language-dropdown"
               data-toggle="dropdown"
               aria-expanded="false">
              {{ current_language()->name }}
            </a>

            <div class="dropdown-menu" aria-labelledby="language-dropdown">
              @foreach ($languages as $language)
                <a class="dropdown-item" href="{{ shop_route('lang.switch', [$language->code]) }}">
                  {{ $language->name }}
                </a>
              @endforeach
            </div>
          </div>
        @endif
        @endhookwrapper

        @hook('header.top.left')
      </div>

      @hook('header.top.language.after')

      <div class="header-top-center d-none d-md-flex align-items-center justify-content-center">
        <div class="header-top-trust-wrap">
          <button type="button" class="header-top-trust-trigger">
            <i class="bi bi-shield-fill-check header-top-trust-icon" aria-hidden="true"></i>
            <span class="header-top-trust-label">{{ __('shop/header.top_trust_line') }}</span>
            <i class="bi bi-chevron-down header-top-trust-chevron" aria-hidden="true"></i>
          </button>
          <div id="header-top-trust-popover" class="header-top-trust-popover" role="tooltip">
            <p class="header-top-trust-popover-title">{{ __('shop/header.trust_popover_title') }}</p>
            <p class="header-top-trust-popover-body mb-0">{{ __('shop/header.trust_popover_body') }}</p>
          </div>
        </div>
      </div>

      <div class="header-top-right right nav d-flex align-items-center justify-content-end">
        @if (system_setting('base.telephone', ''))
          @hookwrapper('header.top.telephone')
          <div class="my-auto"><i class="bi bi-telephone-forward me-2"></i> {{ system_setting('base.telephone') }}</div>
          @endhookwrapper
        @endif

        @hook('header.top.right')
      </div>
    </div>
  </div>

  <div class="header-content d-none d-lg-block">
    <div class="container-fluid lux-header-inner navbar-expand-lg">
      <div class="header-left">
        <nav class="menu-wrap d-none" aria-hidden="true" aria-label="{{ __('shop/header.main_navigation') }}">
          @include('shared.menu-pc')
        </nav>
        <div class="lux-header-quick-nav">
          <a href="#locations" class="lux-header-quick-link lux-header-quick-link--icon" aria-label="Locations">
            <i class="bi bi-geo-alt" aria-hidden="true"></i>
          </a>
          <a href="{{ system_setting('base.telephone') ? 'tel:'.system_setting('base.telephone') : '#contact' }}" class="lux-header-quick-link">
            <i class="bi bi-telephone" aria-hidden="true"></i>Contact Us
          </a>
          <a href="{{ shop_route('page_categories.home') }}" class="lux-header-quick-link">Luxury Watches</a>
          <a href="#sell" class="lux-header-quick-link">Sell Your Watch</a>
        </div>
      </div>
      @hookwrapper('header.menu.logo')
      <div class="logo"><a href="{{ shop_route('home.index') }}">
          <img src="{{ image_origin(system_setting('base.logo')) }}" class="img-fluid lux-logo-default" alt="{{ system_setting('base.meta_title', 'BeikeShop开源好用的跨境电商系统') }}">
          <img src="{{ asset('image/logo-white.png') }}" class="img-fluid lux-logo-white" alt="{{ system_setting('base.meta_title', 'BeikeShop开源好用的跨境电商系统') }}"></a>
      </div>
      @endhookwrapper
      <div class="header-right">
        <div class="right-btn">
        <ul class="navbar-nav flex-row align-items-center">
          @hookwrapper('header.menu.icon')
          {{-- 桌面搜索框（仅 lg+）：直接输入，回车跳转搜索结果 --}}
          <li class="nav-item d-none d-lg-flex align-items-center">
            <form action="{{ shop_route('products.search') }}" method="GET" class="lux-search-form" role="search">
              <div class="lux-search-box">
                <input type="search" name="keyword" class="lux-search-input"
                       placeholder="Search"
                       autocomplete="off"
                       value="{{ request('keyword') }}">
                <button type="submit" class="lux-search-submit" aria-label="{{ __('common.search') }}">
                  <i class="bi bi-search lux-search-box-icon" aria-hidden="true"></i>
                </button>
              </div>
            </form>
          </li>
          {{-- 移动端搜索图标（lg 以下） --}}
          <li class="nav-item d-lg-none"><a href="#offcanvas-search-top" data-bs-toggle="offcanvas" class="nav-link"><img src="{{ asset('image/icons/search.svg') }}" class="img-fluid"></a></li>
          <li class="nav-item d-none"><a href="{{ shop_route('account.wishlist.index') }}" class="nav-link"><img src="{{ asset('image/icons/favorite.svg') }}" class="img-fluid"></a></li>
          <li class="nav-item dropdown">
            <a href="{{ shop_route('account.index') }}" class="nav-link"><i class="bi bi-person lux-nav-icon" aria-hidden="true"></i></a>
            <ul class="dropdown-menu">
              @auth('web_shop')
                <li class="dropdown-item">
                  <a href="{{ shop_route('account.index') }}" class="fw-bold dropdown-item p-0">{{ current_customer()->name }}</a>
                </li>
                <li>
                  <hr class="dropdown-divider opacity-100">
                </li>
                <li><a href="{{ shop_route('account.index') }}" class="dropdown-item"><i class="bi bi-person me-1"></i>
                    {{ __('shop/account.index') }}</a></li>
                <li><a href="{{ shop_route('account.order.index') }}" class="dropdown-item"><i
                      class="bi bi-clipboard-check me-1"></i> {{ __('shop/account/order.index') }}</a></li>
                @hook('header.menu.icon.dropdown.menu.order.after')
                <li><a href="{{ shop_route('account.wishlist.index') }}" class="dropdown-item"><i
                      class="bi bi-heart me-1"></i> {{ __('shop/account/wishlist.index') }}</a></li>
                <li>
                  <hr class="dropdown-divider opacity-100">
                </li>
                <li><a href="{{ shop_route('logout') }}" class="dropdown-item"><i class="bi bi-box-arrow-left me-1"></i>
                    {{ __('common.sign_out') }}</a></li>
              @else
                <li><a href="{{ shop_route('login.index') }}" class="dropdown-item"><i
                      class="bi bi-box-arrow-right me-1"></i>{{ __('shop/login.login_and_sign') }}</a></li>
              @endauth
            </ul>
          </li>
          @endhookwrapper
          <li class="nav-item">
            <a
              class="nav-link position-relative btn-right-cart {{ equal_route('shop.carts.index') ? 'page-cart' : '' }}"
              href="javascript:void(0);" role="button">
              <i class="bi bi-bag lux-nav-icon" aria-hidden="true"></i>
              <span class="cart-badge-quantity"></span>
            </a>
          </li>
          <li class="nav-item d-none d-lg-flex align-items-center">
            <button type="button" class="btn btn-link nav-link py-2 px-2 lux-desktop-menu-btn"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvas-mobile-menu"
                    aria-controls="offcanvas-mobile-menu" aria-label="{{ __('shop/header.open_full_menu') }}">
              <i class="bi bi-list lux-desktop-menu-icon" aria-hidden="true"></i>
            </button>
          </li>
        </ul>
      </div>
      </div>
    </div>
  </div>

  <div class="header-mobile d-lg-none">
    <div class="mobile-content">
      <div class="left">
        <div class="mobile-open-menu"><img src="{{ asset('image/icons/menu.svg') }}" alt="menu" class="img-fluid"></div>
        <div class="mobile-open-search" href="#offcanvas-search-top" data-bs-toggle="offcanvas">
             <img src="{{ asset('image/icons/search.svg') }}" class="img-fluid" alt="search">
        </div>
      </div>
      <div class="center"><a href="{{ shop_route('home.index') }}">
          <img src="{{ image_origin(system_setting('base.logo')) }}" class="img-fluid" alt="{{ system_setting('base.meta_title', 'BeikeShop开源好用的跨境电商系统') }}"></a>
      </div>
      <div class="right">
        <a href="{{ shop_route('account.index') }}" class="nav-link mb-account-icon">
          <img src="{{ asset('image/icons/account.svg') }}" class="img-fluid" alt="account">
          @if (strstr(current_route(), 'shop.account'))
            <span></span>
          @endif
        </a>
        <a href="{{ shop_route('carts.index') }}" class="nav-link ms-3 m-cart position-relative"><img src="{{ asset('image/icons/cart.svg') }}" alt="cart" class="img-fluid">
          <span class="cart-badge-quantity"></span></a>
      </div>
    </div>
  </div>
  <div class="offcanvas offcanvas-end lux-offcanvas-menu" tabindex="-1" id="offcanvas-mobile-menu" aria-labelledby="offcanvasMobileMenuLabel">
    <div class="offcanvas-header lux-offcanvas-menu-header border-0 flex-shrink-0">
      <a href="{{ shop_route('home.index') }}" class="lux-drawer-logo" data-bs-dismiss="offcanvas">
        <img src="{{ image_origin(system_setting('base.logo')) }}" class="img-fluid" alt="{{ system_setting('base.meta_title', '') }}">
      </a>
      <span class="visually-hidden" id="offcanvasMobileMenuLabel">{{ __('common.menu') }}</span>
      <button type="button" class="btn-close lux-drawer-close-btn" data-bs-dismiss="offcanvas" aria-label="{{ __('shop/header.drawer_close') }}"></button>
    </div>
    <div class="offcanvas-body lux-offcanvas-menu-body p-0">
      @include('shared.menu-drawer')
    </div>
  </div>

  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas-right-cart" aria-labelledby="offcanvasRightLabel"></div>

  <x-shop-search-popover />

  @hook('header.after')
</header>
