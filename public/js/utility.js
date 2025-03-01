function setInputValue(value) {
    if(pageState == "moving"){
        let input = document.getElementById('no_moving');
        input.value = value;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }
    else{
        let input = document.getElementById('no_sj');
        input.value = value;
        input.dispatchEvent(new Event('input', { bubbles: true }));
    }
}