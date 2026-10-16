import { PageProps } from '@inertiajs/core';

export interface AuthUser {
    id: number | string;
    name: string;
    email: string;
    phone?: string | null;
    avatar_url?: string | null;
    roles: string[];
    is_owner: boolean;
    is_tenant: boolean;
    is_admin: boolean;
    owner_verification_status?: string | null;
    email_verified?: boolean;
    is_verified_owner?: boolean;
}

export interface SharedProps extends PageProps {
    auth: {
        user: AuthUser | null;
    };
    notifications: {
        unread_count: number;
        items: Array<{
            id: string;
            type: string;
            data: {
                title?: string;
                message?: string;
                action_url?: string;
                booking_code?: string;
            };
            read_at: string | null;
            created_at: string;
        }>;
    };
    flash: {
        success?: string | null;
        error?: string | null;
    };
}
