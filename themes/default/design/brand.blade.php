<section class="module-item {{ $design ? 'module-item-design' : ''}}" id="module-{{ $module_id }}">
  <div class="module-info module-brand lux-brand-strip">
    @if (!empty($content['brands']))
      <div class="{{ $content['module_size'] ?? 'container-fluid' }} lux-brand-strip-inner">
        @if (!empty($content['title']))
          <div class="lux-brand-strip-heading">{{ $content['title'] }}</div>
        @endif
        <div class="lux-brand-strip-bar">
          <div class="lux-brand-strip-scroll-outer" data-lux-brand-strip>
            <button type="button" class="lux-brand-strip-nav lux-brand-strip-nav--prev" aria-label="{{ __('pagination.previous') }}">
              <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="lux-brand-strip-scroll">
              @foreach ($content['brands'] as $brand)
                <a href="{{ $brand['url'] }}" class="lux-brand-strip-item text-decoration-none">
                  <img src="{{ $brand['logo'] ?? asset('image/default/banner-1.png') }}" alt="{{ $brand['name'] }}" class="lux-brand-strip-img seo-img" loading="lazy" width="50" height="50">
                  <span class="lux-brand-strip-name">{{ $brand['name'] }}</span>
                </a>
              @endforeach
            </div>
            <button type="button" class="lux-brand-strip-nav lux-brand-strip-nav--next" aria-label="{{ __('pagination.next') }}">
              <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <div class="lux-brand-strip-footer">
          <a class="lux-brand-strip-all" href="{{ shop_route('brands.index') }}">{{ __('common.show_all') }}</a>
        </div>
      </div>
    @endif
  </div>
</section>
