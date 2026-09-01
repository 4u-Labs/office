<?php
// PROXY DE DOWNLOAD DO GOOGLE DRIVE PARA EVITAR BLOQUEIO DE CORS NO NAVEGADOR
if (isset($_GET['gdrive_id']) && !empty($_GET['gdrive_id'])) {
    $fileId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['gdrive_id']);
    $driveUrl = "https://docs.google.com/uc?export=download&id=" . $fileId;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $driveUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    $pdfContent = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($pdfContent && strlen($pdfContent) > 200) {
        header('Content-Type: application/pdf');
        header('Access-Control-Allow-Origin: *');
        echo $pdfContent;
        exit;
    } else {
        http_response_code(400);
        echo "Erro ao baixar arquivo do Google Drive.";
        exit;
    }
}

header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Free PDF — Studio de Edição e Assinatura Digital</title>
    <meta name="description" content="Free PDF Web App — Suíte oficial de edição, assinatura digital, marca d'água e manipulação nativa de PDF.">
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="manifest" href="manifest.json">
    <script>window.tailwind = { config: { corePlugins: { preflight: true } } };</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- PDF-Lib Engine & PDF.js da Mozilla -->
    <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <style>
        * { box-sizing: border-box !important; }
        body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #070a12; color: #e2e8f0; }
        .mono { font-family: 'JetBrains Mono', monospace; }
        .script-font { font-family: 'Dancing Script', cursive; }
        
        /* FREE PDF EXACT COLOR PALETTE & LAYOUT TOKENS */
        .stirling-sidebar { background: #0b0f19; border-right: 1px solid #1e293b; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .stirling-header { background: #0f172a; border-bottom: 1px solid #1e293b; }
        .stirling-right-panel { background: #0b0f19; border-left: 1px solid #1e293b; transition: all 0.3s; }
        
        .stirling-tool-btn {
            background: #1e293b/60;
            color: #cbd5e1;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255,255,255,0.05);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            white-space: nowrap;
        }
        .stirling-tool-btn:hover { background: #334155; color: #ffffff; border-color: rgba(255,255,255,0.15); transform: translateY(-1px); }
        .stirling-tool-btn.active { background: #2563eb; color: #ffffff; border-color: #3b82f6; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }

        .stirling-tab-btn {
            background: #1e293b; color: #94a3b8; border-radius: 6px; font-weight: 600; font-size: 11px; padding: 6px 12px; transition: all 0.2s;
        }
        .stirling-tab-btn.active { background: #2563eb; color: #ffffff; }

        /* Canvas & PDF Placement Layer */
        .pdf-page-container { position: relative; display: inline-block; margin-bottom: 24px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.7); border-radius: 4px; overflow: hidden; background: white; transition: filter 0.3s ease; }
        .pdf-text-layer { position: absolute; left: 0; top: 0; right: 0; bottom: 0; overflow: hidden; pointer-events: auto; }
        .pdf-drawing-canvas { position: absolute; left: 0; top: 0; right: 0; bottom: 0; z-index: 25; pointer-events: auto; cursor: crosshair; }

        .pdf-text-item {
            position: absolute;
            color: transparent;
            cursor: text;
            white-space: pre;
            transform-origin: 0% 0%;
            border-radius: 2px;
        }
        .pdf-text-item:hover { outline: 1px dashed #2563eb; background: rgba(37, 99, 235, 0.15); }
        
        /* EDICAO ATIVA (ENQUANTO DIGITA) */
        .pdf-text-item.editing {
            outline: 2px solid #2563eb !important;
            background: #ffffff !important;
            color: #000000 !important;
            z-index: 50 !important;
            padding: 0 4px !important;
            font-weight: bold !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
        }

        /* TEXTO EDITADO */
        .pdf-text-item.permanently-edited {
            color: transparent !important;
            background: transparent !important;
            z-index: 40 !important;
            outline: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* MARCA-TEXTO AMARELO 100% NITIDO */
        .pdf-text-item.highlighted {
            background: rgba(250, 204, 21, 0.45) !important;
            color: transparent !important;
            z-index: 45 !important;
            border-radius: 2px;
        }

        /* RESULTADO DA PESQUISA (SEARCH HIGHLIGHT) */
        .pdf-text-item.search-match {
            background: rgba(236, 72, 153, 0.6) !important;
            color: #ffffff !important;
            z-index: 48 !important;
            border-radius: 2px;
            animation: pulseSearch 1.5s infinite;
        }
        @keyframes pulseSearch {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* CAIXA DE TEXTO LIVRE INSERIDA NA PÁGINA (T+) */
        .pdf-custom-text-box {
            position: absolute;
            border: 1px dashed #2563eb;
            background: rgba(255, 255, 255, 0.95);
            color: #000000;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: move;
            z-index: 60;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            user-select: none;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .pdf-custom-text-box [contenteditable="true"] {
            outline: none;
            cursor: text;
            min-width: 60px;
            font-weight: bold;
        }

        /* MODO ESCURO NATIVO NO CANVAS DO PDF */
        .pdf-dark-mode-active .pdf-page-container canvas:not(.pdf-drawing-canvas) {
            filter: invert(0.92) hue-rotate(180deg) !important;
        }

        /* EXIBIÇÃO EM GRADE DE PÁGINAS PERFEITA (PAGE GRID THUMBNAILS) */
        .page-grid-mode #pdfPagesContainer {
            display: flex !important;
            flex-wrap: wrap !important;
            justify-content: center !important;
            gap: 24px !important;
            width: 100% !important;
            padding: 24px !important;
        }
        .page-grid-mode .pdf-page-container {
            width: 220px !important;
            height: 300px !important;
            max-width: 220px !important;
            max-height: 300px !important;
            margin-bottom: 0 !important;
            cursor: pointer !important;
            border: 2px solid #334155 !important;
            border-radius: 8px !important;
            transition: all 0.25s ease !important;
            position: relative !important;
            overflow: hidden !important;
            background: #ffffff !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
        }
        .page-grid-mode .pdf-page-container:hover {
            border-color: #2563eb !important;
            transform: translateY(-4px) scale(1.04) !important;
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.4) !important;
        }
        .page-grid-mode .pdf-page-container canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            pointer-events: none !important;
        }
        .page-grid-mode .pdf-page-container .pdf-text-layer {
            display: none !important;
        }

        /* Movable Signature Overlay Handle */
        .signature-overlay-box {
            position: absolute;
            border: 2px solid #3b82f6;
            background: transparent;
            cursor: move;
            z-index: 60;
            user-select: none;
        }
        .signature-handle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: #3b82f6;
            border-radius: 50%;
        }
        .handle-tl { top: -5px; left: -5px; cursor: nwse-resize; }
        .handle-tr { top: -5px; right: -5px; cursor: nesw-resize; }
        .handle-bl { bottom: -5px; left: -5px; cursor: nesw-resize; }
        .handle-br { bottom: -5px; right: -5px; cursor: nwse-resize; }
        
        .signature-action-bar {
            position: absolute;
            bottom: -36px;
            left: 50%;
            transform: translateX(-50%);
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 4px 8px;
            display: flex;
            gap: 8px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5);
        }

        .pan-grab { cursor: grab !important; }
        .pan-grabbing { cursor: grabbing !important; }

        /* RECOLHIMENTO DA BARRA LATERAL (SIDEBAR COLLAPSED) */
        .sidebar-collapsed { width: 4.5rem !important; }
        .sidebar-collapsed .sidebar-text { display: none !important; }
        .sidebar-collapsed .sidebar-full-only { display: none !important; }
        .sidebar-collapsed .sidebar-icon-only { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
        .sidebar-collapsed .sidebar-drawing-toolbar { flex-direction: column !important; gap: 6px !important; padding: 8px 4px !important; }

        /* BARRINHA DE EDIÇÃO LATERAL SOLICITADA */
        .sidebar-drawing-toolbar {
            background: #111827;
            border: 1px solid #374151;
            border-radius: 12px;
            padding: 4px 6px;
            display: flex;
            align-items: center;
            justify-content: space-around;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        .side-tool-btn {
            padding: 5px 7px;
            border-radius: 8px;
            color: #9ca3af;
            font-size: 13px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .side-tool-btn:hover { color: #ffffff; background: #1f2937; }
        .side-tool-btn.active { color: #ffffff; background: #2563eb; box-shadow: 0 2px 8px rgba(37,99,235,0.4); }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden select-none">

    <div class="flex-1 flex overflow-hidden">

        <!-- 1. LEFT SIDEBAR (DARK GLASS FREE PDF PANEL - RECOLHÍVEL + BARRINHA DE EDIÇÃO ANOTAÇÕES) -->
        <aside id="mainLeftSidebar" class="w-64 stirling-sidebar flex flex-col justify-between p-4 z-40 text-xs">
            <div class="space-y-4">
                <!-- Brand Header -->
                <div class="flex items-center gap-3 select-none py-1">
                    <button onclick="toggleLeftSidebar()" title="Recolher / Expandir Menu Lateral" class="text-gray-400 hover:text-white text-xl font-bold p-1 rounded-lg hover:bg-gray-800 transition-all">≡</button>
                    <div class="flex items-center gap-2.5 sidebar-text">
                        <div class="relative w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-500 p-0.5 shadow-lg shadow-blue-500/25 flex items-center justify-center">
                            <div class="w-full h-full bg-[#0b0f19] rounded-[10px] flex items-center justify-center relative overflow-hidden">
                                <div class="absolute -top-2 -right-2 w-5 h-5 bg-blue-500/30 rounded-full blur-xs"></div>
                                <svg class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-base tracking-tight text-white leading-none">Free <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">PDF</span></span>
                            <span class="text-[9px] text-blue-400/80 font-bold tracking-widest uppercase mt-0.5">STUDIO</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Items BILINGUE -->
                <div class="space-y-1">
                    <button onclick="toggleSearchInput()" title="Pesquisar" class="w-full flex items-center gap-2.5 px-3 py-2 text-gray-300 hover:bg-gray-800/80 rounded-xl text-left font-medium transition-all sidebar-icon-only">
                        <span class="text-base">🔍</span> <span class="sidebar-text" data-i18n="search">Pesquisar</span>
                    </button>
                    <label title="Abrir do computador" class="w-full flex items-center gap-2.5 px-3 py-2 text-gray-300 hover:bg-gray-800/80 rounded-xl text-left font-medium cursor-pointer transition-all sidebar-icon-only">
                        <span class="text-base">📂</span> <span class="sidebar-text" data-i18n="openComputer">Abrir do computador</span>
                        <input type="file" id="sidebarPdfInput" accept=".pdf,.txt" multiple class="hidden" onchange="handleNativePdfUpload(event)">
                    </label>
                    <button onclick="openGoogleDriveImportModal()" title="Google Drive" class="w-full flex items-center gap-2.5 px-3 py-2 text-gray-300 hover:bg-gray-800/80 rounded-xl text-left font-medium transition-all sidebar-icon-only">
                        <span class="text-base">☁️</span> <span class="sidebar-text">Google Drive</span>
                    </button>
                </div>

                <!-- BARRINHA DE EDIÇÃO LATERAL DA IMAGEM DO USUÁRIO (RABISCO, RETA, SETA, QUADRADO, MARCA-TEXTO TRANSPARENTE MULTIPLY, 4 CORES PRIMÁRIAS, DESFAZER) -->
                <div class="pt-2">
                    <div class="sidebar-drawing-toolbar relative">
                        <!-- 1. RABISCO LIVRE (PEN) -->
                        <button onclick="setSideDrawTool('draw-free')" id="sidetool-draw-free" class="side-tool-btn active" title="Rabisco Livre">✏️</button>
                        <!-- 2. RETA -->
                        <button onclick="setSideDrawTool('draw-line')" id="sidetool-draw-line" class="side-tool-btn" title="Reta">📏</button>
                        <!-- 3. SETA -->
                        <button onclick="setSideDrawTool('draw-arrow')" id="sidetool-draw-arrow" class="side-tool-btn" title="Seta">↗️</button>
                        <!-- 4. QUADRADO -->
                        <button onclick="setSideDrawTool('draw-box')" id="sidetool-draw-box" class="side-tool-btn" title="Quadrado / Retângulo">⬜</button>
                        <!-- 5. CANETA MARCA-TEXTO (TRANSPARÊNCIA E MESCLAGEM MULTIPLY REAL) -->
                        <button onclick="setSideDrawTool('draw-highlight')" id="sidetool-draw-highlight" class="side-tool-btn" title="Caneta Marca-Texto Transparente">🖍️</button>
                        
                        <!-- 6. SELETOR DE COR (POP-UP COM 4 CORES PRIMÁRIAS) -->
                        <div class="relative inline-block">
                            <button onclick="toggleSideColorPicker()" id="sideColorPickerBtn" class="side-tool-btn p-1" title="Escolher Cor">
                                <span id="sideActiveColorIndicator" class="w-3.5 h-3.5 rounded-full bg-red-500 inline-block border border-white/50"></span>
                            </button>
                            <div id="sideColorPickerMenu" class="hidden absolute top-full left-0 mt-2 w-32 bg-gray-900 border border-gray-700 rounded-xl p-2 shadow-2xl z-50 flex justify-between items-center">
                                <button onclick="setSideDrawColor('#ef4444')" class="w-5 h-5 rounded-full bg-red-500 border border-white/30 hover:scale-125 transition-transform" title="Vermelho"></button>
                                <button onclick="setSideDrawColor('#3b82f6')" class="w-5 h-5 rounded-full bg-blue-500 border border-white/30 hover:scale-125 transition-transform" title="Azul"></button>
                                <button onclick="setSideDrawColor('#22c55e')" class="w-5 h-5 rounded-full bg-green-500 border border-white/30 hover:scale-125 transition-transform" title="Verde"></button>
                                <button onclick="setSideDrawColor('#eab308')" class="w-5 h-5 rounded-full bg-yellow-500 border border-white/30 hover:scale-125 transition-transform" title="Amarelo"></button>
                            </div>
                        </div>

                        <!-- 7. BOTÃO DESFAZER (PASSO A PASSO HISTÓRICO DE DESENHOS) -->
                        <button onclick="undoSideDrawingStep()" class="side-tool-btn text-red-400 hover:text-red-300" title="Desfazer Anotação Passo a Passo">↩️</button>
                    </div>
                </div>

                <!-- Recent Files Section (MULTIDOCUMENTOS ATIVOS) -->
                <div class="pt-3 border-t border-gray-800/80 sidebar-full-only">
                    <div class="flex justify-between items-center mb-2.5 text-gray-400 font-bold tracking-wider text-[10px] uppercase">
                        <span data-i18n="activeFiles">ARQUIVOS ATIVOS</span>
                        <button onclick="document.getElementById('sidebarPdfInput').click()" class="hover:text-white font-extrabold text-sm" title="Adicionar documento">+</button>
                    </div>
                    <div id="sidebarFilesListContainer" class="space-y-2 max-h-56 overflow-y-auto pr-1">
                        <!-- Itens dinâmicos gerados via JS -->
                    </div>
                </div>
            </div>

            <!-- Rodapé da Barra Lateral -->
            <div class="pt-3 border-t border-gray-800/80 flex flex-col gap-2 text-xs text-gray-400">
                <!-- Links Institucionais Obrigatórios (Privacidade, Termos e Suporte) -->
                <div class="flex items-center justify-between text-[11px] text-gray-400 sidebar-text px-1">
                    <a href="privacidade.php" target="_blank" class="hover:text-blue-400 transition-colors">Privacidade</a>
                    <span>•</span>
                    <a href="termos.php" target="_blank" class="hover:text-blue-400 transition-colors">Termos</a>
                    <span>•</span>
                    <a href="suporte.php" target="_blank" class="hover:text-blue-400 transition-colors">Suporte</a>
                </div>
                <div class="flex items-center justify-between text-xs pt-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="font-semibold text-gray-300 sidebar-text">Free PDF v2.0</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- 2. MAIN CENTER WORKSPACE -->
        <main class="flex-1 flex flex-col overflow-hidden relative">

            <!-- Stirling Top Header & Canvas Toolbar -->
            <header class="stirling-header px-4 h-14 flex items-center justify-between text-xs select-none gap-3 z-30">
                
                <!-- Left Navigation, Language Switcher & Search Box -->
                <div class="flex items-center gap-2">
                    <a href="../index.php" class="px-3 py-1.5 bg-blue-600/20 hover:bg-blue-600 border border-blue-500/40 text-blue-400 hover:text-white font-bold text-xs rounded-lg shadow-md flex items-center gap-1.5 transition-all mr-2">
                        ← FreeOffice
                    </a>
                    <span class="text-gray-700 font-bold mr-1">|</span>
                    <button class="px-3.5 py-1.5 bg-blue-600 text-white font-bold rounded-lg shadow-sm" data-i18n="viewer">Visualizador</button>

                    <!-- SELETOR DE IDIOMA BILINGUË (PT / EN) -->
                    <button onclick="toggleAppLanguage()" id="langToggleBtn" class="px-2.5 py-1 bg-gray-900 border border-gray-700 hover:border-blue-500 text-white font-bold rounded-lg shadow-md flex items-center gap-1.5 transition-all" title="Alternar Idioma / Switch Language">
                        <span>🇧🇷</span> <span>PT</span>
                    </button>

                    <!-- BARRA DE PESQUISA REAL INTEGRADA (SEARCH) -->
                    <div id="topSearchContainer" class="hidden items-center bg-gray-900 border border-blue-500/60 rounded-lg px-2 py-1 gap-1.5 shadow-lg">
                        <span class="text-xs">🔍</span>
                        <input type="text" id="pdfSearchInput" oninput="searchInPdfDocument(this.value)" placeholder="Pesquisar texto no PDF..." data-i18n-ph="searchPlaceholder" class="bg-transparent text-white text-xs focus:outline-none w-44">
                        <button onclick="toggleSearchInput()" class="text-gray-400 hover:text-white text-xs font-bold px-1">✕</button>
                    </div>
                </div>

                <!-- Center Canvas Toolbar -->
                <div class="flex items-center gap-1 bg-gray-900/90 border border-gray-800 p-1 rounded-xl shadow-xl">
                    <button title="Pan Tool (Arraste o documento)" onclick="activateTool('pan')" id="tool-pan" class="stirling-tool-btn active">✋ <span data-i18n="hand">Hand</span></button>
                    <button title="Editar Texto Nativo" onclick="activateTool('edit')" id="tool-edit" class="stirling-tool-btn">✏️ <span data-i18n="edit">Edit</span></button>
                    <button title="Rotate Page" onclick="rotateCurrentPdfPage()" class="stirling-tool-btn">🔄 <span data-i18n="rotate">Rotate</span></button>
                    <button title="Highlight / Marca-Texto Amarelo" onclick="activateHighlightTool()" id="tool-highlight" class="stirling-tool-btn">🔲 <span data-i18n="highlight">Highlight</span></button>
                    <button title="Adicionar Nota Adesiva" onclick="addStickyNoteToPdf()" id="tool-note" class="stirling-tool-btn">📝 <span data-i18n="note">Note</span></button>
                    <button title="Inserir Carimbo (Personalizável)" onclick="addStampToPdf()" id="tool-stamp" class="stirling-tool-btn">✉️ <span data-i18n="stamp">Stamp</span></button>
                    
                    <span class="w-px h-5 bg-gray-800 mx-0.5"></span>

                    <!-- ASSINATURA DIGITAL (SIGN) -->
                    <button title="Assinatura Digital (Sign)" onclick="openRightSignPanel()" id="tool-sign" class="stirling-tool-btn bg-blue-600/20 text-blue-400 hover:bg-blue-600 hover:text-white border border-blue-500/30">
                        🖊️ <span data-i18n="sign">Sign</span>
                    </button>
                    
                    <!-- INSERIR TEXTO LIVRE (TEXT) -->
                    <button title="Inserir Texto Livre na Página (Text)" onclick="addCustomTextToPage()" id="tool-addtext" class="stirling-tool-btn bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600 hover:text-white border border-emerald-500/30">
                        T+ <span data-i18n="text">Text</span>
                    </button>
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-2.5">
                    <button title="Pesquisar no PDF" onclick="toggleSearchInput()" class="stirling-tool-btn">🔍 <span data-i18n="search">Search</span></button>
                    <button title="Print" onclick="window.print()" class="stirling-tool-btn">🖨️ <span data-i18n="print">Print</span></button>
                    <button title="Download PDF" onclick="downloadModifiedNativePdf()" class="text-white font-extrabold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 px-4 py-2 rounded-xl shadow-lg flex items-center gap-1.5 transition-all text-xs border border-blue-400/30">
                        📥 <span data-i18n="downloadPdf">Download PDF</span>
                    </button>
                </div>
            </header>

            <!-- Center Canvas View Area -->
            <div id="pdfWorkspaceCanvas" class="flex-1 bg-[#090d16] p-6 overflow-auto flex justify-center relative pan-grab">
                <div id="pdfViewerCanvasContainer" class="flex flex-col items-center w-full">
                    <!-- Default State or Rendered PDF Canvas -->
                    <div id="pdfPlaceholder" class="text-center py-24 text-gray-400 select-none">
                        <div class="text-6xl mb-4">📄</div>
                        <h2 class="text-2xl font-bold text-white mb-2" data-i18n="placeholderTitle">Free PDF Studio</h2>
                        <p class="text-sm text-gray-400 max-w-md mx-auto mb-6" data-i18n="placeholderDesc">Carregue um arquivo PDF para utilizar todas as ferramentas de assinatura digital, edição de texto e conversão!</p>
                        <label class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl cursor-pointer shadow-xl inline-block">
                            📂 <span data-i18n="openFromCompBtn">Open from computer</span>
                            <input type="file" accept=".pdf,.txt" multiple class="hidden" onchange="handleNativePdfUpload(event)">
                        </label>
                    </div>
                </div>

                <!-- Bottom Floating Control Bar -->
                <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-gray-900/90 border border-gray-800 px-4 py-2 rounded-xl shadow-2xl flex items-center gap-4 text-xs select-none z-40">
                    <div class="flex items-center gap-2 text-gray-400">
                        <button onclick="prevPdfPage()" class="hover:text-white font-bold">|&lt;</button>
                        <button onclick="prevPdfPage()" class="hover:text-white font-bold">&lt;</button>
                        <span id="pdfPageIndicator" class="font-bold text-white mx-1">1 / 1</span>
                        <button onclick="nextPdfPage()" class="hover:text-white font-bold">&gt;</button>
                        <button onclick="nextPdfPage()" class="hover:text-white font-bold">&gt;|</button>
                    </div>
                    <span class="text-gray-700">|</span>
                    <!-- BOTÃO 1: PAGE GRID (GRADE DE PÁGINAS) -->
                    <button title="Grade de Páginas (Page Grid)" onclick="togglePdfPageGrid()" id="btn-page-grid" class="text-gray-400 hover:text-white">📑</button>
                    <!-- BOTÃO 2: DARK MODE (MODO ESCURO DO DOCUMENTO) -->
                    <button title="Modo Escuro do Documento (Dark Mode)" onclick="togglePdfDarkMode()" id="btn-dark-mode" class="text-gray-400 hover:text-white">🌙</button>
                    <span class="text-gray-700">|</span>
                    <div class="flex items-center gap-2 text-gray-400">
                        <button onclick="zoomOutPdf()" class="hover:text-white font-bold">🔍-</button>
                        <span id="pdfZoomVal" class="font-bold text-white">67%</span>
                        <button onclick="zoomInPdf()" class="hover:text-white font-bold">🔍+</button>
                    </div>
                </div>
            </div>

        </main>

        <!-- 3. RIGHT SIDEBAR (SIGN & CONFIGURE SIGNATURE PANEL) -->
        <aside id="rightSignPanel" class="w-80 stirling-right-panel flex flex-col p-4 text-xs select-none">
            <!-- Header -->
            <div class="flex justify-between items-center pb-3 border-b border-gray-800 mb-4">
                <h3 class="font-extrabold text-sm text-white flex items-center gap-2">
                    <span>🖊️</span> <span data-i18n="signPanelTitle">Sign &amp; Assinatura Digital</span>
                </h3>
                <div class="flex items-center gap-2">
                    <button onclick="showToast('🔍 Buscar assinaturas')" class="text-gray-400 hover:text-white">🔍</button>
                    <button onclick="closeRightSignPanel()" class="text-gray-400 hover:text-white">✕</button>
                </div>
            </div>

            <!-- Step 1: Files -->
            <div class="mb-5 space-y-2">
                <div class="font-bold text-gray-300 text-xs flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-gray-800 flex items-center justify-center text-[10px] text-gray-400">1</span>
                    <span>Files</span>
                </div>
                <div id="signFileList" class="p-2.5 rounded-lg bg-gray-900 border border-gray-800 text-gray-300 font-mono text-[11px] truncate flex items-center gap-1.5">
                    <span class="text-green-400 font-bold">✓</span>
                    <span id="signFileNameLabel" class="truncate">Nenhum arquivo ativo</span>
                </div>
            </div>

            <!-- Step 2: Configure Signature -->
            <div class="mb-5 space-y-3">
                <div class="font-bold text-gray-300 text-xs flex items-center gap-1.5">
                    <span class="w-4 h-4 rounded-full bg-gray-800 flex items-center justify-center text-[10px] text-gray-400">2</span>
                    <span data-i18n="configureSignature">Configure Signature</span>
                </div>
                <p class="text-[11px] text-gray-400" data-i18n="chooseSigType">Choose how you want to create the signature</p>

                <!-- Signature Tabs (Canvas, Image, Text, Saved) -->
                <div class="flex gap-1 bg-gray-950 p-1 rounded-lg border border-gray-800">
                    <button onclick="switchSigTab('canvas')" id="sigtab-canvas" class="stirling-tab-btn active flex-1">Canvas</button>
                    <button onclick="switchSigTab('image')" id="sigtab-image" class="stirling-tab-btn flex-1">Image</button>
                    <button onclick="switchSigTab('text')" id="sigtab-text" class="stirling-tab-btn flex-1">Text</button>
                    <button onclick="switchSigTab('saved')" id="sigtab-saved" class="stirling-tab-btn flex-1" data-i18n="savedSigs">Saved</button>
                </div>

                <!-- TAB 1: Canvas Drawing Box -->
                <div id="sigbody-canvas" class="space-y-2">
                    <div class="text-[11px] font-semibold text-gray-300" data-i18n="drawSig">Draw your signature</div>
                    <div class="bg-white rounded-xl p-2 border border-gray-700 relative flex flex-col items-center">
                        <canvas id="signatureCanvas" width="260" height="110" class="cursor-crosshair bg-white w-full h-[110px]"></canvas>
                        <span class="text-[10px] text-gray-400 absolute bottom-1 pointer-events-none">Click to open the drawing canvas</span>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <button onclick="clearSignatureCanvas()" class="text-gray-400 hover:text-white text-[11px]" data-i18n="clearCanvas">Clear Canvas</button>
                        <button onclick="saveAndPlaceSignature()" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-md flex items-center gap-1">
                            <span>👤</span> <span data-i18n="saveSig">Save Signature</span>
                        </button>
                    </div>
                </div>

                <!-- TAB 2: Image File Upload -->
                <div id="sigbody-image" class="space-y-3 hidden">
                    <div class="text-[11px] font-semibold text-gray-300" data-i18n="uploadSigImg">Upload signature image (.png / .jpg)</div>
                    <label class="w-full py-8 border-2 border-dashed border-gray-700 hover:border-blue-500 rounded-xl bg-gray-900 flex flex-col items-center justify-center cursor-pointer">
                        <span class="text-2xl mb-1">🖼️</span>
                        <span class="text-xs text-gray-300 font-bold">Escolher Imagem de Assinatura</span>
                        <span class="text-[10px] text-gray-500">PNG transparente recomendado</span>
                        <input type="file" accept="image/*" class="hidden" onchange="handleSignatureImageUpload(event)">
                    </label>
                </div>

                <!-- TAB 3: Text Script Signature Generator -->
                <div id="sigbody-text" class="space-y-3 hidden">
                    <div class="text-[11px] font-semibold text-gray-300" data-i18n="typeYourName">Digite seu nome para assinar</div>
                    <input type="text" id="sigTextInput" oninput="renderTextSignatureOnCanvas(this.value)" placeholder="Digite seu nome (ex: Assinatura)..." class="w-full bg-gray-950 border border-gray-800 rounded-lg p-2 text-xs text-white focus:border-blue-500 focus:outline-none">
                    <div class="bg-white rounded-xl p-3 text-center border border-gray-700 min-h-[70px] flex items-center justify-center">
                        <span id="sigTextPreview" class="script-font text-3xl text-black">Assinatura</span>
                    </div>
                    <button onclick="saveTextSignatureToCanvas()" class="w-full py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg shadow-md">
                        Usar Assinatura Digitada
                    </button>
                </div>

                <!-- TAB 4: Saved Signatures List -->
                <div id="sigbody-saved" class="space-y-3 hidden">
                    <div class="text-[11px] font-semibold text-gray-300" data-i18n="savedSigs">Assinaturas Salvas no Navegador</div>
                    <div id="savedSignaturesList" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        <!-- Lista dinâmica via JS -->
                    </div>
                </div>
            </div>

            <!-- Step 3: Place & save -->
            <div class="space-y-3 pt-3 border-t border-gray-800">
                <div class="font-bold text-gray-300 text-xs" data-i18n="placeAndSave">Place &amp; save</div>
                <p class="text-[11px] text-gray-400" data-i18n="positionSig">Position the signature on your PDF</p>
                
                <div class="flex gap-2">
                    <button onclick="undoPdfAction()" class="flex-1 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-xs font-semibold flex items-center justify-center gap-1">
                        <span>↩️</span> <span data-i18n="undo">Undo</span>
                    </button>
                    <button onclick="redoPdfAction()" class="flex-1 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-xs font-semibold flex items-center justify-center gap-1">
                        <span>↪️</span> <span data-i18n="redo">Redo</span>
                    </button>
                </div>

                <button onclick="saveAndPlaceSignature()" class="w-full py-2 bg-blue-900/60 border border-blue-700 text-blue-200 font-bold rounded-lg text-xs flex items-center justify-center gap-2">
                    <span>▶️</span> <span data-i18n="resumePlacement">Resume placement</span>
                </button>

                <div id="placementStatusBadge" class="w-full py-2 bg-emerald-950/80 border border-emerald-700 text-emerald-300 font-bold rounded-lg text-xs text-center">
                    Placement paused
                </div>
            </div>
        </aside>

    </div>

    <!-- MODAL DO GOOGLE DRIVE FUNCIONAL -->
    <div id="gdriveImportModal" class="fixed inset-0 bg-black/75 hidden items-center justify-center z-50 p-4 select-text">
        <div class="bg-gray-900 border border-gray-700 rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
            <div class="flex justify-between items-center border-b border-gray-800 pb-3">
                <h3 class="font-bold text-base text-white flex items-center gap-2">
                    <span>☁️</span> <span data-i18n="gdriveTitle">Importar PDF do Google Drive</span>
                </h3>
                <button onclick="closeGoogleDriveImportModal()" class="text-gray-400 hover:text-white font-bold">✕</button>
            </div>
            
            <p class="text-xs text-gray-400" data-i18n="gdriveDesc">Cole o link público de compartilhamento do arquivo PDF no Google Drive:</p>

            <div>
                <input type="text" id="gdriveUrlInput" placeholder="https://drive.google.com/file/d/..." class="w-full bg-gray-950 border border-gray-800 rounded-xl p-3 text-xs text-white focus:border-blue-500 focus:outline-none">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button onclick="closeGoogleDriveImportModal()" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-xl text-xs font-bold" data-i18n="cancel">Cancelar</button>
                <button onclick="importPdfFromGoogleDriveUrl()" class="px-5 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold shadow-md" data-i18n="loadFromDrive">Carregar do Drive</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="officeToast" class="fixed bottom-12 right-6 bg-gray-900 border border-blue-500 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-2xl hidden z-50 flex items-center gap-2">
        <span id="officeToastMsg">Operação concluída</span>
    </div>

    <!-- Core Free PDF Engine Script -->
    <script>
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        }

        /* RECOLHER / EXPANDIR MENU LATERAL (SIDEBAR COLLAPSE TOGGLE) */
        function toggleLeftSidebar() {
            const sidebar = document.getElementById('mainLeftSidebar');
            if (sidebar) {
                sidebar.classList.toggle('sidebar-collapsed');
                const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                showToast(isCollapsed ? '◀️ Menu lateral recolhido!' : '▶️ Menu lateral expandido!');
            }
        }

        /* LÓGICA DA BARRINHA DE EDIÇÃO LATERAL SOLICITADA */
        let sideActiveDrawTool = 'draw-free'; // Default: Rabisco Livre
        let sideActiveDrawColor = '#ef4444'; // Default: Vermelho
        let sideDrawHistoryStack = {}; // Armazena o historico de traços por pagina para desfazer passo a passo

        function setSideDrawTool(tool) {
            sideActiveDrawTool = tool;
            activeTool = tool;
            document.querySelectorAll('.side-tool-btn').forEach(b => b.classList.remove('active'));
            const activeBtn = document.getElementById('sidetool-' + tool);
            if (activeBtn) activeBtn.classList.add('active');

            const toolNames = {
                'draw-free': 'Rabisco Livre ✏️',
                'draw-line': 'Reta 📏',
                'draw-arrow': 'Seta ↗️',
                'draw-box': 'Quadrado ⬜',
                'draw-highlight': 'Caneta Marca-Texto Transparente 🖍️'
            };
            showToast(`Ferramenta ativa: ${toolNames[tool] || tool}`);
        }

        function toggleSideColorPicker() {
            const menu = document.getElementById('sideColorPickerMenu');
            menu.classList.toggle('hidden');
        }

        function setSideDrawColor(color) {
            sideActiveDrawColor = color;
            document.getElementById('sideActiveColorIndicator').style.backgroundColor = color;
            document.getElementById('sideColorPickerMenu').classList.add('hidden');
            showToast('🎨 Cor selecionada!');
        }

        function undoSideDrawingStep() {
            const currentCanvases = document.querySelectorAll('.pdf-drawing-canvas');
            let undone = false;

            currentCanvases.forEach(c => {
                const pageNum = c.getAttribute('data-page-num');
                if (sideDrawHistoryStack[pageNum] && sideDrawHistoryStack[pageNum].length > 0) {
                    const ctx = c.getContext('2d');
                    sideDrawHistoryStack[pageNum].pop(); // Remove o ultimo traço

                    ctx.clearRect(0, 0, c.width, c.height);
                    if (sideDrawHistoryStack[pageNum].length > 0) {
                        const lastState = sideDrawHistoryStack[pageNum][sideDrawHistoryStack[pageNum].length - 1];
                        ctx.putImageData(lastState, 0, 0);
                    }
                    undone = true;
                }
            });

            if (undone) {
                showToast('↩️ Último traço desfeito passo a passo!');
            } else {
                showToast('Nenhum traço para desfazer nesta página.');
            }
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#sideColorPickerBtn') && !e.target.closest('#sideColorPickerMenu')) {
                const menu = document.getElementById('sideColorPickerMenu');
                if (menu) menu.classList.add('hidden');
            }
        });

        /* DICIONÁRIO E MOTOR DE IDIOMAS BILINGUE (PT / EN) */
        let currentLang = localStorage.getItem('freepdf_lang') || 'pt';

        const i18nDict = {
            pt: {
                viewer: "Visualizador",
                search: "Pesquisar",
                openComputer: "Abrir do computador",
                activeFiles: "ARQUIVOS ATIVOS",
                hand: "Mão",
                edit: "Editar",
                rotate: "Girar",
                highlight: "Marca-Texto",
                note: "Nota",
                stamp: "Carimbo",
                sign: "Assinar",
                text: "Texto",
                print: "Imprimir",
                downloadPdf: "Download PDF",
                placeholderTitle: "Free PDF Studio",
                placeholderDesc: "Carregue um arquivo PDF para utilizar todas as ferramentas de assinatura digital, edição de texto e conversão!",
                openFromCompBtn: "Abrir do computador",
                searchPlaceholder: "Pesquisar texto no PDF...",
                gdriveTitle: "Importar PDF do Google Drive",
                gdriveDesc: "Cole o link público de compartilhamento do arquivo PDF no Google Drive:",
                cancel: "Cancelar",
                loadFromDrive: "Carregar do Drive",
                signPanelTitle: "Assinatura Digital",
                configureSignature: "Configurar Assinatura",
                chooseSigType: "Escolha como deseja criar a assinatura",
                drawSig: "Desenhe sua assinatura",
                clearCanvas: "Limpar",
                saveSig: "Salvar Assinatura",
                uploadSigImg: "Carregar imagem (.png / .jpg)",
                typeYourName: "Digite seu nome para assinar",
                savedSigs: "Assinaturas Salvas",
                placeAndSave: "Posicionar & Salvar",
                positionSig: "Posicione a assinatura no PDF",
                undo: "Desfazer",
                redo: "Refazer",
                resumePlacement: "Continuar Posicionamento"
            },
            en: {
                viewer: "Viewer",
                search: "Search",
                openComputer: "Open from computer",
                activeFiles: "ACTIVE FILES",
                hand: "Hand",
                edit: "Edit",
                rotate: "Rotate",
                highlight: "Highlight",
                note: "Note",
                stamp: "Stamp",
                sign: "Sign",
                text: "Text",
                print: "Print",
                downloadPdf: "Download PDF",
                placeholderTitle: "Free PDF Studio",
                placeholderDesc: "Upload a PDF file to use all digital signature, text editing and conversion tools!",
                openFromCompBtn: "Open from computer",
                searchPlaceholder: "Search text in PDF...",
                gdriveTitle: "Import PDF from Google Drive",
                gdriveDesc: "Paste the public sharing link of the PDF file from Google Drive:",
                cancel: "Cancel",
                loadFromDrive: "Load from Drive",
                signPanelTitle: "Digital Signature",
                configureSignature: "Configure Signature",
                chooseSigType: "Choose how you want to create the signature",
                drawSig: "Draw your signature",
                clearCanvas: "Clear Canvas",
                saveSig: "Save Signature",
                uploadSigImg: "Upload image (.png / .jpg)",
                typeYourName: "Type your name to sign",
                savedSigs: "Saved Signatures",
                placeAndSave: "Place & Save",
                positionSig: "Position the signature on your PDF",
                undo: "Undo",
                redo: "Redo",
                resumePlacement: "Resume Placement"
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

            document.querySelectorAll('[data-i18n-ph]').forEach(el => {
                const key = el.getAttribute('data-i18n-ph');
                if (dict[key]) el.placeholder = dict[key];
            });

            showToast(currentLang === 'pt' ? '🇧🇷 Idioma alterado para Português!' : '🇺🇸 Language changed to English!');
        }

        document.addEventListener('DOMContentLoaded', applyLanguageTranslations);

        /* GERENCIADOR DE MÚLTIPLOS DOCUMENTOS */
        let uploadedFilesList = [];
        let activeFileIndex = -1;

        let currentPdfBytes = null;
        let loadedPdfDoc = null;
        let pdfEditedTextItems = [];
        let pdfUndoStack = [];
        let pdfRedoStack = [];

        let currentScale = 1.4;
        let pdfPageNum = 1;
        let pdfTotalPages = 1;
        let pdfRotation = 0;
        let activeTool = 'pan';

        /* MODO ESCURO E GRADE DE PÁGINAS STATES */
        let isDarkModeActive = false;
        let isPageGridActive = false;

        /* PAN / HAND DRAG TOOL LOGIC */
        const workspace = document.getElementById('pdfWorkspaceCanvas');
        let isPanMouseDown = false;
        let panStartX, panStartY, scrollLeftStart, scrollTopStart;

        workspace.addEventListener('mousedown', (e) => {
            if (activeTool !== 'pan') return;
            if (e.target.closest('.pdf-text-item') || e.target.closest('.signature-overlay-box') || e.target.closest('.pdf-custom-text-box')) return;
            isPanMouseDown = true;
            workspace.classList.remove('pan-grab');
            workspace.classList.add('pan-grabbing');
            panStartX = e.pageX - workspace.offsetLeft;
            panStartY = e.pageY - workspace.offsetTop;
            scrollLeftStart = workspace.scrollLeft;
            scrollTopStart = workspace.scrollTop;
        });

        workspace.addEventListener('mouseleave', () => {
            isPanMouseDown = false;
            workspace.classList.remove('pan-grabbing');
            workspace.classList.add('pan-grab');
        });

        workspace.addEventListener('mouseup', () => {
            isPanMouseDown = false;
            workspace.classList.remove('pan-grabbing');
            workspace.classList.add('pan-grab');
        });

        workspace.addEventListener('mousemove', (e) => {
            if (!isPanMouseDown || activeTool !== 'pan') return;
            e.preventDefault();
            const x = e.pageX - workspace.offsetLeft;
            const y = e.pageY - workspace.offsetTop;
            const walkX = (x - panStartX) * 1.5;
            const walkY = (y - panStartY) * 1.5;
            workspace.scrollLeft = scrollLeftStart - walkX;
            workspace.scrollTop = scrollTopStart - walkY;
        });

        /* FUNÇÃO DE PESQUISA REAL NO PDF (SEARCH FUNCIONAL) */
        function toggleSearchInput() {
            const container = document.getElementById('topSearchContainer');
            container.classList.toggle('hidden');
            container.classList.toggle('flex');
            if (!container.classList.contains('hidden')) {
                document.getElementById('pdfSearchInput').focus();
            } else {
                clearSearchMatches();
            }
        }

        function searchInPdfDocument(query) {
            clearSearchMatches();
            if (!query || query.trim().length < 2) return;

            const q = query.toLowerCase().trim();
            const items = document.querySelectorAll('.pdf-text-item');
            let matchCount = 0;

            items.forEach(item => {
                if (item.textContent.toLowerCase().includes(q)) {
                    item.classList.add('search-match');
                    matchCount++;
                }
            });

            if (matchCount > 0) {
                showToast(`🔍 ${matchCount} ocorrência(s) encontrada(s) para "${query}"!`);
            } else {
                showToast(`🔍 Nenhuma ocorrência encontrada para "${query}".`);
            }
        }

        function clearSearchMatches() {
            document.querySelectorAll('.pdf-text-item.search-match').forEach(el => {
                el.classList.remove('search-match');
            });
        }

        /* FUNÇÃO GOOGLE DRIVE VIA SERVIDOR PHP PROXY (100% SEM BLOQUEIO DE CORS!) */
        function openGoogleDriveImportModal() {
            document.getElementById('gdriveImportModal').classList.remove('hidden');
        }

        function closeGoogleDriveImportModal() {
            document.getElementById('gdriveImportModal').classList.add('hidden');
        }

        async function importPdfFromGoogleDriveUrl() {
            const urlInput = document.getElementById('gdriveUrlInput').value.trim();
            if (!urlInput) { alert('Insira um link do Google Drive.'); return; }

            const match = urlInput.match(/\/d\/([a-zA-Z0-9_-]+)/);
            if (!match || !match[1]) {
                alert('Formato de link inválido. Copie o link de compartilhamento do arquivo no Google Drive (ex: https://drive.google.com/file/d/ID...)');
                return;
            }

            const fileId = match[1];

            try {
                showToast('☁️ Baixando PDF do Google Drive via Proxy seguro...');
                
                const res = await fetch(`index.php?gdrive_id=${fileId}`);
                if (!res.ok) {
                    throw new Error('Falha no download via Proxy');
                }

                const arrayBuf = await res.arrayBuffer();

                if (!arrayBuf || arrayBuf.byteLength < 500) {
                    throw new Error('Arquivo retornado não é um PDF válido.');
                }

                uploadedFilesList.push({
                    name: `GoogleDrive_${fileId.substring(0, 6)}.pdf`,
                    date: 'Today',
                    bytes: arrayBuf
                });

                activeFileIndex = uploadedFilesList.length - 1;
                renderFilesSidebarList();
                switchActiveDocument(activeFileIndex);
                closeGoogleDriveImportModal();
                showToast('☁️ Documento do Google Drive carregado com sucesso!');
            } catch (err) {
                console.error('Erro ao baixar do Drive:', err);
                alert('Erro ao carregar o PDF do Google Drive. Verifique se a permissão do link está como "Qualquer pessoa com o link".');
            }
        }

        /* MODO ESCURO NATIVO DO DOCUMENTO (DARK MODE) */
        function togglePdfDarkMode() {
            isDarkModeActive = !isDarkModeActive;
            const btn = document.getElementById('btn-dark-mode');
            if (isDarkModeActive) {
                document.body.classList.add('pdf-dark-mode-active');
                btn.classList.add('text-yellow-400');
                showToast('🌙 Modo Escuro do Documento Ativado!');
            } else {
                document.body.classList.remove('pdf-dark-mode-active');
                btn.classList.remove('text-yellow-400');
                showToast('☀️ Modo Claro do Documento Ativado!');
            }
        }

        /* GRADE DE PÁGINAS / THUMBNAILS (PAGE GRID) */
        function togglePdfPageGrid() {
            isPageGridActive = !isPageGridActive;
            const btn = document.getElementById('btn-page-grid');

            if (isPageGridActive) {
                workspace.classList.add('page-grid-mode');
                btn.classList.add('text-blue-400');
                showToast('📑 Exibição em Grade de Páginas Ativada! Clique em uma página para ampliar.');
            } else {
                workspace.classList.remove('page-grid-mode');
                btn.classList.remove('text-blue-400');
                showToast('📄 Exibição Normal de Páginas Ativada!');
            }
        }

        /* Signature Canvas Drawing State */
        let sigCanvas = document.getElementById('signatureCanvas');
        let sigCtx = sigCanvas.getContext('2d');
        let isDrawing = false;
        let signatureImageDataUrl = null;

        function showToast(msg) {
            const toast = document.getElementById('officeToast');
            document.getElementById('officeToastMsg').textContent = msg;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }

        function activateTool(tool) {
            activeTool = tool;
            document.querySelectorAll('.stirling-tool-btn').forEach(b => b.classList.remove('active'));
            const btn = document.getElementById('tool-' + tool);
            if (btn) btn.classList.add('active');

            if (tool === 'pan') {
                workspace.classList.add('pan-grab');
            } else {
                workspace.classList.remove('pan-grab', 'pan-grabbing');
            }

            showToast(`Ferramenta selecionada: ${tool.toUpperCase()}`);
        }

        function switchSigTab(tab) {
            document.querySelectorAll('.stirling-tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('sigtab-' + tab).classList.add('active');

            document.getElementById('sigbody-canvas').classList.add('hidden');
            document.getElementById('sigbody-image').classList.add('hidden');
            document.getElementById('sigbody-text').classList.add('hidden');
            document.getElementById('sigbody-saved').classList.add('hidden');

            document.getElementById('sigbody-' + tab).classList.remove('hidden');

            if (tab === 'saved') {
                renderSavedSignaturesTab();
            }
        }

        /* CARIMBO PERSONALIZÁVEL E EDITÁVEL */
        function addStampToPdf() {
            activateTool('stamp');
            const targetContainer = document.querySelector('.pdf-page-container');
            if (!targetContainer) { alert('Carregue um PDF primeiro!'); return; }

            const customText = prompt('Digite o texto do carimbo (ex: APROVADO, PAGO, CONFIDENCIAL, CANCELADO):', 'APROVADO');
            if (!customText) return;

            const stampDiv = document.createElement('div');
            stampDiv.className = 'absolute border-4 border-green-600 text-green-600 font-extrabold px-4 py-2 text-xl rounded-lg transform -rotate-12 z-50 cursor-move select-none shadow-xl';
            stampDiv.style.left = '250px';
            stampDiv.style.top = '250px';
            stampDiv.innerHTML = `
                <span class="stamp-text" onclick="editStampText(this)" title="Clique para alterar o texto do carimbo">${customText.toUpperCase()}</span>
                <button onclick="this.parentElement.remove()" class="absolute -top-3 -right-3 w-5 h-5 bg-red-600 text-white rounded-full text-xs flex items-center justify-center font-bold">✕</button>
            `;
            targetContainer.appendChild(stampDiv);
            makeElementDraggable(stampDiv);
            showToast(`✉️ Carimbo "${customText.toUpperCase()}" inserido no PDF!`);
        }

        function editStampText(el) {
            const newTxt = prompt('Alterar texto do carimbo:', el.textContent);
            if (newTxt) el.textContent = newTxt.toUpperCase();
        }

        function handleSignatureImageUpload(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(evt) {
                signatureImageDataUrl = evt.target.result;
                saveAndPlaceSignature();
            };
            reader.readAsDataURL(file);
        }

        function renderTextSignatureOnCanvas(val) {
            document.getElementById('sigTextPreview').textContent = val || 'Assinatura';
        }

        function saveTextSignatureToCanvas() {
            const val = document.getElementById('sigTextInput').value.trim() || 'Assinatura';
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = 300;
            tempCanvas.height = 100;
            const ctx = tempCanvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, 300, 100);
            ctx.font = '36px "Dancing Script", cursive';
            ctx.fillStyle = '#000000';
            ctx.fillText(val, 20, 60);

            signatureImageDataUrl = tempCanvas.toDataURL('image/png');
            saveAndPlaceSignature();
        }

        /* GRAVAÇÃO PERMANENTE EM LOCALSTORAGE (TAB SAVED) */
        function renderSavedSignaturesTab() {
            const container = document.getElementById('savedSignaturesList');
            const saved = JSON.parse(localStorage.getItem('freepdf_saved_signatures') || '[]');

            if (saved.length === 0) {
                container.innerHTML = `
                    <div class="p-3 bg-gray-900 rounded-xl border border-gray-800 text-center text-xs text-gray-400">
                        Nenhuma assinatura salva ainda. Desenhe uma e clique em Save!
                    </div>
                `;
                return;
            }

            container.innerHTML = saved.map((dataUrl, idx) => `
                <div class="p-2 bg-gray-900 border border-gray-800 rounded-xl flex items-center justify-between gap-2">
                    <img src="${dataUrl}" class="h-10 bg-white rounded border border-gray-300 object-contain p-1">
                    <div class="flex items-center gap-1">
                        <button onclick="useSavedSignature(${idx})" class="px-2.5 py-1 bg-blue-600 text-white font-bold rounded text-xs">Usar</button>
                        <button onclick="deleteSavedSignature(${idx})" class="px-2 py-1 bg-red-900/80 text-red-200 font-bold rounded text-xs">✕</button>
                    </div>
                </div>
            `).join('');
        }

        function useSavedSignature(idx) {
            const saved = JSON.parse(localStorage.getItem('freepdf_saved_signatures') || '[]');
            if (saved[idx]) {
                signatureImageDataUrl = saved[idx];
                saveAndPlaceSignature();
            }
        }

        function deleteSavedSignature(idx) {
            let saved = JSON.parse(localStorage.getItem('freepdf_saved_signatures') || '[]');
            saved.splice(idx, 1);
            localStorage.setItem('freepdf_saved_signatures', JSON.stringify(saved));
            renderSavedSignaturesTab();
        }

        /* Undo & Redo System */
        function undoPdfAction() {
            if (pdfEditedTextItems.length === 0) {
                showToast('Nenhuma alteração para desfazer.');
                return;
            }
            const last = pdfEditedTextItems.pop();
            pdfRedoStack.push(last);
            renderPdfWorkspace();
            showToast('↩️ Ação desfeita!');
        }

        function redoPdfAction() {
            if (pdfRedoStack.length === 0) {
                showToast('Nenhuma alteração para refazer.');
                return;
            }
            const last = pdfRedoStack.pop();
            pdfEditedTextItems.push(last);
            renderPdfWorkspace();
            showToast('↪️ Ação refeita!');
        }

        /* FERRAMENTA MARCA-TEXTO AMARELO REAL */
        function activateHighlightTool() {
            activateTool('highlight');
            showToast('🔲 Modo Marca-Texto Amarelo! Clique em qualquer palavra/linha no PDF para destacar com nitidez 100%.');
        }

        function addStickyNoteToPdf() {
            activateTool('note');
            const targetContainer = document.querySelector('.pdf-page-container');
            if (!targetContainer) { alert('Carregue um PDF primeiro!'); return; }

            const noteText = prompt('Digite o texto da nota adesiva:');
            if (!noteText) return;

            const noteDiv = document.createElement('div');
            noteDiv.className = 'absolute bg-yellow-200 text-yellow-900 border border-yellow-400 p-2 rounded shadow-xl text-xs z-50 cursor-move';
            noteDiv.style.left = '150px';
            noteDiv.style.top = '150px';
            noteDiv.style.width = '160px';
            noteDiv.innerHTML = `
                <div class="flex justify-between items-center font-bold mb-1 border-b border-yellow-300 pb-1">
                    <span>📝 Nota</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 font-bold">✕</button>
                </div>
                <div>${noteText}</div>
            `;
            targetContainer.appendChild(noteDiv);
            makeElementDraggable(noteDiv);
            showToast('📝 Nota adesiva adicionada ao PDF!');
        }

        /* BOTÃO 1 CORRIGIDO: ABRE O PAINEL LATERAL DE ASSINATURA */
        function openRightSignPanel() {
            activateTool('sign');
            document.getElementById('rightSignPanel').classList.remove('hidden');
            showToast('🖊️ Painel de Assinatura Digital aberto à direita!');
        }

        function closeRightSignPanel() {
            document.getElementById('rightSignPanel').classList.add('hidden');
        }

        /* BOTÃO 2 CORRIGIDO: INSERIR CAIXA DE TEXTO LIVRE DRAGGABLE E EDITÁVEL */
        function addCustomTextToPage() {
            activateTool('addtext');
            const targetContainer = document.querySelector('.pdf-page-container');
            if (!targetContainer) { alert('Carregue um PDF primeiro!'); return; }

            const textDiv = document.createElement('div');
            textDiv.className = 'pdf-custom-text-box';
            textDiv.style.left = '180px';
            textDiv.style.top = '180px';
            textDiv.innerHTML = `
                <span contenteditable="true" class="text-base text-black font-bold focus:outline-none">Novo Texto Clique Aqui</span>
                <button onclick="this.parentElement.remove()" title="Deletar Caixa" class="w-4 h-4 rounded-full bg-red-600 text-white font-bold flex items-center justify-center text-[10px] ml-1">✕</button>
            `;
            targetContainer.appendChild(textDiv);
            makeElementDraggable(textDiv);

            const editSpan = textDiv.querySelector('[contenteditable="true"]');
            if (editSpan) {
                editSpan.focus();
                document.execCommand('selectAll', false, null);
            }

            showToast('✍️ Caixa de Texto adicionada! Arraste e digite o texto desejado.');
        }

        /* Signature Canvas Drawing Handlers */
        sigCtx.lineWidth = 2.5;
        sigCtx.strokeStyle = '#000000';
        sigCtx.lineCap = 'round';

        sigCanvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            sigCtx.beginPath();
            const rect = sigCanvas.getBoundingClientRect();
            sigCtx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
        });

        sigCanvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const rect = sigCanvas.getBoundingClientRect();
            sigCtx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
            sigCtx.stroke();
        });

        sigCanvas.addEventListener('mouseup', () => { isDrawing = false; });
        sigCanvas.addEventListener('mouseleave', () => { isDrawing = false; });

        function clearSignatureCanvas() {
            sigCtx.clearRect(0, 0, sigCanvas.width, sigCanvas.height);
            signatureImageDataUrl = null;
        }

        /* Saves Signature and places movable overlay directly on PDF Page */
        function saveAndPlaceSignature() {
            if (!signatureImageDataUrl) {
                signatureImageDataUrl = sigCanvas.toDataURL('image/png');
            }
            
            let saved = JSON.parse(localStorage.getItem('freepdf_saved_signatures') || '[]');
            if (!saved.includes(signatureImageDataUrl)) {
                saved.push(signatureImageDataUrl);
                localStorage.setItem('freepdf_saved_signatures', JSON.stringify(saved));
            }

            const targetContainer = document.querySelector('.pdf-page-container');
            if (!targetContainer) {
                alert('Carregue um arquivo PDF primeiro!');
                return;
            }

            const oldBox = document.getElementById('activeSignatureOverlay');
            if (oldBox) oldBox.remove();

            const sigBox = document.createElement('div');
            sigBox.id = 'activeSignatureOverlay';
            sigBox.className = 'signature-overlay-box';
            sigBox.style.left = '200px';
            sigBox.style.top = '400px';
            sigBox.style.width = '240px';
            sigBox.style.height = '100px';

            const img = document.createElement('img');
            img.src = signatureImageDataUrl;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.pointerEvents = 'none';

            const actionBar = document.createElement('div');
            actionBar.className = 'signature-action-bar';
            actionBar.innerHTML = `
                <button onclick="document.getElementById('activeSignatureOverlay').remove()" title="Deletar" class="text-red-400 text-xs px-1">🗑️</button>
            `;

            const handleTL = document.createElement('div'); handleTL.className = 'signature-handle handle-tl';
            const handleTR = document.createElement('div'); handleTR.className = 'signature-handle handle-tr';
            const handleBL = document.createElement('div'); handleBL.className = 'signature-handle handle-bl';
            const handleBR = document.createElement('div'); handleBR.className = 'signature-handle handle-br';

            sigBox.appendChild(img);
            sigBox.appendChild(actionBar);
            sigBox.appendChild(handleTL);
            sigBox.appendChild(handleTR);
            sigBox.appendChild(handleBL);
            sigBox.appendChild(handleBR);

            targetContainer.appendChild(sigBox);
            makeElementDraggable(sigBox);

            document.getElementById('placementStatusBadge').textContent = 'Placement active';
            document.getElementById('placementStatusBadge').className = 'w-full py-2 bg-blue-950/80 border border-blue-700 text-blue-300 font-bold rounded-lg text-xs text-center';
            showToast('✅ Assinatura posicionada no PDF e salva em Saved!');
        }

        function makeElementDraggable(elmnt) {
            let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
            elmnt.onmousedown = dragMouseDown;

            function dragMouseDown(e) {
                if (e.target.classList.contains('signature-handle') || e.target.tagName === 'BUTTON' || e.target.classList.contains('stamp-text') || e.target.getAttribute('contenteditable') === 'true') return;
                e = e || window.event;
                e.preventDefault();
                pos3 = e.clientX;
                pos4 = e.clientY;
                document.onmouseup = closeDragElement;
                document.onmousemove = elementDrag;
            }

            function elementDrag(e) {
                e = e || window.event;
                e.preventDefault();
                pos1 = pos3 - e.clientX;
                pos2 = pos4 - e.clientY;
                pos3 = e.clientX;
                pos4 = e.clientY;
                elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
                elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
            }

            function closeDragElement() {
                document.onmouseup = null;
                document.onmousemove = null;
            }
        }

        /* MULTI-DOCUMENT UPLOAD & SWITCHER SYSTEM COM VERIFICAÇÃO NULA SEGURA */
        async function handleNativePdfUpload(e) {
            const files = Array.from(e.target.files);
            if (!files.length) return;

            for (const file of files) {
                const arrayBuf = await file.arrayBuffer();
                uploadedFilesList.push({
                    name: file.name,
                    date: 'Today',
                    bytes: arrayBuf
                });
            }

            activeFileIndex = uploadedFilesList.length - 1;
            renderFilesSidebarList();
            switchActiveDocument(activeFileIndex);
        }

        /* REMOVE UM ARQUIVO DA LISTA DE ATIVOS */
        function removeFileFromList(idx) {
            uploadedFilesList.splice(idx, 1);
            if (uploadedFilesList.length === 0) {
                activeFileIndex = -1;
                currentPdfBytes = null;
                loadedPdfDoc = null;
                document.getElementById('pdfViewerCanvasContainer').innerHTML = `
                    <div id="pdfPlaceholder" class="text-center py-24 text-gray-400 select-none">
                        <div class="text-6xl mb-4">📄</div>
                        <h2 class="text-2xl font-bold text-white mb-2" data-i18n="placeholderTitle">Free PDF Studio</h2>
                        <p class="text-sm text-gray-400 max-w-md mx-auto mb-6" data-i18n="placeholderDesc">Carregue um arquivo PDF para utilizar todas as ferramentas de assinatura digital, edição de texto e conversão!</p>
                        <label class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl cursor-pointer shadow-xl inline-block">
                            📂 <span data-i18n="openFromCompBtn">Open from computer</span>
                            <input type="file" accept=".pdf,.txt" multiple class="hidden" onchange="handleNativePdfUpload(event)">
                        </label>
                    </div>
                `;
            } else {
                activeFileIndex = Math.max(0, activeFileIndex - 1);
                switchActiveDocument(activeFileIndex);
            }
            renderFilesSidebarList();
            showToast('🗑️ Arquivo removido da lista.');
        }

        function renderFilesSidebarList() {
            const container = document.getElementById('sidebarFilesListContainer');
            if (!container) return;

            if (uploadedFilesList.length === 0) {
                container.innerHTML = `
                    <div class="p-2.5 rounded-xl bg-gray-900/40 border border-gray-800/80 flex items-center gap-2">
                        <span class="text-blue-400 text-base">📄</span>
                        <div class="truncate sidebar-text">
                            <div class="font-semibold text-gray-400 text-xs">Nenhum arquivo ativo</div>
                        </div>
                    </div>
                `;
                return;
            }

            container.innerHTML = uploadedFilesList.map((file, idx) => {
                const isActive = idx === activeFileIndex;
                return `
                <div onclick="switchActiveDocument(${idx})" class="p-2.5 rounded-xl ${isActive ? 'bg-blue-950/60 border-blue-600/80 text-white shadow-md' : 'bg-gray-900/40 border-gray-800/80 text-gray-400'} border flex items-center gap-2 cursor-pointer hover:border-blue-500 transition-all">
                    <span class="${isActive ? 'w-4 h-4 rounded-full bg-green-500 flex items-center justify-center text-black font-extrabold text-[9px]' : 'text-blue-400'}">
                        ${isActive ? '✓' : '📄'}
                    </span>
                    <div class="truncate flex-1 sidebar-text">
                        <div class="font-semibold text-xs truncate ${isActive ? 'text-white' : 'text-gray-300'}">${file.name}</div>
                        <div class="text-[10px] text-gray-500">${file.date} - PDF</div>
                    </div>
                    ${isActive 
                        ? `<button onclick="event.stopPropagation(); removeFileFromList(${idx})" title="Fechar arquivo" class="text-xs text-red-400 hover:text-red-300 p-1 sidebar-text">🗑️</button>` 
                        : `<span title="Clique para Visualizar este PDF" class="text-xs text-blue-400 hover:text-white sidebar-text">👁️</span>`}
                </div>
            `;
            }).join('');
        }

        async function switchActiveDocument(idx) {
            if (idx < 0 || idx >= uploadedFilesList.length) return;
            activeFileIndex = idx;
            const fileObj = uploadedFilesList[idx];

            const signNameEl = document.getElementById('signFileNameLabel');
            if (signNameEl) signNameEl.textContent = fileObj.name;

            showToast(`📄 Alternando para o documento: ${fileObj.name}`);

            try {
                currentPdfBytes = fileObj.bytes;
                loadedPdfDoc = await PDFLib.PDFDocument.load(currentPdfBytes.slice(0));
                pdfEditedTextItems = [];
                pdfTotalPages = loadedPdfDoc.getPageCount();

                renderFilesSidebarList();
                renderPdfWorkspace();
            } catch (err) {
                console.error('Erro ao alternar documento:', err);
                alert('Erro ao carregar o documento selecionado.');
            }
        }

        /* FUNÇÕES AUXILIARES PARA DESENHO DA BARRINHA LATERAL (PEN, LINE, ARROW, BOX, HIGHLIGHT) */
        function drawArrowOnCtx(ctx, fromX, fromY, toX, toY, color, width) {
            const headlen = 16;
            const dx = toX - fromX;
            const dy = toY - fromY;
            const angle = Math.atan2(dy, dx);
            
            ctx.strokeStyle = color;
            ctx.fillStyle = color;
            ctx.lineWidth = width;
            ctx.lineCap = 'round';
            
            ctx.beginPath();
            ctx.moveTo(fromX, fromY);
            ctx.lineTo(toX, toY);
            ctx.stroke();
            
            ctx.beginPath();
            ctx.moveTo(toX, toY);
            ctx.lineTo(toX - headlen * Math.cos(angle - Math.PI / 6), toY - headlen * Math.sin(angle - Math.PI / 6));
            ctx.lineTo(toX - headlen * Math.cos(angle + Math.PI / 6), toY - headlen * Math.sin(angle + Math.PI / 6));
            ctx.lineTo(toX, toY);
            ctx.fill();
        }

        function drawRectOnCtx(ctx, x1, y1, x2, y2, color, width) {
            ctx.strokeStyle = color;
            ctx.lineWidth = width;
            const x = Math.min(x1, x2);
            const y = Math.min(y1, y2);
            const w = Math.abs(x2 - x1);
            const h = Math.abs(y2 - y1);
            ctx.strokeRect(x, y, w, h);
        }

        async function renderPdfWorkspace() {
            if (!currentPdfBytes) return;

            const container = document.getElementById('pdfViewerCanvasContainer');
            container.innerHTML = `<div id="pdfPagesContainer" class="space-y-8 flex flex-col items-center w-full"></div>`;

            const pdfViewer = await pdfjsLib.getDocument({ data: currentPdfBytes.slice(0) }).promise;
            const pagesContainer = document.getElementById('pdfPagesContainer');

            document.getElementById('pdfPageIndicator').textContent = `${pdfPageNum} / ${pdfViewer.numPages}`;
            document.getElementById('pdfZoomVal').textContent = `${Math.round(currentScale * 50)}%`;

            for (let pageNum = 1; pageNum <= pdfViewer.numPages; pageNum++) {
                const page = await pdfViewer.getPage(pageNum);
                const viewport = page.getViewport({ scale: currentScale, rotation: pdfRotation });

                const pageCard = document.createElement('div');
                pageCard.className = 'pdf-page-container';
                pageCard.style.width = `${viewport.width}px`;
                pageCard.style.height = `${viewport.height}px`;

                pageCard.onclick = function() {
                    if (isPageGridActive) {
                        togglePdfPageGrid();
                    }
                };

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // CAMADA DE ANOTAÇÕES DESENHÁVEL
                const drawCanvas = document.createElement('canvas');
                drawCanvas.className = 'pdf-drawing-canvas';
                drawCanvas.setAttribute('data-page-num', pageNum);
                drawCanvas.width = viewport.width;
                drawCanvas.height = viewport.height;
                const drawCtx = drawCanvas.getContext('2d');

                let isDrawingSideShape = false;
                let sStartX = 0, sStartY = 0;
                let tempCanvasSnapshot = null;
                let highlightPathPoints = [];

                drawCanvas.addEventListener('mousedown', (e) => {
                    if (!sideActiveDrawTool.startsWith('draw-')) return;
                    isDrawingSideShape = true;
                    sStartX = e.offsetX;
                    sStartY = e.offsetY;

                    // Guarda snapshot inicial do canvas antes de começar a desenhar
                    tempCanvasSnapshot = drawCtx.getImageData(0, 0, drawCanvas.width, drawCanvas.height);

                    if (sideActiveDrawTool === 'draw-highlight') {
                        highlightPathPoints = [{ x: sStartX, y: sStartY }];
                    } else if (sideActiveDrawTool === 'draw-free') {
                        drawCtx.beginPath();
                        drawCtx.strokeStyle = sideActiveDrawColor;
                        drawCtx.lineWidth = 4;
                        drawCtx.globalAlpha = 1.0;
                        drawCtx.globalCompositeOperation = 'source-over';
                        drawCtx.lineCap = 'round';
                        drawCtx.lineJoin = 'round';
                        drawCtx.moveTo(sStartX, sStartY);
                    }
                });

                drawCanvas.addEventListener('mousemove', (e) => {
                    if (!isDrawingSideShape || !sideActiveDrawTool.startsWith('draw-')) return;
                    const cX = e.offsetX;
                    const cY = e.offsetY;

                    if (sideActiveDrawTool === 'draw-highlight') {
                        highlightPathPoints.push({ x: cX, y: cY });

                        // Restaura o snapshot inicial para NÃO acumular opacidade no mesmo traço
                        drawCtx.putImageData(tempCanvasSnapshot, 0, 0);

                        // Desenha o traço do Marca-Texto com transparência perfeita (Multiply + 0.40 Alpha)
                        drawCtx.save();
                        drawCtx.beginPath();
                        drawCtx.strokeStyle = sideActiveDrawColor;
                        drawCtx.lineWidth = 18;
                        drawCtx.lineCap = 'round';
                        drawCtx.lineJoin = 'round';
                        drawCtx.globalAlpha = 0.40;
                        drawCtx.globalCompositeOperation = 'multiply';

                        drawCtx.moveTo(highlightPathPoints[0].x, highlightPathPoints[0].y);
                        for (let i = 1; i < highlightPathPoints.length; i++) {
                            drawCtx.lineTo(highlightPathPoints[i].x, highlightPathPoints[i].y);
                        }
                        drawCtx.stroke();
                        drawCtx.restore();
                    } else if (sideActiveDrawTool === 'draw-free') {
                        drawCtx.lineTo(cX, cY);
                        drawCtx.stroke();
                    } else {
                        // Formas geométricas (reta, seta, quadrado)
                        drawCtx.putImageData(tempCanvasSnapshot, 0, 0);
                        drawCtx.globalAlpha = 1.0;
                        drawCtx.globalCompositeOperation = 'source-over';

                        if (sideActiveDrawTool === 'draw-line') {
                            drawCtx.beginPath();
                            drawCtx.strokeStyle = sideActiveDrawColor;
                            drawCtx.lineWidth = 4;
                            drawCtx.lineCap = 'round';
                            drawCtx.moveTo(sStartX, sStartY);
                            drawCtx.lineTo(cX, cY);
                            drawCtx.stroke();
                        } else if (sideActiveDrawTool === 'draw-arrow') {
                            drawArrowOnCtx(drawCtx, sStartX, sStartY, cX, cY, sideActiveDrawColor, 4);
                        } else if (sideActiveDrawTool === 'draw-box') {
                            drawRectOnCtx(drawCtx, sStartX, sStartY, cX, cY, sideActiveDrawColor, 4);
                        }
                    }
                });

                drawCanvas.addEventListener('mouseup', () => {
                    if (isDrawingSideShape) {
                        isDrawingSideShape = false;
                        drawCtx.globalAlpha = 1.0;
                        drawCtx.globalCompositeOperation = 'source-over';

                        // Salva o snapshot final no historico da pagina para desfazer passo a passo
                        if (!sideDrawHistoryStack[pageNum]) sideDrawHistoryStack[pageNum] = [];
                        sideDrawHistoryStack[pageNum].push(drawCtx.getImageData(0, 0, drawCanvas.width, drawCanvas.height));

                        showToast('✍️ Marcação com transparência salva!');
                    }
                });

                drawCanvas.addEventListener('mouseleave', () => { isDrawingSideShape = false; });

                const textLayerDiv = document.createElement('div');
                textLayerDiv.className = 'pdf-text-layer';

                pageCard.appendChild(canvas);
                pageCard.appendChild(drawCanvas);
                pageCard.appendChild(textLayerDiv);
                pagesContainer.appendChild(pageCard);

                await page.render({ canvasContext: context, viewport: viewport }).promise;

                // Extrai itens de texto para edição in-place
                const textContent = await page.getTextContent();
                textContent.items.forEach((item) => {
                    if (!item.str || !item.str.trim()) return;

                    const tx = pdfjsLib.Util.transform(viewport.transform, item.transform);
                    const fontHeight = Math.sqrt(tx[2] * tx[2] + tx[3] * tx[3]);

                    const textSpan = document.createElement('span');
                    textSpan.className = 'pdf-text-item';
                    textSpan.textContent = item.str;
                    textSpan.contentEditable = "true";

                    const itemLeft = tx[4];
                    const itemTop = tx[5] - fontHeight;
                    const itemWidth = item.width ? (item.width * viewport.scale) : (item.str.length * fontHeight * 0.5);

                    textSpan.style.left = `${itemLeft}px`;
                    textSpan.style.top = `${itemTop}px`;
                    textSpan.style.fontSize = `${fontHeight}px`;
                    textSpan.style.fontFamily = item.fontName || 'sans-serif';

                    // DESTAQUE MARCA-TEXTO 100% LIMPO
                    textSpan.addEventListener('click', function(e) {
                        if (activeTool === 'highlight') {
                            e.stopPropagation();
                            this.classList.toggle('highlighted');
                            showToast('🔲 Marca-texto amarelo aplicado com nitidez perfeita!');
                        }
                    });

                    textSpan.addEventListener('focus', function() {
                        if (activeTool !== 'highlight') {
                            this.classList.add('editing');
                        }
                    });

                    const saveChanges = function() {
                        if (activeTool === 'highlight') return;

                        const newText = textSpan.innerText.trim();
                        if (newText && newText !== item.str) {
                            textSpan.classList.remove('editing');
                            textSpan.classList.add('permanently-edited');
                            textSpan.innerText = newText;

                            context.font = `bold ${fontHeight}px sans-serif`;
                            const oldTextWidth = context.measureText(item.str).width || itemWidth;
                            const newTextWidth = context.measureText(newText).width || itemWidth;
                            const eraseWidth = Math.max(oldTextWidth, newTextWidth, itemWidth, item.str.length * fontHeight * 0.9) + 80;

                            // 1. Apaga totalmente a frase e símbolos antigos no canvas com retângulo branco amplo (+80px)
                            context.fillStyle = "#ffffff";
                            context.fillRect(itemLeft - 6, itemTop - 6, eraseWidth, fontHeight + 12);

                            // 2. Desenha a nova palavra preta diretamente no canvas 2D
                            context.fillStyle = "#000000";
                            context.fillText(newText, itemLeft, itemTop + fontHeight - 2);

                            pdfEditedTextItems.push({
                                pageNum: pageNum,
                                originalStr: item.str,
                                newStr: newText,
                                x: item.transform[4],
                                y: item.transform[5],
                                fontSize: fontHeight
                            });
                            showToast(`✅ Texto gravado: "${newText}"`);
                        } else {
                            textSpan.classList.remove('editing');
                        }
                    };

                    textSpan.addEventListener('blur', saveChanges);
                    textSpan.addEventListener('keydown', (evt) => {
                        if (evt.key === 'Enter') { evt.preventDefault(); textSpan.blur(); }
                    });

                    textLayerDiv.appendChild(textSpan);
                });
            }

            showToast('✅ Free PDF Studio Pronto!');
        }

        function zoomInPdf() { currentScale += 0.2; renderPdfWorkspace(); }
        function zoomOutPdf() { if (currentScale > 0.6) { currentScale -= 0.2; renderPdfWorkspace(); } }
        function rotateCurrentPdfPage() { pdfRotation = (pdfRotation + 90) % 360; renderPdfWorkspace(); }
        function prevPdfPage() { if (pdfPageNum > 1) { pdfPageNum--; renderPdfWorkspace(); } }
        function nextPdfPage() { if (pdfPageNum < pdfTotalPages) { pdfPageNum++; renderPdfWorkspace(); } }

        /* DOWNLOAD NATIVO DE ALTA PRECISÃO QUE GRAVA 100% DAS EDICÕES, ANOTAÇÕES E MARCA-TEXTO TRANSPARENTE MULTIPLY */
        async function downloadModifiedNativePdf() {
            if (document.activeElement && document.activeElement.blur) {
                document.activeElement.blur();
            }

            const pageContainers = document.querySelectorAll('.pdf-page-container');
            if (!pageContainers || pageContainers.length === 0) {
                alert('Carregue um arquivo PDF primeiro!');
                return;
            }

            try {
                showToast('💾 Gerando PDF final com 100% das alterações salvas...');

                const newPdfDoc = await PDFLib.PDFDocument.create();

                for (let i = 0; i < pageContainers.length; i++) {
                    const container = pageContainers[i];
                    const originalCanvas = container.querySelector('canvas:not(.pdf-drawing-canvas)');
                    const drawCanvas = container.querySelector('.pdf-drawing-canvas');
                    if (!originalCanvas) continue;

                    // Cria um canvas temporário para achatar todas as camadas visuais
                    const exportCanvas = document.createElement('canvas');
                    exportCanvas.width = originalCanvas.width;
                    exportCanvas.height = originalCanvas.height;
                    const exportCtx = exportCanvas.getContext('2d');

                    // 1. Desenha a base da página PDF renderizada com todas as edições de texto
                    exportCtx.drawImage(originalCanvas, 0, 0);

                    // 2. Desenha todas as anotações e formas geométricas (incluindo marca-texto com transparência real)
                    if (drawCanvas) {
                        exportCtx.drawImage(drawCanvas, 0, 0);
                    }

                    // 3. Garante o desenho de qualquer texto alterado nativo (edited)
                    const editedItems = container.querySelectorAll('.pdf-text-item.permanently-edited, .pdf-text-item.editing');
                    editedItems.forEach(item => {
                        const left = parseFloat(item.style.left) || 0;
                        const top = parseFloat(item.style.top) || 0;
                        const fontSize = parseFloat(item.style.fontSize) || 12;
                        const txt = item.textContent;

                        if (txt) {
                            exportCtx.font = `bold ${fontSize}px sans-serif`;
                            const txtW = exportCtx.measureText(txt).width || 100;
                            exportCtx.fillStyle = "#ffffff";
                            exportCtx.fillRect(left - 6, top - 6, txtW + 80, fontSize + 12);
                            exportCtx.fillStyle = "#000000";
                            exportCtx.fillText(txt, left, top + fontSize - 2);
                        }
                    });

                    // 4. Desenha trechos com marca-texto amarelo
                    const highlightedItems = container.querySelectorAll('.pdf-text-item.highlighted');
                    highlightedItems.forEach(item => {
                        const left = parseFloat(item.style.left) || 0;
                        const top = parseFloat(item.style.top) || 0;
                        const fontSize = parseFloat(item.style.fontSize) || 12;
                        
                        exportCtx.font = `${fontSize}px sans-serif`;
                        const txtWidth = exportCtx.measureText(item.textContent).width || 100;

                        exportCtx.fillStyle = "rgba(250, 204, 21, 0.45)";
                        exportCtx.fillRect(left - 2, top - 2, txtWidth + 6, fontSize + 4);
                    });

                    // 5. Desenha caixas de texto adicionais livres (criadas via botão T+)
                    const customTextNotes = container.querySelectorAll('.pdf-custom-text-box');
                    customTextNotes.forEach(box => {
                        const left = box.offsetLeft;
                        const top = box.offsetTop;
                        const txtSpan = box.querySelector('[contenteditable="true"]');
                        const txt = txtSpan ? txtSpan.innerText.trim() : '';

                        if (txt) {
                            exportCtx.save();
                            exportCtx.font = "bold 16px sans-serif";
                            exportCtx.fillStyle = "#000000";
                            exportCtx.fillText(txt, left + 6, top + 18);
                            exportCtx.restore();
                        }
                    });

                    // 6. Desenha assinaturas digitais sobrepostas no container
                    const sigOverlay = container.querySelector('#activeSignatureOverlay');
                    if (sigOverlay) {
                        const img = sigOverlay.querySelector('img');
                        if (img && img.src) {
                            const left = sigOverlay.offsetLeft;
                            const top = sigOverlay.offsetTop;
                            const width = sigOverlay.offsetWidth;
                            const height = sigOverlay.offsetHeight;

                            exportCtx.drawImage(img, left, top, width, height);
                        }
                    }

                    // 7. Desenha carimbos visíveis
                    const stamps = container.querySelectorAll('.stamp-text');
                    stamps.forEach(stamp => {
                        const parent = stamp.parentElement;
                        const left = parent.offsetLeft;
                        const top = parent.offsetTop;
                        
                        exportCtx.save();
                        exportCtx.translate(left + 50, top + 20);
                        exportCtx.rotate(-12 * Math.PI / 180);
                        
                        exportCtx.strokeStyle = "#16a34a";
                        exportCtx.lineWidth = 3;
                        exportCtx.strokeRect(-10, -25, 160, 45);
                        
                        exportCtx.font = "bold 20px sans-serif";
                        exportCtx.fillStyle = "#16a34a";
                        exportCtx.fillText(stamp.textContent, 0, 0);
                        
                        exportCtx.restore();
                    });

                    // Converte o canvas renderizado em PNG de alta definição
                    const pageImageDataUrl = exportCanvas.toDataURL('image/png', 0.98);
                    const pngImage = await newPdfDoc.embedPng(pageImageDataUrl);

                    // Adiciona a página ao novo PDF com dimensões perfeitas
                    const pdfPage = newPdfDoc.addPage([originalCanvas.width, originalCanvas.height]);
                    pdfPage.drawImage(pngImage, {
                        x: 0,
                        y: 0,
                        width: originalCanvas.width,
                        height: originalCanvas.height
                    });
                }

                const pdfBytes = await newPdfDoc.save();
                const blob = new Blob([pdfBytes], { type: 'application/pdf' });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'freepdf_editado.pdf';
                a.click();

                showToast('💾 PDF Baixado com Sucesso! 100% das alterações e anotações foram salvas!');
            } catch (err) {
                console.error('Erro ao baixar PDF:', err);
                alert('Erro ao gerar o PDF editado.');
            }
        }
    </script>

    <script>
      if ("serviceWorker" in navigator) {
        window.addEventListener("load", () => {
          navigator.serviceWorker.register("sw.js").catch(err => console.log("SW reg error:", err));
        });
      }
    </script>
</body>
</html>
