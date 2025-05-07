<div class="post-count-chart">
    <div id="chart-wrapper">
        <div id="values"></div>
        <canvas id="chart"></canvas>
        <div id="weeks"></div>
    </div>
</div>

<style>
    #chart-wrapper {
        position: relative;
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
    }
    #chart {
        border: solid;
        border-width: 0 0 2px 2px;
        border-color: #1dd2af;
        width: 100%;
        height: 200px;
    }
    #weeks {
        margin-left: 10px;
        text-align: left;
    }
    #weeks > span {
        width: 80px;
        display: inline-block;
        color: #1dd2af;
        font-size: 14px;
        position: relative;
    }
    #weeks > span:before {
        content: '';
        width: 5px;
        height: 5px;
        position: absolute;
        background-color: #1dd2af;
        border-radius: 50%;
        bottom: -10px;
        left: 5px;
    }
    #values {
        position: absolute;
        top: 0;
        left: -30px;
        margin-top: -2px;
    }
    #values > span {
        display: block;
        color: #1dd2af;
        font-size: 14px;
        height: 40px;
    }
    #values > span:after {
        content: '';
        width: 4px;
        height: 4px;
        position: absolute;
        background-color: #1dd2af;
        border-radius: 50%;
        margin-left: 5px;
    }
</style>

<script type="text/javascript">
    var chart = document.getElementById("chart").getContext("2d");
    chart.canvas.width = 600; // Feste Breite für bessere Kontrolle
    chart.canvas.height = 200;

    var cw = chart.canvas.width;
    var ch = chart.canvas.height;

    var w = [0, cw / 5, 2 * (cw / 5), 3 * (cw / 5), 4 * (cw / 5), cw];
    var h = [ch, ch - (ch / 5), ch - 2 * (ch / 5), ch - 3 * (ch / 5), ch - 4 * (ch / 5), 0];

    // Dynamische Daten aus PHP
    var weeks = <?= json_encode($chart_labels) ?>;
    var values = <?= json_encode($chart_values) ?>;

    // Wochen-Beschriftungen hinzufügen
    for (var i = 0; i < weeks.length; i++) {
        var week = document.createElement('span');
        var text = document.createTextNode(weeks[i]);
        week.appendChild(text);
        document.getElementById('weeks').appendChild(week);
    }

    // Werte-Beschriftungen hinzufügen
    var maxValue = Math.max(...values);
    for (var i = values.length - 1; i >= 0; i--) {
        var value = document.createElement('span');
        var textValue = document.createTextNode(values[i]);
        value.appendChild(textValue);
        document.getElementById('values').appendChild(value);
    }

    var ctx = document.getElementById("chart").getContext("2d");

    ctx.beginPath();

    // Aktueller Graph
    for (var i = 0; i < weeks.length; i++) {
        var valueIndex = Math.floor((values[i] / maxValue) * 4); // Skalierung auf 5 Höhenstufen
        ctx.moveTo(w[i], ch);
        ctx.strokeStyle = '#1dd2af';
        ctx.lineWidth = 2;
        ctx.lineTo(w[i], h[valueIndex]);
        ctx.stroke();
    }

    // Vertikale Gitterlinien
    function gridV() {
        for (var i = 1; i < w.length - 1; i++) {
            ctx.strokeStyle = 'rgba(29, 210, 175, 0.3)';
            ctx.lineWidth = 1;
            ctx.moveTo(w[i], 0);
            ctx.lineTo(w[i], ch);
        }
        ctx.stroke();
    }

    // Horizontale Gitterlinien
    function gridH() {
        for (var i = 1; i < h.length - 1; i++) {
            ctx.strokeStyle = 'rgba(29, 210, 175, 0.3)';
            ctx.lineWidth = 1;
            ctx.moveTo(0, h[i]);
            ctx.lineTo(cw, h[i]);
        }
        ctx.stroke();
    }

    gridV();
    gridH();
</script>