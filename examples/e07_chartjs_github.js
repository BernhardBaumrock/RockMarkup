'use strict';
$(document).on('loaded', '.RockMarkup2[data-name=e07_chartjs_github]', function(event) {
  var data = RockMarkup2.getFieldData(this);
  var el = $(this).find('canvas')[0];
  var myChart = new Chart(el, {
    type: 'bar',
    data: {
      labels: data.labels,
      datasets: [{
        label: 'Commits',
        data: data.totals,
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderColor: 'rgba(255, 99, 132, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        yAxes: [{
          scaleLabel: {
            display: true,
            labelString: 'Number of Commits',
          },
          ticks: {
            beginAtZero: true
          }
        }],
        xAxes: [{
          scaleLabel: {
            display: true,
            labelString: 'Weeks',
          }
        }],
      }
    }
  });
});
