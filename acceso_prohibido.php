<?php
http_response_code(403);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso prohibido | ZELIA</title>
    <meta http-equiv="refresh" content="3;url=index.php">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-1: #09001a;
            --bg-2: #140b2f;
            --panel: rgba(13, 5, 27, 0.84);
            --border: #36f9ff;
            --warn: #ff4f72;
            --ok: #ffe66d;
            --text: #f5f7ff;
            --muted: #8ee9ff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 15% 20%, rgba(54, 249, 255, 0.12) 0%, transparent 36%),
                radial-gradient(circle at 85% 75%, rgba(255, 79, 114, 0.15) 0%, transparent 35%),
                linear-gradient(140deg, var(--bg-1), var(--bg-2));
            color: var(--text);
            font-family: 'VT323', monospace;
            padding: 20px;
            overflow: hidden;
        }

        .scanlines::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image: repeating-linear-gradient(
                to bottom,
                rgba(255, 255, 255, 0.04),
                rgba(255, 255, 255, 0.04) 1px,
                transparent 1px,
                transparent 3px
            );
            mix-blend-mode: overlay;
            opacity: 0.35;
            animation: drift 8s linear infinite;
        }

        .panel {
            width: min(760px, 100%);
            border: 2px solid var(--border);
            background: var(--panel);
            border-radius: 16px;
            padding: 28px 22px;
            box-shadow: 0 0 22px rgba(54, 249, 255, 0.32), inset 0 0 22px rgba(54, 249, 255, 0.08);
            text-align: center;
            animation: boot 420ms ease-out;
            position: relative;
        }

        .code {
            font-family: 'Press Start 2P', monospace;
            font-size: clamp(32px, 10vw, 74px);
            color: var(--warn);
            letter-spacing: 3px;
            text-shadow: 0 0 14px rgba(255, 79, 114, 0.9);
            margin: 4px 0 18px;
        }

        .title {
            font-family: 'Press Start 2P', monospace;
            font-size: clamp(16px, 3.8vw, 26px);
            color: var(--ok);
            margin: 0 0 14px;
            line-height: 1.35;
        }

        .desc {
            font-size: clamp(24px, 4.7vw, 34px);
            color: var(--text);
            margin: 0 0 18px;
            line-height: 1.3;
        }

        .hint {
            font-size: clamp(24px, 4.2vw, 32px);
            color: var(--muted);
            margin: 0 0 24px;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn {
            border: 2px solid var(--ok);
            color: var(--ok);
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-family: 'Press Start 2P', monospace;
            font-size: 11px;
            letter-spacing: 1px;
            background: rgba(255, 230, 109, 0.08);
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .btn:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 0 14px rgba(255, 230, 109, 0.5);
            background: rgba(255, 230, 109, 0.16);
        }

        .dots {
            display: inline-block;
            width: 1.5em;
            text-align: left;
            overflow: hidden;
            vertical-align: bottom;
            animation: dots 1.2s steps(4, end) infinite;
        }

        @keyframes boot {
            from {
                transform: scale(0.98);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes drift {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(3px);
            }
        }

        @keyframes dots {
            from { width: 0; }
            to { width: 1.5em; }
        }
    </style>
</head>
<body class="scanlines">
    <main class="panel" role="main" aria-live="polite">
        <div class="code">403</div>
        <h1 class="title">ACCESO PROHIBIDO</h1>
        <p class="desc">Este recurso no esta disponible publicamente.</p>
        <p class="hint">Redirigiendo al inicio en 3s<span class="dots">...</span></p>
        <div class="actions">
            <a class="btn" href="index.php">IR AL INICIO</a>
        </div>
    </main>
    <script>
        setTimeout(function () {
            window.location.replace('index.php');
        }, 3000);
    </script>
</body>
</html>
