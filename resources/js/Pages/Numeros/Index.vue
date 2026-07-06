<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useFlashToast } from '@/lib/flash';
import { confirmDelete } from '@/lib/confirm';

defineProps({
    numeros: { type: Array, default: () => [] },
    buscar: { type: String, default: '' },
    disponibles: { type: Number, default: 0 },
});

useFlashToast();

function aplicarBusqueda(value) {
    router.get(route('numeros.index'), { buscar: value }, { preserveState: true, replace: true });
}

async function eliminar(numero) {
    const confirmado = await confirmDelete(`Se eliminará el número ${numero.numero} del inventario.`);
    if (confirmado) {
        router.delete(route('numeros.destroy', numero.id));
    }
}
</script>

<template>
    <Head title="Inventario de números" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Inventario de números</h1>
        </template>

        <div class="mx-auto max-w-6xl space-y-6">
            <div class="rounded-2xl bg-gradient-to-r from-[#1d4ed8] to-[#0891b2] p-5 text-white shadow-sm">
                <p class="text-sm font-medium uppercase tracking-widest text-white/70">Disponibles ahora</p>
                <p class="mt-1 text-3xl font-extrabold">{{ disponibles }}</p>
                <p class="mt-1 text-sm text-white/80">Números listos para asignarse automáticamente cuando el bot cierre una venta.</p>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <input
                    type="text"
                    :value="buscar"
                    @input="aplicarBusqueda($event.target.value)"
                    placeholder="Buscar número…"
                    class="w-full max-w-sm rounded-lg border-slate-300 text-sm shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                />
                <Link
                    :href="route('numeros.create')"
                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#1d4ed8] to-[#0891b2] px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/20 transition hover:opacity-90"
                >
                    + Agregar número
                </Link>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Número</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Estado</th>
                            <th class="px-5 py-3 text-left font-semibold text-slate-600">Asignado a</th>
                            <th class="px-5 py-3 text-right font-semibold text-slate-600">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="numero in numeros" :key="numero.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-800">{{ numero.numero }}</td>
                            <td class="px-5 py-4">
                                <span
                                    :class="numero.estado === 'disponible' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'"
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                >
                                    {{ numero.estado === 'disponible' ? 'Disponible' : 'Asignado' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <span v-if="numero.pedido">{{ numero.pedido.cliente }} — {{ numero.pedido.plan }}</span>
                                <span v-else class="text-slate-400">—</span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <Link
                                    :href="route('numeros.edit', numero.id)"
                                    class="mr-3 font-semibold text-[#1d4ed8] hover:text-[#0891b2]"
                                >
                                    Editar
                                </Link>
                                <button
                                    @click="eliminar(numero)"
                                    class="font-semibold text-red-600 hover:text-red-700"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="numeros.length === 0">
                            <td colspan="4" class="px-5 py-10 text-center text-slate-400">
                                No hay números en el inventario todavía.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
