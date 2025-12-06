document.addEventListener('DOMContentLoaded', () => {
    const countryInput = document.getElementById('country');
    const resultDiv = document.getElementById('result');

    // Lookup Country
    document.getElementById('lookup').addEventListener('click', () => {
        const country = encodeURIComponent(countryInput.value.trim());
        fetchData(country, 'country');
    });

    // Lookup Cities
    document.getElementById('lookup-cities').addEventListener('click', () => {
        const country = encodeURIComponent(countryInput.value.trim());
        fetchData(country, 'cities');
    });

    // Filter table
    document.getElementById('table-search').addEventListener('input', (e) => {
        const filter = e.target.value.toLowerCase();
        const table = document.getElementById('results-table');
        if (!table) return;
        const rows = table.querySelectorAll('tr:nth-child(n+2)');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Observe table changes to make sortable
    const observer = new MutationObserver(() => {
        makeTableSortable('results-table');
    });
    observer.observe(resultDiv, { childList: true });

    function fetchData(country, lookup) {
        resultDiv.innerHTML = `<div class='loader'></div>`;
        fetch(`world.php?country=${country}&lookup=${lookup}`)
            .then(response => response.text())
            .then(data => resultDiv.innerHTML = data)
            .catch(error => resultDiv.innerHTML = `Error: ${error}`);
    }

    function makeTableSortable(tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.onclick = () => sortTableByColumn(table, index);
        });
    }

    function sortTableByColumn(table, columnIndex) {
        const rows = Array.from(table.querySelectorAll('tr:nth-child(n+2)'));
        const asc = table.getAttribute('data-sort-dir') !== 'asc';
        rows.sort((a, b) => {
            const cellA = a.children[columnIndex].innerText.replace(/,/g, '');
            const cellB = b.children[columnIndex].innerText.replace(/,/g, '');
            const numA = parseFloat(cellA);
            const numB = parseFloat(cellB);

            if (!isNaN(numA) && !isNaN(numB)) return asc ? numA - numB : numB - numA;
            return asc ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });
        rows.forEach(row => table.appendChild(row));
        table.setAttribute('data-sort-dir', asc ? 'asc' : 'desc');
    }
});
