<div>
    @if(isset($datos['error']))
        <div style="border: 1px solid #dc3545; border-radius: 8px; padding: 10px; width: fit-content; margin-bottom: 20px;">
            <p style="color: #dc3545; margin: 0; font-size: 0.9em;">⚠️ {{ $datos['error'] }}</p>
        </div>
    @elseif($datos)
        <div
            style="border: 1px solid #ccc; border-radius: 8px; padding: 10px; width: fit-content; margin-bottom: 20px; display: inline-block;">
            <h3 style="margin: 0; color: #555; font-size: 1em;">{{ $datos['label'] }}</h3>
            @if(isset($datos['precio']))
                <p style="font-size: 1.2em; font-weight: bold; margin: 5px 0 0 0; color: #333;">
                    {{ number_format($datos['precio'], 2, ',', '.') }} Bs.
                </p>
            @endif
        </div>
    @else
        <div style="border: 1px solid #ffc107; border-radius: 8px; padding: 10px; width: fit-content; margin-bottom: 20px;">
            <p style="color: #856404; margin: 0; font-size: 0.9em;">⚠️ N/A</p>
        </div>
    @endif
</div>