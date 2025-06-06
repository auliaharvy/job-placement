'use client';

import { useEffect, useState } from 'react';
import { AuthService, User } from '@/lib/auth';

export const useAuth = () => {
  const [user, setUser] = useState<User | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [isAuthenticated, setIsAuthenticated] = useState(false);

  useEffect(() => {
    const checkAuth = () => {
      console.log('Checking auth status...');
      const currentUser = AuthService.getCurrentUser();
      const token = AuthService.getToken();
      
      console.log('Auth check result:', { user: currentUser, token: !!token });
      
      if (currentUser && token) {
        setUser(currentUser);
        setIsAuthenticated(true);
        console.log('User is authenticated');
      } else {
        setUser(null);
        setIsAuthenticated(false);
        console.log('User is not authenticated');
      }
      setIsLoading(false);
    };

    checkAuth();
  }, []);

  const login = async (email: string, password: string) => {
    try {
      console.log('Login attempt started');
      const result = await AuthService.login({ email, password });
      console.log('Login successful, updating state');
      
      setUser(result.user);
      setIsAuthenticated(true);
      
      console.log('State updated successfully');
      return result;
    } catch (error) {
      console.error('Login failed:', error);
      throw error;
    }
  };

  const logout = async () => {
    try {
      console.log('Logout started');
      await AuthService.logout();
      setUser(null);
      setIsAuthenticated(false);
      console.log('Logout completed');
    } catch (error) {
      console.error('Logout error:', error);
    }
  };

  return {
    user,
    isLoading,
    isAuthenticated,
    login,
    logout,
  };
};
