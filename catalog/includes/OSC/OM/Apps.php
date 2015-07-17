<?php
/**
  * osCommerce Online Merchant
  *
  * @copyright Copyright (c) 2015 osCommerce; http://www.oscommerce.com
  * @license GPL; http://www.oscommerce.com/gpllicense.txt
  */

namespace OSC\OM;

use OSC\OM\OSCOM;
use OSC\OM\Registry;

class Apps
{
    public static function getModules($type, $app = null, $filter = null)
    {
        $result = [];

        if (!Registry::exists('ModuleType' . $type)) {
            $class = 'OSC\OM\Modules\\' . $type;

            if (!class_exists($class)) {
                trigger_error('OSC\OM\Apps::getModules(): ' . $type . ' module class not found in OSC\OM\Modules\\');

                return $result;
            }

            Registry::set('ModuleType' . $type, new $class());
        }

        $OSCOM_Type = Registry::get('ModuleType' . $type);

        $directory = OSCOM::BASE_DIR . 'OSC/Apps';

        if (file_exists($directory)) {
            if ($dir = new \DirectoryIterator($directory)) {
                foreach ($dir as $file) {
                    if (!$file->isDot() && $file->isDir() && (!isset($app) || ($file->getFilename() == $app)) && static::exists($file->getFilename()) && (($json = static::getInfo($file->getFilename())) !== false)) {
                        if (isset($json['modules'][$type])) {
                            $modules = $json['modules'][$type];

                            if (isset($filter)) {
                                $modules = $OSCOM_Type->filter($modules, $filter);
                            }

                            foreach ($modules as $key => $data) {
                                $result = array_merge($result, $OSCOM_Type->getInfo($file->getFilename(), $key, $data));
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    public static function exists($app)
    {
        $app = basename($app);

        if (class_exists('OSC\Apps\\' . $app . '\\' . $app)) {
            if (is_subclass_of('OSC\Apps\\' . $app . '\\' . $app, 'OSC\OM\AppAbstract')) {
                return true;
            } else {
                trigger_error('OSC\OM\Apps::exists(): ' . $app . ' - App is not a subclass of OSC\OM\AppAbstract and cannot be loaded.');
            }
        } else {
            trigger_error('OSC\OM\Apps::exists(): ' . $app . ' - App class does not exist.');
        }

        return false;
    }

    public static function getModuleClass($module, $type)
    {
        if (!Registry::exists('ModuleType' . $type)) {
            $class = 'OSC\OM\Modules\\' . $type;

            if (!class_exists($class)) {
                trigger_error('OSC\OM\Apps::getModuleClass(): ' . $type . ' module class not found in OSC\OM\Modules\\');

                return $result;
            }

            Registry::set('ModuleType' . $type, new $class());
        }

        $OSCOM_Type = Registry::get('ModuleType' . $type);

        return $OSCOM_Type->getClass($module);
    }

    public static function getInfo($app)
    {
        $app = basename($app);

        if (!file_exists(OSCOM::BASE_DIR . 'OSC/Apps/' . $app . '/oscommerce.json') || (($json = @json_decode(file_get_contents(OSCOM::BASE_DIR . 'OSC/Apps/' . $app . '/oscommerce.json'), true)) === null)) {
            trigger_error('OSC\OM\Apps::getInfo(): ' . $app . ' - Could not read App information in ' . OSCOM::BASE_DIR . 'OSC/Apps/' . $app . '/oscommerce.json.');

            return false;
        }

        return $json;
    }

    public static function getRouteDestination($route = null, $app = null)
    {
        if (empty($route)) {
            $route = array_keys($_GET);
        }

        $result = $routes = [];

        if (empty($route)) {
            return $result;
        }

        $directory = OSCOM::BASE_DIR . 'OSC/Apps';

        if (file_exists($directory)) {
            if ($dir = new \DirectoryIterator($directory)) {
                foreach ($dir as $file) {
                    if (!$file->isDot() && $file->isDir() && (!isset($app) || ($file->getFilename() == $app)) && static::exists($file->getFilename()) && (($json = static::getInfo($file->getFilename())) !== false)) {
                        if (isset($json['routes'][OSCOM::getSite()])) {
                            $routes[$json['vendor']] = $json['routes'][OSCOM::getSite()];
                        }
                    }
                }
            }
        }

        return call_user_func([
            'OSC\Sites\\' . OSCOM::getSite() . '\\' . OSCOM::getSite(),
            'resolveRoute'
        ], $route, $routes);
    }
}
