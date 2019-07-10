# FieldtypeRockMarkup

The Inputfield of this Fieldtype will render a file with the name of the field located in a specified folder. If no folder is specified it will look for the files in /site/assets/RockMarkup

## Usage

The simplest usage is to create a field and place a corresponding PHP file in the folder `/site/assets/RockMarkup`, for example:

### Field in page edit screen

* Create field `mymarkup`
* Create file `/site/assets/RockMarkup/mymarkup.php`
* Add field to a template

### Field in a ProcessModule

```php
public function ___execute() {
  /** @var InputfieldForm $form */
  $form = $this->modules->get('InputfieldForm');

  $form->add([
    'name' => 'mymarkup',
    'type' => 'RockMarkup',
    'label' => 'My great markup field',
  ]);

  return $form->render();
}
```

This field will then render the contents of `/site/assets/RockMarkup/mymarkup.php`. It will also load corresponding JS and CSS files if available:

```
/site/assets/RockMarkup/mymarkup.php
/site/assets/RockMarkup/mymarkup.css
/site/assets/RockMarkup/mymarkup.js
```

## Setting a custom file path

You can either set the file path via a field's config screen or just set the `path` property in your code:

```php
$form->add([
  'name' => 'mymarkup',
  'type' => 'RockMarkup',
  'label' => 'RockMarkup Field With Custom Path',

  // add custom path
  'path' => 'site/templates/markupfields',
]);
```

## Setting the markup

You can either output the markup directly:

```php
// mymarkup.php
--- mymarkup.php ---
```
![img](https://i.imgur.com/MucL1Gt.png)

Or via PHP `echo`

```php
// mymarkup.php
<?php
$foo = 'bar';
echo 'Value of $foo is: <strong>' . $foo . '</strong>';
```
![img](https://i.imgur.com/u8289ag.png)

Or via PHP `return`

```php
// mymarkup.php
<?php namespace ProcessWire;
return __FILE__;
```
![img](https://i.imgur.com/4zAYCy2.png)


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
