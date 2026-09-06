<?php
/**
 * QA runner: Customizer + menu round-trip via WordPress's own APIs.
 * Usage: php qa-roundtrip.php <action>
 * Actions: snapshot | set-announcement | restore-announcement | rename-menu-item | restore-menu-item | verify-menu
 * Writes JSON to stdout. Machine-local QA tool — never ships.
 */
require '/var/www/html/wp-load.php';

header( 'Content-Type: application/json' );
$action = isset( $argv[1] ) ? $argv[1] : '';

function out( $arr ) { echo json_encode( $arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), "\n"; }

switch ( $action ) {

	case 'snapshot':
		out( array(
			'announcement' => get_option( 'aether_announcement', null ),
			'menu_items'   => wp_get_nav_menu_items( get_nav_menu_locations()['primary'] ?? 0 ) ? array_values( array_map( function( $i ) {
				return array( 'id' => $i->ID, 'title' => $i->post_title, 'menu_order' => $i->menu_order );
			}, wp_get_nav_menu_items( get_nav_menu_locations()['primary'] ) ) ) : array(),
		) );
		break;

	case 'set-announcement':
		// The Customizer stores in the aureon_settings bucket — write there.
		$bucket = get_option( 'aureon_settings', array() );
		update_option( 'qa_bucket_backup', $bucket );
		$bucket['aether_announcement_items'] = array(
			array( 'text' => 'QA ROUNDTRIP TEST ' . time(), 'visible' => true ),
		);
		update_option( 'aureon_settings', $bucket );
		out( array( 'ok' => true, 'new' => get_option( 'aureon_settings' )['aether_announcement_items'] ) );
		break;

	case 'restore-announcement':
		$bak = get_option( 'qa_bucket_backup', '__none__' );
		if ( '__none__' === $bak ) { out( array( 'ok' => false, 'error' => 'no backup' ) ); break; }
		update_option( 'aureon_settings', $bak );
		delete_option( 'qa_bucket_backup' );
		delete_option( 'aether_announcement_items' );
		delete_option( 'qa_announcement_backup' );
		out( array( 'ok' => true, 'restored_bucket' => true ) );
		break;

	case 'cleanup-qa-options':
		// Remove stray QA keys from the earlier mis-aimed run.
		delete_option( 'aether_announcement' );
		delete_option( 'aether_announcement_items' );
		delete_option( 'qa_announcement_backup' );
		out( array( 'ok' => true ) );
		break;

	case 'rename-menu-item':
		$loc = get_nav_menu_locations();
		$mid = $loc['primary'] ?? 0;
		$items = $mid ? wp_get_nav_menu_items( $mid ) : array();
		if ( ! $items ) { out( array( 'ok' => false, 'error' => 'no primary menu items' ) ); break; }
		$first = $items[0];
		update_post_meta( $first->ID, '_menu_item_title_backup', $first->post_title );
		wp_update_post( array( 'ID' => $first->ID, 'post_title' => 'QA-RENAMED-' . time() ) );
		// wp_update_post on a nav item: title lives in post_title; clean cache so next query sees it.
		clean_post_cache( $first->ID );
		out( array( 'ok' => true, 'item_id' => $first->ID, 'was' => $first->post_title ) );
		break;

	case 'restore-menu-item':
		$loc = get_nav_menu_locations();
		$mid = $loc['primary'] ?? 0;
		$items = $mid ? wp_get_nav_menu_items( $mid ) : array();
		$restored = null;
		foreach ( (array) $items as $i ) {
			$bak = get_post_meta( $i->ID, '_menu_item_title_backup', true );
			if ( $bak ) {
				wp_update_post( array( 'ID' => $i->ID, 'post_title' => $bak ) );
				delete_post_meta( $i->ID, '_menu_item_title_backup' );
				clean_post_cache( $i->ID );
				$restored = $bak;
				break;
			}
		}
		out( array( 'ok' => (bool) $restored, 'restored' => $restored ) );
		break;

	default:
		out( array( 'ok' => false, 'error' => 'unknown action', 'action' => $action ) );
}
