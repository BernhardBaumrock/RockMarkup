/**
 * JS files are loaded just like CSS files. You can define custom rules within
 * your PHP files via $config->js(key, value) method. See the example below.
 */

// sample JS alert
// this alert is executed immediately when the file is loaded
alert('JS fired - check the console of your browser!');
console.log('hello world');

// wait for the document to be ready (so that VEX is loaded)
$(document).ready(function() {
  if(ProcessWire.config.noTracy) {
    ProcessWire.alert("Why don't you install TracyDebugger?");
  }
  else {
    ProcessWire.alert("Tracy rulez the (ProcessWire) world :)");
  }
  
  // log the ProcessWire config object to the console
  console.log(ProcessWire.config);
});
