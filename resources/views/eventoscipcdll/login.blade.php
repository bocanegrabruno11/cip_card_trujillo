<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos Institucionales CIPCDLL</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Nunito:wght@300;400;500;600;700&family=Dancing+Script:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --wine:      #7c1228;
            --crimson:   #b01e3a;
            --rose-soft: #d4728a;
            --blush:     #f0c0cc;
            --blush-lt:  #faeaed;
            --gold:      #c49560;
            --gold-lt:   #f2e3cc;
            --cream:     #fdf6f0;
            --ink:       #2c1018;
            --ink-mid:   #6b3040;
            --ink-soft:  #a07080;
            --glass:     rgba(255,252,250,0.88);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--cream);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ── Fondo degradado animado ── */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 75% 60% at 5%   8%,  rgba(212,114,138,.22) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 95% 92%, rgba(196,149,96,.18)  0%, transparent 55%),
                radial-gradient(ellipse 55% 55% at 50% 50%, rgba(240,192,204,.10) 0%, transparent 65%);
        }

        /* ── Flores de fondo ── */
        .bg-deco {
            position: fixed; inset: 0; z-index: 0; pointer-events: none; overflow: hidden;
        }
        .bg-deco span {
            position: absolute; user-select: none; opacity: 0;
            animation: floatUp ease-in-out infinite;
        }
        .bg-deco span:nth-child(1)  { font-size: 8rem;  top: -4%;  left: -3%;  opacity: .07; animation: floatA 9s ease-in-out infinite;    transform: rotate(-25deg); }
        .bg-deco span:nth-child(2)  { font-size: 7rem;  top: 5%;   right: -2%; opacity: .06; animation: floatB 11s ease-in-out infinite;   transform: rotate(20deg);  }
        .bg-deco span:nth-child(3)  { font-size: 9rem;  bottom:2%; left: -2%;  opacity: .07; animation: floatC 10s ease-in-out infinite;   transform: rotate(-18deg); }
        .bg-deco span:nth-child(4)  { font-size: 8rem;  bottom:-1%;right:-2%;  opacity: .06; animation: floatA 8s ease-in-out infinite reverse; transform: rotate(14deg); }
        .bg-deco span:nth-child(5)  { font-size: 4rem;  top: 18%;  left: 6%;   opacity: .09; animation: floatC 7s ease-in-out infinite;    transform: rotate(12deg);  }
        .bg-deco span:nth-child(6)  { font-size: 4rem;  top: 22%;  right: 6%;  opacity: .08; animation: floatA 9s ease-in-out infinite 1s; transform: rotate(-22deg); }
        .bg-deco span:nth-child(7)  { font-size: 3.5rem;top: 62%;  left: 4%;   opacity: .09; animation: floatB 8s ease-in-out infinite 2s; transform: rotate(30deg);  }
        .bg-deco span:nth-child(8)  { font-size: 3rem;  top: 68%;  right: 5%;  opacity: .08; animation: floatC 10s ease-in-out infinite;   transform: rotate(-15deg); }
        .bg-deco span:nth-child(9)  { font-size: 10rem; top: 40%;  left: 46%;  opacity: .04; animation: floatB 13s ease-in-out infinite;   transform: rotate(-10deg); filter: blur(2px); }

        @keyframes floatA { 0%,100%{ transform: translateY(0px) rotate(-25deg); } 50%{ transform: translateY(-14px) rotate(-25deg); } }
        @keyframes floatB { 0%,100%{ transform: translateY(0px) rotate(20deg);  } 50%{ transform: translateY(-10px) rotate(20deg);  } }
        @keyframes floatC { 0%,100%{ transform: translateY(0px) rotate(-18deg); } 50%{ transform: translateY(-18px) rotate(-18deg); } }

        /* ── Tarjeta principal ── */
        .login-wrap {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
            padding: 1.2rem;
            animation: cardIn .75s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(28px) scale(.96); }
            to   { opacity: 1; transform: translateY(0)    scale(1);   }
        }

        .card {
            background: var(--glass);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(196,149,96,.22);
            border-radius: 2rem;
            overflow: hidden;
            box-shadow:
                0 2px 0 rgba(255,255,255,.8) inset,
                0 32px 64px -16px rgba(124,18,40,.22),
                0  8px 24px  -8px rgba(124,18,40,.12);
        }

        /* ── Cabecera vino ── */
        .card-header {
            background: linear-gradient(145deg, var(--wine) 0%, #a01830 55%, #c0304a 100%);
            padding: 2.2rem 2rem 2.8rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        /* Ola inferior */
        .card-header::after {
            content: '';
            position: absolute; bottom: -1px; left: 0; right: 0; height: 36px;
            background: var(--glass);
            clip-path: ellipse(55% 100% at 50% 100%);
        }
        /* Brillo sutil en cabecera */
        .card-header::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 80% 60% at 50% 0%, rgba(255,255,255,.12) 0%, transparent 70%);
            pointer-events: none;
        }
        /* Barra animada superior */
        .card-header-bar {
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--rose-soft), var(--gold));
            background-size: 200% 100%;
            animation: shiftBar 4s linear infinite;
        }
        @keyframes shiftBar { 0%{ background-position: 0% 0%; } 100%{ background-position: 200% 0%; } }

        .logo-wrap {
            position: relative; z-index: 1;
            width: 80px; height: 80px; margin: 0 auto 1rem;
            background: rgba(255,255,255,.15);
            border: 2px solid rgba(255,255,255,.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 24px rgba(0,0,0,.25);
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse {
            0%,100% { box-shadow: 0 8px 24px rgba(0,0,0,.25), 0 0 0 0   rgba(255,255,255,.2); }
            50%      { box-shadow: 0 8px 24px rgba(0,0,0,.25), 0 0 0 8px rgba(255,255,255,.0); }
        }
        .logo-img {
            width: 58px; height: 58px;
            object-fit: contain;
            filter: brightness(1.1) drop-shadow(0 2px 6px rgba(0,0,0,.3));
        }

        .header-eyebrow {
            position: relative; z-index: 1;
            font-size: .65rem; font-weight: 700; letter-spacing: 3px;
            text-transform: uppercase; color: rgba(255,255,255,.65);
            margin-bottom: .3rem;
        }
        .header-title {
            position: relative; z-index: 1;
            font-family: 'Playfair Display', serif;
            font-size: 1.55rem; font-weight: 800; color: #fff;
            line-height: 1.2;
            text-shadow: 0 2px 12px rgba(0,0,0,.2);
        }
        .header-title span {
            font-family: 'Dancing Script', cursive;
            font-size: 1.1rem; font-weight: 700;
            color: var(--gold-lt);
            display: block; margin-top: .2rem;
        }

        /* ── Cuerpo del card ── */
        .card-body {
            padding: 2rem 2rem 2.2rem;
        }

        /* ── Alerta de error ── */
        .alert-error {
            display: flex; align-items: center; gap: .7rem;
            background: rgba(176,30,58,.08);
            border: 1px solid rgba(176,30,58,.22);
            border-left: 3.5px solid var(--crimson);
            border-radius: .85rem;
            padding: .75rem 1rem;
            font-size: .82rem; font-weight: 600; color: var(--crimson);
            margin-bottom: 1.4rem;
            animation: slideIn .35s ease both;
        }
        @keyframes slideIn { from{ opacity:0; transform:translateY(-8px); } to{ opacity:1; transform:translateY(0); } }
        .alert-error i { font-size: 1.1rem; flex-shrink: 0; }

        /* ── Campos ── */
        .field { margin-bottom: 1.1rem; }
        .field-label {
            display: block;
            font-size: .7rem; font-weight: 800; letter-spacing: 1.2px;
            text-transform: uppercase; color: var(--ink-mid);
            margin-bottom: .4rem;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: .95rem; top: 50%; transform: translateY(-50%);
            color: var(--rose-soft); font-size: 1rem; pointer-events: none;
            transition: color .22s;
        }
        .form-input {
            width: 100%;
            padding: .82rem 1rem .82rem 2.65rem;
            border: 1.5px solid rgba(196,149,96,.28);
            border-radius: .9rem;
            font-family: 'Nunito', sans-serif;
            font-size: .93rem; font-weight: 600; color: var(--ink);
            background: rgba(255,252,250,.9);
            outline: none;
            transition: border-color .22s, box-shadow .22s, background .22s;
        }
        .form-input::placeholder { color: #c4a0aa; font-weight: 400; }
        .form-input:focus {
            border-color: var(--crimson);
            box-shadow: 0 0 0 3.5px rgba(176,30,58,.12);
            background: #fff;
        }
        .form-input:focus + .input-icon,
        .input-wrap:focus-within .input-icon { color: var(--crimson); }

        /* ── Botón ── */
        .btn-login {
            display: flex; align-items: center; justify-content: center; gap: .6rem;
            width: 100%; padding: .95rem 2rem;
            background: linear-gradient(108deg, var(--wine) 0%, var(--crimson) 55%, var(--rose-soft) 100%);
            color: #fff;
            font-family: 'Nunito', sans-serif;
            font-size: .93rem; font-weight: 800; letter-spacing: .5px;
            border: none; border-radius: 50px; cursor: pointer;
            margin-top: 1.6rem;
            box-shadow: 0 12px 30px -8px rgba(124,18,40,.4);
            transition: transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
            position: relative; overflow: hidden;
        }
        .btn-login::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.15) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform .6s ease;
        }
        .btn-login:hover { transform: translateY(-3px) scale(1.012); box-shadow: 0 20px 40px -10px rgba(124,18,40,.48); }
        .btn-login:hover::before { transform: translateX(100%); }
        .btn-login:active { transform: scale(.98); }

        /* ── Pie ── */
        .card-footer {
            text-align: center;
            padding: 0 2rem 1.6rem;
        }
        .footer-line {
            display: flex; align-items: center; gap: .5rem;
            justify-content: center;
            font-size: .72rem; color: var(--ink-soft);
            font-weight: 600;
        }
        .footer-dots {
            display: flex; gap: .3rem; align-items: center;
        }
        .dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--gold);
            animation: dotPulse 1.8s ease-in-out infinite;
        }
        .dot:nth-child(2) { animation-delay: .3s; }
        .dot:nth-child(3) { animation-delay: .6s; }
        @keyframes dotPulse { 0%,100%{ opacity:.3; transform:scale(1); } 50%{ opacity:1; transform:scale(1.4); } }
    </style>
