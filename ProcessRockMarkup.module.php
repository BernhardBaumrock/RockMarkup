<?php namespace ProcessWire;
/**
 * ProcessRockMarkup Module
 *
 * @author Bernhard Baumrock, 18.06.2019
 * @license Licensed under MIT
 */
class ProcessRockMarkup extends Process {

  public static function getModuleInfo() {
    return [
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
  }

  /**
   * Reference to RockMarkup module
   * 
   * @var RockMarkup
   */
  private $rm;
  
  /**
   * isRockMarkup flag
   * 
   * This flag is necessary for the uninstallation process
   */
  public $isRockMarkup = true;

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
    $this->browserTitle($this->main() . ": $name");

    // single example view
    if($name) {
      // create file?
      $this->createFile();

      // if the field does not exist we redirect to the overview page
      if(!$this->main()->getFile($name)) {
        $this->error("No PHP file for $name found - please create it!");
        $this->session->redirect('./');
      }

      // render example
      return $this->files->render(__DIR__ . '/views/example', [
        'main' => $this->main(),
        'name' => $name,
      ]);
    }
    
    // list overview
    return $this->files->render(__DIR__ . '/views/execute', [
      'rm' => $this->rm,
    ]);
  }

  /**
   * Example ProcessModule
   */
  public function executeProcessExample() {
    /** @var InputfieldForm $form */
    $form = $this->modules->get('InputfieldForm');
    $this->headline('Example of a RockMarkup field in a ProcessModule');
  
    $form->add([
      'name' => 'e07_chartjs_github',
      'type' => 'RockMarkup',
    ]);
  
    return $form->render();
  }

  /**
   * Get Main Module from current process
   */
  public function main() {
    return $this->modules->get(str_replace('Process', '', $this->process));
  }

  /**
   * Create file?
   */
  public function createFile() {
    $name = $this->input->get('name', 'string');
    if(!$name) return;

    $ext = $this->input->get('create', 'string');
    if(!$ext) return;

    $file = $this->main()->getFile($name);
    if(!$file) return;

    $asset = $file->getAsset($ext);
    if($asset) throw new WireException("File $ext already exists!");
    
    // create file and redirect
    $new = "{$file->dir}$name.$ext"; 
    file_put_contents($new, "");
    $this->session->redirect("./?name=$name");
  }
}

