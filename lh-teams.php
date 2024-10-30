<?php
/*
 Plugin Name: LH Teams
 Plugin URI: https://lhero.org/portfolio/lh-teams/
 Description: Adds a team custom post type
 Author: Peter Shaw
 Author URI: https://shawfactor.com
 Version: 1.13
 License: GPL v3 (http://www.gnu.org/licenses/gpl.html)
*/

class LH_Teams_plugin {

var $opt_name = 'lh_teams-options';
var $hidden_field_name = 'lh_teams-submit_hidden';
var $group_member_states_field_name = 'lh_teams-group_member_states';
var $allow_csv_upload_field_name = 'lh_teams-allow_csv_upload';
var $add_to_menu_field_name = 'lh_teams-add_to_menu';
var $allow_personalised_email_field_name = 'lh_teams-allow_personalised_email';
var $allow_all_signups_field_name = 'lh_teams-allow_all_signups';
var $link_to_profile_field_name = 'lh_teams-link_to_profile';
var $send_emails_to_organiser_field_name = 'lh_teams-send_emails_to_organiser';
var $allow_additional_messages_field_name = 'lh_teams-allow_additional_messages';
var $allow_post_editing_field_name = 'lh_teams-allow_post_editing';
var $entry_template_field_name = 'lh_teams-entry_template';
var $onboarding_page_field_name  = 'lh_teams-onboarding_page';
var $redirect_login_to_team_field_name = 'lh_teams-redirect_login_to_team';
var $unconfirmed_message_field_name = 'lh_teams-unconfirmed_message';
var $email_title_field_name = 'lh_teams-email_title';
var $email_message_field_name = 'lh_teams-email_message';
var $email_button_field_name = 'lh_teams-email_button';
var $confirmed_message_field_name = 'lh_teams-confirmed_message';
var $post_notice_field_name = 'lh_teams-post_notice';
var $login_link_field_name = 'lh_teams-login_link';
var $posttype = 'lh-team';
var $namespace = 'lh_teams';


var $filename;
var $options;

private function return_organisers_teams($post_status, $theuser){

$args = array(
	'post_type' => 'lh-team',
	'posts_per_page' => '50',
	'post_status' => $post_status, 
    	'orderby' => 'title',
	'author'  => $theuser->ID,
    	'order' => 'asc'
);

$authorsposts = get_posts( $args );

//this is so the plugin works with co authors plus

$args = array(
	'post_type' => 'lh-team',
	'posts_per_page' => '50',
	'post_status' => array('publish'), 
    	'orderby'=> 'title',
'tax_query' => array(
			array(
				'taxonomy' => 'author',
				'field' => 'slug',
				'terms' => "cap-".$theuser->user_nicename,
			),
		),
    	'order' => 'asc'
);

$authors = get_posts( $args );

$teams = array_merge( $authorsposts, $authors); 

if ($teams){

return $teams;

} else {

return false;

}


}


private function return_managed_teams($post_status){

global $current_user;


$args = array(
	'post_type' => 'lh-team',
	'posts_per_page' => '50',
	'post_status' => $post_status, 
    	'orderby'=> 'title',
	'perm' => 'editable',
    	'order' => 'asc'
);

$perms = get_posts( $args );

//this is so the plugin works with co authors plus

$args = array(
	'post_type' => 'lh-team',
	'posts_per_page' => '50',
	'post_status' => array('publish'), 
    	'orderby'=> 'title',
'tax_query' => array(
			array(
				'taxonomy' => 'author',
				'field' => 'slug',
				'terms' => "cap-".$current_user->user_nicename,
			),
		),
    	'order' => 'asc'
);

$authors = get_posts( $args );

$teams = array_merge( $perms, $authors); 

if ($teams){

return $teams;

} else {

return false;

}


}

private function return_roles() {
    $editable_roles = get_editable_roles();
    foreach ($editable_roles as $role => $details) {
        $sub['role'] = esc_attr($role);
        $sub['name'] = translate_user_role($details['name']);
        $roles[] = $sub;
    }
    return $roles;
}


private function add_user_roles( $user_id = 0, $roles = array()) {



		if ( empty( $roles ) ) {
			return false;
		}

		$roles = array_map( 'sanitize_key', (array) $roles );
		$roles = array_filter( (array) $roles, 'get_role' );

		$user = get_user_by( 'id', (int) $user_id );



		foreach( $roles as $role ) {
			$user->add_role( $role );
		}



		return true;
	}


public function update_roles( $user_id = 0, $roles = array() ) {


		if ( empty( $roles ) ) {
			return false;
		}

		$roles = array_map( 'sanitize_key', (array) $roles );
		$roles = array_filter( (array) $roles, 'get_role' );

		$user = get_user_by( 'id', (int) $user_id );

		// remove all roles
		$user->set_role( '' );

		foreach( $roles as $role ) {
			$user->add_role( $role );
		}


		return true;
	}





private function count_attached_users($id, $list) {

if (!is_numeric($id)){

$id = get_queried_object_id();

} 

if (!is_array($list)){

$list = array($list);

}


$users = get_users( array(
  'connected_type' => $list,
  'connected_items' => $id,
  'fields' => 'all'
) );

return count($users);


}

private function get_all_wordpress_menus(){
    return get_terms( 'nav_menu', array( 'hide_empty' => false ) ); 
}

private function menu_content($postobject){

if (is_object($postobject)){

$post = $postobject;

} else {

global $post;

}



$content = "";



if ($this->can_current_user_add_player($post)){

$content .= '<li><a href="'.add_query_arg( $this->namespace.'-display', $this->namespace.'-add_player_form', get_permalink()).'#lh_teams-action_area-div" title="Add '.$this->return_member_description().'s">Add</a></li>';

}

if (current_user_can('edit_post', $post->ID)){

$content .= '<li><a href="'.add_query_arg( $this->namespace.'-display', $this->namespace.'-list_members', get_permalink()).'#lh_teams-action_area-div" title="List '.$this->return_member_description().'s">List</a></li>';

}

if ($this->can_current_user_upload_file($post) ){

$content .= '<li><a href="'.add_query_arg( $this->namespace.'-display', $this->namespace.'-upload_file_form', get_permalink()).'#lh_teams-action_area-div" title="Upload '.$this->return_member_description().' File">Upload</a></li>';

}

if ($this->can_current_user_personalise_email($post)){

$content .= '<li><a href="'.add_query_arg( $this->namespace.'-display', $this->namespace.'-personalise_email_form', get_permalink()).'#lh_teams-action_area-div">Personalise emails</a></li>';

}

if (current_user_can('edit_lh_team', $post->ID) and ($this->options[$this->allow_post_editing_field_name] == 1)){

$content .= '<li><a href="'.get_edit_post_link($post->ID).'">Edit Page</a></li>';

}

if ($this->can_current_user_add_yourself($post)){

$content .= '<li>'.$this->add_yourself_form($user, $post).'</li>';

}



return $content;

}




private function get_connection_id_by_type($type){

global $wpdb;

$sql = "SELECT p2p_id FROM ".$wpdb->prefix."p2p WHERE p2p_type = '" .$type. "'";



$from = $wpdb->get_var($sql);

if (is_numeric($from)){


return $from;

} else {


return false;

}




}

private function get_connection_to_by_id($id){

global $wpdb;

$sql = "SELECT p2p_to FROM ".$wpdb->prefix."p2p WHERE p2p_id = '" .$id. "'";

$from = $wpdb->get_var($sql);

return $from;

}


private function return_edit_user_link($userid){

if (function_exists('bp_core_get_userlink')){

return bp_core_get_user_domain( $userid).'profile/edit/';


} else {


return get_edit_user_link($userid );

}


}

private function commas_to_array($var){

if (is_array($var)){

return $var;


} else {

$pieces = explode(",", $var);

unset($var);

$var = array();

foreach ($pieces as $piece){

$var[] = trim($piece);

}

return $var;

}


}


private function clone_post_object($postid, $args = array()){


if (!class_exists('LH_clone_post_object_class')) {




require_once('includes/lh-clone-post-object-class.php');


}

$lh_clone_post_object_instance = new LH_clone_post_object_class();

$new_post_id = $lh_clone_post_object_instance->run_clone($postid, $args);

return $new_post_id;

}

private function return_lh_team_capability_mapping(){

$capabilities = array(
 'publish_posts' => 'publish_lh_teams',
 'edit_posts' => 'edit_lh_teams',
 'edit_others_posts' => 'edit_others_posts',
 'delete_posts' => 'delete_lh_teams',
 'delete_others_posts' => 'delete_others_posts',
 'delete_published_posts'  => 'delete_published_lh_teams',
'edit_published_posts' => 'edit_published_lh_teams',
 'read_private_posts' => 'read_private_posts',
 'edit_post' => 'edit_lh_team',
 'delete_post' => 'delete_lh_team',
 'read_post' => 'read_post'
);

return $capabilities;


}

private function return_new_lh_team_capabilities(){

$all_capabilities = array_values($this->return_lh_team_capability_mapping());

$new_capabilities = array();

foreach ($all_capabilities as $all_capability){

if (strpos($all_capability, 'lh_team') !== false) {

$new_capabilities[] = $all_capability;


}



}

return $new_capabilities;


}



private function can_current_user_add_player($post){

if (($this->options[$this->allow_all_signups_field_name] == 1) or current_user_can('edit_post', $post->ID)){

return true;

} else {

return false;

}


}

private function can_current_user_personalise_email($post){

if (($this->options[$this->allow_personalised_email_field_name] == 1) and current_user_can('edit_post', $post->ID)){

return true;

} else {

return false;

}

}


private function can_current_user_add_yourself($post){

if ((($this->options[$this->allow_all_signups_field_name] == 0) and current_user_can('edit_post', $post->ID)) or (($this->options[$this->allow_all_signups_field_name] == 1) and is_user_logged_in())){

return true;

} else {

return false;

}

}



private function can_current_user_upload_file($post){

if (($this->options[$this->allow_csv_upload_field_name] == 1) and current_user_can('edit_post', $post->ID)){

return true;


} else {

return false;



}


}

private function ensure_user_is_added($email, $first_name, $last_name){

$user = get_user_by( 'email', $email);

if (!$user){

$user_id = $this->handle_new_user( $email, $first_name, $last_name );

$user = get_user_by( 'ID', $user_id);

}


return $user;
}

private function action_add_player($post ){

if ($this->can_current_user_add_player($post)){

if (!is_email($_POST[$this->namespace.'-user_email'] )){
        
	wp_die( __( 'Error: please fill out a valid email address' ) );

} elseif (($_POST[$this->namespace.'-first_name'] == "") or ($_POST[$this->namespace.'-last_name'] == "")){

	wp_die( __( 'Error: please fill out a first name and last name' ) );

} else {

$email = $_POST[$this->namespace.'-user_email'];

$first_name = sanitize_text_field($_POST[$this->namespace.'-first_name']);

$last_name = sanitize_text_field($_POST[$this->namespace.'-last_name']);

}

$user = $this->ensure_user_is_added($email, $first_name, $last_name);


if ($user){


$result = $this->handle_initial_signage($user);

}

if (is_numeric($result)){

return "User successfully inserted";


} else {

return "Something went wrong";


}

}

}

private function action_personalise_email($post){

ob_start();

if( $this->can_current_user_personalise_email($post)) {

if (isset($_POST[$this->namespace.'-personalised_email_message'] )){

if ($_POST[$this->namespace.'-personalised_email_message'] == ""){

delete_post_meta($post->ID, '_'.$this->namespace.'-personalised_email_message');

echo "Message deleted";


} else {

update_post_meta($post->ID, '_'.$this->namespace.'-personalised_email_message', wp_kses_post($_POST[$this->namespace.'-personalised_email_message']));

echo "Message Updated";


}

}

} else {

echo "not permitted";

}

$content = ob_get_contents();

ob_end_clean();
  

return $content;

}

private function action_upload_file(){

 ob_start();

if ( $this->can_current_user_upload_file($post) ) {

if($_FILES[$this->namespace.'-upload_file' ]['error'] == 0){

echo "ready";

// check there are no errors
    $name = $_FILES[$this->namespace.'-upload_file' ]['name'];
    $ext = strtolower(end(explode('.', $_FILES[$this->namespace.'-upload_file' ]['name'])));
    $type = $_FILES[$this->namespace.'-upload_file' ]['type'];
    $tmpName = $_FILES[$this->namespace.'-upload_file' ]['tmp_name'];

    // check the file is a csv


if($ext === 'csv'){

echo "right";

$newusers = $this->import_csv_to_array($tmpName);

//print_r($newusers);

}



  


} else {

echo "user not permitted to upload file";


}
  
}

$content = ob_get_contents();

ob_end_clean();

return $content;

}

private function import_csv_to_array($filename='', $delimiter=','){

if(!file_exists($filename) || !is_readable($filename)){

return false;

//echo "file cant exist";

} else {
	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		{
			if(!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}

	return $data;

}
}





private function import_csv_to_unique_array($csv) {


$arr = explode(',', $csv);


foreach ($arr as $value) {
if ($value != ''){
   $new[] = trim($value);
}
}

$new = array_unique($new);

return $new;


}


private function import_and_insert_user($newuser) {

//print_r($newuser);


$defaults = array('user_nicename','user_url','user_email','display_name','nickname','first_name','last_name','description'); 	


//print_r($default);

echo "user doesn't exists";

unset($newuser['ID']);

unset($newuser['role']);

$userdata = array( 
      'user_login' => esc_attr($newuser['user_email']),
      'user_email' => esc_attr($newuser['user_email']),
      'user_pass' => wp_generate_password('8')
 );  

foreach ($defaults as $default) {
 


  if ($newuser[$default]){
  
  $userdata[$default] = esc_attr($newuser[$default]); 
						
						}

}

//print_r($userdata);

$user_id = wp_insert_user($userdata);


//On success
if ( !is_wp_error($user_id) ) {



echo "user created";


}






}

private function return_team_member_array(){

$array = array('_unconfirmed_team_member' => 'Unconfirmed Member','_confirmed_team_member' => 'Confirmed Member','_retired_team_member' => 'Retired Member','_blocked_team_member' => 'Blocked Member');

$array = apply_filters( 'lh_teams_return_team_member_states_filter', $array);

return $array;

}

private function return_team_member_states(){

$states = array_keys($this->return_team_member_array());
 
return $states;
	
}

private function clean_user_states($type, $user_id, $post_id){

$array_to_remove = array($type);

$states = array_diff($this->return_team_member_states(),$array_to_remove);

foreach ($states as $state){


$foo = p2p_type( $this->namespace.$state )->disconnect( $user_id, $post_id );

}


}

private function return_member_description(){

$description = 'player';

$description = apply_filters( 'lh_teams_return_member_description_filter', $description);

return $description;


}

private function return_group_description(){


$description = 'team';

$description = apply_filters( 'lh_teams_return_group_description_filter', $description);

return $description;

}


private function maybe_upgrade_user($user){

//only run this process if the current user has an unclaimed role
if ($user->roles[0] == 'unclaimed'){


$default_role = get_option( 'default_role' );

wp_update_user(array(
    'ID' => $user->ID,
    'role' => $default_role
));


}




}



private function return_player_list($user, $post){

if ((current_user_can('edit_post', $post->ID)) and ($_GET[$this->namespace.'-display'] == $this->namespace."-list_members")) {

$content .= "<h2>".__(ucwords($this->return_member_description()).' Listings', $this->namespace)." </h2>";

$content .= "<p>".__('This area is not publicly visible.', $this->namespace)."</p>";

$content = apply_filters( 'lh_teams_return_player_list_content_first_filter', $content, $user, $post);

if ($this->options[$this->group_member_states_field_name] == "1"){

$content .= $this->list_users(array('_unconfirmed_team_member', '_confirmed_team_member'), "Active Members", $post);

$content .= $this->list_users('_retired_team_member', "Inactive", $post);


} else {


$content .= $this->list_users('_unconfirmed_team_member', "Unconfirmed Members", $post);

$content .= $this->list_users('_confirmed_team_member', "Confirmed Members", $post);

$content .= $this->list_users('_retired_team_member', "Retired Members", $post);

}

}

return $content;


}



private function add_yourself_form($userobject, $post){


if (is_object($userobject)){

$user = $userobject;

} else {

$user = wp_get_current_user();

}

if (!p2p_connection_exists( $this->namespace.'_confirmed_team_member', array( 'from' => $user->ID, 'to' => $post->ID ) )){

$content .= '<form name="'.$this->namespace.'-add_yourself_form" action="'.get_permalink().'" method="post">';

$content .= "\n<input name=\"".$this->namespace."-save_data-nonce\" value=\"".wp_create_nonce($this->namespace."-save_data-nonce")."\" type=\"hidden\" />";

$content .= '<input type="hidden" name="'.$this->namespace.'-save_data-action" value="add_yourself" />';

$content .= '<a href="" class="'.$this->namespace.'-team_management-submit_link"  title="Add yourself to '.$post->post_title.'" >Join</a>';

$content .= '</form>';


wp_enqueue_script('lh_teams-script', plugins_url( '/scripts/lh-teams.js' , __FILE__ ), array(), '1.00a', true  );

} elseif (p2p_connection_exists( $this->namespace.'_confirmed_team_member', array( 'from' => $user->ID, 'to' => $post->ID ) )){

$content .= 'Joined';

}


return $content;

}


private function echo_add_player_form($user, $post){

if (($_GET[$this->namespace.'-display'] == $this->namespace."-add_player_form") and ($this->can_current_user_add_player($post)) and apply_filters( 'lh_teams_return_can_user_add_player_filter', true, $user, $post)){


if ( is_user_logged_in() ){

$current_user = wp_get_current_user();


echo '<h2>Add a '.$this->return_member_description().' to  this '.$this->return_group_description().'</h2>';

if ($this->options[$this->send_emails_to_organiser_field_name] == 1){

echo '<p class="organisation_note">IMPORTANT: When you add a '.$this->return_member_description().' you will receive the '.$this->return_member_description().'s activation email, this email must be forwarded to the '.$this->return_member_description().'.  Include a few nice words to reinforce your '.$this->return_group_description().'s appreciation of their support.</p>';


} else {


echo '<p>'.$this->return_member_description().'s you add to the '.$this->return_group_description().' will receive an email asking them to confirm their '.$this->return_group_description().' membership</p>';

}

} else {

?>

<h3>Add <?php echo $this->return_member_description(); ?>'s to  this <?php echo $this->return_group_description(); ?></h3>

<p>Users who are not logged in will receive an email asking them to confirm their <?php echo $this->return_group_description(); ?> membership</p>

<?php




}






?>

<form name="<?php echo $this->namespace; ?>-add_player_form" id="<?php echo $this->namespace; ?>-add_player_form" action="<?php  echo add_query_arg( $this->namespace.'-display', $this->namespace.'-add_player_form', get_permalink()); ?>" method="post">

<p>
<!--[if lt IE 10]><label for="<?php echo $this->namespace; ?>-first_name">First Name</label><![endif]-->
<input id="<?php echo $this->namespace; ?>-first_name" name="<?php echo $this->namespace; ?>-first_name" type="text" placeholder="First name (required)" required="required" />
</p>

<p>
<!--[if lt IE 10]><label for="<?php echo $this->namespace; ?>-last_name">Last Name</label><![endif]-->
<input id="<?php echo $this->namespace; ?>-last_name" name="<?php echo $this->namespace; ?>-last_name" type="text" placeholder="Last name (required)" required="required" />
</p>



<p>
<!--[if lt IE 10]><label for="<?php echo $this->namespace; ?>-user_email">Email address</label><![endif]-->
<input id="<?php echo $this->namespace; ?>-user_email" name="<?php echo $this->namespace; ?>-user_email" type="email" placeholder="Email Address (required)" required="required" />
</p>

<?php if ( current_user_can( 'manage_options' ) ) { ?>

<p>
<label for="<?php echo $this->namespace; ?>-send_confirmation_email"><?php _e("Send Confirmation Email:", $this->namespace); ?></label><br/>
<select name="<?php echo $this->namespace; ?>-send_confirmation_email" id="<?php echo $this->namespace; ?>-send_confirmation_email">
<option value="no">No</option>
<option value="yes">Yes</option>
</select> (this field is admin only)
</p>

<?php } ?>



<?php if (current_user_can('edit_users')){ ?>

<p><label><?php _e("Member Status:", $this->namespace); ?></label><br/>
<select name="<?php echo $this->namespace; ?>-user_state" id="<?php echo $this->namespace; ?>-user_state">

<?php $members_array = $this->return_team_member_array(); 

foreach ($members_array as $key => $value){

?>
<option value="<?php  echo $key; ?>"><?php  echo $value; ?></option>
<?php  } ?>
</select> (this field is admin only)
</p>

  <?php } ?>

<?php apply_filters( 'lh_teams_add_form_filter'); ?>

<?php if ($this->options[$this->allow_additional_messages_field_name] == 1){ ?>
<p>
<!--[if lt IE 10]><label for="<?php echo $this->namespace; ?>-message_to_user"><?php echo _x('Send a message', $this->namespace); ?></label><![endif]-->
<textarea id="<?php echo $this->namespace; ?>-message_to_user" name="<?php echo $this->namespace; ?>-message_to_user" cols="45" rows="5" aria-required="true" placeholder="Send a message (optional)"><?php echo get_post_meta( $post->ID, '_'.$this->namespace.'-message_to_user', true ); ?></textarea>
</p>

<?php } ?>

<?php wp_nonce_field( $this->namespace."-save_data-nonce", $this->namespace."-save_data-nonce" ); ?>
<input type="hidden" name="<?php echo $this->namespace; ?>-save_data-action" value="add_player" />

<p>
<input type="submit" id="<?php echo $this->namespace."-add_player_submit"; ?>" name="<?php  echo $this->namespace."-add_player_submit"; ?>" value="Add <?php echo $this->return_member_description(); ?>" />
</p>

</form>

<?php

}


}

private function echo_personalise_email_form($user, $post){

if (($_GET[$this->namespace.'-display'] == $this->namespace."-personalise_email_form") and current_user_can( 'edit_post', $post->ID )){


?>


<h2>Personalise <?php echo $this->return_member_description(); ?> Emails</h2>


<form name="<?php echo $this->namespace; ?>-add_player_form" id="<?php echo $this->namespace; ?>-add_player_form" action="<?php  echo add_query_arg( $this->namespace.'-display', $this->namespace.'-personalise_email_form', get_permalink()); ?>" method="post">

<p>
<!--[if lt IE 10]><label for="<?php echo $this->namespace; ?>-personalised_email_message"><?php echo _x('Send a message', $this->namespace); ?></label><![endif]-->
<textarea id="<?php echo $this->namespace; ?>-personalised_email_message" name="<?php echo $this->namespace; ?>-personalised_email_message" cols="45" rows="5" aria-required="true" placeholder="Customise your message"><?php echo get_post_meta( $post->ID, '_'.$this->namespace.'-personalised_email_message', true ); ?></textarea>
</p>

<?php wp_nonce_field( $this->namespace."-save_data-nonce", $this->namespace."-save_data-nonce" ); ?>
<input type="hidden" name="<?php echo $this->namespace; ?>-save_data-action" value="personalise_email" />

<p>
<input type="submit" id="<?php echo $this->namespace."-personalise_email_submit"; ?>" name="<?php  echo $this->namespace."-personalise_email_submit"; ?>" value="Update Message" />
</p>

</form>

<?php


}

}


private function echo_add_users_by_file_form($user, $post){

if (($_GET[$this->namespace.'-display'] == $this->namespace."-upload_file_form") and current_user_can( 'edit_post', $post-ID )){




?>


<h2>Upload a file of <?php echo $this->return_member_description(); ?>s</h2>

<form name="<?php echo $this->namespace; ?>-upload_file_form" id="<?php echo $this->namespace; ?>-upload_file_form" action="<?php  echo get_permalink(); ?>" accept="text/csv" method="post" enctype="multipart/form-data">

<p><label for="<?php echo $this->namespace; ?>-upload_file"><?php _e( 'CSV file' , $this->namespace); ?></label>
<input type="file" id="<?php echo $this->namespace; ?>-upload_file" name="<?php echo $this->namespace; ?>-upload_file" value=""  />
</p>

<?php wp_nonce_field( $this->namespace."-save_data-nonce", $this->namespace."-save_data-nonce" ); ?>
<input type="hidden" name="<?php echo $this->namespace; ?>-save_data-action" value="upload_user_file" />

<p>
<input type="submit" id="<?php  echo $this->namespace."-upload_file_submit"; ?>" name="<?php  echo $this->namespace."-upload_file_submit"; ?>" value="Upload User File" />
</p>

</form>


<?php

}


}




private function return_move_form($name,$type,$user, $title){

if (in_array($type, $this->return_team_member_states())) {


$content = "<td>\n<form name=\"lh_teams_".$name."_member-".$user->ID."\" id=\"lh_teams-".$name."_member-".$user->ID."\" action=\"\" method=\"post\">\n
<input name=\"lh_teams-action-id\" value=\"".$user->data->ID."\" type=\"hidden\" />\n
<input name=\"lh_teams-action-new_list\" value=\"".$type."\" type=\"hidden\" />\n
<input name=\"lh_teams-action-nonce\" value=\"".wp_create_nonce($this->namespace."-action-nonce".$user->data->ID)."\" type=\"hidden\" />\n
<a title=\"Remove this ".$this->return_member_description()." from the ".$this->return_group_description()."\" href=\"javascript:lh_teams_move_member('lh_teams-".$name."_member-".$user->data->ID."','".$name."')\">".ucwords($title)."</a>\n
</form>\n</td>";

}

return $content;


}





private function get_connection_type_by_id($id){

global $wpdb;

$sql = "SELECT p2p_type FROM ".$wpdb->prefix."p2p WHERE p2p_id = '" .$id. "'";

$type = $wpdb->get_var($sql);

return $type;


}


private function get_connection_from_by_id($id){

global $wpdb;

$sql = "SELECT p2p_from FROM ".$wpdb->prefix."p2p WHERE p2p_id = '" .$id. "'";

$from = $wpdb->get_var($sql);

return $from;

}

private function get_connection_to_by_fromid($id,$types){

global $wpdb;

$sql = "SELECT p2p_to FROM ".$wpdb->prefix."p2p WHERE p2p_from = '" .$id. "' and p2p_type in ('".implode("','",$types)."') LIMIT 1";

$from = $wpdb->get_var($sql);

if (is_numeric($from)){

return $from;

} else {

return false;

}

}



private function personalise_message($message,$post,$user){

$post_author = apply_filters( 'lh_teams_return_post_author_for_personalised_message', $post->post_author, $post, $user);

$author = get_user_by( 'id', $post_author );


$message = str_replace('%post_title%', $post->post_title, $message);
$message = str_replace('%team_title%', $post->post_title, $message);
$message = str_replace('%user_first_name%', $user->first_name, $message);
$message = str_replace('%user_last_name%', $user->last_name, $message);
$message = str_replace('%user_user_email%', $user->user_email, $message);
$message = str_replace('%user_user_login%', $user->user_login, $message);
$message = str_replace('%author_first_name%', $author->first_name, $message);
$message = str_replace('%author_last_name%', $author->last_name, $message);
$message = str_replace('%author_user_email%', $author->user_email, $message);
$message = str_replace('%author_user_login%', $author->user_login, $message);
$message = str_replace('%bloginfo_name%',get_bloginfo('name','display'), $message);

return $message;

}

private function send_unconfirmed_email($user, $post, $type){



if (($type == '_unconfirmed_team_member') and ($_POST[$this->namespace.'-send_confirmation_email'] != "no")){



$this->send_email( $user, $post, $type );
return true;

} else {

return false;
}

}

private function action_signing( $user, $post, $type) {



//only run this process if the current user has no current role
if (!$user->roles[0]){

wp_update_user(array(
    'ID' => $user->ID,
    'role' => 'unclaimed'
));


}



if (p2p_connection_exists( $this->namespace.$type, array( 'from' => $user->ID, 'to' => $post->ID ) )){



//remove other states for clean up purposes
$this->clean_user_states($type, $user->ID, $post->ID);




//the post already has this state

$error = new WP_Error( 'error', __( "You user remains un ".$this->return_group_description(), $this->namespace ) );

$this->send_unconfirmed_email($user, $post, $type);

return $error;

} else {


if ( $p2p_id = p2p_type( 'lh_teams_unconfirmed_team_member' )->get_p2p_id( $user->ID, $post->ID )) {
  // connection exists
  $comment = p2p_get_meta( $p2p_id, 'comment', true );

}

$current_user = wp_get_current_user();


if ($current_user->ID !== $user->ID){

$added_by = $current_user->ID;

}



if ($result = p2p_type( $this->namespace.$type )->connect( $user->ID, $post->ID, array(
 'comment' => $comment,
    'date' => current_time('mysql'),
    'added_by' => $added_by
) )){

$this->clean_user_states($type, $user->ID, $post->ID);

$this->send_unconfirmed_email($user, $post, $type);


//if the user is confirmed then we can upgrade the users
if ($type == '_confirmed_team_member'){

$this->maybe_upgrade_user($user);

}

//remove the login token if it exists
$this->delete_token($user,$postdata);


return $result;


}

}






}

private function create_token( $user, $post ) {

// random salt
$token = wp_generate_password( 20, false );

// we're sending this to the user
$hash  = wp_hash($token);

$p2p_id = p2p_type( 'lh_teams_unconfirmed_team_member' )->get_p2p_id( $user->ID, $post->ID );

if ( $p2p_id ) {

$key = $this->namespace."-confirmation_token";

p2p_update_meta( $p2p_id, $key, $hash);

return $token;

} else {

return false;

}

}

private function use_email_template( $message ) {

if (file_exists(get_stylesheet_directory().'/'.$this->namespace.'-template.php')){

ob_start();

include( get_stylesheet_directory().'/'.$this->namespace.'-template.php');

$message = ob_get_contents();

ob_end_clean();


} else {

ob_start();

include( plugin_dir_path( __FILE__ ).'/'.$this->namespace.'-template.php');

$message = ob_get_contents();

ob_end_clean();


}


if (!class_exists('LH_Css_To_Inline_Styles')) {


require_once('includes/lh-css-to-inline-styles-class.php');


}


$doc = new DOMDocument();

$doc->loadHTML($message);

// create instance
$lh_css_to_inline_styles = new LH_Css_To_Inline_Styles();

$lh_css_to_inline_styles->setHTML($message);

$lh_css_to_inline_styles->setCSS($doc->getElementsByTagName('style')->item(0)->nodeValue);

// output

$message = $lh_css_to_inline_styles->convert(); 

return $message;

}


private function generate_url( $user, $post ) {

if ($token = $this->create_token( $user, $post )){

	$url =  ' '.preg_replace('/\?.*/', '', $this->curpageurl());
	$url .= "?".$this->namespace."-action=login&".$this->namespace."-uid=".$user->ID."&".$this->namespace."-token=".$token;

	return $url;

} else {


return false;

}
}



private function curpageurl() {
	$pageURL = 'http';

	if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")){
		$pageURL .= "s";
}

	$pageURL .= "://";

	if (($_SERVER["SERVER_PORT"] != "80") and ($_SERVER["SERVER_PORT"] != "443")){
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

}

	return $pageURL;
}

private function return_email_button_text() {

if (isset($this->options[$this->email_button_field_name])){

$email_button_text = $this->options[$this->email_button_field_name];

} else {

$email_button_text = "Confirm my membership!";


}

$email_button_text = apply_filters( 'lh_teams_return_email_button_text_filter', $email_button_text);

return $email_button_text;

}

private function return_action_button($user,$post){

$message = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><a class="confirm_button" href="'.$this->generate_url( $user, $post ).'">'.$this->return_email_button_text().'</a></td>
        </tr>
      </table>
    </td>
  </tr>
</table>';


return $message;

}


private function return_email_title( $post ) {

return $this->options[$this->email_title_field_name];

}

private function return_email_message( $post ) {

$meta = get_post_meta( $post->ID, '_'.$this->namespace.'-personalised_email_message', true );

if (($meta != '') and ($this->options[$this->allow_personalised_email_field_name] == 1)) {

return $meta;

} else {

return $this->options[$this->email_message_field_name];

}

}

private function send_email( $user, $post, $type ) {

$title = $this->personalise_message($this->return_email_title($post),$post,$user);

$message = wpautop($this->return_email_message($post));

$headers = array('Content-Type: text/html; charset=UTF-8');

$message = $this->personalise_message($message,$post,$user);


//ensure the email contains the confirmation url
if (strpos($message, '%lh_teams_confirmation_url%') !== false) {

$message = str_replace('%lh_teams_confirmation_url%', $this->return_action_button($user,$post), $message);

} else {

$message .= $this->return_action_button($user,$post);

}


$message = $this->use_email_template($message);

if (!$lh_email_queue_instance){

$lh_email_queue_instance = new LH_Email_queue_class();


}

//this codes send the activation emails to the organiser rather than the potential new member if required

  if (($type == '_unconfirmed_team_member') and ($this->options[$this->send_emails_to_organiser_field_name] == 1)){

$p2p_id = p2p_type( 'lh_teams_unconfirmed_team_member' )->get_p2p_id( $user->ID, $post->ID );

$added_by = p2p_get_meta( $p2p_id, 'added_by', true );

$send_to_user = get_user_by( 'id', $added_by );

}

if (!$send_to_user){

$send_to_user = $user;

}



$lh_email_queue_instance->queue_email($send_to_user, $post, $title, $message, $headers);

}


private function handle_initial_signage( $user) {

global $post;

if (current_user_can('edit_user', $user->ID) ){

if (isset($_POST[$this->namespace.'-user_state'])){

$result = $this->action_signing( $user, $post, $_POST[$this->namespace.'-user_state']);

} else {

$result = $this->action_signing( $user, $post, '_confirmed_team_member');

}

} else {

$result = $this->action_signing( $user, $post, '_unconfirmed_team_member');


}

return $result;


}


private function list_users($type, $title, $post){

if (is_array($type)){

foreach ( $type as $connected ) { 

$connected_type[] = $this->namespace.$connected;

}

} else {

$connected_type = array($this->namespace.$type);



}



$users = get_users( array(
  'connected_type' => $connected_type,
  'connected_items' => $post->ID
) );

$content = "<table>\n<caption>".$title."</caption>\n";

foreach ( $users as $user ) { 

$content .= "<tr>\n";

$content .= "<td>".get_avatar( get_the_author_meta( 'email', $user->data->ID ), '32' )."</td>\n";

$content .= "<td>".get_the_author_meta( 'display_name', $user->data->ID )."</td>\n";


$content .= "<td>";


if (get_the_author_meta( 'email', $user->data->ID )){

$content .= "<a href=\"mailto:".get_the_author_meta( 'email', $user->data->ID )."\">".get_the_author_meta( 'email', $user->data->ID )."</a>";

}

$content .= "</td>\n";


$content .= "<td>";

if (current_user_can('edit_user', $user->data->ID ) and ($this->options[$this->link_to_profile_field_name] == 1) ){ 

$content .= "<a href=\"".$this->return_edit_user_link( $user->data->ID )."\">Edit</a>";

}

$content .= "</td>\n";


if (($type == '_confirmed_team_member') or ($type == '_unconfirmed_team_member') or (in_array($this->namespace."_confirmed_team_member", $connected_type)) or (in_array($this->namespace."_unconfirmed_team_member", $connected_type)) ){

$content .= $this->return_move_form('retire' ,'_retired_team_member',$user, apply_filters( 'lh_teams_string_retire', 'retire member'));

$content .= $this->return_move_form( 'block','_blocked_team_member',$user, apply_filters( 'lh_teams_string_block', 'block member'));


}




$content .= "</tr>\n";

}

$content .= "</table>\n";

return $content;


}

private function register_teams_post_type() {

$capabilities = $this->return_lh_team_capability_mapping();

$label = 'Teams';

$labels = array(
    'name' => 'Team',
      'singular_name' => 'Team',
      'menu_name' => 'Teams',
      'add_new' => 'Add New',
      'add_new_item' => 'Add New Team',
      'edit' => 'Edit team',
      'edit_item' => 'Edit Team',
      'new_item' => 'New Team',
      'view' => 'View Team',
      'view_item' => 'View Team',
      'search_items' => 'Search Teams',
      'not_found' => 'No teams Found',
      'not_found_in_trash' => 'No Teams Found in Trash',
      'parent' => 'Parent Team');

$slug = 'team';



register_post_type($this->posttype, array(
        'label' => $label,
	'menu_icon'  => 'dashicons-groups',
        'description' => '',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
	'show_in_rest' => true,
	'capabilities'=> $capabilities,
        'hierarchical' => false,
        'rewrite' => array('slug' => get_option( 'lh_teams-post_type-slug', 'team' )),
        'query_var' => true,
        'supports' =>  array( 'title', 'editor', 'author', 'thumbnail','page-attributes'),
	'has_archive' => get_option( 'lh_teams-post_type-has_archive', 'teams' ),
        'labels' => $labels,
        )
    );



}


private function handle_new_user( $email, $first_name, $last_name ) {


global $wpdb;

$user_data = array(
                'user_pass' =>  wp_generate_password( $length=12, $include_standard_special_chars=false ),
		'first_name' => $first_name,
		'last_name' => $last_name,
                'user_login' => $email,
                'display_name' => $first_name." ".$last_name,
		'user_email' => $email,
                'role' =>  'unclaimed'
 );


$user_id = wp_insert_user($user_data);

$sql = "update ".$wpdb->users." set user_login = user_email where ID = '".$user_id."'";

$result = $wpdb->get_results($sql);

apply_filters( 'lh_teams_http_post_filter', $user_id);

return $user_id;

}



private function validate_token($user,$post,$token) {




$p2p_id = p2p_type( 'lh_teams_unconfirmed_team_member' )->get_p2p_id( $user->ID, $post->ID );


if (($meta = p2p_get_meta( $p2p_id, $this->namespace."-confirmation_token", true)) != ""){

$hash  = wp_hash($token);

if ($meta == $hash){

return true;

} else {


return false;


}

} else {

return false;

}

}


private function delete_token($user,$post) {

$p2p_id = p2p_type( 'lh_teams_unconfirmed_team_member' )->get_p2p_id( $user->ID, $post->ID );


if (($meta = p2p_get_meta( $p2p_id, $this->namespace."-confirmation_token", true)) != ""){


p2p_delete_meta( $p2p_id, $this->namespace."-confirmation_token");


}


}




public function setup_post_types() {


$this->register_teams_post_type();
 

}


function flush_rewrites_activate() {
	// call your CPT registration function here (it should also be hooked into 'init')
	$this->setup_post_type();
	flush_rewrite_rules();
}





public function save_data(){

global $post;


if ($post->post_type == 'lh-team'){



if (($_POST[$this->namespace.'-save_data-action'] == 'add_player') and (wp_verify_nonce( $_POST[$this->namespace.'-save_data-nonce'], $this->namespace.'-save_data-nonce')) and $this->can_current_user_add_player($post)) {

$GLOBALS[$this->namespace.'-save_data-result'] = $this->action_add_player($post);


} elseif (($_POST[$this->namespace.'-save_data-action'] == 'add_yourself') and (wp_verify_nonce( $_POST[$this->namespace.'-save_data-nonce'], $this->namespace.'-save_data-nonce'))) {


$current_user = wp_get_current_user();
$GLOBALS[$this->namespace.'-save_data-result'] = $this->action_signing( $current_user, $post, '_confirmed_team_member');


} elseif (($_POST[$this->namespace.'-save_data-action'] == 'upload_user_file') and (wp_verify_nonce( $_POST[$this->namespace.'-save_data-nonce'], $this->namespace.'-save_data-nonce'))) {

$GLOBALS[$this->namespace.'-save_data-result'] = $this->action_upload_file();



} elseif (($_POST[$this->namespace.'-save_data-action'] == 'personalise_email') and (wp_verify_nonce( $_POST[$this->namespace.'-save_data-nonce'], $this->namespace.'-save_data-nonce'))) {

$GLOBALS[$this->namespace.'-save_data-result'] = $this->action_personalise_email($post);



}


} else if (($_POST[$this->namespace.'-save_data-action'] == 'create_team') and (wp_verify_nonce( $_POST[$this->namespace.'-save_data-create_team'], $this->namespace.'-save_data-nonce'))) {

//print_r($_POST);

//Give the current user the right role

$userobject = wp_get_current_user();



// Add role
$userobject->add_role( 'organiser' );



$post_title = sanitize_text_field($_POST[$this->namespace.'-team_name']);

if(isset($_POST[$this->namespace.'-clone-postid']) and get_post($_POST[$this->namespace.'-clone-postid'])){

$template_id = $_POST[$this->namespace.'-clone-postid'];

} else {


$template_id = $this->options[$this->entry_template_field_name];

}


if ($to_post_id = $this->clone_post_object($template_id, array('post_author' => $userobject->ID, 'post_title' => $post_title, 'post_status' => 'publish'))){

$to_post_id = apply_filters( 'lh_teams_return_to_post_id_filter', $to_post_id);

wp_redirect(get_permalink($to_post_id));
exit;

} else {

echo $this->options[$this->entry_template_field_name];

echo "something went wrong";
exit;

}



}

}


public function create_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( ucwords($this->return_group_description()).' Types', 'taxonomy general name' ),
		'singular_name'     => _x( 'Type', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Types' ),
		'all_items'         => __( 'All Types' ),
		'parent_item'       => __( 'Parent Types' ),
		'parent_item_colon' => __( 'Parent '.ucwords($this->return_group_description()).' Type' ),
		'edit_item'         => __( 'Edit Type' ),
		'update_item'       => __( 'Update Type' ),
		'add_new_item'      => __( 'Add New Type' ),
		'new_item_name'     => __( 'New Type Name' ),
		'menu_name'         => __( 'Type' ),
	);


	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'teamtype' ),
    'capabilities'    => array(
            'manage_terms' => 'manage_options', //by default only admin
            'edit_terms' => 'manage_options',
            'delete_terms' => 'manage_options',
        'assign_terms'                =>'edit_others_posts',
    )
	);

	register_taxonomy( 'type', array( 'lh-team' ), $args );


}


