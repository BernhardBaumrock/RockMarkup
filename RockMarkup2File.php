<?php namespace ProcessWire;
class RockMarkup2File extends WireData {

  /**
   * constructor
   */
  public function __construct($file, $main) {
    if(!is_file($file)) throw new WireException("File $file not found!");
    $info = pathinfo($file);

    // set reference to the main module
    // this can either be RockMarkup2 or any class that extends RockMarkup2
    $this->main = $main;
    
    $name = $info['filename'];
    $f = $this->main->getFile($name);
    if($f) {
      $dir = $f->dir;
      throw new WireException($info['dirname'] . ": $name is already defined in $dir");
    }

    // set properties
    $this->name = $name;
    $this->path = $file;
    $this->url = $this->main->toUrl($file);
    $this->dir = rtrim($info['dirname'], '/').'/';

    // populate all files
    $this->addFiles();
  }

  /**
   * Get prev file in directory
   */
  public function prev($reverse = false) {
    $files = $this->main->getFilesInPath($this->dir);
    if($reverse) $files = $files->reverse();
    $prev = false;
    foreach($files as $file) {
      if($prev AND $file->path == $this->path) return $prev;
      $prev = $file;
    }
    return false;
  }
  
  /**
   * Get next file in directory
   */
  public function next() {
    return $this->prev(true);
  }

  /**
   * Add all related files to the object
   */
  public function addFiles() {
    $files = [];

    foreach($this->main->extensions as $ext) {
      foreach($this->wire->files->find($this->dir, [
        'recursive' => 0,
        'extensions' => [$ext],
      ]) as $file) {
        $info = pathinfo($file);
        // if the filename does not start with the base file name we skip it
        if(strpos($info['filename'], $this->name) !== 0) continue;
        $files[] = $file;
      }
    }
    $this->files = $files;
  }

  /**
   * Get asset by file extension
   * @param string $extension
   * @return string
   */
  public function getAsset($extension) {
    foreach($this->files as $file) {
      $info = (object)pathinfo($file);
      $info->file = $file;
      if($extension == $info->extension) return $info;
      if($this->main->endsWith($info->filename, ".$extension")) return $info;
    }
  }

  /**
   * Rename all files
   * 
   * @param string $newname
   * @return void
   */
  public function rename($newname) {
    $abort = false;
    $newname = $this->sanitizer->fieldName($newname);
    $newname = strtolower($newname); // not necessary but better for RockFinder
    
    if(!$newname) {
      $this->error("Invalid name - must be a valid PW fieldname!");
      return;
    }

    // pre-check
    $newfile = $this->main->getFile($newname);
    if($newfile) {
      $this->error("File {$newfile->url} already exists");
      $abort = true;
    }

    $renameField = $this->input->get('renameField', 'int');
    $newfield = $this->fields->get($newname);
    if($renameField AND $newfield) {
      $this->error("Field $newfield already exists");
      $abort = true;
    }

    if($abort) return;
    

    // rename files
    foreach($this->files as $file) {
      $info = (object)pathinfo($file);
      $ext = $info->extension;
      $this->wire->files->rename($file, "$newname.$ext");
    }

    // rename inputfield?
    if($renameField) {
      $field = $this->fields->get($this->name);
      if($field) {
        $field->name = $newname;
        $field->save();
      }
    }

    $this->session->redirect("./?name=$newname");
  }

  /**
   * Delete all corresponding files
   */
  public function delete() {
    $num = 0;
    foreach($this->files as $file) {
      $this->wire->files->unlink($file);
      $num++;
    }
    $this->message("$num files were deleted.");

    $field = $this->fields->get($this->name);
    if($field) {
      $this->warning("Remove this field manually if you don't need it any more.");
      $this->session->redirect($this->config->urls->admin . "setup/fields/edit/?id=".$field->id);
    }

    $this->session->redirect("./");
  }

  /**
   * Create pw field for this file
   * @param string $type PW Fieldtype
   */
  public function createField($type) {
    $name = $this->name;

    // early exit if field exists
    $field = $this->fields->get($name);
    if(!$field) {
      $fieldname = $this->sanitizer->fieldName($name);
      if(!$fieldname) throw new WireException("Invalid Fieldname: $name");
      
      $field = $this->wire(new Field);
      $field->type = $type;
      $field->name = $fieldname;
      $field->save();
    }
    else {
      $this->warning("Field $field does already exist!");
    }

    // redirect to file sandbox
    $this->session->redirect("./?name=$name");
  }
}