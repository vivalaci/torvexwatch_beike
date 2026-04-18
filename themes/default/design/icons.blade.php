@php $luxUseStrip = !empty($content['lux_icons_carousel']); @endphp
@if ($luxUseStrip)
@addStyle(asset('vendor/swiper/swiper-bundle.min.css'))
@addScript(asset('vendor/swiper/swiper-bundle.min.js'))
@endif
<section class="module-item {{ $design ? 'module-item-design' : ''}}" id="module-{{ $module_id }}" data-lux-icons-strip="{{ $luxUseStrip ? '1' : '0' }}">
@if ($luxUseStrip)
  <div class="module-info module-icons lux-icons-collections">
    <div class="{{ $content['module_size'] ?? 'container-fluid' }}">
      @if ($content['title'])
      <div class="module-title lux-icons-collections-page-title">{{ $content['title'] }}</div>
      @endif
      @if ($content['sub_title'])
      <div class="module-sub-title">{{ $content['sub_title'] }}</div>
      @endif

      <div class="lux-icons-swiper-root position-relative" data-lux-icons-swiper="{{ $module_id }}">
        <div class="swiper lux-icons-swiper lux-icons-swiper--{{ $module_id }}">
          <div class="swiper-wrapper">
            @foreach ($content['images'] as $image)
            @php
              $luxIconHref = !empty(trim((string) ($image['url'] ?? ''))) ? $image['url'] : 'javascript:void(0)';
            @endphp
            <div class="swiper-slide">
              <div class="lux-icons-collections-card">
                <a href="{{ $luxIconHref }}" class="lux-icons-collections-img-link text-decoration-none">
                  <div class="lux-icons-collections-img-wrap">
                    <img src="{{ $image['image'] }}" class="seo-img" alt="{{ $image['image_alt'] ?? '' }}" loading="lazy">
                  </div>
                </a>
                @if (!empty($image['text']))
                <a href="{{ $luxIconHref }}" class="lux-icons-collections-title-link text-decoration-none">
                  <p class="lux-icons-collections-title">{{ $image['text'] }}</p>
                </a>
                @endif
                <a href="{{ $luxIconHref }}" class="lux-icons-collections-cta">
                  @if (!empty($image['sub_text']))
                  {{ $image['sub_text'] }}
                  @else
                  Shop
                  @endif
                </a>
              </div>
            </div>
            @endforeach
          </div>
          <div class="swiper-button-prev lux-icons-swiper-nav lux-icons-swiper-prev--{{ $module_id }}" aria-label="{{ __('pagination.previous') }}"></div>
          <div class="swiper-button-next lux-icons-swiper-nav lux-icons-swiper-next--{{ $module_id }}" aria-label="{{ __('pagination.next') }}"></div>
        </div>
        {{-- 分页放在 swiper 外，避免 overflow 裁切且便于相对整栏居中 --}}
        <div class="swiper-pagination lux-icons-swiper-pagination lux-icons-swiper-pagination--{{ $module_id }} lux-icons-swiper-pagination--below"></div>
      </div>
    </div>
  </div>
@else
  <div class="module-info module-icons">
    <div class="{{ $content['module_size'] ?? 'container-fluid' }}">
    @php
      $floorVal = $content['floor'] ?? '';
      $floorUrl = '';
      if (is_array($floorVal)) {
        $floorUrl = trim((string)($floorVal[app()->getLocale()] ?? $floorVal['en'] ?? ''));
      } elseif (is_string($floorVal)) {
        $floorUrl = trim($floorVal);
      }
    @endphp
    @if ($content['sub_title'] || $content['title'] || $floorUrl)
    <div class="lux-icons-title-row">
      <div>
        @if ($content['sub_title'])
        <div class="lux-icons-pre-title">{{ $content['sub_title'] }}</div>
        @endif
        @if ($content['title'])
        <div class="module-title">{{ $content['title'] }}</div>
        @endif
      </div>
      @if ($floorUrl)
      <a href="{{ $floorUrl }}" class="lux-icons-shop-all">Shop All</a>
      @endif
    </div>
    @endif
      <div class="row g-3 g-lg-4">
        @foreach ($content['images'] as $image)
        <div class="col-4 col-lg">
          <a href="{{ $image['url'] ?: 'javascript:void(0)' }}" class="text-decoration-none">
            <div class="image-item d-flex justify-content-center mb-lg-3">
              <img src="{{ $image['image'] }}" class="img-fluid seo-img" alt="{{ $image['image_alt'] ?? ''}}">
            </div>
            @if ($image['text'])
            <p class="text-center text-dark mt-2 mb-0 title">{{ $image['text'] }}</p>
            @endif
            @if ($image['sub_text'])
            <p class="text-center text-secondary mt-2 mb-0 sub-title">{{ $image['sub_text'] }}</p>
            @endif
          </a>
        </div>
        @endforeach
      </div>
    </div>
  </div>
@endif
</section>

@if ($luxUseStrip)
@push('add-scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof Swiper === 'undefined') return;
  var el = document.querySelector('.lux-icons-swiper--{{ $module_id }}');
  if (!el) return;
  new Swiper(el, {
    slidesPerView: 1,
    spaceBetween: 0,
    speed: 450,
    watchOverflow: true,
    preventClicks: false,
    touchStartPreventDefault: false,
    breakpoints: {
      576: { slidesPerView: 2, spaceBetween: 0 },
      992: { slidesPerView: 4, spaceBetween: 0 }
    },
    navigation: {
      nextEl: '.lux-icons-swiper-next--{{ $module_id }}',
      prevEl: '.lux-icons-swiper-prev--{{ $module_id }}'
    },
    pagination: {
      el: '.lux-icons-swiper-pagination--{{ $module_id }}',
      clickable: true,
      dynamicBullets: false
    }
  });
});
</script>
@endpush
@endif
