export interface InAppNotification {
    id: string;
    type: string;
    data: {
        title?: string;
        message?: string;
        action_url?: string;
        booking_id?: string;
        booking_code?: string;
        listing_name?: string;
        status?: string;
    };
    read_at: string | null;
    created_at: string;
}

export type NotificationItem = InAppNotification;

