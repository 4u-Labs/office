<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>FreeOffice — Suíte Completa de Produtividade Web</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box !important; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #070a12; color: #e2e8f0; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between select-none">

    <!-- Top Navigation Header -->
    <header class="bg-gray-900 border-b border-gray-800 py-4 px-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-500 p-0.5 shadow-lg shadow-blue-500/25 flex items-center justify-center">
                    <div class="w-full h-full bg-[#0b0f19] rounded-[10px] flex items-center justify-center">
                        <span class="text-lg">💼</span>
                    </div>
                </div>
                <div class="flex flex-col">
                    <span class="font-extrabold text-lg text-white leading-none">Free<span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Office</span></span>
                    <span class="text-[10px] text-blue-400 font-bold tracking-widest uppercase mt-0.5" data-i18n="suiteTitle">SUÍTE COMPLETA WEB</span>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs font-semibold">
                <!-- SELETOR DE IDIOMA BILINGUE (PT / EN) -->
                <button onclick="toggleAppLanguage()" id="langToggleBtn" class="px-3 py-1.5 bg-gray-800 border border-gray-700 hover:border-blue-500 text-white font-bold rounded-lg shadow-md flex items-center gap-1.5 transition-all">
                    <span>🇧🇷</span> <span>PT</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Workspace Dashboard Cards -->
    <main class="max-w-7xl mx-auto px-6 py-12 flex-1 w-full">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h1 class="text-3xl font-extrabold text-white mb-3" data-i18n="mainHeading">Sua Suíte de Produtividade Gratuita no Navegador</h1>
            <p class="text-sm text-gray-400" data-i18n="mainDesc">Crie, edite e exporte planilhas, documentos de texto, apresentações de slides e arquivos PDF com salvamento automático local!</p>
        </div>

        <!-- Office Apps Grid (4 Cards Balanced) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- 1. FREEEXCEL -->
            <a href="excel/index.php" class="group bg-gray-900/60 hover:bg-gray-900 border border-gray-800 hover:border-emerald-500/60 p-6 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-2xl font-bold mb-4 shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-transform">
                        📊
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1 group-hover:text-emerald-400 transition-colors">FreeExcel</h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-4" data-i18n="excelDesc">Planilhas web profissionais com suporte a modelos prontos, centenas de fórmulas, salvamento automático e exportação nativa para .xlsx via SheetJS.</p>
                </div>
                <div class="text-xs font-bold text-emerald-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                    <span data-i18n="openExcel">Abrir FreeExcel</span> →
                </div>
            </a>

            <!-- 2. FREEWORD -->
            <a href="word/index.php" class="group bg-gray-900/60 hover:bg-gray-900 border border-gray-800 hover:border-blue-500/60 p-6 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold mb-4 shadow-lg shadow-blue-500/20 group-hover:scale-110 transition-transform">
                        📝
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1 group-hover:text-blue-400 transition-colors">FreeWord</h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-4" data-i18n="wordDesc">Processador de texto profissional em folha A4 com Modo Foco sem distrações, tabelas, tamanhos de fonte, leitura de .DOCX e auto-salvamento.</p>
                </div>
                <div class="text-xs font-bold text-blue-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                    <span data-i18n="openWord">Abrir FreeWord</span> →
                </div>
            </a>

            <!-- 3. FREEPOWERPOINT -->
            <a href="powerpoint/index.php" class="group bg-gray-900/60 hover:bg-gray-900 border border-gray-800 hover:border-orange-500/60 p-6 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-orange-600 to-amber-500 flex items-center justify-center text-white text-2xl font-bold mb-4 shadow-lg shadow-orange-500/20 group-hover:scale-110 transition-transform">
                        📽️
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1 group-hover:text-orange-400 transition-colors">FreePowerPoint</h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-4" data-i18n="pptDesc">Criador de apresentações visuais 16:9 com seleção de temas de cores, caixas de texto extras, imagens, transições e modo fullscreen (F5).</p>
                </div>
                <div class="text-xs font-bold text-orange-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                    <span data-i18n="openPpt">Abrir FreePowerPoint</span> →
                </div>
            </a>

            <!-- 4. FREEPDF STUDIO (NOVO CARD DEDICADO) -->
            <a href="freepdf/index.php" class="group bg-gray-900/60 hover:bg-gray-900 border border-gray-800 hover:border-rose-500/60 p-6 rounded-2xl shadow-xl transition-all transform hover:-translate-y-1 flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-rose-600 to-red-500 flex items-center justify-center text-white text-2xl font-bold mb-4 shadow-lg shadow-rose-500/20 group-hover:scale-110 transition-transform">
                        📄
                    </div>
                    <h3 class="text-lg font-bold text-white mb-1 group-hover:text-rose-400 transition-colors">FreePDF Studio</h3>
                    <p class="text-xs text-gray-400 leading-relaxed mb-4" data-i18n="pdfDesc">Visualizador, editor e anotador de arquivos PDF com ferramentas verticais de desenho (rabisco, reta, seta, quadrado e caneta marca-texto translúcida).</p>
                </div>
                <div class="text-xs font-bold text-rose-400 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                    <span data-i18n="openPdf">Abrir FreePDF</span> →
                </div>
            </a>

        </div>
    </main>

    <!-- Institutional Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-6 px-6 text-center text-xs text-gray-500">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                &copy; <?php echo date("Y"); ?> FreeOffice &bull; <span data-i18n="rights">Todos os direitos reservados.</span>
            </div>
            <div class="flex items-center gap-4 font-semibold text-gray-400">
                <a href="freepdf/privacidade.php" target="_blank" class="hover:text-blue-400 transition-colors" data-i18n="privacy">Privacidade</a>
                <span>•</span>
                <a href="freepdf/termos.php" target="_blank" class="hover:text-blue-400 transition-colors" data-i18n="terms">Termos</a>
                <span>•</span>
                <a href="freepdf/suporte.php" target="_blank" class="hover:text-blue-400 transition-colors" data-i18n="support">Suporte</a>
            </div>
        </div>
    </footer>

    <script>
        let currentLang = localStorage.getItem('freepdf_lang') || 'pt';

        const i18nDict = {
            pt: {
                suiteTitle: "SUÍTE COMPLETA WEB",
                mainHeading: "Sua Suíte de Produtividade Gratuita no Navegador",
                mainDesc: "Crie, edite e exporte planilhas, documentos de texto, apresentações de slides e arquivos PDF com salvamento automático local!",
                excelDesc: "Planilhas web profissionais com suporte a modelos prontos, centenas de fórmulas, salvamento automático e exportação nativa para .xlsx via SheetJS.",
                openExcel: "Abrir FreeExcel",
                wordDesc: "Processador de texto profissional em folha A4 com Modo Foco sem distrações, tabelas, tamanhos de fonte, leitura de .DOCX e auto-salvamento.",
                openWord: "Abrir FreeWord",
                pptDesc: "Criador de apresentações visuais 16:9 com seleção de temas de cores, caixas de texto extras, imagens, transições e modo fullscreen (F5).",
                openPpt: "Abrir FreePowerPoint",
                pdfDesc: "Visualizador, editor e anotador de arquivos PDF com ferramentas verticais de desenho (rabisco, reta, seta, quadrado e caneta marca-texto translúcida).",
                openPdf: "Abrir FreePDF",
                rights: "Todos os direitos reservados.",
                privacy: "Privacidade",
                terms: "Termos",
                support: "Suporte"
            },
            en: {
                suiteTitle: "FULL WEB SUITE",
                mainHeading: "Your Free Browser Productivity Suite",
                mainDesc: "Create, edit, and export spreadsheets, text documents, presentations, and PDF files with local auto-save!",
                excelDesc: "Professional web spreadsheets with support for templates, hundreds of formulas, auto-save, and native .xlsx export via SheetJS.",
                openExcel: "Open FreeExcel",
                wordDesc: "Professional word processor with A4 page layout, distraction-free Focus Mode, tables, font sizes, .DOCX reading, and auto-save.",
                openWord: "Open FreeWord",
                pptDesc: "Visual 16:9 presentation creator with visual color themes, extra text boxes, images, transitions, and fullscreen mode (F5).",
                openPpt: "Open FreePowerPoint",
                pdfDesc: "PDF viewer, editor, and annotator with drawing tools (freehand, line, arrow, rectangle, and translucent highlighter).",
                openPdf: "Open FreePDF",
                rights: "All rights reserved.",
                privacy: "Privacy",
                terms: "Terms",
                support: "Support"
            }
        };

        function toggleAppLanguage() {
            currentLang = currentLang === 'pt' ? 'en' : 'pt';
            localStorage.setItem('freepdf_lang', currentLang);
            applyLanguageTranslations();
        }

        function applyLanguageTranslations() {
            const dict = i18nDict[currentLang];
            const btn = document.getElementById('langToggleBtn');
            if (btn) {
                btn.innerHTML = currentLang === 'pt' ? '<span>🇧🇷</span> <span>PT</span>' : '<span>🇺🇸</span> <span>EN</span>';
            }

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (dict[key]) el.textContent = dict[key];
            });
        }

        document.addEventListener('DOMContentLoaded', applyLanguageTranslations);
    </script>
</body>
</html>
