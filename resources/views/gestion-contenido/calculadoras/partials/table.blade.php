<table class="table table-hover align-middle mb-0 text-sm {{ isset($sticky) && $sticky ? 'sticky-header' : '' }}">
    <thead class="table-light">
        <tr>
            @if(isset($showSystem) && $showSystem) <th class="ps-4">Sistema</th> @endif
            <th class="{{ isset($showSystem) && $showSystem ? '' : 'ps-4' }}">Tipo de Tabla</th>
            <th class="text-center">Rango</th>
            <th class="text-end">Desde (S/.)</th>
            <th class="text-end">Hasta (S/.)</th>
            <th class="text-end">Monto Fijo</th>
            <th class="text-center">% Exceso</th>
            <th class="text-end pe-4">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $item)
        <tr>
            @if(isset($showSystem) && $showSystem)
                <td class="ps-4">
                    @if($item->tipo_calculadora == 'servicio_arbitral') <span class="badge bg-danger">Arbitraje</span>
                    @elseif($item->tipo_calculadora == 'junta_prevencion') <span class="badge bg-info text-dark">Junta</span>
                    @else <span class="badge bg-secondary">--</span> @endif
                </td>
            @endif
            
            <td class="{{ isset($showSystem) && $showSystem ? '' : 'ps-4' }}">
                @if($item->tipo == 'arbitro_unico') <span class="badge bg-primary">Árbitro Único</span>
                @elseif($item->tipo == 'tribunal_arbitral') <span class="badge bg-success">Tribunal</span>
                @else <span class="badge bg-secondary">Gastos Admin</span> @endif
            </td>
            <td class="text-center fw-bold">{{ $item->rango_letra }}</td>
            <td class="text-end">{{ number_format($item->monto_min, 2) }}</td>
            <td class="text-end">{{ $item->monto_max ? number_format($item->monto_max, 2) : 'A más' }}</td>
            <td class="text-end fw-bold">S/. {{ number_format($item->monto_fijo, 2) }}</td>
            <td class="text-center">
                @if($item->porcentaje_exceso > 0) <span class="badge bg-warning text-dark">{{ $item->porcentaje_exceso }}%</span>
                @else <span class="text-muted">-</span> @endif
            </td>
            <td class="text-end pe-4">
                <div class="btn-group">
                    <a href="{{ route('calculadoras-gestion.show', $item->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('calculadoras-gestion.edit', $item->id) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="confirmAction('{{ route('calculadoras-gestion.destroy', $item->id) }}', 'delete')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-4 text-muted">No hay tarifas registradas en esta sección.</td></tr>
        @endforelse
    </tbody>
</table>