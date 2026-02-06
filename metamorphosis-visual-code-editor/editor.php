<?php
session_start();

// Konfiguration
$user_admin = "admin";
$pass_admin = "admin01ß#!2bAq";
$root_dir = __DIR__;

// Login-Logik
if (isset($_POST['login'])) {
    if ($_POST['user'] === $user_admin && $_POST['pw'] === $pass_admin) {
        $_SESSION['loggedin'] = true;
        header("Location: ?");
        exit;
    } else {
        $error = "Falsche Anmeldedaten!";
    }
}

// Logout
if (isset($_GET['logout'])) { 
    session_destroy(); 
    header("Location: ?"); 
    exit; 
}

// Neue Datei erstellen
if (isset($_POST['new_file']) && isset($_POST['filename'])) {
    $filename = basename($_POST['filename']);
    if (!empty($filename)) {
        if (!strpos($filename, '.')) {
            $filename .= '.php'; // Standard-Erweiterung
        }
        if (!file_exists($filename)) {
            file_put_contents($filename, "<?php\n// Neue Datei\n?>");
            $_SESSION['last_file'] = $filename;
            header("Location: ?edit=" . urlencode($filename));
            exit;
        }
    }
}

// Datei löschen
if (isset($_POST['delete_file']) && isset($_POST['filepath'])) {
    $filepath = $_POST['filepath'];
    if (file_exists($filepath) && $filepath !== 'editor.php' && $filepath !== '.htaccess') {
        if (unlink($filepath)) {
            // Auch die gespeicherten Editor-Daten löschen
            if (isset($_SESSION['editor_states'])) {
                unset($_SESSION['editor_states'][$filepath]);
            }
            $_SESSION['last_file'] = '';
            header("Location: ?");
            exit;
        }
    }
}

