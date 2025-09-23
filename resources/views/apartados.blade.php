@extends('layouts.app')

@section('title', 'Gestión de Apartados')

@section('content')
    <main class="bg-white p-6 rounded-lg shadow-xl border">
        <div class="flex items-center space-x-4 mb-4">
            <h2 class="text-xl font-semibold text-gray-700">Listado de Apartados</h2>
            <select class="p-2 border rounded-lg">
                <option>Vigente</option>
                <option>Vencido</option>
                <option>Pagado</option>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow border">
                <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">Cliente</th>
                        <th class="py-3 px-6 text-left">Número de Teléfono</th>
                        <th class="py-3 px-6 text-right">Abonado</th>
                        <th class="py-3 px-6 text-right">Faltante</th>
                        <th class="py-3 px-6 text-left">Fecha de Pago</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <template x-for="apartado in apartados" :key="apartado.id">
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-6 text-left font-medium" x-text="apartado.client"></td>
                            <td class="py-3 px-6 text-left" x-text="apartado.phone"></td>
                            <td class="py-3 px-6 text-right" x-text="`$${apartado.paid.toLocaleString()}`"></td>
                            <td class="py-3 px-6 text-right" x-text="`$${(apartado.amount - apartado.paid).toLocaleString()}`"></td>
                            <td class="py-3 px-6 text-left" x-text="apartado.due_date"></td>
                            <td class="py-3 px-6 text-center space-x-2">
                                <button @click="printApartadoTicket(apartado)" class="bg-gray-200 text-gray-800 text-xs font-semibold px-2 py-1 rounded-full hover:bg-gray-300 transition-colors">Imprimir Ticket</button>
                                <button @click="notifyClient(apartado)" class="bg-yellow-200 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full hover:bg-yellow-300 transition-colors">Notificar</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </main>

    <div x-cloak x-show="showNotification" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 transform transition-all" :class="notification.success ? 'bg-green-500' : 'bg-red-500'">
        <span x-text="notification.message"></span>
    </div>
@endsection

@section('scripts')
    <script>
        function apartados() {
            return {
                apartados: [
                    { id: 1, client: 'Jorge Salinas', phone: '03754692134', paid: 4000.00, amount: 4780.00, due_date: '2025-06-26' },
                    { id: 2, client: 'Lionel Messi', phone: '03754789345', paid: 3000.00, amount: 5540.00, due_date: 'Vigente' },
                    { id: 3, client: 'Ricardo Arjona', phone: '03754603297', paid: 4800.00, amount: 6400.00, due_date: '2024-12-30' },
                ],
                showNotification: false,
                notification: { message: '', success: true },

                printApartadoTicket(apartado) {
                    this.notify(`Imprimiendo ticket del apartado de ${apartado.client}...`, true);
                },

                notifyClient(apartado) {
                     this.notify(`Notificando al cliente ${apartado.client}...`, true);
                },

                notify(message, success = true) {
                    this.notification = { message, success };
                    this.showNotification = true;
                    setTimeout(() => this.showNotification = false, 3000);
                }
            }
        }
    </script>
@endsection
