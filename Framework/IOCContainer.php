<?php
namespace Framework;

use ReflectionClass;

/**
 * Dependency injection IOC Container.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
 class IOCContainer
 {
     /**
      * singleton instances.
      *
      * @var Array
      */
      private static $_singleton = [];

      /**
       * get Instance from reflection info.
       *
       * @param  \ReflectionClass $reflector
       * @return object
       */
      private static function getInstance(ReflectionClass $reflector)
      {
          $constructor = $reflector->getConstructor();
          // create di params
          $di_params = $constructor ? self::getDiParams($constructor->getParameters()) : [];
          // create instance
          return $reflector->newInstanceArgs($di_params);
      }

      /**
       * create Dependency injection params.
       *
       * @param  Array $params
       * @return Array
       */
      private static function getDiParams(Array $params)
      {
          $di_params = [];
          foreach ($params as $param) {
              $class = $param->getClass();
              if ($class) {
                  $singleton = self::getSingleton($class->name);
                  $di_params[] = $singleton ? $singleton : new $class->name();
              }
          }

          return $di_params;
      }

      /**
       * set a singleton instance.
       *
       * @param  object $instance
       * @return void
       * @throws \InvalidArgumentException
       */
      public static function singleton($instance)
      {
          if( ! is_object($instance)) {
              throw new \InvalidArgumentException("Object need!");
          }
          // singleton not exist, create
          if( ! array_key_exists(get_class($instance), self::$_singleton)) {
              self::$_singleton[get_class($instance)] = $instance;
          }
      }

      /**
       * get a singleton instance.
       *
       * @param  String $class_name
       * @return object
       */
      public static function getSingleton($class_name)
      {
          return array_key_exists($class_name, self::$_singleton) ?
                 self::$_singleton[$class_name] : NULL;
      }

      /**
       * run class method.
       *
       * @param  String $class_name
       * @param  String $method
       * @return mixed
       * @throws \BadMethodCallException
       */
      public static function run($class_name, $method)
      {
          // class exist ?
          if( ! class_exists($class_name)) {
              throw new \BadMethodCallException("Class $class_name is not found!");
          }
          // method exist ?
          if( ! method_exists($class_name, $method)) {
              throw new \BadMethodCallException("undefined method $method in $class_name !");
          }
          // get class reflector
          $reflector = new ReflectionClass($class_name);
          // create instance
          $instance = self::getInstance($reflector);
          /******* method Dependency injection *******/
          $reflectorMethod = $reflector->getMethod($method);
          // create di params
          $di_params = self::getDiParams($reflectorMethod->getParameters());//var_dump(self::$_singleton);
          // run method
          return call_user_func_array([$instance, $method], $di_params);
      }
 }
