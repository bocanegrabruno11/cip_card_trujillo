@extends('eventoscipcdll.layout')

@section('title', 'Dashboard - Control de Asistencia')

@section('content')
<div class="dashboard-container">
    <!-- Tarjetas de resumen -->
    <div class="stats-cards">
        <div class="card card-asistieron" onclick="mostrarAsistieron()">
            <div class="card-icon">✅</div>
            <div class="card-info">
                <h3>Asistieron</h3>
                <div class="card-number" id="totalAsistieron">0</div>
                <span class="card-link">Ver detalles →</span>
            </div>
        </div>

        <div class="card card-faltan" onclick="mostrarFaltan()">
            <div class="card-icon">⏳</div>
            <div class="card-info">
                <h3>Faltan por llegar</h3>
                <div class="card-number" id="totalFaltan">0</div>
                <span class="card-link">Ver detalles →</span>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda -->
    <div class="search-bar">
        <div class="search-icon">🔍</div>
        <input type="text" id="searchInput" placeholder="Buscar por CIP, DNI, nombres, apellidos o capítulo..." onkeyup="filtrarTabla()">
        <button class="clear-search" onclick="limpiarBusqueda()" style="display:none;">✖</button>
    </div>

    <!-- Tabla de asistentes -->
    <div class="table-responsive">
        <table class="asistentes-table" id="asistentesTable">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>CIP</th>
                    <th>DNI</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Capítulo</th>
                    <th>Celular</th>
                    <th>Correo</th>
                    <th>Estado Asistencia</th>
                </tr>
            </thead>
            <tbody id="asistentesList">
                <tr>
                    <td colspan="9" class="loading-text">Cargando asistentes aprobados...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
.dashboard-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Tarjetas */
.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-left: 5px solid;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.card-asistieron {
    border-left-color: #4caf50;
}

.card-faltan {
    border-left-color: #ff9800;
}

.card-icon {
    font-size: 48px;
}

.card-info {
    flex: 1;
}

.card-info h3 {
    margin: 0 0 5px 0;
    font-size: 16px;
    color: #666;
}

.card-number {
    font-size: 36px;
    font-weight: bold;
    margin: 10px 0;
}

.card-asistieron .card-number {
    color: #4caf50;
}

.card-faltan .card-number {
    color: #ff9800;
}

.card-link {
    font-size: 12px;
    color: #999;
    transition: color 0.3s;
}

.card:hover .card-link {
    color: var(--rojo);
}

/* Barra de búsqueda */
.search-bar {
    position: relative;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 12px;
    font-size: 18px;
    color: #999;
    pointer-events: none;
}

.search-bar input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}

.search-bar input:focus {
    outline: none;
    border-color: var(--rojo);
    box-shadow: 0 0 0 3px rgba(179, 0, 0, 0.1);
}

.clear-search {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #999;
    padding: 0 5px;
}

.clear-search:hover {
    color: var(--rojo);
}

/* Tabla */
.table-responsive {
    overflow-x: auto;
    border-radius: 8px;
}

.asistentes-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.asistentes-table th {
    background: var(--rojo-oscuro);
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: bold;
    position: sticky;
    top: 0;
    white-space: nowrap;
}

.asistentes-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e0e0e0;
}

.asistentes-table tbody tr {
    transition: background-color 0.2s;
}

.asistentes-table tbody tr:hover {
    background-color: #f9f9f9;
}

/* Filtro visual para búsqueda */
.asistentes-table tbody tr.hidden-row {
    display: none;
}

.loading-text {
    text-align: center;
    padding: 40px;
    color: #666;
}

.no-results {
    text-align: center;
    padding: 40px;
    color: #999;
    font-style: italic;
}

/* Badges de asistencia */
.asistio-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    min-width: 100px;
}

.asistio-si {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

.asistio-no {
    background: #ffebee;
    color: #c62828;
    border: 1px solid #ef9a9a;
}

.asistio-pendiente {
    background: #fff3e0;
    color: #e65100;
    border: 1px solid #ffcc80;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 15px;
    }
    
    .stats-cards {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .card {
        padding: 15px;
    }
    
    .card-icon {
        font-size: 36px;
    }
    
    .card-number {
        font-size: 28px;
    }
    
    .asistentes-table {
        font-size: 12px;
    }
    
    .asistentes-table th,
    .asistentes-table td {
        padding: 8px;
    }
    
    .asistio-badge {
        min-width: 80px;
        font-size: 11px;
        padding: 4px 8px;
    }
    
    .search-bar input {
        font-size: 14px;
        padding: 10px 10px 10px 35px;
    }
}
</style>

<script>
let todosLosAsistentes = [];

document.addEventListener('DOMContentLoaded', function() {
    cargarAprobados();
});

function cargarAprobados() {
    fetch('/ver-aprobados', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = '/login-eventos';
                throw new Error('No autorizado');
            }
            throw new Error('Error en la respuesta');
        }
        return response.json();
    })
    .then(data => {
        if (data.data) {
            todosLosAsistentes = data.data;
            mostrarAsistentes(todosLosAsistentes);
            actualizarTarjetas(todosLosAsistentes);
        } else if (Array.isArray(data)) {
            todosLosAsistentes = data;
            mostrarAsistentes(todosLosAsistentes);
            actualizarTarjetas(todosLosAsistentes);
        } else {
            console.error('Formato de datos inesperado:', data);
            mostrarError();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarError();
    });
}

