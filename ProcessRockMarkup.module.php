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
      // create file?
      $this->createFile();

      // render example
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

  /**
   * Create file?
   */
  public function createFile() {
    $name = $this->input->get('name', 'string');
    if(!$name) return;

    $ext = $this->input->get('create', 'string');
    if(!$ext) return;

    $file = $this->rm->getFile($name);
    if(!$file) return;

    $asset = $file->getAsset($ext);
    if($asset) throw new WireException("File $ext already exists!");
    
    // create file and redirect
    $new = "{$file->dir}$name.$ext"; 
    file_put_contents($new, "");
    $this->session->redirect("./?name=$name");
  }
}

