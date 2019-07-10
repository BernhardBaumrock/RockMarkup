<?php namespace ProcessWire;
/**
 * RockMarkup
 *
 * @author Bernhard Baumrock, 10.07.2019
 * @license Licensed under MIT
 */
class RockMarkup extends WireData implements Module, ConfigurableModule {

  public function __construct() {
    // populate defaults, which will get replaced with actual
    // configured values before the init/ready methods are called
    $this->setArray(self::$defaults);
  }

  /**
   * Initialize the module (optional)
   */
  public function init() {
  }

  /**
   * Module and API ready
   */
  public function ready() {
    if($this->fullname && !count($this->input->post)) {
      $msg = "Hi $this->fullname! ";
      $msg .= "Your age: $this->age. ";
      $msg .= "Favorite color: $this->color.";
      $this->message($msg);
    }
  }

  public function notes() {
    return 'notes';
  }

  /**
   * Return all scanned directories
   * 
   * @return array
   */
  public function ___getDirs() {
    return explode("\n", $this->dirs);
  }


  /** ########## Module Config ########## */
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
