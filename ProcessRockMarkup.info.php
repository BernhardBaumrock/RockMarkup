<?php namespace ProcessWire;
/**
 * ProcessRockMarkup Info
 *
 * @author Bernhard Baumrock, 18.06.2019
 * @license Licensed under MIT
 */
$info = [
  'title' => 'ProcessRockMarkup',
  'summary' => 'RockMarkup Process Module (Sandbox).',
  'version' => 1,
  'author' => 'Bernhard Baumrock',
  'icon' => 'code',
  'requires' => ['RockMarkup'],
  'page' => [
    'name' => 'rockmarkup',
    'title' => 'RockMarkup',
    'parent' => 'setup',
  ],
];
