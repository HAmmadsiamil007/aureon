#!/usr/bin/env bash
#
# Phase 0 — Local toolchain bootstrap.
#
# Installs the project-local dev toolchain WITHOUT requiring global installs:
#   - Composer (phar) into tools/composer.phar (git-ignored), signature-verified
#   - PHP dev dependencies (PHPCS, PHPStan, Psalm, WPCS) via Composer
#   - Node dev dependencies (Vite, ESLint, Prettier, TypeScript, Sass) via npm
#
# Usage: bash bin/setup-toolchain.sh   (from the theme repo root)

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
cd "${THEME_DIR}"

COMPOSER_PHAR="${THEME_DIR}/tools/composer.phar"

if [[ ! -f "${COMPOSER_PHAR}" ]]; then
	echo "==> Downloading Composer installer (signature-verified)..."
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"

	# Official Composer signature verification (supply-chain hardening):
	# the installer's SHA-384 must match the hash published by the Composer
	# project. Abort if the check fails; never execute an unverified installer.
	php -r <<'PHP'
$expected = trim( file_get_contents( 'https://composer.github.io/installer.sig' ) );
$actual   = hash_file( 'sha384', 'composer-setup.php' );
if ( ! hash_equals( $expected, $actual ) ) {
	fwrite( STDERR, "ERROR: Composer installer signature mismatch. Aborting.\n" );
	unlink( 'composer-setup.php' );
	exit( 1 );
}
echo "Installer verified (SHA-384 OK).\n";
PHP

	php composer-setup.php --install-dir="${THEME_DIR}/tools" --filename=composer.phar
	rm -f composer-setup.php
fi

echo "==> composer install (dev deps: phpcs, phpstan, psalm, wpcs)"
php "${COMPOSER_PHAR}" install --no-interaction --prefer-dist

echo "==> composer dump-autoload (PSR-4, optimized)"
php "${COMPOSER_PHAR}" dump-autoload --optimize

echo "==> npm ci (vite, eslint, prettier, typescript, sass)"
npm ci

echo "==> Toolchain ready."
echo "    - PHP    : $(php -r 'echo PHP_VERSION;')"
echo "    - Vite   : $(npx vite --version)"
echo "    - PHPCS  : $(php vendor/bin/phpcs --version 2>/dev/null || echo 'run composer install first')"
echo "    - PHPStan: $(php vendor/bin/phpstan --version 2>/dev/null || echo 'run composer install first')"
