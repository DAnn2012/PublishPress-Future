<?php
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.InterpolatedVariableNotSnakeCase
		// phpcs:disable WordPress.PHP.StrictComparisons.LooseComparison

		// Get Option
		$expirationdateDefaultDateFormat = get_option( 'expirationdateDefaultDateFormat', POSTEXPIRATOR_DATEFORMAT );
		$expirationdateDefaultTimeFormat = get_option( 'expirationdateDefaultTimeFormat', POSTEXPIRATOR_TIMEFORMAT );
		$expiredemailnotification = get_option( 'expirationdateEmailNotification', POSTEXPIRATOR_EMAILNOTIFICATION );
		$expiredemailnotificationadmins = get_option( 'expirationdateEmailNotificationAdmins', POSTEXPIRATOR_EMAILNOTIFICATIONADMINS );
		$expiredemailnotificationlist = get_option( 'expirationdateEmailNotificationList', '' );
		$expirationdateDefaultDate = get_option( 'expirationdateDefaultDate', POSTEXPIRATOR_EXPIREDEFAULT );
		$expirationdateDefaultDateCustom = get_option( 'expirationdateDefaultDateCustom' );

		$categories = get_option( 'expirationdateCategoryDefaults' );


		$expiredemailnotificationenabled = '';
		$expiredemailnotificationdisabled = '';
if ( $expiredemailnotification == 0 ) {
	$expiredemailnotificationdisabled = 'checked="checked"';
} elseif ( $expiredemailnotification == 1 ) {
	$expiredemailnotificationenabled = 'checked="checked"';
}

		$expiredemailnotificationadminsenabled = '';
		$expiredemailnotificationadminsdisabled = '';
if ( $expiredemailnotificationadmins == 0 ) {
	$expiredemailnotificationadminsdisabled = 'checked="checked"';
} elseif ( $expiredemailnotificationadmins == 1 ) {
	$expiredemailnotificationadminsenabled = 'checked="checked"';
}

