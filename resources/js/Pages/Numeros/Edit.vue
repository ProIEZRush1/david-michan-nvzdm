<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    numero: { type: Object, required: true },
});

const form = useForm({
    numero: props.numero.numero,
    estado: props.numero.estado,
});

function submit() {
    form.put(route('numeros.update', props.numero.id));
}
</script>

<template>
    <Head title="Editar número" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Editar número</h1>
        </template>

        <div class="mx-auto max-w-xl">
            <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div>
                    <InputLabel for="numero" value="Número de teléfono" />
                    <TextInput id="numero" v-model="form.numero" class="mt-1 block w-full" required autofocus />
                    <InputError class="mt-2" :message="form.errors.numero" />
                </div>

                <div>
                    <InputLabel for="estado" value="Estado" />
                    <select
                        id="estado"
                        v-model="form.estado"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                    >
                        <option value="disponible">Disponible</option>
                        <option value="asignado">Asignado</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.estado" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <PrimaryButton :disabled="form.processing">Guardar cambios</PrimaryButton>
                    <Link :href="route('numeros.index')"><SecondaryButton type="button">Cancelar</SecondaryButton></Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
