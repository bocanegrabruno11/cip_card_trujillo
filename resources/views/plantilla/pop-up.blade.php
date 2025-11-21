<style>
    /* Fondo semitransparente del popup */
    .popup-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2000;
    }
    
    /* Contenedor principal */
    .popup-wrapper {
      position: relative;
      width: auto;
      max-width: 95%;
      min-width: 300px;
      padding: 50px 80px; /* Espacio para flechas e indicadores */
    }
    
    .popup-container {
      position: relative;
      width: 100%;
      max-width: 1200px;
      background-color: #fff;
      padding: 0;
      border-radius: 10px;
      overflow: visible; /* Cambiado para que las flechas no se corten */
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
      margin: 0 auto;
    }

    /* Wrapper interno para la imagen con overflow hidden */
    .image-wrapper {
      position: relative;
      width: 100%;
      overflow: visible; /* Cambiado a visible para que se vea la X completa */
      border-radius: 10px;
    }
    
    /* Botón de cerrar - DENTRO del image-wrapper */
    .close-btn {
      position: absolute;
      top: -25px;
      right: -25px;
      font-size: 28px;
      color: #fff;
      background-color: rgba(255, 0, 0, 0.7);
      border: none;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      cursor: pointer;
      z-index: 10002;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.3);
      transition: all 0.3s ease;
      font-weight: bold;
      line-height: 1;
      pointer-events: auto;
    }
    
    .close-btn:hover {
      background-color: #ff0000;
      transform: scale(1.1);
    }
    
    /* Imagen del carrusel */
    .popup-image {
      width: 100%;
      height: auto;
      max-height: 80vh;
      min-height: 400px;
      object-fit: contain; /* Cambiado a contain para mantener proporciones */
      object-position: center;
      display: none;
      transition: all 0.3s ease;
      cursor: zoom-in;
      background-color: #fff;
    }
    
    /* La imagen activa */
    .popup-image.active {
      display: block;
    }

   .popup-action-btn {
        position: absolute; /* Flotante */
        bottom: 30px;       /* Distancia del borde inferior */
        left: 50%;
        transform: translateX(-50%); /* Centrado exacto */
        
        background-color: #D4AF37; /* Dorado */
        color: #000;
        font-weight: bold;
        text-transform: uppercase;
        padding: 12px 35px;
        border-radius: 30px;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        transition: all 0.2s;
        z-index: 2002;
        display: none; /* Oculto por defecto */
        border: 2px solid #fff;
        align-items: center; gap: 8px;
        white-space: nowrap; /* Evita que el texto se parta */
    }
    .popup-action-btn:hover {
        background-color: #f0c448;
        transform: translateX(-50%) scale(1.05); /* Mantiene el centrado al escalar */
        color: #000;
        box-shadow: 0 6px 20px rgba(0,0,0,0.5);
    }
    .popup-action-btn i { margin-left: 5px; }
    
    /* Imagen ampliada */
    .popup-image.zoomed {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      max-height: none;
      min-height: none;
      object-fit: contain;
      z-index: 10003;
      cursor: zoom-out;
      background-color: rgba(0, 0, 0, 0.95);
      padding: 20px;
      box-sizing: border-box;
    }
    
    /* Contenedor de scroll para imagen grande */
    .zoom-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.95);
      z-index: 10003;
      overflow: auto;
      display: none;
      align-items: flex-start;
      justify-content: center;
      padding: 20px;
      box-sizing: border-box;
    }
    
    .zoom-container.active {
      display: flex;
    }
    
    .zoom-container img {
      max-width: 100%;
      height: auto;
      cursor: zoom-out;
      display: block;
      margin: auto;
    }
    
    /* Botones de navegación mejorados */
    .nav-arrow {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 60px;
      height: 60px;
      background-color: rgba(255,255,255,0.8);
      border: 2px solid rgba(0,0,0,0.1);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 10001;
      font-size: 30px;
      color: #333;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
      pointer-events: auto;
      user-select: none;
      padding: 0;
      outline: none;
    }
    
    .nav-arrow:hover {
      background-color: rgba(255,255,255,1);
      transform: translateY(-50%) scale(1.15);
      box-shadow: 0 6px 15px rgba(0,0,0,0.4);
    }
    
    .nav-arrow:active {
      transform: translateY(-50%) scale(1.05);
    }
    
    .nav-arrow:focus {
      outline: 3px solid rgba(59, 130, 246, 0.5);
      outline-offset: 2px;
    }
    
    .nav-left {
      left: -30px; /* Más afuera del contenedor */
    }
    
    .nav-right {
      right: -30px; /* Más afuera del contenedor */
    }
    
    /* Indicadores de posición */
    .indicators {
      position: absolute;
      bottom: -40px; /* Fuera del contenedor de imagen */
      left: 0;
      right: 0;
      display: flex;
      justify-content: center;
      gap: 15px;
      z-index: 10001;
      pointer-events: none;
    }
    
    .indicator {
      width: 15px;
      height: 15px;
      border-radius: 50%;
      background-color: rgba(255,255,255,0.5);
      cursor: pointer;
      transition: all 0.3s ease;
      pointer-events: auto;
      border: none;
      padding: 0;
    }
    
    .indicator.active {
      background-color: #fff;
      transform: scale(1.2);
    }
    
    .indicator:hover {
      background-color: rgba(255,255,255,0.8);
    }
    
    /* Media queries para ajustes específicos */
    @media (max-width: 768px) {
      .popup-wrapper {
        max-width: 90%;
        padding: 30px 60px;
      }
      
      .close-btn {
        top: -20px;
        right: -20px;
        width: 40px;
        height: 40px;
        font-size: 24px;
      }
      
      .nav-arrow {
        width: 50px;
        height: 50px;
        font-size: 24px;
      }
      
      .nav-left {
        left: -25px;
      }
      
      .nav-right {
        right: -25px;
      }
      
      .indicators {
        bottom: -35px;
        gap: 10px;
      }
      
      .indicator {
        width: 12px;
        height: 12px;
      }
      
      .popup-image,
      .popup-image.active {
        max-height: 70vh;
        min-height: 300px;
      }
    }
    
    @media (min-width: 1600px) {
      .popup-container {
        max-width: 1400px;
      }
      
      .popup-wrapper {
        padding: 60px 100px;
      }
      
      .popup-image,
      .popup-image.active {
        max-height: 85vh;
        min-height: 500px;
      }
      
      .close-btn {
        top: -30px;
        right: -30px;
        width: 60px;
        height: 60px;
        font-size: 32px;
      }
      
      .nav-arrow {
        width: 70px;
        height: 70px;
        font-size: 36px;
      }
      
      .nav-left {
        left: -35px;
      }
      
      .nav-right {
        right: -35px;
      }
      
      .indicators {
        bottom: -45px;
        gap: 20px;
      }
      
      .indicator {
        width: 18px;
        height: 18px;
      }
    }
  </style>

