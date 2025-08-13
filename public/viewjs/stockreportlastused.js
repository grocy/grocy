var stockLastUsedTable = $('#stock-last-used-table').DataTable({
    'order': [[2, 'asc']], // Order by 'Last used' date by default
    'columnDefs': [
        { 'orderable': false, 'targets': 0 },
        { 'searchable': false, "targets": 0 }
    ].concat($.fn.dataTable.defaults.columnDefs)
});
$('#stock-last-used-table tbody').removeClass("d-none");
stockLastUsedTable.columns.adjust().draw();

$("#search").on("keyup", Delay(function()
{
    var value = $(this).val();
    if (value === "all")
    {
        value = "";
    }

    stockLastUsedTable.search(value).draw();
}, Grocy.FormFocusDelay));
