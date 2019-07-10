/**
 * Trigger RockMarkup Events
 */
function RockMarkup() {
}

/**
 * Custom log function
 * 
 * This will log only if PW debug mode is true.
 */
RockMarkup.prototype.log = function(...str) {
  if(!ProcessWire.config.debug) return;
  console.log(...str);
}

/**
 * Debounce function for not firing too many events at once
 */
RockMarkup.prototype.debounce = function(func, wait, immediate) {
  var wait = wait || 500; // 500ms default
  var timeout;
  return function() {
    var context = this, args = arguments;
    var later = function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    var callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

/**
 * Setup the RockMarkup JS object
 */
var RockMarkup = new RockMarkup();

// show that this file was loaded
RockMarkup.log('RockMarkup.js');

$(document).ready(function() {
  /**
   * Load is triggered via <script> tag
   */
  
  /**
   * Trigger size event
   */
  $(window).on('resize', RockMarkup.debounce(function() {
    $('.InputfieldRockMarkup .InputfieldContent:visible').trigger('size');
  }));

  /**
   * Trigger RockMarkup
   */
  $(document).trigger('RockMarkup');
});
