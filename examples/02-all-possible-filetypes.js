/**
 * JS files are loaded just like CSS files. You can define custom rules within
 * your PHP files via $config->js(key, value) method. See the example below.
 */

alert('JS fired - check the console of your browser!');
console.log('hello world');

if(ProcessWire.config.noTracy) {
  alert("Why don't you install TracyDebugger?");
}
else {
  alert("Tracy rulez the (ProcessWire) world :)");
}

// log the ProcessWire config object to the console
console.log(ProcessWire.config);
