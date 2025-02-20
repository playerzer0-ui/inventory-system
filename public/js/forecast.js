function getProductData(){
    let productCode = document.getElementById("productCode").value;
    $.ajax({
        type: "get",
        url: "/getProductData",
        data: {
            "productCode": productCode
        },
        dataType: "json",
        success: function (response) {
            resetCanvas();
            createChart(response);
        }
    });
}

function resetCanvas() {
    const canvas = document.getElementById('forecast');
    const parent = canvas.parentNode;
    
    // Remove old canvas
    parent.removeChild(canvas);

    // Create new canvas and append it
    const newCanvas = document.createElement('canvas');
    newCanvas.id = 'forecast';
    parent.appendChild(newCanvas);
}


function createChart(data){
    const ctx = document.getElementById('forecast');
    const period = document.getElementById('period').value;
    let labels = [];
    let normalData = [];
    let ema = [];
    
    if(data.length <= 0){
        alert("no data present for this product");
    }
    else if(period === null || period === ""){
        alert("period must not be empty, it helps with the smoothing");
    }
    else{
        let alpha = 2 / (parseInt(period) + 1);

        for(let i = 0; i < data.length; i++){
            let qty = parseInt(data[i].total_qty);
            labels.push(data[i].orderDate);
            normalData.push(qty);
            if(i < 1){
                ema.push(qty);
            }
            else{
                let result = (alpha * qty) + ((1 - alpha) * ema[i - 1]);
                ema.push(result);
            }
        }

        console.log(normalData);
        console.log(ema);

        new Chart(ctx, {
            type: 'line',
            data: {
            labels: labels,
            datasets: [{
                label: 'normal data',
                data: normalData,
                borderWidth: 1
            },
            {
                label: 'EMA data',
                data: ema,
                borderWidth: 1
            },]
            },
            options: {
            scales: {
                y: {
                beginAtZero: true
                }
            }
            }
        });
    }
}