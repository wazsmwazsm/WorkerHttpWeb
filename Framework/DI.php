<?php
namespace Framework;

use ReflectionClass;
/**
 * Dependency injection.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
 class DI
 {
      public static function run($class_name, $method)
      {
          /******* construct Dependency injection *******/
          $reflector = new ReflectionClass($class_name);
          $constructor = $reflector->getConstructor();
          // create di params
          $di_params = [];
          if($constructor) {
              foreach ($constructor->getParameters() as $param) {
                  $di_class = $param->getClass();
                  if ($di_class) {
                      $di_params[] = new $di_class->name();
                  }
              }
          }
          // create instance
          $instance = $reflector->newInstanceArgs($di_params);

          /******* method Dependency injection *******/
          // create di params
          $di_params = [];
          $reflectorMethod = $reflector->getMethod($method);
          foreach ($reflectorMethod->getParameters() as $param) {
              $di_class = $param->getClass();
              if ($di_class) {
                  $di_params[] = new $di_class->name();
              }
          }
          // run method
          return call_user_func_array([$instance, $method], $di_params);
      }
 }
