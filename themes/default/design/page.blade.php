<section class="module-item {{ $design ? 'module-item-design' : ''}}" id="module-{{ $module_id }}">
  <div class="module-info module-pages lux-pages-bob">
    <div class="{{ $content['module_size'] ?? 'container-fluid' }} position-relative">
      @if (!empty($content['title']))
      <h2 class="lux-pages-bob__heading">{{ $content['title'] }}</h2>
      @endif

      @if (!empty($content['items']))
        @php
          $luxPageItems = $content['items'];
          $luxFirstRow = array_slice($luxPageItems, 0, 3);
          $luxSecondRow = array_slice($luxPageItems, 3, 3);
        @endphp

        <div class="row g-4 lux-pages-bob__grid">
          @foreach ($luxFirstRow as $item)
          <div class="col-12 col-sm-6 col-lg-4">
            <article class="lux-pages-bob-card">
              <a href="{{ $item['url'] ?? shop_route('pages.show', ['page' => $item['id']]) }}" class="lux-pages-bob-card__link-img">
                <img src="{{ $item['image'] }}" class="seo-img" alt="{{ $item['title'] ?? '' }}" loading="lazy">
              </a>
              <div class="lux-pages-bob-card__meta">{{ trim($item['category_name'] ?? '') ?: 'Editorial' }}</div>
              <h3 class="lux-pages-bob-card__title">
                <a href="{{ $item['url'] ?? shop_route('pages.show', ['page' => $item['id']]) }}">{{ $item['title'] ?? '' }}</a>
              </h3>
            </article>
          </div>
          @endforeach
        </div>

        @if (count($luxSecondRow))
        <div class="row g-4 lux-pages-bob__grid lux-pages-bob__grid--second">
          @foreach ($luxSecondRow as $item)
          <div class="col-12 col-sm-6 col-lg-4">
            <article class="lux-pages-bob-card">
              <a href="{{ $item['url'] ?? shop_route('pages.show', ['page' => $item['id']]) }}" class="lux-pages-bob-card__link-img">
                <img src="{{ $item['image'] }}" class="seo-img" alt="{{ $item['title'] ?? '' }}" loading="lazy">
              </a>
              <div class="lux-pages-bob-card__meta">{{ trim($item['category_name'] ?? '') ?: 'Editorial' }}</div>
              <h3 class="lux-pages-bob-card__title">
                <a href="{{ $item['url'] ?? shop_route('pages.show', ['page' => $item['id']]) }}">{{ $item['title'] ?? '' }}</a>
              </h3>
            </article>
          </div>
          @endforeach
        </div>
        @endif

        <div class="lux-pages-bob__actions">
          <a class="lux-pages-bob__view-all" href="{{ shop_route('page_categories.home') }}">View all</a>
        </div>

      @elseif ($design)
        <div class="row g-4 lux-pages-bob__grid">
          @for ($s = 0; $s < 3; $s++)
          <div class="col-12 col-sm-6 col-lg-4">
            <article class="lux-pages-bob-card lux-pages-bob-card--placeholder">
              <a href="javascript:void(0)" class="lux-pages-bob-card__link-img">
                <img src="{{ asset('image/placeholder.png') }}" class="img-fluid" alt="">
              </a>
              <div class="lux-pages-bob-card__meta">Editorial</div>
              <h3 class="lux-pages-bob-card__title"><span>请配置文章</span></h3>
            </article>
          </div>
          @endfor
        </div>
        <div class="row g-4 lux-pages-bob__grid lux-pages-bob__grid--second">
          @for ($s = 0; $s < 3; $s++)
          <div class="col-12 col-sm-6 col-lg-4">
            <article class="lux-pages-bob-card lux-pages-bob-card--placeholder">
              <a href="javascript:void(0)" class="lux-pages-bob-card__link-img">
                <img src="{{ asset('image/placeholder.png') }}" class="img-fluid" alt="">
              </a>
              <div class="lux-pages-bob-card__meta">Editorial</div>
              <h3 class="lux-pages-bob-card__title"><span>请配置文章</span></h3>
            </article>
          </div>
          @endfor
        </div>
        <div class="lux-pages-bob__actions">
          <a class="lux-pages-bob__view-all" href="javascript:void(0)">View all</a>
        </div>
      @endif
    </div>
  </div>
</section>
