{{-- 全站侧栏菜单：上白（后台菜单树）下黑（联系与账号），对标 Bob's 式抽屉 --}}
<div class="lux-menu-drawer">
  <div class="lux-drawer-primary">

    {{-- 横滑商品卡片区（数据来自首页第一个 product 模块） --}}
    @if (!empty($drawer_products))
    <div class="lux-drawer-cards">
      <div class="lux-drawer-cards-scroll" id="drawerCardsScroll">
        @foreach ($drawer_products as $product)
        <a href="{{ $product['url'] }}" class="lux-drawer-card text-decoration-none" data-bs-dismiss="offcanvas">
          <div class="lux-drawer-card-img">
            <img src="{{ $product['images'][0] ?? image_resize('', 300, 300) }}" alt="{{ $product['name'] }}" loading="lazy">
          </div>
          <div class="lux-drawer-card-name">{{ $product['name'] }}</div>
        </a>
        @endforeach
      </div>
      <div class="lux-drawer-cards-track" id="drawerCardsTrack">
        <div class="lux-drawer-cards-progress" id="drawerCardsProgress"></div>
      </div>
    </div>
    @endif

    <div class="mobile-menu-wrap">
      @include('shared.menu-mobile')
    </div>
  </div>

  <div class="lux-drawer-util">
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

@if (!empty($drawer_products))
<script>
(function () {
  var offcanvas = document.getElementById('offcanvas-mobile-menu');
  if (!offcanvas) return;
  var bound = false;

  offcanvas.addEventListener('shown.bs.offcanvas', function () {
    var scroll = document.getElementById('drawerCardsScroll');
    var track  = document.getElementById('drawerCardsTrack');
    var bar    = document.getElementById('drawerCardsProgress');
    if (!scroll || !track || !bar) return;

    function maxScroll() { return scroll.scrollWidth - scroll.clientWidth; }

    function updateBar() {
      var total = scroll.scrollWidth;
      if (total <= scroll.clientWidth) {
        track.style.display = 'none';
        return;
      }
      track.style.display = '';
      var ratio = scroll.clientWidth / total;
      var trackW = track.clientWidth;
      var barW = Math.max(ratio * trackW, 30);
      bar.style.width = barW + 'px';
      var max = maxScroll();
      var pct = max > 0 ? scroll.scrollLeft / max : 0;
      bar.style.left = (pct * (trackW - barW)) + 'px';
    }

    updateBar();
    requestAnimationFrame(updateBar);

    if (bound) return;
    bound = true;

    scroll.addEventListener('scroll', updateBar, { passive: true });
    window.addEventListener('resize', updateBar);

    // 桌面：滚轮纵向 → 横向
    scroll.addEventListener('wheel', function (e) {
      if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
        scroll.scrollLeft += e.deltaY;
        e.preventDefault();
      }
    }, { passive: false });

    // 桌面：在卡片区按住鼠标拖拽
    var cardsDown = false, cardsStartX = 0, cardsStartLeft = 0, cardsMoved = false;
    scroll.addEventListener('mousedown', function (e) {
      cardsDown = true; cardsMoved = false;
      cardsStartX = e.pageX; cardsStartLeft = scroll.scrollLeft;
    });
    window.addEventListener('mousemove', function (e) {
      if (!cardsDown) return;
      var dx = e.pageX - cardsStartX;
      if (Math.abs(dx) > 3) { cardsMoved = true; scroll.classList.add('is-dragging'); }
      scroll.scrollLeft = cardsStartLeft - dx;
    });
    window.addEventListener('mouseup', function () {
      if (!cardsDown) return;
      cardsDown = false;
      setTimeout(function(){ scroll.classList.remove('is-dragging'); }, 0);
    });
    scroll.addEventListener('click', function (e) {
      if (cardsMoved) { e.preventDefault(); e.stopPropagation(); }
    }, true);

    // 进度条：点击轨道跳转 + 拖拽滑块
    function scrollToRatio(pct) {
      scroll.scrollLeft = maxScroll() * Math.max(0, Math.min(1, pct));
    }

    track.addEventListener('mousedown', function (e) {
      if (e.target === bar) return; // 交给滑块拖拽
      var rect = track.getBoundingClientRect();
      var barW = bar.offsetWidth;
      var pct = (e.clientX - rect.left - barW / 2) / (rect.width - barW);
      scrollToRatio(pct);
    });

    var barDown = false, barStartX = 0, barStartLeft = 0;
    bar.addEventListener('mousedown', function (e) {
      barDown = true;
      barStartX = e.clientX;
      barStartLeft = parseFloat(bar.style.left) || 0;
      bar.classList.add('is-dragging');
      e.preventDefault();
      e.stopPropagation();
    });
    window.addEventListener('mousemove', function (e) {
      if (!barDown) return;
      var dx = e.clientX - barStartX;
      var trackW = track.clientWidth;
      var barW = bar.offsetWidth;
      var newLeft = barStartLeft + dx;
      var pct = newLeft / (trackW - barW);
      scrollToRatio(pct);
    });
    window.addEventListener('mouseup', function () {
      if (!barDown) return;
      barDown = false;
      bar.classList.remove('is-dragging');
    });
  });
})();
</script>
@endif
