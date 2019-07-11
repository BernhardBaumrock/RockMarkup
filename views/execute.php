<?php namespace ProcessWire;
// create file if form was submitted
$rm->createFile();
$url = "{$this->config->urls->admin}module/edit?name={$rm->className}";
?>

<p>
  List of all RockMarkup files. Add directories in the 
  <a href="<?= $url ?>">module's config</a>.
</p>

<ul uk-accordion>
  <?php foreach($rm->getDirs(true) as $dir): ?>
    <li>
      <a class="uk-accordion-title" href="#"><?= $dir ?></a>
      <div class="uk-accordion-content uk-margin-left">
        <?php
        // list files
        foreach($rm->getFilesInDir($dir) as $file) {
          echo "<a href='./?name={$file->name}'>{$file->name}</a><br>";
        }
        
        // skip example dir
        if(!in_array($dir, $rm->getDirs())) continue;

        // is folder writable?
        $input = $this->modules->get('InputfieldText');
        $input->type = 'text';
        $input->name = 'new';
        $input->label = 'Create new file';

        if(!is_dir($rm->toPath($dir)))
          $input->error("Folder $dir does not exist");
        if(!is_writable($rm->toPath($dir)))
          $input->error("Folder $dir is not writable for PHP");

        // add form
        $form = $this->modules->get('InputfieldForm');
        $form->addClass('uk-margin-top');
        $form->add([
          'type' => 'hidden',
          'name' => 'dir',
          'value' => $dir,
        ]);
        $form->add($input);
        $form->add([
          'type' => 'submit',
          'value' => 'create file',
          'icon' => 'plus',
        ]);
        echo $form->render();
        ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
