{{-- filepath: g:\Programacion\PHP\prueba\test\resources\views\juegos\create.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Juego</title>
</head>

<body>
    <h1>Agregar Nuevo Juego</h1>

    <!-- Formulario para agregar un nuevo juego -->
    <form action="{{ route('juegos.store') }}" method="POST">
        @csrf <!-- Token CSRF para seguridad -->

        <!-- Campos del formulario -->
        <label>Título: <input type="text" name="titulo" required></label><br>
        <label>Descripción: <textarea name="descripcion" rows="4" style="resize: none;"></textarea></label><br>
        <label>Plataforma: <input type="text" name="plataforma" required></label><br>
        <label>Imagen URL: <input type="text" name="imgURL"></label><br>
        <label>Imagen Baja URL: <input type="text" name="imgLowURL"></label><br>

        <!-- Botón para guardar el juego -->
        <button type="submit">Guardar</button>
    </form>
    <a href="{{ route('index') }}">Volver</a>
</body>
</html>
