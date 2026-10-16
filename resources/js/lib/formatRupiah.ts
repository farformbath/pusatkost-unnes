/**
 * Format an integer Rupiah value into Indonesian currency string.
 * All monetary values in UNNESKost are stored as integer Rupiah (no float).
 *
 * @example formatRupiah(500000) => "Rp 500.000"
 * @example formatRupiah(1250000) => "Rp 1.250.000"
 */
export function formatRupiah(value: number): string {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    })
        .format(value)
        .replace('IDR', 'Rp');
}