<div class="popup-overlay" id="popup">
    <div class="popup-wrapper">
        
        <div class="popup-container">
            <div class="image-wrapper">
                <button class="close-btn" id="closeBtn" aria-label="Cerrar">×</button>
                
                @if(isset($popupData) && $popupData->detalles->count() > 0)
                    @foreach($popupData->detalles as $index => $img)
                        <img src="{{ asset('storage/' . $img->ruta_imagen) }}" 
                             alt="Imagen {{ $index }}" 
                             class="popup-image {{ $index === 0 ? 'active' : '' }}"
                             data-link="{{ $img->url_enlace ?? '' }}">
                    @endforeach

                @endif
            </div>
            <a href="#" target="_blank" class="popup-action-btn" id="popupActionBtn">
                Ver Más Información <i class="fas fa-external-link-alt"></i>
            </a>
            
            <button class="nav-arrow nav-left" id="prevBtn" aria-label="Anterior">❮</button>
            <button class="nav-arrow nav-right" id="nextBtn" aria-label="Siguiente">❯</button>
            
            <div class="indicators" id="indicators">
                @if(isset($popupData) && $popupData->detalles->count() > 0)
                    @foreach($popupData->detalles as $index => $img)
                        <button class="indicator {{ $index === 0 ? 'active' : '' }}" 
                                data-index="{{ $index }}" 
                                aria-label="Ir a imagen {{ $index + 1 }}"></button>
                    @endforeach
                @else
                    <button class="indicator active" data-index="0" aria-label="Ir a imagen 1"></button>
                    <button class="indicator" data-index="1" aria-label="Ir a imagen 2"></button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Contenedor de zoom pantalla completa -->
