import axios from 'axios';

// API base configuration
const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:3001/api';

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor to add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error.response?.status === 401) {
      // Unauthorized - redirect to login
      localStorage.removeItem('token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;

// API endpoints
export const API_ENDPOINTS = {
  // Auth
  LOGIN: '/auth/login',
  REGISTER: '/auth/register',
  REFRESH: '/auth/refresh',
  LOGOUT: '/auth/logout',
  
  // Dashboard
  DASHBOARD: '/dashboard',
  
  // Applicants
  APPLICANTS: '/applicants',
  APPLICANT_REGISTRATION: '/applicants/register',
  APPLICANT_PROFILE: (id: string) => `/applicants/${id}`,
  APPLICANT_CV_UPLOAD: (id: string) => `/applicants/${id}/cv`,
  
  // Jobs
  JOBS: '/jobs',
  JOB_DETAIL: (id: string) => `/jobs/${id}`,
  JOB_APPLICATIONS: (id: string) => `/jobs/${id}/applications`,
  
  // Applications
  APPLICATIONS: '/applications',
  APPLICATION_DETAIL: (id: string) => `/applications/${id}`,
  APPLICATION_UPDATE_STATUS: (id: string) => `/applications/${id}/status`,
  
  // Placements
  PLACEMENTS: '/placements',
  PLACEMENT_DETAIL: (id: string) => `/placements/${id}`,
  PLACEMENT_EXPIRING: '/placements/expiring',
  
  // Companies
  COMPANIES: '/companies',
  COMPANY_DETAIL: (id: string) => `/companies/${id}`,
  
  // Agents
  AGENTS: '/agents',
  AGENT_DETAIL: (id: string) => `/agents/${id}`,
  AGENT_LEADERBOARD: '/agents/leaderboard',
  
  // WhatsApp
  WHATSAPP_LOGS: '/whatsapp/logs',
  WHATSAPP_BROADCAST: '/whatsapp/broadcast',
  WHATSAPP_SEND: '/whatsapp/send',
  
  // Analytics
  ANALYTICS: '/analytics',
  ANALYTICS_REPORTS: '/analytics/reports',
  
  // Settings
  SETTINGS_USERS: '/settings/users',
  SETTINGS_SYSTEM: '/settings/system',
};

// Common API response types
export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data: T;
  pagination?: {
    page: number;
    limit: number;
    total: number;
    total_pages: number;
  };
}

export interface ApiError {
  success: false;
  message: string;
  errors?: Record<string, string[]>;
}

// Helper functions for API calls
export const apiGet = async <T = any>(url: string, params?: any): Promise<ApiResponse<T>> => {
  const response = await apiClient.get(url, { params });
  return response.data;
};

export const apiPost = async <T = any>(url: string, data?: any): Promise<ApiResponse<T>> => {
  const response = await apiClient.post(url, data);
  return response.data;
};

export const apiPut = async <T = any>(url: string, data?: any): Promise<ApiResponse<T>> => {
  const response = await apiClient.put(url, data);
  return response.data;
};

export const apiDelete = async <T = any>(url: string): Promise<ApiResponse<T>> => {
  const response = await apiClient.delete(url);
  return response.data;
};

export const apiUpload = async <T = any>(url: string, formData: FormData): Promise<ApiResponse<T>> => {
  const response = await apiClient.post(url, formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });
  return response.data;
};