public function lh_teams_managed_teams_output($atts) {

global $current_user;

    // define attributes and their defaults
    extract( shortcode_atts( array (
        'post_status' => array('publish')
    ), $atts ) );


$post_status = $this->commas_to_array($post_status);

$teams = $this->return_managed_teams($post_status);

include ('partials/lh_teams_managed_teams_output.php');


return $return_string;

}

public function lh_teams_has_managed_teams_output($atts,$content = null) {

    // define attributes and their defaults
    extract( shortcode_atts( array (
        'post_status' => array('publish')
    ), $atts ) );

$post_status = $this->commas_to_array($post_status);

$teams = $this->return_managed_teams($post_status);

if ($teams){

return do_shortcode($content);


} else {


return '';

}


}

public function lh_teams_teams_joined_output($atts) {

    // define attributes and their defaults
    extract( shortcode_atts( array (
        'season' => 'Summer 2014-15 Competition Sign on Sheet',
        'term' => 'lh_user_tax_team'
    ), $atts ) );

if ($_GET['user_id']){

$userobject = get_userdata( $_GET['user_id'] );


} else {

$userobject = wp_get_current_user();

}

if (!$userobject){

$return_string .= "invalid user id supplied";

} else {



ob_start();

include ('partials/lh_teams_teams_joined_output.php');


 
$return_string = ob_get_clean();
ob_end_clean();

}

return $return_string;

}

