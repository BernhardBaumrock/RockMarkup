'use strict';
/**
 * Trigger RockMarkup2 Events
 */
function RockMarkup2() {
}

/**
 * Custom log function
 * 
 * This will log only if PW debug mode is ON.
 */
RockMarkup2.prototype.log = function(...str) {
  if(!ProcessWire.config.debug) return;
  console.log(...str);
}

/**
 * Get field data from dom data attribute
 */
RockMarkup2.prototype.getFieldData = function(el) {
  return $(el).find('.RockMarkup2Output').data('jsdata') || {};
}

/**
 * Debounce function for not firing too many events at once
 */
RockMarkup2.prototype.debounce = function(func, wait, immediate) {
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
 * Setup the RockMarkup2 JS object
 */
var RockMarkup2 = new RockMarkup2();
$(document).trigger('ready.RM');

$(window).on('resize', RockMarkup2.debounce(function() {
  $('.InputfieldRockMarkup2 .InputfieldContent:visible').trigger('size.RM');
}));
