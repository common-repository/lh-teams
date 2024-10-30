<?php



class LH_Email_queue_class {


var $namespace = 'lh_email_queue';
var $posttypes = array('post','page');



private function check_email_connection_exists($post_id){

global $wpdb;

$sql = "SELECT p2p_id FROM ".$wpdb->prefix."p2p WHERE p2p_to = '" .$post_id. "' and p2p_type in ('".$this->namespace."-emails_queued','".$this->namespace."-emails_sent') LIMIT 1";


$p2p_id = $wpdb->get_var($sql);

if (is_numeric($p2p_id)){

return $p2p_id;

} else {

return false;

}

}

private function move_type_to_sent($p2p_id){

global $wpdb;

$sql = "UPDATE ".$wpdb->prefix."p2p SET p2p_type = '".$this->namespace."-emails_sent' where p2p_id = '".$p2p_id."' LIMIT 1";

return $wpdb->query($sql);

}

private function move_type_to_queued($p2p_id){






}


private function echo_users_list($users){

echo "<ul>";

foreach ( $users as $user ) { 

echo '<li><a href="'.get_edit_user_link( $user->ID ).'">'.get_the_author_meta( 'display_name', $user->ID ).'</a></li>';

}

echo "</ul>";




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


private function get_connection_from_by_id($id){

global $wpdb;

$sql = "SELECT p2p_from FROM ".$wpdb->prefix."p2p WHERE p2p_id = '" .$id. "'";

$from = $wpdb->get_var($sql);

return $from;

}

private function get_connection_to_by_id($id){

global $wpdb;

$sql = "SELECT p2p_to FROM ".$wpdb->prefix."p2p WHERE p2p_id = '" .$id. "'";

$from = $wpdb->get_var($sql);

return $from;

}


public function register_p2p_connection_types() {




$posttypes = apply_filters('lh_email_queue_posttypes_filter', $this->posttypes);

//Register the state for the email queue

  p2p_register_connection_type( array(
	'title' => 'Emails Queued',
        'name' => $this->namespace.'-emails_queued',
        'from' => 'user',
     'to' => $posttypes,
'admin_box' => array(
    'show' => false
  ),
'duplicate_connections' => true
) );

  p2p_register_connection_type( array(
	'title' => 'Emails Sent',
        'name' => $this->namespace.'-emails_sent',
        'from' => 'user',
     'to' => $posttypes,
'admin_box' => array(
    'show' => false
  ),
'duplicate_connections' => true
    ) );



}

public function run_email_queue(){

do_action("p2p_init");

if ($id = $this->get_connection_id_by_type($this->namespace.'-emails_queued')){

$from = $this->get_connection_from_by_id($id);

$to = $this->get_connection_to_by_id($id);

$user = get_user_by('id', $from);

echo $user->user_email;

echo $id;

$title = p2p_get_meta( $id, 'title', true );
$message = p2p_get_meta( $id, 'message', true );
$headers = p2p_get_meta( $id, 'headers', true );
echo $title;
echo $message;
print_r($headers);

if ($this->move_type_to_sent($id)){

//$foo = p2p_type( $this->namespace.'-emails_queued' )->disconnect( $from, $to );

if (wp_mail( $user->user_email, $title, $message, $headers)){

//$this->log_email($from, $to, $title, $message, $headers);



}

}

} 



}




public function add_threeminutes( $schedules ) {
	// add a 'weekly' schedule to the existing set
	$schedules['threeminutes'] = array(
		'interval' => 180,
		'display' => __('Once every 3 minutes')
	);
	return $schedules;
}



/** Add public query vars
	*	@param array $vars List of current public query vars
	*	@return array $vars 
	*/
	public function add_query_vars($vars){
		$vars[] = '__lh_email_queue_endpoint';
		return $vars;
	}


	/**	Sniff Requests
	*	This is where we hijack all API requests
	* 	If $_GET['__lh_infinite_rewards'] is set, we kill WP and serve up qr awesomeness
	*	@return die if API request
	*/
public function sniff_requests(){
		global $wp;
  
if(isset($wp->query_vars['__lh_email_queue_endpoint'])){

$this->run_email_queue();

header("Content-type: text/javascript");

?>

if (document.getElementById("foobar")){


}

<?php

exit;


} 

}

/** Add API Endpoint
	*	This is where the magic happens - brush up on your regex skillz
	*	@return void
	*/

public function add_endpoint(){

add_rewrite_rule('lh_email_queue_endpoint/?','index.php?__lh_email_queue_endpoint=1','top');


}


public function run_initial_processes(){

flush_rewrite_rules();
wp_clear_scheduled_hook( 'lh_email_queue_initial_run' ); 

}


public function on_activate($network) {

    if ( is_multisite() && $network_wide ) { 

        global $wpdb;

        foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
            switch_to_blog($blog_id);
wp_schedule_single_event(time(), 'lh_email_queue_initial_run');
            restore_current_blog();
        } 

    } else {

wp_schedule_single_event(time(), 'lh_email_queue_initial_run');

}

}


public function queue_email($user, $post, $title, $message, $headers){

do_action("p2p_init");


p2p_type( $this->namespace.'-emails_queued' )->connect( $user->ID, $post->ID, array( 'date' => current_time('mysql'), 'title'=> $title, 'message' => $message, 'headers' => $headers) ); 

wp_clear_scheduled_hook( 'lh_email_queue_process' ); 
wp_schedule_event( time(), 'threeminutes', 'lh_email_queue_process' );


}

public function log_email($userid, $postid, $title, $message, $headers){

do_action("p2p_init");


p2p_type( $this->namespace.'-emails_sent' )->connect( $userid, $postid, array( 'date' => current_time('mysql'), 'title'=> $title, 'message' => $message, 'headers' => $headers) ); 


}


public function emails_queued_metabox_content(){

global $post;

$users = get_users( array(
  'connected_type' => array('signing_sign_unconfirmed'),
  'connected_items' => $post->ID
) );



if ($users){

$this->echo_users_list($users);

}



}


public function emails_sent_metabox_content(){

global $post;

$users = get_users( array(
  'connected_type' => array($this->namespace.'-emails_sent'),
  'connected_items' => $post->ID
) );



if ($users){

$this->echo_users_list($users);

}



}


public function add_meta_boxes($post_type, $post) {

$posttypes = apply_filters('lh_email_queue_posttypes_filter', $this->posttypes);

if (in_array($post_type , $posttypes ) and ($this->check_email_connection_exists($post->ID))){

add_meta_box($this->namespace."-emails_queued-div", "Queued Emails", array($this,"emails_queued_metabox_content"), $post_type, "normal", "high");
add_meta_box($this->namespace."-emails_sent-div", "Emails Sent", array($this,"emails_sent_metabox_content"), $post_type, "normal", "high");

}

}





public function admin_init() {

add_action('add_meta_boxes', array($this,"add_meta_boxes"),10,2);

}



public function __construct() {

//Hook to add anything that needs to go in wp-admin
add_action('admin_init', array($this,"admin_init"));

//Register the email queue connection types
add_action( 'p2p_init', array($this,"register_p2p_connection_types"));

//Hook to attach processes to ongoing cron job
add_action('lh_email_queue_process', array($this,"run_email_queue"));
add_filter( 'cron_schedules', array($this,"add_threeminutes"), 10, 1);

//create an endpoint
add_filter('query_vars', array($this, 'add_query_vars'));
add_action('parse_request', array($this, 'sniff_requests'));
add_action('init', array($this, 'add_endpoint'));


//Hook to attach processes to initial cron job
add_action('lh_email_queue_initial_run', array($this,"run_initial_processes"));



}

}

$lh_email_queue_instance = new LH_Email_queue_class();
register_activation_hook(__FILE__, array($lh_email_queue_instance, 'on_activate') , 10, 1);


?>