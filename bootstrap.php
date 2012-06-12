<?php
/**
 * Opauth Multi-provider authentication framework for PHP package for FuelPHP Framework
 * 
 * @package    Fuel-Opauth
 * @version    1.0
 * @author     Andreo Vieira <andreoav@gmail.com>
 * @license    MIT License
 * @copyright  2012 Andreo Vieira
 * @link       http://www.inf.ufsm.br/~andreoav
 */

Autoloader::add_core_namespace('Opauth');
  
Autoloader::add_classes(array(
    'Opauth\\Opauth'           => __DIR__ . '/classes/Opauth.php',
    'Opauth\\OpauthStrategy'   => __DIR__ . '/classes/OpauthStrategy.php',
    'Opauth\\FacebookStrategy' => __DIR__ . '/classes/Strategy/FacebookStrategy.php',
    'Opauth\\TwitterStrategy'  => __DIR__ . '/classes/Strategy/TwitterStrategy.php',
));