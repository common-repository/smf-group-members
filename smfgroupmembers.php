<?php
/*
Plugin Name: SMF Group Members
Plugin URI: http://www.longu.de/_smfgm_redirect.php
Description: This plugin creates a widget that displays all members from a sepcific member group in a SMF (Simple Machine Forum).
Version: 0.4.1
Author: Michael Stock
Author URI: http://www.longu.de/
License: GPL2
*/
/*  Copyright 2011 Michael Stock (email : m.stock@longu.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * function prefix is "smfgm_"
*/

/********************************
	Require Widget File
********************************/
require('smfgroupmembers_widget.php');

/********************************
	Add menu and submenu
********************************/
add_action( 'admin_menu', 'smfgm_add_new_menu_section' ); 
/* function smfgm_add_new_menu_section(){
	add_menu_page( 'smfgm_slug', 'SMF Groups', 'administrator',"smfgm_slug", 'smfgm_admin_settings'  );
} */
function smfgm_add_new_menu_section(){
//	add_menu_page( 'SMF Group Members Settings', 'SMF Groups', 'manage_options', __FILE__, 'smfgm_admin_settings'  );
	add_menu_page( 'SMF Group Members Settings', 'SMF Groups', 'administrator', __FILE__, 'smfgm_admin_settings'  );
	add_submenu_page( __FILE__, 'Settings', 'Settings', 'administrator', __FILE__);
}