public function lh_teams_team_entry_output($atts) {


    // define attributes and their defaults
    extract( shortcode_atts( array ( 'clone' =>''), $atts ) );




$return_string = '';



if ( is_user_logged_in() ){ 

include ('partials/lh_teams_team_entry_output.php');






 }



return $return_string;


}

public function register_shortcodes(){

add_shortcode('lh_teams_managed_teams', array($this,"lh_teams_managed_teams_output"));
add_shortcode('lh_teams_has_managed_teams', array($this,"lh_teams_has_managed_teams_output"));
add_shortcode('lh_teams_teams_joined', array($this,"lh_teams_teams_joined_output"));
add_shortcode('lh_teams_team_entry', array($this,"lh_teams_team_entry_output"));


}



public function move_team_member(){

global $post;


if ($user = get_user_by('ID', $_POST['lh_teams-action-id'])){

if( current_user_can('edit_post', $post->ID) ) {


if (wp_verify_nonce( $_POST['lh_teams-action-nonce'], 'lh_teams-action-nonce'.$_POST['lh_teams-action-id'])){


$this->action_signing( $user, $post, $_POST['lh_teams-action-new_list']);


}


}

}


}



function the_content_filter( $content ) {

global $post;

$the_menu = get_term_by( 'id', $this->options[$this->add_to_menu_field_name], 'nav_menu' );

$current_user = wp_get_current_user();


if (($post->post_type == 'lh-team') and is_singular()){

if ($GLOBALS[$this->namespace.'-team_joined']){

if (!is_numeric($GLOBALS[$this->namespace.'-team_joined'])){

$content .= "There was an error: ";



} else {


$content = wpautop($this->personalise_message(do_shortcode($this->options[$this->confirmed_message_field_name]), $post, $current_user));


}


}



$post->comment_status = "closed";


//the actual javascript
wp_enqueue_script('lh_teams-script', plugins_url( '/scripts/lh-teams.js' , __FILE__ ), array(), '1.00a', true  );

//force the processing of the queue
wp_enqueue_script('lh_teams-endpoint_script', site_url( 'lh_email_queue_endpoint/' ), array(), '1.00', true  );

//Add post Notice

$content .= $this->options[$this->post_notice_field_name];


if (!$the_menu->slug){

$content .= '<div id="lh_teams-functions_menu">
<ul>';

$content .= $this->menu_content($post);

$content .= '</ul>
</div>';

}


if (current_user_can('edit_post', $post->ID) and isset($_GET['lh_teams-display'])){


ob_start();

echo '<div id="lh_teams-action_area-div">';

$add = '<h3>Admin Area</h3>';

$add = apply_filters( 'lh_teams_return_admin_area_content_filter', $add, $user, $post);

echo $add;

if ($GLOBALS[$this->namespace.'-save_data-result']){

echo $this->format_results($post);

}

$this->echo_add_player_form($user, $post);

echo $this->return_player_list($user, $post);

$this->echo_add_users_by_file_form($user, $post);

$this->echo_personalise_email_form($user, $post);

echo '</div>';

$content .= ob_get_contents();

ob_end_clean();

}



}

// Returns the content.
return $content;


}

