'use strict';
/**
 * RockSandbox.js logs all events in the console when you are in the sandbox.
 */
$(document).on('loaded.RM', function(event, name) {
  if(name !== 'e04_events') return;
  console.log("field " + name + " was loaded!");
});
