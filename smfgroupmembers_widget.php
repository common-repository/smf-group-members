<?php

class Widget_SMFGroupMembers extends WP_Widget{
	function Widget_SMFGroupMembers(){
		$widget_options = array(
			'classname' => 'widget-smfgroupmembers',
			'description' => _('SMF Group Members')
		);
/*
		$control_options = array(
			'height' => 300,
			'width' => 250
		);
*/
		$this->WP_Widget( 'widget_smfgroupmembers', _('SMF Group Members'), $widget_options, $control_options );
	}
	function widget( $args, $instance ){
		extract( $args, EXTR_SKIP );
		echo $before_widget;
		echo "<div style='position:relative;'>";
		echo $before_title;
		echo esc_html( $instance['title'] );
		echo $after_title;
		
		$options = get_option('smfgm_options');
		?>
		<div class="donation_goal_widget_parent">
		<?php
		echo $this->generateMemberList( $args, $instance );
		?>
		</div>
		<?php
		echo "</div>";
		echo $after_widget;
	}
	function form( $instance ){
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<?php _e( 'Title' ); ?>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" 
						name="<?php echo $this->get_field_name( 'title' ); ?>"
						value="<?php echo esc_attr( $instance['title'] ); ?>"
				/>
			</label>
		</p>
	<?php
	}
	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] =  esc_html( $new_instance['title'] );
		return $instance;
	}
	
	function generateMemberList( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		$options = get_option('smfgm_settings');
		//$template = '<div style="float:left; text-align:center;"><img src="[userAvatar]" height="45"><br/>[userNick]</div>';
		$smoerbleSettings = array();
		$smoerbleSettings["attachmentfilename"] = "[imagePath]/[attachmentID]_[fileHash]"; // SMF 2.0 saves without file extension!
		$smoerbleSettings["db_server"] = $options['smfgm_dbserver'];
		$smoerbleSettings["db_name"] = $options['smfgm_dbname'];
		$smoerbleSettings["db_user"] = $options['smfgm_dbuser'];
		$smoerbleSettings["db_passwd"] = $options['smfgm_dbpw'];
		$smoerbleSettings["db_prefix"] = $options['smfgm_dbprefix'];
		$smoerbleSettings["usergroupID"] = $options['smfgm_groupid'];
		$smoerbleSettings["limit"] = $options['smfgm_displaylimit'];
		$smoerbleSettings["templateOneItem"] = $options['smfgm_templateoneitem'];
		$smoerbleSettings["templateCode"] = $options['smfgm_templatecode'];
		$smoerbleSettings["noimageURL"] = $options['smfgm_noimageurl'];
		$smoerbleSettings["imagePath"] = $options['smfgm_imagepath'];
		// validate options:
				// WARNING:
				// This check is NOT final, needs to check for
				// valid URL and working image in a future release.
		if (strlen($smoerbleSettings["noimageURL"]) < 6) {
			$smoerbleSettings["noimageURL"] = "http://www.realshooter.eu/images/nopic150x112.gif";
		}
		if (($smoerbleSettings["limit"] < 1) OR ($smoerbleSettings["limit"] > 100)) {
			$smoerbleSettings["limit"] = 30;
		}
		
		$myQuery = "SELECT [prefix]attachments.filename, [prefix]attachments.attachment_type, [prefix]attachments.id_attach, [prefix]attachments.fileext, [prefix]attachments.file_hash, ".
			"[prefix]members.id_member AS userID, member_name ".
			"FROM [prefix]members LEFT JOIN [prefix]attachments ON [prefix]members.id_member = [prefix]attachments.id_member ".
//			"WHERE additional_groups IN (".$smoerbleSettings["usergroupID"].") OR (id_group = ".$smoerbleSettings["usergroupID"].") ".
//			'WHERE ((([prefix]members.additional_groups) Like "%'.$smoerbleSettings["usergroupID"].'%")) ".
			'WHERE (FIND_IN_SET("'.$smoerbleSettings["usergroupID"].'", smf_members.additional_groups)) '.
			'OR ((([prefix]members.id_group)="'.$smoerbleSettings["usergroupID"].'"))'.
			"LIMIT ".$smoerbleSettings["limit"];
		$myQuery = str_replace("[prefix]", $smoerbleSettings["db_prefix"], $myQuery);
/*
		$myQuery = "SELECT , avatar FROM ".."members ".
			"WHERE additional_groups IN (".$smoerbleSettings["usergroupID"].") OR (id_group = ".$smoerbleSettings["usergroupID"].")".
			"LIMIT ".$smoerbleSettings["limit"];
*/
//$returnValue = $myQuery;
		$chandle = mysql_connect($smoerbleSettings["db_server"], $smoerbleSettings["db_user"], $smoerbleSettings["db_passwd"]) 
			or die("Connection Failure to Database");
		mysql_select_db($smoerbleSettings["db_name"], $chandle) or die ($smoerbleSettings["db_name"] . " Database not found. " . $smoerbleSettings["db_user"]);
		$result = mysql_db_query($smoerbleSettings["db_name"], $myQuery) or die("Failed Query of " . $myQuery);  //do the query
		$memberCount = 0;
		if (mysql_num_rows($result) > 0) {
			while($thisRow = mysql_fetch_assoc($result)) {
				$memberCount = $memberCount + 1;
				$userNick = $thisRow["member_name"];
				$userAvatar = $thisRow["filename"];
				// check if user has an avatar set:
				// WARNING:
				// This check is NOT final, needs to check for
				// valid URL and working image in a future release.
				// plus the filename needs to be validated too (should contain something similar to "avatar_[userID]_..."
				if (strlen($userAvatar) < 6) {
					$userAvatar = $smoerbleSettings["noimageURL"];
				} else {
					$smoerbleSettings["imagePath"] = rtrim ($smoerbleSettings["imagePath"], "/");
					$userAvatar = str_replace("[imagePath]", $smoerbleSettings["imagePath"], $smoerbleSettings["attachmentfilename"]);
					$userAvatar = str_replace("[attachmentID]", $thisRow["id_attach"], $userAvatar);
					$userAvatar = str_replace("[fileHash]", $thisRow["file_hash"], $userAvatar);
				//	$userAvatar = str_replace("[fileExtension]", $thisRow["fileext"], $userAvatar);
				}
				$memberListCode = $memberListCode.$smoerbleSettings["templateOneItem"]."";
				$memberListCode = str_replace("[userNick]", $userNick, $memberListCode);
				$memberListCode = str_replace("[userAvatar]", $userAvatar, $memberListCode);
			}
		}
		$returnValue = str_replace("[memberList]", $memberListCode, $smoerbleSettings["templateCode"]);
		$returnValue = str_replace("[memberCount]", $memberCount, $returnValue);
		/*
		*/
		return $returnValue;
	}	// generateMemberList...

} // class...

function widget_smfgroupmembers_init(){
	register_widget('Widget_SMFGroupMembers');
}
add_action( 'widgets_init','widget_smfgroupmembers_init' );

function generateMemberListFromSMF() {
	
}
