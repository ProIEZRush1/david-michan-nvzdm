<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    pedido: { type: Object, required: true },
    planes: { type: Array, default: () => [] },
});

const form = useForm({
    cliente: props.pedido.cliente,
    telefono: props.pedido.telefono,
    plan_id: props.pedido.plan_id,
    estado: props.pedido.estado,
});

function submit() {
    form.put(route('pedidos.update', props.pedido.id));
}
</script>

<template>
    <Head title="Editar pedido" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Editar pedido</h1>
        </template>

        <div class="mx-auto max-w-xl">
            <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div v-if="pedido.numero" class="rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    📲 Número asignado: <span class="font-semibold">{{ pedido.numero }}</span>
                </div>

                <div>
                    <InputLabel for="cliente" value="Nombre del cliente" />
                    <TextInput id="cliente" v-model="form.cliente" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-2" :message="form.errors.cliente" />
                </div>

                <div>
                    <InputLabel for="telefono" value="Teléfono (WhatsApp)" />
                    <TextInput id="telefono" v-model="form.telefono" class="mt-1 block w-full" required />
                    <InputError class="mt-2" :message="form.errors.telefono" />
                </div>

                <div>
                    <InputLabel for="plan_id" value="Plan" />
                    <select
                        id="plan_id"
                        v-model="form.plan_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                    >
                        <option v-for="plan in planes" :key="plan.id" :value="plan.id">{{ plan.nombre }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.plan_id" />
                </div>

                <div>
                    <InputLabel for="estado" value="Estado" />
                    <select
                        id="estado"
                        v-model="form.estado"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                    >
                        <option value="nuevo">Nuevo</option>
                        <option value="confirmado">Confirmado</option>
                        <option value="numero_asignado">Número asignado</option>
                        <option value="entregado">Entregado</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.estado" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <PrimaryButton :disabled="form.processing">Guardar cambios</PrimaryButton>
                    <Link :href="route('pedidos.index')"><SecondaryButton type="button">Cancelar</SecondaryButton></Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
