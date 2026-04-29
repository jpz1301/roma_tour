<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function(){
    $('#tabla').DataTable({
        language:{url:"//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"},
        pageLength:10,
        order:[[0,'desc']],
        columnDefs:[{orderable:false,targets:-1}]
    });
});
</script>
</body>
</html>