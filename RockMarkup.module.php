<?php namespace ProcessWire;
/**
 * RockMarkup
 *
 * @author Bernhard Baumrock, 10.07.2019
 * @license Licensed under MIT
 */
require_once("RockMarkupFile.php");
class RockMarkup extends WireData implements Module, ConfigurableModule {

  /**
   * Directory with example files
   * @var string
   */
  private $exampleDir;

  /**
   * Possible extensions for RockMarkupFiles
   * @var array
   */
  public $extensions = ['md', 'php', 'hooks', 'js', 'css'];

  /**
   * Array of all RockMarkupFiles
   * @var array
   */
  private $files;

  public function __construct() {
    // populate defaults, which will get replaced with actual
    // configured values before the init/ready methods are called
    $this->setArray(self::$defaults);

    // example directory
    $this->exampleDir = $this->config->urls($this)."examples/";
  }

  /**
   * Initialize the module (optional)
   */
  public function init() {
    $this->getFiles();
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
   * @return array
   */
  public function ___getDirs() {
    $dirs = explode("\n", $this->dirs);
    $dirs[] = $this->exampleDir;
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
    foreach($this->getDirs() as $dir) {
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


  /** ########## Module Config ########## */
  // todo: path sanitizations
  static protected $defaults = array(
    'dirs' => "/site/assets/RockMarkup/",
  );
  public function getModuleConfigInputfields(array $data) {
    $inputfields = new InputfieldWrapper();
    $data = array_merge(self::$defaults, $data);

    $f = $this->modules->get('InputfieldTextarea');
    $f->name = 'dirs';
    $f->label = 'Directories to scan';
    $f->required = true;
    $f->value = $data['dirs'];
    $f->notes = $this->notes();
    $inputfields->add($f);

    return $inputfields;
  }
}
