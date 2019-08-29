<?php namespace ProcessWire;
/** @var InputfieldForm $form */
/** @var RockMarkup2 $rm */
$form = $this->modules->get('InputfieldForm');
$form->name = 'example';
$form->method = 'GET';

// get file
$file = $main->getFile($name);
if(!$file) return;

// add hidden name field
$form->add([
  'type' => 'hidden',
  'name' => 'name',
  'value' => $name,
]);

// ajax checkbox
$isAjax = $this->input->get('ajax', 'int');
$ajax = $this->modules->get('InputfieldCheckbox');
$ajax->name = 'ajax';
$ajax->label = ' AJAX';
$ajax->attr('checked', $isAjax ? 'checked' : '');

// add rendered grid
$f = $this->modules->get('InputfieldMarkup');
$f->name = 'navbar';
$f->value =
  "<div class='uk-child-width-1-3' uk-grid>"
    ."<div class='uk-text-left'>"
      ."<a href='./'><i class='fa fa-arrow-left' aria-hidden='true'></i> Zurück</a>"
    ."</div>"
    ."<div class='uk-text-center'>$prevnext</div>"
    ."<div class='uk-text-right'>"
      .$ajax->render()
    ."</div>"
  ."</div>";
$f->addClass('uk-margin-remove');
$form->add($f);

// add rendered result
$form->add([
  'type' => $main, // RockMarkup2 or derived class
  'name' => $name,
  'label' => "Inputfield_$name",
  'collapsed' => $isAjax ? Inputfield::collapsedYesAjax : Inputfield::collapsedNo,
]);

// add code
$form->add([
  'type' => 'markup',
  'name' => $name."_code",
  'label' => 'Files',
  'icon' => 'code',
  'class' => 'scroll',
  'value' => $this->files->render(__DIR__ . '/code', [
    'file' => $file,
  ]),
]);

// add info
$form->add([
  'type' => 'markup',
  'name' => "info",
  'label' => 'Info',
  'icon' => 'info',
  'collapsed' => Inputfield::collapsedYes,
  'value' => $this->files->render(__DIR__ . '/info', [
    'file' => $file,
  ]),
]);

// add actions
$fs = $this->modules->get('InputfieldFieldset');
$fs->name = $name."_actions";
$fs->label = 'Actions';
// $fs->collapsed = Inputfield::collapsedYes;
$fs->icon = 'bolt';
$this->files->include('actions', [
  'fs' => $fs,
  'file' => $file,
]);
$form->add($fs);

$form->add([
  'type' => 'submit',
  'name' => 'submit',
]);
?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/highlight.min.js"></script>
<?= $form->render(); ?>
<script>hljs.initHighlightingOnLoad();</script>
