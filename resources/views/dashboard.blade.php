
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - POS</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            min-height: 100vh;
        }
        [x-cloak] { display: none !important; }
        /* Estilos personalizados para el contenedor del modal, para que funcione correctamente */
        .modal-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.75rem;
            max-width: 90%;
            max-height: 90%;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
    </style>
</head>
<body class="p-4 sm:p-8">

<!-- Encabezado de la aplicación -->
<header class="bg-white rounded-lg shadow-xl p-4 mb-4 flex justify-between items-center flex-wrap gap-4 lg:gap-0">
    <!-- Contenedor del título y el botón "Home" -->
    <div class="flex items-center gap-4">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Bienvenido, Usuario.</h1>
        <a href="#" class="flex items-center gap-1.5 bg-gray-100 text-gray-700 px-3 py-1.5 rounded-full hover:bg-gray-200 transition-colors">
            <i class="fas fa-house-chimney text-lg"></i>
            <span class="text-sm font-semibold">Home</span>
        </a>
    </div>
    <!-- Contenedor de los botones de acción -->
<div class="flex flex-wrap items-center space-x-2">
    <a href="{{ route('statistics.index') }}"
       class="bg-gray-200 text-gray-800 text-sm font-semibold px-3 py-1.5 rounded-full hover:bg-gray-300 transition-colors">
        <i class="fas fa-chart-line mr-1"></i> Estadísticas
    </a>

    <a href="{{ route('inventory.index') }}"
       class="bg-gray-200 text-gray-800 text-sm font-semibold px-3 py-1.5 rounded-full hover:bg-gray-300 transition-colors">
        <i class="fas fa-box-open mr-1"></i> Inventario
    </a>

    <a href="{{ route('providers.index') }}"
       class="bg-gray-200 text-gray-800 text-sm font-semibold px-3 py-1.5 rounded-full hover:bg-gray-300 transition-colors">
        <i class="fas fa-truck-fast mr-1"></i> Proveedores
    </a>

    <a href="{{ route('apartados.index') }}"
       class="bg-gray-200 text-gray-800 text-sm font-semibold px-3 py-1.5 rounded-full hover:bg-gray-300 transition-colors">
        <i class="fas fa-inbox mr-1"></i> Apartados
    </a>
</div>


