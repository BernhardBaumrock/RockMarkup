<?php namespace ProcessWire;
/**
 * ProcessRockMarkup Module
 *
 * @author Bernhard Baumrock, 18.06.2019
 * @license Licensed under MIT
 */
class ProcessRockMarkup extends Process {

  /**
   * Reference to RockMarkup module
   * 
   * @var RockMarkup
   */
  private $rm;

  /**
   * Init. Optional.
   */
  public function init() {
    parent::init(); // always remember to call the parent init

    // set reference to RockMarkup module
    $rm = $this->modules->get('RockMarkup');
    $this->rm = $rm;
    
    // add sandbox js and css
    $this->config->scripts->add($rm->toUrl(__DIR__ . '/RockSandbox.js'));
    $this->config->styles->add($rm->toUrl(__DIR__ . '/RockSandbox.css'));
  }

  /**
   * Main execute method
   */
  public function execute() {
    $name = $this->input->get('name', 'text');
    $this->headline($name);
    $this->browserTitle("Sandbox: $name");

    // single example view
    if($name) {
      return $this->files->render(__DIR__ . '/views/example', [
        'rm' => $this->rm,
        'name' => $name,
      ]);
    }
    
    // list overview
    return $this->files->render(__DIR__ . '/views/execute', [
      'rm' => $this->rm,
    ]);
  }
}

