<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2014 osCommerce

  Released under the GNU General Public License
*/

  use OSC\OM\OSCOM;
  use OSC\OM\Registry;

  class securityCheckExtended_admin_backup_file {
    var $type = 'error';
    var $has_doc = true;

    protected $lang;

    function __construct() {
      $this->lang = Registry::get('Language');

      $this->lang->loadDefinitions('modules/security_check/extended/admin_backup_file');

      $this->title = MODULE_SECURITY_CHECK_EXTENDED_ADMIN_BACKUP_FILE_TITLE;
    }

    function pass() {
      $backup_directory = OSCOM::getConfig('dir_root') . 'includes/backups/';

      $backup_file = null;

      if ( is_dir($backup_directory) ) {
        $dir = dir($backup_directory);
        $contents = array();
        while ($file = $dir->read()) {
          if ( !is_dir($backup_directory . $file) ) {
            $ext = substr($file, strrpos($file, '.') + 1);

            if ( in_array($ext, array('zip', 'sql', 'gz')) && !isset($contents[$ext]) ) {
              $contents[$ext] = $file;

              if ( $ext != 'sql' ) { // zip and gz (binaries) are prioritized over sql (plain text)
                break;
              }
            }
          }
        }

        if ( isset($contents['zip']) ) {
          $backup_file = $contents['zip'];
        } elseif ( isset($contents['gz']) ) {
          $backup_file = $contents['gz'];
        } elseif ( isset($contents['sql']) ) {
          $backup_file = $contents['sql'];
        }
      }

      $result = true;

      if ( isset($backup_file) ) {
        $request = $this->getHttpRequest(OSCOM::link('includes/backups/' . $backup_file));

        $result = ($request['http_code'] != 200);
      }

      return $result;
    }

    function getMessage() {
      return MODULE_SECURITY_CHECK_EXTENDED_ADMIN_BACKUP_FILE_HTTP_200;
    }

    function getHttpRequest($url) {

      $server = parse_url($url);

      if (isset($server['port']) === false) {
        $server['port'] = ($server['scheme'] == 'https') ? 443 : 80;
      }

      if (isset($server['path']) === false) {
        $server['path'] = '/';
      }

      $curl = curl_init($server['scheme'] . '://' . $server['host'] . $server['path'] . (isset($server['query']) ? '?' . $server['query'] : ''));
      curl_setopt($curl, CURLOPT_PORT, $server['port']);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
      curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
      curl_setopt($curl, CURLOPT_NOBODY, true);

      if ( isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) ) {
        curl_setopt($curl, CURLOPT_USERPWD, $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);

        $this->type = 'warning';
      }

      $result = curl_exec($curl);

      $info = curl_getinfo($curl);

      curl_close($curl);

      return $info;
    }
  }
?>
