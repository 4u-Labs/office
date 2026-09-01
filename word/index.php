<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Documento1 - Word Online</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&family=Lora:ital,wght@0,400;0,700;1,400&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Mammoth.js para leitura e importacao nativa de .DOCX -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>

    <style>
        * { box-sizing: border-box !important; margin: 0; padding: 0; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif; }
        
        :root {
            --primary: #185abd;
            --primary-hover: #104a9b;
            --ribbon-bg: #f3f3f3;
            --border-color: #e1dfdd;
            --text-color: #323130;
            --page-bg: #edebe9;
            --toolbar-hover: #edebe9;
            --toolbar-active: #c7e0f4;
        }

        .dark-mode {
            --primary: #2899f5;
            --primary-hover: #0078d4;
            --ribbon-bg: #201f1e;
            --border-color: #3b3a39;
            --text-color: #f3f2f1;
            --page-bg: #11100f;
            --toolbar-hover: #292827;
            --toolbar-active: #323130;
        }

        body { height: 100vh; display: flex; flex-direction: column; overflow: hidden; background: var(--page-bg); color: var(--text-color); }
        
        /* Title Bar */
        .title-bar { background: var(--ribbon-bg); border-bottom: 1px solid var(--border-color); height: 38px; display: flex; align-items: center; padding: 0 12px; flex-shrink: 0; gap: 8px; }
        
        /* Ribbon Tabs */
        .ribbon-tabs { background: var(--ribbon-bg); border-bottom: 1px solid var(--border-color); display: flex; align-items: flex-end; padding: 0 10px; height: 32px; gap: 2px; }
        .ribbon-tab { padding: 5px 14px; font-size: 12.5px; font-weight: 500; cursor: pointer; border-radius: 4px 4px 0 0; border: 1px solid transparent; border-bottom: none; color: var(--text-color); transition: all 0.15s; }
        .ribbon-tab:hover { background: rgba(0,0,0,0.04); }
        .ribbon-tab.active { background: #ffffff; border-color: var(--border-color); border-bottom: 1px solid #ffffff; font-weight: 600; color: var(--primary); margin-bottom: -1px; }
        .dark-mode .ribbon-tab.active { background: #2b2b2b; border-bottom-color: #2b2b2b; }
        .ribbon-tab.file-btn { background: var(--primary); color: #ffffff !important; font-weight: 700; border-radius: 4px; padding: 4px 14px; margin-right: 6px; margin-bottom: 2px; }
        .ribbon-tab.file-btn:hover { background: var(--primary-hover); }

        /* Ribbon Toolbar Content */
        .ribbon-content { background: #ffffff; border-bottom: 1px solid var(--border-color); height: 86px; display: flex; align-items: center; padding: 4px 12px; gap: 12px; overflow-x: auto; flex-shrink: 0; }
        .dark-mode .ribbon-content { background: #2b2b2b; }
        .ribbon-group { display: flex; flex-direction: column; justify-content: space-between; height: 100%; padding: 0 6px; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .ribbon-group-content { display: flex; align-items: center; gap: 4px; flex: 1; }
        .ribbon-group-label { font-size: 10px; color: #8a8886; text-align: center; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Toolbar Button */
        .tool-btn { width: 28px; height: 28px; border: 1px solid transparent; background: transparent; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-color); font-size: 13px; transition: all 0.15s; }
        .tool-btn:hover { background: var(--toolbar-hover); }
        .tool-btn.active { background: var(--toolbar-active); border-color: #a6c8e0; color: var(--primary); }
        .tool-btn-lg { height: 56px; padding: 4px 8px; border-radius: 4px; border: 1px solid transparent; background: transparent; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; font-size: 11px; color: var(--text-color); transition: all 0.15s; }
        .tool-btn-lg:hover { background: var(--toolbar-hover); }

        /* Ruler */
        .ruler { background: #faf9f8; border-bottom: 1px solid var(--border-color); height: 18px; display: flex; align-items: center; justify-content: center; font-size: 9px; color: #8a8886; user-select: none; }
        .dark-mode .ruler { background: #252423; color: #a19f9d; }

        /* A4 Page Editor */
        .word-page {
            width: 794px;
            min-height: 1123px;
            background: #ffffff;
            color: #000000;
            padding: 72px 96px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 2px;
            outline: none;
            font-size: 14px;
            line-height: 1.6;
            transition: all 0.2s;
            margin: 24px auto;
        }
        .dark-mode .word-page.dark-page { background: #1b1a19; color: #f3f2f1; }

        /* Focus Mode */
        .focus-mode .title-bar, .focus-mode .ribbon-tabs, .focus-mode .ribbon-content, .focus-mode .ruler, .focus-mode footer { display: none !important; }
        .focus-mode main { background: #000000 !important; padding: 24px 0 !important; }

        /* Styles Box Cards */
        .style-card { padding: 4px 10px; border: 1px solid var(--border-color); border-radius: 4px; cursor: pointer; text-align: center; min-width: 64px; background: transparent; transition: 0.15s; color: var(--text-color); }
        .style-card:hover { border-color: var(--primary); background: var(--toolbar-hover); }
        .style-card.active { border-color: var(--primary); background: var(--toolbar-active); }

        @media print {
            .title-bar, .ribbon-tabs, .ribbon-content, .ruler, footer, #focusExitBtn { display: none !important; }
            body { background: white !important; }
            .word-page { box-shadow: none !important; margin: 0 !important; width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body id="wordAppBody">

    <!-- 1. Authentic Word 365 Title Bar -->
    <div class="title-bar">
        <a href="../index.php" title="Voltar ao FreeOffice" class="px-2 py-1 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded flex items-center gap-1 text-xs transition-colors">
            <span>←</span> <span>FreeOffice</span>
        </a>

        <div class="flex items-center gap-1 ml-1">
            <button onclick="saveDocument()" class="tool-btn" title="Salvar (Ctrl+S)"><i class="fa-regular fa-floppy-disk text-blue-700"></i></button>
            <button onclick="formatDoc('undo')" class="tool-btn" title="Desfazer (Ctrl+Z)"><i class="fa-solid fa-rotate-left"></i></button>
            <button onclick="formatDoc('redo')" class="tool-btn" title="Refazer (Ctrl+Y)"><i class="fa-solid fa-rotate-right"></i></button>
        </div>

        <div class="h-4 w-px bg-gray-300 mx-1"></div>

        <!-- Auto-save Toggle -->
        <div class="flex items-center gap-2 text-xs">
            <span class="text-gray-500 font-medium">Salvamento Automático</span>
            <div id="autoSaveToggle" onclick="toggleAutoSave()" class="w-8 h-4 bg-blue-600 rounded-full relative cursor-pointer transition-all">
                <div class="w-3 h-3 bg-white rounded-full absolute top-0.5 right-0.5 transition-all"></div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="flex items-center gap-1.5 ml-2 font-semibold text-xs text-gray-700">
            <i class="fa-solid fa-file-word text-blue-700 text-sm"></i>
            <input type="text" id="docTitleInput" value="Documento1" onchange="handleTitleChange(this.value)" class="bg-transparent hover:bg-black/5 focus:bg-white px-2 py-0.5 rounded border border-transparent focus:border-blue-500 outline-none text-xs font-semibold max-w-[150px]">
            <span id="saveStatusBadge" class="text-[11px] text-green-600 font-normal ml-1">Salvo</span>
        </div>

        <!-- Center Search Box -->
        <div class="flex-1 max-w-sm mx-auto flex items-center bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded px-2.5 py-1 gap-2">
            <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
            <input type="text" id="quickSearchInput" placeholder="Pesquisar no documento (Ctrl+F)..." onkeydown="if(event.key==='Enter') executeQuickSearch(this.value)" class="bg-transparent border-none outline-none text-xs w-full text-gray-700 dark:text-gray-200">
        </div>

        <!-- Right Quick Tools -->
        <div class="flex items-center gap-2 ml-auto">
            <button onclick="toggleDarkMode()" class="tool-btn" title="Alternar Modo Escuro/Claro">
                <i class="fa-solid fa-moon"></i>
            </button>
            <button onclick="toggleFocusMode()" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded text-xs font-semibold flex items-center gap-1 shadow-sm">
                <i class="fa-solid fa-bullseye"></i> <span>Modo Foco</span>
            </button>
            <label class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-semibold flex items-center gap-1 cursor-pointer shadow-sm">
                <i class="fa-solid fa-folder-open"></i> <span>Abrir .DOCX</span>
                <input type="file" id="wordFileInput" accept=".docx,.txt,.html,.htm" class="hidden" onchange="uploadWordDocument(event)">
            </label>
            <button onclick="downloadAsDocx()" class="px-3 py-1 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-bold flex items-center gap-1 shadow-sm">
                <i class="fa-solid fa-download"></i> <span>Baixar .DOCX</span>
            </button>
            <div class="w-6 h-6 rounded-full bg-blue-600 text-white text-[11px] font-bold flex items-center justify-center">
                U
            </div>
        </div>
    </div>

    <!-- 2. Authentic Ribbon Tabs -->
    <div class="ribbon-tabs">
        <button class="ribbon-tab file-btn" onclick="openFileDrawer()"><i class="fa-solid fa-bars mr-1"></i> Arquivo</button>
        <button class="ribbon-tab active" data-tab="home" onclick="switchRibbonTab('home')">Página Inicial</button>
        <button class="ribbon-tab" data-tab="insert" onclick="switchRibbonTab('insert')">Inserir</button>
        <button class="ribbon-tab" data-tab="layout" onclick="switchRibbonTab('layout')">Layout</button>
        <button class="ribbon-tab" data-tab="review" onclick="switchRibbonTab('review')">Revisão</button>
        <button class="ribbon-tab" data-tab="view" onclick="switchRibbonTab('view')">Exibir</button>
    </div>

    <!-- 3. Ribbon Content Toolbars -->
    <div class="ribbon-content">
        
        <!-- TAB: PÁGINA INICIAL -->
        <div id="tab-home" class="flex items-center gap-2 h-full">
            
            <!-- Clipboard -->
            <div class="ribbon-group">
                <div class="ribbon-group-content">
                    <button onclick="pasteFromClipboard()" class="tool-btn-lg" title="Colar (Ctrl+V)"><i class="fa-solid fa-paste text-blue-700 text-lg"></i><span>Colar</span></button>
                    <div class="flex flex-col gap-0.5">
                        <button onclick="formatDoc('cut')" class="tool-btn" title="Recortar (Ctrl+X)"><i class="fa-solid fa-scissors"></i></button>
                        <button onclick="formatDoc('copy')" class="tool-btn" title="Copiar (Ctrl+C)"><i class="fa-solid fa-copy"></i></button>
                    </div>
                </div>
                <span class="ribbon-group-label">Área de Transf.</span>
            </div>

            <!-- Font -->
            <div class="ribbon-group">
                <div class="ribbon-group-content flex-col justify-center gap-1">
                    <div class="flex items-center gap-1.5">
                        <select onchange="formatDoc('fontName', this.value)" class="text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-0.5 bg-transparent outline-none w-32 font-medium">
                            <option value="Segoe UI">Segoe UI</option>
                            <option value="Calibri">Calibri</option>
                            <option value="Arial">Arial</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Lora">Lora (Serif)</option>
                            <option value="Georgia">Georgia</option>
                            <option value="JetBrains Mono">JetBrains Mono</option>
                            <option value="Impact">Impact</option>
                        </select>
                        <select onchange="formatDoc('fontSize', this.value)" class="text-xs border border-gray-300 dark:border-gray-600 rounded px-1.5 py-0.5 bg-transparent outline-none w-14 font-medium">
                            <option value="1">8pt</option>
                            <option value="2">10pt</option>
                            <option value="3" selected>12pt</option>
                            <option value="4">14pt</option>
                            <option value="5">18pt</option>
                            <option value="6">24pt</option>
                            <option value="7">36pt</option>
                        </select>
                        <button onclick="changeFontSize(1)" class="tool-btn" title="Aumentar Fonte"><i class="fa-solid fa-arrow-up-a-z"></i></button>
                        <button onclick="changeFontSize(-1)" class="tool-btn" title="Diminuir Fonte"><i class="fa-solid fa-arrow-down-z-a"></i></button>
                        <button onclick="formatDoc('removeFormat')" class="tool-btn" title="Limpar Formatação"><i class="fa-solid fa-eraser text-red-500"></i></button>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="formatDoc('bold')" class="tool-btn font-bold" title="Negrito (Ctrl+B)"><b>B</b></button>
                        <button onclick="formatDoc('italic')" class="tool-btn italic" title="Itálico (Ctrl+I)"><i>I</i></button>
                        <button onclick="formatDoc('underline')" class="tool-btn underline" title="Sublinhado (Ctrl+U)"><u>U</u></button>
                        <button onclick="formatDoc('strikeThrough')" class="tool-btn line-through" title="Tachado"><s>S</s></button>
                        <button onclick="formatDoc('subscript')" class="tool-btn" title="Subscrito">X₂</button>
                        <button onclick="formatDoc('superscript')" class="tool-btn" title="Sobrescrito">X²</button>
                        <div class="h-4 w-px bg-gray-300 mx-0.5"></div>
                        <label class="tool-btn cursor-pointer" title="Cor da Fonte">
                            <i class="fa-solid fa-font text-red-600"></i>
                            <input type="color" onchange="formatDoc('foreColor', this.value)" class="hidden">
                        </label>
                        <label class="tool-btn cursor-pointer" title="Cor de Realce">
                            <i class="fa-solid fa-highlighter text-yellow-500"></i>
                            <input type="color" value="#fef08a" onchange="formatDoc('hiliteColor', this.value)" class="hidden">
                        </label>
                    </div>
                </div>
                <span class="ribbon-group-label">Fonte</span>
            </div>

            <!-- Paragraph -->
            <div class="ribbon-group">
                <div class="ribbon-group-content flex-col justify-center gap-1">
                    <div class="flex items-center gap-1">
                        <button onclick="formatDoc('insertUnorderedList')" class="tool-btn" title="Lista com Marcadores"><i class="fa-solid fa-list-ul"></i></button>
                        <button onclick="formatDoc('insertOrderedList')" class="tool-btn" title="Lista Numerada"><i class="fa-solid fa-list-ol"></i></button>
                        <button onclick="formatDoc('outdent')" class="tool-btn" title="Diminuir Recuo"><i class="fa-solid fa-outdent"></i></button>
                        <button onclick="formatDoc('indent')" class="tool-btn" title="Aumentar Recuo"><i class="fa-solid fa-indent"></i></button>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="formatDoc('justifyLeft')" class="tool-btn" title="Alinhar à Esquerda"><i class="fa-solid fa-align-left"></i></button>
                        <button onclick="formatDoc('justifyCenter')" class="tool-btn" title="Centralizar"><i class="fa-solid fa-align-center"></i></button>
                        <button onclick="formatDoc('justifyRight')" class="tool-btn" title="Alinhar à Direita"><i class="fa-solid fa-align-right"></i></button>
                        <button onclick="formatDoc('justifyFull')" class="tool-btn" title="Justificar"><i class="fa-solid fa-align-justify"></i></button>
                    </div>
                </div>
                <span class="ribbon-group-label">Parágrafo</span>
            </div>

            <!-- Styles Gallery -->
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-1.5">
                    <button onclick="formatDoc('formatBlock', 'p')" class="style-card active">
                        <div class="text-xs font-semibold">Normal</div>
                        <div class="text-[9px] text-gray-400">AaBbCc</div>
                    </button>
                    <button onclick="formatDoc('formatBlock', 'h1')" class="style-card">
                        <div class="text-xs font-bold text-blue-700">Título 1</div>
                        <div class="text-[9px] text-gray-400">AaBbCc</div>
                    </button>
                    <button onclick="formatDoc('formatBlock', 'h2')" class="style-card">
                        <div class="text-xs font-semibold text-blue-600">Título 2</div>
                        <div class="text-[9px] text-gray-400">AaBbCc</div>
                    </button>
                    <button onclick="formatDoc('formatBlock', 'blockquote')" class="style-card">
                        <div class="text-xs italic text-gray-600">Citação</div>
                        <div class="text-[9px] text-gray-400">AaBbCc</div>
                    </button>
                </div>
                <span class="ribbon-group-label">Estilos Rápidos</span>
            </div>

            <!-- Editing -->
            <div class="ribbon-group">
                <div class="ribbon-group-content flex-col justify-center gap-1">
                    <button onclick="openFindModal()" class="flex items-center gap-1.5 px-2 py-0.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-xs text-gray-700 dark:text-gray-300 w-full text-left">
                        <i class="fa-solid fa-magnifying-glass text-blue-600"></i> <span>Localizar</span>
                    </button>
                    <button onclick="openReplaceModal()" class="flex items-center gap-1.5 px-2 py-0.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded text-xs text-gray-700 dark:text-gray-300 w-full text-left">
                        <i class="fa-solid fa-arrow-right-arrow-left text-green-600"></i> <span>Substituir</span>
                    </button>
                </div>
                <span class="ribbon-group-label">Edição</span>
            </div>
        </div>

        <!-- TAB: INSERIR -->
        <div id="tab-insert" class="hidden items-center gap-2 h-full">
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="insertTable()" class="tool-btn-lg"><i class="fa-solid fa-table text-blue-600 text-lg"></i><span>Tabela</span></button>
                    <label class="tool-btn-lg cursor-pointer">
                        <i class="fa-regular fa-image text-green-600 text-lg"></i><span>Imagem</span>
                        <input type="file" accept="image/*" class="hidden" onchange="insertImageFile(event)">
                    </label>
                    <button onclick="insertHorizontalRule()" class="tool-btn-lg"><i class="fa-solid fa-minus text-gray-600 text-lg"></i><span>Linha</span></button>
                    <button onclick="insertPageBreak()" class="tool-btn-lg"><i class="fa-solid fa-file-circle-plus text-purple-600 text-lg"></i><span>Quebra Pág.</span></button>
                    <button onclick="insertLinkPrompt()" class="tool-btn-lg"><i class="fa-solid fa-link text-blue-500 text-lg"></i><span>Link</span></button>
                </div>
                <span class="ribbon-group-label">Elementos</span>
            </div>
        </div>

        <!-- TAB: LAYOUT -->
        <div id="tab-layout" class="hidden items-center gap-2 h-full">
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="setMargins('normal')" class="tool-btn-lg"><i class="fa-solid fa-table-columns text-lg"></i><span>Margem Padrão</span></button>
                    <button onclick="setMargins('narrow')" class="tool-btn-lg"><i class="fa-solid fa-arrows-left-right text-lg"></i><span>Margem Estreita</span></button>
                    <button onclick="setPageOrientation('portrait')" class="tool-btn-lg active"><i class="fa-solid fa-file text-lg"></i><span>Retrato</span></button>
                    <button onclick="setPageOrientation('landscape')" class="tool-btn-lg"><i class="fa-solid fa-file-invoice text-lg"></i><span>Paisagem</span></button>
                </div>
                <span class="ribbon-group-label">Configuração de Página</span>
            </div>
        </div>

        <!-- TAB: REVISÃO -->
        <div id="tab-review" class="hidden items-center gap-2 h-full">
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="showWordCountModal()" class="tool-btn-lg"><i class="fa-solid fa-spell-check text-blue-600 text-lg"></i><span>Contagem</span></button>
                    <button onclick="formatDoc('removeFormat')" class="tool-btn-lg"><i class="fa-solid fa-broom text-amber-600 text-lg"></i><span>Limpar Tudo</span></button>
                </div>
                <span class="ribbon-group-label">Revisão</span>
            </div>
        </div>

        <!-- TAB: EXIBIR -->
        <div id="tab-view" class="hidden items-center gap-2 h-full">
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="toggleRuler()" class="tool-btn-lg active"><i class="fa-solid fa-ruler-horizontal text-lg"></i><span>Régua</span></button>
                    <button onclick="toggleDarkMode()" class="tool-btn-lg"><i class="fa-solid fa-circle-half-stroke text-lg"></i><span>Modo Escuro</span></button>
                    <button onclick="setZoom(1)" class="tool-btn-lg"><i class="fa-solid fa-magnifying-glass-plus text-lg"></i><span>100%</span></button>
                </div>
                <span class="ribbon-group-label">Visualização</span>
            </div>
        </div>

    </div>

    <!-- 4. Visual Ruler -->
    <div id="rulerBar" class="ruler">
        <span class="opacity-60 font-mono tracking-widest">|...1...|...2...|...3...|...4...|...5...|...6...|...7...|...8...|...9...|...10...|...11...|...12...|...13...|...14...|...15...|...16...|...17...|...18...|</span>
    </div>

    <!-- 5. Main Document Workspace -->
    <main class="flex-1 overflow-auto flex justify-center relative p-8 select-text">
        <button id="focusExitBtn" onclick="toggleFocusMode()" class="hidden fixed top-4 right-4 bg-red-600 text-white font-bold px-4 py-2 rounded-xl z-50 text-xs shadow-2xl">
            ✕ Sair do Modo Foco (ESC)
        </button>

        <div id="wordEditor" class="word-page" contenteditable="true" oninput="handleEditorInput()" spellcheck="true">
            <h1 class="text-3xl font-extrabold mb-4 text-gray-900" style="color: #185abd;">Documento sem título</h1>
            <p class="mb-4">Este é o seu processador de texto <strong>Word Online</strong> no FreeOffice. Você pode digitar textos com formatação completa, títulos, fontes, tabelas e salvamento automático local no navegador!</p>
            <p>Comece a digitar aqui...</p>
        </div>
    </main>

    <!-- 6. Bottom Status Bar -->
    <footer class="bg-gray-100 dark:bg-gray-900 border-t border-gray-300 dark:border-gray-800 px-4 py-1 text-xs text-gray-600 dark:text-gray-400 flex justify-between items-center select-none z-30">
        <div class="flex items-center gap-4">
            <span id="pageCountLabel"><i class="fa-regular fa-file mr-1"></i> Página 1 de 1</span>
            <span id="wordCountLabel">28 palavras</span>
            <span id="charCountLabel">198 caracteres</span>
            <span><i class="fa-solid fa-globe mr-1"></i> Português (Brasil)</span>
        </div>
        <div class="flex items-center gap-4">
            <span id="autoSaveIndicator" class="text-green-600 dark:text-green-400 font-semibold"><i class="fa-solid fa-cloud-arrow-up mr-1"></i> Salvo Localmente</span>
            <div class="flex items-center gap-2">
                <button onclick="changeZoom(-0.1)" class="hover:text-blue-600 font-bold px-1">-</button>
                <input type="range" id="zoomSlider" min="50" max="200" value="100" oninput="setZoom(this.value/100)" class="w-20 h-1.5 bg-gray-300 rounded cursor-pointer">
                <button onclick="changeZoom(0.1)" class="hover:text-blue-600 font-bold px-1">+</button>
                <span id="zoomLabel" class="w-10 text-right">100%</span>
            </div>
        </div>
    </footer>

    <!-- File Menu Drawer Modal -->
    <div id="fileDrawerModal" class="fixed inset-0 bg-black/60 hidden z-50" onclick="closeFileDrawer()">
        <div class="w-80 h-full bg-[#185abd] text-white p-6 shadow-2xl flex flex-col justify-between" onclick="event.stopPropagation()">
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 pb-4 border-b border-blue-400/30">
                    <i class="fa-solid fa-file-word text-3xl"></i>
                    <div>
                        <h2 class="font-bold text-lg leading-tight">Word Online</h2>
                        <span class="text-xs text-blue-200">FreeOffice Suite</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <button onclick="newDocument(); closeFileDrawer();" class="w-full text-left px-3 py-2.5 hover:bg-blue-800 rounded-lg text-sm font-semibold flex items-center gap-3"><i class="fa-regular fa-file"></i> Novo Documento</button>
                    <label class="w-full text-left px-3 py-2.5 hover:bg-blue-800 rounded-lg text-sm font-semibold flex items-center gap-3 cursor-pointer"><i class="fa-solid fa-folder-open"></i> Abrir Arquivo .DOCX<input type="file" accept=".docx,.txt,.html" class="hidden" onchange="uploadWordDocument(event); closeFileDrawer();"></label>
                    <button onclick="saveDocument(); closeFileDrawer();" class="w-full text-left px-3 py-2.5 hover:bg-blue-800 rounded-lg text-sm font-semibold flex items-center gap-3"><i class="fa-regular fa-floppy-disk"></i> Salvar Agora</button>
                    <button onclick="downloadAsDocx(); closeFileDrawer();" class="w-full text-left px-3 py-2.5 hover:bg-blue-800 rounded-lg text-sm font-semibold flex items-center gap-3"><i class="fa-solid fa-file-word"></i> Baixar como .DOCX</button>
                    <button onclick="window.print(); closeFileDrawer();" class="w-full text-left px-3 py-2.5 hover:bg-blue-800 rounded-lg text-sm font-semibold flex items-center gap-3"><i class="fa-solid fa-print"></i> Imprimir / Salvar PDF</button>
                </div>
            </div>

            <div class="pt-4 border-t border-blue-400/30 text-xs text-blue-200">
                FreeOffice &copy; 2026. Todos os direitos reservados.
            </div>
        </div>
    </div>

    <!-- Scripts de Funcionalidade -->
    <script>
        let currentZoom = 1.0;
        let isAutoSaveEnabled = true;

        function switchRibbonTab(tabName) {
            document.querySelectorAll('.ribbon-tab').forEach(t => t.classList.remove('active'));
            const activeTab = document.querySelector(`.ribbon-tab[data-tab="${tabName}"]`);
            if (activeTab) activeTab.classList.add('active');

            ['home', 'insert', 'layout', 'review', 'view'].forEach(t => {
                const el = document.getElementById(`tab-${t}`);
                if (el) el.classList.add('hidden');
            });
            const target = document.getElementById(`tab-${tabName}`);
            if (target) {
                target.classList.remove('hidden');
                target.classList.add('flex');
            }
        }

        function openFileDrawer() { document.getElementById('fileDrawerModal').classList.remove('hidden'); }
        function closeFileDrawer() { document.getElementById('fileDrawerModal').classList.add('hidden'); }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('word_darkmode', isDark ? '1' : '0');
        }

        function toggleFocusMode() {
            document.body.classList.toggle('focus-mode');
            const isFocus = document.body.classList.contains('focus-mode');
            const exitBtn = document.getElementById('focusExitBtn');
            if (isFocus) exitBtn.classList.remove('hidden');
            else exitBtn.classList.add('hidden');
        }

        function toggleRuler() {
            const ruler = document.getElementById('rulerBar');
            ruler.classList.toggle('hidden');
        }

        function toggleAutoSave() {
            isAutoSaveEnabled = !isAutoSaveEnabled;
            const toggle = document.getElementById('autoSaveToggle');
            const pin = toggle.querySelector('div');
            if (isAutoSaveEnabled) {
                toggle.className = "w-8 h-4 bg-blue-600 rounded-full relative cursor-pointer transition-all";
                pin.className = "w-3 h-3 bg-white rounded-full absolute top-0.5 right-0.5 transition-all";
                document.getElementById('saveStatusBadge').textContent = "Salvo";
            } else {
                toggle.className = "w-8 h-4 bg-gray-400 rounded-full relative cursor-pointer transition-all";
                pin.className = "w-3 h-3 bg-white rounded-full absolute top-0.5 left-0.5 transition-all";
                document.getElementById('saveStatusBadge').textContent = "Pausado";
            }
        }

        function formatDoc(cmd, value = null) {
            document.execCommand(cmd, false, value);
            document.getElementById('wordEditor').focus();
            handleEditorInput();
        }

        function changeFontSize(delta) {
            const select = document.querySelector('select[onchange*="fontSize"]');
            let current = parseInt(select.value);
            let next = Math.max(1, Math.min(7, current + delta));
            select.value = next;
            formatDoc('fontSize', next);
        }

        function handleEditorInput() {
            updateWordCount();
            if (!isAutoSaveEnabled) return;
            try {
                const html = document.getElementById('wordEditor').innerHTML;
                localStorage.setItem('freeword_autosave', html);
                const title = document.getElementById('docTitleInput').value;
                localStorage.setItem('freeword_title', title);
                document.getElementById('saveStatusBadge').textContent = "Salvo";
            } catch(e){}
        }

        function saveDocument() {
            const html = document.getElementById('wordEditor').innerHTML;
            localStorage.setItem('freeword_autosave', html);
            document.getElementById('saveStatusBadge').textContent = "Salvo agora!";
            setTimeout(() => { document.getElementById('saveStatusBadge').textContent = "Salvo"; }, 2000);
        }

        function handleTitleChange(val) {
            document.title = `${val} - Word Online`;
            handleEditorInput();
        }

        function newDocument() {
            if (confirm("Deseja criar um novo documento em branco?")) {
                localStorage.removeItem('freeword_autosave');
                document.getElementById('wordEditor').innerHTML = `
                    <h1 class="text-3xl font-extrabold mb-4 text-gray-900" style="color: #185abd;">Documento sem título</h1>
                    <p>Comece a digitar aqui...</p>
                `;
                document.getElementById('docTitleInput').value = "Documento1";
                document.title = "Documento1 - Word Online";
                updateWordCount();
            }
        }

        function uploadWordDocument(event) {
            const file = event.target.files[0];
            if (!file) return;
            const docName = file.name.replace(/\.[^/.]+$/, "");
            document.getElementById('docTitleInput').value = docName;
            document.title = `${docName} - Word Online`;

            if (file.name.endsWith('.docx')) {
                const reader = new FileReader();
                reader.onload = function(loadEvent) {
                    const arrayBuffer = loadEvent.target.result;
                    mammoth.convertToHtml({ arrayBuffer: arrayBuffer })
                        .then(function(result) {
                            document.getElementById('wordEditor').innerHTML = result.value;
                            handleEditorInput();
                        })
                        .catch(function(err) {
                            alert("Erro ao abrir o arquivo .DOCX.");
                        });
                };
                reader.readAsArrayBuffer(file);
            } else {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('wordEditor').innerHTML = e.target.result;
                    handleEditorInput();
                };
                reader.readAsText(file);
            }
        }

        function downloadAsDocx() {
            const content = document.getElementById('wordEditor').innerHTML;
            const title = document.getElementById('docTitleInput').value || 'documento';
            const header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' "+
                "xmlns:w='urn:schemas-microsoft-com:office:word' "+
                "xmlns='http://www.w3.org/TR/REC-html40'>"+
                "<head><meta charset='utf-8'><title>" + title + "</title></head><body>";
            const footer = "</body></html>";
            const sourceHTML = header + content + footer;

            const source = 'data:application/vnd.ms-word;charset=utf-8,' + encodeURIComponent(sourceHTML);
            const fileDownload = document.createElement("a");
            document.body.appendChild(fileDownload);
            fileDownload.href = source;
            fileDownload.download = `${title}.doc`;
            fileDownload.click();
            document.body.removeChild(fileDownload);
        }

        function insertTable() {
            const tableHtml = `
                <table style="width:100%; border-collapse:collapse; margin:16px 0; border:1px solid #cbd5e1;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="border:1px solid #cbd5e1; padding:8px;">Coluna 1</th>
                            <th style="border:1px solid #cbd5e1; padding:8px;">Coluna 2</th>
                            <th style="border:1px solid #cbd5e1; padding:8px;">Coluna 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border:1px solid #cbd5e1; padding:8px;">Item A</td>
                            <td style="border:1px solid #cbd5e1; padding:8px;">Item B</td>
                            <td style="border:1px solid #cbd5e1; padding:8px;">Item C</td>
                        </tr>
                    </tbody>
                </table><p></p>
            `;
            document.execCommand('insertHTML', false, tableHtml);
            handleEditorInput();
        }

        function insertHorizontalRule() { formatDoc('insertHorizontalRule'); }
        function insertPageBreak() { document.execCommand('insertHTML', false, '<div style="page-break-before: always; border-top: 1px dashed #cbd5e1; margin: 24px 0;"></div><p></p>'); }
        function insertLinkPrompt() {
            const url = prompt("Digite o link URL:");
            if (url) formatDoc('createLink', url);
        }

        function insertImageFile(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.execCommand('insertImage', false, e.target.result);
                handleEditorInput();
            };
            reader.readAsDataURL(file);
        }

        function setMargins(type) {
            const page = document.getElementById('wordEditor');
            if (type === 'narrow') page.style.padding = '36px 48px';
            else page.style.padding = '72px 96px';
        }

        function setPageOrientation(orient) {
            const page = document.getElementById('wordEditor');
            if (orient === 'landscape') {
                page.style.width = '1123px';
                page.style.minHeight = '794px';
            } else {
                page.style.width = '794px';
                page.style.minHeight = '1123px';
            }
        }

        function setZoom(val) {
            currentZoom = parseFloat(val);
            document.getElementById('wordEditor').style.transform = `scale(${currentZoom})`;
            document.getElementById('wordEditor').style.transformOrigin = 'top center';
            document.getElementById('zoomLabel').textContent = `${Math.round(currentZoom * 100)}%`;
            document.getElementById('zoomSlider').value = Math.round(currentZoom * 100);
        }

        function changeZoom(delta) {
            setZoom(Math.max(0.5, Math.min(2.0, currentZoom + delta)));
        }

        function updateWordCount() {
            const text = document.getElementById('wordEditor').innerText || '';
            const words = text.trim() ? text.trim().split(/\s+/).length : 0;
            const chars = text.length;
            document.getElementById('wordCountLabel').textContent = `${words} palavras`;
            document.getElementById('charCountLabel').textContent = `${chars} caracteres`;
        }

        function executeQuickSearch(query) {
            if (!query) return;
            if (window.find) {
                window.find(query);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('word_darkmode') === '1') {
                document.body.classList.add('dark-mode');
            }
            const savedHtml = localStorage.getItem('freeword_autosave');
            if (savedHtml && savedHtml.trim()) {
                document.getElementById('wordEditor').innerHTML = savedHtml;
            }
            const savedTitle = localStorage.getItem('freeword_title');
            if (savedTitle) {
                document.getElementById('docTitleInput').value = savedTitle;
                document.title = `${savedTitle} - Word Online`;
            }
            updateWordCount();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.body.classList.contains('focus-mode')) toggleFocusMode();
            if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveDocument(); }
        });
    </script>
</body>
</html>
