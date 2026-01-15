{{-- filepath: g:\Programacion\PHP\prueba\test\resources\views\juegos\edit.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Juego</title>
</head>

<body>
    <!-- titulo -->
    <h1>Editar Juego</h1>
    @if($errors->any())
    <ul>
        @foreach($errors->all() as $error)
            <li>{{$error}}</li>
        @endforeach
    </ul>
    @endif
    <!-- formulario para editar un juego -->
    <form method="POST" action="{{ route('juegos.update', $juego->id) }}">
        @csrf           <!-- Token CSRF para seguridad -->
        @method('PUT')  <!-- Metodo HTTP para actualizar -->

        <label>Título: </label>
            <input type="text" name="titulo" value="{{$juego->titulo}}"><br>

        <label>Descripción: </label>
            <textarea name="descripcion" style="resize: none;" rows="4">{{$juego->descripcion}}</textarea><br>

        <label>Plataforma: </label>
            <input type="text" name="plataforma" value="{{$juego->plataforma}}"><br>

        <label>Imagen URL: </label>
            <input type="text" name="imgURL" value="{{$juego->imgURL}}"><br>

        <label>Imagen Baja URL: </label>
            <input type="text" name="imgLowURL" value="{{$juego->imgLowURL}}"><br>

        <label>ID:</label>
            <input type="text" name="id" value="{{$juego->id}}"><br>

        <!-- boton para cargar datos-->
        <button type="submit">Actualizar</button>
    </form>

    <!-- boton para volver a la lista de juegos -->
    <a href="{{ route('index') }}">Volver</a>
</body>
</html>
