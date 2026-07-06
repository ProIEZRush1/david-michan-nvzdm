<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useFlashToast } from '@/lib/flash';
import { confirmDelete } from '@/lib/confirm';

defineProps({
    planes: { type: Array, default: () => [] },
    buscar: { type: String, default: '' },
});

useFlashToast();

function aplicarBusqueda(value) {
    router.get(route('planes.index'), { buscar: value }, { preserveState: true, replace: true });
}

async function eliminar(plan) {
    const confirmado = await confirmDelete(`Se eliminará el plan "${plan.nombre}".`);
    if (confirmado) {
        router.delete(route('planes.destroy', plan.id));
    }
}
</script>

<template>
    <Head title="Planes" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Planes de línea telefónica</h1>
        </template>

        <div class="mx-auto max-w-6xl space-y-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <input
                    type="text"
                    :value="buscar"
                    @input="aplicarBusqueda($event.target.value)"
                    placeholder="Buscar plan por nombre…"
                    class="w-full max-w-sm rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                />
                <Link
                    :href="route('planes.create')"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#1d4ed8] to-[#0891b2] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/20 transition hover:opacity-90"
                >
                    + Nuevo plan
                </Link>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Plan</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Precio</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Pedidos</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Estado</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="plan in planes" :key="plan.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-800">{{ plan.nombre }}</p>
                                <p class="text-xs text-slate-500">{{ plan.descripcion }}</p>
                            </td>
                            <td class="px-5 py-4 font-medium text-slate-700">{{ plan.precio_formato }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ plan.pedidos_count }}</td>
                            <td class="px-5 py-4">
                                <span
                                    :class="plan.activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                >
                                    {{ plan.activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <Link
                                    :href="route('planes.edit', plan.id)"
                                    class="mr-3 font-semibold text-[#1d4ed8] hover:text-[#0891b2]"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="eliminar(plan)"
                                    class="font-semibold text-red-600 hover:text-red-700"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="planes.length === 0">
                            <td colspan="5" class="px-5 py-10 text-center text-slate-400">
                                No hay planes registrados todavía.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
