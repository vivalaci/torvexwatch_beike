@addStyle(asset('vendor/swiper/swiper-bundle.min.css'))
@addScript(asset('vendor/swiper/swiper-bundle.min.js'))

<section class="module-item {{ $design ? 'module-item-design' : ''}}" id="module-{{ $module_id }}">
  <div class="module-info  ">
    <div class="{{ $content['module_size'] ?? 'w-100' }}">
      <div class="swiper module-swiper-img-text-{{ $module_id }} module-img-text-slideshow">
        <div class="swiper-wrapper">
          @foreach($content['images'] as $image)
            <div class="swiper-slide">
              <div class="image-wrap @if(($image['type'] ?? 'image') === 'video') image-wrap--video @endif"
                @if(($image['type'] ?? 'image') !== 'video') style="background-image: url('{{ $image['image'] }}')" @endif>
                @if(($image['type'] ?? 'image') === 'video')
                <video class="lux-hero-bg-video" src="{{ $image['image'] }}" muted loop playsinline autoplay preload="metadata"></video>
                @endif
                <div class="container content-wrap {{ $image['text_position'] }}">
                  <div class="text-wrap lux-hero-text-wrap" data-swiper-parallax-y="-100" data-swiper-parallax-duration="1000" data-swiper-parallax-opacity="0.5" >
                    @if ($image['sub_title'])
                      <div class="sub-title lux-hero-sub">{{ $image['sub_title'] }}</div>
                    @endif
                    @if ($image['title'])
                      <h2 class="title lux-hero-title">{{ $image['title'] }}</h2>
                    @endif
                    @if ($image['description'])
                      <p class="description lux-hero-desc">{!! $image['description'] !!}</p>
                    @endif
                    @if ((isset($image['link']['link']) && $image['link']['link']) || (isset($image['link_2']['link']) && $image['link_2']['link']))
                      <div class="lux-hero-cta">
                        @if (isset($image['link']['link']) && $image['link']['link'])
                          <a href="{{ $image['link']['link'] }}" target="{{ !empty($image['link']['new_window']) ? '_blank' : '_self' }}" rel="{{ !empty($image['link']['new_window']) ? 'noopener' : '' }}" class="btn lux-hero-btn">
                            {{ ($image['link']['text'] ?? '') !== '' ? $image['link']['text'] : __('shop/account.check_details') }}
                          </a>
                        @endif
                        @if (isset($image['link_2']['link']) && $image['link_2']['link'])
                          <a href="{{ $image['link_2']['link'] }}" target="{{ !empty($image['link_2']['new_window']) ? '_blank' : '_self' }}" rel="{{ !empty($image['link_2']['new_window']) ? 'noopener' : '' }}" class="btn lux-hero-btn lux-hero-btn--outline">
                            {{ ($image['link_2']['text'] ?? '') !== '' ? $image['link_2']['text'] : __('shop/home.hero_cta_secondary') }}
                          </a>
                        @endif
                      </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="swiper-pagination slideshow-pagination-{{ $module_id }}"></div>
      </div>

      @if ($content['scroll_text']['text'])
        <div class="module-swiper-img-scroll-text" style="
          background-color: {{ $content['scroll_text']['bg'] }};
          color: {{ $content['scroll_text']['color'] }};
          font-size: {{ $content['scroll_text']['font_size'] }}px;
          padding: {{ $content['scroll_text']['padding'] }}px 0;
          ">
          <div class="scroll-info">
            <span class="scroll-text">{{ $content['scroll_text']['text'] }}</span>
          </div>
        </div>
      @endif
    </div>
  </div>

  @php
    $luxSwiperHeroCount = count($content['images'] ?? []);
    $luxSwiperHeroLoop = $luxSwiperHeroCount >= 2;
  @endphp
  <script>
    var moduleSwiperImgText_{{ $module_id }} = new Swiper ('.module-swiper-img-text-{{ $module_id }}', {
      loop: @json($luxSwiperHeroLoop),
      parallax : true,
      pauseOnMouseEnter: true,
      clickable :true,
      effect: 'fade',

      pagination: {
        el: '.slideshow-pagination-{{ $module_id }}',
        clickable: true
      },

      autoplay: {
        delay: 3000,
        disableOnInteraction: false
      },

      on: {
        init: function () {
          $('.slideshow-pagination-{{ $module_id }} .swiper-pagination-bullet').append('<span></span>')
        },
        autoplayTimeLeft(s, time, progress) {
          $('.slideshow-pagination-{{ $module_id }} .swiper-pagination-bullet-active span').css('width', (1 - progress) * 100 + '%')
        },
        slideChange: function () {
          $('.slideshow-pagination-{{ $module_id }} .swiper-pagination-bullet span').css('width', '0')
        },
      }
    })

    $('.module-img-text-slideshow').hover(function() {
      moduleSwiperImgText_{{ $module_id }}.autoplay.pause();
    }, function() {
      moduleSwiperImgText_{{ $module_id }}.autoplay.resume();
    });

    $(function () {
      scrollTextFun()

      function scrollTextFun() {
        var $module = $('.module-swiper-img-text-{{ $module_id }}').next('.module-swiper-img-scroll-text');
        if (!$module.length) {
          return;
        }

        var $scrollText = $module.find('.scroll-text');
        var scrollText = $scrollText.text();
        var scrollTextWidth = $scrollText.width();
        var scrollInfoWidth = $module.width();

        // 滚动速度（像素/秒）
        var speed = 100; // 可调整滚动速度
        var duration = scrollTextWidth / speed; // 动画持续时间，单位：秒

        // 计算需要重复的次数，确保内容填满容器
        var scrollCount = Math.ceil(scrollInfoWidth / scrollTextWidth) + 1;
        var scrollTextHtml = '';

        // 拼接滚动内容
        for (var i = 0; i < scrollCount; i++) {
          scrollTextHtml +=
            '<span class="scroll-text" style="animation-duration: ' + duration + 's;">' +
            scrollText +
            '</span>';
        }

        // 更新滚动区域内容
        $module.find('.scroll-info').html(scrollTextHtml);
      }
    })
  </script>
</section>



