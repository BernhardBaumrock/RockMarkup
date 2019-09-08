<?php namespace ProcessWire;
/**
 * The RockMarkup2 Process Module (Sandbox) renders an InputfieldForm named
 * "example". In this hooks file we hook before rendering this form and add
 * a custom InputfieldMarkup (PW internal field) that shows some custom HTML.
 */

/** @var WireFileTools $this */
$this->addHookBefore("InputfieldForm(name=example)::render", function(HookEvent $event) {
  $form = $event->object; /** @var InputfieldForm $form */
  $name = $this->input->get('name', 'text');
  if($name != 'e03_hooks') return;

  $f = $this->modules->get('InputfieldMarkup');
  $f->label = "Hooks Example";
  $f->value = "This field was added via hook in <strong>03-hooks.hooks</strong>";
  $form->insertBefore($f, $form->getChildByName('e03_hooks'));
});
