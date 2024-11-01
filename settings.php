<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
function speed_matters_network_options_page() {
	add_submenu_page( 'settings.php', __( 'Speed matters options', SPEED_MATTERS_DOMAIN ), 'Speed matters', 'manage_network_options', 'speed-matters', 'speed_matters__network_options_page_html');
}

function speed_matters__network_options_page_html() {
	echo '<div class="wrap">';
	echo '<h1>'. __( 'Speed matters Network Options', SPEED_MATTERS_DOMAIN ).'</h1>';
	echo '</div>';
}
*/

function speed_matters_options_page() {
	add_options_page( __( 'Speed matters options', SPEED_MATTERS_DOMAIN ), __( 'Speed matters', SPEED_MATTERS_DOMAIN ), 'manage_options', 'speed-matters', 'speed_matters_options_page_html');
}

function speed_matters_options_page_html() {
	echo '<div class="wrap">';


	echo '<h1>'. __( 'Speed matters options', SPEED_MATTERS_DOMAIN ) .'</h1>';
	echo '<h2>'. __( 'Server side improvements', SPEED_MATTERS_DOMAIN ) .'</h2>';
	if ( defined( 'PHP_VERSION' ) ) {
		echo '<p>'. __( 'Your server runs PHP verion:', SPEED_MATTERS_DOMAIN ) .' ' . PHP_VERSION .'. ';
		$php_version = explode( '.', PHP_VERSION );
		if( $php_version[0] < 7 )
			echo __( 'Your version of PHP is outdated. It is not supported any more and should be updated. You will also experience a significant speed improvement.', SPEED_MATTERS_DOMAIN ) .'</p>';
		else
			if( $php_version[1] < 2 )
				echo __( 'Your version of PHP is outdated. It is not supported any more and should be updated. You will also experience a significant speed improvement.', SPEED_MATTERS_DOMAIN ) .'</p>';
			if( $php_version[1] < 3 )
				echo __( 'Your version of PHP is outdated and should be updated. You will also experience a speed improvement.', SPEED_MATTERS_DOMAIN ) .'</p>';
			else
				echo __( 'Your version of PHP is up-to-date.', SPEED_MATTERS_DOMAIN ) .'</p>';
	}

	echo '<h2>'. __( 'Bandwidth usage improvements', SPEED_MATTERS_DOMAIN ) .'</h2>';
	echo '<p>'. __( 'Changing these settings may alter the behaviour of your WordPress setup. Make sure to understand the consequences and test thoroughly.', SPEED_MATTERS_DOMAIN ) .'</p>';

	echo '<form method="post" action="options.php">';
//	$options = unserialize( get_option( 'speed_matters' ) );
	settings_fields( 'speed-matters' );
	$speed_matters_update_jquery = get_option( 'speed_matters_update_jquery' );
	echo '<label for="speed_matters_update_jquery"><input name="speed_matters_update_jquery" type="checkbox" value="1" ' . checked( 1, $speed_matters_update_jquery, false ) . ' />'. __( 'Update jQuery 1.2.4 to jQuery 3.5.1 (drops IE 6–8 support for performance improvements and reduction in filesize)', SPEED_MATTERS_DOMAIN ) .'</label><br />';

	$speed_matters_remove_jquery_migrate = get_option( 'speed_matters_remove_jquery_migrate' );
	echo '<label for="speed_matters_remove_jquery_migrate"><input name="speed_matters_remove_jquery_migrate" type="checkbox" value="1" ' . checked( 1, $speed_matters_remove_jquery_migrate, false ) . ' />'. __( 'Remove jQuery migrate', SPEED_MATTERS_DOMAIN ) .'</label><br />';

	$speed_matters_remove_wpembed = get_option( 'speed_matters_remove_wpembed' );
	echo '<label for="speed_matters_remove_wpembed"><input name="speed_matters_remove_wpembed" type="checkbox" value="1" ' . checked( 1, $speed_matters_remove_wpembed, false ) . ' />'. __( 'Remove WP Embed', SPEED_MATTERS_DOMAIN ) .'</label><br />';

	$speed_matters_disable_emojis = get_option( 'speed_matters_disable_emojis' );
	echo '<label for="speed_matters_disable_emojis"><input name="speed_matters_disable_emojis" type="checkbox" value="1" ' . checked( 1, $speed_matters_disable_emojis, false ) . ' />'. __( 'Disable emojis', SPEED_MATTERS_DOMAIN ) .'</label><br />';

	$speed_matters_disable_heartbeat = get_option( 'speed_matters_disable_heartbeat' );
	echo '<label for="speed_matters_disable_heartbeat"><input name="speed_matters_disable_heartbeat" type="checkbox" value="1" ' . checked( 1, $speed_matters_disable_heartbeat, false ) . ' />'. __( 'Disable the WordPress heartbeat (leave unchecked in case of multiple contributors)', SPEED_MATTERS_DOMAIN ) .'</label><br />';

	submit_button();
	echo '</form>';

	echo '<h2>'. __( 'Database improvements', SPEED_MATTERS_DOMAIN ) .'</h2>';

	global $wpdb;
	$table_prefix = $wpdb->prefix;
	
	if( isset( $_GET['action'] ) ) {
		if ( $_GET['action'] == 'update-to-innodb') {
			$sql = "SELECT table_name FROM information_schema.TABLES WHERE table_schema='$wpdb->dbname' AND table_name LIKE '%$table_prefix%' AND engine = 'MyISAM'";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$results = $wpdb->get_results( $sql );
			foreach ( $results as $result ) {
				$sql = 'ALTER TABLE '. $result->table_name .' ENGINE=InnoDB;';
				if ( WP_DEBUG === true )
					echo "<p>$sql</p>";
				$wpdb->query( $sql );
			}
		}
		if ( $_GET['action'] == 'add-index-to-options') {
			$table_name = $table_prefix.'options';
			$sql = 'ALTER TABLE '. $table_name .' ADD INDEX(autoload); ';
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
		}
		if ( $_GET['action'] == 'add-index-to-postmeta') {
			$table_name = $table_prefix.'postmeta';
			$sql = 'ALTER TABLE '. $table_name .' ADD INDEX(meta_value(256)); ';
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
		}

		if ( $_GET['action'] == 'database-cleanup') {
			$sql = "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS RESULT
				FROM information_schema.tables 
				WHERE table_schema = '$wpdb->dbname'
				GROUP BY table_schema;";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$result = $wpdb->get_var( $sql );
			$before = sprintf( __( 'Database size before cleanup: %s Mbytes', SPEED_MATTERS_DOMAIN ), $result );
			$table_name = $table_prefix.'postmeta';
			$sql = "DELETE FROM $table_name WHERE meta_key = '_edit_lock';";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
			$sql = "DELETE FROM $table_name WHERE meta_key = '_edit_last';";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
			$sql = "DELETE FROM $table_name WHERE meta_key = '_wp_old_slug';";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
			$table_name = $table_prefix.'commentmeta';
			$sql = "DELETE FROM $table_name WHERE meta_key LIKE '%akismet%';";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
			$sql = "DELETE FROM $table_name WHERE comment_id NOT IN (SELECT comment_id FROM $table_name);";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
			$table_name = $table_prefix.'options';
			$sql = "DELETE FROM $table_name WHERE option_name LIKE '_site_transient_browser_%';";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
			$sql = "DELETE FROM $table_name WHERE option_name LIKE '_site_transient_timeout_browser_%';";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
			$sql = "DELETE FROM $table_name WHERE option_name LIKE '_transient_%';";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$wpdb->query( $sql );
			$sql = "SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS RESULT
				FROM information_schema.tables 
				WHERE table_schema = '$wpdb->dbname'
				GROUP BY table_schema;";
			if ( WP_DEBUG === true )
				echo "<p>$sql</p>";
			$result = $wpdb->get_var( $sql );
			$after = sprintf( __( 'Database size after cleanup: %s Mbytes', SPEED_MATTERS_DOMAIN ), $result );
			echo "<p>$before. $after.</p>";
		}
	}

	$sql = "SELECT table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA='$wpdb->dbname' AND table_name LIKE '%$table_prefix%' AND engine = 'MyISAM'";
	if ( WP_DEBUG === true )
		echo "<p>$sql</p>";
	$results = $wpdb->get_results( $sql );
	if( $wpdb->num_rows == 0 ) {
		echo '<p>'. __( 'All your tables are using the InnoDB engine.', SPEED_MATTERS_DOMAIN ) .'</p>';
	}
	else {
		echo '<p>'. __( 'The following tables are using old MyISAM engines. You should consider updating them to a InnoDB engine (MySQL 5.6.4 or higher).', SPEED_MATTERS_DOMAIN ) .'</p>';
		echo '<ul>';
		foreach ( $results as $result ) {
			echo '<li>' . $result->table_name .'</li>';
		}
		echo '</ul>';
		echo '<button class="submit"><a class="button button-primary" href="/wp-admin/options-general.php?page=speed-matters&action=update-to-innodb">'. __( 'Update to InnoDB now.', SPEED_MATTERS_DOMAIN ) .'</a></p>';
	}
	
	$table_name = $table_prefix.'options';
	$sql = "SHOW INDEX FROM $table_name WHERE Column_name = 'autoload'";
	if ( WP_DEBUG === true )
		echo "<p>$sql</p>";
	$results = $wpdb->get_results( $sql );
	if( $wpdb->num_rows == 0 ) {
		echo '<p>'. __( 'Your options table would benefit from an additional index.', SPEED_MATTERS_DOMAIN ) .'</p>';
		echo '<p><button class="submit"><a class="button button-primary" href="/wp-admin/options-general.php?page=speed-matters&action=add-index-to-options">'. __( 'Add index to options table now', SPEED_MATTERS_DOMAIN ) .'</a></button></p>';
	}
	else {
		echo '<p>'. __( 'Your options table does not require an additional index.', SPEED_MATTERS_DOMAIN ) .'</p>';
	}
//	https://www.skyminds.net/wordpress-nettoyer-tables-wp-options-et-wp-postmeta/
	$sql = "SELECT SUM(LENGTH(option_value)) as autoload_size FROM $table_name WHERE autoload='yes';";
	if ( WP_DEBUG === true )
		echo "<p>$sql</p>";

	$table_name = $table_prefix.'postmeta';
	$sql = "SHOW INDEXES FROM $table_name WHERE Column_name = 'meta_value'";
	if ( WP_DEBUG === true )
		echo "<p>$sql</p>";
	$results = $wpdb->get_results( $sql );
	if( $wpdb->num_rows == 0 ) {
		echo '<p>'. __( 'Your postmeta table would benefit from an additional index.', SPEED_MATTERS_DOMAIN ) .'</p>';
		echo '<p><button class="submit"><a class="button button-primary" href="/wp-admin/options-general.php?page=speed-matters&action=add-index-to-postmeta">'. __( 'Add index to postmeta table now', SPEED_MATTERS_DOMAIN ) .'</a></button></p>';
	}
	else {
		echo '<p>'. __( 'Your options table does not require an additional index.', SPEED_MATTERS_DOMAIN ) .'</p>';
	}

	echo '<p><button class="submit"><a class="button button-primary" href="/wp-admin/options-general.php?page=speed-matters&action=database-cleanup">'. __( 'Clean up the database now', SPEED_MATTERS_DOMAIN ) .'</a></button></p>';

	echo '</div>';
}

function register_speed_matters_settings() {
// a:2:{s:21:"remove_jquery_migrate";s:1:"1";s:14:"disable_emojis";s:1:"1";}

/*
	$speed_matters_options = array(
		'remove_jquery_migrate' => '1',
		'disable_emojis' => '1'
	);
	 update_option( 'speed_matters', serialize( $speed_matters_options ) );
*/
	 register_setting( 'speed-matters', 'speed_matters_update_jquery' );
	 register_setting( 'speed-matters', 'speed_matters_remove_jquery_migrate' );
	 register_setting( 'speed-matters', 'speed_matters_remove_wpembed' );
	 register_setting( 'speed-matters', 'speed_matters_disable_emojis' );
	 register_setting( 'speed-matters', 'speed_matters_disable_heartbeat' );
}

function speed_matters_options_sanitize_text_field( $input ) {
	$input = sanitize_text_field( $input );
	return $input;
}

function speed_matters_copy_main_site_options( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
    $mainsite = get_option( 'pagenavi_options' );
    switch_to_blog( $blog_id );
    update_option( 'pagenavi_options', $mainsite );
    restore_current_blog();
}