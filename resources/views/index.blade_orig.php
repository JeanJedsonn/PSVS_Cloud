{{-- filepath: g:\Programacion\PHP\prueba\test\resources\views\juegos\index.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Juegos</title>
</head>
<body>


    <h1>Juegos</h1>

    {{-- <a href="{{ route('juegos.create') }}">Agregar nuevo juego</a> --}}
    <!--a href="{{ route('juegos.create') }}">Agregar nuevo juego</a --><br>
    <a href="{{ route('juegos.buscar') }}">Buscar juego</a><br>

    {{-- filepath: g:\Programacion\PHP\prueba\test\resources\views\index.blade.php --}}
    <a href="{{ route('regionMonedas.index') }}">Administrar Regiones y Monedas</a><br>

    <!-- ...resto del código... -->
    <livewire:tabla>

    <!-- ...tabla... -->
    <table border="1" cellpadding="5">

        <!-- ...encabezado de la tabla... -->
        <thead>
            <tr>
                <th>Título</th>
                <th>Links</th>
                <th>Precio Estandar</th>
                <th>Oferta</th>
                <th>Consola</th>
                <th>Imagen</th>
                <th>Acciones</th>
                <th>Eliminar</th>
            </tr>
        </thead>

        <!-- ... cuerpo de la tabla... -->
        <tbody>

            <!-- ...repetir por cada juego... -->
            @foreach($juegos as $juego)
                <tr>
                    <td>{{ $juego->titulo }}</td>
                    <td>
                        @foreach ($juego->juegoMonedas as $moneda)
                            <a href="{{ $moneda->link }}" target="_blank">
                                {{ $moneda->moneda->region }}
                            </a>
                            </br>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($juego->juegoMonedas as $moneda)
                            <p> {{ $moneda->moneda->region }}: {{ $moneda->precio_original }} </p>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($juego->juegoMonedas as $moneda)
                            <p> {{ $moneda->moneda->region }}: {{ $moneda->precio_oferta }} </p>
                        @endforeach
                    <td>{{ $juego->plataforma }}</td>

                    <td><img src="{{ $juego->imgLowURL }}" alt="imgLow" width="128"></td>

                    <!--botones CRUD -->
                    <td>

                        <!-- boton Editar -->
                        <form action="{{ route('juegos.edit', $juego->id) }}" method="GET" style="display:inline;">
                            <input type="submit" value="Editar">
                        </form>

                        <!-- boton Actualizar -->


                    </td>
                    <td>
                        <!-- boton eliminar -->
                        <form action="{{ route('juegos.destroy', $juego->id) }}" method="POST" style="display:inline;">
                            @csrf               <!-- Token CSRF para seguridad -->
                            @method('DELETE')   <!-- Método HTTP para eliminar -->
                            <input type="submit" value="Eliminar"/>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