<div class="grid grid-cols-12 gap-4 lg:gap-6" x-data="pos()">

    <!-- Columna Izquierda: Productos e Inventario -->
    <div class="col-span-12 lg:col-span-5 bg-white rounded-lg shadow-xl p-4 flex flex-col min-h-[80vh]">
        <div class="relative mb-4">
            <input type="text" x-model="search" placeholder="Buscar Producto..." class="w-full pl-10 pr-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        
        <div class="flex-1 overflow-y-auto">
            <table class="w-full text-sm text-left text-gray-500 rounded-lg overflow-hidden">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-3 sm:px-6 py-3">Producto</th>
                        <th class="px-3 sm:px-6 py-3 text-center">Stock</th>
                        <th class="px-3 sm:px-6 py-3">Precio</th>
                        <th class="px-3 sm:px-6 py-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="product-list">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <tr class="bg-white hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 sm:px-6 py-4 font-medium text-gray-900" x-text="product.name"></td>
                            <td class="px-3 sm:px-6 py-4 text-center" x-text="product.stock"></td>
                            <td class="px-3 sm:px-6 py-4" x-text="`$${product.price.toLocaleString()}`"></td>
                            <td class="px-3 sm:px-6 py-4 text-center">
                                <button @click="addToCart(product)"
                                        class="text-blue-600 hover:text-blue-800 text-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        :disabled="product.stock === 0"
                                        title="Agregar al carrito">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredProducts.length === 0">
                        <tr>
                            <td colspan="4" class="text-center py-8 text-gray-500">
                                <p>No hay productos en el inventario.</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="mt-4 flex gap-4">
            <button @click="showAddProductModal = true" class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 text-center transition-colors">
                Agregar Producto
            </button>
        </div>
    </div>

    <!-- Columna Central: Carrito -->
    <div class="col-span-12 lg:col-span-3 bg-white rounded-lg shadow-xl p-4 flex flex-col min-h-[80vh]">
        <h2 class="text-xl font-bold border-b pb-2 mb-4">Carrito de Venta</h2>
        <div class="flex-1 overflow-y-auto space-y-3" id="cart-list">
            <template x-if="cart.length === 0">
                <p class="text-gray-500 text-center mt-8">El carrito está vacío.</p>
            </template>
            <template x-for="item in cart" :key="item.id">
                <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                    <img :src="item.image" :alt="item.name" class="w-12 h-12 object-cover rounded-md mr-3">
                    <div class="flex-grow">
                        <p class="font-semibold text-sm sm:text-base" x-text="item.name"></p>
                        <p class="text-xs sm:text-sm text-gray-600" x-text="`Subtotal: $${(item.price * item.quantity).toLocaleString()}`"></p>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">
                        <input type="number" x-model.number="item.quantity" @change="updateQuantity(item.id, $event.target.value)" min="1" :max="item.stock" class="w-12 sm:w-16 text-center border rounded-lg">
                        <button @click="removeFromCart(item.id)" class="text-red-500 hover:text-red-700 transition-colors" title="Quitar del carrito">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
        <div class="mt-4 pt-4 border-t-2">
            <div class="flex justify-between items-center mb-2">
                <span class="font-semibold text-lg">Total:</span>
                <span class="font-bold text-2xl text-indigo-600" x-text="`$${total.toLocaleString()}`"></span>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Checkout -->
    <div class="col-span-12 lg:col-span-4 bg-white rounded-lg shadow-xl p-4 flex flex-col min-h-[80vh]">
        <h2 class="text-xl font-bold border-b pb-2 mb-4">Checkout</h2>
        
        <div class="space-y-4">
            <div>
                <label class="font-semibold text-gray-700">Cliente</label>
                <div class="flex items-center gap-2">
                    <select class="w-full p-2 border rounded-lg bg-white" x-model.number="selectedClientId">
                        <template x-for="client in clients" :key="client.id">
                            <option :value="client.id" x-text="client.name"></option>
                        </template>
                    </select>
                    <button @click="showAddClientModal = true" class="bg-black text-white p-2 rounded-lg hover:bg-gray-800 whitespace-nowrap transition-colors"><i class="fas fa-plus"></i> Nuevo</button>
                </div>
            </div>
            
            <div class="bg-gray-100 p-4 rounded-lg text-center">
                <p class="text-gray-600">Total a pagar:</p>
                <p class="text-4xl font-bold text-gray-800" x-text="`$${total.toLocaleString()}`"></p>
            </div>
            
            <div>
                <label for="monto_recibido" class="font-semibold text-gray-700">Monto Recibido:</label>
                <input type="number" id="monto_recibido" x-model.number="montoRecibido" @input="calculateChange()" class="w-full p-2 border rounded-lg text-lg" placeholder="$0">
            </div>
            
            <div class="bg-yellow-100 p-4 rounded-lg text-center">
                <p class="text-gray-600">Cambio:</p>
                <p class="text-3xl font-bold text-gray-800" x-text="cambioText"></p>
            </div>
        </div>
        
        <div class="mt-auto pt-4 space-y-2">
            <button @click="finalizeSale()" :disabled="cart.length === 0 || montoRecibido < total" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">FINALIZAR VENTA</button>
            <button @click="createApartado()" :disabled="cart.length === 0 || selectedClientId == 1" class="w-full bg-yellow-500 text-white font-bold py-3 px-4 rounded-lg hover:bg-yellow-600 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">CREAR APARTADO</button>
            <button @click="printReceipt()" :disabled="cart.length === 0" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors">IMPRIMIR RECIBO</button>
        </div>
    </div>

    <!-- Modal de Notificación -->
    <div x-cloak x-show="showNotification" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 transform transition-all" :class="notification.success ? 'bg-green-500' : 'bg-red-500'">
        <span x-text="notification.message"></span>
    </div>

    <!-- Modal genérico para mensajes y apartados -->
    <div id="modal-container" class="modal-container hidden" onclick="closeModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <h3 id="modal-title" class="text-xl sm:text-2xl font-bold mb-4"></h3>
            <div id="modal-message" class="text-gray-700"></div>
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors text-2xl">&times;</button>
        </div>
    </div>

    <!-- Modal para Agregar Producto -->
    <div x-cloak x-show="showAddProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4">
        <div @click.away="showAddProductModal = false" class="bg-white rounded-xl shadow-xl p-8 w-full max-w-lg">
            <h3 class="text-2xl font-bold mb-4">Añadir Nuevo Producto</h3>
            <div class="space-y-4">
                <div>
                    <label for="newProductName" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="newProductName" x-model="newProductName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="newProductPrice" class="block text-sm font-medium text-gray-700">Precio</label>
                    <input type="number" step="0.01" id="newProductPrice" x-model.number="newProductPrice" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="newProductStock" class="block text-sm font-medium text-gray-700">Stock</label>
                    <input type="number" id="newProductStock" x-model.number="newProductStock" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button @click="showAddProductModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200">Cancelar</button>
                <button @click="saveNewProduct" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">Guardar</button>
            </div>
        </div>
    </div>

    <!-- Modal para Añadir Cliente -->
    <div x-cloak x-show="showAddClientModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4">
        <div @click.away="showAddClientModal = false" class="bg-white rounded-xl shadow-xl p-8 w-full max-w-lg">
            <h3 class="text-2xl font-bold mb-4">Añadir Nuevo Cliente</h3>
            <div class="space-y-4">
                <div>
                    <label for="newClientName" class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" id="newClientName" x-model="newClientName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="newClientPhone" class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" id="newClientPhone" x-model="newClientPhone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button @click="showAddClientModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors duration-200">Cancelar</button>
                <button @click="saveNewClient" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">Guardar</button>
            </div>
        </div>
    </div>

