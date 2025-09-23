@extends('layouts.app')

@section('title', 'Panel de Estadísticas')

@section('content')
<main class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">

<div class="col-span-1 lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
<div class="bg-white p-6 rounded-lg shadow-sm text-center">
<p class="font-semibold text-gray-600">VENTAS DE HOY</p>
<p class="text-3xl font-bold mt-2 text-indigo-600">$17.250,00</p>
</div>
<div class="bg-white p-6 rounded-lg shadow-sm text-center">
<p class="font-semibold text-gray-600">VENTAS DEL MES</p>
<p class="text-3xl font-bold mt-2 text-indigo-600">$480.850,00</p>
</div>
<div class="bg-white p-6 rounded-lg shadow-sm text-center">
<p class="font-semibold text-gray-600">EGRESOS DEL MES</p>
<p class="text-3xl font-bold mt-2 text-red-600">$3.250,00</p>
</div>
<div class="bg-white p-6 rounded-lg shadow-sm flex flex-col items-center justify-center">
<p class="font-semibold text-gray-600">PRODUCTOS CON BAJO STOCK</p>
<i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mt-2"></i>
</div>
</div>

    
    <div class="bg-white p-6 rounded-lg shadow-md border">
        <h4 class="text-lg font-semibold mb-2">Estadísticas de Ventas</h4>
        <canvas id="salesChart" class="w-full h-64"></canvas>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md border overflow-x-auto">
        <h4 class="text-lg font-semibold mb-2">Apartados por Vencer</h4>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-2 px-4 text-left font-semibold">Cliente</th>
                    <th class="py-2 px-4 text-left font-semibold">Número de Teléfono</th>
                    <th class="py-2 px-4 text-right font-semibold">Monto</th>
                    <th class="py-2 px-4 text-left font-semibold">Fecha de Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="py-2 px-4">Lionel Messi</td>
                    <td class="py-2 px-4">03754789345</td>
                    <td class="py-2 px-4 text-right">$3.000,00</td>
                    <td class="py-2 px-4">2025-09-20</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 px-4">Cristiano Ronaldo</td>
                    <td class="py-2 px-4">03754123456</td>
                    <td class="py-2 px-4 text-right">$2.500,00</td>
                    <td class="py-2 px-4">2025-10-15</td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function renderSalesChart() {
const ctx = document.getElementById('salesChart');
new Chart(ctx, {
type: 'bar',
data: {
labels: ['Día 1', 'Día 2', 'Día 3', 'Día 4', 'Día 5', 'Día 6', 'Día 7'],
datasets: [{
label: 'Ventas Diarias',
data: [1200, 800, 1500, 2000, 1300, 1800, 2500],
backgroundColor: 'rgba(79, 70, 229, 0.8)',
borderColor: 'rgba(79, 70, 229, 1)',
borderWidth: 1,
borderRadius: 6
}]
},
options: {
responsive: true,

scales: {
y: {
beginAtZero: true,
title: {
display: true,
text: 'Monto de Venta ($)'
}
}
},
plugins: {
legend: {
display: false
}
}
}
});
}
document.addEventListener('DOMContentLoaded', renderSalesChart);
</script>
@endsection