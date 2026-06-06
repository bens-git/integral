import "vuetify/styles";
import { createVuetify } from "vuetify";
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

// Custom INTEGRAL theme
const lightTheme = {
  dark: false,
  colors: {
    primary: '#0B6E4F',        // deep green
    'primary-darken-1': '#08593d',
    secondary: '#111827',      // slate-900
    accent: '#FFD166',         // warm yellow
    info: '#0EA5E9',
    success: '#10B981',
    warning: '#F59E0B',
    error: '#EF4444',

    // extra semantic tokens used by site styles
    'primary-faint': '#E6F4EE',
    'primary-dim': '#4B5563',
    bg: '#FFFFFF',
    bg2: '#F3F4F6',
    border: '#E5E7EB',
    'text-dim': '#6B7280',
  },
};

const darkTheme = {
  dark: true,
  colors: {
    primary: '#66D2A7',
    'primary-darken-1': '#4FB98F',
    secondary: '#0F1724',
    accent: '#FFD166',
    info: '#7DD3FC',
    success: '#34D399',
    warning: '#F59E0B',
    error: '#FB7185',

    'primary-faint': '#072118',
    'primary-dim': '#9CA3AF',
    bg: '#0B1220',
    bg2: '#0F1720',
    border: '#1F2937',
    'text-dim': '#9CA3AF',
  },
};

export default createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'light',
    themes: {
      light: lightTheme,
      dark: darkTheme,
    },
  },
});
