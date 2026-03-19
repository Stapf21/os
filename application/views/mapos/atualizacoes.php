<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .deploy-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 16px;
    }
    .deploy-card {
        border: 1px solid #dbe2ea;
        border-radius: 12px;
        background: #fff;
        padding: 14px 16px;
    }
    .deploy-card small {
        display: block;
        color: #5f6f82;
        margin-bottom: 6px;
        text-transform: uppercase;
        font-size: 11px;
    }
    .deploy-card strong {
        color: #1f344d;
        word-break: break-word;
    }
    .deploy-log {
        border-radius: 12px;
        border: 1px solid #dbe2ea;
        background: #0f1720;
        color: #e7edf6;
        max-height: 460px;
        overflow: auto;
        white-space: pre-wrap;
        word-break: break-word;
        padding: 14px;
        font-family: Consolas, monospace;
        font-size: 12px;
        line-height: 1.45;
    }
    .deploy-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 12px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon"><i class="fas fa-sync"></i></span>
                <h5>Atualizacoes do Sistema</h5>
            </div>

            <div class="widget-content" style="padding:16px;">
                <div class="deploy-grid">
                    <div class="deploy-card">
                        <small>Branch</small>
                        <strong><?php echo htmlspecialchars($deploy['branch']); ?></strong>
                    </div>
                    <div class="deploy-card">
                        <small>Commit atual</small>
                        <strong><?php echo htmlspecialchars($deploy['git_current'] ?: '-'); ?></strong>
                    </div>
                    <div class="deploy-card">
                        <small>Commit remoto</small>
                        <strong><?php echo htmlspecialchars($deploy['git_remote'] ?: '-'); ?></strong>
                    </div>
                    <div class="deploy-card">
                        <small>Status</small>
                        <strong>
                            <?php echo $deploy['running'] ? 'Executando' : 'Parado'; ?>
                            <?php if (!empty($deploy['lock_pid'])) { ?>
                                (PID <?php echo (int) $deploy['lock_pid']; ?>)
                            <?php } ?>
                        </strong>
                    </div>
                    <div class="deploy-card">
                        <small>Repositorio</small>
                        <strong><?php echo $deploy['git_clean'] === null ? '-' : ($deploy['git_clean'] ? 'Limpo' : 'Com alteracoes locais'); ?></strong>
                    </div>
                    <div class="deploy-card">
                        <small>Disparo via painel</small>
                        <strong><?php echo $deploy['allow_web_trigger'] ? 'Ativado' : 'Desativado'; ?></strong>
                    </div>
                    <div class="deploy-card">
                        <small>Script</small>
                        <strong><?php echo htmlspecialchars($deploy['script_path']); ?></strong>
                    </div>
                    <div class="deploy-card">
                        <small>Lock file</small>
                        <strong><?php echo htmlspecialchars($deploy['lock_file']); ?></strong>
                    </div>
                    <div class="deploy-card">
                        <small>Log file</small>
                        <strong><?php echo htmlspecialchars($deploy['log_file']); ?></strong>
                    </div>
                </div>

                <div class="deploy-actions">
                    <form action="<?php echo site_url('configuracoes/atualizacoes/executar'); ?>" method="post" style="margin:0;">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <button type="submit" class="button btn btn-warning" <?php echo (!$deploy['allow_web_trigger'] || $deploy['is_windows'] || !$deploy['script_exists']) ? 'disabled' : ''; ?>>
                            <span class="button__icon"><i class="bx bx-play"></i></span>
                            <span class="button__text2">Executar atualizacao</span>
                        </button>
                    </form>
                    <form action="<?php echo site_url('configuracoes/atualizacoes/migracoes'); ?>" method="post" style="margin:0;">
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <button type="submit" class="button btn btn-success">
                            <span class="button__icon"><i class="bx bx-data"></i></span>
                            <span class="button__text2">Executar migrations</span>
                        </button>
                    </form>
                    <a href="<?php echo site_url('configuracoes/atualizacoes'); ?>" class="button btn btn-info">
                        <span class="button__icon"><i class="bx bx-refresh"></i></span>
                        <span class="button__text2">Recarregar status</span>
                    </a>
                    <a href="<?php echo site_url('mapos/configurar'); ?>" class="button btn btn-default">
                        <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                        <span class="button__text2">Voltar</span>
                    </a>
                </div>

                <?php if (!$deploy['script_exists']) { ?>
                    <div class="alert alert-error">Script de deploy nao encontrado no caminho configurado.</div>
                <?php } ?>
                <?php if (!$deploy['allow_web_trigger']) { ?>
                    <div class="alert alert-warning">Atualizacao pelo painel esta desativada. Configure <code>ALLOW_WEB_DEPLOY=true</code> no <code>application/.env</code> para habilitar.</div>
                <?php } ?>
                <?php if ($deploy['is_windows']) { ?>
                    <div class="alert alert-warning">Execucao via painel em Windows nao e permitida.</div>
                <?php } ?>

                <h5>Log do deploy</h5>
                <div class="deploy-log"><?php echo htmlspecialchars($deploy['log_tail'] ?: 'Sem registros ainda.'); ?></div>
            </div>
        </div>
    </div>
</div>
