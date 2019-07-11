<?php
/**
 * PHP files are executed when the field is RENDERED. On Inputfields with
 * collapsed state Inputfield::collapsedYesAjax this code is only executed
 * when the field is opened. That's why there is a separate file for hooks
 * that is always executed in the admin on every pageload.
 */
for($i = 0; $i<5; $i++) {
  echo "<div>Line $i</div>";
}
