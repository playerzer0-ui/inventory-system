var period = 3; // Default period value
const overlay = document.querySelector(".main-forecast");
const slider = document.getElementById("vertical-slider");
const sliderValue = document.getElementById("slider-value");

// Initialize the chart data and chart instance variables
let chartData = null;
let chartInstance = null;

let allProducts = []; // To store all product data
let filteredProducts = []; // To store filtered products
const searchInput = document.getElementById('productSearch');
const searchButton = document.getElementById('searchButton');
const clearSearch = document.getElementById('clearSearch');

$(document).ready(function () {
    $.ajax({
        type: "get",
        url: "/getAllProductCodes",
        dataType: "json",
        success: function (response) {
            allProducts = response; // Store all products
            filteredProducts = response; // Initially show all products
            renderProductCharts(response);
        }
    });
    
    searchButton.addEventListener('click', filterProducts);
    searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            filterProducts();
        }
    });
    clearSearch.addEventListener('click', clearSearchResults);
});

// New function to filter products
function filterProducts() {
    const searchTerm = searchInput.value.toLowerCase();
    
    if (searchTerm === '') {
        filteredProducts = [...allProducts];
    } else {
        filteredProducts = allProducts.filter(product => 
            product.productCode.toLowerCase().includes(searchTerm) ||
            (product.productName && product.productName.toLowerCase().includes(searchTerm))
        );
    }
    
    // Clear existing charts
    document.getElementById('charts').innerHTML = '';
    
    // Render filtered charts
    renderProductCharts(filteredProducts);
}

// New function to clear search
function clearSearchResults() {
    searchInput.value = '';
    filteredProducts = [...allProducts];
    document.getElementById('charts').innerHTML = '';
    renderProductCharts(allProducts);
}

// New function to render charts (extracted from your success callback)
function renderProductCharts(products) {
    for (let i = 0; i < products.length; i++) {
        getProductData(products[i].productCode, products[i].productName);
    }
}

function openOverlay() {
    overlay.classList.add("visible");
}

function closeOverlay() {
    console.log("close");
    overlay.classList.remove("visible");
}

// Update the slider value display and dynamically update the EMA dataset
slider.addEventListener("input", () => {
    period = slider.value; // Update the period with the slider value
    sliderValue.textContent = period; // Update the displayed value
    if (chartData && chartInstance) {
        updateEMA(chartData); // Dynamically update the EMA dataset
    }
});

// Timeframe buttons functionality
document.querySelectorAll('.timeframe-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.timeframe-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        // Add active class to clicked button
        this.classList.add('active');
        
        if (chartData && chartInstance) {
            const timeframe = this.dataset.timeframe;
            filterDataByTimeframe(timeframe);
        }
    });
});

function filterDataByTimeframe(timeframe) {
    if (!chartData || chartData.length === 0) return;

    let filteredData = [...chartData]; // Start with a copy of all data
    
    if (timeframe !== 'all') {
        const now = new Date();
        filteredData = chartData.filter(item => {
            const itemDate = new Date(item.orderDate);
            const diffTime = now - itemDate;
            const diffDays = diffTime / (1000 * 60 * 60 * 24);
            
            switch(timeframe) {
                case 'week':
                    return diffDays <= 7;
                case 'month':
                    return diffDays <= 30;
                case '3months':
                    return diffDays <= 90;
                case 'year':
                    return diffDays <= 365;
                default:
                    return true;
            }
        });
    }
    
    updateChartWithData(filteredData);
}

function updateChartWithData(data) {
    let labels = [];
    let normalData = [];
    let ema = [];
    let alpha = 2 / (parseInt(period) + 1);

    for (let i = 0; i < data.length; i++) {
        let qty = parseInt(data[i].total_qty);
        labels.push(data[i].orderDate);
        normalData.push(qty);
        if (i < 1) {
            ema.push(qty);
        } else {
            let result = (alpha * qty) + ((1 - alpha) * ema[i - 1]);
            ema.push(result);
        }
    }

    chartInstance.data.labels = labels;
    chartInstance.data.datasets[0].data = normalData;
    chartInstance.data.datasets[1].data = ema;
    chartInstance.update();
}

// Set 'All' as default active button
document.querySelector('.timeframe-btn[data-timeframe="all"]').classList.add('active');

