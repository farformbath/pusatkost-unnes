import { Listing } from './listing';

export interface Wishlist {
    id: string;
    user_id: string | number;
    listing_id: string;
    created_at: string;
    listing?: Listing;
}
