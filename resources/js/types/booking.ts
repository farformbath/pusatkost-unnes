import { Listing, Room } from './listing';

export type BookingStatus = 'pending' | 'approved' | 'rejected' | 'cancelled' | 'expired' | 'completed';

export type PaymentStatus = 'pending' | 'paid' | 'failed' | 'expired' | 'cancelled';

export interface Payment {
    id: string;
    booking_id: string;
    invoice_number: string;
    payment_channel: string;
    payment_reference: string | null;
    gross_amount: number;
    formatted_gross_amount: string;
    transaction_status: string;
    payload: Record<string, unknown> | null;
    paid_at: string | null;
    created_at: string;
}

export interface Booking {
    id: string;
    booking_code: string;
    user_id: number;
    listing_id: string;
    room_id: string;
    tenant_name: string;
    tenant_phone: string;
    start_date: string;
    duration_months: number;
    end_date: string;
    total_amount: number;
    formatted_total_amount: string;
    deposit_amount: number;
    formatted_deposit_amount: string;
    grand_total: number;
    formatted_grand_total: string;
    status: BookingStatus;
    payment_status: PaymentStatus;
    notes: string | null;
    expired_at: string;
    paid_at?: string | null;
    created_at: string;
    updated_at: string;
    listing?: Listing;
    room?: Room;
    payment?: Payment | null;
    payments?: Payment[];
    latest_payment?: Payment | null;
    user?: {
        id: number;
        name: string;
        email: string;
    };
}