/********************************
	Set up admin page
********************************/
function smfgm_admin_settings(){
?>
	<style>
		.textareabig { font-family:arial; font-size:11px; width:400px; height:100px; }
		.textareasmall { font-family:arial; font-size:10px; width:400px; height:50px; }
		.exampleCode { font-family:Courier New; font-size:10px; width:400px; background:#ccc; padding:5px; border: 1px solid black; line-height:12px; }
		.smfgmWarning { color:black; background:#cf0; padding:5px; margin:3px;}
		.smfgmWarning h3 { color:red; font-size:18px; }
		.partRight { float:right; }
		.smg_error { background:#ccc; color:#f00; }
	</style>
	<div class="wrap">
		<?php 
		$options = get_option('smfgm_settings');
		if(!empty($options['errors']))
			echo implode( '<br />', $options['errors'] );	
		?>
		
		<h3>Support</h3>
		If you need to support or want to request new features for this plugin, <a href="http://www.longu.de/_smfgm_redirect.php">click here</a>.
		<hr/>
		<form method="POST" action="options.php" enctype="multipart/form-data">
			<?php settings_fields('smfgm_settings'); ?>
			<p class="submit">
				<input name="submit" type="submit" class="button-primary" value="<?php esc_attr_e('Update Settings'); ?>" />
			</p>
			<?php do_settings_sections(__FILE__); ?>
			<p class="submit">
				<input name="submit" type="submit" class="button-primary" value="<?php esc_attr_e('Update Settings'); ?>" />
			</p>
		</form>


		<h2>If your SMF is...</h2>
		... on a different server/domain than your Wordpress, you need to modify the .htaccess:<br/>
		<br/>
		<div class="partRight">
			<div class="smfgmWarning">
				Original file (/forum/attachments/.htaccess):<br/>
				<div class="exampleCode">
&lt;Files *><br/>
&nbsp;&nbsp;&nbsp;Order Deny,Allow<br/>
&nbsp;&nbsp;&nbsp;Deny from all<br/>
&nbsp;&nbsp;&nbsp;Allow from localhost<br/>
&lt;/Files><br/>
<br/>
RemoveHandler .php .php3 .phtml .cgi .fcgi .pl .fpl .shtml';
				</div>
				<br/>
				Replace this with the following lines:<br/>
				<div class="exampleCode">
&lt;Files *><br/>
&nbsp;&nbsp;&nbsp;Order Deny,Allow<br/>
&nbsp;&nbsp;&nbsp;Deny from all<br/>
&nbsp;&nbsp;&nbsp;Allow from localhost<br/>
&nbsp;&nbsp;&nbsp;SetEnvIfNoCase Referer "^http://www.bombrats.com/" local_ref=1 [NC]<br/>
&nbsp;&nbsp;&nbsp;SetEnvIfNoCase Referer "^http://www.bombrat.com/" local_ref=1 [NC]<br/>
&nbsp;&nbsp;&nbsp;Allow from env=local_ref<br/>
&lt;/Files><br/>
<br/>
RemoveHandler .php .php3 .phtml .cgi .fcgi .pl .fpl .shtml<br/>
				</div>
				</div>
		</div>
		<div class="smfgmWarning">
			<h3>Modify the .htaccess in your SMF forum!</h3>
			If you access a SMF forum from a different server/domain, you need to change the file<br/>
			"/forum/attachments/.htaccess".<br/>
			Reason: SMF has a standard setting, that blocks direct linking to images/attachments from other domains.<br/>
			<br/>
		</div>

	</div>

	<div class="smfgm_about">
		<h2>About the author...</h2>
		<img src="http://www.smoerble.de/images/selfportrait01.jpg" width="50"/><br/>
		maybe some info comes later...
	</div>
<?php	
}

/********************************
	Register & Build  Fields
********************************/
add_action( 'admin_init', 'smfgm_register_and_build_fields' );
function smfgm_register_and_build_fields(){
	register_setting( 'smfgm_settings', 'smfgm_settings', 'smfgm_validate_options');
	add_settings_section( 'smfgm_dbcon_section', 'SMF Forum Database Settings', 'smfgm_dbcon_section_text', __FILE__ );
	add_settings_field( 'smfgm_dbserver', '<strong>SMF database server</strong><br />', 'smfgm_dbserver_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_dbname', '<strong>SMF database name</strong><br />', 'smfgm_dbname_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_dbuser', '<strong>SMF database user</strong><br />', 'smfgm_dbuser_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_dbpw', '<strong>SMF database Password</strong><br />', 'smfgm_dbpw_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_dbprefix', '<strong>SMF table prefix</strong><br />', 'smfgm_dbprefix_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_groupid', '<strong>ID of member group</strong><br />', 'smfgm_groupid_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_templatecode', '<strong>Widget template</strong><br />', 'smfgm_templatecode_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_templateoneitem', '<strong>Item template</strong><br />', 'smfgm_templateoneitem_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_displaylimit', '<strong>Limit</strong><br />Show max number of members (default:30, max:100).', 'smfgm_displaylimit_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_noimageurl', '<strong>Dummy image</strong><br />Used, if member has no avatar set.', 'smfgm_noimageurl_field', __FILE__, 'smfgm_dbcon_section' );
	add_settings_field( 'smfgm_imagepath', '<strong>Path to images</strong><br />SMF path for /attachments/', 'smfgm_imagepath_field', __FILE__, 'smfgm_dbcon_section' );
/*
//	add_settings_field( 'intro_text', '<strong>Dummy text</strong><br />Add some dummy text please.', 'smfgm_intro_text', __FILE__, 'main_section' );
	add_settings_field( 'smfgm_template', '<strong>Template</strong><br />Use [userNick], [userAvatar].', 'smfgm_template', __FILE__, 'smfgm_dbcon_section' );
	add_settings_section( 'main_section', 'SMF Forum Settings', 'smfgm_widget_settings_text', __FILE__ );
*/
}

function smfgm_dbserver_field() {
	$options = get_option('smfgm_settings');
	echo "<input id='smfgm_dbserver' name='smfgm_settings[smfgm_dbserver]' size='40' type='text' value='{$options['smfgm_dbserver']}' /><br/>";
	echo 'Example: "localhost" or "http://www.mydomain.com"';
}

function smfgm_dbname_field() {
	$options = get_option('smfgm_settings');
	echo "<input id='smfgm_dbname' name='smfgm_settings[smfgm_dbname]' size='40' type='text' value='{$options['smfgm_dbname']}' />";
}

function smfgm_dbuser_field() {
	$options = get_option('smfgm_settings');
	echo "<input id='smfgm_dbuser' name='smfgm_settings[smfgm_dbuser]' size='40' type='text' value='{$options['smfgm_dbuser']}' />";
}

function smfgm_dbpw_field() {
	$options = get_option('smfgm_settings');
	echo "<input id='smfgm_dbpw' name='smfgm_settings[smfgm_dbpw]' size='40' type='text' value='{$options['smfgm_dbpw']}' />";
}

function smfgm_dbprefix_field() {
	$options = get_option('smfgm_settings');
	echo "<input id='smfgm_dbprefix' name='smfgm_settings[smfgm_dbprefix]' size='40' type='text' value='{$options['smfgm_dbprefix']}' /><br/>";
	echo 'Example: "smf_"';
}

function smfgm_groupid_field($args) {
	$options = get_option('smfgm_settings');
	// old: text field
	// echo "<input id='smfgm_groupid' name='smfgm_settings[smfgm_groupid]' size='40' type='text' value='{$options['smfgm_groupid']}' />";
	// new: dropdown
	// check if databse conneciton is established:
	$options = get_option('smfgm_settings');
	$smoerbleSettings = array();
	$smoerbleSettings["attachmentfilename"] = "[imagePath]/[attachmentID]_[fileHash]"; // SMF 2.0 saves without file extension!
	$smoerbleSettings["db_server"] = $options['smfgm_dbserver'];
	$smoerbleSettings["db_name"] = $options['smfgm_dbname'];
	$smoerbleSettings["db_user"] = $options['smfgm_dbuser'];
	$smoerbleSettings["db_passwd"] = $options['smfgm_dbpw'];
	$smoerbleSettings["db_prefix"] = $options['smfgm_dbprefix'];
	// check if needed data is set, if not, don't try a DB connection:
	$dbConnWorks = false;
	$templateDropdown = '<select name="smfgm_settings[smfgm_groupid]">[options]</select>';
	$templateOption = '<option value="[groupID]"[isSelected]>[groupName]</option>';
	$dropdown = "";
	if ((strlen($smoerbleSettings["db_server"]) > 3)
		&& (strlen($smoerbleSettings["db_name"]) > 3)
		&& (strlen($smoerbleSettings["db_user"]) > 3)
		&& (strlen($smoerbleSettings["db_passwd"]) > 3)
		) {
		if ($chandle = mysql_connect($smoerbleSettings["db_server"], $smoerbleSettings["db_user"], $smoerbleSettings["db_passwd"]) ) {
			if(is_resource($chandle) && get_resource_type($chandle) === 'mysql link') {
				if (mysql_select_db($smoerbleSettings["db_name"], $chandle)) {
					$dbConnWorks = true;
				} else {
					$templateDropdown = '<div class="smf_error">could not find database on this server.</div>';
				}
			} else {
				$templateDropdown = '<div class="smf_error">connection is not a mysql link.</div>';
			}
		} else {
			$templateDropdown = '<div class="smf_error">connection to server failed.</div>';
		}
	} else {
		$templateDropdown = '<div class="smf_error">db server, name, user or pw missing.</div>';
	}
	if ($dbConnWorks) {
		// get all member groups:
		$myQuery = "SELECT id_group, group_name FROM [prefix]membergroups ORDER BY group_name";

		/*
		$myQuery = "SELECT [prefix]attachments.filename, [prefix]attachments.attachment_type, [prefix]attachments.id_attach, [prefix]attachments.fileext, [prefix]attachments.file_hash, ".
			"[prefix]members.id_member AS userID, member_name ".
			"FROM [prefix]members LEFT JOIN [prefix]attachments ON [prefix]members.id_member = [prefix]attachments.id_member ".
			'WHERE ((([prefix]members.additional_groups) Like "%23%")) OR ((([prefix]members.id_group)="23"))'.
			"";
*/
		$myQuery = str_replace("[prefix]", $smoerbleSettings["db_prefix"], $myQuery);
		$queryFailed = false;
		$result = mysql_query($myQuery);

//		if ($queryFailed) {
		if (mysql_num_rows($result) < 1) {
			$dropdown = '<option>(unknown error)</option>';
			$dropdown = str_replace("[options]", $dropdown, $templateDropdown);
			$dropdown = $dropdown.'<div class="smg_error">could not find any user group, mySQL query failed: '.$myQuery.'</div>';
		} else {
		//do the query
			$memberCount = 0;
			if (mysql_num_rows($result) > 0) {
				while($thisRow = mysql_fetch_assoc($result)) {
					$dropdown = $dropdown.$templateOption;
					$dropdown = str_replace("[groupID]", $thisRow["id_group"], $dropdown);
					$dropdown = str_replace("[groupName]", $thisRow["group_name"], $dropdown);
					if ($thisRow["id_group"] == $options['smfgm_groupid']) {
						$dropdown = str_replace("[isSelected]", " selected", $dropdown);
					} else {
						$dropdown = str_replace("[isSelected]", "", $dropdown);
					}
/*
*/
				}
			} else {
				$dropdown = '<option>(unknown error)</option>
					<div class="smg_error">could not find any user group!</div>';
			}
		}
		//die("Failed Query of " . $myQuery); 
		//echo '<div class="smf_error">ERROR: Failed Query of '.$myQuery.'</div>';
/*
		//die("Failed Query of " . $myQuery);  
*/
		$dropdown = str_replace("[options]", $dropdown, $templateDropdown);
	} else {
		$dropdown = '<option>(database connection not working)</option>';
		$dropdown = str_replace("[options]", $dropdown, $templateDropdown);
		$dropdown = $dropdown.'<div class="smg_error">Connection to SMF failed, please enter your <b>database name, server, user, password</b> and (if it\'s used) the table prefix of your SMF database.</div>';
	}
	echo $dropdown;
	// get list of member groups from SMF:
	
}

function smfgm_imagepath_field() {
	$options = get_option('smfgm_settings');
	echo "<input id='smfgm_imagepath' name='smfgm_settings[smfgm_imagepath]' size='40' type='text' value='{$options['smfgm_imagepath']}' /><br />";
	echo "Example: http://myDomain.com/forum/attachments/";
}

function smfgm_templatecode_field() {
	$options = get_option('smfgm_settings');
	$exampleCode = '
<style>
.oneMember { float:left; text-align:center; padding:3px; width:70px; height:70px; font-size:8px }
</style>
<center>We have [memberCount] members:</center>
<div>
[memberList]
</div>
<br clear="both"/>
';
	echo 'This generates the widget.<br />';
	echo '<textarea id="smfgm_templatecode" name="smfgm_settings[smfgm_templatecode]" class="textareabig">'.$options['smfgm_templatecode'].'</textarea><br/>';
	echo 'Placeholders: [memberCount], [memberList]<br/>Example template:<br/><p class="exampleCode">'.str_replace("<", "&lt;", str_replace(">", "&gt;", $exampleCode)).'</p>';
}

function smfgm_templateoneitem_field() {
	$options = get_option('smfgm_settings');
	$exampleCode = '
<div class="oneMember">
<img src="[userAvatar]" height="45"><br/>
<div>[userNick]</div>
</div>
';
	echo 'Code to display one member (repeats for each member).<br />';
	echo '<textarea id="smfgm_templateoneitem" name="smfgm_settings[smfgm_templateoneitem]" class="textareabig">'.$options['smfgm_templateoneitem'].'</textarea><br/>';
	echo 'Placeholders: [userAvatar], [userNick]<br/>Example template:<br/><p class="exampleCode">'.str_replace("<", "&lt;", str_replace(">", "&gt;", $exampleCode)).'</p>';
}

function smfgm_displaylimit_field() {
	$options = get_option('smfgm_settings');
	echo "<input id='smfgm_displaylimit' name='smfgm_settings[smfgm_displaylimit]' size='40' type='text' value='{$options['smfgm_displaylimit']}' />";
}

function smfgm_noimageurl_field() {
	$options = get_option('smfgm_settings');
	echo "<input id='smfgm_noimageurl' name='smfgm_settings[smfgm_noimageurl]' size='40' type='text' value='{$options['smfgm_noimageurl']}' /><br/>";
	echo "Example: http://www.realshooter.eu/images/nopic150x112.gif";
	
}

function smfgm_dbcon_section_text(){
	echo '<p>
	1) Add the database connection details to your Simple Machine Forum database.<br/>
	2) Click "update settings".<br/>
	3) Choose the SMF member group from the dropdown.<br/>
	4) Modify the HTML code for the widget.<br/>
	OPTIONAL: if your wordpress and SMF are on seperated servers/domains, you NEED to modify the /attachments/.htaccess file, details at the bottom.<br/>
	</p>';
}

/********************************
	Validate Options
********************************/
function smfgm_validate_options( $smfgm_options ){
	return $smfgm_options;
}

//add a settings link on the plugins page
function smfgm_settings_link($links){
  $settings_link = '<a href="admin.php?page=smfgroupmembers/smfgroupmembers.php">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", "smfgm_settings_link");
