<?php
require_once 'telemetry_db.php';
error_reporting(0);

$speedtest = getSpeedtestUserById($_GET['id']);
if (!is_array($speedtest)) {
    header('Location: ../index.html');
    exit();
}

function format($d) {
    if ($d < 10) return number_format($d, 2, '.', '');
    if ($d < 100) return number_format($d, 1, '.', '');
    return number_format($d, 0, '.', '');
}

$dl = format($speedtest['dl']);
$ul = format($speedtest['ul']);
$ping = format($speedtest['ping']);
$jitter = format($speedtest['jitter']);
$timestamp = $speedtest['timestamp'];

$ispinfo = json_decode($speedtest['ispinfo'], true)['processedString'] ?? '';
$dash = strpos($ispinfo, '-');
if ($dash !== false) {
    $ispinfo = substr($ispinfo, $dash + 2);
    $par = strrpos($ispinfo, '(');
    if ($par !== false) {
        $ispinfo = substr($ispinfo, 0, $par);
    }
} else {
    $ispinfo = '';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resultado do Teste de Velocidade - TI Remoto</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #111424;
    font-family: 'Inter', sans-serif;
    color: #ffffff;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.container {
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(14, 229, 127, 0.2);
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.3);
    width: 100%;
    max-width: 600px;
    text-align: center;
}

.header {
    margin-bottom: 40px;
}

.logo {
    height: 60px;
    margin-bottom: 15px;
}

.title {
    font-size: 28px;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 10px;
}

.subtitle {
    color: rgba(255,255,255,0.7);
    font-size: 16px;
}

.results-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.result-card {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 25px 20px;
    transition: all 0.3s ease;
}

.result-card:hover {
    border-color: rgba(14, 229, 127, 0.3);
    transform: translateY(-2px);
}

.result-label {
    font-size: 14px;
    font-weight: 500;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}

.result-value {
    font-size: 32px;
    font-weight: 700;
    color: #0EE57F;
    margin-bottom: 5px;
    text-shadow: 0 2px 10px rgba(14, 229, 127, 0.3);
}

.result-unit {
    font-size: 14px;
    color: rgba(255,255,255,0.5);
    font-weight: 500;
}

.info-section {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 30px;
    text-align: left;
}

.info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 500;
    color: rgba(255,255,255,0.8);
}

.info-value {
    color: #ffffff;
    font-family: 'Monaco', monospace;
    font-size: 14px;
}

.actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    background: linear-gradient(135deg, #0EE57F, #0BC96B);
    color: #111424;
    border: none;
    border-radius: 12px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(14, 229, 127, 0.3);
}

.btn-secondary {
    background: rgba(255,255,255,0.08);
    color: #ffffff;
    border: 1px solid rgba(255,255,255,0.2);
    text-decoration: none;
    font-weight: 600;
    border-radius: 12px;
    padding: 12px 24px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
}

.btn-secondary:hover {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.4);
    box-shadow: 0 8px 25px rgba(255,255,255,0.1);
    color: #ffffff;
    transform: translateY(-2px);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: #1A1D2A;
    border: 1px solid rgba(14, 229, 127, 0.3);
    border-radius: 20px;
    padding: 30px;
    max-width: 400px;
    width: 90%;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}

.modal-title {
    font-size: 20px;
    font-weight: 600;
    color: #0EE57F;
    margin-bottom: 15px;
}

.modal-message {
    color: rgba(255,255,255,0.8);
    margin-bottom: 25px;
    line-height: 1.5;
}

.modal-close {
    background: #0EE57F;
    color: #111424;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: #0BC96B;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .container {
        padding: 30px 20px;
    }
    
    .results-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .result-value {
        font-size: 28px;
    }
    
    .actions {
        flex-direction: column;
    }
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="../.logo/logo2.png" alt="TI Remoto" class="logo">
        <div class="title">Resultado do Teste</div>
        <div class="subtitle">Teste de Velocidade da Internet</div>
    </div>
    
    <div class="results-grid">
        <div class="result-card">
            <div class="result-label">Download</div>
            <div class="result-value"><?= htmlspecialchars($dl) ?></div>
            <div class="result-unit">Mbit/s</div>
        </div>
        
        <div class="result-card">
            <div class="result-label">Upload</div>
            <div class="result-value"><?= htmlspecialchars($ul) ?></div>
            <div class="result-unit">Mbit/s</div>
        </div>
        
        <div class="result-card">
            <div class="result-label">Ping</div>
            <div class="result-value"><?= htmlspecialchars($ping) ?></div>
            <div class="result-unit">ms</div>
        </div>
        
        <div class="result-card">
            <div class="result-label">Jitter</div>
            <div class="result-value"><?= htmlspecialchars($jitter) ?></div>
            <div class="result-unit">ms</div>
        </div>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Data do Teste:</span>
            <span class="info-value"><?= htmlspecialchars($timestamp) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">IP:</span>
            <span class="info-value"><?= htmlspecialchars($speedtest['ip']) ?></span>
        </div>
        <?php if (!empty($ispinfo)): ?>
        <div class="info-row">
            <span class="info-label">Provedor:</span>
            <span class="info-value"><?= htmlspecialchars($ispinfo) ?></span>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="actions">
        <a href="../index.html" class="btn">Fazer Novo Teste</a>
        <a href="index.php?id=<?= htmlspecialchars($_GET['id']) ?>" class="btn-secondary">Baixar Imagem</a>
        <button class="btn-secondary" onclick="copyLink()">Copiar Link</button>
    </div>
</div>

<div id="modal" class="modal">
    <div class="modal-content">
        <div id="modalTitle" class="modal-title"></div>
        <div id="modalMessage" class="modal-message"></div>
        <button class="modal-close" onclick="closeModal()">OK</button>
    </div>
</div>

<script>
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        showModal('Link Copiado!', 'O link foi copiado para a área de transferência com sucesso.');
    }).catch(function() {
        showModal('Erro', 'Não foi possível copiar o link. Tente selecionar e copiar manualmente.');
    });
}

function showModal(title, message) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('modal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}
</script>
</body>
</html>