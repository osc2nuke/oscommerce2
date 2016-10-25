<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2015 osCommerce

  Released under the GNU General Public License
*/

use OSC\OM\OSCOM;

define('NAVBAR_TITLE', 'Security Check');
define('HEADING_TITLE', 'Security Check');

define('TEXT_INFORMATION', 'We have detected that your browser has generated a different SSL Session ID used throughout our secure pages.<br /><br />For security measures you will need to log into your account again to continue shopping online.<br /><br />Some older browsers do not have the capability of generating a secure SSL Session ID automatically which we require. If you use such a browser, we recommend switching to another browser such as <a href="https://www.microsoft.com/download/internet-explorer.aspx" target="_blank">Microsoft Internet Explorer</a>, <a href="https://www.google.com/chrome/browser/desktop/" target="_blank">Google Chrome</a>, or <a href="https://www.mozilla.org/firefox/new/" target="_blank">Mozilla Firefox</a> to continue your online shopping experience.<br /><br />We have taken this measurement of security for your benefit, and apologize upfront if any inconveniences are caused.<br /><br />Please <a href="' . OSCOM::link('contact_us.php', '', 'SSL') . '">contact us</a> if you have any questions relating to this requirement, or to continue purchasing products offline.');

define('BOX_INFORMATION_HEADING', 'Privacy and Security');
define('BOX_INFORMATION', 'We validate the SSL Session ID automatically generated by your browser on every secure page request made to this server.<br /><br />This validation assures that it is you who is navigating on this site with your account and not somebody else.');
?>
