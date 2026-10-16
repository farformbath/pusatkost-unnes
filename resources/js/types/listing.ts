import { Review } from './review';

export type GenderType = 'putra' | 'putri' | 'campur';
export type ListingStatus = 'draft' | 'published' | 'archived';
export type RoomStatus = 'available' | 'occupied' | 'maintenance';

export interface Facility {
    id: string;
    name: string;
    slug: string;
    icon: string | null;
    category: 'room' | 'property' | 'general';
}

export interface ListingImage {
    id: string;
    listing_id: string;
    room_id: string | null;
    file_path: string;
    url: string;
    is_primary: boolean;
    sort_order: number;
}

export interface Room {
    id: string;
    listing_id: string;
    name: string;
    size: string | null;
    price_monthly: number;
    price_deposit: number;
    formatted_price_monthly: string;
    formatted_price_deposit: string;
    status: RoomStatus;
    total_rooms: number;
    available_rooms: number;
    facilities?: Facility[];
    images?: ListingImage[];
}

export interface UserSummary {
    id: number;
    name: string;
    email: string;
    phone?: string | null;
}

export interface Listing {
    id: string;
    owner_id: number;
    name: string;
    slug: string;
    gender_type: GenderType;
    description: string;
    address: string;
    area: string;
    latitude: number | null;
    longitude: number | null;
    rules: string | null;
    status: ListingStatus;
    rating_avg: number;
    review_count: number;
    starting_price: number;
    formatted_starting_price: string;
    primary_image?: ListingImage | null;
    images?: ListingImage[];
    rooms?: Room[];
    facilities?: Facility[];
    reviews?: Review[];
    owner?: UserSummary;
    created_at?: string;
    updated_at?: string;
}

export interface PaginationLinks {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    prev_page_url: string | null;
    next_page_url: string | null;
    links: PaginationLinks[];
}
