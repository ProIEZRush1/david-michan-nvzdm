import Swal from 'sweetalert2';

/** Branded confirm dialog replacing the native `confirm()` for destructive actions. */
export async function confirmDelete(text = 'Esta acción no se puede deshacer.') {
    const result = await Swal.fire({
        title: '¿Eliminar este registro?',
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
    });

    return result.isConfirmed;
}
