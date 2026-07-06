<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre: '',
    precio: '',
    descripcion: '',
    activo: true,
    orden: 0,
});

function submit() {
    form.post(route('planes.store'));
}
</script>

<template>
    <Head title="Nuevo plan" />

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-xl font-bold text-slate-800">Nuevo plan</h1>
        </template>

        <div class="mx-auto max-w-xl">
            <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div>
                    <InputLabel for="nombre" value="Nombre del plan" />
                    <TextInput id="nombre" v-model="form.nombre" class="mt-1 block w-full" placeholder="Ej. Ilimitado 20GB" required autofocus />
                    <InputError class="mt-2" :message="form.errors.nombre" />
                </div>

                <div>
                    <InputLabel for="precio" value="Precio (MXN)" />
                    <TextInput id="precio" v-model="form.precio" type="number" step="0.01" min="0" class="mt-1 block w-full" placeholder="299.00" required />
                    <InputError class="mt-2" :message="form.errors.precio" />
                </div>

                <div>
                    <InputLabel for="descripcion" value="Descripción" />
                    <textarea
                        id="descripcion"
                        v-model="form.descripcion"
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#1d4ed8] focus:ring-[#1d4ed8]"
                        placeholder="Ej. 20GB de datos, llamadas y SMS ilimitados"
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.descripcion" />
                </div>

                <div>
                    <InputLabel for="orden" value="Orden de aparición" />
                    <TextInput id="orden" v-model="form.orden" type="number" min="0" class="mt-1 block w-full" />
                    <InputError class="mt-2" :message="form.errors.orden" />
                </div>

                <label class="flex items-center gap-2">
                    <input type="checkbox" v-model="form.activo" class="rounded border-gray-300 text-[#1d4ed8] focus:ring-[#1d4ed8]" />
                    <span class="text-sm text-slate-700">Plan activo (visible en el bot de WhatsApp)</span>
                </label>

                <div class="flex items-center gap-3 pt-2">
                    <PrimaryButton :disabled="form.processing">Guardar plan</PrimaryButton>
                    <Link :href="route('planes.index')"><SecondaryButton type="button">Cancelar</SecondaryButton></Link>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
