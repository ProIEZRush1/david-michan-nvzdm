import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

/** Shows a success toast whenever the shared Inertia flash message changes. */
export function useFlashToast() {
    const page = usePage();

    watch(
        () => page.props.flash?.success,
        (message) => {
            if (!message) return;
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
            });
        },
        { immediate: true },
    );
}
