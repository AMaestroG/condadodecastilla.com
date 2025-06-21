<?php
require_once __DIR__ . '/../includes/head_common.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../includes/head_common.php'; ?>
    <title>Árbol Genealógico - Personajes</title>
    <script src="https://cdn.jsdelivr.net/npm/d3@7"></script>
    <style>
        #graph svg { width: 100%; height: 600px; }
        #graph text { fill: var(--epic-purple-emperor, #4A0D67); font-size: 0.8rem; }
        #graph circle { fill: var(--epic-purple-emperor, #4A0D67); stroke: var(--epic-old-gold, #CFB53B); stroke-width: 2px; }
        #graph line { stroke: var(--epic-old-gold, #CFB53B); stroke-width: 2px; }
    </style>
</head>
<body class="alabaster-bg">
<?php require_once __DIR__ . '/../fragments/header.php'; ?>
<main class="container my-5">
    <h1 class="gradient-text">Árbol Genealógico</h1>
    <div id="graph"></div>
</main>
<?php require_once __DIR__ . '/../fragments/footer.php'; ?>
<script>
fetch('/personajes/parent_child_pairs.json')
  .then(r => r.json())
  .then(data => initGraph(data));

function initGraph(data) {
  const nodesMap = {};
  const links = data.map(d => {
    if (!nodesMap[d.parent]) nodesMap[d.parent] = {id: d.parent, link: d.parent_link};
    if (!nodesMap[d.child]) nodesMap[d.child] = {id: d.child, link: d.child_link};
    return {source: d.parent, target: d.child};
  });
  const nodes = Object.values(nodesMap);
  const width = document.getElementById('graph').clientWidth || 800;
  const height = 600;
  const svg = d3.select('#graph').append('svg');

  const simulation = d3.forceSimulation(nodes)
      .force('link', d3.forceLink(links).id(d => d.id).distance(150))
      .force('charge', d3.forceManyBody().strength(-300))
      .force('center', d3.forceCenter(width / 2, height / 2));

  const link = svg.append('g')
      .selectAll('line')
      .data(links)
      .enter().append('line');

  const node = svg.append('g')
      .selectAll('circle')
      .data(nodes)
      .enter().append('circle')
      .attr('r', 8)
      .call(d3.drag()
          .on('start', dragstarted)
          .on('drag', dragged)
          .on('end', dragended))
      .on('click', (event,d) => { if(d.link) window.location.href = d.link; });

  const label = svg.append('g')
      .selectAll('text')
      .data(nodes)
      .enter().append('text')
      .attr('dx', 10)
      .attr('dy', 4)
      .text(d => d.id);

  simulation.on('tick', () => {
    link.attr('x1', d => d.source.x)
        .attr('y1', d => d.source.y)
        .attr('x2', d => d.target.x)
        .attr('y2', d => d.target.y);
    node.attr('cx', d => d.x).attr('cy', d => d.y);
    label.attr('x', d => d.x).attr('y', d => d.y);
  });

  function dragstarted(event, d) {
    if (!event.active) simulation.alphaTarget(0.3).restart();
    d.fx = d.x; d.fy = d.y;
  }
  function dragged(event, d) {
    d.fx = event.x; d.fy = event.y;
  }
  function dragended(event, d) {
    if (!event.active) simulation.alphaTarget(0);
    d.fx = null; d.fy = null;
  }
}
</script>
</body>
</html>

