@extends('inicio')

@section('title', 'Políticas - CARD CD La Libertad')

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
        margin: 0 auto 50px;
    }

    /* === GRID DE DOCUMENTOS (4 Columnas) === */
    .cert-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr); 
        gap: 30px; 
        width: 100%;
    }

    .cert-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 100%;
    }

    /* CONTENEDOR DE VISTA PREVIA (Compacto y Uniforme) */
    .doc-preview-wrapper {
        width: 100%;
        height: 320px; /* Altura fija */
        background-color: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        cursor: pointer;
        text-decoration: none; 
    }

    .doc-preview-wrapper:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(173, 43, 46, 0.15);
        border-color: #AD2B2E;
    }

    /* IMAGEN DE PORTADA */
    .doc-image {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Ajusta sin cortar */
        display: block;
    }

    /* CONTENIDO CUANDO ES DOCUMENTO (PDF/DOC) */
    .doc-icon-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        width: 100%;
        padding: 20px;
        background-color: #fcfcfc;
    }

    .doc-icon { font-size: 60px; color: #AD2B2E; margin-bottom: 15px; transition: transform 0.3s; }
    .doc-preview-wrapper:hover .doc-icon { transform: scale(1.1); }

    .doc-type-label {
        font-size: 12px;
        font-weight: 800;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }

    .doc-filename {
        font-size: 13px;
        color: #555;
        word-break: break-word;
        max-width: 100%;
        line-height: 1.3;
    }

    /* Títulos */
    .cert-title { font-size: 16px; font-weight: 700; color: #333; margin-bottom: 5px; line-height: 1.3; }
    .cert-desc { font-size: 13px; color: #777; line-height: 1.4; }
    
    .empty-msg { color: #888; font-style: italic; margin-top: 30px; text-align: center; width: 100%; grid-column: 1 / -1; }

    /* RESPONSIVE */
    @media (max-width: 1024px) { .cert-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .cert-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { 
        .cert-grid { grid-template-columns: 1fr; max-width: 320px; margin: 0 auto; }
        .doc-preview-wrapper { height: 350px; }
        .main-headline { font-size: 20px; }
    }
</style>
@endsection

@section('content')

<div class="cert-container">
    <div class="page-header">
        <h1>Políticas Institucionales</h1>
    </div>

    <div class="main-headline">
        Conoce las políticas y lineamientos que rigen el funcionamiento ético y administrativo del CARD.
    </div>

    <div class="cert-grid">
        
        @forelse($politicas as $pol)
            @php
                $extension = strtolower(pathinfo($pol->ruta_archivo, PATHINFO_EXTENSION));
                $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            @endphp

            <div class="cert-card">
                {{-- Enlace al archivo (abre en nueva pestaña) --}}
                <a href="{{ asset('storage/' . $pol->ruta_archivo) }}" target="_blank" class="doc-preview-wrapper">
                    
                    @if($esImagen)
                        {{-- CASO 1: Es una Imagen --}}
                        <img src="{{ asset('storage/' . $pol->ruta_archivo) }}" class="doc-image" alt="Política">
                    @else
                        {{-- CASO 2: Es Documento --}}
                        <div class="doc-icon-container">
                            @if($extension == 'pdf')
                                <i class="fas fa-file-pdf doc-icon"></i>
                                <div class="doc-type-label">Documento PDF</div>
                            @elseif(in_array($extension, ['doc', 'docx']))
                                <i class="fas fa-file-word doc-icon" style="color: #2b5797;"></i>
                                <div class="doc-type-label">Documento Word</div>
                            @else
                                <i class="fas fa-file-alt doc-icon" style="color: #555;"></i>
                                <div class="doc-type-label">Archivo {{ strtoupper($extension) }}</div>
                            @endif
                            
                            <div class="doc-filename">Ver documento completo</div>
                        </div>
                    @endif

                </a>
                
                <div class="cert-title">{{ $pol->titulo }}</div>
                
                @if($pol->descripcion)
                    <div class="cert-desc">{{ $pol->descripcion }}</div>
                @endif
            </div>
        @empty
            <p class="empty-msg">No hay políticas publicadas actualmente.</p>
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