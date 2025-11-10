<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'lastfm_played_api_key' );

global $wpdb;

$wpdb->query(
	"DELETE FROM {$wpdb->options}
	WHERE option_name LIKE '_transient_lastfm_user_%'
	OR option_name LIKE '_transient_timeout_lastfm_user_%'"
);

$wpdb->query(
	"DELETE FROM {$wpdb->options}
	WHERE option_name LIKE '_transient_lastfm_tracks_%'
	OR option_name LIKE '_transient_timeout_lastfm_tracks_%'"
);

delete_option( 'widget_lastfm_widget' );

wp_cache_flush();
