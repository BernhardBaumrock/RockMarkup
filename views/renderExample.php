<?php namespace ProcessWire;
/** @var InputfieldForm $form */
/** @var RockMarkupSandbox $sandbox */
$form = $this->modules->get('InputfieldForm');
$form->name = 'renderExample';
$form->method = 'GET';

// get path from url parameter
$dir = $this->input->get('dir', 'int');
$path = $sandbox->getExampleDirs($dir);
$info = (object)pathinfo($name);

// add hidden name field
$form->add([
  'type' => 'hidden',
  'name' => 'name',
  'value' => $info->filename,
]);

// add hidden dir field
$form->add([
  'type' => 'hidden',
  'name' => 'dir',
  'value' => $dir,
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
  'type' => str_replace('Sandbox', '', $sandbox->className),
  'name' => $info->filename,
  'path' => $path,
  'label' => 'Result',
  'collapsed' => $isAjax ? Inputfield::collapsedYesAjax : Inputfield::collapsedNo,
]);

// add code
$form->add([
  'type' => 'markup',
  'name' => 'code_'.$info->filename,
  'label' => '',
  'icon' => 'code',
  'value' => $sandbox->renderCode($info->filename),
]);
?>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.8/highlight.min.js"></script>
<?= $form->render(); ?>
<script>hljs.initHighlightingOnLoad();</script>
