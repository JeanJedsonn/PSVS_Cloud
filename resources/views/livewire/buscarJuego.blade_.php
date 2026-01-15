<div>
    <!-- Formulario para buscar un juego -->
    {{-- wire:submit se le asigna la funcion a llmar del componente --}}
    <form wire:submit="buscarJuego">
        <!-- Input de búsqueda, wire:model se le asigna la variable del componente -->
        <input type="text" placeholder="Buscar..." wire:model="respuesta" >
        <!-- Botón para buscar el juego, no se le pasa ninguna accion -->
        <button type="submit">Buscar</button>
    </form>

    <!-- ...tabla... -->
    <table border="1" cellpadding="5">

        <!-- ...encabezado de la tabla... -->
        <thead>
            <tr>
                <th>Título</th>
                <th>Consola</th>
                <th>Precio Original</th>
                <th>Oferta</th>
                <th>Link</th>
                <th>Caratula</th>
                <th>Guardar</th>
            </tr>
        </thead>

        <!-- ... cuerpo de la tabla... -->
        <tbody>

            <!-- ...repetir por cada juego... -->
            @foreach($resultados as $clave => $juego)
                <tr>
                    <td>{{ $juego['titulo'] }}</td>
                    <td>{{ $juego['consola'] }}</td>
                    <td>
                        @foreach ($juego['precioOriginal'] as $region => $precio)
                            <p>{{ $region }}: {{ $precio }}</p>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($juego['precioActual'] as $region => $precio)
                            <p>{{ $region }}: {{ $precio }}</p>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($juego['link'] as $region => $link)
                            <p>{{ $link }}</p>
                            {{--
                            <a href="{{ $link }}" target="_blank">
                                {{ $region }}
                            </a>--}}
                            <br>
                        @endforeach
                    </td>
                    <td><img src="{{ $juego['imagenURL'] }}" alt="imgLow" width="128"></td>
                    <td>
                        <button
                            type="submit"
                            wire:click="guardar('{{ $clave }}')"
                            wire:loading.attr="disabled"
                        >Guardar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
