<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Termos de Uso — Free PDF Studio</title>
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
            <span class="text-xs text-gray-500">Termos Legais</span>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-10 flex-1">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-lg shadow-lg">
                📜
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">Termos de Uso</h1>
                <p class="text-xs text-gray-400">Última atualização: <?php echo date("d/m/Y"); ?></p>
            </div>
        </div>

        <div class="space-y-6 text-sm text-gray-300 leading-relaxed">
            <section class="bg-gray-900/60 p-6 rounded-xl border border-gray-800">
                <h2 class="text-base font-bold text-white mb-3 flex items-center gap-2">
                    <span>1.</span> Licença e Uso Gratuito
                </h2>
                <p>O <strong>Free PDF Studio</strong> é disponibilizado de forma 100% gratuita para uso pessoal, acadêmico e comercial. Você tem a liberdade de abrir, editar, assinar e exportar seus arquivos PDF sem limitações.</p>
            </section>

            <section class="bg-gray-900/60 p-6 rounded-xl border border-gray-800">
                <h2 class="text-base font-bold text-white mb-3 flex items-center gap-2">
                    <span>2.</span> Responsabilidade do Usuário
                </h2>
                <p>O usuário é o único responsável pelo conteúdo dos arquivos carregados e editados na aplicação. O Free PDF Studio não realiza auditoria nos conteúdos manipulados localmente no seu dispositivo.</p>
            </section>

            <section class="bg-gray-900/60 p-6 rounded-xl border border-gray-800">
                <h2 class="text-base font-bold text-white mb-3 flex items-center gap-2">
                    <span>3.</span> Suporte Legal e Contato
                </h2>
                <p>Dúvidas sobre os Termos de Uso e suporte legal: <a href="mailto:contato@4u.ia.br" class="text-blue-400 font-semibold hover:underline">contato@4u.ia.br</a>.</p>
            </section>
        </div>
    </main>

    <footer class="bg-gray-900 border-t border-gray-800 py-4 text-center text-xs text-gray-500">
        &copy; <?php echo date("Y"); ?> Free PDF Studio &bull; Todos os direitos reservados.
    </footer>

</body>
</html>
