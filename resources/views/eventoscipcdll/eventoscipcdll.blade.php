<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Festejemos a Mamá | Ingenieras CIPCDLL</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Nunito:wght@300;400;500;600;700&family=Dancing+Script:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            --glass:     rgba(255,252,250,0.82);
        }

        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'Nunito', sans-serif;
            background: var(--cream);
            color: var(--ink);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body.flyer-active { overflow: hidden; }

        body::before {
            content: '';
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(ellipse 70% 55% at 0%   5%,  rgba(212,114,138,.18) 0%, transparent 65%),
                radial-gradient(ellipse 55% 45% at 100% 95%, rgba(196,149, 96,.16) 0%, transparent 60%),
                radial-gradient(ellipse 50% 50% at 50%  50%, rgba(240,192,204,.08) 0%, transparent 70%);
        }

        /* ═══════════════════════════════════════════════
           PANTALLA INTRO — FLOR CON PÉTALOS SECUENCIALES
           ═══════════════════════════════════════════════ */
        #introScreen {
            position: fixed; inset: 0; z-index: 5000;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column;
            overflow: hidden;
            background: radial-gradient(ellipse 120% 120% at 50% 50%,
                #fff0f4 0%, #fce4ea 35%, #f8d0da 65%, #f2bec8 100%);
            transition: opacity 1s ease, transform 1s ease;
        }
        #introScreen.fade-out {
            opacity: 0;
            transform: scale(1.04);
            pointer-events: none;
        }

        /* Partículas de fondo del intro */
        .intro-bg-particles {
            position: absolute; inset: 0; pointer-events: none; overflow: hidden;
        }
        .intro-particle {
            position: absolute;
            font-size: 1.8rem;
            opacity: 0;
            animation: particleDrift linear infinite;
        }

        @keyframes particleDrift {
            0%   { transform: translateY(110vh) rotate(0deg);   opacity: 0; }
            8%   { opacity: 0.18; }
            92%  { opacity: 0.18; }
            100% { transform: translateY(-15vh) rotate(360deg); opacity: 0; }
        }

        /* ─── Centro del intro ─── */
        .intro-center {
            position: relative; z-index: 1;
            display: flex; flex-direction: column; align-items: center; gap: 0;
        }

        /* ─── Texto "Feliz Día Mamá" ─── */
        .intro-greeting {
            font-family: 'Dancing Script', cursive;
            font-weight: 800;
            font-size: clamp(2.8rem, 8vw, 5.2rem);
            background: linear-gradient(135deg, #7c1228 0%, #b01e3a 40%, #d4728a 70%, #c49560 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            line-height: 1.1;
            opacity: 0;
            transform: translateY(22px) scale(0.92);
            transition: opacity 0.9s cubic-bezier(.22,1,.36,1), transform 0.9s cubic-bezier(.22,1,.36,1);
            filter: drop-shadow(0 4px 14px rgba(176,30,58,.22));
            padding: 0 1rem;
            margin-bottom: 0.2rem;
        }
        .intro-greeting.visible {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .intro-greeting span {
            display: block;
        }
        .intro-sub {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: clamp(1rem, 3vw, 1.35rem);
            color: var(--ink-mid);
            text-align: center;
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.8s ease 0.35s, transform 0.8s ease 0.35s;
            margin-bottom: 2.8rem;
            letter-spacing: 0.5px;
        }
        .intro-sub.visible {
            opacity: 0.75;
            transform: translateY(0);
        }

        /* ─── Flor central ─── */
        .intro-flower-wrap {
            position: relative;
            width: min(320px, 72vw);
            height: min(320px, 72vw);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 2rem;
        }

        /* Círculo de brillo giratorio detrás */
        .flower-glow-ring {
            position: absolute; inset: -8px;
            border-radius: 50%;
            background: conic-gradient(
                rgba(212,114,138,0) 0deg,
                rgba(212,114,138,.35) 60deg,
                rgba(196,149,96,.3) 120deg,
                rgba(176,30,58,.2) 180deg,
                rgba(240,192,204,.4) 240deg,
                rgba(212,114,138,.15) 300deg,
                rgba(212,114,138,0) 360deg
            );
            animation: ringRotate 7s linear infinite;
            opacity: 0;
            transition: opacity 1s ease;
        }
        .flower-glow-ring.visible { opacity: 1; }
        @keyframes ringRotate { to { transform: rotate(360deg); } }

        /* Segundo anillo pulsante */
        .flower-pulse-ring {
            position: absolute; inset: 10px;
            border-radius: 50%;
            border: 1.5px solid rgba(196,149,96,.25);
            animation: pulseRing 2.5s ease-in-out infinite;
        }
        @keyframes pulseRing {
            0%,100% { transform: scale(1);   opacity: 0.4; }
            50%      { transform: scale(1.06); opacity: 0.8; }
        }

        /* SVG de la flor */
        .intro-flower-svg {
            position: relative; z-index: 2;
            width: 58%;
            height: 58%;
            opacity: 0;
            transform: scale(0.5) rotate(-20deg);
            transition: opacity 1s cubic-bezier(.22,1,.36,1), transform 1s cubic-bezier(.22,1,.36,1);
            filter: drop-shadow(0 8px 24px rgba(124,18,40,.28));
        }
        .intro-flower-svg.visible {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }

        /* ─── Pétalos orbitales secuenciales ─── */
        .petals-orbit {
            position: absolute; inset: 0;
            pointer-events: none;
        }

        .orbit-petal {
            position: absolute;
            font-size: clamp(1.6rem, 4.5vw, 2.4rem);
            left: 50%; top: 50%;
            transform-origin: center center;
            opacity: 0;
        }

        /* ─── Barra de carga ─── */
        .intro-loader-bar {
            width: min(280px, 62vw);
            height: 3px;
            background: rgba(196,149,96,.18);
            border-radius: 10px;
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.6s ease 0.5s;
        }
        .intro-loader-bar.visible { opacity: 1; }
        .intro-loader-fill {
            height: 100%;
            width: 0%;
            background: linear-gradient(90deg, var(--wine), var(--gold), var(--rose-soft));
            border-radius: 10px;
            transition: width 4.2s cubic-bezier(.4,0,.2,1);
        }
        .intro-loader-fill.go { width: 100%; }

        .intro-loader-text {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--ink-soft);
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 0.6rem;
            opacity: 0;
            transition: opacity 0.6s ease 0.7s;
        }
        .intro-loader-text.visible { opacity: 1; }

        /* ═══ FONDO DE FLORES ═══ */
        .bg-roses { position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
        .bg-rose  { position:absolute; user-select:none; }

        .bg-rose:nth-child(1)  { font-size:9rem;  top:-3%;   left:-4%;   opacity:.07;  filter:blur(1.5px); transform:rotate(-25deg); animation: floatA 9s ease-in-out infinite; }
        .bg-rose:nth-child(2)  { font-size:8rem;  top:8%;    right:-3%;  opacity:.065; filter:blur(1px);   transform:rotate(20deg);  animation: floatB 11s ease-in-out infinite; }
        .bg-rose:nth-child(3)  { font-size:10rem; bottom:3%; left:-3%;   opacity:.07;  filter:blur(2px);   transform:rotate(-18deg); animation: floatC 10s ease-in-out infinite; }
        .bg-rose:nth-child(4)  { font-size:9rem;  bottom:-2%;right:-2%;  opacity:.065; filter:blur(1.5px); transform:rotate(14deg);  animation: floatA 8s ease-in-out infinite reverse; }
        .bg-rose:nth-child(5)  { font-size:11rem; top:42%;   left:48%;   opacity:.05;  filter:blur(3px);   transform:rotate(-10deg); animation: floatB 13s ease-in-out infinite; }
        .bg-rose:nth-child(6)  { font-size:5rem;  top:18%;   left:5%;    opacity:.09;  filter:blur(0.5px); transform:rotate(12deg);  animation: floatC 7s ease-in-out infinite; }
        .bg-rose:nth-child(7)  { font-size:4.5rem;top:28%;   right:6%;   opacity:.08;  filter:blur(0.5px); transform:rotate(-22deg); animation: floatA 9s ease-in-out infinite 1s; }
        .bg-rose:nth-child(8)  { font-size:5.5rem;top:55%;   left:3%;    opacity:.085; filter:blur(1px);   transform:rotate(30deg);  animation: floatB 8.5s ease-in-out infinite 2s; }
        .bg-rose:nth-child(9)  { font-size:4rem;  top:70%;   right:4%;   opacity:.09;  filter:blur(0.5px); transform:rotate(-15deg); animation: floatC 10s ease-in-out infinite 0.5s; }
        .bg-rose:nth-child(10) { font-size:5rem;  top:35%;   left:12%;   opacity:.07;  filter:blur(1px);   transform:rotate(8deg);   animation: floatA 12s ease-in-out infinite 3s; }
        .bg-rose:nth-child(11) { font-size:4.5rem;top:48%;   right:10%;  opacity:.08;  filter:blur(0.5px); transform:rotate(-35deg); animation: floatB 9s ease-in-out infinite 1.5s; }
        .bg-rose:nth-child(12) { font-size:3.5rem;top:82%;   left:18%;   opacity:.095; filter:blur(0.5px); transform:rotate(20deg);  animation: floatC 7.5s ease-in-out infinite 2.5s; }
        .bg-rose:nth-child(13) { font-size:4rem;  top:15%;   left:42%;   opacity:.07;  filter:blur(1px);   transform:rotate(-28deg); animation: floatA 11s ease-in-out infinite 0.8s; }
        .bg-rose:nth-child(14) { font-size:3rem;  top:60%;   left:55%;   opacity:.085; filter:blur(0.5px); transform:rotate(45deg);  animation: floatB 8s ease-in-out infinite 3.5s; }
        .bg-rose:nth-child(15) { font-size:4.5rem;top:88%;   right:22%;  opacity:.08;  filter:blur(0.5px); transform:rotate(-12deg); animation: floatC 9.5s ease-in-out infinite 1.2s; }
        .bg-rose:nth-child(16) { font-size:3.5rem;top:5%;    left:28%;   opacity:.09;  filter:blur(0.5px); transform:rotate(18deg);  animation: floatA 10s ease-in-out infinite 2.8s; }
        .bg-rose:nth-child(17) { font-size:3rem;  top:75%;   left:38%;   opacity:.075; filter:blur(1px);   transform:rotate(-40deg); animation: floatB 11.5s ease-in-out infinite 0.3s; }
        .bg-rose:nth-child(18) { font-size:4rem;  top:32%;   right:25%;  opacity:.08;  filter:blur(0.5px); transform:rotate(25deg);  animation: floatC 8.5s ease-in-out infinite 4s; }
        .bg-rose:nth-child(19) { font-size:3.5rem;top:92%;   left:62%;   opacity:.07;  filter:blur(1px);   transform:rotate(-8deg);  animation: floatA 9s ease-in-out infinite 1.8s; }
        .bg-rose:nth-child(20) { font-size:2.8rem;top:50%;   left:28%;   opacity:.085; filter:blur(0.5px); transform:rotate(55deg);  animation: floatB 7s ease-in-out infinite 2.2s; }
        .bg-rose:nth-child(21) { font-size:2.5rem;top:22%;   right:35%;  opacity:.1;   filter:blur(0px);   transform:rotate(-18deg); animation: floatC 6s ease-in-out infinite 0.7s; }
        .bg-rose:nth-child(22) { font-size:2.2rem;top:64%;   left:72%;   opacity:.09;  filter:blur(0px);   transform:rotate(30deg);  animation: floatA 8s ease-in-out infinite 3.2s; }
        .bg-rose:nth-child(23) { font-size:2rem;  top:40%;   right:45%;  opacity:.095; filter:blur(0px);   transform:rotate(-42deg); animation: floatB 7.5s ease-in-out infinite 1.4s; }
        .bg-rose:nth-child(24) { font-size:2.5rem;top:78%;   right:55%;  opacity:.085; filter:blur(0px);   transform:rotate(15deg);  animation: floatC 9s ease-in-out infinite 2.6s; }
        .bg-rose:nth-child(25) { font-size:2rem;  top:10%;   right:50%;  opacity:.1;   filter:blur(0px);   transform:rotate(-60deg); animation: floatA 6.5s ease-in-out infinite 0.4s; }

        @keyframes floatA {
            0%,100% { transform: translateY(0px) rotate(var(--rot, -25deg)); }
            50%      { transform: translateY(-14px) rotate(var(--rot, -25deg)); }
        }
        @keyframes floatB {
            0%,100% { transform: translateY(0px) rotate(var(--rot, 20deg)); }
            50%      { transform: translateY(-10px) rotate(var(--rot, 20deg)); }
        }
        @keyframes floatC {
            0%,100% { transform: translateY(0px) rotate(var(--rot, -18deg)); }
            50%      { transform: translateY(-18px) rotate(var(--rot, -18deg)); }
        }

        /* ═══ FLYER SCREEN ═══ */
        .flyer-screen {
            position: fixed; inset: 0; z-index: 3000;
            background: linear-gradient(160deg, #fdf0f3 0%, #f9dce3 45%, #f5cdd6 100%);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            transition: opacity .85s ease;
            opacity: 0;
            pointer-events: none;
        }
        .flyer-screen.show-flyer {
            opacity: 1;
            pointer-events: all;
        }

        .flyer-screen-flowers {
            position: absolute; inset: 0; pointer-events: none; overflow: hidden;
        }
        .flyer-flower {
            position: absolute; user-select: none;
            animation: floatC 8s ease-in-out infinite;
        }
        .flyer-flower:nth-child(1)  { font-size:6rem;  top:-2%;   left:-2%;   opacity:.18; transform:rotate(-30deg); animation-duration:7s; }
        .flyer-flower:nth-child(2)  { font-size:5rem;  top:5%;    right:2%;   opacity:.15; transform:rotate(20deg);  animation-duration:9s; animation-delay:1s; }
        .flyer-flower:nth-child(3)  { font-size:7rem;  bottom:2%; left:1%;    opacity:.16; transform:rotate(-15deg); animation-duration:10s; animation-delay:2s; }
        .flyer-flower:nth-child(4)  { font-size:6rem;  bottom:-2%;right:1%;   opacity:.17; transform:rotate(25deg);  animation-duration:8s; animation-delay:0.5s; }
        .flyer-flower:nth-child(5)  { font-size:4rem;  top:20%;   left:4%;    opacity:.14; transform:rotate(40deg);  animation-duration:6.5s; animation-delay:1.5s; }
        .flyer-flower:nth-child(6)  { font-size:4.5rem;top:25%;   right:3%;   opacity:.13; transform:rotate(-35deg); animation-duration:11s; animation-delay:0.8s; }
        .flyer-flower:nth-child(7)  { font-size:3.5rem;top:55%;   left:3%;    opacity:.15; transform:rotate(15deg);  animation-duration:7.5s; animation-delay:2.5s; }
        .flyer-flower:nth-child(8)  { font-size:3rem;  top:60%;   right:3%;   opacity:.14; transform:rotate(-20deg); animation-duration:9s; animation-delay:3s; }
        .flyer-flower:nth-child(9)  { font-size:5rem;  top:40%;   left:-1%;   opacity:.12; transform:rotate(50deg);  animation-duration:12s; animation-delay:1s; }
        .flyer-flower:nth-child(10) { font-size:4rem;  top:42%;   right:0%;   opacity:.13; transform:rotate(-50deg); animation-duration:8s; animation-delay:2s; }
        .flyer-flower:nth-child(11) { font-size:2.5rem;top:10%;   left:12%;   opacity:.16; transform:rotate(22deg);  animation-duration:6s; animation-delay:0.3s; }
        .flyer-flower:nth-child(12) { font-size:2.5rem;top:12%;   right:10%;  opacity:.15; transform:rotate(-28deg); animation-duration:7s; animation-delay:1.8s; }
        .flyer-flower:nth-child(13) { font-size:3rem;  bottom:10%;left:10%;   opacity:.16; transform:rotate(38deg);  animation-duration:8.5s; animation-delay:2.2s; }
        .flyer-flower:nth-child(14) { font-size:2.8rem;bottom:12%;right:8%;   opacity:.14; transform:rotate(-18deg); animation-duration:9.5s; animation-delay:0.6s; }
        .flyer-flower:nth-child(15) { font-size:3.5rem;top:48%;   left:10%;   opacity:.12; transform:rotate(62deg);  animation-duration:11s; animation-delay:3.5s; }

        .flyer-content {
            display: flex; flex-direction: column; align-items: center; gap: 1.5rem;
            animation: flyerIn .65s cubic-bezier(.22,1,.36,1) both;
            position: relative; z-index: 1;
        }
        @keyframes flyerIn {
            from { opacity:0; transform:scale(.93); }
            to   { opacity:1; transform:scale(1);   }
        }

        .flyer-imagen {
            max-width: min(560px, 92vw);
            max-height: 78vh;
            border-radius: 2rem;
            object-fit: contain;
            box-shadow: 0 0 0 6px rgba(255,255,255,.96), 0 0 0 11px rgba(196,149,96,.35), 0 50px 90px -20px rgba(124,18,40,.42);
        }

        .flyer-badge {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 1.4rem;
            color: var(--wine);
            background: rgba(253,246,240,.92);
            backdrop-filter: blur(14px);
            padding: .6rem 2rem;
            border-radius: 60px;
            border: 1.5px solid rgba(196,149,96,.4);
            display: flex; align-items: center; gap: .6rem;
        }

        .rain-container {
            position: fixed; inset: 0; z-index: 3100;
            pointer-events: none; overflow: hidden;
            transition: opacity .7s ease;
        }
        .petal {
            position: absolute; top: -60px;
            animation: petalFall linear forwards;
            pointer-events: none; will-change: transform, opacity;
        }
        @keyframes petalFall {
            0%   { transform: translateY(0) rotate(0deg); opacity:1; }
            60%  { opacity:.85; }
            100% { transform: translateY(108vh) rotate(300deg); opacity:0; }
        }

        .form-wrapper {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 3rem 1.2rem 4.5rem;
            opacity: 0; visibility: hidden;
            transition: opacity .8s ease, visibility .8s;
        }
        .form-wrapper.visible { opacity:1; visibility:visible; }

        .form-center { width:100%; max-width:680px; }

        .card-event {
            background: var(--glass);
            backdrop-filter: blur(22px);
            border: 1px solid rgba(196,149,96,.22);
            border-radius: 2.2rem;
            padding: 3rem 3.4rem;
            box-shadow: 0 30px 60px -14px rgba(124,18,40,.2);
            position: relative; overflow: hidden;
        }
        .card-event::before {
            content:''; position:absolute; top:0; left:0; right:0; height:4px;
            background: linear-gradient(90deg, var(--wine), var(--gold), var(--rose-soft), var(--wine));
            background-size: 200% 100%;
            animation: shiftGold 5s linear infinite;
        }
        @keyframes shiftGold {
            0%  { background-position:0% 0%; }
            100%{ background-position:200% 0%; }
        }

        .card-deco {
            position:absolute; pointer-events:none;
            font-size:5.5rem; opacity:.065; user-select:none;
        }
        .card-deco-tl  { top:-12px;  left:-14px;  transform:rotate(-25deg); }
        .card-deco-br  { bottom:-16px; right:-10px; transform:rotate(15deg); }
        .card-deco-tr  { top:-10px;  right:-12px; transform:rotate(28deg);  font-size:4.5rem; }
        .card-deco-bl  { bottom:-14px; left:-10px; transform:rotate(-18deg); font-size:4rem; }
        .card-deco-ml  { top:38%;   left:-18px;  transform:rotate(-35deg); font-size:3.5rem; opacity:.05; }
        .card-deco-mr  { top:55%;   right:-16px; transform:rotate(20deg);  font-size:4rem;   opacity:.05; }
        .card-deco-tm  { top:-14px; left:42%;    transform:rotate(10deg);  font-size:3rem;   opacity:.06; }
        .card-deco-bm  { bottom:-12px; left:50%; transform:rotate(-8deg);  font-size:3.5rem; opacity:.06; }

        @media (max-width:680px) {
            .card-event { padding:2.2rem 1.4rem 2rem; border-radius:1.6rem; }
        }

        .hero-header {
            display:flex; align-items:center; gap:1.2rem; flex-wrap:wrap;
            background: linear-gradient(120deg, rgba(250,234,237,.85), rgba(242,227,204,.5));
            border: 1px solid rgba(196,149,96,.22);
            border-radius: 1.4rem;
            padding: 1.2rem 1.5rem;
            margin-bottom: 2rem;
        }
        .logo-img { width:60px; height:60px; object-fit:contain; flex-shrink:0; filter:drop-shadow(0 4px 10px rgba(124,18,40,.22)); }
        .hero-texts { flex:1; min-width:170px; }
        .hero-eyebrow { font-size:.68rem; font-weight:700; letter-spacing:2.8px; text-transform:uppercase; color:var(--gold); margin-bottom:.25rem; }
        .hero-title { font-family:'Playfair Display',serif; font-size:1.95rem; font-weight:800; color:var(--wine); line-height:1.1; margin-bottom:.2rem; }
        .hero-sub { font-size:.78rem; color:var(--ink-soft); font-weight:600; }

        .info-row {
            display: flex;
            flex-wrap: nowrap;
            gap: 1rem;
            margin-bottom: 1.8rem;
            align-items: center;
            justify-content: center;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            border-radius: 50px;
            padding: .5rem 1.3rem;
            font-size: .85rem;
            font-weight: 700;
            white-space: nowrap;
            flex: 0 0 auto;
        }
        .pill-wine {
            background: rgba(124,18,40,.08);
            border: 1px solid rgba(124,18,40,.18);
            color: var(--wine);
        }
        .pill-gold {
            background: rgba(196,149,96,.1);
            border: 1px solid rgba(196,149,96,.3);
            color: #7a5222;
            text-decoration: none;
            transition: all .2s ease;
        }
        .pill-gold:hover {
            background: rgba(196,149,96,.22);
            transform: translateY(-1px);
        }

        @media (max-width: 680px) {
            .info-row { gap: 0.7rem; flex-wrap: wrap; }
            .pill { padding: 0.45rem 1rem; font-size: 0.75rem; }
        }
        @media (max-width: 550px) {
            .info-row { flex-direction: column; align-items: stretch; gap: 0.6rem; }
            .pill { justify-content: center; white-space: normal; text-align: center; }
        }

        .event-alert {
            display:flex; gap:.9rem; align-items:flex-start;
            background: linear-gradient(120deg, rgba(250,234,237,.9), rgba(250,234,237,.5));
            border:1px solid rgba(176,30,58,.18); border-left:4px solid var(--crimson);
            border-radius:1.1rem; padding:1rem 1.2rem;
            font-size:.86rem; color:var(--wine); margin-bottom:1.8rem; line-height:1.6;
        }
        .event-alert i { font-size:1.3rem; flex-shrink:0; margin-top:.1rem; }

        .divider {
            display:flex; align-items:center; gap:.7rem;
            color:var(--gold); font-size:.7rem; font-weight:700; letter-spacing:3px;
            text-transform:uppercase; margin:1.6rem 0 1.8rem;
        }
        .divider::before,.divider::after { content:''; flex:1; height:1px; background:linear-gradient(90deg,transparent,rgba(175, 109, 35, 0.78),transparent); }

        .row-fields { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem; }
        @media (max-width:540px) { .row-fields { grid-template-columns:1fr; } }
        .field-group { display:flex; flex-direction:column; gap:.45rem; }
        .field-label { font-size:.73rem; font-weight:800; letter-spacing:1px; text-transform:uppercase; color:var(--ink-mid); }
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:.95rem; top:50%; transform:translateY(-50%); color:var(--rose-soft); font-size:.95rem; pointer-events:none; }
        .form-input {
            width:100%; padding:.78rem 1rem .78rem 2.6rem;
            border:1.5px solid rgba(196,149,96,.28); border-radius:.85rem;
            font-family:'Nunito',sans-serif; font-size:.92rem; font-weight:600; color:var(--ink);
            background:rgba(255,252,250,.88); outline:none;
            transition:border-color .22s, box-shadow .22s, background .22s;
        }
        .form-input::placeholder { color:#c4a0aa; font-weight:400; }
        .form-input:focus { border-color:var(--crimson); box-shadow:0 0 0 3.5px rgba(176,30,58,.13); background:#fff; }
        .form-input.readonly-field { background:linear-gradient(120deg,rgba(250,234,237,.7),rgba(242,227,204,.4)); color:var(--wine); cursor:default; }
        .form-input:disabled { opacity: 0.7; background: #f0f0f0; cursor: not-allowed; }
        .field-hint { font-size:.72rem; color:var(--ink-soft); padding-left:.15rem; }
        .auto-msg { font-size:.78rem; font-style:italic; padding-left:.15rem; min-height:1rem; transition:color .25s; }
        .status-ok { color:#15803d; }
        .status-pending { color:var(--ink-soft); }
        .status-error { color:var(--crimson); }
        .status-warning { color:#d97706; }

        .btn-submit {
            display:flex; align-items:center; justify-content:center; gap:.65rem; width:100%;
            padding:1rem 2rem;
            background:linear-gradient(108deg, var(--wine) 0%, var(--crimson) 55%, var(--rose-soft) 100%);
            color:#fff; font-family:'Nunito',sans-serif; font-size:.96rem; font-weight:800; letter-spacing:.4px;
            border:none; border-radius:50px; cursor:pointer; margin-top:1.6rem;
            box-shadow:0 12px 30px -8px rgba(124,18,40,.45);
            transition:transform .25s cubic-bezier(.34,1.56,.64,1), box-shadow .25s;
        }
        .btn-submit:hover { transform:translateY(-3px) scale(1.012); box-shadow:0 20px 40px -10px rgba(124,18,40,.48); }
        .btn-submit:active { transform:scale(.98); }
        .btn-submit:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .legal-strip {
            margin-top:1.4rem; padding:.9rem 1.2rem;
            background:rgba(250,234,237,.5); border:1px dashed rgba(196,149,96,.3);
            border-radius:1rem; font-size:.76rem; color:var(--ink-soft); text-align:center; line-height:1.6;
        }

        .modal-overlay {
            position:fixed; inset:0; z-index:9000;
            background:rgba(44,16,24,.52); backdrop-filter:blur(7px);
            display:flex; align-items:center; justify-content:center; padding:1rem;
            opacity:0; pointer-events:none; transition:opacity .3s ease;
        }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box {
            background:#fff; border-radius:2rem; max-width:400px; width:100%; overflow:hidden;
            box-shadow:0 40px 80px -16px rgba(124,18,40,.4);
            transform:scale(.88) translateY(20px);
            transition:transform .38s cubic-bezier(.34,1.56,.64,1);
        }
        .modal-overlay.open .modal-box { transform:scale(1) translateY(0); }

        .modal-header {
            background:linear-gradient(135deg, var(--wine), var(--crimson));
            padding:1.8rem 1.8rem 2.6rem; text-align:center; position:relative; overflow:hidden;
        }
        .modal-header::after { content:'🌹'; position:absolute; font-size:6rem; opacity:.12; bottom:-1rem; right:-1rem; pointer-events:none; }
        .modal-header::before {
            content:''; position:absolute; bottom:-1px; left:0; right:0; height:32px;
            background:#fff; clip-path:ellipse(55% 100% at 50% 100%);
        }
        .modal-icon {
            width:62px; height:62px;
            background:rgba(255,255,255,.18); border:2px solid rgba(255,255,255,.35);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            font-size:1.8rem; margin:0 auto .9rem;
        }
        .modal-htitle { font-family:'Playfair Display',serif; font-size:1.5rem; font-weight:700; color:#fff; line-height:1.2; }
        .modal-body-area { padding:.6rem 1.8rem 1.8rem; text-align:center; }
        .modal-desc { font-size:.88rem; color:var(--ink-mid); line-height:1.7; margin-bottom:1.4rem; }
        .modal-desc strong { color:var(--ink); }
        .modal-chips { display:flex; justify-content:center; gap:.7rem; flex-wrap:wrap; margin-bottom:1.4rem; }
        .modal-chip {
            display:inline-flex; align-items:center; gap:.4rem;
            background:var(--blush-lt); border:1px solid rgba(176,30,58,.18);
            border-radius:40px; padding:.35rem .95rem; font-size:.76rem; font-weight:700;
            color:var(--wine); text-decoration:none; transition:background .2s;
        }
        .modal-chip:hover { background:var(--blush); }
        .modal-btn-back {
            display:inline-flex; align-items:center; gap:.45rem;
            padding:.72rem 2rem;
            background:linear-gradient(108deg, var(--wine), var(--crimson));
            color:#fff; font-family:'Nunito',sans-serif; font-size:.88rem; font-weight:800;
            border:none; border-radius:50px; cursor:pointer;
            box-shadow:0 8px 22px -6px rgba(124,18,40,.4);
            transition:transform .22s, box-shadow .22s;
        }
        .modal-btn-back:hover { transform:translateY(-2px); box-shadow:0 14px 28px -8px rgba(124,18,40,.45); }

        .loading-spinner {
            display: inline-block;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ─── Botón flotante de música ─── */
        #musicToggle {
            position: fixed;
            bottom: 1.4rem;
            right: 1.4rem;
            z-index: 9999;
            width: 48px; height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--wine), var(--crimson));
            border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            color: #fff;
            box-shadow: 0 6px 20px -4px rgba(124,18,40,.5);
            transition: transform .25s, box-shadow .25s;
            opacity: 0;
            pointer-events: none;
        }
        #musicToggle.show {
            opacity: 1;
            pointer-events: all;
            animation: popIn .45s cubic-bezier(.34,1.56,.64,1) both;
        }
        #musicToggle:hover { transform: scale(1.12); box-shadow: 0 10px 28px -6px rgba(124,18,40,.55); }
        @keyframes popIn {
            from { opacity:0; transform: scale(0.5); }
            to   { opacity:1; transform: scale(1); }
        }
    </style>
</head>
<body class="flyer-active">

<!-- ═══ AUDIO - CONFIGURADO PARA AUTOPLAY ═══ -->
<audio id="bgMusic" loop preload="auto" muted>
    <source src="{{ asset('img/musica.mp3') }}" type="audio/mpeg">
</audio>

<!-- ─── Botón flotante mute/unmute ─── -->
<button id="musicToggle" title="Pausar/Reproducir música" aria-label="Pausar/Reproducir música">
    🎵
</button>

<!-- ═══════════════════════
     PANTALLA INTRO
     ═══════════════════════ -->
<div id="introScreen">
    <!-- Partículas de fondo -->
    <div class="intro-bg-particles" id="introBgParticles"></div>

    <div class="intro-center">
        <!-- Flor con pétalos -->
        <div class="intro-flower-wrap">
            <div class="flower-glow-ring" id="flowerGlowRing"></div>
            <div class="flower-pulse-ring"></div>

            <!-- SVG Flor -->
            <svg class="intro-flower-svg" id="introFlowerSvg"
                 viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <radialGradient id="petalGrad1" cx="50%" cy="30%" r="70%">
                        <stop offset="0%" stop-color="#f9a8b8"/>
                        <stop offset="50%" stop-color="#e05070"/>
                        <stop offset="100%" stop-color="#9b1030"/>
                    </radialGradient>
                    <radialGradient id="petalGrad2" cx="50%" cy="30%" r="70%">
                        <stop offset="0%" stop-color="#fcc8d4"/>
                        <stop offset="50%" stop-color="#d4728a"/>
                        <stop offset="100%" stop-color="#7c1228"/>
                    </radialGradient>
                    <radialGradient id="centerGrad" cx="40%" cy="35%" r="65%">
                        <stop offset="0%" stop-color="#fde68a"/>
                        <stop offset="50%" stop-color="#c49560"/>
                        <stop offset="100%" stop-color="#8b5e3c"/>
                    </radialGradient>
                    <filter id="softShadow">
                        <feDropShadow dx="0" dy="3" stdDeviation="4" flood-color="rgba(124,18,40,0.35)"/>
                    </filter>
                </defs>
                <!-- Pétalos exteriores (capa trasera) -->
                <g filter="url(#softShadow)">
                    <ellipse cx="100" cy="55"  rx="16" ry="34" fill="url(#petalGrad2)" opacity=".75" transform="rotate(0   100 100)"/>
                    <ellipse cx="100" cy="55"  rx="16" ry="34" fill="url(#petalGrad2)" opacity=".75" transform="rotate(45  100 100)"/>
                    <ellipse cx="100" cy="55"  rx="16" ry="34" fill="url(#petalGrad2)" opacity=".75" transform="rotate(90  100 100)"/>
                    <ellipse cx="100" cy="55"  rx="16" ry="34" fill="url(#petalGrad2)" opacity=".75" transform="rotate(135 100 100)"/>
                    <ellipse cx="100" cy="55"  rx="16" ry="34" fill="url(#petalGrad2)" opacity=".75" transform="rotate(180 100 100)"/>
                    <ellipse cx="100" cy="55"  rx="16" ry="34" fill="url(#petalGrad2)" opacity=".75" transform="rotate(225 100 100)"/>
                    <ellipse cx="100" cy="55"  rx="16" ry="34" fill="url(#petalGrad2)" opacity=".75" transform="rotate(270 100 100)"/>
                    <ellipse cx="100" cy="55"  rx="16" ry="34" fill="url(#petalGrad2)" opacity=".75" transform="rotate(315 100 100)"/>
                </g>
                <!-- Pétalos interiores (capa delantera) -->
                <g filter="url(#softShadow)">
                    <ellipse cx="100" cy="62"  rx="12" ry="26" fill="url(#petalGrad1)" transform="rotate(22.5  100 100)"/>
                    <ellipse cx="100" cy="62"  rx="12" ry="26" fill="url(#petalGrad1)" transform="rotate(67.5  100 100)"/>
                    <ellipse cx="100" cy="62"  rx="12" ry="26" fill="url(#petalGrad1)" transform="rotate(112.5 100 100)"/>
                    <ellipse cx="100" cy="62"  rx="12" ry="26" fill="url(#petalGrad1)" transform="rotate(157.5 100 100)"/>
                    <ellipse cx="100" cy="62"  rx="12" ry="26" fill="url(#petalGrad1)" transform="rotate(202.5 100 100)"/>
                    <ellipse cx="100" cy="62"  rx="12" ry="26" fill="url(#petalGrad1)" transform="rotate(247.5 100 100)"/>
                    <ellipse cx="100" cy="62"  rx="12" ry="26" fill="url(#petalGrad1)" transform="rotate(292.5 100 100)"/>
                    <ellipse cx="100" cy="62"  rx="12" ry="26" fill="url(#petalGrad1)" transform="rotate(337.5 100 100)"/>
                </g>
                <!-- Centro -->
                <circle cx="100" cy="100" r="22" fill="url(#centerGrad)" filter="url(#softShadow)"/>
                <circle cx="100" cy="100" r="14" fill="#f2e3cc" opacity=".7"/>
                <circle cx="94"  cy="94"  r="4"  fill="rgba(255,255,255,0.55)"/>
            </svg>

            <!-- Pétalos orbitales animados (JS los mueve) -->
            <div class="petals-orbit" id="petalsOrbit">
                <div class="orbit-petal" id="op0">🌸</div>
                <div class="orbit-petal" id="op1">🌹</div>
                <div class="orbit-petal" id="op2">🌺</div>
                <div class="orbit-petal" id="op3">💗</div>
                <div class="orbit-petal" id="op4">🌸</div>
                <div class="orbit-petal" id="op5">🌹</div>
            </div>
        </div>

        <!-- Texto principal -->
        <div class="intro-greeting" id="introGreeting">
            <span>🌸¡Festejemos a Mamá!🌸</span>
        </div>
        <div class="intro-sub" id="introSub">Bienvenida, Ingeniera Mamá</div>

        <!-- Barra de carga -->
        <div class="intro-loader-bar" id="introLoaderBar">
            <div class="intro-loader-fill" id="introLoaderFill"></div>
        </div>
        <div class="intro-loader-text" id="introLoaderText">✨ Cargando invitación especial ✨</div>
    </div>
</div>

<!-- ═══ FONDO DE FLORES ═══ -->
<div class="bg-roses" aria-hidden="true">
    <span class="bg-rose">🌹</span><span class="bg-rose">🌸</span>
    <span class="bg-rose">🌹</span><span class="bg-rose">🌺</span>
    <span class="bg-rose">💐</span><span class="bg-rose">🌸</span>
    <span class="bg-rose">🌹</span><span class="bg-rose">🌺</span>
    <span class="bg-rose">💗</span><span class="bg-rose">🌸</span>
    <span class="bg-rose">🌹</span><span class="bg-rose">💐</span>
    <span class="bg-rose">🌺</span><span class="bg-rose">🌸</span>
    <span class="bg-rose">🌹</span><span class="bg-rose">💗</span>
    <span class="bg-rose">🌺</span><span class="bg-rose">🌸</span>
    <span class="bg-rose">🌹</span><span class="bg-rose">💐</span>
    <span class="bg-rose">🌸</span><span class="bg-rose">🌹</span>
    <span class="bg-rose">💗</span><span class="bg-rose">🌺</span>
    <span class="bg-rose">🌸</span>
</div>

<!-- ═══ PANTALLA DEL FLYER ═══ -->
<div id="flyerScreen" class="flyer-screen">
    <div class="flyer-screen-flowers" aria-hidden="true">
        <span class="flyer-flower">🌹</span><span class="flyer-flower">🌸</span>
        <span class="flyer-flower">🌺</span><span class="flyer-flower">💐</span>
        <span class="flyer-flower">🌸</span><span class="flyer-flower">🌹</span>
        <span class="flyer-flower">💗</span><span class="flyer-flower">🌺</span>
        <span class="flyer-flower">🌸</span><span class="flyer-flower">🌹</span>
        <span class="flyer-flower">💐</span><span class="flyer-flower">🌺</span>
        <span class="flyer-flower">🌸</span><span class="flyer-flower">💗</span>
        <span class="flyer-flower">🌹</span>
    </div>
    <div class="flyer-content">
        <img src="{{ asset('img/mama.jpeg') }}" alt="Flyer Festejemos a Mamá" class="flyer-imagen"
             onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 820 580%22%3E%3Crect width=%22820%22 height=%22580%22 fill=%22%23f9dce3%22/%3E%3Ctext x=%22410%22 y=%22210%22 text-anchor=%22middle%22 fill=%22%237c1228%22 font-size=%2252%22 font-weight=%22bold%22 font-family=%22Georgia%22%3EFestejemos a Mamá%3C/text%3E%3Ctext x=%22410%22 y=%22285%22 text-anchor=%22middle%22 fill=%22%23b01e3a%22 font-size=%2228%22 font-family=%22Georgia%22%3ECena Show Bailable%3C/text%3E%3Ctext x=%22410%22 y=%22370%22 text-anchor=%22middle%22 fill=%22%23993333%22 font-size=%2222%22 font-family=%22Georgia%22%3EViernes 08 de mayo · Auditorio CECAP%3C/text%3E%3Ctext x=%22410%22 y=%22440%22 text-anchor=%22middle%22 font-size=%2248%22%3E🌹 🌸 🌺%3C/text%3E%3C/svg%3E';">
        <div class="flyer-badge">🌹Festejemos a Mamá🌸</div>
    </div>
</div>

<div id="rainContainer" class="rain-container"></div>

<div id="errorModal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-icon" id="modalIcon">🔒</div>
            <h2 class="modal-htitle" id="modalTitle">Datos no coinciden</h2>
        </div>
        <div class="modal-body-area">
            <p class="modal-desc" id="modalMessage">
                El CIP y el DNI ingresados no coinciden entre sí.
                <br><br>
                ✔ Si cometiste un error al escribir tus datos, haz clic en <strong>"Corregir mis datos"</strong> e inténtalo nuevamente.
                <br><br>
                📞 Si estás segura de que los datos son correctos y el problema continúa, comunícate al número de soporte.
            </p>
            <div class="modal-chips">
                <a href="tel:+5144340010" class="modal-chip">
                    <i class="bi bi-telephone-fill"></i> 044 340010 anexo 201
                </a>
            </div>
            <button class="modal-btn-back" id="closeErrorModal">
                <i class="bi bi-arrow-left-short"></i> Corregir mis datos
            </button>
        </div>
    </div>
</div>

<div id="formWrapper" class="form-wrapper">
    <div class="form-center">
        <div class="card-event">
            <span class="card-deco card-deco-tl" aria-hidden="true">🌹</span>
            <span class="card-deco card-deco-br" aria-hidden="true">🌸</span>
            <span class="card-deco card-deco-tr" aria-hidden="true">🌺</span>
            <span class="card-deco card-deco-bl" aria-hidden="true">💐</span>
            <span class="card-deco card-deco-ml" aria-hidden="true">🌸</span>
            <span class="card-deco card-deco-mr" aria-hidden="true">🌹</span>
            <span class="card-deco card-deco-tm" aria-hidden="true">💗</span>
            <span class="card-deco card-deco-bm" aria-hidden="true">🌺</span>

            <div class="hero-header">
                <img src="{{ asset('img/logo.png') }}" alt="Logo CIPCDLL" class="logo-img"
                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2245%22 fill=%22%237c1228%22/%3E%3Ctext x=%2250%22 y=%2268%22 text-anchor=%22middle%22 fill=%22white%22 font-size=%2244%22 font-weight=%22bold%22 font-family=%22Georgia%22%3EC%3C/text%3E%3C/svg%3E';">
                <div class="hero-texts">
                    <div class="hero-eyebrow">✦ CIP — Consejo Departamental La Libertad</div>
                    <div class="hero-title">Festejemos a Mamá 🌹</div>
                    <div class="hero-sub"><i class="bi bi-stars" style="color:var(--gold)"></i> Cena Show Bailable · Comité de Damas</div>
                </div>
            </div>

            <div class="info-row">
                <span class="pill pill-wine"><i class="bi bi-calendar-heart"></i> Viernes 08 de mayo · 5:00 PM</span>
                <span class="pill pill-wine"><i class="bi bi-building"></i> Auditorio CECAP</span>
                <a href="https://maps.app.goo.gl/rQxpX9LdMNJ1U8PQ7" target="_blank" class="pill pill-gold">
                    <i class="bi bi-pin-map-fill"></i> Ver en Maps <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>

            <div class="event-alert">
                <i class="bi bi-stars"></i>
                <div>
                    <strong>✨ Evento exclusivo para Ingenieras que también son Mamás ✨</strong><br>
                    Solo ingenieras con CIP activo que sean madres. ¡Cupos limitados!
                    — regalo especial para quienes lleguen antes de las 5:00 PM.
                </div>
            </div>

            <div class="divider">✦ Formulario de inscripción ✦</div>

            <form id="registroEventoForm" novalidate>
                @csrf

                <div class="row-fields">
                    <div class="field-group">
                        <label for="cipInput" class="field-label"><i class="bi bi-person-badge-fill"></i> CIP *</label>
                        <div class="input-wrap">
                            <i class="bi bi-card-heading input-icon"></i>
                            <input type="tel" class="form-input" id="cipInput"
                                   placeholder="Ej: 16587" maxlength="6"
                                   autocomplete="off" inputmode="numeric">
                        </div>
                        <span class="field-hint">Máximo 6 dígitos numéricos</span>
                    </div>
                    <div class="field-group">
                        <label for="dniInput" class="field-label"><i class="bi bi-qr-code"></i> DNI *</label>
                        <div class="input-wrap">
                            <i class="bi bi-credit-card input-icon"></i>
                            <input type="tel" class="form-input" id="dniInput"
                                   placeholder="76543210" maxlength="8" minlength="8"
                                   autocomplete="off" inputmode="numeric">
                        </div>
                        <span class="field-hint">Exactamente 8 dígitos</span>
                    </div>
                </div>

                <div class="field-group" style="margin-bottom:1rem">
                    <label for="nombreInput" class="field-label"><i class="bi bi-person-lines-fill"></i> Nombres y Apellidos *</label>
                    <div class="input-wrap">
                        <i class="bi bi-file-person input-icon"></i>
                        <input type="text" class="form-input readonly-field" id="nombreInput"
                               placeholder="Se autocompleta al verificar CIP + DNI" readonly>
                    </div>
                    <div class="auto-msg status-pending" id="autoMsg">
                        <i class="bi bi-info-circle"></i> Ingresa tu CIP (máx 6 dígitos) y luego tu DNI (8 dígitos)
                    </div>
                </div>

                <div class="row-fields">
                    <div class="field-group">
                        <label for="telefonoInput" class="field-label"><i class="bi bi-phone"></i> Teléfono *</label>
                        <div class="input-wrap">
                            <i class="bi bi-telephone-fill input-icon"></i>
                            <input type="tel" class="form-input" id="telefonoInput"
                                placeholder="+51 987 654 321"
                                maxlength="9" pattern="\d{9}"
                                inputmode="numeric" disabled>
                        </div>
                    </div>
                    <div class="field-group">
                        <label for="correoInput" class="field-label"><i class="bi bi-envelope"></i> Correo *</label>
                        <div class="input-wrap">
                            <i class="bi bi-at input-icon"></i>
                            <input type="email" class="form-input" id="correoInput"
                                   placeholder="ingeniera@ejemplo.com" disabled>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit" id="submitBtn" disabled>
                    <i class="bi bi-send-check-fill"></i>
                    INSCRIBIRME COMO INGENIERA MAMÁ
                </button>

                <div class="legal-strip">
                    <i class="bi bi-heart-fill" style="color:var(--rose-soft)"></i>
                    Al registrarme acepto la verificación de mi CIP y DNI.<br>
                    <strong>🌹 con amor,el CIP La Libertad 🌹</strong>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    // ==================== MÚSICA - AUTOPLAY GARANTIZADO ====================
    const bgMusic = document.getElementById('bgMusic');
    const musicBtn = document.getElementById('musicToggle');
    let musicPlaying = false;
    let musicStarted = false;
    let fadeInterval = null;

    // Función principal para iniciar música automáticamente
    function startMusicAutomatically() {
        if (!bgMusic || musicStarted) return;
        
        // Estrategia: Iniciar con volumen bajo y hacer fade-in
        bgMusic.volume = 0.15;
        bgMusic.muted = false;
        
        const playPromise = bgMusic.play();
        
        if (playPromise !== undefined) {
            playPromise.then(() => {
                musicStarted = true;
                musicPlaying = true;
                if (musicBtn) musicBtn.textContent = '🎵';
                
                // Fade-in progresivo del volumen
                let vol = 0.15;
                if (fadeInterval) clearInterval(fadeInterval);
                fadeInterval = setInterval(() => {
                    vol = Math.min(vol + 0.035, 0.55);
                    bgMusic.volume = vol;
                    if (vol >= 0.55) clearInterval(fadeInterval);
                }, 120);
                
                console.log("✓ Música iniciada automáticamente!");
            }).catch((error) => {
                console.log("Autoplay necesita interacción mínima:", error);
                // Si falla, esperar cualquier interacción
                waitForFirstInteraction();
            });
        }
    }

    // Esperar la primera interacción del usuario (cualquier clic)
    function waitForFirstInteraction() {
        if (musicStarted) return;
        
        const events = ['click', 'touchstart', 'touchend'];
        let interactionCount = 0;
        
        function tryPlayOnInteraction() {
            if (musicStarted) return;
            
            interactionCount++;
            bgMusic.volume = 0.15;
            bgMusic.muted = false;
            
            bgMusic.play().then(() => {
                musicStarted = true;
                musicPlaying = true;
                if (musicBtn) musicBtn.textContent = '🎵';
                
                // Fade-in
                let vol = 0.15;
                if (fadeInterval) clearInterval(fadeInterval);
                fadeInterval = setInterval(() => {
                    vol = Math.min(vol + 0.04, 0.55);
                    bgMusic.volume = vol;
                    if (vol >= 0.55) clearInterval(fadeInterval);
                }, 100);
                
                // Remover listeners después de éxito
                events.forEach(ev => document.removeEventListener(ev, tryPlayOnInteraction));
            }).catch(() => {});
        }
        
        events.forEach(ev => document.addEventListener(ev, tryPlayOnInteraction, { once: false }));
        
        // Mostrar hint sutil si después de 2 segundos no ha iniciado
        setTimeout(() => {
            if (!musicStarted) {
                showSubtleMusicHint();
            }
        }, 2000);
    }
    
    function showSubtleMusicHint() {
        // Solo mostrar si aún no hay música
        if (musicStarted) return;
        
       
        
        hint.onclick = () => {
            bgMusic.play().then(() => {
                musicStarted = true;
                musicPlaying = true;
                if (musicBtn) musicBtn.textContent = '🎵';
                bgMusic.volume = 0.5;
                hint.remove();
            }).catch(() => {});
        };
        
        document.body.appendChild(hint);
        
        // Auto-remover después de 6 segundos
        setTimeout(() => {
            if (hint.parentNode) hint.remove();
        }, 6000);
    }
    
    // Agregar animación CSS para el hint
    const styleSheet = document.createElement("style");
    styleSheet.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }
    `;
    document.head.appendChild(styleSheet);
    
    // INICIAR MÚSICA INMEDIATAMENTE AL CARGAR LA PÁGINA
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', startMusicAutomatically);
    } else {
        startMusicAutomatically();
    }
    
    // Botón de control manual
    if (musicBtn) {
        musicBtn.addEventListener('click', () => {
            if (!bgMusic) return;
            if (musicPlaying) {
                bgMusic.pause();
                musicPlaying = false;
                musicBtn.textContent = '🔇';
                if (fadeInterval) clearInterval(fadeInterval);
            } else {
                bgMusic.volume = 0.5;
                bgMusic.play().catch(() => {});
                musicPlaying = true;
                musicStarted = true;
                musicBtn.textContent = '🎵';
            }
        });
    }

    // ==================== ANIMACIÓN INTRO ====================
    const PETAL_COUNT = 6;
    const ORBIT_R = 52;
    const angles = Array.from({ length: PETAL_COUNT }, (_, i) => (360 / PETAL_COUNT) * i);

    // Partículas de fondo
    (function spawnBgParticles() {
        const container = document.getElementById('introBgParticles');
        if (!container) return;
        const items = ['🌸','🌹','💗','🌺','✿','❀','🌼'];
        for (let i = 0; i < 28; i++) {
            const el = document.createElement('div');
            el.className = 'intro-particle';
            el.textContent = items[i % items.length];
            el.style.left = (Math.random() * 100) + '%';
            el.style.fontSize = (Math.random() * 18 + 10) + 'px';
            el.style.animationDuration = (Math.random() * 10 + 8) + 's';
            el.style.animationDelay = (Math.random() * 10) + 's';
            container.appendChild(el);
        }
    })();

    function setPetalPos(idx, orbitRadiusPct) {
        const el = document.getElementById('op' + idx);
        if (!el) return;
        const wrap = document.querySelector('.intro-flower-wrap');
        const wSize = wrap ? wrap.offsetWidth : 280;
        const r = (wSize / 2) * (orbitRadiusPct / 100);
        const rad = (angles[idx] - 90) * (Math.PI / 180);
        const cx = wSize / 2 + Math.cos(rad) * r;
        const cy = wSize / 2 + Math.sin(rad) * r;
        const size = parseFloat(getComputedStyle(el).fontSize);
        el.style.left = (cx - size / 2) + 'px';
        el.style.top = (cy - size / 2) + 'px';
    }

    function animatePetalSequence(startDelay) {
        const RISE_DUR = 520;
        const HOLD_DUR = 320;
        const FALL_DUR = 450;
        const GAP = 180;
        const CYCLE_DUR = RISE_DUR + HOLD_DUR + FALL_DUR + GAP;
        const TOTAL_LOOP = CYCLE_DUR * PETAL_COUNT + 300;

        function animateOnePetal(idx) {
            const el = document.getElementById('op' + idx);
            if (!el) return;
            setPetalPos(idx, ORBIT_R);

            const wrap = document.querySelector('.intro-flower-wrap');
            const wSize = wrap ? wrap.offsetWidth : 280;
            const r = (wSize / 2) * (ORBIT_R / 100);
            const rad = (angles[idx] - 90) * (Math.PI / 180);
            const cx = wSize / 2 + Math.cos(rad) * r;
            const cy = wSize / 2 + Math.sin(rad) * r;
            const size = parseFloat(getComputedStyle(el).fontSize);
            const baseTop = cy - size / 2;
            const risePx = 22;

            el.style.transition = 'none';
            el.style.opacity = '0';
            el.style.top = baseTop + 'px';

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    el.style.transition = `opacity ${RISE_DUR}ms cubic-bezier(.22,1,.36,1), top ${RISE_DUR}ms cubic-bezier(.22,1,.36,1)`;
                    el.style.opacity = '1';
                    el.style.top = (baseTop - risePx) + 'px';

                    setTimeout(() => {
                        el.style.transition = `top ${HOLD_DUR * 0.4}ms ease-out`;
                        el.style.top = (baseTop - risePx - 5) + 'px';

                        setTimeout(() => {
                            el.style.transition = `opacity ${FALL_DUR}ms ease-in, top ${FALL_DUR}ms cubic-bezier(.55,0,1,.85)`;
                            el.style.opacity = '0';
                            el.style.top = baseTop + 'px';
                        }, HOLD_DUR * 0.4 + 60);
                    }, RISE_DUR);
                });
            });
        }

        function runCycle(delay) {
            setTimeout(() => {
                for (let i = 0; i < PETAL_COUNT; i++) {
                    setTimeout(() => animateOnePetal(i), i * (RISE_DUR + HOLD_DUR + FALL_DUR * 0.3 + GAP * 0.7));
                }
                runCycle(TOTAL_LOOP);
            }, delay);
        }

        runCycle(startDelay);
    }
    // ==================== PAUSAR MÚSICA AL SALIR DE LA PESTAÑA ====================
    document.addEventListener('visibilitychange', () => {
        if (!bgMusic || !musicStarted) return;
        if (document.hidden) {
            bgMusic.pause();
            musicPlaying = false;
            if (musicBtn) musicBtn.textContent = '🔇';
        } else {
            bgMusic.play().catch(() => {});
            musicPlaying = true;
            if (musicBtn) musicBtn.textContent = '🎵';
        }
    });
    // ==================== ORQUESTACIÓN DEL INTRO ====================
    let pageReady = false;
    let introDone = false;
    const MIN_INTRO_MS = 4800;

    window.addEventListener('load', () => { pageReady = true; tryTransition(); });
    setTimeout(() => { pageReady = true; tryTransition(); }, MIN_INTRO_MS + 500);

    function tryTransition() {
        if (!pageReady || !introDone) return;
        doTransition();
    }

    // Arrancar animaciones del intro
    setTimeout(() => {
        const svg = document.getElementById('introFlowerSvg');
        const ring = document.getElementById('flowerGlowRing');
        if (svg) svg.classList.add('visible');
        if (ring) ring.classList.add('visible');

        animatePetalSequence(600);

        setTimeout(() => {
            const g = document.getElementById('introGreeting');
            const s = document.getElementById('introSub');
            if (g) g.classList.add('visible');
            setTimeout(() => { if (s) s.classList.add('visible'); }, 200);
        }, 350);

        // Barra de carga
        setTimeout(() => {
            const bar = document.getElementById('introLoaderBar');
            const fill = document.getElementById('introLoaderFill');
            const txt = document.getElementById('introLoaderText');
            if (bar) bar.classList.add('visible');
            if (txt) txt.classList.add('visible');
            
            requestAnimationFrame(() => requestAnimationFrame(() => {
                if (fill) fill.classList.add('go');
            }));
        }, 500);

        setTimeout(() => {
            introDone = true;
            tryTransition();
        }, MIN_INTRO_MS);
    }, 200);

    // ==================== TRANSICIÓN INTRO → FLYER → FORM ====================
    let swalShown = false;

    function doTransition() {
        const introEl = document.getElementById('introScreen');
        const flyerEl = document.getElementById('flyerScreen');

        if (flyerEl) {
            flyerEl.style.zIndex = '2999';
            flyerEl.classList.add('show-flyer');
        }

        requestAnimationFrame(() => requestAnimationFrame(() => {
            if (introEl) {
                introEl.classList.add('fade-out');
                setTimeout(() => {
                    introEl.style.display = 'none';
                    if (flyerEl) flyerEl.style.zIndex = '3000';
                    startFlyerFlow();
                }, 1000);
            } else {
                startFlyerFlow();
            }
        }));
    }

    function startFlyerFlow() {
        const flyerEl = document.getElementById('flyerScreen');
        if (musicBtn) musicBtn.classList.add('show');

        crearLluvia();

        setTimeout(() => {
            if (flyerEl) {
                flyerEl.style.transition = 'opacity 1.4s ease';
                flyerEl.style.opacity = '0';
                setTimeout(() => {
                    flyerEl.style.display = 'none';
                    document.body.classList.remove('flyer-active');
                    const rainContainer = document.getElementById('rainContainer');
                    if (rainContainer) {
                        rainContainer.style.transition = 'opacity 1.8s ease';
                        rainContainer.style.opacity = '0';
                        setTimeout(() => {
                            if (rainContainer) rainContainer.style.display = 'none';
                        }, 1800);
                    }
                    const fw = document.getElementById('formWrapper');
                    if (fw) fw.classList.add('visible');
                    
                    if (!swalShown) {
                        swalShown = true;
                        Swal.fire({
                            icon: 'success',
                            title: '🌸 ¡Festejemos a Mamá! 🌸',
                            html: '<strong>Bienvenida, Ingeniera Mamá</strong><br><br>Completa tus datos para que podamos validar y confirmar tu asistencia a la Cena Show Bailable.',
                            confirmButtonColor: '#7c1228',
                            timer: 4000,
                            showConfirmButton: true,
                            background: '#fff8f4',
                            iconColor: '#b01e3a'
                        });
                    }
                }, 1400);
            }
        }, 7500);
    }

    // ==================== LLUVIA DE PÉTALOS ====================
    const rainEl = document.getElementById('rainContainer');
    function crearLluvia() {
        if (!rainEl) return;
        const emojis = ['🌹','🌹','🌹','🌹','🌸','🌸','🌸','💖','🌺','🌺','💐','💐','🥀','💗','💗','❣️','🌹','🌸','🌹','🌹'];
        for (let i = 0; i < 100; i++) {
            const el = document.createElement('div');
            el.className = 'petal';
            el.textContent = emojis[Math.floor(Math.random() * emojis.length)];
            el.style.left = Math.random() * 102 + '%';
            el.style.fontSize = (Math.random() * 32 + 14) + 'px';
            el.style.animationDuration = (Math.random() * 7 + 6) + 's';
            el.style.animationDelay = (Math.random() * 5.5) + 's';
            el.style.transform = `rotate(${Math.random() * 360}deg)`;
            rainEl.appendChild(el);
        }
    }

    // ==================== FORMULARIO ====================
    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }
    let datosVerificados = null;
    let verificando = false;

    const cipEl = document.getElementById('cipInput');
    const dniEl = document.getElementById('dniInput');
    const nombreEl = document.getElementById('nombreInput');
    const telEl = document.getElementById('telefonoInput');
    const mailEl = document.getElementById('correoInput');
    const msgEl = document.getElementById('autoMsg');
    const form = document.getElementById('registroEventoForm');

    const errorModal = document.getElementById('errorModal');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const closeErrorModal = document.getElementById('closeErrorModal');

    function openModal(icon, title, message) {
        if (modalIcon) modalIcon.innerHTML = icon;
        if (modalTitle) modalTitle.textContent = title;
        if (modalMessage) modalMessage.innerHTML = message;
        if (errorModal) errorModal.classList.add('open');
    }
    function closeModal() {
        if (errorModal) errorModal.classList.remove('open');
    }

    if (closeErrorModal) closeErrorModal.addEventListener('click', closeModal);
    if (errorModal) {
        errorModal.addEventListener('click', (e) => { if (e.target === errorModal) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
    }

    function normalizarCIP(cip) {
        if (!cip) return '';
        let cipStr = cip.toString().trim();
        if (cipStr === '') return '';
        let cipNorm = parseInt(cipStr, 10);
        if (isNaN(cipNorm)) return '';
        return cipNorm.toString();
    }
    function validarCIP(cip) { return cip && /^\d{1,6}$/.test(cip); }
    function validarDNI(dni) { return dni && /^\d{8}$/.test(dni); }

    function setCamposHabilitados(h) {
        const t = document.getElementById('telefonoInput');
        const m = document.getElementById('correoInput');
        const s = document.getElementById('submitBtn');
        t.disabled = !h;
        m.disabled = !h;
        s.disabled = !h;
        if (h) {
            t.classList.remove('readonly-field');
            m.classList.remove('readonly-field');
        } else {
            t.classList.add('readonly-field');
            m.classList.add('readonly-field');
        }
    }

    async function verificarConAPI(cipOriginal, dni) {
        verificando = true;
        msgEl.className = 'auto-msg status-pending';
        msgEl.innerHTML = "<i class='bi bi-hourglass-split'></i> Verificando en el padrón...";
        const cipNorm = normalizarCIP(cipOriginal);
        try {
            const resp = await fetch('/validar-cip-dni', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                body: JSON.stringify({ cip: cipNorm, dni })
            });
            const data = await resp.json();
            if (data.success) {
                const nombre = `${data.nombres} ${data.apellidos}`;
                nombreEl.value = nombre;
                datosVerificados = { nombres: data.nombres, apellidos: data.apellidos, capitulo: data.capitulo || '', cip_normalizado: cipNorm };
                msgEl.className = 'auto-msg status-ok';
                msgEl.innerHTML = `<i class='bi bi-check-circle-fill'></i> ✅ Identidad verificada: <strong>${nombre}</strong>`;
                setCamposHabilitados(true);
                return true;
            } else {
                nombreEl.value = '';
                datosVerificados = null;
                setCamposHabilitados(false);
                let icon = '🔒', titulo = '', mensaje = '';
                if (data.message === 'El CIP no existe en el padrón') {
                    titulo = 'CIP no registrado';
                    mensaje = `El CIP <strong>${cipOriginal}</strong> no se encuentra registrado como hábil.<br><br>✔ Verifica que el número de CIP esté correctamente escrito.<br><br>📞 Si estás segura de que tu CIP es correcto, comunícate al número de soporte.`;
                } else if (data.message === 'El DNI no coincide con el CIP') {
                    titulo = 'Datos no coinciden';
                    mensaje = `El DNI <strong>${dni}</strong> no coincide con el CIP ingresado.<br><br>✔ Si cometiste un error al escribir tus datos, corrígelos e inténtalo nuevamente.<br><br>📞 Si estás segura de que los datos son correctos, comunícate al número de soporte.`;
                } else {
                    titulo = 'No se pudo verificar';
                    mensaje = 'No se pudo verificar la información ingresada.<br><br>✔ Revisa tus datos e inténtalo nuevamente.<br><br>📞 Si el problema persiste, comunícate al número de soporte.';
                }
                openModal(icon, titulo, mensaje);
                return false;
            }
        } catch (err) {
            console.error(err);
            nombreEl.value = '';
            datosVerificados = null;
            setCamposHabilitados(false);
            openModal('⚠️', 'Error de conexión', 'No se pudo conectar con el servidor. Verifica tu conexión e intenta nuevamente.');
            return false;
        } finally {
            verificando = false;
        }
    }

    let timeoutBusqueda = null;

    function handleCIPChange() {
        const cip = cipEl.value.trim();
        dniEl.value = '';
        nombreEl.value = '';
        datosVerificados = null;
        setCamposHabilitados(false);
        if (cip && !validarCIP(cip)) {
            msgEl.className = 'auto-msg status-error';
            msgEl.innerHTML = "<i class='bi bi-x-circle-fill'></i> El CIP solo puede tener hasta 6 dígitos numéricos.";
            return;
        }
        if (cip) {
            msgEl.className = 'auto-msg status-warning';
            msgEl.innerHTML = "<i class='bi bi-keyboard'></i> ⚠️ CIP válido. Ahora ingresa tu DNI (8 dígitos exactos).";
        } else {
            msgEl.className = 'auto-msg status-pending';
            msgEl.innerHTML = "<i class='bi bi-info-circle'></i> Ingresa tu CIP (máx 6 dígitos) y luego tu DNI (8 dígitos).";
        }
    }

    function handleDNIChange() {
        if (timeoutBusqueda) clearTimeout(timeoutBusqueda);
        const dni = dniEl.value.trim();
        const cip = cipEl.value.trim();
        if (!cip) {
            msgEl.className = 'auto-msg status-pending';
            msgEl.innerHTML = "<i class='bi bi-info-circle'></i> Primero ingresa tu CIP.";
            return;
        }
        if (!validarCIP(cip)) return;
        if (!dni) {
            msgEl.className = 'auto-msg status-warning';
            msgEl.innerHTML = "<i class='bi bi-keyboard'></i> ⚠️ Ingresa tu DNI (8 dígitos exactos).";
            return;
        }
        if (dni.length === 8 && validarDNI(dni)) {
            msgEl.className = 'auto-msg status-pending';
            msgEl.innerHTML = "<i class='bi bi-hourglass-split'></i> Verificando...";
            timeoutBusqueda = setTimeout(() => { if (!verificando) verificarConAPI(cip, dni); }, 300);
        } else if (dni.length < 8) {
            msgEl.className = 'auto-msg status-warning';
            msgEl.innerHTML = `<i class='bi bi-exclamation-triangle-fill'></i> El DNI debe tener exactamente 8 dígitos (llevas ${dni.length})`;
            nombreEl.value = '';
            datosVerificados = null;
            setCamposHabilitados(false);
        } else {
            msgEl.className = 'auto-msg status-error';
            msgEl.innerHTML = "<i class='bi bi-x-circle-fill'></i> El DNI no puede tener más de 8 dígitos.";
            nombreEl.value = '';
            datosVerificados = null;
            setCamposHabilitados(false);
        }
    }

    cipEl.addEventListener('input', handleCIPChange);
    dniEl.addEventListener('input', handleDNIChange);

    function validarFormulario() {
        const tel = telEl.value.trim();
        const mail = mailEl.value.trim();
        if (!datosVerificados) {
            Swal.fire({ icon: 'error', title: 'Identidad no verificada', text: 'Por favor, verifica que tu CIP y DNI sean correctos.', confirmButtonColor: '#7c1228' });
            return false;
        }
        if (!tel || tel.length < 7) {
            Swal.fire({ icon: 'warning', title: 'Teléfono requerido', text: 'Ingresa un número de teléfono válido.', confirmButtonColor: '#7c1228' });
            telEl.focus();
            return false;
        }
        if (!/^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/.test(mail)) {
            Swal.fire({ icon: 'warning', title: 'Correo inválido', text: 'Ejemplo: nombre@dominio.com', confirmButtonColor: '#7c1228' });
            mailEl.focus();
            return false;
        }
        return true;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validarFormulario()) return;
        const submitBtn = document.getElementById('submitBtn');
        const origTxt = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Verificando disponibilidad...';
        try {
            const aforoResp = await fetch('/verificar-aforo', { method: 'GET', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' } });
            const aforoData = await aforoResp.json();
            if (aforoData.total_aprobados >= 250) {
                Swal.fire({
                    icon: 'error', title: '🌸 Evento Completado 🌸',
                    html: `<div style="font-size:.97rem;line-height:1.75"><strong>Lo sentimos, el aforo máximo de 250 personas ya ha sido alcanzado.</strong><br><br>📊 <strong>Estado actual:</strong><br>• Cupo total: 250 personas<br>• Registradas aprobadas: ${aforoData.total_aprobados}<br>• Cupos disponibles: 0<br><br>🌹 Te invitamos a estar atenta a futuros eventos del Comité de Damas.<br>¡Gracias por tu interés!</div>`,
                    confirmButtonText: 'Entendido 🌹', confirmButtonColor: '#7c1228', background: '#fff8f4'
                }).then(() => window.location.reload());
                submitBtn.disabled = false;
                submitBtn.innerHTML = origTxt;
                return;
            }
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Registrando...';
            const cipNorm = normalizarCIP(cipEl.value.trim());
            const resp = await fetch('/registrar-asistente', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
                body: JSON.stringify({ cip: cipNorm, dni: dniEl.value.trim(), nombres: datosVerificados.nombres, apellidos: datosVerificados.apellidos, capitulo: datosVerificados.capitulo, celular: telEl.value.trim(), correo: mailEl.value.trim() })
            });
            const data = await resp.json();
            if (data.success) {
                Swal.fire({
                    title: '🎉 ¡Inscripción confirmada!',
                    html: `<div style="font-size:.97rem;line-height:1.75"><strong>${data.message || 'Tu inscripción como Ingeniera Mamá fue registrada.'}</strong><br><br>🌹 Recibirás confirmación por correo en las próximas 48 h.<br>Te esperamos el <strong>viernes 08 de mayo</strong> en el Auditorio CECAP.<br><br>⏰ Llega antes de las 5:00 PM y llévate un regalo especial.</div>`,
                    icon: 'success', confirmButtonText: '¡Gracias! 🌹', confirmButtonColor: '#7c1228', background: '#fff8f4'
                }).then(() => {
                    cipEl.value = '';
                    dniEl.value = '';
                    nombreEl.value = '';
                    telEl.value = '';
                    mailEl.value = '';
                    datosVerificados = null;
                    setCamposHabilitados(false);
                    msgEl.className = 'auto-msg status-pending';
                    msgEl.innerHTML = "<i class='bi bi-info-circle'></i> Ingresa tu CIP (máx 6 dígitos) y luego tu DNI (8 dígitos).";
                });
            } else {
                if (data.message && data.message.includes('aforo')) {
                    Swal.fire({
                        icon: 'error', title: '🌸 Evento Completado 🌸',
                        html: `<div style="font-size:.97rem;line-height:1.75"><strong>${data.message}</strong><br><br>El cupo máximo de 250 personas ya fue alcanzado durante tu registro.<br><br>🌹 Gracias por tu interés en participar.</div>`,
                        confirmButtonText: 'Entendido 🌹', confirmButtonColor: '#7c1228', background: '#fff8f4'
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'No se pudo registrar', text: data.message || 'Ocurrió un error. Intenta nuevamente.', confirmButtonColor: '#7c1228' });
                }
            }
        } catch (err) {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo completar el registro. Verifica tu conexión e intenta nuevamente.', confirmButtonColor: '#7c1228' });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = origTxt;
        }
    });

    function soloNumeros(input, max) {
        input.addEventListener('keydown', (e) => {
            if (['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) return;
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
        input.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, max);
        });
    }
    soloNumeros(document.getElementById('cipInput'), 6);
    soloNumeros(document.getElementById('dniInput'), 8);
    soloNumeros(document.getElementById('telefonoInput'), 9);
})();
</script>
</body>
</html>