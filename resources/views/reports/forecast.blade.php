<x-header :title="$title" />

<main>
<div class="main-forecast">
    <ion-icon name="close-circle-outline" class="close-icon" onclick="closeOverlay()"></ion-icon>
    <div class="slider-container">
        <p>Period</p>
        <input type="range" id="vertical-slider" min="3" max="15" value="3">
        <p id="slider-value">3</p>
    </div>
    <div class="chart-container">
        <canvas id="forecast"></canvas>
    </div>
</div>
<div class="container text-center">
    
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="{{asset('js/forecast.js')}}" async defer></script>
<x-footer />