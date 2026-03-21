<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title><?= $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= $this->config->item('app_name') . ' - ' . $this->config->item('app_subname') ?>">
    <meta name="csrf-token-name" content="<?= config_item('csrf_token_name') ?>">
    <meta name="csrf-cookie-name" content="<?= config_item('csrf_cookie_name') ?>">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/auth-modern.css" />
    <link href="<?= base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/img/favicon.png">
    <script src="<?= base_url() ?>assets/js/jquery-1.12.4.min.js"></script>
    <script src="<?= base_url() ?>assets/js/jquery.mask.min.js"></script>
    <script src="<?= base_url() ?>assets/js/funcoes.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
</head>

<?php
$parse_email = $this->input->get('e');
?>

<body class="auth-page">
    <div class="auth-bg-shape auth-bg-shape-one"></div>
    <div class="auth-bg-shape auth-bg-shape-two"></div>

    <div class="auth-shell">
        <section class="auth-aside">
            <div class="auth-brand">
                <img src="<?= base_url(); ?>assets/img/logo3.png" alt="<?= $this->config->item('app_name') ?>">
            </div>
            <h1>Area do cliente</h1>
            <p>Acompanhe ordens de servico, compras, cobrancas e dados cadastrais em um unico painel.</p>
            <div class="auth-version">Versao <?= $this->config->item('app_version'); ?></div>
            <img src="<?= base_url() ?>assets/img/forms-animate.svg" class="auth-illustration" alt="Area do cliente">
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-card-header">
                    <h2>Entrar na area do cliente</h2>
                    <p>Informe suas credenciais para acessar seu painel.</p>
                </div>

                <form id="formLogin" method="post" action="<?= site_url('mine/login') ?>">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                    <div class="auth-form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="text" placeholder="voce@email.com" value="<?= trim($parse_email); ?>" />
                    </div>

                    <div class="auth-form-group">
                        <label for="senha">Senha</label>
                        <input id="senha" name="senha" type="password" placeholder="Sua senha" />
                    </div>

                    <button class="auth-submit" type="submit">Acessar</button>
                    <a href="<?= site_url('mine/cadastrar') ?>" class="btn btn-success" style="margin-top:10px; width:100%; border-radius:12px; min-height:44px; font-weight:700;">Cadastrar-me</a>

                    <div class="links-uteis" style="text-align:center; margin-top:14px;">
                        <a href="<?= site_url('mine/resetarSenha') ?>" style="font-size:0.9rem; color:#334155;">Esqueci minha senha</a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
    <script src="<?= base_url() ?>assets/js/jquery.validate.js"></script>
    <script src="<?= base_url() ?>assets/js/sweetalert2.all.min.js"></script>
    <?php if ($this->session->flashdata('success') != null) { ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: '<?= $this->session->flashdata('success'); ?>',
                showConfirmButton: false,
                timer: 4000
            })
        </script>
    <?php } ?>

    <?php if ($this->session->flashdata('error') != null) { ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: '<?= $this->session->flashdata('error'); ?>',
                showConfirmButton: false,
                timer: 4000
            })
        </script>
    <?php } ?>

    <script type="text/javascript">
        $(document).ready(function() {
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
                        required: 'Campo obrigatorio.',
                        email: 'Insira um email valido'
                    },
                    senha: {
                        required: 'Campo obrigatorio.'
                    }
                },
                submitHandler: function(form) {
                    var dados = $(form).serialize();

                    $.ajax({
                        type: "POST",
                        url: "<?= base_url(); ?>index.php/mine/login?ajax=true",
                        data: dados,
                        dataType: 'json',
                        success: function(data) {
                            if (data.result == true) {
                                window.location.href = "<?= base_url(); ?>index.php/mine/painel";
                            } else {
                                Swal.fire({
                                    position: 'center',
                                    icon: 'error',
                                    title: data.message || 'Os dados de acesso estao incorretos. Por favor tente novamente.',
                                    showConfirmButton: false,
                                    timer: 4000
                                });

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
