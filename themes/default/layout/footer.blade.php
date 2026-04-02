<footer>
  @hook('footer.before')

  <div class="container-fluid">
    @hook('footer.services.before')

    @if ($footer_content['services']['enable'])
      {{-- 二开：替代原服务图标，Bob's 风格「Our Promise」条（后台仍用「服务图标」开关控制显示） --}}
      <div class="lux-footer-promise">
        <div class="lux-footer-promise__inner">
          <div class="row lux-footer-promise__main align-items-start">
            <div class="col-12 col-lg-3 mb-3 mb-lg-0 lux-footer-promise__title-wrap">
              <h2 class="lux-footer-promise__title">Our Promise</h2>
            </div>
            <div class="col-12 col-lg-9">
              <div class="row lux-footer-promise__cols g-0">
                {{-- 竖线：仅在「标题区｜第1栏」与「第1栏｜第2栏」；第3栏左侧无竖线（对标 Bob's） --}}
                <div class="col-12 col-md-4 lux-footer-promise__col lux-footer-promise__col--divider">
                  <h3 class="lux-footer-promise__col-title">Pricing Transparency</h3>
                  <p class="lux-footer-promise__col-text">We publish the buy and sell prices for all watches to honor the fair market value.</p>
                </div>
                <div class="col-12 col-md-4 lux-footer-promise__col lux-footer-promise__col--divider">
                  <h3 class="lux-footer-promise__col-title">Real-Time Inventory</h3>
                  <p class="lux-footer-promise__col-text">All watches are in stock and ready for sale. New Arrivals are updated hourly.</p>
                </div>
                <div class="col-12 col-md-4 lux-footer-promise__col">
                  <h3 class="lux-footer-promise__col-title">Free Shipping</h3>
                  <p class="lux-footer-promise__col-text">All purchases are eligible for free, overnight shipping.</p>
                </div>
              </div>
            </div>
          </div>
          <div class="row lux-footer-promise__actions-row">
            <div class="col-lg-3 d-none d-lg-block" aria-hidden="true"></div>
            <div class="col-12 col-lg-9 lux-footer-promise__actions">
              <a class="lux-footer-promise__btn" href="#">Our Authentication Pledge</a>
            </div>
          </div>
        </div>
      </div>
    @endif

    @hook('footer.services.after')

    @include('layout.footer-about-story')

    @include('layout.footer-reviews-press')

    <div class="footer-content">
      <div class="row">
        <div class="col-12 col-md-3 me-lg-5">
          <div class="footer-content-left footer-link-wrap">
            <h6 class="text-uppercase intro-title">{{ __('shop/common.company_profile') }}<span class="icon-open"><i class="bi bi-plus-lg"></i></span></h6>
            <div class="intro-wrap">
              @if ($footer_content['content']['intro']['logo'] ?? false)
                <div class="logo"><a href="{{ shop_route('home.index') }}"><img src="{{ image_origin($footer_content['content']['intro']['logo']) }}" alt="{{ system_setting('base.meta_title', 'BeikeShop开源好用的跨境电商系统') }}" class="img-fluid"></a></div>
              @endif
              <div class="text">{!! $footer_content['content']['intro']['text'][locale()] ?? '' !!}</div>
              <div class="social-network">
                @foreach ($footer_content['content']['intro']['social_network'] ?? [] as $item)
                <a href="{{ $item['link'] }}" target="_blank"><img src="{{ image_origin($item['image']) }}" class="img-fluid"></a>
                @endforeach
              </div>
            </div>
          </div>
        </div>
        @for ($i = 1; $i <= 4; $i++)
          @php
            $link = $footer_content['content']['link' . $i];
          @endphp
          @if ($design || ($link['title'][locale()] ?? false))
          <div class="col-12 col-md footer-content-link{{ $i }} footer-link-wrap">
            <h6 class="text-uppercase">{{ $link['title'][locale()] ?? '' }}<span class="icon-open"><i class="bi bi-plus-lg"></i></span></h6>
            <ul class="list-unstyled">
              @foreach ($link['links'] as $item)
                @if ($item['link'])
                <li class="lh-lg">
                  <a href="{{ $item['link'] }}" @if (isset($item['new_window']) && $item['new_window']) target="_blank" @endif>
                    {{ $item['text'] }}
                  </a>
                </li>
              @endif
              @endforeach
            </ul>
          </div>
          @endif
        @endfor

        {{-- 二开：不展示「联系我们」列，扩展链接栏至第 4 列编辑 --}}
        @hook('footer.contact.before')
        @hookwrapper('footer.contact')
        @endhookwrapper
        @hook('footer.contact.after')
      </div>
    </div>
  </div>

  @hookwrapper('footer.copyright')
  @if (isset($footer_content['bottom']['image']) && $footer_content['bottom']['image'])
  <div class="footer-bottom">
    <div class="container-fluid">
      <div class="d-lg-flex align-items-center justify-content-center">
        <div class="ms-auto right-img py-md-2 text-center">
          <img src="{{ image_origin($footer_content['bottom']['image']) }}" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
  @endif
  @endhookwrapper

  @hook('footer.after')
</footer>
