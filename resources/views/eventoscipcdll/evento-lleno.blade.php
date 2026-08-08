<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Validando Inscripciones | Ingenieras CIPCDLL</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --wine:      #7c1228;
            --crimson:   #b01e3a;
            --rose-soft: #d4728a;
            --blush:     #f0c0cc;
            --gold:      #c49560;
            --cream:     #fdf6f0;
            --ink:       #2c1018;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, var(--cream) 0%, var(--blush) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background: radial-gradient(ellipse 70% 55% at 0% 5%, rgba(212,114,138,.15) 0%, transparent 65%),
                        radial-gradient(ellipse 55% 45% at 100% 95%, rgba(196,149,96,.12) 0%, transparent 60%);
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 600px;
            width: 100%;
            animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: rgba(255, 252, 250, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 2.5rem;
            padding: 3rem 2.5rem;
            text-align: center;
            box-shadow: 0 30px 60px -20px rgba(124, 18, 40, 0.3);
            border: 1px solid rgba(196, 149, 96, 0.25);
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--wine), var(--gold), var(--rose-soft), var(--wine));
            background-size: 200% 100%;
            animation: shiftGold 3s linear infinite;
        }

        @keyframes shiftGold {
            0% { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }

        .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .main-title {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: var(--wine);
            margin-bottom: 1rem;
            font-weight: 800;
        }

        .subtitle {
            font-size: 1.3rem;
            color: var(--crimson);
            margin-bottom: 1.5rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .message {
            background: rgba(124, 18, 40, 0.08);
            border-left: 4px solid var(--crimson);
            padding: 1.2rem;
            border-radius: 1rem;
            margin: 1.5rem 0;
            text-align: left;
        }

        .message p {
            color: var(--ink);
            line-height: 1.6;
            margin-bottom: 0.75rem;
        }

        .message p:last-child {
            margin-bottom: 0;
        }

        .highlight {
            font-weight: 800;
            color: var(--wine);
        }

        .info-box {
            background: linear-gradient(135deg, rgba(250, 234, 237, 0.8), rgba(242, 227, 204, 0.8));
            border-radius: 1.2rem;
            padding: 1.2rem;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: var(--wine);
            font-weight: 600;
        }

        .info-item i {
            font-size: 1.2rem;
            color: var(--gold);
        }

        .social-card {
            background: white;
            border-radius: 1.2rem;
            padding: 1rem 1.2rem;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid rgba(196,149,96,0.3);
        }

        .social-text {
            font-weight: 600;
            color: var(--wine);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-social {
            background: linear-gradient(108deg, #3b5998, #4c70ba);
            color: white;
            padding: 0.6rem 1.4rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: 0.25s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .btn-social:hover {
            transform: translateY(-2px);
            background: #2d4373;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: linear-gradient(108deg, var(--wine) 0%, var(--crimson) 100%);
            color: white;
            padding: 0.9rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            margin-top: 0.5rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            box-shadow: 0 8px 20px -8px rgba(124, 18, 40, 0.4);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -10px rgba(124, 18, 40, 0.5);
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.75rem;
            color: #a07080;
            text-align: center;
        }

        @media (max-width: 600px) {
            .card {
                padding: 2rem 1.5rem;
            }
            .main-title {
                font-size: 1.5rem;
            }
            .subtitle {
                font-size: 1rem;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
            .social-card {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">✨🌸</div>
            
            <!-- Título principal eliminado, solo queda el icono -->
            
            <div class="subtitle">ESTAMOS VALIDANDO INSCRIPCIONES</div>
            
            <div class="info-box">
                <div class="info-item">
                    <i class="bi bi-calendar-heart-fill"></i>
                    <span>08 de Mayo</span>
                </div>
                <div class="info-item">
                    <i class="bi bi-building"></i>
                    <span>Auditorio CECAP</span>
                </div>
            </div>
            
            <div class="message">
                <p><i class="bi bi-check-circle-fill" style="color: var(--crimson); margin-right: 0.5rem;"></i> 
                <strong>✅ En estos momentos, nuestro equipo está validando la información y depurando registros</strong> para confirmar las asistencias reales.</p>
                <p>🔄 <strong>¿Pueden liberarse nuevos cupos?</strong> Sí. Si alguna inscripción no cumple con los requisitosd, <strong>abriremos espacios adicionales</strong> y los anunciaremos de inmediato.</p>
                <p>📱 <strong>Te invitamos a seguirnos en redes sociales</strong> para enterarte primero si se liberan nuevas entradas o para futuros eventos.</p>
            </div>
            
            <!-- Invitación a redes sociales -->
            <div class="social-card">
                <div class="social-text">
                    <i class="bi bi-facebook"></i> CIP La Libertad — Canal oficial
                </div>
                <a href="https://www.facebook.com/CIPLaLibertad?locale=es_LA" target="_blank" rel="noopener noreferrer" class="btn-social">
                    <i class="bi bi-box-arrow-up-right"></i> Ver Facebook
                </a>
            </div>
            
            <div>
                <a href="tel:+5144340010" class="btn">
                    <i class="bi bi-telephone-fill"></i> Contactar Sede CIP
                </a>
            </div>
            
            <div class="footer">
                <i class="bi bi-flower1"></i> Consejo Departamental La Libertad — Comité de Damas Ingenieras <i class="bi bi-flower1"></i><br>
                <span style="font-size:0.7rem;">🔔 Estate atenta a nuestras redes — Podrían liberarse nuevos cupos</span>
            </div>
        </div>
    </div>
</body>
</html>