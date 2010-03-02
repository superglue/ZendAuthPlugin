<?php

/**
* 
*/
class ZendAuthPluginConfiguration
{
  protected static $zendLoaded = false;
  const UNAUTHORIZED_HEADER = 'HTTP/1.1 401 Unauthorized';
  const UNAUTHORIZED_HEADER_CODE = 401;
  
  
  public function postConfig()
  {
    self::registerZend();
  }
  
  public function preExecute()
  {
    $route = sgContext::getCurrentRoute();
    if (isset($route['is_secure']) && $route['is_secure'])
    {
      self::redirectIfNotAuth();
    }
  }
  
  public static function redirectIfNotAuth()
  {
    if (!Zend_Auth::getInstance()->hasIdentity())
    {
      header('Location: ' . sgToolkit::url(sgConfiguration::getPath('routing.ZendAuthPlugin_login.path') . '?destination=' . urlencode($route['path'])));
    }
  }
  
  public static function registerZend()
  {
    if (self::$zendLoaded)
    {
      return;
    }
    
    if (!class_exists('Zend_Loader_Autoloader'))
    {
      $path = sgConfiguration::getPath('settings.ZendAuthPlugin.zend_lib_path');
      set_include_path($path . PATH_SEPARATOR . get_include_path());
      require_once $path . '/Zend/Loader/Autoloader.php';
    }
    
    Zend_Loader_Autoloader::getInstance();
    self::$zendLoaded = true;
  }
  
  public static function install()
  {
    sgToolkit::touch(sgConfiguration::getPath('settings.ZendAuthPlugin.passwd_path'));
    
    $message = <<<END
    You must place the Zend Framework in your project. ZendAuthPlugin will 
    automatically look in project_root/lib/vendor for the Zend dir, or you 
    can specify the lib dir with the config ZendAuthPlugin => zend_lib_path. 
    This path should be the directory containing the Zend dir, without 
    including the Zend/ dir itself. Also remeber to exclude the Zend path from
    the autoloader by adding this line to your ProjectConfiguration before
    the sgAutoload::register() call is made: 
    
    sgAutoloader::setExclusions(array(realpath('path/to/Zend')));
    
    Finally, you must add users to the /data/ZendAuthPlugin.passwd file in the format:

    username:realm:md5(username:realm:password)

END;
    sgCLI::println($message, sgCLI::STYLE_COMMENT);
  }
}
