<?php namespace ProcessWire;
class RockMarkupFile extends WireData {

  /**
   * constructor
   */
  public function __construct($file) {
    if(!is_file($file)) throw new WireException("File $file not found!");
    $info = pathinfo($file);
    $rm = $this->modules->get('RockMarkup');
    
    $name = $info['filename'];
    $f = $rm->getFile($name);
    if($f) {
      $dir = $f->dir;
      throw new WireException($info['dirname'] . ": $name is already defined in $dir");
    }

    $this->name = $name;
    $this->path = $file;
    $this->url = $rm->toUrl($file);
    $this->dir = $rm->toPath($info['dirname']);

    // populate all files
    $this->addFiles($rm);
  }

  /**
   * Add all related files to the object
   */
  public function addFiles($rm) {
    $files = [];

    foreach($rm->extensions as $ext) {
      foreach($this->wire->files->find($this->dir, [
        'recursive' => 0,
        'extensions' => [$ext],
      ]) as $file) {
        $info = pathinfo($file);
        if($info['filename'] != $this->name) continue;
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
      if($extension == $info->extension) {
        $info->file = $file;
        return $info;
      }
    }
  }

  /**
   * Render Code Inputfield for the Sandbox Process Module
   */
  public function renderCode() {
    $link = 'vscode://file/%file:%line';
    $tracy = $this->modules->get('TracyDebugger');
    if($tracy AND $tracy->editor) $link = $tracy->editor;
    
    $out = '';
    foreach($this->files as $file) {
      $info = (object)pathinfo($file);

      $lang = $info->extension;
      if($lang == 'hooks') $lang = 'php';
      if($lang == 'md') $lang = '';

      $dir = $info->dirname;
      $base = $info->basename;
      $ext = $info->extension;

      // setup editor link
      $url = str_replace("%file", "$dir/$base", $link);
      $url = str_replace("%line", "1", $url);
      $code = $this->sanitizer->entities(file_get_contents("$dir/$base"));
      $code = "<pre class='uk-margin-small'><code class='$lang'>$code</code></pre>";

      // markdown?
      if($ext == 'md') {
        require_once(__DIR__.'/lib/Parsedown.php');
        $Parsedown = new \Parsedown();
        $code = $Parsedown->text($this->wire->files->render("$dir/$base"));
      }
      
      // add line to table
      $out .= "<tr>"
        ."<td class='uk-text-nowrap'>"
          .'<i class="fa fa-file-code-o uk-margin-small-right" aria-hidden="true"></i>'
          ."<a href='$url'>$base</a>"
        ."</td>"
        ."<td>$code</td>"
        ."</tr>";
    }
    return $out;
  }
}