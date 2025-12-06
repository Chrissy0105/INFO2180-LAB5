document.addEventListener('DOMContentLoaded', () => {
    const lookupBtn = document.getElementById('lookup');
    const countryInput = document.getElementById('country');
    const resultDiv = document.getElementById('result');

    lookupBtn.addEventListener('click', () => {
        const country = encodeURIComponent(countryInput.value.trim());

        // AJAX request to world.php
        fetch(`world.php?country=${country}`)
            .then(response => response.text())
            .then(data => {
                resultDiv.innerHTML = data;
            })
            .catch(error => {
                resultDiv.innerHTML = `Error: ${error}`;
            });
    });
});

