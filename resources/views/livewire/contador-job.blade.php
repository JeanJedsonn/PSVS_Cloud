<div wire:poll.5s>
    @if($jobsCount > 0)
        <div class="alert alert-warning d-flex align-items-center mb-4 shadow-sm" role="alert">
            <div class="spinner-border spinner-border-sm text-warning me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div>
                <strong>Procesando segundo plano:</strong> Hay <strong>{{ $jobsCount }}</strong> tareas pendientes.
            </div>
        </div>
    @elseif($jobsCount == 0 && $failedJobsCount == 0)
        <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm" role="alert">
            <div class="spinner-border spinner-border-sm text-success me-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div>
                <strong>Procesando segundo plano:</strong> No hay tareas pendientes.
            </div>
        </div>
    @endif

    @if($failedJobsCount > 0)
        <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm" role="alert">
            <div class="me-2 text-danger">⚠️</div>
            <div>
                <strong>Atención:</strong> Hay <strong>{{ $failedJobsCount }}</strong> tarea(s) fallida(s).
            </div>
        </div>
    @endif
</div>
