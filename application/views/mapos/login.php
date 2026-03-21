<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/auth-modern.css" />
    <link href="<?= base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="shortcut icon" type="image/png" href="<?= base_url(); ?>assets/img/favicon.png" />
</head>

<?php
function saudacao($nome = '')
{
    $hora = date('H');
    if ($hora >= 0 && $hora < 12) {
        return 'Ola, bom dia' . (empty($nome) ? '' : ', ' . $nome);
    }
    if ($hora >= 12 && $hora < 18) {
        return 'Ola, boa tarde' . (empty($nome) ? '' : ', ' . $nome);
    }

    return 'Ola, boa noite' . (empty($nome) ? '' : ', ' . $nome);
}
?>

<body class="auth-page">
    <div class="auth-bg-shape auth-bg-shape-one"></div>
    <div class="auth-bg-shape auth-bg-shape-two"></div>

    <div class="auth-shell">
        <section class="auth-aside">
            <div class="auth-brand">
                <img src="<?= base_url(); ?>assets/img/logo3.png" alt="<?= $this->config->item('app_name') ?>">
            </div>
            <h1><?= saudacao('equipe') ?></h1>
            <p>Painel interno para gestao de ordens de servico, clientes e operacao.</p>
            <div class="auth-version">Versao <?= $this->config->item('app_version'); ?></div>
            <img src="<?= base_url() ?>assets/img/dashboard-animate.svg" class="auth-illustration" alt="Painel do sistema">
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-card-header">
                    <h2>Entrar no sistema</h2>
                    <p>Use seu email e senha para continuar.</p>
                </div>

                <form id="formLogin" method="post" action="<?= site_url('login/verificarLogin') ?>">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <?php if ($this->session->flashdata('error') != null) { ?>
                        <div class="alert alert-danger">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?= $this->session->flashdata('error'); ?>
                        </div>
                    <?php } ?>

                    <div class="auth-form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="text" placeholder="voce@empresa.com">
                    </div>
                    <div class="auth-form-group">
                        <label for="senha">Senha</label>
                        <input id="senha" name="senha" type="password" placeholder="Sua senha">
                    </div>

                    <button id="btn-acessar" class="auth-submit" type="submit">Acessar</button>
                </form>
            </div>
        </section>
    </div>

    <a href="#notification" id="call-modal" role="button" class="btn hide" data-toggle="modal">notification</a>
    <div id="notification" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-header">
            <h4>Sistema</h4>
        </div>
        <div class="modal-body">
            <h5 style="text-align: center" id="message">Os dados de acesso estao incorretos, por favor tente novamente.</h5>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Fechar</button>
        </div>
    </div>

    <script src="<?= base_url() ?>assets/js/jquery-1.12.4.min.js"></script>
    <script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
    <script src="<?= base_url() ?>assets/js/validate.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#email').focus();
            $("#formLogin").validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    senha: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        required: '',
                        email: 'Insira um email valido'
                    },
                    senha: {
                        required: 'Campo obrigatorio.'
                    }
                },
                submitHandler: function(form) {
                    var dados = $(form).serialize();
                    $('#btn-acessar').addClass('disabled');

                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('login/verificarLogin?ajax=true'); ?>",
                        data: dados,
                        dataType: 'json',
                        success: function(data) {
                            if (data.result == true) {
                                window.location.href = "<?= site_url('mapos'); ?>";
                            } else {
                                $('#btn-acessar').removeClass('disabled');
                                $('#message').text(data.message || 'Os dados de acesso estao incorretos, por favor tente novamente.');
                                $('#call-modal').trigger('click');

                                var newCsrfToken = data.MAPOS_TOKEN;
                                $("input[name='<?= $this->security->get_csrf_token_name(); ?>']").val(newCsrfToken);
                            }
                        }
                    });

                    return false;
                },
                errorClass: "help-inline",
                errorElement: "span",
                highlight: function(element) {
                    $(element).closest('.auth-form-group').addClass('error');
                },
                unhighlight: function(element) {
                    $(element).closest('.auth-form-group').removeClass('error');
                }
            });
        });
    </script>
</body>

</html>
