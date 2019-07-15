<?php namespace ProcessWire;
/**
 * RockMarkup
 *
 * @author Bernhard Baumrock, 10.07.2019
 * @license Licensed under MIT
 */
require_once("RockMarkupFile.php");
class RockMarkup extends WireData implements Module, ConfigurableModule {
  public static function getModuleInfo() {
    return array(
      'title' => 'RockMarkup Main Module',
      'version' => '0.0.1',
      'summary' => 'RockMarkup Main Module that installs and uninstalls all related modules.',
      'singular' => true,
      'autoload' => 'template=admin',
      'icon' => 'bolt',
      'installs' => [
        'FieldtypeRockMarkup',
        'InputfieldRockMarkup',
        'ProcessRockMarkup',
      ],
    );
  }
  static protected $defaults = array(
    'dirs' => "tmp",
  );
  public function getModuleConfigInputfields(array $data) {
    $inputfields = new InputfieldWrapper();
    $data = array_merge(self::$defaults, $data);

    $f = $this->modules->get('InputfieldTextarea');
    $f->name = 'dirs';
    $f->label = 'Directories to scan';
    $f->required = true;
    $f->value = $data['dirs'];
    $f->notes = "Path relative to site root, must begin and end with a slash!";
    $inputfields->add($f);

    return $inputfields;
  }
  
  /**
   * Directory with example files
   * @var string
   */
  private $exampleDir;

  /**
   * Possible extensions for RockMarkupFiles
   * @var array
   */
  public $extensions = ['md', 'php', 'ready', 'css', 'js', 'hooks'];

  /**
   * Array of all RockMarkupFiles
   * @var array
   */
  private $files;

  /**
   * isRockMarkup flag
   * 
   * This flag is necessary for the uninstallation process
   */
  public $isRockMarkup = true;
  public $isRockMarkupMain = true;
  
  public function __construct() {
    // populate defaults, which will get replaced with actual
    // configured values before the init/ready methods are called
    $this->setArray(self::$defaults);
  }

  /**
   * Initialize the module (optional)
   */
  public function init() {
    $this->exampleDir = $this->config->urls($this)."examples/";
    $this->getFiles();

    if($this->className == 'RockMarkup') {
      $this->addHookBefore("Modules::uninstall", $this, "customUninstall");
    }
  }

  /**
   * Module and API ready
   */
  public function ready() {
  }

  /**
   * Return all scanned directories
   * 
   * This method can be hooked so that other modules can use RockMarkup as well.
   * 
   * @param bool $addExampleDir
   * @return array
   */
  public function ___getDirs($addExampleDir = false) {
    $dirs = explode("\n", $this->dirs);
    if($addExampleDir) $dirs[] = $this->exampleDir;
    return $dirs;
  }

  /**
   * Return all files inside scanned folders
   * 
   * @return array
   */
  public function getFiles() {
    if($this->files) return $this->files;

    $arr = $this->wire(new WireArray);
    foreach($this->getDirs(true) as $dir) {
      $path = $this->toPath($dir);
      foreach($this->wire->files->find($path, [
        'extensions' => ['php'],
        'recursive' => 0,
      ]) as $file) {
        $rmf = new RockMarkupFile($file);
        
        // if a hook file was found include it now
        $hooks = $rmf->getAsset('hooks');
        if($hooks) {
          $this->wire->files->includeOnce($hooks->file, [
            'rm' => $this,
            'wire' => $this->wire,
          ]);
        }

        $arr->add($rmf);
      }
      // must be set on each iteration!
      $this->files = $arr;
    }

    return $arr;
  }

  /**
   * Find file by name
   * 
   * @param string $name
   * @return RockMarkupFile
   */
  public function getFile($name) {
    if(!$this->files) return;
    return $this->files->get($name);
  }

  /**
   * Create new RockMarkup file
   */
  public function createFile() {
    $new = $this->input->post('new', 'string');
    $dir = $this->input->post('dir', 'string');
    if(!$new) return;
    if(!$dir) return;

    // check if directory is allowed
    $dirs = $this->getDirs(true);
    if(!in_array($dir, $dirs))
      throw new WireException("$dir is not in allowed directories!");

    // check writable
    $path = $this->toPath($dir);
    if(!is_dir($path)) $this->wire->files->mkdir($path);
    if(!is_writable($path)) throw new WireException("Folder $path not writable");

    // create a new file and redirect
    file_put_contents($path.$new.".php", "// your code here");
    $this->session->redirect("./?name=$new");
  }

  /**
   * Get all files in a directory
   * 
   * @param string $dir
   * @return array
   */
  public function getFilesInDir($dir) {
    $arr = [];
    $dir = $this->toPath($dir);

    // check if directory exists
    if(!is_dir($dir)) return $arr;

    // loop all files
    foreach($this->files as $file) {
      if($file->dir == $dir) $arr[] = $file;
    }

    return $arr;
  }

  /**
   * Convert path to url relative to root
   *
   * @param string $path
   * @return string
   */
  public function toUrl($path) {
    $path = $this->config->urls->normalizeSeparators($path);
    $url = str_replace($this->config->paths->root, $this->config->urls->root, $path);
    $url = ltrim($url, "/");
    $url = rtrim($url,"/");

    // is it a file or a directory?
    $info = pathinfo($url);
    if(array_key_exists("extension", $info)) return "/$url";
    else return "/$url/";
  }

  /**
   * Convert url to path and make sure it exists
   *
   * @param string $url
   * @return string
   */
  public function toPath($url) {
    $url = $this->toUrl($url);
    return $this->config->paths->root.ltrim($url,"/");
  }

  /**
   * Custom uninstall routine
   * 
   * @param HookEvent $event
   */
  public function customUninstall($event) {
    $class = $event->arguments(0);
    $url = "./edit?name=$class";

    // is this a rockmarkup derived class?
    $module = $this->modules->get($class);
    if(!$module->isRockMarkup) return;
    
    // if it is not the main module redirect to it
    if(!$module->isRockMarkupMain) {
      $main = str_replace(['Fieldtype', 'Inputfield', 'Process'], '', $class);
      $this->error('Please uninstall the main module');
      $event->replace = true;
      $url = "./edit?name=$main";
      $this->session->redirect($url);
      return;
    }
    
    // ### main module uninstall ###
    $abort = false;

    // we remove this hook so that it does not interfere with submodule-uninstalls
    $event->removeHook(null);

    // check if any fields exist
    $fields = $this->wire->fields->find("type=Fieldtype$class")->count();
    if($fields > 0) {
      $this->error("Remove all fields of type $class before uninstall!");
      $abort = true;
    }

    // uninstall?
    if($abort) {
      // there where some errors, don't execute uninstall
      $event->replace = true; // prevents original uninstall
      $this->session->redirect($url); // prevent "module uninstalled" message
    }
  }

  /**
   * Install routine
   */
  public function ___install() {
    $name = $this->className;
    $this->modules->saveConfig($this, [
      'dirs' => "/site/assets/$name/\n/site/templates/$name/",
    ]);
  }
}
