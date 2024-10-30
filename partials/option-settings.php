<h1><?php echo esc_html(get_admin_page_title()); ?> Settings</h1>
<form name="lh-teams-identifier-form" method="post" action="">
<input type="hidden" name="<?php echo $this->hidden_field_name; ?>" value="Y" />

<h3>Listing options</h3>

<p>
<label for="<?php echo $this->redirect_login_to_team_field_name; ?>"><?php _e("Group Confirmed and Unconfirmed Members:", $this->namespace); ?></label>
<select name="<?php echo $this->group_member_states_field_name; ?>" id="<?php echo $this->group_member_states_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->group_member_states_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->group_member_states_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>

<p>
<label for="<?php echo $this->allow_csv_upload_field_name; ?>"><?php _e("Allow CSV Upload:", $this->namespace); ?></label>
<select name="<?php echo $this->allow_csv_upload_field_name; ?>" id="<?php echo $this->allow_csv_upload_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->allow_csv_upload_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->allow_csv_upload_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>


<p>
<label for="<?php echo $this->allow_additional_messages_field_name; ?>"><?php _e("Allow Additional Messages:", $this->namespace); ?></label>
<select name="<?php echo $this->allow_additional_messages_field_name; ?>" id="<?php echo $this->allow_additional_messages_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->allow_additional_messages_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->allow_additional_messages_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>


<p>
<label for="<?php echo $this->add_to_menu_field_name; ?>"><?php _e("Add to menu:", $this->namespace); ?></label>
<select name="<?php echo $this->add_to_menu_field_name; ?>" id="<?php echo $this->add_to_menu_field_name; ?>">
<option value="">Do not add</option>
<?php
$menus = $this->get_all_wordpress_menus();

$the_menu = get_term_by( 'id', $this->options[$this->add_to_menu_field_name], 'nav_menu' );

print_r($the_menu);

foreach ( $menus as $menu ) {

echo '<option value="'.$menu->term_id.'" ';

if ($this->options[$this->add_to_menu_field_name] == $menu->term_id){ echo 'selected="selected"';}

echo ' >'.$menu->name.'</option>';




}


?>
</select>
</p>


<p>
<?php _e("Allow Personalised Email:", $this->namespace); ?>
<select name="<?php echo $this->allow_personalised_email_field_name; ?>" id="<?php echo $this->allow_personalised_email_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->allow_personalised_email_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->allow_personalised_email_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>

<p><label for="<?php echo $this->redirect_login_to_team_field_name; ?>"><?php _e("Redirect to team page on login:", $this->namespace); ?></label>
<select name="<?php echo $this->redirect_login_to_team_field_name; ?>" id="<?php echo $this->redirect_login_to_team_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->redirect_login_to_team_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->redirect_login_to_team_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>

<p>
<?php _e("Allow anyone to sign up:", $this->namespace); ?>
<select name="<?php echo $this->allow_all_signups_field_name; ?>" id="<?php echo $this->allow_all_signups_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->allow_all_signups_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->allow_all_signups_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>


<?php  if ($this->options[$this->allow_all_signups_field_name] == 0){   ?>
<p>
<?php _e("Send emails to organiser:", $this->namespace); ?>
<select name="<?php echo $this->send_emails_to_organiser_field_name; ?>" id="<?php echo $this->send_emails_to_organiser_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->send_emails_to_organiser_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->send_emails_to_organiser_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>
<?php  }   ?>

<p>
<?php _e("Allow post editing:", $this->namespace); ?>
<select name="<?php echo $this->allow_post_editing_field_name; ?>" id="<?php echo $this->allow_post_editing_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->allow_post_editing_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->allow_post_editing_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>

<p>
<?php _e("Link to profile:", $this->namespace); ?>
<select name="<?php echo $this->link_to_profile_field_name; ?>" id="<?php echo $this->link_to_profile_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->link_to_profile_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->link_to_profile_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
</p>


<p>
<label for="<?php  echo $this->onboarding_page_field_name;  ?>">Enter ID of Onboarding page</label>
<input type="number" placeholder="Enter post id of onboarding page" name="<?php echo $this->onboarding_page_field_name;  ?>" id="<?php echo $this->onboarding_page_field_name;  ?>" size="30" value="<?php echo $this->options[$this->onboarding_page_field_name];  ?>"   />
</p>


<p>
<label for="<?php  echo $this->entry_template_field_name;  ?>">Enter ID of Entry Template</label>
<input type="number" placeholder="Enter post id of template" name="<?php echo $this->entry_template_field_name;  ?>" id="<?php echo $this->entry_template_field_name;  ?>" size="30" value="<?php echo $this->options[$this->entry_template_field_name];  ?>"   />
</p>



<div>
<h3>Unconfirmed message details</h3>
<p>
<?php wp_editor( $this->options[$this->unconfirmed_message_field_name], $this->unconfirmed_message_field_name); ?> 
</p>
</div>

<div>
<h3>Email Details</h3>
<p>
<label class="screen-reader-text" id="<?php  echo $this->email_title_field_name."-prompt-text";  ?>" for="<?php  echo $this->email_title_field_name;  ?>">Enter title here</label>
<input type="text" placeholder="Enter Email title here" name="<?php echo $this->email_title_field_name;  ?>" id="<?php echo $this->email_title_field_name;  ?>" size="30" value="<?php echo $this->options[$this->email_title_field_name];  ?>"   />
</p>
<p>
<?php wp_editor( $this->options[$this->email_message_field_name], $this->email_message_field_name); ?> 
</p>
<label id="<?php  echo $this->email_button_field_name."-prompt-text";  ?>" for="<?php  echo $this->email_button_field_name;  ?>">Email confirmation button text:</label>
<input type="text" name="<?php  echo $this->email_button_field_name;  ?>" id="<?php  echo $this->email_button_field_name;  ?>"  size="50" value="<?php echo $this->return_email_button_text();  ?>" placeholder="e.g Visit my account or Activate Membership" />



<p>
<label id="<?php  echo $this->login_link_field_name."-prompt-text";  ?>" for="<?php  echo $this->login_link_field_name;  ?>">Logged the confirmed User in:</label>
<select name="<?php echo $this->login_link_field_name; ?>" id="<?php echo $this->login_link_field_name; ?>">
<option value="1" <?php  if ($this->options[$this->login_link_field_name] == 1){ echo 'selected="selected"'; }  ?>>Yes</option>
<option value="0" <?php  if ($this->options[$this->login_link_field_name] == 0){ echo 'selected="selected"';}  ?>>No</option>
</select>
(<a href="http://lhero.org/plugins/lh-teams/#<?php echo $this->login_link_field_name; ?>">What does this mean?</a>)
</p>



</div>

<div>
<h3>Confirmed message details</h3>
<p>
<?php wp_editor( $this->options[$this->confirmed_message_field_name], $this->confirmed_message_field_name); ?> 
</p>
</div>

<div>
<h3>Post Notice</h3>
<p>
<?php wp_editor( $this->options[$this->post_notice_field_name], $this->post_notice_field_name); ?> 
</p>
</div>

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>

<?php 
//$this->give_users_participant_role();
//$this->give_users_follower_role();

 ?>