<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
$assetVersion = time();
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Apresentação1 - PowerPoint Online</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box !important; margin: 0; padding: 0; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif; }

        :root {
            --primary: #c43e1c;
            --primary-hover: #a03014;
            --ribbon-bg: #f3f3f3;
            --border-color: #e1dfdd;
            --text-color: #323130;
            --page-bg: #edebe9;
            --toolbar-hover: #edebe9;
            --toolbar-active: #fce8e6;
        }

        .dark-mode {
            --primary: #ea580c;
            --primary-hover: #c43e1c;
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

        /* Ribbon Content Toolbars */
        .ribbon-content { background: #ffffff; border-bottom: 1px solid var(--border-color); height: 86px; display: flex; align-items: center; padding: 4px 12px; gap: 12px; overflow-x: auto; flex-shrink: 0; }
        .dark-mode .ribbon-content { background: #2b2b2b; }
        .ribbon-group { display: flex; flex-direction: column; justify-content: space-between; height: 100%; padding: 0 6px; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .ribbon-group-content { display: flex; align-items: center; gap: 4px; flex: 1; }
        .ribbon-group-label { font-size: 10px; color: #8a8886; text-align: center; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Toolbar Button */
        .tool-btn { width: 28px; height: 28px; border: 1px solid transparent; background: transparent; border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--text-color); font-size: 13px; transition: all 0.15s; }
        .tool-btn:hover { background: var(--toolbar-hover); }
        .tool-btn.active { background: var(--toolbar-active); border-color: #fca5a5; color: var(--primary); }
        .tool-btn-lg { height: 56px; padding: 4px 8px; border-radius: 4px; border: 1px solid transparent; background: transparent; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; font-size: 11px; color: var(--text-color); transition: all 0.15s; }
        .tool-btn-lg:hover { background: var(--toolbar-hover); }

        /* 16:9 Slide Canvas */
        .slide-canvas {
            width: 880px;
            height: 495px;
            background: #ffffff;
            color: #0f172a;
            position: relative;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.18);
            border-radius: 4px;
            overflow: hidden;
            transition: all 0.2s ease-in-out;
            padding: 48px;
            margin: auto;
        }

        /* Slide Thumbnails */
        .slide-thumb-card {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 6px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .slide-thumb-card:hover { background: rgba(0,0,0,0.05); }
        .slide-thumb-card.active { background: #fee7e2; }
        .slide-num { font-size: 11px; font-weight: 700; color: #8a8886; width: 14px; text-align: right; }
        .slide-thumb-card.active .slide-num { color: var(--primary); }

        .slide-thumb {
            width: 110px;
            height: 62px;
            background: #ffffff;
            color: #0f172a;
            border-radius: 4px;
            overflow: hidden;
            border: 1.5px solid #d1d1d1;
            transition: all 0.15s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4px;
            font-size: 7px;
            text-align: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        }
        .slide-thumb-card.active .slide-thumb { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(196, 62, 28, 0.3); }

        /* Fullscreen Presentation */
        .presentation-mode {
            position: fixed;
            inset: 0;
            background: #000000;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .presentation-mode .slide-canvas {
            width: 100vw;
            height: 56.25vw;
            max-height: 100vh;
            max-width: 177.78vh;
            border-radius: 0;
            box-shadow: none;
        }

        .theme-card {
            border: 2px solid #e1dfdd;
            border-radius: 6px;
            padding: 4px 8px;
            cursor: pointer;
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            transition: 0.15s;
            width: 74px;
        }
        .theme-card:hover { border-color: var(--primary); transform: translateY(-1px); }
        .theme-card.active { border-color: var(--primary); box-shadow: 0 0 0 2px rgba(196, 62, 28, 0.3); }

        @media print {
            .title-bar, .ribbon-tabs, .ribbon-content, aside, footer, #presentControls { display: none !important; }
            body { background: white !important; }
            .slide-canvas { box-shadow: none !important; margin: 0 auto 30px !important; page-break-after: always; }
        }
    </style>
</head>
<body id="pptAppBody">

    <!-- 1. Authentic PowerPoint 365 Title Bar -->
    <div class="title-bar">
        <a href="../index.php" title="Voltar ao FreeOffice" class="px-2 py-1 bg-[#c43e1c] hover:bg-[#a03014] text-white font-bold rounded flex items-center gap-1 text-xs transition-colors">
            <span>←</span> <span>FreeOffice</span>
        </a>

        <div class="flex items-center gap-1 ml-1">
            <button onclick="savePresentation()" class="tool-btn" title="Salvar (Ctrl+S)"><i class="fa-regular fa-floppy-disk text-[#c43e1c]"></i></button>
            <button onclick="document.execCommand('undo')" class="tool-btn" title="Desfazer (Ctrl+Z)"><i class="fa-solid fa-rotate-left"></i></button>
            <button onclick="document.execCommand('redo')" class="tool-btn" title="Refazer (Ctrl+Y)"><i class="fa-solid fa-rotate-right"></i></button>
        </div>

        <div class="h-4 w-px bg-gray-300 mx-1"></div>

        <!-- Auto-save Switch -->
        <div class="flex items-center gap-2 text-xs">
            <span class="text-gray-500 font-medium">Salvamento Automático</span>
            <div class="w-8 h-4 bg-[#c43e1c] rounded-full relative cursor-pointer transition-all">
                <div class="w-3 h-3 bg-white rounded-full absolute top-0.5 right-0.5 transition-all"></div>
            </div>
        </div>

        <!-- Title Input -->
        <div class="flex items-center gap-1.5 ml-2 font-semibold text-xs text-gray-700">
            <i class="fa-solid fa-file-powerpoint text-[#c43e1c] text-sm"></i>
            <input type="text" id="pptTitleInput" value="Apresentação1" onchange="handleTitleChange(this.value)" class="bg-transparent hover:bg-black/5 focus:bg-white px-2 py-0.5 rounded border border-transparent focus:border-[#c43e1c] outline-none text-xs font-semibold max-w-[150px]">
            <span id="saveStatusBadge" class="text-[11px] text-green-600 font-normal ml-1">Salvo</span>
        </div>

        <!-- Search Box -->
        <div class="flex-1 max-w-sm mx-auto flex items-center bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded px-2.5 py-1 gap-2">
            <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
            <input type="text" placeholder="Pesquisar nos slides..." onkeydown="if(event.key==='Enter') window.find(this.value)" class="bg-transparent border-none outline-none text-xs w-full text-gray-700 dark:text-gray-200">
        </div>

        <!-- Right Quick Actions -->
        <div class="flex items-center gap-2 ml-auto">
            <button onclick="startPresentation()" class="px-3 py-1 bg-[#c43e1c] hover:bg-[#a03014] text-white rounded text-xs font-bold flex items-center gap-1 shadow-sm">
                <i class="fa-solid fa-play"></i> <span>Apresentar (F5)</span>
            </button>
            <button onclick="window.print()" class="px-2.5 py-1 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded text-xs font-semibold flex items-center gap-1 shadow-sm">
                <i class="fa-solid fa-file-pdf text-red-600"></i> <span>PDF</span>
            </button>
            <div class="w-6 h-6 rounded-full bg-[#c43e1c] text-white text-[11px] font-bold flex items-center justify-center">
                U
            </div>
        </div>
    </div>

    <!-- 2. Authentic Ribbon Tabs -->
    <div class="ribbon-tabs">
        <button class="ribbon-tab file-btn" onclick="openFileDrawer()"><i class="fa-solid fa-bars mr-1"></i> Arquivo</button>
        <button class="ribbon-tab active" data-tab="home" onclick="switchRibbonTab('home')">Página Inicial</button>
        <button class="ribbon-tab" data-tab="insert" onclick="switchRibbonTab('insert')">Inserir</button>
        <button class="ribbon-tab" data-tab="design" onclick="switchRibbonTab('design')">Design</button>
        <button class="ribbon-tab" data-tab="slideshow" onclick="switchRibbonTab('slideshow')">Apresentação de Slides</button>
        <button class="ribbon-tab" data-tab="view" onclick="switchRibbonTab('view')">Exibir</button>
    </div>

    <!-- 3. Ribbon Content Toolbars -->
    <div class="ribbon-content">
        
        <!-- TAB: PÁGINA INICIAL -->
        <div id="tab-home" class="flex items-center gap-2 h-full">
            
            <!-- Slides Group -->
            <div class="ribbon-group">
                <div class="ribbon-group-content">
                    <button onclick="addNewSlide()" class="tool-btn-lg" title="Novo Slide (Ctrl+M)">
                        <i class="fa-solid fa-file-circle-plus text-[#c43e1c] text-lg"></i>
                        <span>Novo Slide</span>
                    </button>
                    <button onclick="duplicateCurrentSlide()" class="tool-btn-lg" title="Duplicar Slide">
                        <i class="fa-regular fa-copy text-blue-600 text-lg"></i>
                        <span>Duplicar</span>
                    </button>
                    <button onclick="deleteCurrentSlide()" class="tool-btn-lg" title="Excluir Slide">
                        <i class="fa-regular fa-trash-can text-red-600 text-lg"></i>
                        <span>Excluir</span>
                    </button>
                </div>
                <span class="ribbon-group-label">Slides</span>
            </div>

            <!-- Font -->
            <div class="ribbon-group">
                <div class="ribbon-group-content flex-col justify-center gap-1">
                    <div class="flex items-center gap-1.5">
                        <select onchange="formatDoc('fontName', this.value)" class="text-xs border border-gray-300 dark:border-gray-600 rounded px-2 py-0.5 bg-transparent outline-none w-32 font-medium">
                            <option value="Segoe UI">Segoe UI</option>
                            <option value="Arial">Arial</option>
                            <option value="Calibri">Calibri</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Impact">Impact</option>
                        </select>
                        <select onchange="formatDoc('fontSize', this.value)" class="text-xs border border-gray-300 dark:border-gray-600 rounded px-1.5 py-0.5 bg-transparent outline-none w-14 font-medium">
                            <option value="2">14pt</option>
                            <option value="3">18pt</option>
                            <option value="4">24pt</option>
                            <option value="5" selected>32pt</option>
                            <option value="6">44pt</option>
                            <option value="7">60pt</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="formatDoc('bold')" class="tool-btn font-bold" title="Negrito"><b>B</b></button>
                        <button onclick="formatDoc('italic')" class="tool-btn italic" title="Itálico"><i>I</i></button>
                        <button onclick="formatDoc('underline')" class="tool-btn underline" title="Sublinhado"><u>U</u></button>
                        <div class="h-4 w-px bg-gray-300 mx-0.5"></div>
                        <label class="tool-btn cursor-pointer" title="Cor da Fonte">
                            <i class="fa-solid fa-font text-red-600"></i>
                            <input type="color" onchange="formatDoc('foreColor', this.value)" class="hidden">
                        </label>
                        <label class="tool-btn cursor-pointer" title="Cor de Fundo">
                            <i class="fa-solid fa-fill-drip text-yellow-500"></i>
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
                        <button onclick="formatDoc('insertUnorderedList')" class="tool-btn" title="Marcadores"><i class="fa-solid fa-list-ul"></i></button>
                        <button onclick="formatDoc('insertOrderedList')" class="tool-btn" title="Numeração"><i class="fa-solid fa-list-ol"></i></button>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="formatDoc('justifyLeft')" class="tool-btn" title="Esquerda"><i class="fa-solid fa-align-left"></i></button>
                        <button onclick="formatDoc('justifyCenter')" class="tool-btn" title="Centro"><i class="fa-solid fa-align-center"></i></button>
                        <button onclick="formatDoc('justifyRight')" class="tool-btn" title="Direita"><i class="fa-solid fa-align-right"></i></button>
                    </div>
                </div>
                <span class="ribbon-group-label">Parágrafo</span>
            </div>

            <!-- Drawing & Insertion -->
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="addTextBoxToSlide()" class="tool-btn-lg" title="Caixa de Texto">
                        <i class="fa-solid fa-font text-blue-600 text-lg"></i>
                        <span>Caixa Texto</span>
                    </button>
                    <label class="tool-btn-lg cursor-pointer" title="Inserir Imagem">
                        <i class="fa-regular fa-image text-green-600 text-lg"></i>
                        <span>Imagem</span>
                        <input type="file" accept="image/*" class="hidden" onchange="insertImageToSlide(event)">
                    </label>
                    <button onclick="insertShape('rect')" class="tool-btn-lg" title="Retângulo">
                        <i class="fa-regular fa-square text-orange-600 text-lg"></i>
                        <span>Formas</span>
                    </button>
                </div>
                <span class="ribbon-group-label">Desenho</span>
            </div>

        </div>

        <!-- TAB: INSERIR -->
        <div id="tab-insert" class="hidden items-center gap-2 h-full">
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="addNewSlide()" class="tool-btn-lg"><i class="fa-solid fa-file-circle-plus text-[#c43e1c] text-lg"></i><span>Novo Slide</span></button>
                    <button onclick="addTextBoxToSlide()" class="tool-btn-lg"><i class="fa-solid fa-font text-blue-600 text-lg"></i><span>Texto</span></button>
                    <label class="tool-btn-lg cursor-pointer"><i class="fa-regular fa-image text-green-600 text-lg"></i><span>Imagem</span><input type="file" accept="image/*" class="hidden" onchange="insertImageToSlide(event)"></label>
                    <button onclick="insertShape('circle')" class="tool-btn-lg"><i class="fa-regular fa-circle text-purple-600 text-lg"></i><span>Círculo</span></button>
                    <button onclick="insertTable()" class="tool-btn-lg"><i class="fa-solid fa-table text-blue-600 text-lg"></i><span>Tabela</span></button>
                </div>
                <span class="ribbon-group-label">Elementos de Slide</span>
            </div>
        </div>

        <!-- TAB: DESIGN -->
        <div id="tab-design" class="hidden items-center gap-2 h-full">
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="applySlideTheme('light')" class="theme-card active bg-white text-gray-800">Claro</button>
                    <button onclick="applySlideTheme('dark')" class="theme-card bg-slate-900 text-white">Dark Pro</button>
                    <button onclick="applySlideTheme('blue')" class="theme-card bg-blue-800 text-white">Blue</button>
                    <button onclick="applySlideTheme('orange')" class="theme-card bg-orange-700 text-white">Orange</button>
                    <button onclick="applySlideTheme('emerald')" class="theme-card bg-emerald-800 text-white">Emerald</button>
                </div>
                <span class="ribbon-group-label">Temas da Apresentação</span>
            </div>
        </div>

        <!-- TAB: APRESENTAÇÃO DE SLIDES -->
        <div id="tab-slideshow" class="hidden items-center gap-2 h-full">
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="selectSlide(0); startPresentation();" class="tool-btn-lg"><i class="fa-solid fa-play text-green-600 text-lg"></i><span>Do Começo</span></button>
                    <button onclick="startPresentation();" class="tool-btn-lg"><i class="fa-solid fa-forward-step text-blue-600 text-lg"></i><span>Slide Atual</span></button>
                </div>
                <span class="ribbon-group-label">Iniciar Apresentação</span>
            </div>
        </div>

        <!-- TAB: EXIBIR -->
        <div id="tab-view" class="hidden items-center gap-2 h-full">
            <div class="ribbon-group">
                <div class="ribbon-group-content gap-2">
                    <button onclick="toggleDarkMode()" class="tool-btn-lg"><i class="fa-solid fa-circle-half-stroke text-lg"></i><span>Modo Escuro</span></button>
                </div>
                <span class="ribbon-group-label">Exibição</span>
            </div>
        </div>

    </div>

    <!-- 4. Main Workspace (Left Sidebar Thumbnails + Center Slide Canvas) -->
    <div class="flex-1 flex overflow-hidden">
        
        <!-- Left Slides Sidebar Navigator -->
        <aside class="w-48 bg-[#faf9f8] dark:bg-[#1f1f1f] border-r border-gray-300 dark:border-gray-800 p-3 flex flex-col justify-between z-20">
            <div class="space-y-3">
                <div class="flex justify-between items-center text-xs text-gray-500 font-bold uppercase tracking-wider">
                    <span>Slides (<span id="slideCountBadge">1</span>)</span>
                    <button onclick="addNewSlide()" class="text-[#c43e1c] hover:text-orange-700 text-base font-extrabold" title="Adicionar Slide">+</button>
                </div>

                <div id="slidesThumbList" class="space-y-2 max-h-[calc(100vh-180px)] overflow-y-auto pr-1">
                    <!-- Slides gerados via JS -->
                </div>
            </div>

            <button onclick="addNewSlide()" class="w-full py-2 bg-orange-50 text-[#c43e1c] border border-orange-300 hover:bg-[#c43e1c] hover:text-white rounded-lg text-xs font-bold transition-all flex items-center justify-center gap-1">
                <i class="fa-solid fa-plus"></i> <span>Novo Slide</span>
            </button>
        </aside>

        <!-- Center Slide Editor Canvas -->
        <main class="flex-1 p-8 overflow-auto flex items-center justify-center relative select-text">
            <div id="activeSlideCanvas" class="slide-canvas">
                <div contenteditable="true" class="text-4xl font-extrabold mb-4 outline-none text-[#c43e1c]" oninput="handleSlideContentChange()">Título da Apresentação</div>
                <div contenteditable="true" class="text-lg text-gray-600 outline-none" oninput="handleSlideContentChange()">Clique para adicionar um subtítulo profissional</div>
            </div>
        </main>
    </div>

    <!-- 5. Bottom Status Bar -->
    <footer class="bg-gray-100 dark:bg-gray-900 border-t border-gray-300 dark:border-gray-800 px-4 py-1 text-xs text-gray-600 dark:text-gray-400 flex justify-between items-center select-none z-30">
        <div class="flex items-center gap-4">
            <span id="statusBarSlideIndex"><i class="fa-solid fa-sliders mr-1"></i> Slide 1 de 1</span>
            <span><i class="fa-solid fa-globe mr-1"></i> Português (Brasil)</span>
        </div>
        <div class="flex items-center gap-4">
            <span id="autoSaveIndicator" class="text-green-600 dark:text-green-400 font-semibold"><i class="fa-solid fa-cloud-arrow-up mr-1"></i> Salvo Localmente</span>
            <button onclick="startPresentation()" class="hover:text-[#c43e1c] font-semibold"><i class="fa-solid fa-expand mr-1"></i> Apresentação de Slides</button>
        </div>
    </footer>

    <!-- Fullscreen Presentation Controls -->
    <div id="presentControls" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 bg-black/80 backdrop-blur text-white px-4 py-2 rounded-full z-50 flex items-center gap-4 text-xs font-semibold">
        <button onclick="prevSlide()" class="hover:text-orange-400">◀ Anterior</button>
        <span id="presentSlideIndicator">1 / 1</span>
        <button onclick="nextSlide()" class="hover:text-orange-400">Próximo ▶</button>
        <div class="h-3 w-px bg-gray-600"></div>
        <button onclick="stopPresentation()" class="hover:text-red-400 font-bold">✕ Sair (ESC)</button>
    </div>

    <!-- File Menu Drawer -->
    <div id="fileDrawerModal" class="fixed inset-0 bg-black/60 hidden z-50" onclick="closeFileDrawer()">
        <div class="w-80 h-full bg-[#c43e1c] text-white p-6 shadow-2xl flex flex-col justify-between" onclick="event.stopPropagation()">
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 pb-4 border-b border-orange-400/30">
                    <i class="fa-solid fa-file-powerpoint text-3xl"></i>
                    <div>
                        <h2 class="font-bold text-lg leading-tight">PowerPoint Online</h2>
                        <span class="text-xs text-orange-200">FreeOffice Suite</span>
                    </div>
                </div>

                <div class="space-y-1">
                    <button onclick="resetPresentation(); closeFileDrawer();" class="w-full text-left px-3 py-2.5 hover:bg-[#a03014] rounded-lg text-sm font-semibold flex items-center gap-3"><i class="fa-regular fa-file"></i> Nova Apresentação</button>
                    <button onclick="savePresentation(); closeFileDrawer();" class="w-full text-left px-3 py-2.5 hover:bg-[#a03014] rounded-lg text-sm font-semibold flex items-center gap-3"><i class="fa-regular fa-floppy-disk"></i> Salvar Agora</button>
                    <button onclick="startPresentation(); closeFileDrawer();" class="w-full text-left px-3 py-2.5 hover:bg-[#a03014] rounded-lg text-sm font-semibold flex items-center gap-3"><i class="fa-solid fa-play"></i> Iniciar Apresentação</button>
                    <button onclick="window.print(); closeFileDrawer();" class="w-full text-left px-3 py-2.5 hover:bg-[#a03014] rounded-lg text-sm font-semibold flex items-center gap-3"><i class="fa-solid fa-print"></i> Exportar para PDF</button>
                </div>
            </div>

            <div class="pt-4 border-t border-orange-400/30 text-xs text-orange-200">
                FreeOffice &copy; 2026. Todos os direitos reservados.
            </div>
        </div>
    </div>

    <script>
        let slides = [
            {
                id: 1,
                theme: 'light',
                html: '<div contenteditable="true" class="text-4xl font-extrabold mb-4 outline-none text-[#c43e1c]" oninput="handleSlideContentChange()">Título da Apresentação</div><div contenteditable="true" class="text-lg text-gray-600 outline-none" oninput="handleSlideContentChange()">Clique para adicionar um subtítulo profissional</div>'
            }
        ];
        let currentSlideIndex = 0;

        function switchRibbonTab(tabName) {
            document.querySelectorAll('.ribbon-tab').forEach(t => t.classList.remove('active'));
            const activeTab = document.querySelector(`.ribbon-tab[data-tab="${tabName}"]`);
            if (activeTab) activeTab.classList.add('active');

            ['home', 'insert', 'design', 'slideshow', 'view'].forEach(t => {
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
        }

        function formatDoc(cmd, value = null) {
            document.execCommand(cmd, false, value);
            handleSlideContentChange();
        }

        function renderThumbnails() {
            const list = document.getElementById('slidesThumbList');
            list.innerHTML = '';
            document.getElementById('slideCountBadge').textContent = slides.length;
            document.getElementById('statusBarSlideIndex').innerHTML = `<i class="fa-solid fa-sliders mr-1"></i> Slide ${currentSlideIndex + 1} de ${slides.length}`;

            slides.forEach((slide, idx) => {
                const card = document.createElement('div');
                card.className = `slide-thumb-card ${idx === currentSlideIndex ? 'active' : ''}`;
                card.onclick = () => selectSlide(idx);

                card.innerHTML = `
                    <span class="slide-num">${idx + 1}</span>
                    <div class="slide-thumb" style="${getThemeStyles(slide.theme)}">
                        <div style="transform: scale(0.25); transform-origin: center center; width: 340px; pointer-events: none;">
                            ${slide.html}
                        </div>
                    </div>
                `;
                list.appendChild(card);
            });
        }

        function getThemeStyles(theme) {
            switch(theme) {
                case 'dark': return 'background: #0f172a; color: #f8fafc;';
                case 'blue': return 'background: linear-gradient(135deg, #1e3a8a, #0284c7); color: #ffffff;';
                case 'orange': return 'background: linear-gradient(135deg, #9a3412, #ea580c); color: #ffffff;';
                case 'emerald': return 'background: linear-gradient(135deg, #064e3b, #059669); color: #ffffff;';
                default: return 'background: #ffffff; color: #0f172a;';
            }
        }

        function selectSlide(idx) {
            currentSlideIndex = idx;
            const slide = slides[idx];
            const canvas = document.getElementById('activeSlideCanvas');
            canvas.style = getThemeStyles(slide.theme);
            canvas.innerHTML = slide.html;
            renderThumbnails();
        }

        function addNewSlide() {
            const newId = Date.now();
            slides.push({
                id: newId,
                theme: slides[currentSlideIndex].theme || 'light',
                html: '<div contenteditable="true" class="text-3xl font-bold mb-3 outline-none" oninput="handleSlideContentChange()">Novo Slide</div><div contenteditable="true" class="text-base text-gray-600 outline-none" oninput="handleSlideContentChange()">Adicione tópicos, dados ou gráficos...</div>'
            });
            selectSlide(slides.length - 1);
        }

        function duplicateCurrentSlide() {
            const current = slides[currentSlideIndex];
            slides.splice(currentSlideIndex + 1, 0, {
                id: Date.now(),
                theme: current.theme,
                html: current.html
            });
            selectSlide(currentSlideIndex + 1);
        }

        function deleteCurrentSlide() {
            if (slides.length <= 1) {
                alert("A apresentação deve conter pelo menos 1 slide.");
                return;
            }
            slides.splice(currentSlideIndex, 1);
            selectSlide(Math.max(0, currentSlideIndex - 1));
        }

        function handleSlideContentChange() {
            const canvas = document.getElementById('activeSlideCanvas');
            slides[currentSlideIndex].html = canvas.innerHTML;
            renderThumbnails();
            savePresentation();
        }

        function applySlideTheme(theme) {
            slides[currentSlideIndex].theme = theme;
            document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('active'));
            const activeCard = document.querySelector(`.theme-card[onclick*="${theme}"]`);
            if (activeCard) activeCard.classList.add('active');
            selectSlide(currentSlideIndex);
            savePresentation();
        }

        function addTextBoxToSlide() {
            const canvas = document.getElementById('activeSlideCanvas');
            const newBox = document.createElement('div');
            newBox.contentEditable = "true";
            newBox.className = "text-base p-2 border border-dashed border-gray-300 hover:border-orange-500 rounded my-2 outline-none";
            newBox.textContent = "Novo bloco de texto editável...";
            newBox.oninput = handleSlideContentChange;
            canvas.appendChild(newBox);
            newBox.focus();
            handleSlideContentChange();
        }

        function insertShape(shape) {
            const canvas = document.getElementById('activeSlideCanvas');
            const el = document.createElement('div');
            if (shape === 'circle') {
                el.className = "w-24 h-24 rounded-full bg-gradient-to-tr from-orange-500 to-amber-400 my-2 shadow-md";
            } else {
                el.className = "w-32 h-20 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-500 my-2 shadow-md";
            }
            canvas.appendChild(el);
            handleSlideContentChange();
        }

        function insertTable() {
            const canvas = document.getElementById('activeSlideCanvas');
            const tableHtml = `
                <table style="width:100%; border-collapse:collapse; margin:12px 0; border:1px solid #cbd5e1;">
                    <tr style="background:#f1f5f9; font-weight:bold;">
                        <th style="border:1px solid #cbd5e1; padding:6px;">Item</th>
                        <th style="border:1px solid #cbd5e1; padding:6px;">Qtd</th>
                        <th style="border:1px solid #cbd5e1; padding:6px;">Valor</th>
                    </tr>
                    <tr>
                        <td style="border:1px solid #cbd5e1; padding:6px;">A</td>
                        <td style="border:1px solid #cbd5e1; padding:6px;">10</td>
                        <td style="border:1px solid #cbd5e1; padding:6px;">R$ 100</td>
                    </tr>
                </table>
            `;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = tableHtml;
            canvas.appendChild(wrapper);
            handleSlideContentChange();
        }

        function insertImageToSlide(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const canvas = document.getElementById('activeSlideCanvas');
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = "max-h-48 rounded shadow-md my-3 border border-gray-200";
                canvas.appendChild(img);
                handleSlideContentChange();
            };
            reader.readAsDataURL(file);
        }

        function handleTitleChange(val) {
            document.title = `${val} - PowerPoint Online`;
            savePresentation();
        }

        function savePresentation() {
            try {
                localStorage.setItem('freeppt_slides', JSON.stringify(slides));
                const title = document.getElementById('pptTitleInput').value;
                localStorage.setItem('freeppt_title', title);
                document.getElementById('saveStatusBadge').textContent = "Salvo";
            } catch(e){}
        }

        function resetPresentation() {
            if (confirm("Deseja iniciar uma nova apresentação?")) {
                localStorage.removeItem('freeppt_slides');
                slides = [{
                    id: 1,
                    theme: 'light',
                    html: '<div contenteditable="true" class="text-4xl font-extrabold mb-4 outline-none text-[#c43e1c]" oninput="handleSlideContentChange()">Título da Apresentação</div><div contenteditable="true" class="text-lg text-gray-600 outline-none" oninput="handleSlideContentChange()">Clique para adicionar um subtítulo profissional</div>'
                }];
                selectSlide(0);
            }
        }

        function startPresentation() {
            document.body.classList.add('presentation-mode');
            document.getElementById('presentControls').classList.remove('hidden');
            document.getElementById('presentControls').classList.add('flex');
            updatePresentationIndicator();
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen().catch(()=>{});
            }
        }

        function stopPresentation() {
            document.body.classList.remove('presentation-mode');
            document.getElementById('presentControls').classList.add('hidden');
            document.getElementById('presentControls').classList.remove('flex');
            if (document.exitFullscreen) {
                document.exitFullscreen().catch(()=>{});
            }
        }

        function nextSlide() {
            if (currentSlideIndex < slides.length - 1) {
                selectSlide(currentSlideIndex + 1);
                updatePresentationIndicator();
            }
        }

        function prevSlide() {
            if (currentSlideIndex > 0) {
                selectSlide(currentSlideIndex - 1);
                updatePresentationIndicator();
            }
        }

        function updatePresentationIndicator() {
            document.getElementById('presentSlideIndicator').textContent = `${currentSlideIndex + 1} / ${slides.length}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem('freeppt_slides');
            if (saved) {
                try {
                    slides = JSON.parse(saved);
                } catch(e){}
            }
            const savedTitle = localStorage.getItem('freeppt_title');
            if (savedTitle) {
                document.getElementById('pptTitleInput').value = savedTitle;
                document.title = `${savedTitle} - PowerPoint Online`;
            }
            selectSlide(0);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'F5') { e.preventDefault(); startPresentation(); }
            if (e.key === 'Escape' && document.body.classList.contains('presentation-mode')) stopPresentation();
            if (document.body.classList.contains('presentation-mode')) {
                if (e.key === 'ArrowRight' || e.key === ' ') nextSlide();
                if (e.key === 'ArrowLeft') prevSlide();
            }
        });
    </script>
</body>
</html>
