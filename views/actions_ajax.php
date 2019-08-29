<?php namespace ProcessWire;
$name = $this->input->get('name', 'string');
?>

<div>
  <table class="uk-table kvpairs uk-table-small">
    <tbody>
      <tr class="kvpair">
        <td class="uk-width-1-3"><input class="uk-input key" placeholder="key"></td>
        <td class="uk-width-expand"><input class="uk-input value" placeholder="value"></td>
      </tr>
    </tbody>
  </table>
  
  <?php
  $b = $this->modules->get('InputfieldButton');
  $b->id = 'add_key_value_pair';
  $b->value = $this->_('Add Key-Value-Pair');
  $b->addClass('ui-priority-secondary');
  $b->icon = 'plus';
  echo $b->render();

  $b = $this->modules->get('InputfieldButton');
  $b->id = 'tabulator_ajax_post';
  $b->attr('data-name', $name);
  $b->value = $this->_('Send AJAX Post Request');
  $b->icon = 'paper-plane-o';
  echo $b->render();
  ?>

  <small>See browser devtools for output</small>
</div>
<script>
$('#add_key_value_pair').click(function(e) {
  $tr = $(e.target).closest('div').find('.kvpair').first();
  var $clone = $tr.clone();
  $tr.after($clone);
});

$('#tabulator_ajax_post').on('click', function() {
  var name = $(this).data('name');

  // get payload from key-value-pairs
  var payload = {};
  $('.kvpair').each(function(i, tr) {
    var key = $(tr).find('.key').val();
    if(!key) return;
    var val = $(tr).find('.value').val();
    payload[key] = val;
  });
  
  // send ajax post
  RockTabulator.post({
    name,
    payload,
    done: function(data) {
      console.log(data);
    },
    error: function(data) {
      console.error(data);
    },
  });
});
</script>
