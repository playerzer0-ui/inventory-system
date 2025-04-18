<x-header :title="$title" />

<main class="container">
    <div class="search-container mb-4">
        <div class="input-group">
            <input type="text" id="productSearch" class="form-control" placeholder="Search by product code...">
            <button class="btn btn-outline-secondary" type="button" id="searchButton">Search</button>
            <button class="btn btn-outline-secondary" type="button" id="clearSearch">Clear</button>
        </div>
    </div>
<div class="main-forecast">
    <ion-icon name="close-circle-outline" class="close-icon" onclick="closeOverlay()"></ion-icon>
    <div class="slider-container">
        <p>Period</p>
        <input type="range" id="vertical-slider" min="3" max="15" value="3">
        <p id="slider-value">3</p>
    </div>
    <div class="chart-container">
        <div class="timeframe-buttons">
            <button class="timeframe-btn" data-timeframe="week">Weekly</button>
            <button class="timeframe-btn" data-timeframe="month">Monthly</button>
            <button class="timeframe-btn" data-timeframe="3months">3 Months</button>
            <button class="timeframe-btn" data-timeframe="year">Yearly</button>
            <button class="timeframe-btn active" data-timeframe="all">All</button>
        </div>
        <canvas id="forecast"></canvas>
    </div>
</div>
<div id="charts">
    
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="{{asset('js/forecast.js')}}" async defer></script>
<x-footer />