function plugin_menu() {

add_submenu_page('edit.php?post_type='.$this->posttype, __('Settings', $this->namespace), __('Settings', $this->namespace), 'manage_options', $this->filename, array($this, 'plugin_options'));


}

function plugin_options() {


if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}


if( isset($_POST[ $this->hidden_field_name ]) && $_POST[ $this->hidden_field_name ] == 'Y' ) {

if (($_POST[$this->group_member_states_field_name] == "0") || ($_POST[$this->group_member_states_field_name] == "1")){
$options[$this->group_member_states_field_name] = $_POST[ $this->group_member_states_field_name ];
}


if (($_POST[$this->allow_csv_upload_field_name] == "0") || ($_POST[$this->allow_csv_upload_field_name] == "1")){
$options[$this->allow_csv_upload_field_name] = $_POST[ $this->allow_csv_upload_field_name ];
}

if (($_POST[$this->allow_personalised_email_field_name] == "0") || ($_POST[$this->allow_personalised_email_field_name] == "1")){
$options[$this->allow_personalised_email_field_name] = $_POST[ $this->allow_personalised_email_field_name ];
}

if (($_POST[$this->allow_all_signups_field_name] == "0") || ($_POST[$this->allow_all_signups_field_name] == "1")){
$options[$this->allow_all_signups_field_name] = $_POST[ $this->allow_all_signups_field_name ];
}

if (($_POST[$this->send_emails_to_organiser_field_name] == "0") || ($_POST[$this->send_emails_to_organiser_field_name] == "1")){
$options[$this->send_emails_to_organiser_field_name] = $_POST[ $this->send_emails_to_organiser_field_name ];
}



if (get_post($_POST[$this->entry_template_field_name])){
$options[$this->entry_template_field_name] = $_POST[ $this->entry_template_field_name ];

}

if (get_post($_POST[$this->onboarding_page_field_name])){
$options[$this->onboarding_page_field_name] = $_POST[ $this->onboarding_page_field_name ];

}


if (($_POST[$this->redirect_login_to_team_field_name] == "0") || ($_POST[$this->redirect_login_to_team_field_name] == "1")){
$options[$this->redirect_login_to_team_field_name] = $_POST[ $this->redirect_login_to_team_field_name ];
}



if ($_POST[ $this->unconfirmed_message_field_name] != ""){
$options[ $this->unconfirmed_message_field_name ] = wp_kses_post($_POST[ $this->unconfirmed_message_field_name ]);
}

if ($_POST[ $this->email_title_field_name] != ""){
$options[ $this->email_title_field_name ] = sanitize_text_field($_POST[ $this->email_title_field_name ]);
}

if ($_POST[ $this->email_message_field_name] != ""){
$options[ $this->email_message_field_name ] = wp_kses_post($_POST[ $this->email_message_field_name ]);
}

if (($_POST[$this->allow_additional_messages_field_name] == "0") || ($_POST[$this->allow_additional_messages_field_name] == "1")){
$options[$this->allow_additional_messages_field_name] = $_POST[ $this->allow_additional_messages_field_name ];
}


if (($_POST[$this->login_link_field_name] == "0") || ($_POST[$this->login_link_field_name] == "1")){
$options[$this->login_link_field_name] = $_POST[ $this->login_link_field_name ];
}

if (($_POST[$this->allow_post_editing_field_name] == "0") || ($_POST[$this->allow_post_editing_field_name] == "1")){
$options[$this->allow_post_editing_field_name] = $_POST[ $this->allow_post_editing_field_name ];
}



if (($_POST[$this->link_to_profile_field_name] == "0") || ($_POST[$this->link_to_profile_field_name] == "1")){
$options[$this->link_to_profile_field_name] = $_POST[ $this->link_to_profile_field_name ];
}


if ($_POST[ $this->confirmed_message_field_name] != ""){
$options[ $this->confirmed_message_field_name ] = wp_kses_post($_POST[ $this->confirmed_message_field_name ]);
}

if (isset($_POST[ $this->email_button_field_name])){
$options[ $this->email_button_field_name ] = sanitize_text_field($_POST[ $this->email_button_field_name ]);
}



if ($_POST[ $this->post_notice_field_name] != ""){
$options[ $this->post_notice_field_name] = wp_kses_post($_POST[ $this->post_notice_field_name ]);
}

if (($_POST[ $this->add_to_menu_field_name] != "") and is_numeric($_POST[ $this->add_to_menu_field_name])){
$options[ $this->add_to_menu_field_name ] = $_POST[ $this->add_to_menu_field_name ];
} else {

unset($options[ $this->add_to_menu_field_name ]);


}




if (update_option( $this->opt_name, $options )){


$this->options = get_option($this->opt_name);


?>
<div class="updated"><p><strong><?php _e('Values saved', $this->namespace ); ?></strong></p></div>
<?php

    } 

}

   // Now display the settings editing screen

