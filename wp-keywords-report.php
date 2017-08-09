<?php
/*
Plugin Name: Google Keywords Report
Plugin URI:
Description: Displays top keywords of your blog in Google search along with blog position in google for that keyword, url, Keyword cost, traffic, query volume and number of results in Google 
Version: 1.0
Author: Sunny Verma
Author URI: http://99webtools.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
$gkr_site=parse_url(site_url(),PHP_URL_HOST);
function gkr_dashboard_widgets()
{
add_meta_box( 'gkr_dashboard_widget', 'Top Keywords in Google SERP', 'gkr_dashboard_widget_function', 'dashboard', 'side', 'high' );
}
add_action( 'wp_dashboard_setup', 'gkr_dashboard_widgets' );
// Create the function to output the contents of our Dashboard Widget.
function gkr_dashboard_widget_function() {
if($data=get_transient( 'wpgkr' ))
{}
else
{
global $gkr_site;
$rs=wp_remote_get( 'http://widget.semrush.com/widget.php?action=report&type=organic&db=us&domain='.$gkr_site, array( 'timeout' => 10, 'httpversion' => '1.1' ));
$rs=json_decode($rs['body'],true);
$data=isset($rs['organic']['data'])?$rs['organic']['data']:array();
set_transient('wpgkr',$data,DAY_IN_SECONDS);
}
echo '<div class="gkr">';
foreach($data as $v)
echo '<span>'.$v['Ph'].'</span>';
echo '<br><a href="'.admin_url('?page=full-keywords-report').'" >View Full Report &raquo;</a></div>';
}
//Full Report Page
function gkr_full_report() {
if($data=get_transient( 'wpgkr' ))
{}
else
{
global $gkr_site;
$rs=wp_remote_get( 'http://widget.semrush.com/widget.php?action=report&type=organic&db=us&domain='.$gkr_site, array( 'timeout' => 10, 'httpversion' => '1.1' ));
$rs=json_decode($rs['body'],true);
$data=isset($rs['organic']['data'])?$rs['organic']['data']:array();
set_transient('wpgkr',$data,DAY_IN_SECONDS);
}
echo '<div class="wrap"><h2>Google Keywords Report</h2><table class="gkr"><thead><tr><th>Keyword</th><th>URL</th><th>Position</th><th>Traffic</th><th>keyword Cost</th><th>Queries/Day</th><th>Results in Google</th></tr></thead><tbody>';
foreach($data as $v)
echo '<tr><td>'.$v['Ph'].'</td><td><a target="_blank" href="'.$v['Ur'].'" >'.$v['Ur'].'</a></td><td>'.$v['Po'].'</td><td>'.$v['Tr'].'%</td><td>'.$v['Cp'].'</td><td>'.$v['Nq'].'</td><td>'.$v['Nr'].'</td></tr>';
echo '</tbody></table><div>';
}
function gkr_menu() {
	add_submenu_page(null,'Full Keywords Report', 'Full Keywords Report', 'read', 'full-keywords-report', 'gkr_full_report');
}
add_action('admin_menu', 'gkr_menu');
//Admin Css
function gkr_css() {
   echo '<style type="text/css">
			.gkr>span{display: inline-block;padding:3px;margin:1px;border:1px solid #CCC;border-radius:5px}
			.gkr>span:hover{background-color:#2278E1;color:#FFF}
           .gkr{width:100%;background-color:#FFF;border-collapse:collapse;border-spacing:0px}
		   .gkr td{padding:2px;border-top:1px solid rgb(221, 221, 221);line-height:1.42}
		   .gkr>tbody>tr:nth-child(even){background-color:rgb(245, 245, 245)}
		   .gkr th{text-align:left;line-height:1.42}
		   .gkr>thead>tr>th{border-bottom:2px solid rgb(221, 221, 221)}
         </style>';
}
add_action('admin_head', 'gkr_css');
?>