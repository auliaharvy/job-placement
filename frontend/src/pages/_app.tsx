import React from 'react';
import type { AppProps } from 'next/app';
import { ConfigProvider } from 'antd';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import idID from 'antd/locale/id_ID';
import 'antd/dist/reset.css';
import '../styles/globals.css';

// Create a client
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 1,
      refetchOnWindowFocus: false,
      staleTime: 5 * 60 * 1000, // 5 minutes
    },
  },
});

// Ant Design theme configuration
const theme = {
  token: {
    colorPrimary: '#1890ff',
    colorSuccess: '#52c41a',
    colorWarning: '#faad14',
    colorError: '#ff4d4f',
    colorInfo: '#1890ff',
    borderRadius: 6,
    fontSize: 14,
  },
  components: {
    Layout: {
      colorBgHeader: '#fff',
      colorBgBody: '#f5f5f5',
    },
    Menu: {
      colorItemBg: 'transparent',
      colorItemTextSelected: '#1890ff',
      colorItemBgSelected: '#e6f7ff',
    },
  },
};

export default function App({ Component, pageProps }: AppProps) {
  return (
    <QueryClientProvider client={queryClient}>
      <ConfigProvider 
        locale={idID}
        theme={theme}
      >
        <Component {...pageProps} />
      </ConfigProvider>
    </QueryClientProvider>
  );
}
