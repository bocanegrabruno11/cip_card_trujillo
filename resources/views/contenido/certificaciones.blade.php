@extends('inicio')

@section('title', 'Certificaciones - CARD CD La Libertad')

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

    .page-header { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 40px; }
    .page-header h1 { color: #333; font-size: 32px; font-weight: 700; margin: 0; }

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

    .pdf-canvas {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        display: block;
    }

    .pdf-thumbnail-wrapper:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(173, 43, 46, 0.15);
        border-color: #AD2B2E;
    }

    .loading-icon { position: absolute; color: #999; font-size: 24px; }

    /* Títulos */
    .cert-title { font-size: 18px; font-weight: 800; color: #333; margin-bottom: 5px; }
    .cert-desc { font-size: 14px; color: #666; line-height: 1.4; }
    
    .empty-msg { color: #888; font-style: italic; margin-top: 30px; text-align: center; width: 100%; }

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
        El CARD cuenta con el respaldo y certificación de las siguientes normas:
    </div>

    <div class="cert-grid">
        
        @forelse($certificados as $cert)
            <div class="cert-card">
                {{-- Enlace al PDF (abre en nueva pestaña) --}}
                <a href="{{ asset('storage/' . $cert->ruta_archivo) }}" target="_blank" class="pdf-link-wrapper" style="text-decoration: none;">
                    
                    <div class="pdf-thumbnail-wrapper">
                        <i class="fas fa-spinner fa-spin loading-icon"></i>
                        
                        {{-- Canvas dinámico con la ruta del archivo --}}
                        <canvas class="pdf-canvas" data-url="{{ asset('storage/' . $cert->ruta_archivo) }}"></canvas>
                    </div>
                </a>
                
                <div class="cert-title">{{ $cert->titulo }}</div>
                
                @if($cert->descripcion)
                    <div class="cert-desc">{{ $cert->descripcion }}</div>
                @endif
            </div>
        @empty
            <p class="empty-msg">No hay certificaciones vigentes publicadas por el momento.</p>
        @endforelse

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

        if (canvases.length > 0) {
            canvases.forEach(canvas => {
                const url = canvas.getAttribute('data-url');
                const wrapper = canvas.parentElement;
                const loadingIcon = wrapper.querySelector('.loading-icon');

                // Cargar el PDF
                pdfjsLib.getDocument(url).promise.then(pdf => {
                    // Obtener la página 1 para la miniatura
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
                    // Si falla la carga del PDF (ej: archivo corrupto), mostrar icono de PDF estático
                    loadingIcon.className = 'fas fa-file-pdf'; 
                    loadingIcon.style.fontSize = '50px';
                    loadingIcon.style.color = '#AD2B2E';
                    loadingIcon.classList.remove('fa-spin'); // Quitar animación
                });
            });
        }
    });
</script>
@endsection