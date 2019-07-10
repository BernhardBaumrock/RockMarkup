<?php namespace ProcessWire;
/** @var InputfieldForm $form */
/** @var RockMarkup $rm */
$form = $this->modules->get('InputfieldForm');
$form->name = 'example';
$form->method = 'GET';

// get file
$file = $rm->getFile($name);
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
  "<div class='uk-child-width-1-2' uk-grid>"
    ."<div class='uk-text-left'>"
      ."<a href='./'><i class='fa fa-arrow-left' aria-hidden='true'></i> Zurück</a>"
    ."</div>"
    ."<div class='uk-text-right'>"
      .$ajax->render()
    ."</div>"
  ."</div>";
$f->addClass('uk-margin-remove');
$form->add($f);

// add rendered grid
$form->add([
  'type' => 'RockMarkup', // todo: str_replace('Process', '', $sandbox->className),
  'name' => $name,
  'label' => 'Result',
  'collapsed' => $isAjax ? Inputfield::collapsedYesAjax : Inputfield::collapsedNo,
]);

// add code
$form->add([
  'type' => 'markup',
  'name' => "code_$name",
  'label' => '',
  'icon' => 'code',
  'value' => $this->files->render(__DIR__ . '/code', [
    'file' => $file,
  ]),
]);
?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/highlight.min.js"></script>
<?= $form->render(); ?>
<script>hljs.initHighlightingOnLoad();</script>
