/**
 * This JS file is only loaded when the ProcessHello module is run
 *
 * You should delete it if you have no javascript to add.
 *
 */
console.log('RockSandbox.js');

// log all events (before dom is ready)
var logRockSandboxEvent = function(event, num) {
  console.log('Event was fired:', event);
}
$(document).on('RockMarkup', function(event) { logRockSandboxEvent(event); });
$(document).on('loaded', function(event) { logRockSandboxEvent(event); });
$(document).on('size', function(event) { logRockSandboxEvent(event); });


$(document).ready(function() {
  // submit form on AJAX setting change
  $('#Inputfield_ajax').change(function() {
    $(this).closest('form').submit();
  });

  // copy text on click
  $(document).on('click', '.copy', function() {
    // get value
    var val = $(this).closest('a').find('span').text();

    // copy to clipboard
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val(val).select();
    document.execCommand("copy");
    $temp.remove();

    // show notification
    UIkit.notification({
      message: 'Copied to clipboard',
      timeout: 2000
    });

    return false;
  });
}); 
