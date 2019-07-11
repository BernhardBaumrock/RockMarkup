<?php namespace ProcessWire;
/**
 * RockMarkup Info
 *
 * @author Bernhard Baumrock, 10.07.2019
 * @license Licensed under MIT
 */
$info = array(
  'title' => 'RockMarkup',
  'version' => '0.0.1',
  'summary' => 'RockMarkup Main Module',
  'singular' => true,
  'autoload' => 'template=admin',
  'icon' => 'bolt',
  'installs' => [
    'FieldtypeRockMarkup',
    'InputfieldRockMarkup',
    'ProcessRockMarkup',
  ],
);
