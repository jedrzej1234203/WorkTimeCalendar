<?php
if( !defined( 'WP_UNINSTALL_PLUGIN' )) exit();
/**
 * Delete table from database
 */
global $wpdb;
$table_name = $wpdb->prefix . 'worktmp_absence';
$query ='DROP TABLE '.$table_name;
$wpdb->query($query);