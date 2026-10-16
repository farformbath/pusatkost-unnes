export interface ReviewUser {
    id: string | number;
    name: string;
}

export interface Review {
    id: string;
    user_id: string | number;
    listing_id: string;
    booking_id?: string | null;
    rating: number;
    comment: string;
    owner_reply?: string | null;
    owner_replied_at?: string | null;
    created_at: string;
    user?: ReviewUser;
}
