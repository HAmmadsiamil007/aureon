<?php
/**
 * Team card — team member card: image, name, role.
 *
 * Key:    'card/team'
 * Source: team.html `.card-team`
 *
 * Props:  (all keys optional; escaped at render; missing key → default listed)
 * - `string $name      Member name. Default ''.`
 * - `string $role      Member role. Default ''.`
 * - `string $bio       Short bio. Default ''.`
 * - `string $image     Image URL. Default ''.`
 * - `string $alt       Image alt text. Default $name.`
 * - `array $behavior  Behavior whitelist. Default [].`
 *
 * Slots:  none
 * Variants: none
 * Tokens:  no hardcoded presentation values — theme uses `--aureon-*` custom props only.
 *
 * @package Aureon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$componentData = isset( $componentData ) ? (array) $componentData : array();

$name     = isset( $componentData['name'] ) ? $componentData['name'] : '';
$role     = isset( $componentData['role'] ) ? $componentData['role'] : '';
$bio      = isset( $componentData['bio'] ) ? $componentData['bio'] : '';
$image    = isset( $componentData['image'] ) ? $componentData['image'] : '';
$alt      = isset( $componentData['alt'] ) ? $componentData['alt'] : $name;
$behavior = isset( $componentData['behavior'] ) ? (array) $componentData['behavior'] : array();

if ( ! $name ) {
	return;
}
?>
<div class="team-card" data-phantom="team_member" <?php echo aether_behavior_attrs( $behavior ); ?>>
	<div class="team-image">
		<?php if ( $image ) : ?>
			<img loading="lazy" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
		<?php endif; ?>
	</div>
	<h3 class="team-name" data-phantom="team_name"><?php echo esc_html( $name ); ?></h3>
	<?php if ( $role ) : ?>
		<span class="team-role" data-phantom="team_role"><?php echo esc_html( $role ); ?></span>
	<?php endif; ?>
	<?php if ( $bio ) : ?>
		<p class="team-bio" data-phantom="team_bio"><?php echo esc_html( $bio ); ?></p>
	<?php endif; ?>
</div>
