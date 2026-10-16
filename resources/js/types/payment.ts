import { Booking } from './booking';

export type PaymentStatusType = 'pending' | 'processing' | 'paid' | 'failed' | 'expired' | 'refund_pending' | 'refunded';

export type PaymentReviewStatusType = 'pending' | 'contacting' | 'verified' | 'rejected' | 'approved';

export type PayoutStatusType = 'pending' | 'processing' | 'paid' | 'failed' | 'cancelled';

export interface PaymentReview {
    id: string;
    payment_id: string;
    booking_id: string;
    status: PaymentReviewStatusType;
    tenant_contacted: boolean;
    owner_contacted: boolean;
    tenant_confirmed: boolean;
    owner_confirmed: boolean;
    admin_id: number | null;
    admin_notes: string | null;
    reviewed_at: string | null;
    approved_at: string | null;
    rejected_at: string | null;
    rejection_reason: string | null;
    created_at: string;
    updated_at: string;
    admin?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

export interface Payout {
    id: string;
    payment_id: string;
    booking_id: string;
    owner_id: number;
    gross_amount: number;
    formatted_gross_amount: string;
    platform_fee: number;
    formatted_platform_fee: string;
    net_amount: number;
    formatted_net_amount: string;
    payout_provider: string;
    provider_payout_id: string | null;
    status: PayoutStatusType;
    idempotency_key: string;
    requested_at: string | null;
    completed_at: string | null;
    failure_reason: string | null;
    created_at: string;
    updated_at: string;
    owner?: {
        id: number;
        name: string;
        email: string;
        phone?: string | null;
    } | null;
    booking?: Booking;
    payment?: PaymentDetail;
}

export interface PaymentAudit {
    id: string;
    payment_id: string;
    actor_id: number | null;
    action: string;
    old_status: string | null;
    new_status: string | null;
    metadata: Record<string, unknown> | null;
    created_at: string;
    actor?: {
        id: number;
        name: string;
        email: string;
    } | null;
}

export interface PaymentDetail {
    id: string;
    booking_id: string;
    invoice_number: string;
    payment_provider: string;
    payment_channel: string;
    payment_reference: string | null;
    provider_transaction_id: string | null;
    gross_amount: number;
    formatted_gross_amount: string;
    transaction_status: PaymentStatusType;
    payload: Record<string, unknown> | null;
    provider_payload: Record<string, unknown> | null;
    idempotency_key: string | null;
    paid_at: string | null;
    verified_at: string | null;
    created_at: string;
    updated_at: string;
    booking?: Booking;
    review?: PaymentReview | null;
    payout?: Payout | null;
    audits?: PaymentAudit[];
}
