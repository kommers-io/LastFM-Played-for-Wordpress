<?php
/**
 * Plugin Name: Last.FM Recently Played for WordPress
 * Plugin URI: https://kommers.io
 * Description: Display your recently played tracks from Last.FM in a clean, responsive widget. Not affiliated with Last.FM.
 * Version: 1.1.0
 * Author: KOMMERS GmbH
 * Author URI: https://kommers.io
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: lastfm-played-wp
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Tested up to: 6.7
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LASTFM_PLAYED_VERSION', '1.1.0' );
define( 'LASTFM_PLAYED_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LASTFM_PLAYED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'LASTFM_PLAYED_DEFAULT_API_KEY', 'b3f34d8652bf87d8d1dcbfa5c53d245d' );

function lastfm_played_init(): void {
	load_plugin_textdomain( 'lastfm-played-wp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	register_widget( 'LastFM_Played_Widget' );
}
add_action( 'widgets_init', 'lastfm_played_init' );

function lastfm_played_admin_menu(): void {
	add_options_page(
		__( 'Last.FM Settings', 'lastfm-played-wp' ),
		__( 'Last.FM Settings', 'lastfm-played-wp' ),
		'manage_options',
		'lastfm-played-settings',
		'lastfm_played_settings_page'
	);
}
add_action( 'admin_menu', 'lastfm_played_admin_menu' );

function lastfm_played_register_settings(): void {
	register_setting(
		'lastfm_played_options',
		'lastfm_played_api_key',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
			'default'           => '',
		)
	);
}
add_action( 'admin_init', 'lastfm_played_register_settings' );

function lastfm_played_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['lastfm_played_save'] ) && check_admin_referer( 'lastfm_played_settings_action', 'lastfm_played_settings_nonce' ) ) {
		$api_key = isset( $_POST['lastfm_played_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['lastfm_played_api_key'] ) ) : '';
		update_option( 'lastfm_played_api_key', $api_key );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved.', 'lastfm-played-wp' ) . '</p></div>';
	}

	$api_key = get_option( 'lastfm_played_api_key', '' );
	$using_default = empty( $api_key );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

		<div class="notice notice-info">
			<p>
				<strong><?php esc_html_e( 'Legal Notice:', 'lastfm-played-wp' ); ?></strong>
				<?php esc_html_e( 'This plugin is not affiliated with, endorsed by, or sponsored by Last.FM. Last.FM is a registered trademark of Last.FM Limited. By using this plugin, you agree to comply with Last.FM\'s Terms of Service and API Terms.', 'lastfm-played-wp' ); ?>
			</p>
		</div>

		<?php if ( $using_default ) : ?>
		<div class="notice notice-warning">
			<p>
				<strong><?php esc_html_e( 'Using Default API Key', 'lastfm-played-wp' ); ?></strong><br>
				<?php esc_html_e( 'The plugin is currently using a shared default API key. For better performance and to avoid rate limits, we recommend getting your own free API key.', 'lastfm-played-wp' ); ?>
			</p>
		</div>
		<?php endif; ?>

		<form method="post" action="">
			<?php wp_nonce_field( 'lastfm_played_settings_action', 'lastfm_played_settings_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="lastfm_played_api_key"><?php esc_html_e( 'Last.FM API Key (Optional)', 'lastfm-played-wp' ); ?></label>
					</th>
					<td>
						<input type="text" id="lastfm_played_api_key" name="lastfm_played_api_key"
							   value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Leave empty to use default shared key', 'lastfm-played-wp' ); ?>" />
						<p class="description">
							<?php
							printf(
								esc_html__( 'Optional: Get your own free API key from %s for better performance and higher rate limits.', 'lastfm-played-wp' ),
								'<a href="https://www.last.fm/api/account/create" target="_blank" rel="noopener noreferrer">Last.FM API</a>'
							);
							?>
							<br>
							<?php
							printf(
								esc_html__( 'Read: %1$s | %2$s', 'lastfm-played-wp' ),
								'<a href="https://www.last.fm/legal/terms" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Terms of Service', 'lastfm-played-wp' ) . '</a>',
								'<a href="https://www.last.fm/api/tos" target="_blank" rel="noopener noreferrer">' . esc_html__( 'API Terms', 'lastfm-played-wp' ) . '</a>'
							);
							?>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save Settings', 'lastfm-played-wp' ), 'primary', 'lastfm_played_save' ); ?>
		</form>
	</div>
	<?php
}

class LastFM_Played_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'lastfm_widget',
			__( 'Last.FM Recently Played', 'lastfm-played-wp' ),
			array(
				'description' => __( 'Display your recently played tracks from Last.FM', 'lastfm-played-wp' ),
				'classname'   => 'lastfm-played-widget',
			)
		);
	}

	public function form( $instance ): void {
		$title         = isset( $instance['title'] ) ? $instance['title'] : __( 'Recently Played', 'lastfm-played-wp' );
		$lastfm_user   = isset( $instance['lastfm_user'] ) ? $instance['lastfm_user'] : '';
		$lastfm_tracks = isset( $instance['lastfm_tracks'] ) ? absint( $instance['lastfm_tracks'] ) : 5;
		$show_user     = isset( $instance['show_user'] ) ? (bool) $instance['show_user'] : true;

		$api_key = get_option( 'lastfm_played_api_key', LASTFM_PLAYED_DEFAULT_API_KEY );
		if ( empty( $api_key ) || LASTFM_PLAYED_DEFAULT_API_KEY === $api_key ) {
			?>
			<p class="description" style="color: #0073aa;">
				<?php
				printf(
					esc_html__( 'Using default API key. You can configure your own Last.FM API key in the %s for better rate limits.', 'lastfm-played-wp' ),
					'<a href="' . esc_url( admin_url( 'options-general.php?page=lastfm-played-settings' ) ) . '">' . esc_html__( 'settings page', 'lastfm-played-wp' ) . '</a>'
				);
				?>
			</p>
			<?php
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'lastfm-played-wp' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
				   value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'lastfm_user' ) ); ?>">
				<?php esc_html_e( 'Last.FM Username:', 'lastfm-played-wp' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'lastfm_user' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'lastfm_user' ) ); ?>" type="text"
				   value="<?php echo esc_attr( $lastfm_user ); ?>" required />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'lastfm_tracks' ) ); ?>">
				<?php esc_html_e( 'Number of tracks to show:', 'lastfm-played-wp' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'lastfm_tracks' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'lastfm_tracks' ) ); ?>" type="number"
				   value="<?php echo esc_attr( $lastfm_tracks ); ?>" min="1" max="50" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_user ); ?>
				   id="<?php echo esc_attr( $this->get_field_id( 'show_user' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'show_user' ) ); ?>" />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_user' ) ); ?>">
				<?php esc_html_e( 'Show user profile information', 'lastfm-played-wp' ); ?>
			</label>
		</p>
		<?php
	}

	public function widget( $args, $instance ): void {
		$title         = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$lastfm_user   = isset( $instance['lastfm_user'] ) ? sanitize_text_field( $instance['lastfm_user'] ) : '';
		$lastfm_tracks = isset( $instance['lastfm_tracks'] ) ? absint( $instance['lastfm_tracks'] ) : 5;
		$show_user     = isset( $instance['show_user'] ) ? (bool) $instance['show_user'] : true;

		if ( empty( $lastfm_user ) ) {
			if ( current_user_can( 'edit_theme_options' ) ) {
				echo wp_kses_post( $args['before_widget'] );
				echo '<p>' . esc_html__( 'Please configure your Last.FM username in the widget settings.', 'lastfm-played-wp' ) . '</p>';
				echo wp_kses_post( $args['after_widget'] );
			}
			return;
		}

		$lastfm_tracks = max( 1, min( 50, $lastfm_tracks ) );

		$api_key = get_option( 'lastfm_played_api_key', LASTFM_PLAYED_DEFAULT_API_KEY );
		if ( empty( $api_key ) ) {
			$api_key = LASTFM_PLAYED_DEFAULT_API_KEY;
		}

		$user_data = null;
		if ( $show_user ) {
			$user_data = $this->get_lastfm_user_info( $lastfm_user, $api_key );
			if ( is_wp_error( $user_data ) ) {
				if ( current_user_can( 'edit_theme_options' ) ) {
					echo wp_kses_post( $args['before_widget'] );
					echo '<p>' . esc_html__( 'Error loading Last.FM user data. Please check your username and API key.', 'lastfm-played-wp' ) . '</p>';
					echo wp_kses_post( $args['after_widget'] );
				}
				return;
			}
		}

		$tracks = $this->get_lastfm_recent_tracks( $lastfm_user, $api_key, $lastfm_tracks );

		if ( is_wp_error( $tracks ) ) {
			if ( current_user_can( 'edit_theme_options' ) ) {
				echo wp_kses_post( $args['before_widget'] );
				echo '<p>' . esc_html__( 'Error loading Last.FM tracks. Please check your username and API key.', 'lastfm-played-wp' ) . '</p>';
				echo wp_kses_post( $args['after_widget'] );
			}
			return;
		}

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}

		if ( $show_user && $user_data ) {
			$this->display_user_info( $user_data );
		}

		if ( ! empty( $tracks ) && is_array( $tracks ) ) {
			$this->display_tracks( $tracks );
		}

		echo wp_kses_post( $args['after_widget'] );
	}

	public function update( $new_instance, $old_instance ): array {
		$instance                  = array();
		$instance['title']         = ! empty( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['lastfm_user']   = ! empty( $new_instance['lastfm_user'] ) ? sanitize_text_field( $new_instance['lastfm_user'] ) : '';
		$instance['lastfm_tracks'] = ! empty( $new_instance['lastfm_tracks'] ) ? absint( $new_instance['lastfm_tracks'] ) : 5;
		$instance['show_user']     = ! empty( $new_instance['show_user'] ) ? 1 : 0;

		if ( $instance['lastfm_user'] !== ( $old_instance['lastfm_user'] ?? '' ) ) {
			delete_transient( 'lastfm_user_' . md5( $instance['lastfm_user'] ) );
			delete_transient( 'lastfm_tracks_' . md5( $instance['lastfm_user'] ) );
		}

		return $instance;
	}

	public function get_lastfm_user_info( string $username, string $api_key ) {
		$cache_key = 'lastfm_user_' . md5( $username );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$api_url = add_query_arg(
			array(
				'method'  => 'user.getinfo',
				'user'    => $username,
				'api_key' => $api_key,
				'format'  => 'json',
			),
			'https://ws.audioscrobbler.com/2.0/'
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'    => 10,
				'user-agent' => 'WordPress/LastFM-Played-Plugin',
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data ) || isset( $data['error'] ) ) {
			return new WP_Error( 'lastfm_api_error', __( 'Failed to fetch Last.FM user data.', 'lastfm-played-wp' ) );
		}

		set_transient( $cache_key, $data, HOUR_IN_SECONDS );

		return $data;
	}

	public function get_lastfm_recent_tracks( string $username, string $api_key, int $limit ) {
		$cache_key = 'lastfm_tracks_' . md5( $username . $limit );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$api_url = add_query_arg(
			array(
				'method'  => 'user.getrecenttracks',
				'user'    => $username,
				'api_key' => $api_key,
				'limit'   => $limit,
				'format'  => 'json',
			),
			'https://ws.audioscrobbler.com/2.0/'
		);

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'    => 10,
				'user-agent' => 'WordPress/LastFM-Played-Plugin',
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		if ( empty( $data ) || isset( $data['error'] ) ) {
			return new WP_Error( 'lastfm_api_error', __( 'Failed to fetch Last.FM tracks.', 'lastfm-played-wp' ) );
		}

		set_transient( $cache_key, $data, 5 * MINUTE_IN_SECONDS );

		return $data;
	}

	public function display_user_info( array $user_data ): void {
		if ( empty( $user_data['user'] ) ) {
			return;
		}

		$user       = $user_data['user'];
		$user_name  = isset( $user['name'] ) ? esc_html( $user['name'] ) : '';
		$realname   = isset( $user['realname'] ) ? esc_html( $user['realname'] ) : $user_name;
		$user_url   = isset( $user['url'] ) ? esc_url( $user['url'] ) : '';
		$user_image = isset( $user['image'][2]['#text'] ) ? esc_url( $user['image'][2]['#text'] ) : '';
		$playcount  = isset( $user['playcount'] ) ? number_format_i18n( absint( $user['playcount'] ) ) : '0';

		if ( empty( $user_image ) ) {
			$user_image = 'https://lastfm.freetls.fastly.net/i/u/64s/818148bf682d429dc215c1705eb27b98.png';
		}
		?>
		<div class="lastfm-row lastfm-user">
			<div class="lastfm-col-quarter">
				<img width="100" height="100" src="<?php echo esc_url( $user_image ); ?>" alt="<?php echo esc_attr( $realname ); ?>" loading="lazy" />
			</div>
			<div class="lastfm-col-center">
				<div><strong><?php echo esc_html( $realname ); ?></strong></div>
				<?php if ( ! empty( $user_url ) ) : ?>
					<div>
						<a href="<?php echo esc_url( $user_url ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $user_name ); ?>
						</a>
					</div>
				<?php endif; ?>
				<div>
					<small>
						<?php
						printf(
							esc_html__( '%s Tracks', 'lastfm-played-wp' ),
							esc_html( $playcount )
						);
						?>
					</small>
				</div>
			</div>
		</div>
		<?php
	}

	public function display_tracks( array $tracks_data ): void {
		if ( empty( $tracks_data['recenttracks']['track'] ) ) {
			return;
		}

		$tracks = $tracks_data['recenttracks']['track'];

		if ( isset( $tracks['name'] ) ) {
			$tracks = array( $tracks );
		}

		foreach ( $tracks as $track ) {
			$name       = isset( $track['name'] ) ? esc_html( $track['name'] ) : '';
			$artist     = isset( $track['artist']['#text'] ) ? esc_html( $track['artist']['#text'] ) : ( isset( $track['artist'] ) ? esc_html( $track['artist'] ) : '' );
			$image      = isset( $track['image'][2]['#text'] ) ? esc_url( $track['image'][2]['#text'] ) : '';
			$nowplaying = isset( $track['@attr']['nowplaying'] ) && 'true' === $track['@attr']['nowplaying'];

			if ( empty( $image ) ) {
				$image = 'https://lastfm.freetls.fastly.net/i/u/64s/2a96cbd8b46e442fc41c2b86b821562f.png';
			}

			if ( $nowplaying ) {
				$time_display = esc_html__( 'Now playing...', 'lastfm-played-wp' );
			} elseif ( isset( $track['date']['uts'] ) ) {
				$timestamp    = absint( $track['date']['uts'] );
				$time_display = sprintf(
					esc_html__( '%s ago', 'lastfm-played-wp' ),
					human_time_diff( $timestamp )
				);
			} else {
				$time_display = '';
			}
			?>
			<div class="lastfm-row lastfm-tracklist">
				<div class="lastfm-col-twenty">
					<img width="64" height="64" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy" />
				</div>
				<div class="lastfm-col">
					<div class="lastfm-track-title"><strong><?php echo esc_html( $name ); ?></strong></div>
					<div class="lastfm-track-artist"><?php echo esc_html( $artist ); ?></div>
					<?php if ( ! empty( $time_display ) ) : ?>
						<div class="lastfm-track-time"><?php echo esc_html( $time_display ); ?></div>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
	}
}

function lastfm_get_tracks( string $username, int $count = 5, bool $show_user = false ): string {
	$api_key = get_option( 'lastfm_played_api_key', LASTFM_PLAYED_DEFAULT_API_KEY );
	if ( empty( $api_key ) ) {
		$api_key = LASTFM_PLAYED_DEFAULT_API_KEY;
	}

	$widget = new LastFM_Played_Widget();
	$count = max( 1, min( 50, $count ) );

	ob_start();

	echo '<div class="lastfm-played-widget">';

	if ( $show_user ) {
		$user_data = $widget->get_lastfm_user_info( $username, $api_key );
		if ( ! is_wp_error( $user_data ) ) {
			$widget->display_user_info( $user_data );
		}
	}

	$tracks = $widget->get_lastfm_recent_tracks( $username, $api_key, $count );
	if ( ! is_wp_error( $tracks ) && ! empty( $tracks ) && is_array( $tracks ) ) {
		$widget->display_tracks( $tracks );
	}

	echo '</div>';

	return ob_get_clean();
}

function lastfm_display_tracks( string $username, int $count = 5, bool $show_user = false ): void {
	echo lastfm_get_tracks( $username, $count, $show_user );
}

function lastfm_shortcode( $atts ): string {
	$atts = shortcode_atts(
		array(
			'user'      => '',
			'count'     => 5,
			'showuser'  => 'true',
		),
		$atts,
		'lastfm_tracks'
	);

	if ( empty( $atts['user'] ) ) {
		return '<p>' . esc_html__( 'Please specify a Last.FM username using the "user" parameter.', 'lastfm-played-wp' ) . '</p>';
	}

	return lastfm_get_tracks(
		sanitize_text_field( $atts['user'] ),
		absint( $atts['count'] ),
		filter_var( $atts['showuser'], FILTER_VALIDATE_BOOLEAN )
	);
}
add_shortcode( 'lastfm_tracks', 'lastfm_shortcode' );

function lastfm_played_enqueue_styles(): void {
	wp_enqueue_style(
		'lastfm-played-widget',
		LASTFM_PLAYED_PLUGIN_URL . 'style.css',
		array(),
		LASTFM_PLAYED_VERSION,
		'all'
	);
}
add_action( 'wp_enqueue_scripts', 'lastfm_played_enqueue_styles' );

function lastfm_played_plugin_action_links( array $links ): array {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=lastfm-played-settings' ) ) . '">' . __( 'Settings', 'lastfm-played-wp' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'lastfm_played_plugin_action_links' );
