/**
 * 品牌横条：左右箭头平滑滚动（对标站）
 */
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('[data-lux-brand-strip]').forEach(function (wrap) {
    const el = wrap.querySelector('.lux-brand-strip-scroll');
    const prev = wrap.querySelector('.lux-brand-strip-nav--prev');
    const next = wrap.querySelector('.lux-brand-strip-nav--next');
    if (!el || !prev || !next) return;

    const step = function () {
      return Math.min(Math.max(Math.floor(el.clientWidth * 0.5), 160), 420);
    };

    prev.addEventListener('click', function () {
      el.scrollBy({ left: -step(), behavior: 'smooth' });
    });
    next.addEventListener('click', function () {
      el.scrollBy({ left: step(), behavior: 'smooth' });
    });
  });
});
