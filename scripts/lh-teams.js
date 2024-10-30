function lh_teams_move_member(id,action){
if (document.getElementById(id)){

var retVal = confirm("Are you sure you want to " + action + " this member?");

}

if( retVal == true ){

document.getElementById(id).submit();
	  return true;
   }

}

function lh_teams_initialise_team_management_form(){


var submit_links = document.getElementsByClassName("lh_teams-team_management-submit_link");



for (var i = submit_links.length - 1; i >= 0; i--){

submit_links[i].addEventListener('click', function(event){
 var target = event.target;
event.preventDefault();
target.parentNode.submit();
return false;
});

}


}

lh_teams_initialise_team_management_form();

function lh_teams_createcss(content) {
	// Create the <style> tag
	var style = document.createElement("style");

	// Add a media (and/or media query) here if you'd like!
        style.setAttribute("media", "screen")


	// WebKit hack :(
	style.appendChild(document.createTextNode(content));

	// Add the <style> element to the page
	document.head.appendChild(style);

	return style.sheet;
};

lh_teams_createcss("#lh_teams-functions_menu ul { margin: 0; padding: 0; list-style-type: none; } #lh_teams-functions_menu ul li { display: inline; } #lh_teams-functions_menu ul li a { padding: .2em 1em;} ");
