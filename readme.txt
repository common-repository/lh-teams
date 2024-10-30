=== LH Teams ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-teams/
Tags: list, lists, email, email-validation, validation, team, teams, register, member, sign, join
Requires at least: 3.0
Tested up to: 4.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

LH Teams creates a cpt called a team, this is effectively a configurable validated list of users, which can be managed by the author

== Description ==
The LocalHero Project is about running membership organisations using WordPress. A vital part of any organisation and especially sporting organisations are teams.

LH Teams creates a custom post type called a team. Visitors can register for a team by adding their name and email address, they are then sent a confirmation email with an activation link, by clicking the link they confirm their membership on the team. Team members are created as users in the database.


== Installation ==

1. Upload the `lh-teams` folder to the `/wp-content/plugins/` directory
1. Install the WordPress Posts 2 Posts plugin
1. Activate both plugins through the 'Plugins' menu in WordPress
1. Go to Teams->Settings and set an Unconfirmed Message, an Email Message, and a Confirmed Message


== Frequently Asked Questions ==

= What could you use this for? =
This plugin is part of the LocalHero project for member driven WordPress organisations but as it is modular could be used for other things. Use your imagination

= How does the verification work? =

Visitors who are not logged in enter their email address and are sent an email with a verification link, which they click to verify their agreement to join the team. Users who are logged in can automatically join.

= Can this be spammed? =
Users who join teams that they are not supposed too can be removed by the author or an admin. Problem users can be blocked entirely.

= Can I customise the email templates that is used for the confirmation emails? =
Yes if you wish to customise the format of the html emails that are sent by this plugin, move the lh_teams-template.php file to your theme or child theme folder. From there you can edit its content. Styles in the head are automatically moved into the body when the email is sent.

== Changelog ==


**1.0 March 15, 2016*  
Initial release.

**1.01 April 3, 2016**  
Bug fix

**1.02 April 13, 2016**  
Full action url

**1.03 June 22, 2016**  
Changed status names

**1.04 August 22, 2016**  
Enabled Rest Api for CPT


**1.07 November 30, 2016**  
Various improvements


**1.05 October 3, 2016**  
Many Changes

**1.06 November 13, 2016**  
Many more changes

**1.08 December 13, 2016**  
Allow menu move

**1.09 January 01, 2017**  
More modular code

**1.10 January 03, 2017**  
Minor fix

**1.11 January 22, 2017**  
Further improvements

**1.12 February 04, 2017**  
Further improvements

**1.13 March 04, 2017**  
Further improvements