</div>

<script>
    function showModal(title, message) {
        document.getElementById('modal-title').innerText = title;
        document.getElementById('modal-message').innerHTML = message;
        document.getElementById('modal-container').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal-container').classList.add('hidden');
    }

    function pos() {
        return {
            
            products: [
                { id: 1, name: 'Lavandina Sedile 1L', price: 780, stock: 40, image: 'https://placehold.co/150x150/F5A7F8/FFF?text=Lavandina' },
                { id: 2, name: 'Papel Higienico Higienol 4u x 38', price: 1500, stock: 38, image: 'https://placehold.co/150x150/99B898/FFF?text=Papel' },
                { id: 3, name: 'Jabon Seiseme 500g', price: 1600, stock: 41, image: 'https://placehold.co/150x150/FFD8D8/FFF?text=Jabon' },
                { id: 4, name: 'Esponja Virulana', price: 550, stock: 35, image: 'https://placehold.co/150x150/FFEFD8/FFF?text=Esponja' },
            ],
            clients: [
                { id: 1, name: 'Cliente General', phone: '' },
            ],
            providers: [
                { id: 1, name: 'TecnoCorp SA', contact: 'Juan Pérez', phone: '11-5555-1234' },
                { id: 2, name: 'LimpiaTodo SRL', contact: 'Ana Gómez', phone: '11-4444-5678' },
                { id: 3, name: 'Accesorios GEEK', contact: 'Pedro Ruiz', phone: '11-3333-9012' },
            ],
            apartados: [
                { client: 'Lionel Messi', phone: '03754789345', amount: 3000.00, due_date: '2025-09-20', items: [{ name: 'Jabon Seiseme 500g', quantity: 2, price: 1600 }] },
                { client: 'Cristiano Ronaldo', phone: '03754123456', amount: 2500.00, due_date: '2025-10-15', items: [{ name: 'Laptop Pro', quantity: 1, price: 1200000 }] }
            ],
            
            
            cart: [],
            search: '',
            selectedClientId: 1,
            montoRecibido: null,
            cambioText: '$0',
            
            
            showAddProductModal: false,
            newProductName: '',
            newProductPrice: '',
            newProductStock: '',
            showAddClientModal: false,
            newClientName: '',
            newClientPhone: '',
            showNotification: false,
            notification: { message: '', success: true },

            
            get filteredProducts() {
                if (this.search === '') {
                    return this.products;
                }
                const searchTerm = this.search.toLowerCase();
                return this.products.filter(product =>
                    product.name.toLowerCase().includes(searchTerm)
                );
            },
            
            get total() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            },

            
            init() {
                this.calculateChange();
            },

            
            viewStatistics() {
                const salesData = this.apartados.map(a => ({
                    date: a.due_date,
                    amount: a.amount
                }));
                let content = `
                    <p class="mb-4">Funcionalidad para ver estadísticas de ventas, simulado con datos de apartados:</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                            <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <tr>
                                    <th class="py-3 px-6 text-left">Fecha</th>
                                    <th class="py-3 px-6 text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                ${salesData.length > 0 ? salesData.map(d => `
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left">${d.date}</td>
                                        <td class="py-3 px-6 text-right">$${d.amount.toFixed(2)}</td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="2" class="py-4 text-center text-gray-500">No hay datos de ventas.</td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                `;
                showModal('Estadísticas de Ventas', content);
            },

            viewInventory() {
                let content = `
                    <p class="mb-4">Lista completa de productos en el inventario:</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                            <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <tr>
                                    <th class="py-3 px-6 text-left">Producto</th>
                                    <th class="py-3 px-6 text-center">Stock</th>
                                    <th class="py-3 px-6 text-right">Precio</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                ${this.products.length > 0 ? this.products.map(p => `
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left font-medium">${p.name}</td>
                                        <td class="py-3 px-6 text-center">${p.stock}</td>
                                        <td class="py-3 px-6 text-right">$${p.price.toLocaleString()}</td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-gray-500">No hay productos en el inventario.</td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                `;
                showModal('Inventario', content);
            },
            
            viewProviders() {
                let content = `
                    <p class="mb-4">Lista de proveedores registrados:</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                            <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <tr>
                                    <th class="py-3 px-6 text-left">Nombre</th>
                                    <th class="py-3 px-6 text-left">Contacto</th>
                                    <th class="py-3 px-6 text-left">Teléfono</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                ${this.providers.length > 0 ? this.providers.map(p => `
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left font-medium">${p.name}</td>
                                        <td class="py-3 px-6 text-left">${p.contact}</td>
                                        <td class="py-3 px-6 text-left">${p.phone}</td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-gray-500">No hay proveedores registrados.</td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                `;
                showModal('Gestión de Proveedores', content);
            },

            viewApartados() {
                let content = `
                    <p class="mb-4">Lista de apartados activos:</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
                            <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                <tr>
                                    <th class="py-3 px-6 text-left">Cliente</th>
                                    <th class="py-3 px-6 text-left">Teléfono</th>
                                    <th class="py-3 px-6 text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                ${this.apartados.length > 0 ? this.apartados.map(a => `
                                    <tr class="border-b hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left">${a.client}</td>
                                        <td class="py-3 px-6 text-left">${a.phone}</td>
                                        <td class="py-3 px-6 text-right">$${a.amount.toFixed(2)}</td>
                                    </tr>
                                `).join('') : `
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-gray-500">No hay apartados registrados.</td>
                                    </tr>
                                `}
                            </tbody>
                        </table>
                    </div>
                `;
                showModal('Gestión de Apartados', content);
            },

            
            addToCart(product) {
                const existingItem = this.cart.find(i => i.id === product.id);
                if (existingItem) {
                    if (existingItem.quantity < product.stock) {
                        existingItem.quantity++;
                    } else {
                        this.notify('No hay más stock para este producto.', false);
                    }
                } else {
                    if (product.stock > 0) {
                        this.cart.push({ ...product, quantity: 1 });
                    } else {
                        this.notify('Producto sin stock.', false);
                    }
                }
                this.calculateChange();
            },

            removeFromCart(id) {
                this.cart = this.cart.filter(i => i.id !== id);
                this.calculateChange();
            },

            updateQuantity(id, qty) {
                const item = this.cart.find(i => i.id === id);
                qty = parseInt(qty, 10);

                if (!item) return;

                if (isNaN(qty) || qty < 1) {
                    item.quantity = 1;
                } else if (qty > item.stock) {
                    item.quantity = item.stock;
                    this.notify(`Stock máximo para ${item.name} es ${item.stock}.`, false);
                } else {
                    item.quantity = qty;
                }
                this.calculateChange();
            },

            calculateChange() {
                const total = this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                if (this.montoRecibido === null || this.montoRecibido === '' || this.montoRecibido < total) {
                    const faltante = total - (this.montoRecibido || 0);
                    this.cambioText = `Faltan: $${faltante.toLocaleString()}`;
                } else {
                    const cambio = this.montoRecibido - total;
                    this.cambioText = `$${cambio.toLocaleString()}`;
                }
            },
            
            saveNewProduct() {
                if (!this.newProductName || this.newProductPrice === '' || this.newProductStock === '') {
                    this.notify('Todos los campos son obligatorios.', false);
                    return;
                }
                const newId = this.products.length ? Math.max(...this.products.map(p => p.id)) + 1 : 1;
                const newProduct = {
                    id: newId,
                    name: this.newProductName,
                    price: parseFloat(this.newProductPrice),
                    stock: parseInt(this.newProductStock),
                    image: `https://placehold.co/150x150/F1D2B9/000?text=${this.newProductName.split(' ')[0]}`
                };
                this.products.push(newProduct);
                this.notify('Producto agregado con éxito!');
                this.showAddProductModal = false;
                this.newProductName = '';
                this.newProductPrice = '';
                this.newProductStock = '';
            },

            saveNewClient() {
                if (this.newClientName.trim() === '') {
                    this.notify('El nombre del cliente no puede estar vacío.', false);
                    return;
                }
                const newId = this.clients.length ? Math.max(...this.clients.map(c => c.id)) + 1 : 1;
                const newClient = {
                    id: newId,
                    name: this.newClientName.trim(),
                    phone: this.newClientPhone.trim()
                };
                this.clients.push(newClient);
                this.selectedClientId = newId;
                this.notify('Cliente guardado y seleccionado!');
                this.showAddClientModal = false;
                this.newClientName = '';
                this.newClientPhone = '';
            },

            finalizeSale() {
                if (this.cart.length === 0) {
                    this.notify('El carrito está vacío.', false);
                    return;
                }
                
                this.cart.forEach(cartItem => {
                    const product = this.products.find(p => p.id === cartItem.id);
                    if (product) {
                        product.stock -= cartItem.quantity;
                    }
                });
                
                
                console.log('Venta finalizada:', this.cart, 'Total:', this.total, 'Cliente:', this.selectedClientId);

                this.notify('Venta finalizada con éxito!');
                this.resetState();
            },

            createApartado() {
                if (this.cart.length === 0) {
                    this.notify('El carrito está vacío.', false);
                    return;
                }
                const selectedClient = this.clients.find(c => c.id === this.selectedClientId);
                if (!selectedClient || selectedClient.id === 1) {
                    this.notify('Debes seleccionar un cliente válido para crear un apartado.', false);
                    return;
                }
                
                const newApartado = {
                    client: selectedClient.name,
                    phone: selectedClient.phone,
                    amount: this.total,
                    due_date: new Date().toISOString().slice(0, 10),
                    items: this.cart.map(item => ({ name: item.name, quantity: item.quantity, price: item.price }))
                };
                this.apartados.push(newApartado);
                
                this.notify('Apartado creado con éxito!');
                this.resetState();
            },

            printReceipt() {
                if (this.cart.length === 0) {
                    this.notify('No hay productos en el carrito para imprimir un recibo.', false);
                    return;
                }

                const date = new Date().toLocaleString();
                let receiptContent = `
                    <div class="font-mono text-xs">
                        <h4 class="text-center font-bold text-base mb-2">Recibo de Venta</h4>
                        <p class="text-center mb-4">${date}</p>
                        <hr class="border-t border-dashed border-gray-400 my-2">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="text-left font-semibold">Producto</th>
                                    <th class="text-right font-semibold">Cant.</th>
                                    <th class="text-right font-semibold">Precio</th>
                                    <th class="text-right font-semibold">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${this.cart.map(item => `
                                    <tr>
                                        <td class="text-left py-1">${item.name}</td>
                                        <td class="text-right py-1">${item.quantity}</td>
                                        <td class="text-right py-1">$${item.price.toLocaleString()}</td>
                                        <td class="text-right py-1">$${(item.price * item.quantity).toLocaleString()}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                        <hr class="border-t border-dashed border-gray-400 my-2">
                        <div class="flex justify-between font-bold text-sm mt-2">
                            <span>TOTAL:</span>
                            <span>$${this.total.toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span>Monto Recibido:</span>
                            <span>$${(this.montoRecibido || 0).toLocaleString()}</span>
                        </div>
                        <div class="flex justify-between font-bold text-sm mt-1">
                            <span>Cambio:</span>
                            <span>${this.cambioText.includes('Faltan') ? this.cambioText : `$${(this.montoRecibido - this.total).toLocaleString()}`}</span>
                        </div>
                        <p class="text-center mt-4">¡Gracias por su compra!</p>
                    </div>
                `;
                showModal('Recibo', receiptContent);
            },
            
            resetState() {
                this.cart = [];
                this.montoRecibido = null;
                this.selectedClientId = 1;
                this.search = '';
                this.calculateChange();
            },
            
            notify(message, success = true) {
                this.notification = { message, success };
                this.showNotification = true;
                setTimeout(() => this.showNotification = false, 3000);
            }
        }
    }
</script>

</body>
</html>
