<?php namespace ProcessWire;
/**
 * Fieldtype that can hold any custom markup.
 *
 * @author Bernhard Baumrock, 11.02.2019
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class FieldtypeRockMarkup extends Fieldtype {

  /**
   * module initialisation
   */
  public function init() {
  }

  /**
   * Return the associated Inputfield
   */
  public function getInputfield(Page $page, Field $field) {
    $f = $this->modules->get('InputfieldRockMarkup');
    $f->skipLabel = Inputfield::skipLabelBlank;
    return $f;
  }

  /**
   * Field configuration screen
   * 
   * @param Field $field
   * @return InputfieldWrapper
   *
   */
  public function ___getConfigInputfields(Field $field) {
    $inputfields = parent::___getConfigInputfields($field);
    $f = $this->modules->get('InputfieldRockMarkup');
    $f->name = $field->name;

    $inputfields->add([
      'type' => 'text',
      'name' => 'path',
      'label' => $this->_('Custom File Path'),
      'value' => $field->path,
      'description' => $this->_('Relative to root folder, default is') . " " . $f->toUrl($f->defaultPath),
      'notes' => $this->_('Current path') . ": " . $f->getFilePath(),
    ]);

    return $inputfields; 
  }
  
  /**
   * the formatted value of this field
   * necessary to render the grid's markup on the frontend
   */
  public function sanitizeValue(Page $page, Field $field, $value) {
    if($this->process == 'ProcessPageView') {
      $f = $this->getInputfield($page, $field);
      $f->field = $field;
      return $f->render();
    }
  }

  ###########################################################################################

  /**
   * The following functions are defined as replacements to keep this fieldtype out of the DB
   *
   */

  public function ___wakeupValue(Page $page, Field $field, $value) { return $value; }
  public function ___sleepValue(Page $page, Field $field, $value) { return $value; }
  public function getLoadQuery(Field $field, DatabaseQuerySelect $query) { return $query; }
  public function ___loadPageField(Page $page, Field $field) { return null; }
  public function ___savePageField(Page $page, Field $field) { return true; }
  public function ___deletePageField(Page $page, Field $field) { return true; }
  public function ___deleteField(Field $field) { return true; }
  public function getDatabaseSchema(Field $field) { return array(); }
  public function ___createField(Field $field) { return true; }
  public function getMatchQuery($query, $table, $subfield, $operator, $value) {
    throw new WireException("Field '{$query->field->name}' is runtime and not queryable");
  }
  public function ___getCompatibleFieldtypes(Field $field) { return new Fieldtypes(); }
  public function getLoadQueryAutojoin(Field $field, DatabaseQuerySelect $query) { return null; }
}

