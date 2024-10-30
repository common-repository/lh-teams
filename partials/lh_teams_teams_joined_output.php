<?php
$teams = get_posts( array(
  'connected_type' =>  $this->namespace.'_confirmed_team_member',
  'connected_items' => $userobject->ID,
  'suppress_filters' => false,
  'nopaging' => true
) );

if ($teams){



echo "<h3>Team Memberships</h3><ul>";



foreach ( $teams as $team ) { 

echo '<li><a href="'.get_permalink($team->ID).'">'.$team->post_title.'</a></li>';

}


echo "</ul>";

}

?>