include ('partials/option-settings.php');


}

public function register_p2p_connection_types() {

//Allow attachments to be associated with teams

  p2p_register_connection_type( array(
        'name' => $this->namespace.'_image_of_team',
        'from' => 'attachment',
        'to' => $this->posttype,
'admin_column' => 'from'
    ) );

//Register the 4 connection states, unconfirmed, confirmed, retired, and blocked


  p2p_register_connection_type( array(
	'title' => 'Unconfirmed Member',
        'name' => $this->namespace.'_unconfirmed_team_member',
        'from' => 'user',
        'to' => $this->posttype,
'admin_column' => 'from',
'admin_dropdown' => 'from'
    ) );



  p2p_register_connection_type( array(
	'title' => 'Confirmed Member',
        'name' => $this->namespace.'_confirmed_team_member',
        'from' => 'user',
        'to' => $this->posttype,
'admin_column' => 'from',
'admin_dropdown' => 'from'
    ) );


  p2p_register_connection_type( array(
	'title' => 'Retired Member',
        'name' => $this->namespace.'_retired_team_member',
        'from' => 'user',
        'to' => $this->posttype,
'admin_column' => 'from',
'admin_dropdown' => 'from'
    ) );



  p2p_register_connection_type( array(
	'title' => 'Blocked Member',
        'name' => $this->namespace.'_blocked_team_member',
        'from' => 'user',
        'to' => $this->posttype,
'admin_column' => 'from',
'admin_dropdown' => 'from'
    ) );



}




