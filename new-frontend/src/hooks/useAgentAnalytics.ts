'use client';

import { useState, useEffect } from 'react';
import { AgentService } from '@/lib/agent';
import api from '@/lib/api';

export interface LinkClickEvent {
  agentId: string;
  referralCode?: string;
  timestamp: string;
  userAgent: string;
  source: string; // utm_source or 'direct'
  medium: string; // utm_medium or 'direct'
  campaign?: string; // utm_campaign
  ipAddress?: string;
}

export interface AgentLinkAnalytics {
  agent: {
    id: number;
    name: string;
    agent_code: string;
    referral_code: string;
    success_rate: number;
    successful_placements: number;
    total_referrals: number;
  };
  period: {
    start_date: string;
    end_date: string;
    days: number;
  };
  totals: {
    total_clicks: number;
    unique_clicks: number;
    converted_clicks: number;
    conversion_rate: number;
  };
  sources: Record<string, number>;
  mediums: Record<string, number>;
  campaigns: Record<string, number>;
  daily_clicks: Record<string, number>;
  hourly_distribution: Record<string, number>;
  top_user_agents: Record<string, number>;
  conversion_funnel: {
    clicks: number;
    conversions: number;
    placements: number;
    click_to_conversion_rate: number;
    conversion_to_placement_rate: number;
    click_to_placement_rate: number;
  };
}

class AgentAnalyticsService {
  private static STORAGE_KEY = 'agent_link_analytics';
  private static SESSION_KEY = 'agent_session_id';

  static getSessionId(): string {
    let sessionId = sessionStorage.getItem(this.SESSION_KEY);
    if (!sessionId) {
      sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
      sessionStorage.setItem(this.SESSION_KEY, sessionId);
    }
    return sessionId;
  }

  static async trackLinkClick(agentId: string, referralCode?: string): Promise<void> {
    try {
      // Get URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      
      const data = {
        agent_id: agentId,
        referral_code: referralCode,
        utm_source: urlParams.get('utm_source') || 'direct',
        utm_medium: urlParams.get('utm_medium') || 'direct',
        utm_campaign: urlParams.get('utm_campaign') || undefined,
        session_id: this.getSessionId(),
      };

      // Track via API
      await AgentService.trackLinkClick(data);
      
      // Also store locally as backup
      const event: LinkClickEvent = {
        agentId,
        referralCode,
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent,
        source: data.utm_source,
        medium: data.utm_medium,
        campaign: data.utm_campaign,
      };

      const existingData = this.getStoredAnalytics();
      existingData.push(event);
      localStorage.setItem(this.STORAGE_KEY, JSON.stringify(existingData));
      
      console.log('Agent link click tracked:', event);
    } catch (error) {
      console.error('Failed to track link click:', error);
    }
  }

  static async markConversion(agentId: string): Promise<boolean> {
    try {
      const sessionId = this.getSessionId();
      return await AgentService.markConversion(sessionId, agentId);
    } catch (error) {
      console.error('Failed to mark conversion:', error);
      return false;
    }
  }

  static async getAgentAnalytics(agentId: string, filters: any = {}): Promise<AgentLinkAnalytics | null> {
    try {
      const params = new URLSearchParams();
      
      if (filters.start_date) params.append('start_date', filters.start_date);
      if (filters.end_date) params.append('end_date', filters.end_date);
      if (filters.utm_source) params.append('utm_source', filters.utm_source);
      if (filters.utm_medium) params.append('utm_medium', filters.utm_medium);
      if (filters.utm_campaign) params.append('utm_campaign', filters.utm_campaign);

      const queryString = params.toString();
      const url = `/analytics/agents/${agentId}${queryString ? '?' + queryString : ''}`;
      
      const response = await api.get(url);
      
      if (response.data.success) {
        return response.data.data;
      }
      return null;
    } catch (error) {
      console.error('Failed to get agent analytics:', error);
      return null;
    }
  }

  static getStoredAnalytics(): LinkClickEvent[] {
    try {
      const stored = localStorage.getItem(this.STORAGE_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (error) {
      console.error('Failed to get stored analytics:', error);
      return [];
    }
  }

  static clearAnalytics(): void {
    localStorage.removeItem(this.STORAGE_KEY);
    sessionStorage.removeItem(this.SESSION_KEY);
  }

  static exportAnalytics(): string {
    const data = this.getStoredAnalytics();
    return JSON.stringify(data, null, 2);
  }
}

export function useAgentAnalytics(agentId?: string) {
  const [analytics, setAnalytics] = useState<AgentLinkAnalytics | null>(null);
  const [allAnalytics, setAllAnalytics] = useState<Record<string, AgentLinkAnalytics>>({});
  const [loading, setLoading] = useState(false);

  const trackClick = async (trackingAgentId: string, referralCode?: string) => {
    await AgentAnalyticsService.trackLinkClick(trackingAgentId, referralCode);
    if (agentId === trackingAgentId) {
      refreshAnalytics();
    }
  };

  const markConversion = async (trackingAgentId: string) => {
    const success = await AgentAnalyticsService.markConversion(trackingAgentId);
    if (success && agentId === trackingAgentId) {
      refreshAnalytics();
    }
    return success;
  };

  const refreshAnalytics = async () => {
    if (!agentId) return;
    
    setLoading(true);
    try {
      const agentAnalytics = await AgentAnalyticsService.getAgentAnalytics(agentId);
      setAnalytics(agentAnalytics);
    } catch (error) {
      console.error('Failed to refresh analytics:', error);
    } finally {
      setLoading(false);
    }
  };

  const clearAnalytics = () => {
    AgentAnalyticsService.clearAnalytics();
    setAnalytics(null);
    setAllAnalytics({});
  };

  const exportAnalytics = () => {
    return AgentAnalyticsService.exportAnalytics();
  };

  useEffect(() => {
    if (agentId) {
      refreshAnalytics();
    }
  }, [agentId]);

  return {
    analytics,
    allAnalytics,
    loading,
    trackClick,
    markConversion,
    refreshAnalytics,
    clearAnalytics,
    exportAnalytics
  };
}