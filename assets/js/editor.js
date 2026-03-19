$(document).ready(function() {
    tinymce.init({
        selector: '.editor',
        language: 'pt_BR',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        images_upload_url: '/assets/upload/upload_tinymce.php', // endpoint para upload de imagens
        automatic_uploads: true,
        images_reuse_filename: true,
        height: 300,
        branding: false,
        content_style: "img {max-width:100%; height:auto; display:block; margin:10px auto;}"
    });
}); 