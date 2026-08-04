/**
 * Phantom Core — component behavior layer (Phase 11).
 *
 * Progressive-enhancement behaviors for the component library. Every behavior
 * is: delegated (safe with dynamically rendered content), keyboard-accessible,
 * aria-aware, and gated behind `prefers-reduced-motion` where motion is
 * involved. No framework dependency — vanilla DOM only.
 */

const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/** Whether an element is a plain button/input (space/enter is native). */
function isNativeActivator(el: Element): boolean {
  const tag = el.tagName.toLowerCase();
  return tag === 'button' || tag === 'input' || tag === 'a' || tag === 'summary';
}

/** Toggle `hidden` + an `.is-open` state class on an element. */
function setExpanded(el: HTMLElement, open: boolean): void {
  el.hidden = !open;
  el.classList.toggle('is-open', open);
}

/**
 * Tabs — WAI-ARIA tabs pattern. Rows of `[role=tab]` controlling
 * `[role=tabpanel]` panels inside a `[data-phantom-tabs]` scope.
 */
function initTabs(root: HTMLElement): void {
  const tabs = Array.from(root.querySelectorAll<HTMLButtonElement>('[role="tab"]'));
  const panels = Array.from(root.querySelectorAll<HTMLElement>('[role="tabpanel"]'));

  if (tabs.length === 0 || panels.length === 0) {
    return;
  }

  const select = (index: number): void => {
    tabs.forEach((tab, i) => {
      const active = i === index;
      tab.setAttribute('aria-selected', String(active));
      tab.tabIndex = active ? 0 : -1;
      panels[i]?.toggleAttribute('hidden', !active);
    });
    tabs[index]?.focus();
  };

  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => select(index));
    tab.addEventListener('keydown', (event) => {
      let next: number | null = null;

      if (event.key === 'ArrowRight') {
        next = (index + 1) % tabs.length;
      } else if (event.key === 'ArrowLeft') {
        next = (index - 1 + tabs.length) % tabs.length;
      } else if (event.key === 'Home') {
        next = 0;
      } else if (event.key === 'End') {
        next = tabs.length - 1;
      }

      if (next !== null) {
        event.preventDefault();
        select(next);
      }
    });
  });
}

/**
 * Modal / popup — open/close with focus management and ESC handling.
 * Activators are `[data-phantom-modal-open]` / `[data-phantom-popup]`.
 */
function initModal(root: HTMLElement): void {
  const overlay = root.querySelector<HTMLElement>('[data-phantom-modal-overlay]');
  const openers = root.querySelectorAll<HTMLElement>('[data-phantom-modal-open]');
  const closers = root.querySelectorAll<HTMLElement>('[data-phantom-modal-close]');

  if (!overlay) {
    return;
  }

  const dialog = overlay.querySelector<HTMLElement>('[role="dialog"]');
  let previouslyFocused: HTMLElement | null = null;

  const open = (): void => {
    previouslyFocused =
      document.activeElement instanceof HTMLElement ? document.activeElement : null;
    setExpanded(overlay, true);
    dialog?.focus();
  };

  const close = (): void => {
    setExpanded(overlay, false);
    previouslyFocused?.focus();
  };

  openers.forEach((opener) => opener.addEventListener('click', open));
  closers.forEach((closer) => closer.addEventListener('click', close));

  overlay.addEventListener('click', (event) => {
    if (event.target === overlay) {
      close();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !overlay.hidden) {
      close();
    }
  });
}

/** Popup — delayed, one-shot, dismissible overlay. */
function initPopup(root: HTMLElement): void {
  const popup = root.querySelector<HTMLElement>('[data-phantom-popup]');

  if (!popup) {
    return;
  }

  const close = (): void => setExpanded(popup, false);
  const closeBtn = popup.querySelector<HTMLElement>('[data-phantom-popup-close]');
  closeBtn?.addEventListener('click', close);

  const delay = Number(popup.getAttribute('data-delay') ?? 5000);
  const stored = window.sessionStorage.getItem('phantom-popup-dismissed');

  if (stored === null && !REDUCED) {
    window.setTimeout(() => setExpanded(popup, true), Number.isFinite(delay) ? delay : 5000);
  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !popup.hidden) {
      close();
      window.sessionStorage.setItem('phantom-popup-dismissed', '1');
    }
  });
}

/** Notification / alert — dismissible status banners. */
function initDismissible(root: HTMLElement): void {
  root
    .querySelectorAll<HTMLElement>('[data-phantom-notification], [data-phantom-alert]')
    .forEach((el) => {
      const closeBtn = el.querySelector<HTMLElement>(
        '[data-phantom-notification-close], [data-phantom-alert-close]'
      );
      closeBtn?.addEventListener('click', () => setExpanded(el, false));
    });
}

