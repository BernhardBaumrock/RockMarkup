<?php namespace ProcessWire;
$link = 'vscode://file/%file:%line';
$tracy = $this->modules->get('TracyDebugger');
if($tracy AND $tracy->editor) $link = $tracy->editor;
$rm = $file->rm;

$out = "<table class='uk-table uk-table-small uk-table-divider'>"
  ."<tbody>";

foreach($rm->extensions as $ext) {
  $asset = $file->getAsset($ext);
  $code = "<a href='./?name={$file->name}&create=$ext'><i class='fa fa-plus'></i> Create File</a>";
  $label = "{$file->name}.$ext";

  if($asset) {
    $info = (object)pathinfo($asset->file);

    $lang = $info->extension;
    if($lang == 'hooks') $lang = 'php';
    if($lang == 'md') $lang = '';

    $dir = $info->dirname;
    $base = $info->basename;

    // setup editor link
    $url = str_replace("%file", "$dir/$base", $link);
    $url = str_replace("%line", "1", $url);
    $code = $this->sanitizer->entities(file_get_contents("$dir/$base"));
    $code = "<pre class='uk-margin-small'><code class='$lang'>$code</code></pre>";

    // markdown?
    if($ext == 'md') {
      require_once('../lib/Parsedown.php');
      $Parsedown = new \Parsedown();
      $code = $Parsedown->text($this->wire->files->render("$dir/$base"));
    }

    $label = "<a href='$url'>$base</a>";
  }
  
  // add line to table
  $out .= "<tr>"
    ."<td class='uk-width-auto uk-text-nowrap'>"
      .'<i class="fa fa-file-code-o uk-margin-small-right" aria-hidden="true"></i>'
      .$label
    ."</td>"
    ."<td class='uk-width-expand'>$code</td>"
    ."</tr>";
}

// create field if link was clicked
$createField = $this->input->get('pwfield', 'int');
if($createField) $file->createField((string)$this->process->main());

// add link to pw field
$field = $this->fields->get($file->name);
if($field) {
  $url = $this->config->urls->admin . "setup/field/edit?id=".$field->id;
  $pwfield = "<a href='$url'>"
    ."<i class='fa fa-edit'></i> {$file->name}"
    ."</a>";
}
else {
  $pwfield = "<a href='./?name={$file->name}&pwfield=1'>"
    ."<i class='fa fa-plus'></i> Create PW Field"
    ."</a>";
}
$out .= "<tr><td>PW Field</td><td>$pwfield</td></tr>";

$out .= "</tbody></table>";
return $out;
