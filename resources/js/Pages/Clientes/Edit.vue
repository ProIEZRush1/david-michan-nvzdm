<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    cliente: { type: Object, required: true },
});

const form = useForm({
    nombre: props.cliente.nombre,
    telefono: props.cliente.telefono,
});

function submit() {
    form.put(route('clientes.update', props.cliente.id));
}
</script>

<template>
    <Head title="Editar cliente" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Editar cliente</h1>
        </template>

        <div class="mx-auto max-w-xl">
            <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div>
                    <InputLabel for="nombre" value="Nombre" />
                    <TextInput id="nombre" v-model="form.nombre" class="mt-1 block w-full" autofocus />
                    <InputError class="mt-2" :message="form.errors.nombre" />
                </div>

                <div>
                    <InputLabel for="telefono" value="Teléfono (WhatsApp)" />
                    <TextInput id="telefono" v-model="form.telefono" class="mt-1 block w-full" required />
                    <InputError class="mt-2" :message="form.errors.telefono" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <PrimaryButton :disabled="form.processing">Guardar cambios</PrimaryButton>
                    <Link :href="route('clientes.index')"><SecondaryButton type="button">Cancelar</SecondaryButton></Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
