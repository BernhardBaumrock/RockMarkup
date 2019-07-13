<?php namespace ProcessWire;
/**
 * Inputfield for RockMarkup Fieldtype
 *
 * @author Bernhard Baumrock, 11.02.2019
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class InputfieldRockMarkup extends InputfieldMarkup {

  public static function getModuleInfo() {
    return [
      'title' => 'RockMarkup Inputfield', 
      'summary' => 'Inputfield to display any markup in the PW backend.',
      'version' => '0.0.1',
      'author' => 'Bernhard Baumrock',
      'icon' => 'code',
      'requires' => ['RockMarkup'],
    ];
  }

  /**
   * Reference to RockMarkup Module
   */
  private $rm;

  /**
   * Init this module
   *
   * @return void
   */
  public function init() {
    parent::init();
    $this->rm = $this->modules->get('RockMarkup');

    // add the RockMarkup class to this field
    // this class is also added from derived fields (like RockTabulator)
    // and makes sure that all events are fired properly
    // must load before RockTabulator.js
    $this->addClass('RockMarkup');
    $this->config->scripts->add($this->rm->toUrl(__DIR__.'/RockMarkup.js'));
  }
  
  /**
   * Render file in assets folder
   *
   * @return void
   */
  public function ___render() {
    $content = $this->getContent();
    $script = $this->getScriptTag();
    return '<div class="RockMarkupOutput">' . $content.$script . '</div>';
  }
  
  /**
   * Called on renderReady
   * 
   * MUST NOT be hookable!
   */
  public function renderReady(Inputfield $parent = null, $renderValueMode = false) {
    $this->setLabel();

    $file = $this->rm->getFile($this->name);
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
      'field' => $this,
      'rm' => $this->rm,
    ]);
    
    return parent::renderReady($parent, $renderValueMode);
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
    $file = $this->rm->getFile($name);
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
          'that' => $this, // can be used to attach hooks
          'page' => $page,
          'pages' => $this->pages,
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
}
