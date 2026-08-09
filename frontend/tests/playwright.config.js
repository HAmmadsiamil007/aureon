// AETHER frontend E2E configuration.
// Targets the local WP stack (default http://localhost:8080). Override with WEB_BASE_URL.
// Uses the installed Google Chrome via channel:'chrome' — no browser download needed.
const { defineConfig } = require('@playwright/test');

const baseURL = process.env.WEB_BASE_URL || 'http://localhost:8080';

module.exports = defineConfig({
  testDir: './specs',
  timeout: 60000,
  fullyParallel: false,
  workers: 1,
  retries: process.env.CI ? 2 : 1,
  reporter: [['list']],
  outputDir: './results',
  use: {
    baseURL,
    channel: 'chrome',
    headless: true,
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
  },
  projects: [
    { name: 'desktop', use: { viewport: { width: 1280, height: 800 } } },
    { name: 'mobile', use: { viewport: { width: 390, height: 844 } } },
  ],
});
