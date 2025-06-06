import api from './api';
import Cookies from 'js-cookie';

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface User {
  id: number;
  first_name: string;
  last_name: string;
  full_name: string;
  email: string;
  phone: string;
  role: string;
  status: string;
  profile_picture?: string | null;
  last_login_at?: string;
  created_at?: string;
  // Additional data for different roles
  admin_stats?: any;
  agent?: any;
  applicant?: any;
}

export interface LoginResponseData {
  user: User;
  token: string;
  token_type: string;
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}

export class AuthService {
  static async login(credentials: LoginCredentials): Promise<LoginResponseData> {
    try {
      console.log('Attempting login with:', credentials);
      
      const response = await api.post<ApiResponse<LoginResponseData>>('/auth/login', credentials);
      
      console.log('Login response:', response.data);
      
      if (response.data.success && response.data.data) {
        const { user, token, token_type } = response.data.data;
        
        console.log('Storing user:', user);
        console.log('Storing token:', token);
        
        // Store token and user in cookies
        Cookies.set('token', token, { expires: 7, path: '/' }); // 7 days
        Cookies.set('user', JSON.stringify(user), { expires: 7, path: '/' });
        
        console.log('Cookies set successfully');
        
        return response.data.data;
      } else {
        console.error('Login failed:', response.data.message);
        throw new Error(response.data.message || 'Login failed');
      }
    } catch (error: any) {
      console.error('Login error:', error);
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('Network error. Please check your connection.');
    }
  }

  static async logout(): Promise<void> {
    try {
      await api.post('/auth/logout');
    } catch (error) {
      // Even if logout fails, we should clear cookies
      console.error('Logout error:', error);
    } finally {
      Cookies.remove('token', { path: '/' });
      Cookies.remove('user', { path: '/' });
    }
  }

  static getCurrentUser(): User | null {
    try {
      const userStr = Cookies.get('user');
      console.log('Getting user from cookies:', userStr);
      return userStr ? JSON.parse(userStr) : null;
    } catch (error) {
      console.error('Error parsing user from cookies:', error);
      return null;
    }
  }

  static getToken(): string | undefined {
    const token = Cookies.get('token');
    console.log('Getting token from cookies:', token);
    return token;
  }

  static isAuthenticated(): boolean {
    const token = this.getToken();
    const user = this.getCurrentUser();
    const authenticated = !!(token && user);
    console.log('Is authenticated:', authenticated, { token: !!token, user: !!user });
    return authenticated;
  }
}
