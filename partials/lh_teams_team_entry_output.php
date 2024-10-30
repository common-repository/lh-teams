<?php

$return_string .= '<form id="lh-teams-team_entry-form" name="lh-teams-team_entry-form" method="post" action="" data-team_entry-nonce="'.wp_create_nonce($this->namespace."-team_entry-nonce").'">';

$return_string .= '<p><!--[if lt IE 10]><label for="'.$this->namespace.'-team_name">'.$this->return_group_description().' Name</label><![endif]--><input id="'.$this->namespace.'-team_name" name="'.$this->namespace.'-team_name" type="text" placeholder="'.$this->return_group_description().' Name (required)" required="required" /></p>';

$return_string = apply_filters( 'lh_teams_team_entry_before_submit_filter', $return_string);

$return_string .= '<input type="hidden" id="'.$this->namespace.'-save_data-create_team" name="'.$this->namespace.'-save_data-create_team" value="'.wp_create_nonce( $this->namespace.'-save_data-nonce' ).'" />';

if (!empty($clone) and is_numeric($clone)){

$return_string .= '<input type="hidden" id="'.$this->namespace.'-clone-postid" name="'.$this->namespace.'-clone-postid" value="'.$clone.'" />';

}




$return_string .= '<input type="hidden" id="'.$this->namespace.'-save_data-action" name="'.$this->namespace.'-save_data-action" value="create_team" />';


$return_string .= '
<p>
<input type="submit" id="lh-teams-team_entry-form" name="lh-teams-team_entry-form-submit" value="Register '.$this->return_group_description().'" />
</p>
</form>
';



?>