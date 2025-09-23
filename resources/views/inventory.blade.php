@extends('layouts.app')

@section('title', 'Inventario de Productos')

@section('content')
    <main class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-lg shadow-xl border">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-700">Productos en Stock</h2>
                <input type="text" x-model="searchInventory" placeholder="Buscar Producto..." class="w-full max-w-sm pl-4 pr-4 py-2 border rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded-lg shadow border">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Producto</th>
                            <th class="py-3 px-6 text-center">Stock</th>
                            <th class="py-3 px-6 text-right">Precio</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light">
                        <template x-for="product in filteredInventory" :key="product.id">
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-6 text-left font-medium" x-text="product.name"></td>
                                <td class="py-3 px-6 text-center" x-text="product.stock"></td>
                                <td class="py-3 px-6 text-right" x-text="`$${product.price.toLocaleString()}`"></td>
                                <td class="py-3 px-6 text-center space-x-2">
                                    <button @click="editProduct(product)" class="text-blue-500 hover:text-blue-700 transition-colors" title="Editar"><i class="fas fa-edit"></i></button>
                                    <button @click="deleteProduct(product.id)" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="col-span-1 bg-white p-6 rounded-lg shadow-xl border h-fit sticky top-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Registrar Nuevo Producto</h2>
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
                <button @click="saveNewProduct()" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors duration-200">Guardar</button>
            </div>
        </div>
    </main>

    <div x-cloak x-show="showNotification" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" class="fixed bottom-5 right-5 p-4 rounded-lg shadow-xl text-white z-50 transform transition-all" :class="notification.success ? 'bg-green-500' : 'bg-red-500'">
        <span x-text="notification.message"></span>
    </div>
@endsection

@section('scripts')
    <script>
        function inventory() {
            return {
                products: [
                    { id: 1, name: 'Laptop Pro', price: 1200000, stock: 10 },
                    { id: 2, name: 'Smartphone Z', price: 800000, stock: 25 },
                    { id: 3, name: 'Auriculares X', price: 50000, stock: 50 },
                    { id: 4, name: 'Teclado Mecánico', price: 90000, stock: 15 },
                    { id: 5, name: 'Mouse Inalámbrico', price: 25000, stock: 30 },
                    { id: 6, name: 'Lavandina Sedile 1L', price: 780, stock: 40 },
                    { id: 7, name: 'Papel Higienico Higienol 4u x 38', price: 1500, stock: 38 },
                    { id: 8, name: 'Jabon Seiseme 500g', price: 1600, stock: 41 },
                    { id: 9, name: 'Esponja Virulana', price: 550, stock: 35 },
                ],
                searchInventory: '',
                newProductName: '',
                newProductPrice: '',
                newProductStock: '',
                showNotification: false,
                notification: { message: '', success: true },

                get filteredInventory() {
                    if (this.searchInventory === '') {
                        return this.products;
                    }
                    const searchTerm = this.searchInventory.toLowerCase();
                    return this.products.filter(p => p.name.toLowerCase().includes(searchTerm));
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
                    };
                    this.products.push(newProduct);
                    this.notify('Producto agregado con éxito!');
                    this.newProductName = '';
                    this.newProductPrice = '';
                    this.newProductStock = '';
                },

                deleteProduct(id) {
                    this.products = this.products.filter(p => p.id !== id);
                    this.notify('Producto eliminado con éxito!');
                },

                editProduct(product) {
                    this.notify(`Editar producto: ${product.name}`, true);
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
