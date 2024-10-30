<?php


class LH_User_roles_class {




static function add_all_roles() {


if (!get_role('unknown')){
        add_role('unknown', 'Unknown User', array('read' => false,));

}

  
if (!get_role('unclaimed')){
        add_role('unclaimed', 'Unclaimed User', array(
            'read' => false, // True allows that capability, False specifically removes it.
        ));

}

if (!get_role('follower')){
        add_role('follower', 'Follower', array(
            'read' => false, // True allows that capability, False specifically removes it.
        ));

}

if (!get_role('participant')){
        add_role('participant', 'Participant', array(
            'read' => false, // True allows that capability, False specifically removes it.
        ));

}

}


}


?>