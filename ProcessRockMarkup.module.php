<?php namespace ProcessWire;
/**
 * ProcessRockMarkup Module
 *
 * @author Bernhard Baumrock, 18.06.2019
 * @license Licensed under MIT
 */
class ProcessRockMarkup extends Process {

  /**
   * Init. Optional.
   */
  public function init() {
    parent::init(); // always remember to call the parent init
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
      return $this->files->render(__DIR__ . '/views/renderExample', [
        'sandbox' => $this->modules->get($this->className),
        'name' => $name,
      ]);
    }
    
    // list overview
    return $this->files->render(__DIR__ . '/views/execute', [
      'path' => __DIR__."/examples",
      'sandbox' => $this->modules->get($this->className),
    ]);
  }
}