private function format_results($post){

ob_start();

if( is_wp_error( $GLOBALS[$this->namespace.'-save_data-result']) ) {

$error = $GLOBALS[$this->namespace.'-save_data-result'];

echo "There was an error";

echo '<p><strong>'.$error->get_error_code() .'</strong>: '.$error->get_error_message() .'</p>';


} else {

echo $GLOBALS[$this->namespace.'-save_data-result'];


}

$content .= ob_get_contents();

ob_end_clean();

return $content;


}



public function add_organiser_role(){

if (!get_role('organiser')){
        
	add_role('organiser', 'Organiser', array(
            'read' => false, // True allows that capability, False specifically removes it.
        ));

}

if (get_role('organiser')){

$organiser = get_role('organiser');

$contributor = get_role('contributor');

$contributor_caps = array_keys($contributor->capabilities);

foreach ($contributor_caps as $contributor_cap){

$organiser->add_cap( $contributor_cap ); 

}

$organiser->add_cap('upload_files');

//add new caps to organiser
$new_caps = $this->return_new_lh_team_capabilities();

foreach ($new_caps as $new_cap){

$organiser->add_cap( $new_cap ); 

}

$administrator = get_role('administrator');

//add new caps to administrator

foreach ($new_caps as $new_cap){

$administrator->add_cap( $new_cap ); 

}

}


}