</head>
<body>

<!-- Flores de fondo -->
<div class="bg-deco" aria-hidden="true">
    <span>🌹</span><span>🌸</span><span>🌹</span><span>🌺</span>
    <span>🌸</span><span>🌹</span><span>💐</span><span>🌺</span><span>💗</span>
</div>

<div class="login-wrap">
    <div class="card">

        <!-- Cabecera -->
        <div class="card-header">
            <div class="card-header-bar"></div>

            <div class="logo-wrap">
                <img src="{{ asset('img/logo.png') }}" alt="Logo CIPCDLL" class="logo-img"
                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2245%22 fill=%22rgba(255,255,255,0.2)%22/%3E%3Ctext x=%2250%22 y=%2268%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2244%22 font-weight=%22bold%22 font-family=%22Georgia%22%3EC%3C/text%3E%3C/svg%3E';">
            </div>

            <p class="header-eyebrow">✦ CIP — Consejo Departamental La Libertad ✦</p>
            <h1 class="header-title">
                Eventos Institucionales
                <span>🌹 CIPCDLL 🌹</span>
            </h1>
        </div>

        <!-- Cuerpo -->
        <div class="card-body">

            @if(session('error'))
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.eventos.post') }}">
                @csrf

                <div class="field">
                    <label for="usuario" class="field-label">
                        <i class="bi bi-person-badge"></i> Usuario
                    </label>
                    <div class="input-wrap">
                        <input type="text" id="usuario" name="usuario"
                               class="form-input"
                               placeholder="Ingresa tu usuario"
                               required autocomplete="username">
                        <i class="bi bi-person-fill input-icon"></i>
                    </div>
                </div>

                <div class="field">
                    <label for="password" class="field-label">
                        <i class="bi bi-lock"></i> Contraseña
                    </label>
                    <div class="input-wrap">
                        <input type="password" id="password" name="password"
                               class="form-input"
                               placeholder="••••••••"
                               required autocomplete="current-password">
                        <i class="bi bi-lock-fill input-icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Ingresar al Sistema
                </button>
            </form>
        </div>

        <!-- Pie -->
        <div class="card-footer">
            <div class="footer-line">
                <div class="footer-dots">
                    <div class="dot"></div><div class="dot"></div><div class="dot"></div>
                </div>
                Acceso restringido · Solo personal autorizado
                <div class="footer-dots">
                    <div class="dot"></div><div class="dot"></div><div class="dot"></div>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>