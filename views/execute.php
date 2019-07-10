<p>
  Here you see all files inside folders that are listed in the 
  <a href='<?= $this->config->urls->admin ?>module/edit?name=<?= $sandbox->className ?>'>module's config</a>.

  <?php
  if($sandbox->className == 'ProcessRockTabulator') {
    echo 'See also <a href="../rockmarkup/">the RockMarkup Sandbox</a>.';
  }
  ?>
</p>

<ul uk-accordion>
  <?php
  $rm = $this->modules->get('InputfieldRockMarkup');

  foreach($sandbox->getExampleDirs() as $i=>$dir): ?>
    <li class="uk-open">
      <a class="uk-accordion-title" href="#"><?= $dir ?></a>
      <div class="uk-accordion-content">
        <ul>
          <?php
          $path = $rm->toPath($dir);
          foreach($this->files->find($path, [
            'extensions' => ['php'],
          ]) as $file) {
            $info = (object)pathinfo($file);
            $name = $info->filename;
            echo "<li><a href='./?name=$name&dir=$i'>$name</a></li>";
          }
          ?>
        </ul>
      </div>
    </li>
    <?php
  endforeach;
  ?>
</ul>
