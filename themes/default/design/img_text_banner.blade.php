<section class="module-item {{ $design ? 'module-item-design' : ''}}" id="module-{{ $module_id }}">
  <div class="module-info  ">
    <div class="img-text-banner-wrap {{ $content['module_size'] ?? 'container-fluid' }}">
      <div class="row {{ $content['image_position'] == 'left' ? 'flex-row-reverse' : '' }} mx-0">
        <div class="col-12 col-lg-6 px-0">
          <div class="text-wrap {{ $content['text_position'] == 'left' ? 'align-items-start text-start' : ($content['text_position'] == 'center' ? 'align-items-center text-center' : 'align-items-end text-end') }}" style="background-color: {{ $content['bg_color'] }}; color: {{ $content['text_color'] ?? '#222' }}">
            <div style="max-width: {{ $content['text_max_width'] ?? '1900' }}px">
              <div class="fs-2 fw-bold title">{{ $content['title'] }}</div>
              @if ($content['sub_title'])
              <div class="fs-4 sub-title mb-4">{{ $content['sub_title'] }}</div>
              @endif
              <p class="description">{{ $content['description'] }}</p>
              @if (!empty($content['show_sell_form']))
              <form class="sell-watch-form" id="sell-form-{{ $module_id }}" action="{{ $content['form_action'] ?? '' }}" method="GET" novalidate>
                <input type="email" name="email" class="sell-form-input" placeholder="{{ $content['form_email_placeholder'] ?? 'Email Address' }}" required>
                <select name="brand" class="sell-form-select">
                  @foreach (explode(',', $content['form_brands'] ?? 'Rolex,Omega,Patek Philippe,Audemars Piguet,TAG Heuer,Breitling,IWC,Cartier') as $brand)
                  <option value="{{ trim($brand) }}">{{ trim($brand) }}</option>
                  @endforeach
                </select>
                <button type="submit" class="sell-form-btn" style="background-color: {{ $content['btn_bg'] ?? '#222' }}; color: {{ $content['btn_color'] ?? '#fff' }}">{{ $content['btn_text'] ?? __('common.view_more') }}</button>
              </form>
              {{-- Temporary: centered confirmation overlay --}}
              <div id="sell-confirm-{{ $module_id }}" role="dialog" aria-modal="true" aria-label="提交確認"
                style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
                <div style="background:#111;color:#fff;padding:1.5rem 2.5rem;border-radius:0.9rem;font-size:1.15rem;font-weight:500;letter-spacing:0.03em;display:flex;align-items:center;gap:1.75rem;box-shadow:0 12px 40px rgba(0,0,0,0.4);font-family:system-ui,-apple-system,'Segoe UI',sans-serif;">
                  <span>已發送，待回覆</span>
                  <button type="button" id="sell-confirm-close-{{ $module_id }}"
                    style="background:none;border:none;color:#fff;font-size:1.4rem;line-height:1;cursor:pointer;padding:0;opacity:0.7;" aria-label="關閉">×</button>
                </div>
              </div>
              @else
              <a href="{{ $content['link'] }}" class="btn btn-lg" style="background-color: {{ $content['btn_bg'] ?? '#fd560f' }}; color: {{ $content['btn_color'] ?? '#fff' }}">{{ $content['btn_text'] ?? __('common.view_more') }}</a>
              @endif
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6 px-0">
          <div class="img-wrap">
            <img src="{{ $content['image'] }}" class="img-fluid seo-img" alt="{{ $content['image_alt'] ?? '' }}">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@if (!empty($content['show_sell_form']))
@push('add-scripts')
<script>
(function () {
  var form    = document.getElementById('sell-form-{{ $module_id }}');
  var overlay = document.getElementById('sell-confirm-{{ $module_id }}');
  var closeBtn = document.getElementById('sell-confirm-close-{{ $module_id }}');
  if (!form || !overlay) return;

  var hideTimer;

  function showOverlay() {
    overlay.style.display = 'flex';
    clearTimeout(hideTimer);
    hideTimer = setTimeout(hideOverlay, 3500);
  }

  function hideOverlay() {
    overlay.style.display = 'none';
    clearTimeout(hideTimer);
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var emailInput = form.querySelector('input[type="email"]');
    if (!emailInput || !emailInput.value.trim() || !emailInput.checkValidity()) {
      emailInput.focus();
      emailInput.style.outline = '2px solid #c0392b';
      setTimeout(function () { emailInput.style.outline = ''; }, 1800);
      return;
    }
    showOverlay();
  });

  if (closeBtn) closeBtn.addEventListener('click', hideOverlay);

  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) hideOverlay();
  });
})();
</script>
@endpush
@endif