?>

		<p><?php _e( 'The post expirator plugin sets a custom meta value, and then optionally allows you to select if you want the post changed to a draft status or deleted when it expires.', 'post-expirator' ); ?></p>

		<form method="post" id="expirationdate_save_options">
			<?php wp_nonce_field( 'postexpirator_menu_general', '_postExpiratorMenuGeneral_nonce' ); ?>
			<h3><?php _e( 'Defaults', 'post-expirator' ); ?></h3>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="expired-default-date-format"><?php _e( 'Date Format', 'post-expirator' ); ?></label></th>
					<td>
						<input type="text" name="expired-default-date-format" id="expired-default-date-format" value="<?php echo $expirationdateDefaultDateFormat; ?>" size="25" /> <span class="description">(<?php echo date_i18n( "$expirationdateDefaultDateFormat" ); ?>)</span>
						<p class="description"><?php echo sprintf( __( 'The default format to use when displaying the expiration date within a post using the shortcode or within the footer.  For information on valid formatting options, see: %s.', 'post-expirator' ), '<a href="http://us2.php.net/manual/en/function.date.php" target="_blank">PHP Date Function</a>' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="expired-default-time-format"><?php _e( 'Time Format', 'post-expirator' ); ?></label></th>
					<td>
						<input type="text" name="expired-default-time-format" id="expired-default-time-format" value="<?php echo $expirationdateDefaultTimeFormat; ?>" size="25" /> <span class="description">(<?php echo date_i18n( "$expirationdateDefaultTimeFormat" ); ?>)</span>
						<p class="description"><?php echo sprintf( __( 'The default format to use when displaying the expiration time within a post using the shortcode or within the footer.  For information on valid formatting options, see: %s.', 'post-expirator' ), '<a href="http://us2.php.net/manual/en/function.date.php" target="_blank">PHP Date Function</a>' ); ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="expired-default-expiration-date"><?php _e( 'Default Date/Time Duration', 'post-expirator' ); ?></label></th>
					<td>
						<select name="expired-default-expiration-date" id="expired-default-expiration-date" class="pe-custom-date-toggle">
							<option value="null" <?php echo ( $expirationdateDefaultDate == 'null' ) ? ' selected="selected"' : ''; ?>><?php _e( 'None', 'post-expirator' ); ?></option>
							<option value="custom" <?php echo ( $expirationdateDefaultDate == 'custom' ) ? ' selected="selected"' : ''; ?>><?php _e( 'Custom', 'post-expirator' ); ?></option>
							<option value="publish" <?php echo ( $expirationdateDefaultDate == 'publish' ) ? ' selected="selected"' : ''; ?>><?php _e( 'Post/Page Publish Time', 'post-expirator' ); ?></option>
						</select>
						<p class="description"><?php _e( 'Set the default expiration date to be used when creating new posts and pages.  Defaults to none.', 'post-expirator' ); ?></p>
						<?php $show = ( $expirationdateDefaultDate == 'custom' ) ? 'block' : 'none'; ?>
						<div id="expired-custom-container" style="display: <?php echo $show; ?>;" class="pe-custom-date-container">
							<br/>
							<label for="expired-custom-expiration-date"><?php _e( 'Custom', 'post-expirator' ); ?>:</label>
							<input type="text" value="<?php echo $expirationdateDefaultDateCustom; ?>" name="expired-custom-expiration-date" id="expired-custom-expiration-date" />
							<p class="description"><?php echo sprintf( __( 'Set the custom value to use for the default expiration date.  For information on formatting, see %1$s. For example, you could enter %2$s+1 month%3$s or %4$s+1 week 2 days 4 hours 2 seconds%5$s or %6$snext Thursday%7$s.', 'post-expirator' ), '<a href="http://php.net/manual/en/function.strtotime.php" target="_new">PHP strtotime function</a>', '<code>', '</code>', '<code>', '</code>', '<code>', '</code>' ); ?></p>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Default Expiration Category', 'post-expirator' ); ?></th>
					<td>
			<?php
					echo '<div class="wp-tab-panel" id="post-expirator-cat-list">';
					echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">';
					$walker = new Walker_PostExpirator_Category_Checklist();
					wp_terms_checklist( 0, array( 'taxonomy' => 'category', 'walker' => $walker, 'selected_cats' => $categories, 'checked_ontop' => false ) );
					echo '</ul>';
					echo '</div>';
			?>
						<p class="description"><?php _e( 'Sets the default expiration category for the post.', 'post-expirator' ); ?></p>
					</td>
				</tr>
			</table>

			<h3><?php _e( 'Expiration Email Notification', 'post-expirator' ); ?></h3>
			<p class="description"><?php _e( 'Whenever a post expires, an email can be sent to alert users of the expiration.', 'post-expirator' ); ?></p>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Enable Email Notification?', 'post-expirator' ); ?></th>
					<td>
						<input type="radio" name="expired-email-notification" id="expired-email-notification-true" value="1" <?php echo $expiredemailnotificationenabled; ?>/> <label for="expired-email-notification-true"><?php _e( 'Enabled', 'post-expirator' ); ?></label>
						&nbsp;&nbsp;
						<input type="radio" name="expired-email-notification" id="expired-email-notification-false" value="0" <?php echo $expiredemailnotificationdisabled; ?>/> <label for="expired-email-notification-false"><?php _e( 'Disabled', 'post-expirator' ); ?></label>
						<p class="description"><?php _e( 'This will enable or disable the send of email notification on post expiration.', 'post-expirator' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Include Blog Administrators?', 'post-expirator' ); ?></th>
					<td>
						<input type="radio" name="expired-email-notification-admins" id="expired-email-notification-admins-true" value="1" <?php echo $expiredemailnotificationadminsenabled; ?>/> <label for="expired-email-notification-admins-true"><?php _e( 'Enabled', 'post-expirator' ); ?></label>
						&nbsp;&nbsp;
						<input type="radio" name="expired-email-notification-admins" id="expired-email-notification-admins-false" value="0" <?php echo $expiredemailnotificationadminsdisabled; ?>/> <label for="expired-email-notification-admins-false"><?php _e( 'Disabled', 'post-expirator' ); ?></label>
						<p class="description"><?php _e( 'This will include all users with the role of "Administrator" in the post expiration email.', 'post-expirator' ); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="expired-email-notification-list"><?php _e( 'Who to notify', 'post-expirator' ); ?></label></th>
					<td>
						<input class="large-text" type="text" name="expired-email-notification-list" id="expired-email-notification-list" value="<?php echo $expiredemailnotificationlist; ?>" />
						<p class="description"><?php _e( 'Enter a comma separate list of emails that you would like to be notified when the post expires.  This will be applied to ALL post types.  You can set post type specific emails on the Defaults tab.', 'post-expirator' ); ?></p>
					</td>
				</tr>
			</table>

			<p class="submit">
				<input type="submit" name="expirationdateSave" class="button-primary" value="<?php _e( 'Save Changes', 'post-expirator' ); ?>" />
			</p>
	</form>

<?php
	// phpcs:enable