/** Counters — animate `[data-count-target]` when scrolled into view. */
function initCounters(root: HTMLElement): void {
  const counters = root.querySelectorAll<HTMLElement>('[data-count-target]');

  if (counters.length === 0) {
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        const el = entry.target as HTMLElement;
        const target = Number(el.getAttribute('data-count-target') ?? 0);
        const duration = Number(
          el.closest('[data-phantom-counters]')?.getAttribute('data-duration') ?? 1200
        );

        if (REDUCED || !Number.isFinite(target)) {
          return;
        }

        // The number lives in its own span so the styled suffix element is
        // preserved (never flattened into plain text). Clear the static
        // server-rendered number first to avoid duplicating it.
        const suffix = el.querySelector<HTMLElement>('.phantom-counters__suffix');
        const number = document.createElement('span');
        number.className = 'phantom-counters__number';
        el.textContent = '';
        el.append(number);
        if (suffix) {
          el.append(suffix);
        }

        const start = performance.now();
        const step = (now: number): void => {
          const progress = Math.min(1, (now - start) / duration);
          const eased = 1 - Math.pow(1 - progress, 3);
          const value = Math.round(target * eased);
          number.textContent = String(value);

          if (progress < 1) {
            requestAnimationFrame(step);
          }
        };

        requestAnimationFrame(step);
        observer.unobserve(el);
      });
    },
    { threshold: 0.4 }
  );

  counters.forEach((counter) => observer.observe(counter));
}

/** Back to top — show after scrolling, smooth-scroll home. */
function initBackToTop(root: HTMLElement): void {
  const button = root.querySelector<HTMLElement>('[data-phantom-back-to-top]');

  if (!button) {
    return;
  }

  const onScroll = (): void => {
    const show = window.scrollY > 600;
    button.hidden = !show;
  };

  button.addEventListener('click', () => {
    if (REDUCED) {
      window.scrollTo(0, 0);
      return;
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
}

/** Sticky add to cart — track product visibility to show the sticky bar. */
function initStickyAddToCart(root: HTMLElement): void {
  const bar = root.querySelector<HTMLElement>('[data-phantom-sticky-atc]');

  if (!bar) {
    return;
  }

  const target = document.querySelector(
    bar.getAttribute('data-phantom-track') ?? '.phantom-product-card'
  );

  if (!target) {
    return;
  }

  const observer = new IntersectionObserver(
    ([entry]) => {
      bar.hidden = entry.isIntersecting;
    },
    { rootMargin: '0px 0px -80% 0px' }
  );

  observer.observe(target);
} /** Cart drawer / off-canvas — toggle `is-open` on activator click. */
function initTogglePanel(root: HTMLElement): void {
  const toggles = root.querySelectorAll<HTMLElement>('[data-phantom-toggle-target]');

  toggles.forEach((toggle) => {
    const target = document.querySelector<HTMLElement>(
      toggle.getAttribute('data-phantom-toggle-target') ?? ''
    );

    if (!target) {
      return;
    }

    const setOpen = (open: boolean): void => {
      target.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-pressed', String(open));
    };

    toggle.addEventListener('click', () => setOpen(!target.classList.contains('is-open')));

    // Keyboard users can open a panel but must be able to close it: Escape
    // closes, and clicking the backdrop closes drawer-style panels.
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && target.classList.contains('is-open')) {
        setOpen(false);
      }
    });

    target.addEventListener('click', (event) => {
      if (event.target === target) {
        setOpen(false);
      }
    });
  });
}

/** Generic delegated click-to-trigger behaviors via [data-phantom-action]. */
function initActions(root: HTMLElement): void {
  root.querySelectorAll<HTMLElement>('[data-phantom-action]').forEach((el) => {
    const action = el.getAttribute('data-phantom-action');

    if (action === 'close' && !isNativeActivator(el)) {
      el.setAttribute('role', 'button');
      el.setAttribute('tabindex', '0');
      el.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          (el as HTMLElement).click();
        }
      });
    }
  });
}

function boot(): void {
  const roots = document.querySelectorAll<HTMLElement>(
    '[data-phantom-tabs], [data-phantom-modal], [data-phantom-popup], [data-phantom-counters]'
  );
  const root = document.documentElement;

  // Tabs & counters are scoped per root; modal/popup/dismissible act on the document.
  roots.forEach((scope) => {
    initTabs(scope);
    initCounters(scope);
  });

  document.querySelectorAll<HTMLElement>('[data-phantom-modal]').forEach(initModal);
  document.querySelectorAll<HTMLElement>('[data-phantom-popup]').forEach(initPopup);
  initDismissible(root);
  initBackToTop(root);
  initStickyAddToCart(root);
  initTogglePanel(root);
  initActions(root);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
  boot();
}

export {};
