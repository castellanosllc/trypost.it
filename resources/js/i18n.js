import { ref, onMounted, watchEffect } from "vue";
import dayjs from "@/dayjs";
import { loadLanguageAsync } from "laravel-vue-i18n";

export function useI18n() {
    const currentLocale = ref("en");

    const localeMap = {
        en: "en", // English
        es: "es", // Spanish
        pt: "pt", // Portuguese
    };

    const updateLocale = async () => {
        const browserLang =
            navigator.language || navigator.userLanguage || "en";
        const matchedLocale = Object.keys(localeMap).find((key) =>
            browserLang.toLowerCase().includes(key)
        );

        currentLocale.value = localeMap[matchedLocale] || "en"; // Fallback to English

        // Update Day.js locale
        dayjs.locale(currentLocale.value);

        // Dynamically load translations
        await loadLanguageAsync(currentLocale.value);
    };

    onMounted(() => {
        updateLocale();
    });

    return {
        currentLocale,
        setLocale: updateLocale,
    };
}
