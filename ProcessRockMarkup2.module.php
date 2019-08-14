<?php namespace ProcessWire;
/**
 * ProcessRockMarkup2 Module
 *
 * @author Bernhard Baumrock, 18.06.2019
 * @license Licensed under MIT
 */
class ProcessRockMarkup2 extends Process {

  public static function getModuleInfo() {
    return [
      'title' => 'ProcessRockMarkup2',
      'summary' => 'RockMarkup2 Process Module (Sandbox).',
      'version' => 1,
      'author' => 'Bernhard Baumrock',
      'icon' => 'code',
      'requires' => ['RockMarkup2'],
      'page' => [
        'name' => 'rockmarkup',
        'title' => 'RockMarkup2',
        'parent' => 'setup',
      ],
    ];
  }

  /**
   * Reference to RockMarkup2 module
   * 
   * @var RockMarkup2
   */
  private $rm;
  
  /**
   * isRockMarkup2 flag
   * 
   * This flag is necessary for the uninstallation process
   */
  public $isRockMarkup2 = true;

  /**
   * Init. Optional.
   */
  public function init() {
    parent::init(); // always remember to call the parent init

    // set reference to RockMarkup2 module
    $rm = $this->modules->get('RockMarkup2');
    $this->rm = $rm;
    
    // add sandbox js and css
    $this->config->scripts->add($rm->toUrl(__DIR__ . '/RockSandbox.js'));
    $this->config->styles->add($rm->toUrl(__DIR__ . '/RockSandbox.css'));
  }

  /**
   * Main execute method
   */
  public function ___execute() {
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
    $this->headline('Example of a RockMarkup2 field in a ProcessModule');
  
    $form->add([
      'name' => 'e07_chartjs_github',
      'type' => 'RockMarkup2',
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
    file_put_contents($new, $this->getNewFileContent($ext, [
      'name' => $name,
    ]));
    $this->session->redirect("./?name=$name");
  }

  /**
   * Get example content for new file
   */
  public function getNewFileContent($ext, $vars = []) {
    $path = $this->config->paths($this);
    $file = $path."snippets/$ext.$ext";
    if(!is_file($file)) return;
    switch($ext) {
      case 'ready':
      case 'hooks':
        $content = file_get_contents($file);
        break;
      default:
        $content = $this->wire->files->render($file, $vars);
    }
    
    return $content;
  }
}
