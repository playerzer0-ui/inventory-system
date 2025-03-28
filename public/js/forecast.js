var period = 3; // Default period value
const overlay = document.querySelector(".main-forecast");
const slider = document.getElementById("vertical-slider");
const sliderValue = document.getElementById("slider-value");

// Initialize the chart data and chart instance variables
let chartData = null;
let chartInstance = null;

$(document).ready(function () {
    $.ajax({
        type: "get",
        url: "/getAllProductCodes",
        dataType: "json",
        success: function (response) {
            for (let i = 0; i < response.length; i++) {
                getProductData(response[i].productCode, response[i].productName);
            }
        }
    });
});

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
    const ctx = document.getElementById("forecast");
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
                    duration: 500, // Animation duration in milliseconds
                    easing: 'easeInOutQuad', // Smooth easing function
                },
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
                layout: {
                    padding: {
                        top: 10,
                        right: 10,
                        bottom: 10,
                        left: 10,
                    },
                },
            },
        });
    }
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