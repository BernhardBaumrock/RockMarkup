<?php namespace ProcessWire;
/**
 * Inputfield for RockMarkup2 Fieldtype
 *
 * @author Bernhard Baumrock, 11.02.2019
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class InputfieldRockMarkup2 extends InputfieldMarkup {

  public static function getModuleInfo() {
    return [
      'title' => 'RockMarkup2 Inputfield', 
      'summary' => 'Inputfield to display any markup in the PW backend.',
      'version' => '0.0.1',
      'author' => 'Bernhard Baumrock',
      'icon' => 'code',
      'requires' => ['RockMarkup2'],
    ];
  }

  /**
   * Reference to RockMarkup2 Module
   */
  protected $rm;

  /**
   * Variable holding all JS data
   * @var WireArray
   */
  private $jsData;
  
  /**
   * isRockMarkup2 flag
   * 
   * This flag is necessary for the uninstallation process
   */
  public $isRockMarkup2 = true;

  /**
   * Init this module
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->rm = $this->modules->get('RockMarkup2');

    // store a reference to the main module of this inputfield
    // eg InputfieldRockTabulator->main = RockTabulator
    $this->main = $this->main();

    // set js config var
    $this->jsData = $this->wire(new WireArray);

    // add the RockMarkup2 class to this field
    // this class is also added from derived fields (like RockTabulator)
    // and makes sure that all events are fired properly
    // must load before RockTabulator.js
    $this->addClass('RockMarkup2');
    $this->config->scripts->add($this->rm->toUrl(__DIR__.'/RockMarkup2.js'));
  }
  
  /**
   * Render file in assets folder
   *
   * @return void
   */
  public function ___render() {
    $content = $this->getContent();
    $script = $this->getScriptTag();
    $jsData = $this->getJsData();
    return "<div class='RockMarkup2Output' $jsData>" . $content.$script . "</div>";
  }
  
  /**
   * Called on renderReady
   * 
   * MUST NOT be hookable!
   */
  public function renderReady(Inputfield $parent = null, $renderValueMode = false) {
    $this->setLabel();

    $file = $this->main()->getFile($this->name);
    if(!$file) return;

    // add field name to inputfield
    $this->wrapAttr('data-name', $this->name);

    // add css + js
    $css = $file->getAsset('css');
    $js = $file->getAsset('js');
    if($css) $this->config->styles->add($this->rm->toUrl($css->file).'?t='.filemtime($css->file));
    if($js) $this->config->scripts->add($this->rm->toUrl($js->file).'?t='.filemtime($js->file));

    // load ready file
    $ready = $file->getAsset('ready');
    if($ready) $this->files->include($ready->file, [
      'inputfield' => $this,
      'rm' => $this->rm,
    ], [
      'allowedPaths' => [$ready->dirname],
    ]);

    // load global config
    // this is done for each field so that each field can modify settings
    $this->main()->loadGlobalConfig();
    
    return parent::renderReady($parent, $renderValueMode);
  }

  /**
   * Return main module
   */
  public function main() {
    return $this->modules->get(str_replace('Inputfield', '', (string)$this));
  }

  /**
   * Return jsData string
   * @return string
   */
  public function getJsData() {
    $json = json_encode($this->jsData->getArray());
    return "data-jsData='$json'";
  }

  /**
   * Set JS config variable
   * 
   * @param string|array $key
   * @param mixed $value
   */
  public function js($key, $value = null) {
    // if an array was provided we set all single items
    if(is_array($key)) {
      foreach($key as $k=>$v) $this->js($k, $v);
      return;
    }

    // set key value pair
    $this->jsData->set($key, $value);
  }

  /**
   * Set Label of Field
   */
  public function setLabel() {
    if(!$this->label) {
      // no label was set
      // if label is not NULL we set the field name as label
      if(!$this->hideLabel) $this->label = $this->name;
    }
  }

  /**
   * Set the field content from the file with the same name
   */
  public function ___getContent() {
    $out = '';

    // get file
    $name = $this->name;
    $file = $this->main()->getFile($name);
    if(!$file) return "No file found for field $name";
    
    // if a value was set return it
    if($this->value) $out = $this->value;
    else {
      // otherwise try to render the file
      try {
        // get page object
        $page = $this->page;
        if($this->process == 'ProcessPageEdit') {
          $page = $this->process->getPage();
        }

        // get markup
        $out = $this->files->render($file->path, [
          'inputfield' => $this,
          'rm' => $this->rm,
        ], [
          'allowedPaths' => [$file->path],
        ]);
      } catch (\Throwable $th) {
        $out = $th->getMessage();
      }
    }

    return $out;
  }

  /**
   * Wrap script tags around the output.
   *
   * @param string $out
   * @return void
   */
  public function ___getScriptTag() {
    // if javascript events are disabled we return the original markup
    // not implemented yet
    if($this->noEvents) return;

    // javascript events are ON
    // show spinner and fire loaded event
    return "<script>$('#Inputfield_{$this->name}').trigger('loaded');</script>";
  }

  /**
   * Inputfield config fields
   */
  public function ___getConfigInputfields() {
    // Get the defaults and $inputfields wrapper we can add to
    $inputfields = parent::___getConfigInputfields();
    $url = $this->config->urls->admin ."setup/" . $this->main() . "/?name=".$this->name;
    
    // list all related files
    $f = $this->wire('modules')->get('InputfieldMarkup');
    $f->label = 'Sandbox';
    $f->value = "<p><a href='$url'>$url</a></p>";
    $inputfields->add($f);

    return $inputfields;
  }
}
