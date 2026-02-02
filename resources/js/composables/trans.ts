import { usePage } from '@inertiajs/vue3';

// Type for nested translation objects
type Translations = {
    [key: string]: string | Translations;
};

// Helper to resolve nested keys like 'sidebar.dashboard'
function getNestedTranslation(obj: Translations, key: string): string | undefined {
    return key.split('.').reduce<any>((acc, part) => (acc && acc[part] !== undefined ? acc[part] : undefined), obj);
}

export function useTrans(value: string): string {
    const translations = usePage().props.translations as Translations;
    const result = getNestedTranslation(translations, value);
    return typeof result === 'string' ? result : value;
}