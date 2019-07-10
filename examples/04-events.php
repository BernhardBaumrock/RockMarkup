<p>This example shows all the JS <strong>events that are triggered by
InputfieldRockMarkup</strong></p>
<p>
  When working with more complex JS (like grids or charts) you'll have to take
  care about WHEN special parts of your code get executed. To make development
  easier RockMarkup comes with default events that you can easily intercept and
  then fire actions based on that events. It helps you handle tedious edge-cases
  that will likely occur during development and that you might not think of when
  starting with a new project (like handling AJAX-loaded Inputfields etc).
</p>
<p class="uk-text-bold uk-text-center">
  Watch the console of your browser to see all fired events!
</p>

<table class="uk-table uk-table-small uk-table-divider uk-table-striped">
  <thead>
    <tr>
      <th class="uk-width-auto">Event</th>
      <th class="uk-width-auto">Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="uk-text-nowrap">RockMarkup</td>
      <td>Triggered when InputfieldRockMarkup.js was loaded.</td>
    </tr>
    <tr>
      <td class="uk-text-nowrap">loaded</td>
      <td>
        Triggered when the field was loaded (eg when it is AJAX loaded and
        displayed the first time).
      </td>
    </tr>
    <tr>
      <td class="uk-text-nowrap">size</td>
      <td>
        Triggered whenever the window is resized (this can also happen when an
        Inputfield is collapsed).
      </td>
    </tr>
  </tbody>
</table>
