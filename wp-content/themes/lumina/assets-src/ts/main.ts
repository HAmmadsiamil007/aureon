/**
 * Lumina Core — frontend entry point (Phase 7).
 *
 * Minimal interactive bootstrap for the Asset Pipeline: marks the theme as
 * initialized on the document element. The animation runtime (Phase 10) is
 * a code-split entry loaded by the PHP side only when the Animation Engine
 * is active — it is not bundled into this entry (zero cost when unused).
 */

const LUMINA_READY = 'data-lumina-ready';

if (document.documentElement && !document.documentElement.hasAttribute(LUMINA_READY)) {
  document.documentElement.setAttribute(LUMINA_READY, '');
}

export {};