if (!isset($_SESSION['loggedin'])) {
    die('
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Metamorphosis Live Code-Editor</title>
        <link rel="icon" type="image/x-icon" href="https://metamorphosis.live/images/logo.png">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
			body {
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
				background: linear-gradient(135deg, #2e2e2e 0%, #252526 100%);
				height: 100vh;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #fff;
			}
            .login-container {
                width: 100%;
                max-width: 400px;
                padding: 20px;
            }
            .login-card {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
               border-radius: 5px;
                padding: 40px 30px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                animation: fadeIn 0.6s ease-out;
            }
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .logo-section {
                text-align: center;
                margin-bottom: 30px;
            }
			.logo-image {
				width: 200px;
				height: auto;
				margin: 0 auto 15px;
			}
            .logo-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
            .logo-text {
                font-size: 24px;
                font-weight: 300;
                letter-spacing: 2px;
                color: #4fc1ff;
                margin-bottom: 5px;
            }
			.logo-subtext {
				font-size: 18px;
				color: #b7b7b7;
				margin-bottom: 10px;
			}
            .input-group {
                margin-bottom: 15px;
            }
            .input-group label {
                display: block;
                margin-bottom: 8px;
                color: #a0a0c0;
                font-size: 14px;
            }
            .input-group input {
                width: 100%;
                padding: 15px;
                background: rgba(255, 255, 255, 0.07);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 5px;
                color: #fff;
                font-size: 16px;
            }
            .input-group input:focus {
                outline: none;
                border-color: #4fc1ff;
                box-shadow: 0 0 0 2px rgba(79, 193, 255, 0.2);
            }
            .error {
                background: rgba(255, 68, 68, 0.1);
                border: 1px solid rgba(255, 68, 68, 0.3);
                color: #ff6b6b;
                padding: 12px;
                border-radius: 5px;
                margin-bottom: 20px;
                text-align: center;
                font-size: 14px;
            }
            .login-btn {
                width: 100%;
                padding: 16px;
                background: linear-gradient(135deg, #4fc1ff 0%, #2d8bff 100%);
                border: none;
                border-radius: 5px;
                color: white;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                margin-top: 20px;
            }
            .login-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(79, 193, 255, 0.3);
            }
            .login-btn:active {
                transform: translateY(0);
            }
            .footer {
                text-align: center;
                margin-top: 30px;
                color: #fff;
                font-size: 12px;
                opacity: 0.7;
            }
			.footer a {
				font-size: 15px;
				font-weight: 300;
				letter-spacing: 2px;
				color: #4fc1ff;
				text-decoration: none;
				line-height: 2;
			}
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="login-card">
                <div class="logo-section">
                    <div class="logo-image">
                        <img src="https://metamorphosis.live/images/logo.png" alt="Metamorphosis Logo">
                    </div>
                    <div class="logo-text">METAMORPHOSIS</div>
                    <div class="logo-subtext">🔐 Live Code-Editor</div>
                </div>
                '.(isset($error) ? '<div class="error">'.$error.'</div>' : '').'
                <form method="post">
                    <div class="input-group">
                        <label for="user">Benutzername</label>
                        <input type="text" id="user" name="user" placeholder="admin" required>
                    </div>
                    <div class="input-group">
                        <label for="pw">Passwort</label>
                        <input type="password" id="pw" name="pw" placeholder="••••••••" required>
                    </div>
                    <button type="submit" name="login" class="login-btn">Anmelden</button>
                </form>
				<div class="footer">Nur für autorisiertes Personal <a href="https://metamorphosis.live/" title="Metamorphosis Brandenburg">www.metamorphosis.live</a></div>
            </div>
        </div>
	<script>window.history.pushState({}, "", window.location.pathname);</script>
    </body>
    </html>');
}

$redirect = $_GET['redirect'];
if (isset($_GET['redirect'])) { 
    header('Location: '.$redirect);
}

// Textlänge kürzen
function truncateText($text, $length = 17) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

// Datei speichern (AJAX)
if (isset($_POST['save_file']) && isset($_POST['filepath'])) {
    if(file_put_contents($_POST['filepath'], $_POST['content'])) {
        echo "Erfolgreich gespeichert!";
    } else {
        echo "Fehler beim Schreiben!";
    }
    exit;
}

// Save editor state (AJAX)
if (isset($_POST['save_editor_state']) && isset($_POST['filepath']) && isset($_POST['state'])) {
    $filepath = $_POST['filepath'];
    $state = json_decode($_POST['state'], true);
    
    if (!isset($_SESSION['editor_states'])) {
        $_SESSION['editor_states'] = [];
    }
    
    $_SESSION['editor_states'][$filepath] = $state;
    echo "Editor state saved";
    exit;
}

// Get editor state (AJAX)
if (isset($_GET['get_editor_state']) && isset($_GET['filepath'])) {
    $filepath = $_GET['filepath'];
    
    if (isset($_SESSION['editor_states'][$filepath])) {
        echo json_encode($_SESSION['editor_states'][$filepath]);
    } else {
        echo json_encode([]);
    }
    exit;
}

// Aktuelle Datei laden (priorisiert: GET-Parameter, dann Session, dann LocalStorage via JavaScript)
$current_file = $_GET['edit'] ?? ($_SESSION['last_file'] ?? '');
$content = "";
if ($current_file && file_exists($current_file)) {
    $content = file_get_contents($current_file);
    $_SESSION['last_file'] = $current_file;
} else {
	$current_file = 'example.html';
    $content = file_get_contents('example.html');
    $_SESSION['last_file'] = 'example.html';
}

// Verzeichnis auslesen
$files = scandir($root_dir);
$files = str_replace('.htaccess', '', $files);
$files = str_replace('editor.php', '', $files);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metamorphosis Live Code-Editor</title>
    <link rel="icon" type="image/x-icon" href="https://metamorphosis.live/images/logo.png">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <!-- Ace Editor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ext-language_tools.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-html.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/mode-css.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/theme-monokai.min.js"></script>
	<script>window.history.pushState({}, "", window.location.pathname);</script>
    <style>
        :root {
            --sidebar-width: 220px;
            --splitter-width: 8px;
            --topbar-height: 45px;
            --code-size: 50%;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body, html { 
            height: 100%; 
            font-family: 'Segoe UI', system-ui, sans-serif; 
            background: #1e1e1e; 
            color: #ccc; 
            overflow: hidden;
			scrollbar-width: thin;
			scrollbar-color: #888 #f000;
			user-select: none;
		}
		
		::-webkit-scrollbar {
			width: 8px;
			height: 8px;
		}

		::-webkit-scrollbar-track {
			background: #000;
			border-radius: 5px;
		}

		::-webkit-scrollbar-thumb {
			background: #888;
			border-radius: 5px;
			border: 2px solid #000;
		}

		::-webkit-scrollbar-thumb:hover {
			background: #555;
		}
        
		#topbar {
			height: 55px;
			background: #252526;
			display: flex;
			align-items: center;
			padding: 0 15px;
			border-bottom: 1px solid #333;
			justify-content: space-between;
			position: fixed;
			z-index: 9999;
			width: 100%;
		}
        
        .topbar-left, .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .topbar-center {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-small {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
		.logo-icon {
			margin-top: 6px;
			width: 50px;
			height: auto;
		}
        
        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
		#workspace {
			display: flex;
			height: calc(100% - var(--topbar-height));
			position: fixed;
			top: 55px;
			width: 100%;
		}
        
        #sidebar { 
            width: var(--sidebar-width); 
            background: #252526; 
            border-right: 1px solid #333; 
            overflow-y: auto; 
            padding: 10px; 
            font-size: 14px;
            transition: width 0.3s ease;
			overflow-x: hidden;
        }
        
		#sidebar.collapsed {
			width: 30px;
			padding: 0;
			overflow: hidden;
			padding-top: 0px !important;
		}
        
        #sidebar.collapsed strong span {
            display: none;
        }

        #sidebar.collapsed hr {
            display: none;
        }
        
        #sidebar.collapsed a {
            text-align: center;
            padding: 8px 5px;
        }

        #sidebar.collapsed a span {
			display: none;
        }
        
        #sidebar a { 
            display: flex;
            align-items: center;
            gap: 10px;
            color: #aaa; 
            text-decoration: none; 
            padding: 8px 10px; 
            border-radius: 0px; 
            margin-bottom: 2px;
            transition: all 0.2s;
        }
        
        #sidebar a:hover { background: #37373d; color: white; }
        #sidebar .active { background: #006499; color: white; }
        #sidebar strong { 
            display: block; 
            color: #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        #main-content {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        
        #editor-container { 
            flex: 1; 
            display: flex; 
            overflow: hidden;
            position: relative;
        }
        
        /* Standard: Side-by-Side (horizontal) */
        #editor-container.horizontal {
            flex-direction: row;
        }
        
        /* Vertikal: Code oben, Vorschau unten */
        #editor-container.vertical {
            flex-direction: column;
        }
        
        #editor-wrapper, #preview-wrapper {
            position: relative;
            min-width: 100px;
            min-height: 100px;
        }
        
        /* Größen für horizontales Layout */
		#editor-wrapper {
			height: calc(100vh - 55px) !important;
		}

        .horizontal #editor-wrapper {
            width: var(--code-size);
            height: 100%;
        }
		
		#preview-wrapper {
			margin-left: -15px;
		}
		
		.horizontal #preview-wrapper {
			height: calc(100vh - 55px) !important;
		}
        
        .horizontal #preview-wrapper {
            width: calc(100% - var(--code-size));
            height: 100%;
            position: relative;
        }
        
        /* Größen für vertikales Layout */
        .vertical #editor-wrapper {
            width: 100%;
            height: var(--code-size) !important;
        }
        
        .vertical #preview-wrapper {
            width: 100%;
            height: calc(100% - var(--code-size));
            position: relative;
        }
        
        #editor { 
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
			display: none;
        }
        
        #preview-frame { 
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white; 
            border: none;
        }
        
        /* Splitter Styling - FIXED */
		.splitter {
			position: relative;
			z-index: 1000;
			transition: background-color 0.2s;
			user-select: none;
			touch-action: none;
			padding: 10px;
		}
        
        /* Splitter für horizontales Layout */
        .horizontal .splitter {
            width: var(--splitter-width);
            height: 100%;
            cursor: col-resize;
            background: #272727;
        }
        
        /* Splitter für vertikales Layout */
		.vertical .splitter {
			width: 100%;
			height: var(--splitter-width);
			cursor: row-resize;
			background: #272727;
		}
        
		.splitter:hover, .splitter.dragging {
			background: linear-gradient(135deg, #4fc1ffcc 0%, #006195 100%) !important;
		}
        
        /* Pixel-Anzeige auf Splittern - DRITT für horizontales Layout */
        .splitter-size-display {
            position: absolute;
            color: white;
            font-size: 11px;
            padding: 3px 6px;
            border-radius: 3px;
            white-space: nowrap;
            pointer-events: none;
            z-index: 1001;
            font-family: 'Consolas', monospace;
            transform-origin: center;
        }
        
        /* Horizontal: Schrift um 90 Grad nach links drehen */
        .horizontal #main-splitter .splitter-size-display {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
        }
        
        /* Vertikal: Normale Schrift */
        .vertical #main-splitter .splitter-size-display {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(0deg);
        }
        
        /* Geräte-Auswahl Dropdown */
		.device-selector {
			position: absolute;
			top: 15px;
			right: 15px;
			z-index: 1000;
			background: rgba(37, 37, 38, 0.95);
			border: 1px solid #333;
			border-radius: 5px;
			padding: 7px 7px;
			display: flex;
			align-items: center;
			gap: 8px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
		}
        
		.device-selector select {
			background: #1e1e1e;
			color: #ccc;
			border: 1px solid #333;
			padding: 6px 5px;
			border-radius: 5px;
			font-size: 12px;
			max-width: 202px;
			cursor: pointer;
		}
        
        .device-selector select:focus {
            outline: none;
            border-color: #4fc1ff;
        }
        
        .device-selector .device-icon {
            color: #4fc1ff;
            font-size: 16px;
            transition: transform 0.3s ease;
        }
        
        .device-selector .device-icon.rotated {
            transform: rotate(90deg);
        }
        
		.device-selector .size-display {
			text-align: center;
			background: #1e1e1e;
			color: #ccc;
			border: 1px solid #333;
			padding: 6px 10px;
			border-radius: 5px;
			font-size: 12px;
		}
        
        /* Einfache Search Box */
		.search-box {
			position: fixed;
			z-index: 999999;
			top: 54px;
			right: -1px;
			background: #252526;
			border: 1px solid #333;
			border-bottom-left-radius: 5px;
			padding: 15px;
			padding-right: 15px;
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
			z-index: 1000;
			display: flex;
			gap: 10px;
			align-items: center;
			opacity: 0;
			transform: translateY(-10px);
			transition: all 0.2s;
			pointer-events: none;
		}  
        
        .search-box.active {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }
        
		.search-input {
			width: 195px;
			padding: 6px 12px;
			background: #1e1e1e;
			border: 1px solid #3a3a3a;
			color: white;
			border-radius: 5px;
			font-size: 14px;
			font-family: 'Consolas', monospace;
		}
        
        .search-input:focus {
            outline: none;
            border-color: #3a3a3a;
        }
        
		.search-btn {
			padding: 5px 5px;
			font-size: 13px;
			background: linear-gradient(135deg, #4fc1ffcc 0%, #0652b0ba 100%);
			border: none;
			border-radius: 5px;
			color: white;
			cursor: pointer;
			white-space: nowrap;
			border: 1px solid #3a3a3a;
			width: 29px;
			height: 29px;
		}
        
        .search-btn:hover {
            background: #444;
        }
        
		.search-close {
			background: transparent;
			border: 1px solid #3a3a3a;
			color: #ffffff;
			font-size: 18px;
			cursor: pointer;
			padding: 2px;
			padding-left: 8px;
			padding-right: 8px;
			width: 29px;
			height: 29px;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 5px;
			background: linear-gradient(135deg, #F44336 0%, #801a1399 100%);
		}
        
		.search-close:hover {
			background: #3e3e42;
			color: #fff;
			padding: 2px;
			padding-left: 8px;
			padding-right: 8px;
		}
        
        /* Neue Datei Modal */
        .new-file-modal, .delete-file-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
        }
        
        .new-file-modal.active, .delete-file-modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .new-file-card, .delete-file-card {
            background: #252526;
            border: 1px solid #333;
            border-radius: 5px;
            padding: 30px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        
        .new-file-card h3, .delete-file-card h3 {
            color: #4fc1ff;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .delete-file-card h3 {
            color: #F44336;
        }
        
        .delete-file-card p {
            margin-bottom: 20px;
            text-align: center;
            color: #ccc;
        }
        
        .new-file-input {
            width: 100%;
            padding: 12px;
            background: #1e1e1e;
            border: 1px solid #3a3a3a;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .new-file-input:focus {
            outline: none;
            border-color: #4fc1ff;
        }
        
        .new-file-buttons, .delete-file-buttons {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
		.new-file-btn, .delete-file-btn {
			padding: 10px 20px;
			background: linear-gradient(135deg, #4fc1ff 0%, #2d8bff 100%);
			border: none;
			border-radius: 5px;
			color: white;
			cursor: pointer;
			font-size: 15px;
			display: flex;
			align-items: center;
			justify-content: center;
		}
        
        .new-file-cancel, .delete-file-cancel {
            background: #444;
        }
        
        .delete-file-confirm {
            background: linear-gradient(135deg, #F44336 0%, #801a1399 100%);
        }
        
        /* Buttons */
		button {
			width: 31px !important;
			height: 31px !important;
			padding: 0px !important;
			padding-left: 0px;
			padding-right: 0px;
			background: linear-gradient(135deg, #4fc1ffcc 0%, #0652b0ba 100%);
			border: 1px solid #3a3a3a !important;
			border-radius: 5px;
			color: white;
			font-size: 12px;
			font-weight: 400;
			cursor: pointer;
			margin-top: 0px;
            white-space: nowrap;
			line-height: 2.5;
        }
        
        .delete-btn {
            background: linear-gradient(135deg, #F44336 0%, #801a1399 100%) !important;
        }
        
		.iframe-click-btn {
			background: linear-gradient(135deg, #FF9800 0%, #9b5712 100%) !important;
		}        
        
		.live-edit-btn {
			background: linear-gradient(135deg, #d411f5 0%, #7B1FA2 100%) !important;
		}
        
        .live-edit-btn.active {
            background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%) !important;
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.5) !important;
        }
        
        button:hover { background: #444 !important; }
        
        .delete-btn:hover { 
            background: #444 !important; 
        }
        
        .iframe-click-btn:hover { 
            background: #444 !important; 
        }
        
        .live-edit-btn:hover { 
            background: #444 !important; 
        }
        
        .live-edit-btn.active:hover { 
            background: #444C !important; 
        }

		.save-btn {
			width: 100%;
			padding: 7px;
			padding-left: 10px;
			padding-right: 10px;
			background: linear-gradient(135deg, #4fc1ffcc 0%, #0652b0ba 100%);
			border: none;
			border-radius: 5px;
			color: white;
			font-size: 12px;
			font-weight: 400;
			cursor: pointer;
			margin-top: 0px;
			white-space: nowrap;
		}
        .save-btn:hover { background: #444; }
        
        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            background: #2196F3;
            color: white;
            padding: 15px 15px;
            border-radius: 5px;
            z-index: 1000;
            animation: opacity 0.5s ease-out;
			text-align: center;
        }
        
        @keyframes opacity {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
		.collapsed-sidebar-btn {
			background: transparent;
			border: none;
			color: #a5a5a5;
			cursor: pointer;
			padding: 2px;
			width: 30px;
			border: 0px !important;
			border-radius: 0px;
			background: #444;
			font-size: 14px;
			display: flex;
			justify-content: center;
			align-items: center;
		}
        
        .collapsed-sidebar-btn:hover {
            color: #fff;
        }
		
		.ace_scroller {
			background-color: #323232;
		}
		
		.ace-monokai .ace_gutter {
			background: #3a3a3a;
		}
		
		.horizontal .splitter {
			background: #3a3a3a;
		}
		
		.fa-regular, .far {
			font-size: 20px;
		}
		
		.VIpgJd-ZVi9od-aZ2wEe-wOHMyf {
			display: none !important;
		}
        
        @media (max-width: 950px) {
			 #sidebar {
				 position: absolute;
				 z-index: 2000;
				 height: calc(100% - var(--topbar-height));
				 box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
				 height: -webkit-fill-available;
				 font-size 12px;
             }
            
			 #sidebar.collapsed {
			     width: 30px;
                 padding: 0;
                 overflow: hidden;
             }

		     .splitter:hover, .splitter.dragging {
			     padding: 15px;
		     }

		     .collapsed-sidebar-btn:hover {
			     border: 0px !important;
			     border-radius: 0px;
		     }
			 
			#main-content {
				margin-left: 31px;
			}			 
	    }

	    @media (max-width: 667px) {		   
			.logo-small {
				width: 170px;
			}
			
			#topbar {
				padding: 0 10px;
			}			
			
			.topbar-center {
				display: none;

			}
			
			.topbar-right {
				overflow-x: auto;
				width: auto;
				scrollbar-width: none;
				gap: 7px;
			}

			button {
				padding-left: 7px !important;
				padding-right: 8px !important;
			}
			
			.icon2 {
				display: none;
			}
			
			#searchBox {
				padding-right: 10px;
				gap: 7px;
			}
		}
    </style>
</head>
<body>

<div id="topbar">
    <div class="topbar-left">
        <div class="logo-small">
            <div class="logo-icon">
                <img src="https://metamorphosis.live/images/logo.png" alt="Logo">
            </div>
            <span style="font-size: 13px; color: #4fc1ff;">
                METAMORPHOSIS <span style="color: #fff;"><i class="icon2 fa-solid fa-bolt"></i> Live Code-Editor</span>
            </span>
        </div>
    </div>
    
    <div class="topbar-center notranslate">
            <span style="font-size: 13px; color: #4fc1ff;">
                <span style="color: #fff; display: block; align-items: center; justify-content: center;">
                <i class="fa-regular fa-file-code"></i> <?= htmlspecialchars($current_file ?: 'Keine Datei ausgewählt') ?></span>
            </span>
    </div>
    
    <div class="topbar-right">
        <button class="save-btn" onclick="saveFile()"><i class="icon fas fa-save"></i></button>
        <button onclick="showNewFileModal()" title="Neue Datei"><i class="icon fa-solid fa-plus"></i></button>
        <?php if ($current_file && $current_file !== 'editor.php' && $current_file !== '.htaccess'): ?>
        <button class="delete-btn" onclick="showDeleteFileModal()" title="Datei löschen"><i class="icon fa-solid fa-trash"></i></button>
        <?php endif; ?>
        <button class="iframe-click-btn" id="iframeClickBtn" onclick="toggleIframeClickMode()" title="Element im Iframe auswählen"><i class="icon fa-solid fa-mouse-pointer"></i></button>
        <button class="live-edit-btn" id="liveEditBtn" onclick="toggleLiveEdit()" title="Live Edit - Echtzeit-Update"><i class="icon fa-solid fa-bolt"></i></button>
        <button onclick="toggleSearch()"><i class="icon fa fa-search" aria-hidden="true"></i></button>
        <button onclick="findNext()" style="display: none;"><i class="icon fa-solid fa-angle-left"></i></button>
        <button onclick="findPrev()" style="display: none;"><i class="icon fa-solid fa-angle-right"></i></button>
        <button id="layoutBtn" onclick="toggleLayout()">
            <span id="layoutIcon"><i class="fa-solid fa-left-right"></i></span>
        </button>
		<button class="fullscreen-btn" onclick="toggleFullscreen()" title="Vollbild"><i class="icon fa-solid fa-expand"></i></button>
        <button onclick="window.history.pushState({}, '', window.location.pathname); location.replace(window.location.pathname + '?logout=1');" style="background: linear-gradient(135deg, #F44336 0%, #801a1399 100%);"><i class="icon fa-solid fa-lock"></i></button>
    </div>                                                                                                               
</div>

<div id="workspace">
    <div id="sidebar">
        <strong>
            <span>Dateien</span>
            <button class="collapsed-sidebar-btn" onclick="toggleSidebar()" title="Sidebar ein/ausklappen"><i class="fa-solid fa-arrows-left-right"></i></button>
        </strong>
		<div class="notranslate">
        <hr style="border:0; border-top:1px solid #333; margin:10px 0;">
        <?php foreach($files as $f): 
            if($f == "" || $f == "." || $f == ".." || is_dir($f)) continue; 
            $active = ($current_file == $f) ? 'active' : '';
        ?>
            <a href="?edit=<?= urlencode($f) ?>" class="<?= $active ?>">
                <i class="fa-regular fa-file-code"></i> <span><?= truncateText($f) ?></span>
            </a>
        <?php endforeach; ?>
		</div>
    </div>
    
    <div id="main-content">
        <div id="editor-container" class="horizontal">
            <div id="editor-wrapper" class="notranslate">
                <div id="editor"><?= htmlspecialchars($content) ?></div>
            </div>
            
            <div class="splitter" id="main-splitter">
                <div class="splitter-size-display" id="mainSplitterSize">260px × 800px</div>
            </div>
            
            <div id="preview-wrapper">
                <!-- Geräte-Auswahl Dropdown -->
                <div class="device-selector" id="deviceSelector">
                    <i onclick="rotateDevice()" title="Landscape/Portrait umschalten" class="device-icon fa-solid fa-mobile-screen" style="cursor: pointer;" id="deviceIcon"></i>
					<select id="deviceSelect">
						<option value="responsive">Responsive</option>
						<!-- iPhones -->
						<option value="iphone15">iPhone 15 (393×852)</option>
						<option value="iphone14">iPhone 14 (390×844)</option>
						<option value="iphone13">iPhone 13 (390×844)</option>
						<option value="iphone13mini">iPhone 13 mini (375×812)</option>
						<option value="iphone12">iPhone 12 (390×844)</option>
						<option value="iphonese">iPhone SE (375×667)</option>
						<option value="iphonexr">iPhone XR (414×896)</option>
						<option value="iphonexs">iPhone XS (375×812)</option>
						<option value="iphone11">iPhone 11 (414×896)</option>
						<!-- Samsung -->
						<option value="samsung23">Samsung S23 (360×780)</option>
						<option value="samsungfold">Samsung Fold (717×868)</option>
						<option value="samsungflip">Samsung Flip (340×744)</option>
						<option value="samsunga54">Samsung A54 (384×812)</option>
						<!-- Google Pixel -->
						<option value="pixel7">Google Pixel 7 (412×915)</option>
						<option value="pixel6">Google Pixel 6 (412×824)</option>
						<option value="pixel5">Google Pixel 5 (393×851)</option>
						<!-- OnePlus -->
						<option value="oneplus11">OnePlus 11 (480×1076)</option>
						<option value="oneplusnord">OnePlus Nord (411×876)</option>
						<!-- Xiaomi & Huawei -->
						<option value="xiaomi13">Xiaomi 13 (412×915)</option>
						<option value="huaweip50">Huawei P50 (390×844)</option>
						<option value="redminote">Redmi Note (393×873)</option>
						<!-- Tablets - iPads -->
						<option value="ipadpro">iPad Pro (1024×1366)</option>
						<option value="ipadair">iPad Air (820×1180)</option>
						<option value="ipadmini">iPad mini (744×1133)</option>
						<!-- Tablets - Android -->
						<option value="samsungtab">Samsung Tab S8 (800×1280)</option>
						<option value="amazontab">Amazon Fire HD 10 (800×1280)</option>
						<option value="lenovotab">Lenovo Tab M10 (800×1280)</option>
						<option value="huaweimatepad">Huawei MatePad (1200×2000)</option>
						<!-- Tablets - Surface -->
						<option value="surfacepro">Surface Pro (1440×2160)</option>
						<option value="surfacego">Surface Go (1280×1920)</option>
						<!-- Desktops -->
						<option value="desktop">Desktop (1920×1080)</option>
						<option value="laptop">Laptop (1440×900)</option>
						<option value="ultrawide">UltraWide (2560×1080)</option>
						<option value="2k">2K (2560×1440)</option>
						<option value="4k">4K (3840×2160)</option>
					</select>
                    <div class="size-display" id="currentSizeDisplay">390×844</div>
                </div>
                
				<div style="width: calc(100% + 15px); position: relative; height: -webkit-fill-available; overflow: hidden;">
					<iframe id="preview-frame" class="notranslate" src="<?= $current_file ? $current_file.'?t='.time() : 'https://metamorphosis.live/' ?>" style="background: #000;"></iframe>
				</div>
            </div>
        </div>
    </div>
</div>

<!-- Neue Datei Modal -->
<div class="new-file-modal" id="newFileModal">
    <div class="new-file-card">
        <h3><i class="fa-regular fa-file-code"></i> Neue Datei erstellen</h3>
        <form method="post" id="newFileForm">
            <input type="text" name="filename" class="new-file-input" placeholder="Dateiname (z.B. beispiel.php)" required>
            <div class="new-file-buttons">
                <button type="button" class="new-file-btn new-file-cancel" onclick="hideNewFileModal()"><i class="icon fa-solid fa-x"></i></button>
                <button type="submit" name="new_file" class="new-file-btn"><i class="icon fa-solid fa-plus"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Datei löschen Modal -->
<div class="delete-file-modal" id="deleteFileModal">
    <div class="delete-file-card">
        <h3><i class="fa-solid fa-triangle-exclamation"></i> Datei löschen</h3>
        <p>Möchtest du die Datei <strong>"<?= htmlspecialchars($current_file) ?>"</strong> wirklich löschen?</p>
        <p style="color: #F44336; font-size: 12px;">Diese Aktion kann nicht rückgängig gemacht werden!</p>
        <form method="post" id="deleteFileForm">
            <input type="hidden" name="filepath" value="<?= htmlspecialchars($current_file) ?>">
            <div class="delete-file-buttons">
                <button type="button" class="delete-file-btn delete-file-cancel" onclick="hideDeleteFileModal()"><i class="icon fa-solid fa-x"></i></button>
                <button type="submit" name="delete_file" class="delete-file-btn delete-file-confirm"><i class="icon fa-solid fa-trash"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Einfache Search Box -->
<div class="search-box" id="searchBox">
    <input type="text" id="searchInput" class="search-input" placeholder="Suchen..." autocomplete="off">
    <button class="search-btn" onclick="findPrev()"><i class="icon fa-solid fa-angle-left"></i></button>
    <button class="search-btn" onclick="findNext()"><i class="icon fa-solid fa-angle-right"></i></button>
    <button class="search-close" onclick="toggleSearch()"><i class="icon fa-solid fa-x"></i></button>
</div>
<script>
    // Ace Editor Initialisierung
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    
    // Automatische Modus-Erkennung
    <?php 
        $ext = pathinfo($current_file, PATHINFO_EXTENSION);
        $mode = 'text';
        if ($ext == 'php') $mode = 'php';
        elseif ($ext == 'js') $mode = 'javascript';
        elseif ($ext == 'css') $mode = 'css';
        elseif ($ext == 'html' || $ext == 'htm') $mode = 'html';
        echo "editor.session.setMode('ace/mode/$mode');";
    ?>
    
    editor.setShowPrintMargin(false);
    editor.setFontSize("14px");
    
    // Erweiterte Einstellungen
    editor.setOptions({
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        enableSnippets: true,
        showLineNumbers: true,
        tabSize: 4,
        wrap: true,
        fontSize: "14px",
        fontFamily: "'Monaco', 'Menlo', 'Ubuntu Mono', monospace"
    });

    // ===== KORRIGIERTE UNDO/REDO POSITION SPEICHERUNG =====
    const currentFileName = "<?= addslashes($current_file) ?>";
    let editorHistory = JSON.parse(localStorage.getItem('editorHistory') || '{}');
    let isIframeClickMode = false;
    let isLiveEditMode = localStorage.getItem('liveEditMode') === 'true';
    let lastClickedElement = null;
    let liveEditTimeout = null;
    const liveEditBtn = document.getElementById('liveEditBtn');
    const previewFrame = document.getElementById('preview-frame');
    let isLandscape = false;
    
    // Live Edit Button initialisieren
    if (isLiveEditMode) {
        liveEditBtn.classList.add('active');
        liveEditBtn.innerHTML = '<i class="icon fa-solid fa-bolt"></i>';
        liveEditBtn.title = 'Echtzeit-Vorschau';
    }
    
    // Korrigierte Save-Funktion - speichert die EXAKTE Position
    function saveEditorState() {
        if (!currentFileName) return;
        
        const cursor = editor.getCursorPosition();
        const scrollTop = editor.renderer.getScrollTop();
        const scrollLeft = editor.renderer.getScrollLeft();
        const selection = editor.getSelection();
        const selectionRange = selection.getRange();
        
        // WICHTIG: Speichere die aktuelle Position vor Änderungen
        editorHistory[currentFileName] = {
            cursor: { 
                row: cursor.row, 
                column: cursor.column 
            },
            scroll: { 
                top: scrollTop, 
                left: scrollLeft 
            },
            selection: {
                start: {
                    row: selectionRange.start.row,
                    column: selectionRange.start.column
                },
                end: {
                    row: selectionRange.end.row,
                    column: selectionRange.end.column
                }
            },
            timestamp: Date.now(),
            // Speichere den INHALT separat
            content: editor.getValue()
        };
        
        localStorage.setItem('editorHistory', JSON.stringify(editorHistory));
        
        // Auch save to server session via AJAX
        saveEditorStateToServer();
    }
    
    // Korrigierte Load-Funktion - stellt EXAKTE Position wieder her
    function loadEditorState() {
        if (!currentFileName || !editorHistory[currentFileName]) return;
        
        const savedState = editorHistory[currentFileName];
        
        // Warte bis Editor vollständig geladen ist
        setTimeout(() => {
            // WICHTIG: Zuerst den Inhalt setzen
            if (savedState.content && savedState.content !== editor.getValue()) {
                editor.setValue(savedState.content, -1);
            }
            
            // Dann Cursor setzen
            if (savedState.cursor) {
                editor.gotoLine(savedState.cursor.row + 1, savedState.cursor.column, false);
            }
            
            // Dann Scroll-Position
            if (savedState.scroll) {
                editor.renderer.scrollToY(savedState.scroll.top);
                editor.renderer.scrollToX(savedState.scroll.left || 0);
            }
            
            // Dann Selection wiederherstellen
            if (savedState.selection) {
                const Range = ace.require('ace/range').Range;
                const range = new Range(
                    savedState.selection.start.row,
                    savedState.selection.start.column,
                    savedState.selection.end.row,
                    savedState.selection.end.column
                );
                editor.selection.setSelectionRange(range);
            }
        }, 100);
        
        // Auch load from server session
        loadEditorStateFromServer();
    }
    
    // Save to server session
    function saveEditorStateToServer() {
        if (!currentFileName) return;
        
        const cursor = editor.getCursorPosition();
        const scrollTop = editor.renderer.getScrollTop();
        const scrollLeft = editor.renderer.getScrollLeft();
        
        const state = {
            cursor: cursor,
            scroll: {
                top: scrollTop,
                left: scrollLeft
            },
            timestamp: Date.now()
        };
        
        const formData = new FormData();
        formData.append('save_editor_state', '1');
        formData.append('filepath', currentFileName);
        formData.append('state', JSON.stringify(state));
        
        fetch('', { method: 'POST', body: formData })
            .catch(error => console.error('Error saving editor state:', error));
    }
    
    // Load from server session
    function loadEditorStateFromServer() {
        if (!currentFileName) return;
        
        fetch(`?get_editor_state=1&filepath=${encodeURIComponent(currentFileName)}`)
            .then(response => response.json())
            .then(state => {
                if (state && state.cursor) {
                    setTimeout(() => {
                        editor.gotoLine(state.cursor.row + 1, state.cursor.column, false);
                        if (state.scroll) {
                            editor.renderer.scrollToY(state.scroll.top);
                            editor.renderer.scrollToX(state.scroll.left || 0);
                        }
                    }, 200);
                }
            })
            .catch(error => console.error('Error loading editor state:', error));
    }
    
    // ===== UNDO/REDO TRACKING =====
    let lastUndoPosition = null;
    let lastRedoPosition = null;
    
    // Track Undo/Redo actions
    editor.commands.on('exec', function(e) {
        if (e.command.name === 'undo' || e.command.name === 'redo') {
            // Speichere die Position NACH der Undo/Redo Aktion
            setTimeout(() => {
                const cursor = editor.getCursorPosition();
                if (e.command.name === 'undo') {
                    lastUndoPosition = cursor;
                } else {
                    lastRedoPosition = cursor;
                }
                saveEditorState();
            }, 50);
        }
    });
    
    // Auto-save editor state on changes
    let saveTimeout;
    editor.on('change', function(e) {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(saveEditorState, 1000);
        
        // Live Edit Mode
        if (isLiveEditMode) {
            clearTimeout(liveEditTimeout);
            liveEditTimeout = setTimeout(updateLivePreview, 500);
        }
    });
    
    editor.on('changeSelection', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(saveEditorState, 500);
    });
    
    // ===== LIVE EDIT FUNCTIONALITY =====
    function toggleLiveEdit() {
        isLiveEditMode = !isLiveEditMode;
        
        if (isLiveEditMode) {
            liveEditBtn.classList.add('active');
            liveEditBtn.innerHTML = '<i class="icon fa-solid fa-bolt"></i>';
            liveEditBtn.title = 'Echtzeit-Vorschau';
            localStorage.setItem('liveEditMode', 'true');
            showNotification('Echtzeit-Vorschau aktiviert', 'success');
            
            // Sofortige Aktualisierung
            updateLivePreview();
			
        } else {
            liveEditBtn.classList.remove('active');
            liveEditBtn.innerHTML = '<i class="icon fa-solid fa-bolt"></i>';
            liveEditBtn.title = 'Echtzeit-Vorschau';
            localStorage.setItem('liveEditMode', 'false');
            showNotification('Echtzeit-Vorschau deaktiviert', 'info');
        }
    }
    
    function updateLivePreview() {
        if (!isLiveEditMode || !currentFileName) return;
        
        const content = editor.getValue();
        const filepath = currentFileName;
        
        // Save to file
        const formData = new FormData();
        formData.append('save_file', '1');
        formData.append('filepath', filepath);
        formData.append('content', content);
        
        fetch('', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(msg => {
                // Update iframe with cache busting
                previewFrame.src = filepath + '?t=' + new Date().getTime();
                
                // WICHTIG: Element-Auswahl nach Neuladen wieder aktivieren
                setTimeout(() => {
                    if (isIframeClickMode) {
                        disableIframeClickMode();
                        setTimeout(() => enableIframeClickMode(), 100);
                    }
                }, 500);
            })
            .catch(error => {
                console.error('Live Edit Error:', error);
            });
    }
    
    // ===== IFRAME CLICK TO CODE - KORRIGIERTE VERSION =====
    const iframeClickBtn = document.getElementById('iframeClickBtn');
    let iframeEventListeners = [];
    
    function toggleIframeClickMode() {
        isIframeClickMode = !isIframeClickMode;
        
        if (isIframeClickMode) {
            iframeClickBtn.style.background = 'linear-gradient(135deg, #4CAF50 0%, #388E3C 100%) !important';
            iframeClickBtn.innerHTML = '<i class="icon fa-solid fa-crosshairs"></i>';
            iframeClickBtn.title = 'Element-Auswahl aktiv.';
            
            // Enable click mode in iframe
            enableIframeClickMode();
            showNotification('Element-Auswahl aktiviert.', 'info');
        } else {
            iframeClickBtn.style.background = 'linear-gradient(135deg, #FF9800 0%, #F57C00 100%) !important';
            iframeClickBtn.innerHTML = '<i class="icon fa-solid fa-mouse-pointer"></i>';
            iframeClickBtn.title = 'Element im Iframe auswählen';
            
            // Disable click mode
            disableIframeClickMode();
            showNotification('Element-Auswahl deaktiviert.', 'info');
        }
    }
    
    function enableIframeClickMode() {
        try {
            const iframeDoc = previewFrame.contentDocument || previewFrame.contentWindow.document;
            
            // Alte Event-Listener entfernen
            disableIframeClickMode();
            
            // Neue Event-Listener hinzufügen
            const clickHandler = function(event) {
                handleIframeClick(event);
            };
            
            const hoverHandler = function(event) {
                handleIframeHover(event);
            };
            
            const hoverOutHandler = function(event) {
                handleIframeHoverOut(event);
            };
            
            // Event-Listener speichern, um sie später entfernen zu können
            iframeEventListeners = [
                { type: 'click', handler: clickHandler },
                { type: 'mouseover', handler: hoverHandler },
                { type: 'mouseout', handler: hoverOutHandler }
            ];
            
            // Event-Listener hinzufügen
            iframeEventListeners.forEach(listener => {
                iframeDoc.addEventListener(listener.type, listener.handler, true);
            });
            
            // Change cursor for all elements
            iframeDoc.body.classList.add('clickable-cursor');
            
        } catch (error) {
            console.log('Iframe not accessible:', error);
            // Try again after a delay
            setTimeout(enableIframeClickMode, 100);
        }
    }
    
    function disableIframeClickMode() {
        try {
            const iframeDoc = previewFrame.contentDocument || previewFrame.contentWindow.document;
            
            // Alle gespeicherten Event-Listener entfernen
            iframeEventListeners.forEach(listener => {
                iframeDoc.removeEventListener(listener.type, listener.handler, true);
            });
            iframeEventListeners = [];
            
            // Remove hover classes
            iframeDoc.body.classList.remove('clickable-cursor');
            
            // Remove highlight from last element
            if (lastClickedElement) {
                lastClickedElement.classList.remove('element-highlight');
                lastClickedElement = null;
            }
            
        } catch (error) {
            console.log('Iframe not accessible:', error);
        }
    }
    
    function handleIframeClick(event) {
        if (!isIframeClickMode) return;
        
        event.preventDefault();
        event.stopPropagation();
        
        const element = event.target;
        lastClickedElement = element;
        
        // Highlight clicked element
        element.classList.add('element-highlight');
        
        // Get element info
        const elementInfo = getElementInfo(element);
        
        // Find element in source code
        findElementInSourceCode(elementInfo);
           
        return false;
    }
    
    function handleIframeHover(event) {
        if (!isIframeClickMode) return;
        
        const element = event.target;
        element.style.outline = '2px dashed #FF9800';
        element.style.outlineOffset = '1px';
        element.style.cursor = 'crosshair';
    }
    
    function handleIframeHoverOut(event) {
        if (!isIframeClickMode) return;
        
        const element = event.target;
        element.style.outline = '';
        element.style.outlineOffset = '';
        element.style.cursor = '';
    }
    
    function getElementInfo(element) {
        const info = {
            tagName: element.tagName.toLowerCase(),
            id: element.id,
            className: element.className,
            name: element.getAttribute('name'),
            text: element.textContent?.substring(0, 100)?.trim() || '',
            innerText: element.innerText?.substring(0, 100)?.trim() || '',
            outerHTML: element.outerHTML?.substring(0, 300) || '',
            href: element.getAttribute('href'),
            src: element.getAttribute('src'),
            alt: element.getAttribute('alt'),
            title: element.getAttribute('title'),
            value: element.value,
            type: element.getAttribute('type'),
            style: element.getAttribute('style'),
            'data-id': element.getAttribute('data-id'),
            'data-name': element.getAttribute('data-name')
        };
        
        // Try to find a unique identifier - VERBESSERTE LOGIK
        if (info.id) {
            info.selector = `#${CSS.escape(info.id)}`;
            info.primarySelector = `id="${info.id}"`;
        } else if (info.className && info.className.trim()) {
            const firstClass = info.className.split(' ')[0].trim();
            if (firstClass) {
                info.selector = `.${CSS.escape(firstClass)}`;
                info.primarySelector = `class=".*${firstClass}.*"`;
            }
        } else if (info.name) {
            info.selector = `[name="${info.name}"]`;
            info.primarySelector = `name="${info.name}"`;
        } else if (info.href) {
            info.selector = `[href="${info.href}"]`;
            info.primarySelector = `href="${info.href}"`;
        } else if (info.src) {
            info.selector = `[src="${info.src}"]`;
            info.primarySelector = `src="${info.src}"`;
        } else {
            info.selector = info.tagName;
            info.primarySelector = `<${info.tagName}`;
        }
        
        return info;
    }
    
    function findElementInSourceCode(elementInfo) {
        const sourceCode = editor.getValue();
        const lines = sourceCode.split('\n');
        
        let bestMatch = { line: -1, score: 0, content: '' };
        const searchPatterns = [];
        
        // 1. PRIORITÄT: Eindeutige Identifikatoren
        if (elementInfo.id) {
            searchPatterns.push({
                pattern: `id="${elementInfo.id}"`,
                score: 10
            });
            searchPatterns.push({
                pattern: `id='${elementInfo.id}'`,
                score: 10
            });
        }
        
        // 2. PRIORITÄT: Name-Attribute
        if (elementInfo.name) {
            searchPatterns.push({
                pattern: `name="${elementInfo.name}"`,
                score: 9
            });
            searchPatterns.push({
                pattern: `name='${elementInfo.name}'`,
                score: 9
            });
        }
        
        // 3. PRIORITÄT: Href für Links
        if (elementInfo.href) {
            searchPatterns.push({
                pattern: `href="${elementInfo.href}"`,
                score: 8
            });
            searchPatterns.push({
                pattern: `href='${elementInfo.href}'`,
                score: 8
            });
        }
        
        // 4. PRIORITÄT: Src für Bilder
        if (elementInfo.src) {
            searchPatterns.push({
                pattern: `src="${elementInfo.src}"`,
                score: 8
            });
            searchPatterns.push({
                pattern: `src='${elementInfo.src}'`,
                score: 8
            });
        }
        
        // 5. PRIORITÄT: Klassen (nur erste Klasse für bessere Genauigkeit)
        if (elementInfo.className && elementInfo.className.trim()) {
            const classes = elementInfo.className.split(' ').filter(c => c.trim());
            classes.forEach((cls, index) => {
                searchPatterns.push({
                    pattern: `class=".*${cls}.*"`,
                    score: 7 - index // erste Klasse hat höheren Score
                });
                searchPatterns.push({
                    pattern: `class='.*${cls}.*'`,
                    score: 7 - index
                });
            });
        }
        
        // 6. PRIORITÄT: Text-Inhalt (nur wenn nicht zu lang)
        if (elementInfo.text && elementInfo.text.length < 5000) {
            const cleanText = elementInfo.text.replace(/[^a-zA-Z0-9äöüÄÖÜß\s]/g, ' ').trim();
            if (cleanText.length > 3) {
                searchPatterns.push({
                    pattern: cleanText,
                    score: 6
                });
            }
        }
        
        // 7. PRIORITÄT: Andere Attribute
        if (elementInfo.alt) {
            searchPatterns.push({
                pattern: `alt="${elementInfo.alt}"`,
                score: 5
            });
        }
        
        if (elementInfo.title) {
            searchPatterns.push({
                pattern: `title="${elementInfo.title}"`,
                score: 5
            });
        }
        
        if (elementInfo.type) {
            searchPatterns.push({
                pattern: `type="${elementInfo.type}"`,
                score: 5
            });
        }
        
        // 8. PRIORITÄT: Style-Attribute
        if (elementInfo.style) {
            const styleParts = elementInfo.style.split(';').filter(s => s.trim());
            styleParts.forEach(part => {
                if (part.trim().length > 5) {
                    searchPatterns.push({
                        pattern: part.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&'),
                        score: 4
                    });
                }
            });
        }
        
        // 9. PRIORITÄT: Tag-Name
        searchPatterns.push({
            pattern: `<${elementInfo.tagName}`,
            score: 3
        });
        
        // Search in each line
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            let score = 0;
            let matchedPatterns = [];
            
            searchPatterns.forEach(searchPattern => {
                if (new RegExp(searchPattern.pattern, 'i').test(line)) {
                    score += searchPattern.score;
                    matchedPatterns.push(searchPattern.pattern);
                }
            });
            
            // Bonus-Punkte für exakte Übereinstimmungen
            if (elementInfo.id && (line.includes(`id="${elementInfo.id}"`) || line.includes(`id='${elementInfo.id}'`))) {
                score += 5;
            }
            
            if (elementInfo.text && elementInfo.text.length < 3000 && line.includes(elementInfo.text.trim())) {
                score += 4;
            }
            
            if (score > bestMatch.score) {
                bestMatch = { 
                    line: i, 
                    score: score, 
                    content: line,
                    patterns: matchedPatterns
                };
            }
        }
        
        // Jump to the best matching line
        if (bestMatch.line !== -1 && bestMatch.score > 3) {
            setTimeout(() => {
                editor.gotoLine(bestMatch.line + 1, 0, true);
                
                // Highlight the line
                editor.session.selection.clearSelection();
                const Range = ace.require('ace/range').Range;
                const range = new Range(bestMatch.line, 0, bestMatch.line, bestMatch.content.length);
                editor.selection.setSelectionRange(range);
                
                // Scroll to make it visible
                editor.scrollToLine(bestMatch.line + 1, true, true, () => {});
                                
            }, 100);
        } else {
            showNotification('Element nicht eindeutig identifizierbar', 'info');
        }
    }
    
    // ===== GENERAL FUNCTIONS =====
    // Variablen
    let isVerticalLayout = false;
    let codeSize = 50;
    let lastSearch = '';
    const editorContainer = document.getElementById('editor-container');
    const editorWrapper = document.getElementById('editor-wrapper');
    const previewWrapper = document.getElementById('preview-wrapper');
    const mainSplitter = document.getElementById('main-splitter');
    const mainSplitterSize = document.getElementById('mainSplitterSize');
    const deviceSelector = document.getElementById('deviceSelector');
    const deviceSelect = document.getElementById('deviceSelect');
    const deviceIcon = document.getElementById('deviceIcon');
    const currentSizeDisplay = document.getElementById('currentSizeDisplay');
    const layoutBtn = document.getElementById('layoutBtn');
    const layoutIcon = document.getElementById('layoutIcon');
    const searchBox = document.getElementById('searchBox');
    const searchInput = document.getElementById('searchInput');
    const newFileModal = document.getElementById('newFileModal');
    const deleteFileModal = document.getElementById('deleteFileModal');
    
    // Gerätedefinitionen - KOMPLETT ÜBERARBEITET mit Tablets und korrekten Viewport-Größen
    const devices = {
        'responsive': { name: 'Responsive', width: null, height: null, type: 'responsive' },
        // iPhones
        'iphone15': { name: 'iPhone 15', width: 393, height: 852, type: 'phone', viewportScale: 3 },
        'iphone15pro': { name: 'iPhone 15 Pro', width: 393, height: 852, type: 'phone', viewportScale: 3 },
        'iphone14': { name: 'iPhone 14', width: 390, height: 844, type: 'phone', viewportScale: 3 },
        'iphone14pro': { name: 'iPhone 14 Pro', width: 393, height: 852, type: 'phone', viewportScale: 3 },
        'iphone13': { name: 'iPhone 13', width: 390, height: 844, type: 'phone', viewportScale: 3 },
        'iphone13mini': { name: 'iPhone 13 mini', width: 375, height: 812, type: 'phone', viewportScale: 3 },
        'iphone12': { name: 'iPhone 12', width: 390, height: 844, type: 'phone', viewportScale: 3 },
        'iphonese': { name: 'iPhone SE', width: 375, height: 667, type: 'phone', viewportScale: 2 },
        'iphonexr': { name: 'iPhone XR', width: 414, height: 896, type: 'phone', viewportScale: 2 },
        'iphonexs': { name: 'iPhone XS', width: 375, height: 812, type: 'phone', viewportScale: 3 },
        'iphone11': { name: 'iPhone 11', width: 414, height: 896, type: 'phone', viewportScale: 2 },
        'iphone11pro': { name: 'iPhone 11 Pro', width: 375, height: 812, type: 'phone', viewportScale: 3 },
        // Samsung
        'samsung23': { name: 'Samsung S23', width: 360, height: 780, type: 'phone', viewportScale: 3 },
        'samsung23ultra': { name: 'Samsung S23 Ultra', width: 360, height: 780, type: 'phone', viewportScale: 3 },
        'samsung22': { name: 'Samsung S22', width: 360, height: 780, type: 'phone', viewportScale: 3 },
        'samsung21': { name: 'Samsung S21', width: 360, height: 780, type: 'phone', viewportScale: 3 },
        'samsung20': { name: 'Samsung S20', width: 360, height: 800, type: 'phone', viewportScale: 3 },
        'samsungfold': { name: 'Samsung Fold', width: 717, height: 868, type: 'phone', viewportScale: 3 },
        'samsungflip': { name: 'Samsung Flip', width: 340, height: 744, type: 'phone', viewportScale: 3 },
        // Google Pixel
        'pixel7': { name: 'Google Pixel 7', width: 412, height: 915, type: 'phone', viewportScale: 2.75 },
        'pixel7pro': { name: 'Google Pixel 7 Pro', width: 412, height: 915, type: 'phone', viewportScale: 2.75 },
        'pixel6': { name: 'Google Pixel 6', width: 412, height: 824, type: 'phone', viewportScale: 2.75 },
        'pixel5': { name: 'Google Pixel 5', width: 393, height: 851, type: 'phone', viewportScale: 2.75 },
        // OnePlus
        'oneplus11': { name: 'OnePlus 11', width: 480, height: 1076, type: 'phone', viewportScale: 2.75 },
        'oneplus10': { name: 'OnePlus 10', width: 480, height: 1080, type: 'phone', viewportScale: 2.75 },
        // Xiaomi & Huawei
        'xiaomi13': { name: 'Xiaomi 13', width: 412, height: 915, type: 'phone', viewportScale: 2.75 },
        'huaweip50': { name: 'Huawei P50', width: 390, height: 844, type: 'phone', viewportScale: 2.75 },
        // Tablets - iPads
        'ipadpro': { name: 'iPad Pro', width: 1024, height: 1366, type: 'tablet', viewportScale: 2 },
        'ipadair': { name: 'iPad Air', width: 820, height: 1180, type: 'tablet', viewportScale: 2 },
        'ipadmini': { name: 'iPad mini', width: 744, height: 1133, type: 'tablet', viewportScale: 2 },
        // Tablets - Android
        'samsungtab': { name: 'Samsung Tab S8', width: 800, height: 1280, type: 'tablet', viewportScale: 2 },
        'amazontab': { name: 'Amazon Fire HD 10', width: 800, height: 1280, type: 'tablet', viewportScale: 1.5 },
        'lenovotab': { name: 'Lenovo Tab M10', width: 800, height: 1280, type: 'tablet', viewportScale: 1.5 },
        'huaweimatepad': { name: 'Huawei MatePad', width: 1200, height: 2000, type: 'tablet', viewportScale: 2 },
        'surfacepro': { name: 'Surface Pro', width: 1440, height: 2160, type: 'tablet', viewportScale: 2 },
        // Desktops
        'desktop': { name: 'Desktop', width: 1920, height: 1080, type: 'desktop', viewportScale: 1 },
        'laptop': { name: 'Laptop', width: 1440, height: 900, type: 'desktop', viewportScale: 1 },
        'ultrawide': { name: 'UltraWide', width: 2560, height: 1080, type: 'desktop', viewportScale: 1 },
        '2k': { name: '2K', width: 2560, height: 1440, type: 'desktop', viewportScale: 1 },
        '4k': { name: '4K', width: 3840, height: 2160, type: 'desktop', viewportScale: 1 }
    };

    // Initialize saved settings
	function initializeSavedSettings() {
		// Load layout orientation
		const savedLayout = localStorage.getItem('layoutVertical');
		if (savedLayout === '1') {
			//toggleLayout(); // Switch to vertical
		}
		
		// Load splitter position
		const savedSplitterPos = localStorage.getItem('splitterPosition');
		if (savedSplitterPos !== null) {
			codeSize = parseFloat(savedSplitterPos);
			updateLayoutSizes();
		}
		
		// Load device selection and landscape orientation
		const savedDevice = localStorage.getItem('selectedDevice');
		const savedLandscape = localStorage.getItem('deviceLandscape') === 'true';
		
		if (savedDevice && devices[savedDevice]) {
			deviceSelect.value = savedDevice;
			isLandscape = savedLandscape;
			
			// Apply saved device settings
			applyDeviceSettings(savedDevice, isLandscape);
		} else {
			// Default device
			deviceSelect.value = 'iphone15';
			applyDeviceSettings('iphone15', false);
		}
	}

	// Apply device settings with proper scaling - DAS FEHLTE
	function applyDeviceSettings(deviceKey, landscape) {
		const device = devices[deviceKey];
		isLandscape = landscape;
		
		// Update icon rotation
		updateDeviceIcon(device.type, landscape);
		
		// Apply device size
		applyDeviceSize(device, landscape);
		
		// Save to localStorage - DAS IST WICHTIG
		localStorage.setItem('selectedDevice', deviceKey);
		localStorage.setItem('deviceLandscape', landscape);
	}

	// Update device icon based on type and orientation - DAS FEHLTE
	function updateDeviceIcon(deviceType, landscape) {
		let iconClass = 'fa-solid ';
		
		switch(deviceType) {
			case 'phone':
				iconClass += landscape ? 'fa-mobile-alt' : 'fa-mobile-screen';
				break;
			case 'tablet':
				iconClass += landscape ? 'fa-tablet-alt' : 'fa-tablet-screen-button';
				break;
			case 'desktop':
				iconClass += 'fa-desktop';
				break;
			default:
				iconClass += 'fa-mobile-screen';
		}
		
		deviceIcon.className = iconClass + ' device-icon';
		
		// Rotate icon for landscape
		if (landscape) {
			deviceIcon.classList.add('rotated');
		} else {
			deviceIcon.classList.remove('rotated');
		}
	}

	// Geräteauswahl Event-Listener - MIT SPEICHERN
	deviceSelect.addEventListener('change', function() {
		const deviceKey = this.value;
		applyDeviceSettings(deviceKey, isLandscape);
	});

	// Rotate Funktion für Landscape/Portrait - MIT SPEICHERN
	function rotateDevice() {
		const deviceKey = deviceSelect.value;
		isLandscape = !isLandscape;
		
		applyDeviceSettings(deviceKey, isLandscape);
		
		// Button visuell aktualisieren
		if (isLandscape) {
			showNotification('Landscape-Modus aktiviert', 'success');
		} else {
			showNotification('Portrait-Modus aktiviert', 'success');
		}
	}
    initializeSavedSettings();
    // Calculate actual display size with viewport scaling
    function calculateActualSize(device, landscape) {
        let width = device.width;
        let height = device.height;
        
        if (landscape && device.type !== 'desktop') {
            // Swap dimensions for landscape
            [width, height] = [height, width];
        }
        
        // Apply viewport scaling for accurate representation
        const viewportScale = device.viewportScale || 1;
        const displayWidth = Math.round(width);
        const displayHeight = Math.round(height);
        
        return { displayWidth, displayHeight, actualWidth: width, actualHeight: height };
    }
    
	function applyDeviceSize(device, landscape) {
		if (device.width && device.height) {
			const sizes = calculateActualSize(device, landscape);
			
			// Aktuelle Größe anzeigen (tatsächliche Geräteauflösung)
			currentSizeDisplay.textContent = `${device.width}×${device.height}`;
			
			// Iframe Größe setzen (skalierte Darstellung)
			const iframeContainer = previewFrame.parentElement;
			
			// Für vertikales Layout: -100% transform, sonst -50%
			const transformX = isVerticalLayout ? '-50%' : '-50%';
			
			const iframeStyle = `
				width: ${sizes.displayWidth}px !important;
				height: ${sizes.displayHeight}px !important;
				max-width: 100%;
				max-height: 100%;
				margin: auto;
				position: relative;
				border: 1px solid #333;
				box-shadow: 0 0 20px rgba(0,0,0,0.3);
				background: white;
			`;
			
			previewFrame.style.cssText = iframeStyle;
			
			// Container anpassen
			iframeContainer.style.cssText = `
				display: flex;
				align-items: center;
				justify-content: center;
				height: 100%;
				width: 100%;
				overflow: auto;
			`;
			
			// WICHTIG: Splitter automatisch an Gerätegröße anpassen
			const containerWidth = editorContainer.clientWidth;
			const targetPreviewWidth = sizes.displayWidth;
			const targetCodeSize = 100 - ((targetPreviewWidth / containerWidth) * 100);
			codeSize = Math.max(20, Math.min(80, targetCodeSize));
			
			document.documentElement.style.setProperty('--code-size', codeSize + '%');
			
			setTimeout(() => {
				if (editor && editor.resize) editor.resize();
				updateSplitterDisplay();
			}, 10);
			
			// Viewport Meta Tag für korrekte Darstellung setzen
			setTimeout(() => {
				try {
					const iframeDoc = previewFrame.contentDocument || previewFrame.contentWindow.document;
					let viewportMeta = iframeDoc.querySelector('meta[name="viewport"]');
					
					if (!viewportMeta) {
						viewportMeta = iframeDoc.createElement('meta');
						viewportMeta.name = 'viewport';
						iframeDoc.head.appendChild(viewportMeta);
					}
					
					// Viewport für korrekte Skalierung setzen
					const devicePixelRatio = device.viewportScale || 1;
					viewportMeta.content = `width=${sizes.displayWidth}, initial-scale=1, maximum-scale=${devicePixelRatio}, user-scalable=no`;
					
				} catch (error) {
					console.log('Could not set viewport meta:', error);
				}
			}, 300);
			
		} else {
			// Responsive Mode - volle Größe
			const transformX = isVerticalLayout ? '-50%' : '-50%';
			
			previewFrame.style.cssText = `
				width: 100% !important;
				height: 100% !important;
				left: 50%;
				transform: translateX(${transformX});
				border: none;
			`;
			currentSizeDisplay.textContent = 'Responsive';
		}
	}

    function adjustSplitterForDevice(device) {
        if (!device.width || !device.height) return;
        
        const containerWidth = editorContainer.clientWidth;
        const sizes = calculateActualSize(device, isLandscape);
        const targetPreviewWidth = sizes.displayWidth;
        
        // Berechne prozentuale Position für Splitter
        const targetCodeSize = 100 - ((targetPreviewWidth / containerWidth) * 100);
        
        // Setze Splitter-Position (begrenzt auf 20-80%)
        codeSize = Math.max(20, Math.min(80, targetCodeSize));
        
        updateLayoutSizes();
        setTimeout(() => {
            if (editor && editor.resize) editor.resize();
            updateSplitterDisplay();
        }, 10);
        
        localStorage.setItem('splitterPosition', codeSize);
    }
    
    // Update Splitter Size Anzeige - NUR VORSCHAUFENSTER wird gemessen
    function updateSplitterDisplay() {
        const previewWidth = previewWrapper.clientWidth;
        const previewHeight = previewFrame.clientHeight;
	  
        // Größe anzeigen - NUR Vorschaufenster
        mainSplitterSize.textContent = `${previewWidth + 2}px × ${previewHeight + 2}px`;
        
        // Schrift drehen für horizontales Layout
        if (isVerticalLayout) {
            mainSplitterSize.style.transform = 'translate(-50%, -50%) rotate(0deg)';
        } else {
            mainSplitterSize.style.transform = 'translate(-50%, -50%) rotate(-90deg)';
        }
    }
    
    // CSS Variablen setzen
    function updateLayoutSizes() {
        document.documentElement.style.setProperty('--code-size', codeSize + '%');
        updateSplitterDisplay();
    }
    
    // ===== IMPROVED SPLITTER WITH GLOBAL DRAGGING =====
    let isDragging = false;
    let startDragX = 0;
    let startDragY = 0;
    let startCodeSize = 50;
    let currentMouseMoveHandler = null;
    let currentMouseUpHandler = null;
    let currentTouchMoveHandler = null;
    let currentTouchEndHandler = null;

    // Maus-Down auf Splitter
    mainSplitter.addEventListener('mousedown', function(e) {
        startSplitterDrag(e.clientX, e.clientY);
        e.preventDefault();		
    });

    // Touch-Start auf Splitter
    mainSplitter.addEventListener('touchstart', function(e) {
        if (e.touches.length === 1) {
            const touch = e.touches[0];
            startSplitterDrag(touch.clientX, touch.clientY);
            e.preventDefault();
        }
    });

    // Start Drag mit globalen Event-Listenern
    function startSplitterDrag(startX, startY) {
        if (isDragging) return;
        
        isDragging = true;
        startDragX = startX;
        startDragY = startY;
        startCodeSize = codeSize;
        
        // Splitter visuell hervorheben
        mainSplitter.classList.add('dragging');
        document.body.style.cursor = isVerticalLayout ? 'row-resize' : 'col-resize';
        document.body.style.userSelect = 'none';
        previewFrame.style.pointerEvents = "none";

        // Globale Event-Listener für präzises Tracking
        currentMouseMoveHandler = function(e) {
            handleSplitterMove(e.clientX, e.clientY);
        };
        
        currentMouseUpHandler = function() {
            endSplitterDrag();
        };
        
        currentTouchMoveHandler = function(e) {
            if (e.touches.length === 1) {
                const touch = e.touches[0];
                handleSplitterMove(touch.clientX, touch.clientY);
                e.preventDefault();
            }
        };
        
        currentTouchEndHandler = function() {
            endSplitterDrag();
        };
        
        document.addEventListener('mousemove', currentMouseMoveHandler);
        document.addEventListener('mouseup', currentMouseUpHandler);
        document.addEventListener('touchmove', currentTouchMoveHandler, { passive: false });
        document.addEventListener('touchend', currentTouchEndHandler);
        document.addEventListener('touchcancel', currentTouchEndHandler);
    }

    // Handle Move mit verbesserten Berechnungen
    function handleSplitterMove(currentX, currentY) {
        if (!isDragging) return;
        
        if (isVerticalLayout) {
            // Vertikal: Code oben, Vorschau unten
            const containerHeight = editorContainer.clientHeight;
            const deltaY = currentY - startDragY;
            const deltaPercent = (deltaY / containerHeight) * 100;
            
            codeSize = Math.max(20, Math.min(80, startCodeSize + deltaPercent));
        } else {
            // Horizontal: Code links, Vorschau rechts
            const containerWidth = editorContainer.clientWidth;
            const deltaX = currentX - startDragX;
            const deltaPercent = (deltaX / containerWidth) * 100;
            
            codeSize = Math.max(20, Math.min(80, startCodeSize + deltaPercent));
        }
        
        // Sofortiges Update
        updateLayoutSizes();
        
        // Mini-Resize für Editor
        requestAnimationFrame(() => {
            if (editor && editor.resize) editor.resize();
        });
    }

    // Beende Drag mit Cleanup
    function endSplitterDrag() {
        if (!isDragging) return;
        
        isDragging = false;
        
        // Visuelle Effekte entfernen
        mainSplitter.classList.remove('dragging');
        document.body.style.cursor = '';
        document.body.style.userSelect = '';
		previewFrame.style.pointerEvents = "all";
        
        // Alle globalen Event-Listener entfernen
        if (currentMouseMoveHandler) {
            document.removeEventListener('mousemove', currentMouseMoveHandler);
            currentMouseMoveHandler = null;
        }
        
        if (currentMouseUpHandler) {
            document.removeEventListener('mouseup', currentMouseUpHandler);
            currentMouseUpHandler = null;
        }
        
        if (currentTouchMoveHandler) {
            document.removeEventListener('touchmove', currentTouchMoveHandler);
            currentTouchMoveHandler = null;
        }
        
        if (currentTouchEndHandler) {
            document.removeEventListener('touchend', currentTouchEndHandler);
            document.removeEventListener('touchcancel', currentTouchEndHandler);
            currentTouchEndHandler = null;
        }
        
        // Finales Resize für Editor
        setTimeout(() => {
            if (editor && editor.resize) editor.resize();
        }, 10);
        
        // Layout in localStorage speichern
        localStorage.setItem('splitterPosition', codeSize);
    }

    // Verhindere unbeabsichtigtes Text-Selektieren während Drag
    mainSplitter.addEventListener('selectstart', function(e) {
        if (isDragging) {
            e.preventDefault();
            return false;
        }
    });

    // Mouseleave-Handler für bessere UX (optional)
    mainSplitter.addEventListener('mouseleave', function(e) {
        if (!isDragging) {
            mainSplitter.style.background = '#3a3a3a';
        }
    });

    // Mouseenter-Handler für Hover-Effekt
    mainSplitter.addEventListener('mouseenter', function(e) {
        if (!isDragging) {
            mainSplitter.style.background = '#4a4a4a';
        }
    });

    // Bei Seitenverlassen sicherstellen, dass Drag beendet wird
    window.addEventListener('blur', endSplitterDrag);
    document.addEventListener('visibilitychange', endSplitterDrag);

	// 1. Layout beim Laden setzen
	if (localStorage.getItem('layout') === 'vertical') {
		const container = document.getElementById('editor-container');
		container.classList.remove('horizontal');
		container.classList.add('vertical');
		document.getElementById('layoutIcon').innerHTML = '<i class="fa-solid fa-up-down"></i>';
		isVerticalLayout = true;
	} else {
		const container = document.getElementById('editor-container');
		container.classList.remove('vertical');
		container.classList.add('horizontal');
		document.getElementById('layoutIcon').innerHTML = '<i class="fa-solid fa-left-right"></i>';
		isVerticalLayout = false;
	}
	

	// 2. Toggle Funktion
	function toggleLayout() {
		const container = document.getElementById('editor-container');
		const isVertical = container.classList.contains('vertical');
		
		if (isVertical) {
			container.classList.remove('vertical');
			container.classList.add('horizontal');
			document.getElementById('layoutIcon').innerHTML = '<i class="fa-solid fa-left-right"></i>';
			localStorage.setItem('layout', 'horizontal');
			isVerticalLayout = false;
		} else {
			container.classList.remove('horizontal');
			container.classList.add('vertical');
			document.getElementById('layoutIcon').innerHTML = '<i class="fa-solid fa-up-down"></i>';
			localStorage.setItem('layout', 'vertical');
			isVerticalLayout = true;
		}
		
		// Splitter zurücksetzen
		codeSize = 50;
		updateLayoutSizes();
		
		// Editor resizen
		setTimeout(() => editor.resize(), 10);
		initializeSavedSettings();
	}

    // ===== VOLLBILD FUNKTION =====
    function toggleFullscreen() {
        const elem = document.documentElement;
        
        if (!document.fullscreenElement) {
            // Vollbild aktivieren
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
            showNotification('Vollbild aktiviert', 'success');
        } else {
            // Vollbild verlassen
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
            showNotification('Vollbild deaktiviert', 'info');
        }
    }
    
    // Vollbild Änderungen überwachen
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('msfullscreenchange', handleFullscreenChange);
    
    function handleFullscreenChange() {
        const isFullscreen = !!document.fullscreenElement || 
                           !!document.webkitFullscreenElement || 
                           !!document.msFullscreenElement;
        
        const fullscreenBtn = document.querySelector('.fullscreen-btn');
        if (isFullscreen) {
            fullscreenBtn.innerHTML = '<i class="icon fa-solid fa-compress"></i>';
            fullscreenBtn.title = 'Vollbild beenden';
        } else {
            fullscreenBtn.innerHTML = '<i class="icon fa-solid fa-expand"></i>';
            fullscreenBtn.title = 'Vollbild';
        }
    }
    
    // Load editor state on init
    setTimeout(loadEditorState, 300);
    
    // Event-Listener für iFrame-Load hinzufügen, um Element-Auswahl nach Neuladen zu erhalten
    previewFrame.addEventListener('load', function() {
        setTimeout(() => {
            if (isIframeClickMode) {
                disableIframeClickMode();
                setTimeout(() => enableIframeClickMode(), 200);
            }
        }, 300);
    });

    // Neue Datei Modal Funktionen
    function showNewFileModal() {
        newFileModal.classList.add('active');
        document.querySelector('.new-file-input').focus();
    }
    
    function hideNewFileModal() {
        newFileModal.classList.remove('active');
        document.getElementById('newFileForm').reset();
    }
    
    // Datei löschen Modal Funktionen
    function showDeleteFileModal() {
        if (!currentFileName || currentFileName === 'editor.php' || currentFileName === '.htaccess') {
            showNotification("Diese Datei kann nicht gelöscht werden!", "error");
            return;
        }
        deleteFileModal.classList.add('active');
    }
    
    function hideDeleteFileModal() {
        deleteFileModal.classList.remove('active');
    }
    
    // ESC zum Schließen der Modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (newFileModal.classList.contains('active')) {
                hideNewFileModal();
            }
            if (deleteFileModal.classList.contains('active')) {
                hideDeleteFileModal();
            }
            if (isIframeClickMode) {
                toggleIframeClickMode();
            }
        }
    });

    // Search Funktionen
    function toggleSearch() {
        searchBox.classList.toggle('active');
        if (searchBox.classList.contains('active')) {
            searchInput.focus();
            searchInput.select();
        } else {
        }
    }

    function findNext() {
        const searchValue = searchInput.value;
        if (!searchValue) return;
        
        lastSearch = searchValue;
        
        const found = editor.find(searchValue, {
            backwards: false,
            wrap: true,
            caseSensitive: false,
            wholeWord: false,
            regExp: false
        });
        
        if (!found && searchValue) {
            showNotification("Keine weiteren Treffer", "info");
        }
        
    }

    function findPrev() {
        const searchValue = searchInput.value;
        if (!searchValue) return;
        
        lastSearch = searchValue;
        
        const found = editor.find(searchValue, {
            backwards: true,
            wrap: true,
            caseSensitive: false,
            wholeWord: false,
            regExp: false
        });
        
        if (!found && searchValue) {
            showNotification("Keine weiteren Treffer", "info");
        }
        
    }

    // Live-Suche beim Tippen
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (searchInput.value.length > 0) {
                findNext();
            } else {
                editor.clearSelection();
            }
        }, 300);
    });

    // Sidebar ein/ausklappen
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    
        const isCollapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('minimized', isCollapsed ? 1 : 0);
    }

    if (localStorage.getItem('minimized') == 1) {
        toggleSidebar();
    }

    // Speichern Funktion
    function saveFile() {
        const content = editor.getValue();
        const filepath = "<?= addslashes($current_file) ?>";
        
        if(!filepath) { 
            showNotification("Wähle erst eine Datei aus!", "error");
            return; 
        }

        const formData = new FormData();
        formData.append('save_file', '1');
        formData.append('filepath', filepath);
        formData.append('content', content);

        fetch('', { method: 'POST', body: formData })
            .then(r => r.text())
            .then(msg => {
                showNotification("Datei gespeichert!", "success");
                previewFrame.src = filepath + '?t=' + new Date().getTime();
                saveEditorState();
                
                setTimeout(() => {
                    if (isIframeClickMode) {
                        disableIframeClickMode();
                        setTimeout(() => enableIframeClickMode(), 300);
                    }
                }, 500);
            })
            .catch(error => {
                showNotification("Fehler beim Speichern!", "error");
            });
    }

    function showNotification(message, type) {
        document.querySelectorAll('.notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.style.background = type === 'error' ? '#f44336' : 
                                      type === 'info' ? '#2196F3' : '#2196F3';
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.3s';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    // Hotkeys
    document.addEventListener('keydown', function(e) {
        // Strg+S zum Speichern
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveFile();
        }
        
        // Strg+F zum Suchen
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            toggleSearch();
        }
        
        // F3 für nächstes
        if (e.key === 'F3') {
            e.preventDefault();
            if (lastSearch || searchInput.value) {
                findNext();
            } else {
                toggleSearch();
            }
        }
        
        // Shift+F3 für vorheriges
        if (e.shiftKey && e.key === 'F3') {
            e.preventDefault();
            if (lastSearch || searchInput.value) {
                findPrev();
            } else {
                toggleSearch();
            }
        }
        
        // ESC zum Schließen
        if (e.key === 'Escape') {
            if (searchBox.classList.contains('active')) {
                toggleSearch();
            }
        }
        
        // Strg+N für neue Datei
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            showNewFileModal();
        }
        
        // Strg+Shift+D für Datei löschen
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
            e.preventDefault();
            showDeleteFileModal();
        }
        
        // Strg+Shift+C für Iframe-Click-Mode
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'C') {
            e.preventDefault();
            toggleIframeClickMode();
        }
        
        // Strg+Shift+L für Live Edit
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'L') {
            e.preventDefault();
            toggleLiveEdit();
        }
        
        // F11 für Vollbild
        if (e.key === 'F11') {
            e.preventDefault();
            toggleFullscreen();
        }
        
        // Strg+Shift+R für Rotate
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'R') {
            e.preventDefault();
            rotateDevice();
        }
    });

    // Initialisierung
    editor.focus();
    
    // Initialize saved settings
    initializeSavedSettings();
    
    // Bei Mobile: Layout auf vertikal setzen
    if (window.innerWidth <= 768) {
        isVerticalLayout = true;
        editorContainer.classList.remove('horizontal');
        editorContainer.classList.add('vertical');
        layoutIcon.innerHTML = '<i class="fa-solid fa-up-down"></i>';
        updateLayoutSizes();
        
        // Update iframe transform for mobile
        const deviceKey = deviceSelect.value;
        applyDeviceSettings(deviceKey, isLandscape);
    }

    document.getElementById('editor').style.display = 'none';

    // Show it after 2000ms (2 seconds)
    setTimeout(() => {
        document.getElementById('editor').style.display = 'block';
    }, 0);
    
    // Initial Splitter Größen aktualisieren
    setTimeout(updateSplitterDisplay, 100);
    window.addEventListener('resize', updateSplitterDisplay);
</script>
<script>window.gtranslateSettings = {"default_language":"de","native_language_names":true,"detect_browser_language":true,"languages":["de","fr","it","es","en"]}</script>
<script src="https://cdn.gtranslate.net/widgets/latest/float.js" defer></script>
<script>
// Sofortiges Ausblenden beim Start
const hideElementImmediately = () => {
  const element = document.querySelector('.VIpgJd-ZVi9od-aZ2wEe-wOHMyf');
  if (element) {
    element.style.display = 'none';
  }
};

// Erstes sofortiges Ausblenden
hideElementImmediately();

// Intervall für kontinuierliches Überprüfen und Ausblenden (jede 100ms)
const hideInterval = setInterval(hideElementImmediately, 0);
</script>
</body>
</html>
