<?php

defined('BASEPATH') or exit('No direct script access allowed');

$config['deploy_branch'] = $_ENV['DEPLOY_BRANCH'] ?? 'main';
$config['deploy_script_path'] = $_ENV['DEPLOY_SCRIPT_PATH'] ?? 'scripts/deploy.sh';
$config['deploy_log_file'] = $_ENV['DEPLOY_LOG_FILE'] ?? 'application/logs/deploy.log';
$config['deploy_lock_file'] = $_ENV['DEPLOY_LOCK_FILE'] ?? 'application/cache/deploy.lock';
$config['deploy_allow_web_trigger'] = filter_var($_ENV['ALLOW_WEB_DEPLOY'] ?? false, FILTER_VALIDATE_BOOLEAN);
$config['deploy_log_tail_lines'] = (int) ($_ENV['DEPLOY_LOG_TAIL_LINES'] ?? 120);
