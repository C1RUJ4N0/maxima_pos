<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Maxima')</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-100 min-h-screen">

    
    <nav class="bg-white shadow-md p-4 flex justify-between items-center">
        <div class="text-2xl font-bold text-gray-800">
            Maxima
        </div>
        <div class="flex items-center space-x-6">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
            <a href="{{ route('statistics.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-chart-line"></i>
                <span>Estadísticas</span>
            </a>
            <a href="{{ route('inventory.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-box-open"></i>
                <span>Inventario</span>
            </a>
            <a href="{{ route('apartados.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-calendar-check"></i>
                <span>Apartados</span>
            </a>
            <a href="{{ route('providers.index') }}" class="flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors duration-200">
                <i class="fas fa-truck"></i>
                <span>Proveedores</span>
            </a>
        </div>
        <div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition-colors duration-200">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </nav>

    
    <div class="container mx-auto p-8">
        @yield('content')
    </div>

    @yield('scripts')
</body>
</html>