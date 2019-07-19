# RockMarkup

This is a ProcessWire module that helps you injecting any custom PHP/JS/CSS code into the PW admin as Inputfields. It comes with a ProcessModule (sandbox) to easily create, test and edit your fields. This sandbox module will only work on Uikit based admin themes.

## Installation

All you need to do is to install the main module. All necessary sub-modules will be installed automatically.

## Support, Feedback

If you need any assistance or want to provide feedback, please head over to the [PW Support Forum thread](https://processwire.com/talk/topic/21982-rockmarkup).

## Usage

After installation head over to the newly created page under /setup and create new files or see the examples:

![img](https://i.imgur.com/jul009u.png)

It is very easy to create new fields/files via the admin interface. Just click the links and see the examples of how to use the module.

### Show a field in the page editor

You can add your markup to any page editor by creating a new PW field of type `RockMarkup` and adding it to your page's template.

There is a helper on the details page of each example:

![img](https://i.imgur.com/0t4CuIN.png)

### Show a field in a ProcessModule

It is also very easy to use this Inputfield in your ProcessModules:

```php
public function ___execute() {
  /** @var InputfieldForm $form */
  $form = $this->modules->get('InputfieldForm');

  $form->add([
    'name' => 'yourfieldname',
    'type' => 'RockMarkup',
  ]);

  return $form->render();
}
```

All the configuration and logic will be done in the field's related files. You can see `/setup/rockmarkup/process-example` in your PW installation as an example.

## Adding dirs via hook

You can create custom modules based on RockMarkup. To add your module's directory to RockMarkup you need to modify the `getDirs()` return array in your module's `init()` method, otherwise the files will never be scanned and executed:

```php
$this->addHookAfter("RockMarkup::getDirs", function(HookEvent $event) {
  $dirs = $event->return;
  $dirs[] = '/site/modules/MyModule/RockMarkup/';
  $event->return = $dirs;
});
```

## Misc

### Hide the label of a field

By default the Inputfield will render the field's name if no label is set. You can hide the label via the `hideLabel` property:

```php
$form->add([
  'name' => 'mymarkup',
  'type' => 'RockMarkup',
  'hideLabel' => true,
]);
```
