<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useFlashToast } from '@/lib/flash';
import { confirmDelete } from '@/lib/confirm';

const props = defineProps({
    pedidos: { type: Array, default: () => [] },
    buscar: { type: String, default: '' },
    estado: { type: String, default: '' },
});

useFlashToast();

const estados = [
    { value: '', label: 'Todos' },
    { value: 'nuevo', label: 'Nuevo' },
    { value: 'confirmado', label: 'Confirmado' },
    { value: 'numero_asignado', label: 'Número asignado' },
    { value: 'entregado', label: 'Entregado' },
    { value: 'cancelado', label: 'Cancelado' },
];

const badgeClass = {
    nuevo: 'bg-slate-100 text-slate-600',
    confirmado: 'bg-amber-100 text-amber-700',
    numero_asignado: 'bg-blue-100 text-blue-700',
    entregado: 'bg-emerald-100 text-emerald-700',
    cancelado: 'bg-red-100 text-red-700',
};

const badgeLabel = {
    nuevo: 'Nuevo',
    confirmado: 'Confirmado',
    numero_asignado: 'Número asignado',
    entregado: 'Entregado',
    cancelado: 'Cancelado',
};

function aplicarFiltro(overrides = {}) {
    router.get(
        route('pedidos.index'),
        { buscar: props.buscar, estado: props.estado, ...overrides },
        { preserveState: true, replace: true },
    );
}

async function eliminar(pedido) {
    const confirmado = await confirmDelete(`Se eliminará el pedido de "${pedido.cliente}".`);
    if (confirmado) {
        router.delete(route('pedidos.destroy', pedido.id));
    }
}
</script>

<template>
    <Head title="Pedidos" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Pedidos</h1>
        </template>

        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-1 flex-col gap-3 sm:flex-row">
                    <input
                        type="text"
                        :value="buscar"
                        @input="aplicarFiltro({ buscar: $event.target.value })"
                        placeholder="Buscar por cliente o teléfono…"
                        class="w-full max-w-sm rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                    />
                    <select
                        :value="estado"
                        @change="aplicarFiltro({ estado: $event.target.value })"
                        class="rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                    >
                        <option v-for="opcion in estados" :key="opcion.value" :value="opcion.value">{{ opcion.label }}</option>
                    </select>
                </div>
                <Link
                    :href="route('pedidos.create')"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#1d4ed8] to-[#0891b2] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/20 transition hover:opacity-90"
                >
                    + Nuevo pedido
                </Link>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Cliente</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Plan</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Número</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Estado</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Fecha</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="pedido in pedidos" :key="pedido.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800">{{ pedido.cliente }}</p>
                                <p class="text-xs text-slate-500">{{ pedido.telefono }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ pedido.plan || '—' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ pedido.numero || '—' }}</td>
                            <td class="px-5 py-4">
                                <span :class="badgeClass[pedido.estado]" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                                    {{ badgeLabel[pedido.estado] ?? pedido.estado }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-500">{{ pedido.created_at }}</td>
                            <td class="px-5 py-4 text-right">
                                <Link
                                    :href="route('pedidos.edit', pedido.id)"
                                    class="mr-3 font-semibold text-[#1d4ed8] hover:text-[#0891b2]"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="eliminar(pedido)"
                                    class="font-semibold text-red-600 hover:text-red-700"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="pedidos.length === 0">
                            <td colspan="6" class="px-5 py-10 text-center text-slate-400">
                                No hay pedidos con ese filtro.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
