<p>List of all RockMarkup files:</p>

<ul uk-accordion>
  <?php foreach($rm->getDirs() as $dir): ?>
    <li>
      <a class="uk-accordion-title" href="#"><?= $dir ?></a>
      <div class="uk-accordion-content uk-margin-left">
        <?php
        foreach($rm->getFilesInDir($dir) as $file) {
          echo "<a href='./?name={$file->name}'>{$file->name}</a><br>";
        }
        ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
