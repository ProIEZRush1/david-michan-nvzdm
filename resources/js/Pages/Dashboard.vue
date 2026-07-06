<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    stats: { type: Object, required: true },
});

const page = usePage();

const businessName = computed(() => page.props.name ?? 'David Michan');
const userFirstName = computed(() => {
    const name = (page.props.auth?.user?.name ?? '').trim();
    return name ? name.split(/\s+/)[0] : '';
});

const cards = computed(() => [
    {
        label: 'Planes activos',
        value: props.stats.planes_activos,
        hint: 'Visibles ahora mismo en el bot',
        gradient: 'from-[#1d4ed8] to-[#3b82f6]',
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        href: route('planes.index'),
    },
    {
        label: 'Pedidos totales',
        value: props.stats.pedidos_total,
        hint: `${props.stats.pedidos_pendientes} por confirmar o cerrar`,
        gradient: 'from-[#0369a1] to-[#0891b2]',
        icon: 'M9 2a1 1 0 00-1 1v1H6a2 2 0 00-2 2v13a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2V3a1 1 0 00-1-1H9zM8 12h8m-8 4h5',
        href: route('pedidos.index'),
    },
    {
        label: 'Clientes',
        value: props.stats.clientes_total,
        hint: 'Contactos que ya compraron',
        gradient: 'from-[#0891b2] to-[#06b6d4]',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        href: route('clientes.index'),
    },
    {
        label: 'Números disponibles',
        value: props.stats.numeros_disponibles,
        hint: `${props.stats.numeros_asignados} ya asignados`,
        gradient: 'from-[#1e40af] to-[#0ea5e9]',
        icon: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
        href: route('numeros.index'),
    },
]);
</script>

<template>
    <Head title="Inicio" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold tracking-tight text-slate-800">
                Panel de control
            </h2>
        </template>

        <div class="mx-auto max-w-7xl space-y-8">
            <!-- Hero -->
            <section
                class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#1d4ed8] to-[#0891b2] p-8 text-white shadow-xl shadow-blue-500/20 sm:p-10"
            >
                <div
                    class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl"
                ></div>
                <div
                    class="pointer-events-none absolute -bottom-20 -left-10 h-56 w-56 rounded-full bg-cyan-300/20 blur-2xl"
                ></div>
                <div class="relative">
                    <p class="text-sm font-medium uppercase tracking-widest text-white/70">
                        Bienvenido a tu sistema de venta de líneas
                    </p>
                    <h1 class="mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">
                        Hola<span v-if="userFirstName">, {{ userFirstName }}</span> 👋
                    </h1>
                    <p class="mt-3 max-w-2xl text-base text-white/85">
                        Este es el panel de
                        <span class="font-semibold">{{ businessName }}</span>.
                        Tu bot de WhatsApp vende líneas telefónicas las 24 horas: aquí
                        administras planes, inventario de números, pedidos y clientes.
                    </p>
                </div>
            </section>

            <!-- Stat cards -->
            <section>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    <Link
                        v-for="card in cards"
                        :key="card.label"
                        :href="card.href"
                        class="group rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg"
                    >
                        <div class="flex items-start justify-between">
                            <span
                                :class="[
                                    'flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br text-white shadow-md',
                                    card.gradient,
                                ]"
                            >
                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        :d="card.icon"
                                    />
                                </svg>
                            </span>
                        </div>
                        <p class="mt-4 text-3xl font-extrabold text-slate-800">
                            {{ card.value }}
                        </p>
                        <p class="mt-1 text-sm font-semibold text-slate-600">
                            {{ card.label }}
                        </p>
                        <p class="mt-0.5 text-xs text-slate-400">{{ card.hint }}</p>
                        <p class="mt-3 text-xs font-semibold text-[#1d4ed8] opacity-0 transition group-hover:opacity-100">
                            Ver / Administrar →
                        </p>
                    </Link>
                </div>
            </section>

            <!-- Bot status / next steps -->
            <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm lg:col-span-2"
                >
                    <h3 class="text-lg font-bold text-slate-800">
                        Tu bot de WhatsApp está listo para vender
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">
                        En cuanto un cliente escribe, el bot de
                        <span class="font-semibold text-slate-800">{{ businessName }}</span>
                        muestra los planes, responde preguntas frecuentes (cobertura, precios,
                        portabilidad y activación), captura los datos del cliente y asigna
                        automáticamente un número disponible al confirmar la venta.
                    </p>
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <Link :href="route('conectar')" class="rounded-xl bg-slate-50 p-4 transition hover:bg-slate-100">
                            <p class="text-sm font-semibold text-slate-700">
                                Vincula tu WhatsApp
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Escanea el código QR para activar el bot.
                            </p>
                        </Link>
                        <Link :href="route('numeros.index')" class="rounded-xl bg-slate-50 p-4 transition hover:bg-slate-100">
                            <p class="text-sm font-semibold text-slate-700">
                                Carga tu inventario de números
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Así el bot siempre tiene números disponibles para asignar.
                            </p>
                        </Link>
                    </div>
                </div>

                <div
                    class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-800 p-8 text-white shadow-sm"
                >
                    <div>
                        <h3 class="text-lg font-bold">¿Necesitas ayuda?</h3>
                        <p class="mt-2 text-sm text-slate-300">
                            Estamos para acompañarte. Cualquier ajuste o nueva función
                            que necesites, lo resolvemos por ti.
                        </p>
                    </div>
                    <p class="mt-6 text-xs text-slate-400">
                        Plataforma impulsada por
                        <span class="font-semibold text-slate-200">Overcloud</span>
                    </p>
                </div>
            </section>
        </div>
    </AuthenticatedLayout>
</template>
