<?php
/*
	Plugin Name: Clean Buddypress Tables
	Author: Tony Thomas
	Description: Clean Buddypress Database From Wordpress
	Version: 1.0
*/
add_action('admin_menu', 'cleanup_menu_init');
function cleanup_menu_init() {
	add_options_page("Cleanup Buddypress", "Cleanup Buddypress", "manage_options", "cleanup", "display_cleanup_page");
}
function display_cleanup_page() {
?>
	<div>
		<h1>Cleanup Buddypress Tables</h1>
		<form action = "options.php" method = "POST">
			<?php settings_fields('cleanup_options'); ?>
			<?php do_settings_sections('cleanup'); ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php //echo "Delete"; if(wpmu_delete_user( 242)) { echo "User deleted"; } ?>

<?
	$options	=	get_option('cleanup_options');
	if($options["cleanup_group"] == "group") {
		echo "<h2>Groups have been cleaned</h2>";
		$options['cleanup_group']	=	"";
		$groups	=	groups_get_groups(array( 'per_page' => 100));
		$only_groups	=	$groups["groups"];
		foreach ($only_groups as $group) {
			groups_edit_group_settings(intval($group->id), 0, 'public', true);
		}
		update_option('cleanup_options', $options );
	}
}
add_action('admin_init', 'cleanup_init');
function cleanup_init() {
	register_setting('cleanup_options', 'cleanup_options', 'validate_cleanup');
	add_settings_section('cleanup_main', 'Cleanup Settings', 'cleanup_main_display', 'cleanup');
	add_settings_field('cleanup_group', 'Cleanup Group', 'cleanup_main_group_display', 'cleanup', 'cleanup_main');
}
function cleanup_main_display() {
	echo '<h2>Cleanup Main Section</h2>';
}
function cleanup_main_group_display() {
	$options	=	get_option ('cleanup_options');
	echo '<p>Enter <strong>group</strong> to initiate</p>';
	echo '<input id = "plugin_id" type = "text" name = "cleanup_options[cleanup_group]" value = "'.$options['cleanup_group'].'" />';
}
function validate_cleanup($input) {
	return $input;
}
add_filter( 'wp_mail_from_name', 'clean_mail_from_name');
function clean_mail_from_name() {
	global $bp;
	if($bp->loggedin_user->id > 0) {
		return $bp->loggedin_user->fullname;
	} else {
		return "";
	}
}
add_filter( 'wp_mail_from', 'clean_mail_from');
function clean_mail_from() {
	global $bp;
	if($bp->loggedin_user->id > 0) {
		return bp_core_get_user_email($bp->loggedin_user->id);
	} else {
		return "tony.uce@gmail.com";
	}
}
?>
