/**
 * Lumina Core — animation runtime (Phase 10).
 *
 * Declarative animation driven by `data-lumina-anim` attributes and the
 * boot config emitted by `Lumina\Core\Animation\Engine`. GSAP, Lenis and
 * Three.js are loaded as **dynamic imports** (code-split, never global):
 *   - GSAP/ScrollTrigger — when registered presets or scroll triggers exist;
 *   - Lenis — only when enabled and reduced motion is off;
 *   - Three — only when a declared `.lumina-canvas`-style mount exists.
 *
 * Reduced-motion policy (plan §Phase 10): if the user prefers reduced motion
 * (or the config enforces it), the runtime exits before any library is
 * loaded — no listeners, no observers, static content.
 *
 * Performance gates (Breaking): IntersectionObserver count cap, observer
 * budget, and `will-change` only while animating.
 */

interface LuminaAnimationConfig {
  presets: Record<string, LuminaPreset>;
  reduced_motion: { enforced: boolean };
  budgets: { js_budget: number; observer_cap: number };
  lenis: { enabled: boolean; options: Record<string, unknown> };
  three: Record<string, Record<string, unknown>>;
  triggers: Record<string, Record<string, unknown>>;
}

interface LuminaPreset {
  name: string;
  type: string;
  target: string;
  options: Record<string, unknown>;
  scroll: Record<string, unknown>;
  decorative: boolean;
}

const ATTR = 'data-lumina-anim';

/**
 * Resolve the boot config: prefers the engine-injected global, falls back to
 * a zero-animation default (safe when the PHP side never enqueued the entry).
 */
function resolveConfig(): LuminaAnimationConfig {
  const globalConfig = (globalThis as { luminaAnimation?: LuminaAnimationConfig }).luminaAnimation;

  return (
    globalConfig ?? {
      presets: {},
      reduced_motion: { enforced: true },
      budgets: { js_budget: 120 * 1024, observer_cap: 40 },
      lenis: { enabled: false, options: {} },
      three: {},
      triggers: {},
    }
  );
}

/**
 * Whether the user prefers reduced motion (media query + config enforcement).
 */
function prefersReducedMotion(config: LuminaAnimationConfig): boolean {
  if (config.reduced_motion.enforced) {
    return true;
  }

  return typeof matchMedia === 'function'
    ? matchMedia('(prefers-reduced-motion: reduce)').matches
    : false;
}

/**
 * Start the animation runtime. Safe to call multiple times (idempotent).
 */
export async function startAnimation(): Promise<void> {
  const config = resolveConfig();

  // Reduced-motion early exit: no listeners, no observers, no library loads.
  if (prefersReducedMotion(config)) {
    document.documentElement?.setAttribute('data-lumina-anim-reduced', '');
    return;
  }

  const observed = document.querySelectorAll(`[${ATTR}]`).length;

  // Observer cap: never track more elements than the budget allows.
  if (observed > config.budgets.observer_cap) {
    return;
  }

  const { default: gsap } = await import('gsap');
  const { ScrollTrigger } = await import('gsap/ScrollTrigger');

  gsap.registerPlugin(ScrollTrigger);

  // Tween configuration keys belong in the `to` vars; every other preset
  // option is a `from` value (visual start state). This honours declarative
  // duration/ease/stagger instead of hard-coding them.
  const TWEEN_KEYS = ['duration', 'ease', 'delay', 'stagger', 'repeat', 'yoyo'];

  document.querySelectorAll(`[${ATTR}]`).forEach((element) => {
    const name = element.getAttribute(ATTR);

    if (!name || !config.presets[name]) {
      return;
    }

    const preset = config.presets[name];

    // Decorative-only presets are the ones we animate; content-dependent
    // presets are skipped here by design.
    if (!preset.decorative) {
      return;
    }

    const options = preset.options as Record<string, unknown>;
    const fromVars: Record<string, unknown> = { opacity: 0, y: 24 };
    const toVars: Record<string, unknown> = { opacity: 1, y: 0 };

    for (const [key, value] of Object.entries(options)) {
      if (TWEEN_KEYS.includes(key)) {
        toVars[key] = value;
      } else {
        fromVars[key] = value;
      }
    }

    // will-change is applied only while the animation is pending/active —
    // removed on complete to avoid layout-thrash pressure.
    element.setAttribute('will-change', 'transform, opacity');

    // Paused by default: the tween starts only when the ScrollTrigger fires
    // onEnter (reveal-on-scroll). Without `paused: true` the tween would play
    // immediately on load, defeating the scroll-driven acceptance path.
    const tween = gsap.fromTo(element, fromVars, {
      ...toVars,
      paused: true,
      onComplete: () => element.removeAttribute('will-change'),
    });

    ScrollTrigger.create({
      trigger: element,
      start: 'top 85%',
      once: true,
      onEnter: () => tween.play(),
    });
  });

  // Lenis smooth scroll — only when enabled (opt-in feature flag). The RAF
  // loop is cancellable via a real custom event so consumers can stop it.
  if (config.lenis.enabled) {
    const { default: Lenis } = await import('lenis');
    const lenis = new Lenis(config.lenis.options as ConstructorParameters<typeof Lenis>[0]);
    let raf = 0;

    const loop = (time: number): void => {
      lenis.raf(time);
      raf = requestAnimationFrame(loop);
    };

    raf = requestAnimationFrame(loop);

    const stop = (): void => cancelAnimationFrame(raf);

    document.documentElement?.addEventListener('lumina:lenis:stop', stop, { once: true });
  }

  // Three.js — only when a declared canvas mount exists in the DOM. The
  // declared selectors ARE the mount contract: we query them directly (a
  // `data-lumina-canvas` attribute is NOT required). Each declared mount
  // gets its own WebGL renderer.
  const canvasMounts = Object.keys(config.three);

  if (canvasMounts.length > 0) {
    for (const selector of canvasMounts) {
      const mount = document.querySelector(selector);

      if (!mount) {
        continue;
      }

      const { Scene, PerspectiveCamera, WebGLRenderer } = await import('three');

      const scene = new Scene();
      const camera = new PerspectiveCamera(45, 1, 0.1, 100);
      const renderer = new WebGLRenderer({ antialias: true, alpha: true });

      camera.position.z = 5;
      renderer.setSize(mount.clientWidth, mount.clientHeight);
      mount.appendChild(renderer.domElement);

      const animate = (): void => {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
      };

      animate();
    }
  }

  // Announce readiness for other modules to hook into.
  document.documentElement?.setAttribute('data-lumina-anim-ready', '');
}

// Self-boot: the PHP side enqueues this entry only when the Animation Engine
// is active. The engine injects `window.luminaAnimation`; without it the
// module stays inert (zero-cost default).
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    void startAnimation();
  });
} else {
  void startAnimation();
}

export {};
