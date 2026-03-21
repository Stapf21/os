<?php
$clientePmocOnly = (bool) $this->session->userdata('cliente_pmoc_only');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <title>Area do Cliente - <?= $this->config->item('app_name') ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= $this->config->item('app_name') . ' - ' . $this->config->item('app_subname') ?>">
    <meta name="csrf-token-name" content="<?= config_item('csrf_token_name') ?>">
    <meta name="csrf-cookie-name" content="<?= config_item('csrf_cookie_name') ?>">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-style.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/matrix-media.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/fullcalendar.css" />
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/conecte-modern.css" />
    <link href="<?= base_url(); ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/img/favicon.png">
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/jquery-1.12.4.min.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/sweetalert.min.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/funcoesGlobal.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/js/csrf.js"></script>
</head>

<body class="cliente-layout <?= $clientePmocOnly ? 'cliente-pmoc-only' : '' ?>">
    <div class="cliente-shell">
        <nav id="sidebar" class="cliente-sidebar">
            <div class="cliente-sidebar-header">
                <div class="cliente-logo">
                    <img src="<?= base_url() ?>assets/img/logo3.png" alt="<?= $this->config->item('app_name'); ?>">
                </div>
                <div class="cliente-brand">
                    <strong><?= $this->config->item('app_name'); ?></strong>
                    <small>Area do Cliente</small>
                </div>
            </div>

            <div class="cliente-user-chip">
                <i class='bx bx-user-circle'></i>
                <span><?= $this->session->userdata('nome') ?></span>
            </div>

            <div class="cliente-menu-label">Navegacao</div>
            <ul class="cliente-menu-list">
                <?php if ($clientePmocOnly): ?>
                    <li class="cliente-menu-item <?= isset($menuPmoc) ? 'active' : '' ?>">
                        <a class="cliente-menu-link" href="<?= site_url('mine/pmoc'); ?>" title="PMOC e Plano">
                            <i class='bx bx-clipboard'></i>
                            <span>PMOC e Plano</span>
                        </a>
                    </li>
                <?php else: ?>
                    <li class="cliente-menu-item <?= isset($menuPainel) ? 'active' : '' ?>">
                        <a class="cliente-menu-link" href="<?= site_url('mine/painel'); ?>" title="Painel">
                            <i class='bx bx-home-alt'></i>
                            <span>Painel</span>
                        </a>
                    </li>
                    <li class="cliente-menu-item <?= isset($menuConta) ? 'active' : '' ?>">
                        <a class="cliente-menu-link" href="<?= site_url('mine/conta'); ?>" title="Minha Conta">
                            <i class='bx bx-user-circle'></i>
                            <span>Minha Conta</span>
                        </a>
                    </li>
                    <li class="cliente-menu-item <?= isset($menuOs) ? 'active' : '' ?>">
                        <a class="cliente-menu-link" href="<?= site_url('mine/os'); ?>" title="Ordens de Servico">
                            <i class='bx bx-spreadsheet'></i>
                            <span>Ordens de Servico</span>
                        </a>
                    </li>
                    <li class="cliente-menu-item <?= isset($menuVendas) ? 'active' : '' ?>">
                        <a class="cliente-menu-link" href="<?= site_url('mine/compras'); ?>" title="Compras">
                            <i class='bx bx-cart-alt'></i>
                            <span>Compras</span>
                        </a>
                    </li>
                    <li class="cliente-menu-item <?= isset($menuCobrancas) ? 'active' : '' ?>">
                        <a class="cliente-menu-link" href="<?= site_url('mine/cobrancas'); ?>" title="Cobrancas">
                            <i class='bx bx-credit-card-front'></i>
                            <span>Cobrancas</span>
                        </a>
                    </li>
                    <li class="cliente-menu-item <?= isset($menuPmoc) ? 'active' : '' ?>">
                        <a class="cliente-menu-link" href="<?= site_url('mine/pmoc'); ?>" title="PMOC e Plano">
                            <i class='bx bx-clipboard'></i>
                            <span>PMOC e Plano</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="cliente-sidebar-footer">
                <a class="cliente-menu-link cliente-logout-link" href="<?= site_url('mine/sair'); ?>" title="Sair">
                    <i class='bx bx-log-out-circle'></i>
                    <span>Sair</span>
                </a>
            </div>
        </nav>

        <div class="cliente-sidebar-overlay" id="cliente-sidebar-overlay"></div>

        <div id="content" class="cliente-content">
            <header class="cliente-topbar">
                <button type="button" class="cliente-sidebar-toggle" id="cliente-menu-toggle" aria-label="Alternar menu lateral">
                    <i class='bx bx-menu' id="cliente-menu-toggle-icon"></i>
                </button>

                <div id="breadcrumb">
                    <?php if ($clientePmocOnly): ?>
                        <a href="<?= site_url('mine/pmoc'); ?>" title="PMOC e Plano" class="tip-bottom"><i class="fas fa-clipboard"></i> PMOC e Plano</a>
                    <?php else: ?>
                        <a href="<?= site_url('mine/painel'); ?>" title="Painel" class="tip-bottom"><i class="fas fa-home"></i> Painel</a>
                    <?php endif; ?>
                </div>

                <div id="user-nav" class="navbar navbar-inverse">
                    <ul class="nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class='bx bx-user-circle'></i> <?= $this->session->userdata('nome') ?></a>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <?php if (! $clientePmocOnly): ?>
                                    <li><a title="Meu Perfil" href="<?= site_url('mine/conta'); ?>"><i class="fas fa-user"></i> Meu Perfil</a></li>
                                    <li class="divider"></li>
                                <?php endif; ?>
                                <li><a title="Sair" href="<?= site_url('mine/sair'); ?>"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </header>

            <main class="cliente-main">
                <div class="container-fluid">
                    <div class="row-fluid">
                        <div class="span12">
                            <?php if ($var = $this->session->flashdata('success')) : ?><script>swal('Sucesso', "<?= str_replace('"', '', $var); ?>", 'success');</script><?php endif; ?>
                            <?php if ($var = $this->session->flashdata('error')) : ?><script>swal('Falha', "<?= str_replace('"', '', $var); ?>", 'error');</script><?php endif; ?>
                            <?php if (isset($output)) { $this->load->view($output); } ?>
                        </div>
                    </div>
                </div>
            </main>

            <div class="row-fluid">
                <div id="footer" class="span12 cliente-footer">
                    <?= date('Y') ?> &copy; <?= $this->config->item('app_name'); ?> - Versao: <?= $this->config->item('app_version'); ?>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url(); ?>assets/js/bootstrap.min.js"></script>
    <script src="<?= base_url(); ?>assets/js/matrix.js"></script>
    <script>
        $(function() {
            var $body = $('body');
            var $toggle = $('#cliente-menu-toggle');
            var $icon = $('#cliente-menu-toggle-icon');
            var $overlay = $('#cliente-sidebar-overlay');
            var mobileBreakpoint = 980;

            function isMobile() {
                return $(window).width() <= mobileBreakpoint;
            }

            function applyIcon() {
                if (isMobile()) {
                    $icon.attr('class', $body.hasClass('sidebar-open') ? 'bx bx-x' : 'bx bx-menu');
                    return;
                }

                $icon.attr('class', $body.hasClass('sidebar-collapsed') ? 'bx bx-chevrons-right' : 'bx bx-chevrons-left');
            }

            function loadDesktopPreference() {
                if (isMobile()) {
                    $body.removeClass('sidebar-collapsed');
                    return;
                }

                if (localStorage.getItem('clienteSidebarCollapsed') === '1') {
                    $body.addClass('sidebar-collapsed');
                } else {
                    $body.removeClass('sidebar-collapsed');
                }
            }

            $toggle.on('click', function() {
                if (isMobile()) {
                    $body.toggleClass('sidebar-open');
                } else {
                    $body.toggleClass('sidebar-collapsed');
                    localStorage.setItem('clienteSidebarCollapsed', $body.hasClass('sidebar-collapsed') ? '1' : '0');
                }

                applyIcon();
            });

            $overlay.on('click', function() {
                $body.removeClass('sidebar-open');
                applyIcon();
            });

            $('#sidebar a').on('click', function() {
                if (isMobile()) {
                    $body.removeClass('sidebar-open');
                    applyIcon();
                }
            });

            $(window).on('resize', function() {
                if (isMobile()) {
                    $body.removeClass('sidebar-open');
                } else {
                    loadDesktopPreference();
                }
                applyIcon();
            });

            loadDesktopPreference();
            applyIcon();
        });
    </script>
</body>

</html>