function mostrarAsistentes(asistentes) {
    const tbody = document.getElementById('asistentesList');
    
    if (!asistentes || asistentes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="no-results">No hay asistentes para mostrar</td></tr>';
        return;
    }
    
    tbody.innerHTML = asistentes.map((asistente, index) => {
        let asistioHtml = '';
        if (asistente.asistio === 1) {
            asistioHtml = '<span class="asistio-badge asistio-si">✓ Asistió</span>';
        } else if (asistente.asistio === 0) {
            asistioHtml = '<span class="asistio-badge asistio-no">✗ No asistió</span>';
        } else {
            asistioHtml = '<span class="asistio-badge asistio-pendiente">⏳ Por llegar</span>';
        }
        
        return `
            <tr data-cip="${asistente.cip || ''}" 
                data-dni="${asistente.dni || ''}" 
                data-nombres="${(asistente.nombres || '').toLowerCase()}" 
                data-apellidos="${(asistente.apellidos || '').toLowerCase()}" 
                data-capitulo="${(asistente.capitulo || '').toLowerCase()}"
                data-asistio="${asistente.asistio}">
                <td>${index + 1}</td>
                <td>${asistente.cip || '-'}</td>
                <td>${asistente.dni || '-'}</td>
                <td>${asistente.nombres || '-'}</td>
                <td>${asistente.apellidos || '-'}</td>
                <td>${asistente.capitulo || '-'}</td>
                <td>${asistente.celular || '-'}</td>
                <td>${asistente.correo || '-'}</td>
                <td>${asistioHtml}</td>
            </td>
        `;
    }).join('');
}

function actualizarTarjetas(asistentes) {
    const asistieron = asistentes.filter(a => a.asistio === 1).length;
    const faltan = asistentes.filter(a => a.asistio === null || a.asistio === 0).length;
    
    document.getElementById('totalAsistieron').textContent = asistieron;
    document.getElementById('totalFaltan').textContent = faltan;
}

function filtrarTabla() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
    const clearBtn = document.querySelector('.clear-search');
    
    if (searchTerm === '') {
        clearBtn.style.display = 'none';
        mostrarAsistentes(todosLosAsistentes);
        actualizarTarjetas(todosLosAsistentes);
        return;
    }
    
    clearBtn.style.display = 'block';
    
    const filtered = todosLosAsistentes.filter(asistente => {
        return (asistente.cip && asistente.cip.toLowerCase().includes(searchTerm)) ||
               (asistente.dni && asistente.dni.toLowerCase().includes(searchTerm)) ||
               (asistente.nombres && asistente.nombres.toLowerCase().includes(searchTerm)) ||
               (asistente.apellidos && asistente.apellidos.toLowerCase().includes(searchTerm)) ||
               (asistente.capitulo && asistente.capitulo.toLowerCase().includes(searchTerm));
    });
    
    mostrarAsistentes(filtered);
    actualizarTarjetas(filtered);
}

function limpiarBusqueda() {
    document.getElementById('searchInput').value = '';
    document.querySelector('.clear-search').style.display = 'none';
    mostrarAsistentes(todosLosAsistentes);
    actualizarTarjetas(todosLosAsistentes);
}

function mostrarAsistieron() {
    const asistieron = todosLosAsistentes.filter(a => a.asistio === 1);
    mostrarAsistentes(asistieron);
    actualizarTarjetas(asistieron);
    document.getElementById('searchInput').value = '🔍 Mostrando: Asistieron';
    document.querySelector('.clear-search').style.display = 'block';
    
    // Scroll suave a la tabla
    document.querySelector('.table-responsive').scrollIntoView({ behavior: 'smooth' });
}

function mostrarFaltan() {
    const faltan = todosLosAsistentes.filter(a => a.asistio === null || a.asistio === 0);
    mostrarAsistentes(faltan);
    actualizarTarjetas(faltan);
    document.getElementById('searchInput').value = '🔍 Mostrando: Faltan por llegar';
    document.querySelector('.clear-search').style.display = 'block';
    
    // Scroll suave a la tabla
    document.querySelector('.table-responsive').scrollIntoView({ behavior: 'smooth' });
}

function mostrarError() {
    const tbody = document.getElementById('asistentesList');
    tbody.innerHTML = '<tr><td colspan="9" class="loading-text">❌ Error al cargar los datos. Intente nuevamente.</td></tr>';
    document.getElementById('totalAsistieron').textContent = 'Error';
    document.getElementById('totalFaltan').textContent = 'Error';
}
</script>
@endsection