public function autologin_via_url(){

if (isset($_GET[$this->namespace.'-action']) and ($_GET[$this->namespace.'-action'] == "login") and ($user = get_user_by('ID', $_GET[$this->namespace.'-uid']))){

do_action("p2p_init");

$postdata = get_post(url_to_postid(strtok($this->curpageurl(), '?')));

$token = $_GET[$this->namespace.'-token'];


if ($this->validate_token($user,$postdata,$token)){


//give the user a confirmed state
$this->action_signing( $user, $postdata, '_confirmed_team_member');


//log the user in
wp_set_auth_cookie( $user->ID );
do_action( 'wp_login', $user->user_login);
setcookie($this->namespace.'-team_joined', $postdata->ID, time()+3600);  /* expire in 1 hour */

} else {


$error = __( "something went wrong", $this->namespace );
setcookie($this->namespace.'-team_joined', json_encode($error), time()+3600);  /* expire in 1 hour */

}

wp_redirect(strtok($this->curpageurl(), '?'));
exit;


} elseif (isset($_COOKIE[$this->namespace.'-team_joined'])){

if (is_numeric($_COOKIE[$this->namespace.'-team_joined'])){

$GLOBALS[$this->namespace.'-team_joined'] = $_COOKIE[$this->namespace.'-team_joined'];

} else {


$GLOBALS[$this->namespace.'-team_joined'] = json_decode(trim(stripslashes($_COOKIE[$this->namespace.'-team_joined'])));


}

setcookie($this->namespace.'-team_joined', '', time() - 3600); //set the cookie in the past to destro it


}

}