<div class="zoom-container" id="zoomContainer">
    <img src="" alt="Imagen ampliada" id="zoomedImage">
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    let currentIndex = 0;
    const images = document.querySelectorAll('.popup-image');
    const indicators = document.querySelectorAll('.indicator');
    const actionBtn = document.getElementById('popupActionBtn');
    
    // Referencias DOM
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const closeBtn = document.getElementById('closeBtn');
    const popup = document.getElementById('popup');
    const zoomContainer = document.getElementById('zoomContainer');
    const zoomedImage = document.getElementById('zoomedImage');
    
    let autoSlideTimer;

    // === FUNCIÓN PRINCIPAL: ACTUALIZAR BOTÓN ===
    function updateActionButton(index) {
        if (!images[index] || !actionBtn) return;

        const link = images[index].getAttribute('data-link');
        
        // Verificamos si hay link y si no está vacío
        if (link && link.trim() !== '') {
            actionBtn.href = link;
            actionBtn.style.display = 'inline-flex'; // Flex para alinear el icono
        } else {
            actionBtn.style.display = 'none';
        }
    }

    function showImage(index) {
        if(images.length > 0) {
            // Ocultar actual
            images[currentIndex].classList.remove('active');
            if(indicators[currentIndex]) indicators[currentIndex].classList.remove('active');
            
            // Calcular nuevo índice
            currentIndex = (index + images.length) % images.length;
            
            // Mostrar nueva
            images[currentIndex].classList.add('active');
            if(indicators[currentIndex]) indicators[currentIndex].classList.add('active');

            // Actualizar botón
            updateActionButton(currentIndex);
        }
    }

    function nextImage() { showImage(currentIndex + 1); resetAutoSlide(); }
    function prevImage() { showImage(currentIndex - 1); resetAutoSlide(); }

    function startAutoSlide() {
        if(images.length > 1) {
            autoSlideTimer = setInterval(() => showImage(currentIndex + 1), 5000);
        }
    }

    function resetAutoSlide() {
        clearInterval(autoSlideTimer);
        startAutoSlide();
    }

    function closePopup() {
        if(popup) popup.style.display = 'none';
        clearInterval(autoSlideTimer);
        closeZoom();
    }

    function openZoom(src) {
        zoomedImage.src = src;
        zoomContainer.classList.add('active');
        clearInterval(autoSlideTimer);
    }

    function closeZoom() {
        zoomContainer.classList.remove('active');
        if(popup.style.display !== 'none') startAutoSlide();
    }

    // === EVENTOS ===
    if(prevBtn) prevBtn.addEventListener('click', (e) => { e.stopPropagation(); prevImage(); });
    if(nextBtn) nextBtn.addEventListener('click', (e) => { e.stopPropagation(); nextImage(); });
    if(closeBtn) closeBtn.addEventListener('click', (e) => { e.stopPropagation(); closePopup(); });
    
    // Click fuera del popup para cerrar
    if(popup) popup.addEventListener('click', (e) => { if(e.target === popup) closePopup(); });

    // Click en imágenes para zoom
    images.forEach(img => {
        img.addEventListener('click', (e) => {
            e.stopPropagation();
            openZoom(img.src);
        });
    });

    // Cerrar zoom
    if(zoomContainer) zoomContainer.addEventListener('click', () => closeZoom());

    // Indicadores
    indicators.forEach(ind => {
        ind.addEventListener('click', (e) => {
            e.stopPropagation();
            showImage(parseInt(ind.dataset.index));
            resetAutoSlide();
        });
    });

    // Teclado
    document.addEventListener('keydown', (e) => {
        if(zoomContainer.classList.contains('active')) {
            if(e.key === 'Escape') closeZoom();
        } else if (popup.style.display !== 'none') {
            if(e.key === 'Escape') closePopup();
            if(e.key === 'ArrowRight') nextImage();
            if(e.key === 'ArrowLeft') prevImage();
        }
    });

    // === INICIALIZACIÓN ===
    if(images.length === 0) {
        if(popup) popup.style.display = 'none';
    } else {
        updateActionButton(0); // Verificar la primera imagen al cargar
        startAutoSlide();
    }
});
</script>