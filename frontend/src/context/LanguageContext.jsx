import React, { createContext, useState, useContext, useEffect } from 'react';
import { useTranslation } from 'react-i18next';

const LanguageContext = createContext();

export const LanguageProvider = ({ children }) => {
    const { t, i18n } = useTranslation();
    const [lang, setLang] = useState(() => {
        return localStorage.getItem('medlex_lang') || 'en';
    });

    useEffect(() => {
        i18n.changeLanguage(lang);
        localStorage.setItem('medlex_lang', lang);
    }, [lang, i18n]);

    return (
        <LanguageContext.Provider value={{ lang, setLang, t }}>
            {children}
        </LanguageContext.Provider>
    );
};

export const useLanguage = () => useContext(LanguageContext);
