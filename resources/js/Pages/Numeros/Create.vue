<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    numero: '',
});

function submit() {
    form.post(route('numeros.store'));
}
</script>

<template>
    <Head title="Agregar número" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Agregar número al inventario</h1>
        </template>

        <div class="mx-auto max-w-xl">
            <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div>
                    <InputLabel for="numero" value="Número de teléfono" />
                    <TextInput id="numero" v-model="form.numero" class="mt-1 block w-full" placeholder="+52 55 1234 5678" required autofocus />
                    <InputError class="mt-2" :message="form.errors.numero" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <PrimaryButton :disabled="form.processing">Agregar número</PrimaryButton>
                    <Link :href="route('numeros.index')"><SecondaryButton type="button">Cancelar</SecondaryButton></Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
