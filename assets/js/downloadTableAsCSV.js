function downloadTableAsCSV(tableId, filename = 'table', options = {}) {
    const { skipColumns = [], includeColumns = null } = options;

    const table = document.getElementById(tableId);
    let csv = [];

    for (let row of table.rows) {
        let rowData = [];
        const totalCells = row.cells.length;

        // Normalize skipColumns (handle negative indices)
        const resolvedSkipColumns = skipColumns.map(i => (i < 0 ? totalCells + i : i));

        // Normalize includeColumns if defined
        const resolvedIncludeColumns = includeColumns
            ? includeColumns.map(i => (i < 0 ? totalCells + i : i))
            : null;

        for (let i = 0; i < totalCells; i++) {
            const shouldSkip = resolvedSkipColumns.includes(i);
            const shouldInclude = resolvedIncludeColumns ? resolvedIncludeColumns.includes(i) : true;

            if (shouldSkip || !shouldInclude) continue;

            let text = row.cells[i].innerText.replace(/"/g, '""');
            rowData.push(`"${text}"`);
        }
        csv.push(rowData.join(','));
    }

    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);

    const link = document.createElement('a');
    link.href = url;

    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    const dateTimeStr = `${year}-${month}-${day}_${hours}-${minutes}-${seconds}`;

    link.setAttribute('download', `${filename}_${dateTimeStr}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
