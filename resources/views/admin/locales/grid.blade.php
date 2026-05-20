@if($locales->count() > 0)
    <div class="locales-grid">
        @foreach($locales as $local)
            <div class="local-card">
            <!-- Card Header with Image -->
            <div class="local-card-header">
                @if($local->image_logo)
                    <img src="{{ asset($local->image_logo) }}" alt="{{ $local->name }}">
                @else
                    <div class="local-card-header-placeholder">
                        <i class="fas fa-store"></i>
                    </div>
                @endif
            </div>
            
            <!-- Card Body -->
            <div class="local-card-body">
                <div class="local-card-head">
                    <h3 class="local-card-name" title="{{ $local->name }}">{{ $local->name }}</h3>
                    <div class="local-actions">
                        <button type="button"
                                class="icon-action-btn edit btn-edit-local"
                                data-id="{{ $local->local_id }}"
                                data-name="{{ $local->name }}"
                                data-manager-id="{{ optional($local->users->first())->user_id }}"
                                data-update-url="{{ route('locales.update', $local->local_id) }}"
                                title="Editar local">
                            <i class="fas fa-pen-to-square"></i>
                        </button>
                        <form id="del-local-{{ $local->local_id }}" method="POST" action="{{ route('locales.destroy', $local->local_id) }}" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="icon-action-btn delete btn-delete" data-id="{{ $local->local_id }}" data-name="{{ $local->name }}" title="Eliminar local">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Status Badge -->
                <div class="local-card-status status-toggler {{ $local->status === 'Active' ? 'status-active' : 'status-inactive' }}" 
                     data-local-id="{{ $local->local_id }}"
                     data-current-status="{{ $local->status }}"
                     data-status-label="{{ $local->status === 'Active' ? 'Activo' : 'Inactivo' }}"
                     title="Haz clic para cambiar el estado">
                    <i class="fas fa-{{ $local->status === 'Active' ? 'check-circle' : 'times-circle' }}"></i>
                    <span>{{ $local->status === 'Active' ? 'Activo' : 'Inactivo' }}</span>
                </div>
                
                @if($local->description)
                    <p class="local-card-description">{{ $local->description }}</p>
                @endif
                
                @if($local->contact)
                    <p class="local-card-contact">
                        <i class="fas fa-phone" style="color: #e18018;"></i>
                        {{ $local->contact }}
                    </p>
                @endif
                
                <!-- Managers Section -->
                @if($local->users->count() > 0)
                    <div class="local-card-managers">
                        <div class="managers-label">
                            <i class="fas fa-users" style="color: #e18018; margin-right: 6px;"></i>
                            Gerentes
                        </div>
                        <div>
                            @foreach($local->users as $manager)
                                <span class="manager-badge">{{ $manager->full_name }}</span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="local-card-managers">
                        <div class="managers-label">
                            <i class="fas fa-users" style="color: #d1d5db; margin-right: 6px;"></i>
                        </div>
                        <div style="color: #d1d5db; font-size: 12px;">
                            Sin gerentes asignados
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
@else
    <!-- Empty State -->
    <div class="no-locales">
        <i class="fas fa-inbox"></i>
        <h4>No hay locales registrados</h4>
        <p style="margin: 0;">Aún no hay locales en el sistema. Los locales aparecerán aquí cuando sean creados.</p>
    </div>
@endif
