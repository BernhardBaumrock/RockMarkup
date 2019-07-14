<?php namespace ProcessWire;
$http = new WireHttp();
$http->setHeader('Accept', 'application/vnd.github.v3+json');
$json = $http->getJSON("https://api.github.com/repos/processwire/processwire/stats/commit_activity");

// prepare data array
$data = ['labels' => [], 'totals' => []];
foreach(array_reverse($json) as $item) {
  $data['labels'][] = date("W", $item['week']);
  $data['totals'][] = $item['total'];
}

$inputfield->js($data);
?>
<canvas></canvas>
