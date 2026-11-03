const overlays = new Set();
let previousOverflow = '';

export function lockOverlayScroll(overlay) {
    if (!overlay || overlays.has(overlay)) return;
    if (overlays.size === 0) previousOverflow = document.body.style.overflow;
    overlays.add(overlay);
    document.body.style.overflow = 'hidden';
}

export function unlockOverlayScroll(overlay) {
    if (!overlays.delete(overlay)) return;
    if (overlays.size === 0) document.body.style.overflow = previousOverflow;
}
