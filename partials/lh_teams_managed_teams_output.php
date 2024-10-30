<?php 

$return_string = '<ul class="lh_teams-managed_teams-ul">';


foreach ( $teams as $team ) {

$return_string .= '<li class="lh_teams-managed_teams-li"><a href="'.get_permalink( $team->ID ).'">'.$team->post_title.'</a></li>';

}

$return_string .= '</ul>';

?>