function getProductData(productCode, productName) {
    const container = document.getElementById("charts");
    const rows = container.querySelectorAll('.row');
    const lastRow = rows[rows.length - 1];
    let row;

    if (!lastRow || lastRow.children.length >= 3) {
        row = document.createElement('div');
        row.classList.add('row', 'align-items-start', 'add-margin-top-15');
        container.appendChild(row);
    } else {
        row = lastRow;
    }

    const col = document.createElement('div');
    col.classList.add('col');

    const card = document.createElement('div');
    card.classList.add('card', 'card-forecast');
    card.style.width = '18rem';

    const cardBody = document.createElement('div');
    cardBody.classList.add('card-body');

    const cardTitle = document.createElement('h5');
    cardTitle.classList.add('card-title');
    cardTitle.textContent = `${productCode}: ${productName}`;

    const canvas = document.createElement('canvas');
    canvas.id = productCode;

    cardBody.appendChild(cardTitle);
    cardBody.appendChild(canvas);
    card.appendChild(cardBody);
    col.appendChild(card);
    row.appendChild(col);

    $.ajax({
        type: "get",
        url: "/getProductData",
        data: {
            "productCode": productCode
        },
        dataType: "json",
        success: function (response) {
            createMiniChart(response, productCode);

            card.addEventListener('click', () => {
                resetCanvas();
                openOverlay();
                chartData = response; // Store the chart data
                createChart(response); // Render the chart with the current period
            });
        }
    });
}

function resetCanvas() {
    const canvas = document.getElementById('forecast');
    const parent = canvas.parentNode;
    parent.removeChild(canvas);
    const newCanvas = document.createElement('canvas');
    newCanvas.id = 'forecast';
    parent.appendChild(newCanvas);
}

function createChart(data) {
    chartData = data; // Store the original data
    const ctx = document.getElementById("forecast");
    
    // Filter data based on default timeframe ('All')
    const filteredData = filterDataForTimeframe(data, 'all');
    
    let labels = [];
    let normalData = [];
    let ema = [];
    let alpha = 2 / (parseInt(period) + 1);

    for (let i = 0; i < filteredData.length; i++) {
        let qty = parseInt(filteredData[i].total_qty);
        labels.push(filteredData[i].orderDate);
        normalData.push(qty);
        if (i < 1) {
            ema.push(qty);
        } else {
            let result = (alpha * qty) + ((1 - alpha) * ema[i - 1]);
            ema.push(result);
        }
    }

    // Create new chart instance
    chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Normal Data',
                    data: normalData,
                    borderWidth: 1,
                    borderColor: 'blue',
                },
                {
                    label: 'EMA Data',
                    data: ema,
                    borderWidth: 1,
                    borderColor: 'red',
                },
            ],
        },
        options: {
            animation: {
                duration: 500,
                easing: 'easeInOutQuad',
            },
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
            layout: {
                padding: {
                    top: 40, // Added more top padding for buttons
                    right: 10,
                    bottom: 10,
                    left: 10,
                },
            },
        },
    });
}

// Helper function for initial data filtering
function filterDataForTimeframe(data, timeframe) {
    if (timeframe === 'all') return data;
    
    const now = new Date();
    return data.filter(item => {
        const itemDate = new Date(item.orderDate);
        const diffTime = now - itemDate;
        const diffDays = diffTime / (1000 * 60 * 60 * 24);
        
        switch(timeframe) {
            case 'week':
                return diffDays <= 7;
            case 'month':
                return diffDays <= 30;
            case '3months':
                return diffDays <= 90;
            case 'year':
                return diffDays <= 365;
            default:
                return true;
        }
    });
}

// Function to dynamically update the EMA dataset
function updateEMA(data) {
    let labels = [];
    let normalData = [];
    let ema = [];

    if (data.length <= 0) {
        alert("No data present for this product");
    } else if (period === null || period === "") {
        alert("Period must not be empty, it helps with the smoothing");
    } else {
        let alpha = 2 / (parseInt(period) + 1);

        for (let i = 0; i < data.length; i++) {
            let qty = parseInt(data[i].total_qty);
            labels.push(data[i].orderDate);
            normalData.push(qty);
            if (i < 1) {
                ema.push(qty);
            } else {
                let result = (alpha * qty) + ((1 - alpha) * ema[i - 1]);
                ema.push(result);
            }
        }

        // Update the EMA dataset in the existing chart
        chartInstance.data.datasets[1].data = ema; // Update EMA data
        chartInstance.update(); // Re-render the chart
    }
}

function createMiniChart(data, productCode) {
    const ctx = document.getElementById(productCode);
    let normalData = [];

    if (data.length <= 0) {
        alert("No data present for this product");
    } else {
        for (let i = 0; i < data.length; i++) {
            let qty = parseInt(data[i].total_qty);
            normalData.push(qty);
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: Array(normalData.length).fill(''),
                datasets: [
                    {
                        label: '',
                        data: normalData,
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}