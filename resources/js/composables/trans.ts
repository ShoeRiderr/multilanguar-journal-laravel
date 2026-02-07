import { usePage } from '@inertiajs/vue3';

// Type for nested translation objects
type Translations = {
    [key: string]: string | Translations;
};

// Helper to resolve nested keys like 'sidebar.dashboard'
function getNestedTranslation(obj: Translations, key: string): string | undefined {
    return key.split('.').reduce<any>((acc, part) => (acc && acc[part] !== undefined ? acc[part] : undefined), obj);
}

export function useTrans(value: string, params?: Record<string, any>): string {
    const translations = usePage().props.translations as Translations;
    let result = getNestedTranslation(translations, value);
    if (typeof result === 'string' && params) {
        let translated = result;
        Object.keys(params).forEach(key => {
            translated = translated.replace(new RegExp(`{${key}}`, 'g'), params[key]);
        });
        result = translated;
    }
    return typeof result === 'string' ? result : value;
}