<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();

$feedbackMsg = "";
$feedbackType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_SPECIAL_CHARS);
    $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($nome && $email && $mensagem) {
        $logDir = __DIR__ . '/uploads';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/messages_log.json';
        $existingLogs = [];
        if (file_exists($logFile)) {
            $jsonContent = @file_get_contents($logFile);
            if ($jsonContent) {
                $existingLogs = json_decode($jsonContent, true) ?? [];
            }
        }

        $newEntry = [
            'id' => uniqid(),
            'timestamp' => date('Y-m-d H:i:s'),
            'nome' => $nome,
            'email' => $email,
            'assunto' => $assunto ?: 'Contato Geral - Free PDF Studio',
            'mensagem' => $mensagem,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        $existingLogs[] = $newEntry;
        @file_put_contents($logFile, json_encode($existingLogs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $to = "contato@4u.ia.br";
        $subject = "Contato Suporte Free PDF Studio: " . ($assunto ?: "Geral");
        $body = "Nome: $nome\nE-mail: $email\nAssunto: $assunto\nData: " . date("d/m/Y H:i:s") . "\n\nMensagem:\n$mensagem\n";
        $headers = "From: contato@4u.ia.br\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        @mail($to, $subject, $body, $headers);

        $feedbackMsg = "Mensagem enviada com sucesso! Nossa equipe responderá em breve.";
        $feedbackType = "success";
    } else {
        $feedbackMsg = "Por favor, preencha todos os campos obrigatórios corretamente.";
        $feedbackType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Suporte &amp; Ajuda — Free PDF Studio</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; background: #070a12; color: #e2e8f0; }</style>
</head>
<body class="min-h-screen flex flex-col justify-between">

    <header class="bg-gray-900 border-b border-gray-800 py-4 px-6">
        <div class="max-w-4xl mx-auto flex items-center justify-between">
            <a href="index.php" class="text-blue-400 font-bold text-sm flex items-center gap-2 hover:text-blue-300">
                ← Voltar ao Free PDF
            </a>
            <span class="text-xs text-gray-500">Central de Suporte</span>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-10 flex-1 w-full">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-lg">
                🎧
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Central de Suporte &amp; FAQ</h1>
                <p class="text-xs text-gray-400">Tire suas dúvidas ou envie uma mensagem direta para a nossa equipe técnica.</p>
            </div>
        </div>

        <?php if ($feedbackMsg): ?>
            <div class="mb-6 p-4 rounded-xl text-sm font-semibold border <?php echo $feedbackType === 'success' ? 'bg-green-950/80 border-green-700 text-green-300' : 'bg-red-950/80 border-red-700 text-red-300'; ?>">
                <?php echo $feedbackMsg; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-gray-900/60 p-6 rounded-xl border border-gray-800 shadow-xl">
                <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                    ✉️ Enviar Mensagem
                </h2>
                <form action="suporte.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">Seu Nome *</label>
                        <input type="text" name="nome" required class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-sm text-white focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">Seu E-mail *</label>
                        <input type="email" name="email" required class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-sm text-white focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">Assunto</label>
                        <input type="text" name="assunto" placeholder="Ex: Dúvida sobre o Free PDF Studio" class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-sm text-white focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 mb-1">Mensagem *</label>
                        <textarea name="mensagem" rows="4" required class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2.5 text-sm text-white focus:border-blue-500 focus:outline-none" placeholder="Escreva aqui sua mensagem..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2.5 px-4 rounded-lg text-sm transition-all shadow-md">
                        Enviar Mensagem
                    </button>
                </form>
            </div>

            <div class="space-y-4">
                <h2 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                    ❓ Perguntas Frequentes
                </h2>

                <div class="bg-gray-900/60 p-4 rounded-xl border border-gray-800">
                    <h3 class="font-semibold text-sm text-white mb-1">Como funciona a assinatura digital no Free PDF?</h3>
                    <p class="text-xs text-gray-400">Desenhe sua assinatura no painel direito (Sign), clique em "Save Signature" e arraste a assinatura para a posição exata no documento.</p>
                </div>

                <div class="bg-gray-900/60 p-4 rounded-xl border border-gray-800">
                    <h3 class="font-semibold text-sm text-white mb-1">Os meus arquivos PDF são salvos em servidores?</h3>
                    <p class="text-xs text-gray-400">Não! O Free PDF Studio funciona 100% no seu navegador. Seus arquivos nunca saem da memória local da sua máquina.</p>
                </div>

                <div class="bg-gray-900/60 p-4 rounded-xl border border-gray-800">
                    <h3 class="font-semibold text-sm text-white mb-1">Qual o e-mail oficial de suporte?</h3>
                    <p class="text-xs text-gray-400">Você pode nos enviar uma mensagem a qualquer momento pelo e-mail: <a href="mailto:contato@4u.ia.br" class="text-blue-400 underline font-medium">contato@4u.ia.br</a>.</p>
                </div>
            </div>

        </div>
    </main>

    <footer class="bg-gray-900 border-t border-gray-800 py-4 text-center text-xs text-gray-500">
        &copy; <?php echo date("Y"); ?> Free PDF Studio &bull; Todos os direitos reservados.
    </footer>

</body>
</html>
