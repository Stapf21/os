// Upload de anexos
$(document).on('change', 'input[name="userfile[]"]', function() {
    var formData = new FormData();
    var files = $(this)[0].files;
    var os_id = $('#idOs').val();

    for (var i = 0; i < files.length; i++) {
        formData.append('userfile[]', files[i]);
    }
    formData.append('os_id', os_id);

    $.ajax({
        url: baseUrl + 'index.php/os/uploadAnexo',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso',
                    text: 'Anexo(s) adicionado(s) com sucesso!'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: data.error || 'Erro ao fazer upload do arquivo.'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao fazer upload do arquivo.'
            });
        }
    });
});

// Exclusão de anexos
$(document).on('click', '.btn-excluir-anexo', function(e) {
    e.preventDefault();
    var id = $(this).attr('anexo');
    
    Swal.fire({
        title: 'Tem certeza?',
        text: "Você não poderá reverter isso!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = baseUrl + 'index.php/os/excluirAnexo/' + id;
        }
    });
}); 