public function run_initial_processes(){

//flush the rewrite rules
$this->flush_rewrites_activate;



//Add the various LH roles

if (!class_exists('LH_User_roles_class')) {

require_once('includes/lh-user-roles-class.php');

}

LH_User_roles_class::add_all_roles(); 


//Add the organiser role
$this->add_organiser_role();


//add the options to the database

if (!get_option($this->opt_name)){

$option[$this->unconfirmed_message_field_name] = 'Thankyou for registering %first_name% %last_name% with %team_title%.

However %first_name% %last_name% is not yet a member.

A confirmation email has been sent to  %user_email% with an activation link. Once the activation link has been visited %first_name% %last_name% will be a confirmed member of %team_title%.';

$option[$this->email_title_field_name] = 'Team registration for %team_title%';

$option[$this->email_message_field_name] = 'Gday %first_name% %last_name%,

you have been registered with %team_title%.

To confirm your membership, please click on the below link.';

$option[$this->confirmed_message_field_name] = 'Gday %first_name% %last_name%,

Thankyou for joining';

add_option($this->opt_name, $option);


}



}

public function on_activate($network) {

    if ( is_multisite() && $network_wide ) { 

        global $wpdb;

        foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
            switch_to_blog($blog_id);
wp_schedule_single_event(time(), 'lh_teams_initial_run');
            restore_current_blog();
        } 

    } else {

wp_schedule_single_event(time(), 'lh_teams_initial_run');

}

}


public function views_edit_lh_team( $views ) {
    $views['publish'] = str_replace('Published', 'Active', $views['publish']);
    return $views;
}


public function add_translator() {


if (!class_exists('LH_Posttype_Retrans')) {


require_once('includes/lh-posttype-retrans-class.php');


}

// Sample code
// Replace 'Publish' with 'Save' and 'Preview' with 'Lurk' on pages and posts
$lh_posttype_retrans_instance = new LH_Posttype_Retrans(
 array (
'replacements' => array ( 
'Publish' => 'Activate'
 ,   'Published' => 'Active' 
 )
 ,   'post_type'    => array ( 'lh-team' )
 )
);


}

public function add_participant_status() {


if (class_exists('LH_locked_post_status_plugin')) {

$lh_locked_participant_post_status = new LH_locked_post_status_plugin('participant_lock','participants only','Participant lock <span class="count">(%s)</span>','read_participant_posts');


}


}

public function add_email_queue_class() {


if (!class_exists('LH_Email_queue_class')) {


require_once('includes/lh-email-queue-class.php');


}

}

public function add_posttype_to_queue($posttypes){

if(!in_array($this->posttype, $posttypes)){


$posttypes[] = $this->posttype;


}


return $posttypes;



}


public function login_redirect( $url, $request, $user ){
 
 if( in_array( 'administrator', $user->roles ) ) {

return $url;


 }  elseif ( in_array( 'organiser', $user->roles ) ) {


$teams = $this->return_organisers_teams('publish', $user);

if (!isset($teams[1]->ID) and isset($teams[0]->ID)){

return get_permalink($teams[0]->ID);

} elseif (isset($this->options[$this->onboarding_page_field_name]) and get_post($this->options[$this->onboarding_page_field_name])){

return get_permalink($this->options[$this->onboarding_page_field_name]);

} else {

return $url;

}

} else {





if ($this->get_connection_to_by_fromid($user->ID,array('lh_teams_unconfirmed_team_member','lh_teams_confirmed_team_member'))){

return get_permalink($this->get_connection_to_by_fromid($user->ID,array('lh_teams_unconfirmed_team_member','lh_teams_confirmed_team_member')));


} else {

return $url;

}

}



}

// Filter wp_nav_menu() to add additional links and other output
public function new_nav_menu_items($items) {

    $items .= $this->menu_content($post);
    // add the home link to the end of the menu
    //$items = $items . $homelink;
    return $items;

}

public function place_menus(){

global $post;

if (($post->post_type == 'lh-team') and is_singular()){

$the_menu = get_term_by( 'id', $this->options[$this->add_to_menu_field_name], 'nav_menu' );

if ($the_menu->slug){

add_filter( 'wp_nav_menu_'.$the_menu->slug.'_items', array($this,"new_nav_menu_items") );

} 

}


}


public function admin_init() {




}

public function add_stylesheet() {

wp_enqueue_style( 'lh-teams-posttype-styles', plugins_url( '/styles/lh-teams.css', __FILE__ ), false, '1.01a', 'all' );

}


public function enqueue_if_requred() { 

global $wp_query; 

if (is_singular()) { 

$post = $wp_query->get_queried_object(); 

if ($post->post_type == $this->posttype ){ 

add_action('wp_print_styles', array($this,"add_stylesheet"),1000);



} 

} 

}



public function __construct() {

add_action('admin_init', array($this,"admin_init"));

add_action('admin_menu', array($this,"plugin_menu"));


add_action('template_redirect', array($this,"enqueue_if_requred"));


$this->options = get_option($this->opt_name);
$this->filename = plugin_basename( __FILE__ );

//Register custom post type 
add_action('init', array($this,"setup_post_types"));
add_action( 'init', array($this,"create_taxonomies"));
add_action( 'init', array($this,"register_shortcodes"));
add_action('init', array($this,"autologin_via_url"));
add_action( 'wp', array($this,"save_data"));
add_action( 'wp', array($this,"place_menus"));
add_action( 'wp', array($this,"move_team_member"));
add_filter( 'the_content', array($this,"the_content_filter"),100);

add_action( 'p2p_init', array($this,"register_p2p_connection_types"));


//Hooks to change the status names
add_filter( 'views_edit-lh-team', array($this,"views_edit_lh_team"), 10, 1);
add_action( 'plugins_loaded', array($this,"add_translator"));

//Hooks to change the status names
add_action( 'plugins_loaded', array($this,"add_participant_status"));

//hook to add the email queue class
add_action( 'plugins_loaded', array($this,"add_email_queue_class"));

//add lh-team as a queuable post tpe
add_filter( 'lh_email_queue_posttypes_filter', array($this,"add_posttype_to_queue"), 10, 1);

if (isset($this->options[$this->redirect_login_to_team_field_name]) and ($this->options[$this->redirect_login_to_team_field_name] == 1)){

add_filter('login_redirect', array($this,"login_redirect"), 100000, 3 );

}

//Hook to attach processes to initial cron job
add_action('lh_teams_initial_run', array($this,"run_initial_processes"));


}

}




$lh_teams_instance = new LH_Teams_plugin();
register_activation_hook(__FILE__, array($lh_teams_instance, 'on_activate') , 10, 1);


function lh_teams_deactivate() {
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'lh_teams_deactivate' );

add_action( 'init', function() { add_post_type_support( 'lh-team', 'front-end-editor' );  } );




?>