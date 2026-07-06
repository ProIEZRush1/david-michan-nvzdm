<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useFlashToast } from '@/lib/flash';
import { confirmDelete } from '@/lib/confirm';

defineProps({
    clientes: { type: Array, default: () => [] },
    buscar: { type: String, default: '' },
});

useFlashToast();

function aplicarBusqueda(value) {
    router.get(route('clientes.index'), { buscar: value }, { preserveState: true, replace: true });
}

async function eliminar(cliente) {
    const confirmado = await confirmDelete(`Se eliminará al cliente "${cliente.nombre ?? cliente.telefono}".`);
    if (confirmado) {
        router.delete(route('clientes.destroy', cliente.id));
    }
}
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Clientes</h1>
        </template>

        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <input
                    type="text"
                    :value="buscar"
                    @input="aplicarBusqueda($event.target.value)"
                    placeholder="Buscar por nombre o teléfono…"
                    class="w-full max-w-sm rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                />
                <Link
                    :href="route('clientes.create')"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#1d4ed8] to-[#0891b2] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/20 transition hover:opacity-90"
                >
                    + Nuevo cliente
                </Link>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Nombre</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Teléfono</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Pedidos</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="cliente in clientes" :key="cliente.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ cliente.nombre || '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ cliente.telefono }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ cliente.pedidos_count }}</td>
                            <td class="px-5 py-4 text-right">
                                <Link
                                    :href="route('clientes.edit', cliente.id)"
                                    class="mr-3 font-semibold text-[#1d4ed8] hover:text-[#0891b2]"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="eliminar(cliente)"
                                    class="font-semibold text-red-600 hover:text-red-700"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="clientes.length === 0">
                            <td colspan="4" class="px-5 py-10 text-center text-slate-400">
                                No hay clientes registrados todavía.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
