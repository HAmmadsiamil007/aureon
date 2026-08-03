import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const rootDir = dirname(fileURLToPath(import.meta.url));

// Phase 0 — minimal, valid Vite configuration.
//
// Expanded fully in Phase 7 (Asset Pipeline): SCSS → CSS, TS/ESM → JS,
// manifest output, hashing, dev-server (HMR) with `PHANTOM_VITE_PORT`,
// vendor bundling (GSAP / Lenis / Three.js), fonts, and critical CSS.
// This skeleton only pins the toolchain and proves the pipeline boots.
export default defineConfig({
  root: resolve(rootDir, 'assets-src'),
  publicDir: false,
  build: {
    outDir: resolve(rootDir, 'assets/dist'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: resolve(rootDir, 'assets-src/ts/main.ts'),
    },
  },
});
