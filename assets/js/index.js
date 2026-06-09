// Live clock
(function () {
    var el = document.getElementById('live-time');
    if (!el) return;
    function tick() {
        var now = new Date();
        el.textContent = now.toLocaleTimeString('nl-NL', { hour: '2-digit', minute: '2-digit' });
    }
    tick();
    setInterval(tick, 30000);
})();

document.addEventListener('DOMContentLoaded', function () {
    const COLS      = 6;
    const MIN_ROWS  = 5;
    const ROW_H     = 48;   // px – matches CSS min-height
    const GAP       = 0;    // px – grid-card has no gap (borders used instead)
    const SPEED_PPS = 40;   // pixels per second for auto-scroll
    const PAUSE_S   = 2;    // seconds paused at top/bottom

    document.querySelectorAll('.grid-body').forEach(function (body) {
        const wrap    = body.parentElement;
        const minRows = parseInt(body.dataset.minRows || MIN_ROWS, 10);
        const cells   = body.children.length;
        const totalRows = cells / COLS;

        // Visible height = minRows rows + gaps between them
        const visibleH = minRows * ROW_H + (minRows - 1) * GAP;
        wrap.style.height = visibleH + 'px';

        if (totalRows <= minRows) return; // nothing to scroll

        // Full content height
        const fullH = totalRows * ROW_H + (totalRows - 1) * GAP;
        const distance = fullH - visibleH;

        const durationS = (distance / SPEED_PPS) + PAUSE_S * 2;

        body.style.setProperty('--scroll-distance', '-' + distance + 'px');
        body.style.setProperty('--scroll-duration', durationS.toFixed(1) + 's');
        body.classList.add('is-scrolling');
    });
});
