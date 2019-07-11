/**
 * RockSandbox.js logs all events in the console when you are in the sandbox.
 */
var file = "04-events.js";
$(document).on('RockMarkup', '.RockMarkup', function(event) {
  console.log(file, event);
});
$(document).on('loaded', '.RockMarkup', function(event) {
  console.log(file, event);
  console.log("field id:", event.target.id);
  console.log("field name:", $(event.target).data('name'));
});
$(document).on('size', '.RockMarkup', function(event) {
  console.log(file, event);
});
