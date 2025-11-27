@extends('inicio')

@section('title', 'Certificaciones - CIP La Libertad')

@section('styles')
<style>
    /* === CONTENEDOR PRINCIPAL === */
    .cert-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 50px 20px;
        font-family: 'Arial', sans-serif;
        background-color: #fff;
        min-height: 70vh;
    }

    .page-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 40px;
    }
    .page-header h1 {
        color: #333;
        font-size: 32px;
        font-weight: 700;
        margin: 0;
    }

    .main-headline {
        color: #AD2B2E;
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        line-height: 1.4;
        max-width: 900px;
        margin: 0 auto 60px;
    }

    /* === GRID DE CERTIFICADOS === */
    .cert-grid {
        display: flex;
        justify-content: center;
        gap: 50px;
        flex-wrap: wrap;
    }

    .cert-card {
        width: 280px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    /* Contenedor visual del PDF */
    .pdf-thumbnail-wrapper {
        width: 100%;
        /* Altura fija para mantener uniformidad, el canvas se ajustará */
        height: 380px; 
        background-color: #f4f4f4;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    /* El elemento Canvas donde se dibuja el PDF */
    .pdf-canvas {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }

    /* Efecto Hover */
    .pdf-thumbnail-wrapper:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(173, 43, 46, 0.15);
        border-color: #AD2B2E;
    }

    /* Icono de carga */
    .loading-icon {
        position: absolute;
        color: #999;
        font-size: 24px;
    }

    /* Títulos */
    .cert-title { font-size: 18px; font-weight: 800; color: #333; margin-bottom: 5px; }
    .cert-desc { font-size: 14px; color: #666; line-height: 1.4; }

    @media (max-width: 600px) {
        .cert-grid { flex-direction: column; gap: 40px; }
        .main-headline { font-size: 20px; text-align: left; }
    }
</style>
@endsection

@section('content')

<div class="cert-container">
    <div class="page-header">
        <h1>Certificaciones</h1>
    </div>

    <div class="main-headline">
        El CARD cuenta con el respaldo y certificación de las siguientes normas en Arbitraje Institucional
    </div>

    <div class="cert-grid">
        
        <div class="cert-card">
            <a href="{{ asset('docs/certificaciones/SGCA_CARD.pdf') }}" target="_blank" class="pdf-link-wrapper">
                <div class="pdf-thumbnail-wrapper">
                    <i class="fas fa-spinner fa-spin loading-icon"></i>
                    <canvas class="pdf-canvas" data-url="{{ asset('docs/certificaciones/SGCA_CARD.pdf') }}"></canvas>
                </div>
            </a>
            <div class="cert-title">SGCA - CARD</div>
            <div class="cert-desc">Sistema de Gestión de Calidad</div>
        </div>

        <div class="cert-card">
            <a href="{{ asset('docs/certificaciones/SGAS_CARD.pdf') }}" target="_blank" class="pdf-link-wrapper">
                <div class="pdf-thumbnail-wrapper">
                    <i class="fas fa-spinner fa-spin loading-icon"></i>
                    <canvas class="pdf-canvas" data-url="{{ asset('docs/certificaciones/SGAS_CARD.pdf') }}"></canvas>
                </div>
            </a>
            <div class="cert-title">SGAS - CARD</div>
            <div class="cert-desc">Sistema de Gestión Anti-Soborno</div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<script>
    // Configurar el Worker de PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

    document.addEventListener('DOMContentLoaded', function() {
        const canvases = document.querySelectorAll('.pdf-canvas');

        canvases.forEach(canvas => {
            const url = canvas.getAttribute('data-url');
            const wrapper = canvas.parentElement;
            const loadingIcon = wrapper.querySelector('.loading-icon');

            // Cargar el PDF
            pdfjsLib.getDocument(url).promise.then(pdf => {
                // Obtener la página 1
                pdf.getPage(1).then(page => {
                    const viewport = page.getViewport({ scale: 1.5 }); // Escala 1.5 para buena calidad
                    const context = canvas.getContext('2d');

                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };

                    // Dibujar en el canvas
                    page.render(renderContext).promise.then(() => {
                        // Ocultar icono de carga cuando termine
                        loadingIcon.style.display = 'none';
                    });
                });
            }).catch(error => {
                console.error('Error cargando PDF:', error);
                loadingIcon.className = 'fas fa-file-pdf'; // Mostrar icono estático si falla
                loadingIcon.style.fontSize = '50px';
                loadingIcon.style.color = '#AD2B2E';
            });
        });
    });
</script>
@endsection