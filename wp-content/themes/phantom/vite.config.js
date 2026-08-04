import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const rootDir = dirname(fileURLToPath(import.meta.url));

// Phase 7 — Asset Pipeline (Phase 10 adds the animation entry;
// Phase 11 adds the component behaviors entry).
//
// Entries:
//   main       → assets-src/ts/main.ts        (frontend JS/ESM, deferred, hashed)
//   animation  → assets-src/ts/animation.ts   (Phase 10 — enqueued only when the
//                Animation Engine is active; GSAP/Lenis/Three are dynamic
//                imports inside it, so they code-split per chunk)
//   components → assets-src/ts/components.ts  (Phase 11 — enqueued only when the
//                Component Library is active; delegated DOM behaviors)
//   styles     → assets-src/scss/main.scss    (tokens + base + components, hashed)
//
// `manifest: true` emits assets/dist/manifest.json consumed by
// Phantom\Core\Assets\ManifestReader / AssetLoader (prod cache busting).
// Dev server (HMR) runs from assets-src/ and is detected by the PHP loader
// via PHANTOM_VITE_ACTIVE / PHANTOM_VITE_PORT (ADR-011).
export default defineConfig({
  root: resolve(rootDir, 'assets-src'),
  publicDir: false,
  build: {
    outDir: resolve(rootDir, 'assets/dist'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        main: resolve(rootDir, 'assets-src/ts/main.ts'),
        animation: resolve(rootDir, 'assets-src/ts/animation.ts'),
        components: resolve(rootDir, 'assets-src/ts/components.ts'),
        styles: resolve(rootDir, 'assets-src/scss/main.scss'),
      },
    },
  },
});
