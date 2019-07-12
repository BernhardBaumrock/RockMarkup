<?php namespace ProcessWire;

// rename files
$rename = $this->input->get('rename', 'string');
if($rename) $file->rename($rename);
$fset = $this->modules->get('InputfieldFieldset');
$fset->label = 'Rename';
$fset->icon = 'refresh';
$fset->collapsed = Inputfield::collapsedYes;
$fset->add([
  'type' => 'text',
  'name' => 'rename',
  'label' => 'New Name',
  'notes' => 'Enter the new filename (without extension)',
]);
$field = $this->fields->get($file->name);
if($field) {
  $fset->add([
    'type' => 'checkbox',
    'name' => 'renameField',
    'label' => 'Also rename the corresponding PW Field',
    'notes' => 'If this checkbox is checked a rename action will also rename the PW field',
  ]);
}
$fs->add($fset);

// delete files
$files = implode("\n", $file->files);
$delete = $this->input->get('delete', 'int');
if($delete) $file->delete();
$fset = $this->modules->get('InputfieldFieldset');
$fset->label = 'Delete';
$fset->icon = 'trash';
$fset->collapsed = Inputfield::collapsedYes;
$field = $this->fields->get($file->name);
$desc = '';
if($field) {
  $desc = 'The PW field has to be removed manually: '
  ."<a href='{$this->config->urls->admin}setup/field/edit/?id={$field->id}'>$field</a>";
}
$fset->add([
  'type' => 'checkbox',
  'name' => 'delete',
  'description' => $desc,
  'entityEncodeText' => false,
  'label' => 'Delete all files',
  'notes' => "This will delete the following files:\n$files",
]);
$fs->add($fset);
