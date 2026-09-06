<?php
/**
 * Account profile — account heading, avatar initial and orders link.
 *
 * Key:    'account/profile'
 * Source: account.html `.account-profile`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $name    Display name. Default ''.`
 * - `string $email   Email. Default ''.`
 * - `string $initial  Avatar initial. Default ''.`
 * - `array $stats    Stat block schema. Default [].`
 * - `array $menu     Account nav schema. Default [].`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$name    = isset( $componentData['name'] ) ? $componentData['name'] : '';
$email   = isset( $componentData['email'] ) ? $componentData['email'] : '';
$initial = isset( $componentData['initial'] ) ? $componentData['initial'] : '';
$stats   = isset( $componentData['stats'] ) ? (array) $componentData['stats'] : array();
$menu    = isset( $componentData['menu'] ) ? (array) $componentData['menu'] : array();
?>
<div class="account-section" data-phantom-account="true">
	<div class="account-hero">
		<div class="account-avatar"><?php echo esc_html( $initial ); ?></div>
		<h1 class="account-name"><?php echo esc_html( $name ); ?></h1>
		<p class="account-email"><?php echo esc_html( $email ); ?></p>
	</div>

	<div class="account-grid">
		<?php foreach ( $stats as $stat ) : ?>
			<div class="account-stat">
				<div class="account-stat-number"><?php echo esc_html( isset( $stat['number'] ) ? $stat['number'] : '' ); ?></div>
				<div class="account-stat-label"><?php echo esc_html( isset( $stat['label'] ) ? $stat['label'] : '' ); ?></div>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="account-menu">
		<?php foreach ( $menu as $item ) : ?>
			<a href="<?php echo esc_url( isset( $item['url'] ) ? $item['url'] : '#' ); ?>" class="account-menu-item">
				<div class="account-menu-icon"><i class="<?php echo esc_attr( isset( $item['icon'] ) ? $item['icon'] : 'fas fa-circle' ); ?>"></i></div>
				<span><?php echo esc_html( isset( $item['label'] ) ? $item['label'] : '' ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div>
