import { GenderType } from './listing';

export interface ListingSummary {
    id: string;
    name: string;
    slug: string;
    area: string;
    gender: GenderType | string;
    starting_price: number;
    primary_image_url: string | null;
}

export interface NesaMessage {
    id: string;
    role: 'user' | 'assistant';
    content: string;
    created_at?: string;
    recommended_listings?: ListingSummary[];
}

export interface NesaChatResponse {
    reply: string;
    session_token: string;
    recommended_listings: ListingSummary[];
}

export interface NesaHistoryResponse {
    messages: Array<{
        id: string;
        role: 'user' | 'assistant';
        content: string;
        created_at: string;
    }>;
    recommended_listings: ListingSummary[];
}
