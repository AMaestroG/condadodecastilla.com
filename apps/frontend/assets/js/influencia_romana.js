(function(){
    if (typeof d3 === 'undefined') return;
    const data = [
        {year: 0, value: 10, note: 'Comienzo de la era'},
        {year: 200, value: 40, note: 'Avance de la romanización'},
        {year: 400, value: 55, note: 'Consolidación cristiana'},
        {year: 800, value: 30, note: 'Etapa de inestabilidad'},
        {year: 1200, value: 65, note: 'Esplendor medieval'},
        {year: 1500, value: 60, note: 'Renacimiento'},
        {year: 1800, value: 50, note: 'Edad Moderna'},
        {year: 2025, value: 90, note: 'Legado presente'}
    ];

    const margin = {top:20, right:20, bottom:30, left:50};
    const width = 600 - margin.left - margin.right;
    const height = 400 - margin.top - margin.bottom;

    const svg = d3.select('#roman-chart')
        .append('svg')
        .attr('viewBox', `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
        .attr('preserveAspectRatio', 'xMidYMid meet');

    const defs = svg.append('defs');
    const gradient = defs.append('linearGradient')
        .attr('id', 'romanGradient')
        .attr('x1', '0%').attr('x2', '100%')
        .attr('y1', '0%').attr('y2', '0%');
    gradient.append('stop').attr('offset', '0%').attr('stop-color', 'var(--epic-purple-emperor)');
    gradient.append('stop').attr('offset', '100%').attr('stop-color', 'var(--epic-gold-main)');

    const glow = defs.append('filter').attr('id', 'glow');
    glow.append('feGaussianBlur').attr('stdDeviation', 4).attr('result', 'coloredBlur');
    const feMerge = glow.append('feMerge');
    feMerge.append('feMergeNode').attr('in', 'coloredBlur');
    feMerge.append('feMergeNode').attr('in', 'SourceGraphic');

    const g = svg.append('g').attr('transform', `translate(${margin.left},${margin.top})`);
    const x = d3.scaleLinear().domain([0, 2025]).range([0, width]);
    const y = d3.scaleLinear().domain([0, 100]).range([height, 0]);

    g.append('g').attr('class','axis').attr('transform',`translate(0,${height})`).call(d3.axisBottom(x).ticks(10).tickFormat(d3.format('d')));
    g.append('g').attr('class','axis').call(d3.axisLeft(y));

    const line = d3.line().x(d=>x(d.year)).y(d=>y(d.value)).curve(d3.curveMonotoneX);
    g.append('path')
        .datum(data)
        .attr('fill','none')
        .attr('stroke','url(#romanGradient)')
        .attr('stroke-width',3)
        .attr('filter','url(#glow)')
        .attr('d', line);

    const tooltip = d3.select('#roman-chart')
        .append('div')
        .attr('class','tooltip');

    g.selectAll('circle')
        .data(data)
        .enter()
        .append('circle')
        .attr('cx', d=>x(d.year))
        .attr('cy', d=>y(d.value))
        .attr('r', 5)
        .attr('fill','var(--epic-gold-main)')
        .attr('filter', d => d.value >= 80 ? 'url(#glow)' : null)
        .on('mouseenter', function(event,d){
            const [xPos, yPos] = d3.pointer(event, this.closest('svg'));
            tooltip.style('left', xPos + 'px').style('top', yPos - 30 + 'px')
                   .style('opacity',1)
                   .html(`<strong>${d.year}</strong>: ${d.note}`);
        })
        .on('mouseleave', function(){ tooltip.style('opacity',0); });
})();
