import api from './api';

export interface Agent {
  id: number;
  agent_code: string;
  referral_code: string;
  level: string;
  total_referrals: number;
  successful_placements: number;
  success_rate: string;
  total_points: number;
  total_commission: string;
  qr_code_url: string;
  user: {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string;
    phone: string;
  };
}

export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
}

export class AgentService {
  static async getAllAgents(): Promise<Agent[]> {
    try {
      const response = await api.get<ApiResponse<Agent[]>>('/agents');
      
      if (response.data.success) {
        return response.data.data;
      } else {
        throw new Error(response.data.message || 'Failed to fetch agents');
      }
    } catch (error: any) {
      console.error('Error fetching agents:', error);
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('Network error. Please check your connection.');
    }
  }

  static async getAgentByReferralCode(referralCode: string): Promise<Agent | null> {
    try {
      const response = await api.get<ApiResponse<Agent>>(`/agents/referral/${referralCode}`);
      
      if (response.data.success) {
        return response.data.data;
      } else {
        return null;
      }
    } catch (error: any) {
      console.error('Error fetching agent by referral code:', error);
      return null;
    }
  }
}
